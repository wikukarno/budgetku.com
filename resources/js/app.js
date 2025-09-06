import './bootstrap';
import './crypto/openpgp-global';
import './crypto/argon2-global';

// Import new E2EE store
import e2eeStore from './stores/e2ee';
import axios from 'axios';

// Legacy page scripts (untuk backward compatibility dengan Blade views)
import './pages/e2ee_setup';
import './pages/login';
import './pages/account';

// jQuery and plugins
import $ from 'jquery';
window.$ = window.jQuery = $;

// DataTables (Bootstrap 5 styling)
import 'datatables.net-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

// Select2
import 'select2';
import 'select2/dist/css/select2.css';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Export E2EE store to window untuk backward compatibility
window.E2EESession = e2eeStore;

// Auto-initialize E2EE store
(async () => {
  try {
    // Try silent auth refresh so session is rebuilt if only AUTH cookie was cleared
    try { await axios.post('/auth/refresh', {}, { withCredentials: true }); } catch {}
    await e2eeStore.initialize();
    console.log('[App] E2EE store initialized');
  } catch (error) {
    console.warn('[App] E2EE store initialization failed:', error);
  }
})();

// Bersihkan kunci saat logout
document.addEventListener('submit', (e) => {
  const form = e.target;
  if (form && form.tagName === 'FORM') {
    try {
      const action = form.getAttribute('action') || '';
      if (/\/logout\b/.test(action)) {
        e2eeStore.lock();
        console.log('[App] E2EE locked on logout');
      }
    } catch (error) {
      console.warn('[App] Failed to lock E2EE on logout:', error);
    }
  }
}, true);

// Bootstrap Inertia + Vue 3 (progressive enhancement: hanya jika #app ada)
(async () => {
  try {
    const el = document.getElementById('app');
    if (!el) return;
    const { createInertiaApp } = await import('@inertiajs/vue3');
    const { createApp, h } = await import('vue');
    // Gunakan path folder yang sesuai (lowercase `pages`)
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
    createInertiaApp({
      resolve: name => pages[`./pages/${name}.vue`],
      setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.mount(el);
      },
      progress: { color: '#4B5563' },
    });
  } catch {}
})();
