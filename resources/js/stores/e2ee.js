// Vue 3 Composable untuk E2EE State Management
import { ref, computed, watch } from 'vue';
import axios from 'axios';

// Runtime flags:
// - window.E2EE_MEMORY_ONLY === true -> don't persist keys to localStorage (memory-only)
// - window.E2EE_SHARE_ACROSS_TABS === false -> don't use SharedWorker (per-tab only)
const MEMORY_ONLY = typeof window !== 'undefined' && window.E2EE_MEMORY_ONLY === true;
const SHARE_ACROSS_TABS = typeof window === 'undefined' ? true : (window.E2EE_SHARE_ACROSS_TABS !== false);

const Rb64 = ref(null);
const expiresAt = ref(0);
const e2eeEnabled = ref(false);
const userKeys = ref(null);
const isSetupComplete = ref(false);

const STORAGE_KEY = 'budgetku_e2ee_R';
const STORAGE_EXP_KEY = 'budgetku_e2ee_R_exp';
const STORAGE_ENABLED_KEY = 'budgetku_e2ee_enabled';
const COOKIE_WRAP = 'bk_wr';

// Default TTL: 30 hari (sama seperti config di app.js)
const DEFAULT_TTL_MS = 30 * 24 * 60 * 60 * 1000;

// Helper functions
function now() { return Date.now(); }
function isExpired(timestamp) { return !timestamp || timestamp <= now(); }
function wrapBytes(b64) { return Uint8Array.from(atob(b64), c => c.charCodeAt(0)); }
function b64(bytes) { return btoa(String.fromCharCode(...new Uint8Array(bytes))); }
function b64ToBytes(b64str) { return Uint8Array.from(atob(b64str), c => c.charCodeAt(0)); }

// Cookie helpers (non-HttpOnly only)
function getCookie(name) {
  if (typeof document === 'undefined') return null;
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}
function deleteCookie(name) {
  if (typeof document === 'undefined') return;
  document.cookie = `${name}=; Max-Age=0; path=/; SameSite=Lax`;
}

// Computed states
const isUnlocked = computed(() => Rb64.value && !isExpired(expiresAt.value));
const isLocked = computed(() => !isUnlocked.value);

// Session key lifecycle (in-memory only)
const sessionInfo = {
  exp: 0,
  timerId: null,
};

// Load dari localStorage saat startup
function loadFromStorage() {
  if (MEMORY_ONLY) {
    return;
  }
  try {
    const storedR = localStorage.getItem(STORAGE_KEY);
    const storedExp = Number(localStorage.getItem(STORAGE_EXP_KEY)) || 0;
    const storedEnabled = localStorage.getItem(STORAGE_ENABLED_KEY) === 'true';
    
    if (storedR && !isExpired(storedExp)) {
      Rb64.value = storedR;
      expiresAt.value = storedExp;
    }
    
    e2eeEnabled.value = storedEnabled;
    
    console.log('[E2EE Store] Loaded from storage:', { 
      hasR: !!storedR, 
      expired: isExpired(storedExp), 
      enabled: storedEnabled 
    });
  } catch (error) {
    console.warn('[E2EE Store] Failed to load from storage:', error);
  }
}

// Save ke localStorage
function saveToStorage() {
  if (MEMORY_ONLY) {
    return;
  }
  try {
    if (Rb64.value && !isExpired(expiresAt.value)) {
      localStorage.setItem(STORAGE_KEY, Rb64.value);
      localStorage.setItem(STORAGE_EXP_KEY, String(expiresAt.value));
    } else {
      localStorage.removeItem(STORAGE_KEY);
      localStorage.removeItem(STORAGE_EXP_KEY);
    }
    
    localStorage.setItem(STORAGE_ENABLED_KEY, String(e2eeEnabled.value));
  } catch (error) {
    console.warn('[E2EE Store] Failed to save to storage:', error);
  }
}

// Set R key dengan persistence
function setR(value, options = {}) {
  const ttl = options.ttlMs || DEFAULT_TTL_MS;
  
  Rb64.value = value || null;
  expiresAt.value = value ? now() + ttl : 0;
  
  // Save to localStorage
  saveToStorage();
  
  // Update SharedWorker jika tersedia
  updateSharedWorker(value, ttl);
  
  console.log('[E2EE Store] Set R:', { hasR: !!value, ttl, expiresAt: expiresAt.value });
  
  return Rb64.value;
}

// Get R key dengan auto-cleanup jika expired
function getR() {
  if (Rb64.value && !isExpired(expiresAt.value)) {
    return Rb64.value;
  }
  
  // Cleanup expired keys
  if (isExpired(expiresAt.value)) {
    lock();
  }
  
  return null;
}

// Lock/clear semua keys
function lock() {
  console.log('[E2EE Store] Locking session');

  Rb64.value = null;
  expiresAt.value = 0;
  
  // Clear localStorage
  try {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(STORAGE_EXP_KEY);
  } catch {}
  
  // Clear SharedWorker
  clearSharedWorker();

  // Clear rotation timer
  if (sessionInfo.timerId) {
    clearTimeout(sessionInfo.timerId);
    sessionInfo.timerId = null;
  }
}

// Update SharedWorker
async function updateSharedWorker(value, ttl) {
  if (!SHARE_ACROSS_TABS) return;
  try {
    const mod = await import('../crypto/key-worker-client');
    if (value) {
      await mod.setR(value, ttl);
    } else {
      await mod.lock();
    }
  } catch (error) {
    console.warn('[E2EE Store] SharedWorker update failed:', error);
  }
}

// Clear SharedWorker
async function clearSharedWorker() {
  if (!SHARE_ACROSS_TABS) return;
  try {
    const mod = await import('../crypto/key-worker-client');
    await mod.lock();
  } catch {}
}

// Fetch user E2EE keys dari server
async function fetchUserKeys() {
  try {
    // Use web route guarded by session auth (not the stateless API route)
    const response = await axios.get('/e2ee/keys');
    userKeys.value = response.data;
    e2eeEnabled.value = response.data.e2ee_enabled || false;
    isSetupComplete.value = e2eeEnabled.value;
    
    saveToStorage();
    
    console.log('[E2EE Store] Fetched user keys:', { 
      enabled: e2eeEnabled.value,
      keyVersion: response.data.key_version 
    });
    
    return userKeys.value;
  } catch (error) {
    console.warn('[E2EE Store] Failed to fetch user keys:', error);
    return null;
  }
}

// Derive key dari password menggunakan user's KDF params
async function deriveKeyFromPassword(password, keys = null) {
  const keyData = keys || userKeys.value;
  if (!keyData) throw new Error('No key data available');
  
  const salt = wrapBytes(keyData.e2ee_pass_salt);
  
  // Prefer Argon2id jika tersedia
  if (keyData.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
    const params = keyData.e2ee_kdf_params;
    const result = await window.argon2.hash({
      pass: password,
      salt,
      type: window.argon2.ArgonType.Argon2id,
      mem: params.mem || 65536,
      time: params.time || 3,
      parallelism: params.parallelism || 1,
      hashLen: 32,
      raw: true
    });
    return await crypto.subtle.importKey('raw', result.hash, { name: 'AES-GCM' }, false, ['decrypt']);
  }
  
  // Fallback ke PBKDF2
  const iterations = Math.max(310000, keyData.e2ee_kdf_params?.iter || 0);
  const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(password), 'PBKDF2', false, ['deriveKey']);
  return await crypto.subtle.deriveKey(
    { name: 'PBKDF2', salt, iterations, hash: 'SHA-256' },
    baseKey,
    { name: 'AES-GCM', length: 256 },
    false,
    ['decrypt']
  );
}

// Unlock E2EE dengan password
async function unlockWithPassword(password) {
  try {
    const keys = userKeys.value || await fetchUserKeys();
    if (!keys || !keys.e2ee_enabled) {
      throw new Error('E2EE not enabled for this user');
    }
    
    const passwordKey = await deriveKeyFromPassword(password, keys);
    const wrappedData = wrapBytes(keys.e2ee_pass_wrap);
    const iv = wrappedData.slice(0, 12);
    const ciphertext = wrappedData.slice(12);
    
    const decryptedR = await crypto.subtle.decrypt(
      { name: 'AES-GCM', iv },
      passwordKey,
      ciphertext
    );
    
    const Rb64Value = b64(decryptedR);
    setR(Rb64Value, { ttlMs: DEFAULT_TTL_MS });
    
    console.log('[E2EE Store] Successfully unlocked with password');
    return true;
  } catch (error) {
    console.error('[E2EE Store] Failed to unlock with password:', error);
    throw error;
  }
}

// Auto-restore dari SharedWorker saat startup
async function autoRestoreFromSharedWorker() {
  try {
    const mod = await import('../crypto/key-worker-client');
    const cachedR = await mod.getR();
    
    if (cachedR) {
      setR(cachedR, { ttlMs: DEFAULT_TTL_MS });
      console.log('[E2EE Store] Auto-restored from SharedWorker');
      return true;
    }
  } catch (error) {
    console.warn('[E2EE Store] SharedWorker auto-restore failed:', error);
  }
  return false;
}

// Initialize store
async function initialize() {
  console.log('[E2EE Store] Initializing...');
  
  // Load dari localStorage dulu
  loadFromStorage();
  
  // Fetch user keys
  await fetchUserKeys();
  
  // Auto-restore dari SharedWorker jika localStorage kosong
  if (!Rb64.value && SHARE_ACROSS_TABS) {
    await autoRestoreFromSharedWorker();
  }
  // Auto-restore via session key cookie HANYA jika sudah authenticated (dibuktikan
  // oleh fetchUserKeys() sukses) dan E2EE enabled. Ini mencegah loop di halaman login.
  if (!Rb64.value && e2eeEnabled.value) {
    try {
      const hasWrap = !!getCookie(COOKIE_WRAP);
      if (hasWrap) {
        const ok = await restoreFromSessionKey();
        if (ok) {
          console.log('[E2EE Store] Restored from session key after init');
          try { await wrapWithSessionKey(); } catch {}
        }
      } else {
        // Try device wrap (IndexedDB) when cookies are missing
        const ok2 = await restoreFromDevice?.();
        if (ok2) {
          console.log('[E2EE Store] Restored from device wrap');
          try { await wrapWithSessionKey(); } catch {}
        }
      }
    } catch (e) {
      console.warn('[E2EE Store] Session key restore at init failed:', e);
    }
  }
  
  console.log('[E2EE Store] Initialized:', { 
    unlocked: isUnlocked.value, 
    enabled: e2eeEnabled.value 
  });
}

// ===== Session-key based remember (no localStorage) =====
async function fetchSessionKey(refresh = false) {
  const url = refresh ? '/e2ee/session/key/refresh' : '/e2ee/session/key';
  const method = refresh ? 'post' : 'get';
  const { data } = await axios[method](url);
  try {
    // schedule rotation based on new exp
    if (data?.exp) scheduleRotation(Number(data.exp));
  } catch (e) { /* ignore */ }
  return { keyB64: data.key, exp: data.exp };
}

async function wrapWithSessionKey(options = {}) {
  const persist = !!options.persist; // true => long-lived cookie, don't clear on logout
  const Rcurr = getR();
  if (!Rcurr) throw new Error('No R in memory to wrap');
  const { keyB64 } = await fetchSessionKey(false);

  const Rbytes = b64ToBytes(Rcurr);
  const Sbytes = b64ToBytes(keyB64);
  const Skey = await crypto.subtle.importKey('raw', Sbytes, { name: 'AES-GCM' }, false, ['encrypt']);
  const iv = crypto.getRandomValues(new Uint8Array(12));
  const cipher = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, Skey, Rbytes);
  const payload = new Uint8Array(iv.length + new Uint8Array(cipher).length);
  payload.set(iv, 0);
  payload.set(new Uint8Array(cipher), iv.length);
  const wrapB64 = b64(payload);
  const wrapEnc = encodeURIComponent(wrapB64);
  
  // Try server-set cookie first
  try {
    await axios.post('/e2ee/session/wrap', { wrap: wrapEnc, persist }, { withCredentials: true });
  } catch (e) {
    console.warn('[E2EE Store] Server wrap set failed, will try client cookie fallback:', e);
  }

  // Client-side fallback to ensure cookie exists even if server didn't set it (e.g., CSRF or proxy)
  try {
    const maxAge = persist ? (180 * 24 * 60 * 60) : Math.floor(DEFAULT_TTL_MS / 1000);
    const secure = (typeof location !== 'undefined' && location.protocol === 'https:') ? '; Secure' : '';
    document.cookie = `${COOKIE_WRAP}=${wrapEnc}; Max-Age=${maxAge}; Path=/; SameSite=Lax${secure}`;
    if (persist) {
      document.cookie = `bk_wr_keep=1; Max-Age=${maxAge}; Path=/; SameSite=Lax${secure}`;
    }
  } catch {}
  try { console.log('[E2EE Store] wrap cookie set?', document.cookie.includes(`${COOKIE_WRAP}=`)); } catch {}
  return true;
}

async function restoreFromSessionKey() {
  if (restoreFromSessionKey._busy) return false;
  restoreFromSessionKey._busy = true;
  try {
  const wrap = getCookie(COOKIE_WRAP);
  if (!wrap) return false;
  const { keyB64 } = await fetchSessionKey(false);
  
  const payload = b64ToBytes(decodeURIComponent(wrap));
    const iv = payload.slice(0, 12);
    const ciphertext = payload.slice(12);
    const Sbytes = b64ToBytes(keyB64);
    const Skey = await crypto.subtle.importKey('raw', Sbytes, { name: 'AES-GCM' }, false, ['decrypt']);
    const Rbuf = await crypto.subtle.decrypt({ name: 'AES-GCM', iv }, Skey, ciphertext);
    const Rb64Value = b64(Rbuf);
    setR(Rb64Value, { ttlMs: DEFAULT_TTL_MS });
    return true;
  } finally {
    restoreFromSessionKey._busy = false;
  }
}

async function clearSessionWrap() {
  try { await axios.post('/e2ee/session/clear'); } catch {}
  deleteCookie(COOKIE_WRAP);
  try {
    // Ensure deletion even if server didn't clear
    const secure = (typeof location !== 'undefined' && location.protocol === 'https:') ? '; Secure' : '';
    document.cookie = `${COOKIE_WRAP}=; Max-Age=0; Path=/; SameSite=Lax${secure}`;
    document.cookie = `bk_wr_keep=; Max-Age=0; Path=/; SameSite=Lax${secure}`;
  } catch {}
}

function scheduleRotation(expSeconds) {
  const newExp = Number(expSeconds) || 0;
  if (!newExp) return;

  // Avoid rescheduling if exp unchanged and timer already exists
  if (sessionInfo.exp === newExp && sessionInfo.timerId) {
    return;
  }

  sessionInfo.exp = newExp;
  if (sessionInfo.timerId) {
    clearTimeout(sessionInfo.timerId);
    sessionInfo.timerId = null;
  }

  const nowMs = Date.now();
  const expMs = newExp * 1000;

  // We want to rotate shortly before expiry: 2 minutes before or 10% of TTL, whichever is sooner
  const ttlMs = Math.max(0, expMs - nowMs);
  const thresholdMs = Math.max(5000, Math.min(Math.floor(ttlMs * 0.1), 120000));
  const targetMs = expMs - thresholdMs;
  let delayMs = Math.max(1000, targetMs - nowMs);

  const MAX_TIMEOUT_MS = 2_000_000_000; // ~23 days, below setTimeout limit

  if (delayMs > MAX_TIMEOUT_MS) {
    console.log('[E2EE Store] Scheduling rotation checkpoint in', Math.round(MAX_TIMEOUT_MS/1000), 's');
    sessionInfo.timerId = setTimeout(() => {
      // Recompute on checkpoint without rotating yet
      try { scheduleRotation(sessionInfo.exp); } catch {}
    }, MAX_TIMEOUT_MS);
    return;
  }

  console.log('[E2EE Store] Scheduling session key rotation in', Math.round(delayMs/1000), 's');
  sessionInfo.timerId = setTimeout(async () => {
    try {
      console.log('[E2EE Store] Rotating session key...');
      await rotateSessionKey();
    } catch (e) {
      console.warn('[E2EE Store] Session key rotation failed:', e);
    }
  }, delayMs);
}

async function rotateSessionKey() {
  const { keyB64, exp } = await fetchSessionKey(true);
  // If R exists, re-wrap so cookie stays valid for next loads
  if (getR()) {
    try {
      await wrapWithSessionKey();
      console.log('[E2EE Store] Re-wrapped R after key rotation; new exp:', exp);
    } catch (e) {
      console.warn('[E2EE Store] Failed to re-wrap R after rotation:', e);
    }
    try { await saveDeviceWrap?.(); } catch {}
  }
}

// Watch untuk auto-save
watch([Rb64, expiresAt, e2eeEnabled], saveToStorage);

// Export composable
export function useE2EE() {
  return {
    // States (reactive refs)
    Rb64,
    expiresAt,
    e2eeEnabled,
    userKeys,
    isSetupComplete,
    
    // Computed
    isUnlocked,
    isLocked,
    
    // Methods
    setR,
    getR,
    lock,
    fetchUserKeys,
    deriveKeyFromPassword,
    unlockWithPassword,
    initialize,
    wrapWithSessionKey,
    restoreFromSessionKey,
    clearSessionWrap,
    rotateSessionKey,
    unlockWithAccountPassword,
    setAccountWrap,
    restoreFromDevice,
    saveDeviceWrap
  };
}

// Export default untuk backward compatibility
export default {
  setR,
  getR,
  lock,
  isLocked: () => isLocked.value,
  isUnlocked: () => isUnlocked.value,
  initialize
};

// --- Additional helpers for account-based wrap ---
async function setAccountWrap(password) {
  try {
    const Rcurr = getR();
    if (!Rcurr) throw new Error('No R in memory to wrap');
    const keys = userKeys.value || await fetchUserKeys();
    if (!keys) throw new Error('No key data');

    // Use dedicated salt for account wrap, generate if missing
    let accSaltB64 = keys.e2ee_acc_salt;
    if (!accSaltB64) {
      const salt = crypto.getRandomValues(new Uint8Array(16));
      accSaltB64 = b64(salt);
    }

    // Derive from login password with same KDF policy (prefer argon2id)
    let accKey;
    const salt = b64ToBytes(accSaltB64);
    if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
      const p = keys.e2ee_kdf_params;
      const r = await window.argon2.hash({ pass: password, salt, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
      accKey = await crypto.subtle.importKey('raw', r.hash, { name: 'AES-GCM' }, false, ['encrypt']);
    } else {
      const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(password), 'PBKDF2', false, ['deriveKey']);
      const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
      accKey = await crypto.subtle.deriveKey({ name:'PBKDF2', salt, iterations: iter, hash:'SHA-256' }, baseKey, { name:'AES-GCM', length:256 }, false, ['encrypt']);
    }

    const iv = crypto.getRandomValues(new Uint8Array(12));
    const Rbytes = b64ToBytes(Rcurr);
    const ct = await crypto.subtle.encrypt({ name:'AES-GCM', iv }, accKey, Rbytes);
    const payload = new Uint8Array(iv.length + new Uint8Array(ct).length);
    payload.set(iv, 0); payload.set(new Uint8Array(ct), iv.length);
    const accWrapB64 = b64(payload);

    await axios.post('/e2ee/account-wrap', { e2ee_acc_wrap: accWrapB64, e2ee_acc_salt: accSaltB64 }, { headers: { 'Accept': 'application/json' } });
    // Update cache so next login can use it
    userKeys.value = { ...(userKeys.value||{}), e2ee_acc_wrap: accWrapB64, e2ee_acc_salt: accSaltB64 };
    console.log('[E2EE Store] Account wrap set/updated');
    return true;
  } catch (e) {
    console.warn('[E2EE Store] Failed to set account wrap:', e);
    return false;
  }
}

async function unlockWithAccountPassword(password) {
  try {
    const keys = userKeys.value || await fetchUserKeys();
    if (!keys || !keys.e2ee_enabled) throw new Error('No keys');
    if (!keys.e2ee_acc_wrap || !keys.e2ee_acc_salt) throw new Error('No account wrap');

    const salt = b64ToBytes(keys.e2ee_acc_salt);
    let dkey;
    if (keys.e2ee_kdf_params?.kdf === 'argon2id' && window.argon2) {
      const p = keys.e2ee_kdf_params || {};
      const r = await window.argon2.hash({ pass: password, salt, type: window.argon2.ArgonType.Argon2id, mem: p.mem || 65536, time: p.time || 3, parallelism: p.parallelism || 1, hashLen: 32, raw: true });
      dkey = await crypto.subtle.importKey('raw', r.hash, { name:'AES-GCM' }, false, ['decrypt']);
    } else {
      const baseKey = await crypto.subtle.importKey('raw', new TextEncoder().encode(password), 'PBKDF2', false, ['deriveKey']);
      const iter = Math.max(310000, (keys.e2ee_kdf_params?.iter || 0));
      dkey = await crypto.subtle.deriveKey({ name:'PBKDF2', salt, iterations: iter, hash:'SHA-256' }, baseKey, { name:'AES-GCM', length:256 }, false, ['decrypt']);
    }

    const payload = b64ToBytes(keys.e2ee_acc_wrap);
    const iv = payload.slice(0,12), ct = payload.slice(12);
    const Rbuf = await crypto.subtle.decrypt({ name:'AES-GCM', iv }, dkey, ct);
    const Rb64Value = b64(Rbuf);
    setR(Rb64Value, { ttlMs: DEFAULT_TTL_MS });
    console.log('[E2EE Store] Unlocked with account password');
    return true;
  } catch (e) {
    console.warn('[E2EE Store] Failed to unlock with account password:', e);
    return false;
  }
}

// ===== Device wrap (IndexedDB) =====
function openDeviceDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open('budgetku_e2ee', 1);
    req.onupgradeneeded = () => {
      const db = req.result;
      if (!db.objectStoreNames.contains('device')) {
        db.createObjectStore('device');
      }
    };
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}
function idbGet(db, key) {
  return new Promise((resolve, reject) => {
    const tx = db.transaction('device', 'readonly');
    const st = tx.objectStore('device');
    const r = st.get(key);
    r.onsuccess = () => resolve(r.result || null);
    r.onerror = () => reject(r.error);
  });
}
function idbSet(db, key, val) {
  return new Promise((resolve, reject) => {
    const tx = db.transaction('device', 'readwrite');
    const st = tx.objectStore('device');
    const r = st.put(val, key);
    r.onsuccess = () => resolve(true);
    r.onerror = () => reject(r.error);
  });
}

async function getOrCreateDeviceSecret() {
  if (typeof indexedDB === 'undefined') return null;
  try {
    const db = await openDeviceDB();
    let secret = await idbGet(db, 'device_secret');
    if (!secret) {
      const bytes = crypto.getRandomValues(new Uint8Array(32));
      secret = b64(bytes);
      await idbSet(db, 'device_secret', secret);
    }
    return secret;
  } catch (e) {
    console.warn('[E2EE Store] Device secret unavailable:', e);
    return null;
  }
}

async function saveDeviceWrap() {
  if (typeof indexedDB === 'undefined') return false;
  const Rcurr = getR();
  if (!Rcurr) return false;
  try {
    const secretB64 = await getOrCreateDeviceSecret();
    if (!secretB64) return false;
    const keyBytes = b64ToBytes(secretB64);
    const key = await crypto.subtle.importKey('raw', keyBytes, { name:'AES-GCM' }, false, ['encrypt']);
    const iv = crypto.getRandomValues(new Uint8Array(12));
    const ct = await crypto.subtle.encrypt({ name:'AES-GCM', iv }, key, b64ToBytes(Rcurr));
    const payload = new Uint8Array(iv.length + new Uint8Array(ct).length);
    payload.set(iv, 0);
    payload.set(new Uint8Array(ct), iv.length);
    const wrap = b64(payload);
    const db = await openDeviceDB();
    await idbSet(db, 'device_wrap', wrap);
    console.log('[E2EE Store] Device wrap saved');
    return true;
  } catch (e) {
    console.warn('[E2EE Store] Failed to save device wrap:', e);
    return false;
  }
}

async function restoreFromDevice() {
  if (typeof indexedDB === 'undefined') return false;
  try {
    const db = await openDeviceDB();
    const wrap = await idbGet(db, 'device_wrap');
    const secretB64 = await idbGet(db, 'device_secret');
    if (!wrap || !secretB64) return false;
    const keyBytes = b64ToBytes(secretB64);
    const key = await crypto.subtle.importKey('raw', keyBytes, { name:'AES-GCM' }, false, ['decrypt']);
    const payload = b64ToBytes(wrap);
    const iv = payload.slice(0,12), ct = payload.slice(12);
    const Rbuf = await crypto.subtle.decrypt({ name:'AES-GCM', iv }, key, ct);
    const Rb64Value = b64(Rbuf);
    setR(Rb64Value, { ttlMs: DEFAULT_TTL_MS });
    return true;
  } catch (e) {
    console.warn('[E2EE Store] Failed to restore from device wrap:', e);
    return false;
  }
}
