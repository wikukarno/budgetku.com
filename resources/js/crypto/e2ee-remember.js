// Minimal "Remember this device" using WebAuthn gating + IndexedDB wrapped R
// Note: This is a UX/security enhancement; WebAuthn ceremony requires user presence.

const DB_NAME = 'e2ee-remember';
const STORE = 'kv';
const KEY_ID = 'device_kek';
const WRAP_ID = 'wrapped_R';
const CRED_ID = 'webauthn_cred';

async function openDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open(DB_NAME, 1);
    req.onupgradeneeded = () => {
      req.result.createObjectStore(STORE);
    };
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

async function idbGet(key){ const db = await openDB(); return new Promise((res, rej)=>{ const tx=db.transaction(STORE,'readonly'); const st=tx.objectStore(STORE); const r=st.get(key); r.onsuccess=()=>res(r.result||null); r.onerror=()=>rej(r.error); }); }
async function idbSet(key,val){ const db = await openDB(); return new Promise((res, rej)=>{ const tx=db.transaction(STORE,'readwrite'); const st=tx.objectStore(STORE); const r=st.put(val, key); r.onsuccess=()=>res(true); r.onerror=()=>rej(r.error); }); }
async function idbDel(key){ const db = await openDB(); return new Promise((res, rej)=>{ const tx=db.transaction(STORE,'readwrite'); const st=tx.objectStore(STORE); const r=st.delete(key); r.onsuccess=()=>res(true); r.onerror=()=>rej(r.error); }); }

async function getOrCreateKEK(){
  let key = await idbGet(KEY_ID);
  if (key) return key;
  // Generate non-extractable key; store CryptoKey in IndexedDB (structured clone supported in modern browsers)
  key = await crypto.subtle.generateKey({ name:'AES-GCM', length:256 }, false, ['encrypt','decrypt']);
  await idbSet(KEY_ID, key);
  return key;
}

function b64ToBytes(b64){ const bin=atob(b64); const out=new Uint8Array(bin.length); for (let i=0;i<bin.length;i++) out[i]=bin.charCodeAt(i); return out; }
function bytesToB64(bytes){ return btoa(String.fromCharCode(...new Uint8Array(bytes))); }
function bytesToB64url(bytes){ return bytesToB64(bytes).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,''); }
function b64urlToBytes(s){ s = (s||'').replace(/-/g,'+').replace(/_/g,'/'); while (s.length % 4) s += '='; return b64ToBytes(s); }

async function wrapR(Rb64){
  const key = await getOrCreateKEK();
  const Rbytes = b64ToBytes(Rb64);
  const iv = crypto.getRandomValues(new Uint8Array(12));
  const ct = await crypto.subtle.encrypt({ name:'AES-GCM', iv }, key, Rbytes);
  await idbSet(WRAP_ID, { iv: bytesToB64(iv), ct: bytesToB64(new Uint8Array(ct)) });
}

async function unwrapR(){
  const key = await getOrCreateKEK();
  const rec = await idbGet(WRAP_ID); if (!rec) return null;
  const iv = b64ToBytes(rec.iv); const ct = b64ToBytes(rec.ct);
  const pt = await crypto.subtle.decrypt({ name:'AES-GCM', iv }, key, ct);
  return bytesToB64(new Uint8Array(pt));
}

function randomBytes(n){ const a=new Uint8Array(n); crypto.getRandomValues(a); return a; }

async function registerWebAuthn(){
  const pubKey = {
    challenge: randomBytes(32),
    rp: { name: 'BudgetKu', id: location.hostname },
    user: { id: randomBytes(16), name: 'user@'+location.hostname, displayName: 'BudgetKu User' },
    pubKeyCredParams: [{ type:'public-key', alg: -7 }, { type:'public-key', alg: -257 }],
    authenticatorSelection: { userVerification: 'required', authenticatorAttachment: 'platform' },
    timeout: 60000,
  };
  const cred = await navigator.credentials.create({ publicKey: pubKey });
  const rawId = cred?.rawId ? new Uint8Array(cred.rawId) : null;
  const id = rawId ? bytesToB64url(rawId) : (cred?.id || null);
  if (id) await idbSet(CRED_ID, id);
  return id;
}

async function requireWebAuthn(){
  const id = await idbGet(CRED_ID);
  if (!id) throw new Error('No registered authenticator');
  const allow = [{ id: b64urlToBytes(id), type: 'public-key' }];
  const pubKey = { challenge: randomBytes(32), timeout: 60000, userVerification:'preferred', allowCredentials: allow };
  await navigator.credentials.get({ publicKey: pubKey });
}

export async function enableRemember(Rb64){
  if (!('credentials' in navigator) || !window.PublicKeyCredential) throw new Error('WebAuthn not supported');
  try {
    const existing = await idbGet(CRED_ID);
    if (!existing) { await registerWebAuthn(); }
    await wrapR(Rb64);
    try { localStorage.setItem('e2ee_auto_unlock','1'); } catch {}
    return true;
  } catch (e) {
    try { localStorage.removeItem('e2ee_auto_unlock'); } catch {}
    throw e;
  }
}

export async function unlockRemember(){
  await requireWebAuthn();
  return await unwrapR();
}

export async function clearRemember(){ await idbDel(WRAP_ID); await idbDel(CRED_ID); try { localStorage.removeItem('e2ee_auto_unlock'); } catch {} }

export async function hasRemember(){ return !!(await idbGet(WRAP_ID)) && !!(await idbGet(CRED_ID)); }

if (typeof window !== 'undefined') { window.E2EERemember = { enableRemember, unlockRemember, clearRemember, hasRemember }; }
