// Client helper to talk to key-shared-worker
let worker = null;
let port = null;
let ready = false;

function connect() {
  try {
    if (worker && port && ready) return true;
    // Vite-friendly URL for SharedWorker
    const url = new URL('./key-shared-worker.js', import.meta.url);
    try {
      worker = new SharedWorker(url, { type: 'module', name: 'e2ee-key-shared-worker' });
    } catch (e) {
      try { worker = new SharedWorker(url, { name: 'e2ee-key-shared-worker' }); } catch { return false; }
    }
    port = worker.port;
    port.start();
    port.onmessage = (e) => { if (e.data && e.data.type === 'hello') ready = true; };
    return true;
  } catch (e) { return false; }
}

function ask(msg) {
  return new Promise((resolve) => {
    try {
      if (!connect()) return resolve(null);
      const channel = new MessageChannel();
      const listener = (e) => { channel.port1.removeEventListener('message', listener); resolve(e.data || null); };
      channel.port1.addEventListener('message', listener);
      channel.port1.start();
      port.postMessage(msg, [channel.port2]);
    } catch (e) { resolve(null); }
  });
}

export async function getR() {
  const res = await ask({ type:'get' });
  return res && res.R ? res.R : null;
}

export async function setR(R, ttlMs) {
  if (!R) return false;
  const res = await ask({ type:'set', R, ttlMs });
  return !!(res && res.ok);
}

export async function lock() {
  const res = await ask({ type:'lock' });
  return !!(res && res.ok);
}

if (typeof window !== 'undefined') {
  window.KeyWorker = { getR, setR, lock };
}
