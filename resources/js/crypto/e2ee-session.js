// Lightweight E2EE session manager
// Stores R (base64) primarily in-memory, with optional sessionStorage persistence + TTL and idle auto-lock.

  const DEFAULT_TTL_MS = 15 * 60 * 1000; // 15 minutes
  let Rb64 = null;
  let expiresAt = 0;
  let ttlMs = DEFAULT_TTL_MS;
  let lastActive = Date.now();
  let idleTimer = null;

  const KEY = 'e2ee_R_b64';
  const KEY_EXP = 'e2ee_R_exp';

  function now(){ return Date.now(); }
  function isExpired(ts){ return !ts || ts <= now(); }

  function setR(value, options = {}){
    const persist = !!options.persist; // default false (memory-only)
    const customTtl = typeof options.ttlMs === 'number' ? options.ttlMs : ttlMs;
    Rb64 = value || null;
    expiresAt = Rb64 ? now() + customTtl : 0;
    if (persist && Rb64) {
      try { sessionStorage.setItem(KEY, Rb64); sessionStorage.setItem(KEY_EXP, String(expiresAt)); } catch {}
    } else {
      try { sessionStorage.removeItem(KEY); sessionStorage.removeItem(KEY_EXP); } catch {}
    }
    return Rb64;
  }

  function getR(){
    // prefer memory
    if (Rb64 && !isExpired(expiresAt)) return Rb64;
    // try sessionStorage
    try {
      const s = sessionStorage.getItem(KEY);
      const e = Number(sessionStorage.getItem(KEY_EXP) || 0);
      if (s && !isExpired(e)) {
        Rb64 = s; expiresAt = e; return Rb64;
      }
    } catch {}
    // no valid R
    lock();
    return null;
  }

  function lock(){
    Rb64 = null; expiresAt = 0;
    try { sessionStorage.removeItem(KEY); sessionStorage.removeItem(KEY_EXP); } catch {}
  }

  function isLocked(){ return !getR(); }
  function configure(opts){ if (opts && typeof opts.ttlMs === 'number') ttlMs = opts.ttlMs; }

  function _scheduleIdle(){
    if (idleTimer) clearTimeout(idleTimer);
    idleTimer = setTimeout(() => {
      // If idle and R exists, lock
      if (getR()) lock();
    }, ttlMs);
  }

  function initAutoLock(){
    const bump = () => { lastActive = now(); _scheduleIdle(); };
    ['mousemove','keydown','pointerdown','touchstart','visibilitychange'].forEach(evt => {
      window.addEventListener(evt, bump, { passive: true });
    });
    // periodic sessionStorage expiry check
    setInterval(() => {
      try {
        const e = Number(sessionStorage.getItem(KEY_EXP) || 0);
        if (e && isExpired(e)) lock();
      } catch {}
    }, Math.min(30_000, ttlMs));
    _scheduleIdle();
  }

  const api = { setR, getR, lock, isLocked, configure, initAutoLock };
  if (typeof window !== 'undefined') window.E2EESession = api;
export default api;
