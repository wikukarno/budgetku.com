// SharedWorker to hold E2EE R (base64) in memory across page reloads
let Rb64 = null;
let expiresAt = 0;
let timer = null;

function now(){ return Date.now(); }
function isExpired(){ return !Rb64 || expiresAt <= now(); }
function clearTimer(){ if (timer) { clearTimeout(timer); timer = null; } }
function armTimer(){
  clearTimer();
  if (!Rb64) return;
  const ms = Math.max(1000, expiresAt - now());
  timer = setTimeout(() => { Rb64 = null; expiresAt = 0; }, ms);
}

function onMessage(e, port){
  try {
    const msg = e.data || {};
    if (msg.type === 'get') {
      if (isExpired()) { Rb64 = null; expiresAt = 0; }
      port.postMessage({ type:'get', R: Rb64 });
    } else if (msg.type === 'set') {
      Rb64 = msg.R || null;
      const ttl = typeof msg.ttlMs === 'number' ? msg.ttlMs : (120*60*1000);
      expiresAt = Rb64 ? now() + ttl : 0;
      armTimer();
      port.postMessage({ type:'set', ok: true });
    } else if (msg.type === 'lock') {
      Rb64 = null; expiresAt = 0; clearTimer();
      port.postMessage({ type:'lock', ok: true });
    }
  } catch (e) {
    try { port.postMessage({ type:'error', message: String(e&&e.message||e) }); } catch {}
  }
}

onconnect = function(event) {
  const port = event.ports[0];
  port.onmessage = (e) => onMessage(e, port);
  try { port.postMessage({ type:'hello' }); } catch {}
};

