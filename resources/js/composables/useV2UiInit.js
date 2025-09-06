// Re-initialize v2 UI behaviors after Inertia navigation
import { onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useV2UiInit() {
  const cleanup = [];

  function patchMenuSafe() {
    try {
      const M = window.Menu;
      if (!M || M._safePatched) return;
      const proto = M.prototype || {};
      const origManage = proto.manageScroll;
      proto.manageScroll = function (...args) {
        try {
          const hasPs = this && this._scrollbar && typeof this._scrollbar.destroy === 'function';
          if (hasPs && typeof origManage === 'function') {
            return origManage.apply(this, args);
          }
          // Fallback: ensure scroll still works without PerfectScrollbar
          this?._el?.querySelector?.('.menu-inner')?.classList?.add('overflow-auto');
          return undefined;
        } catch (_) { /* no-op */ }
      };
      M._safePatched = true;
    } catch { /* no-op */ }
  }

  function initMenu() {
    try {
      const layoutMenus = document.querySelectorAll('#layout-menu');
      if (!layoutMenus.length) return;
      // Initialize Menu for each layout-menu element
      layoutMenus.forEach((el) => {
        try {
          // Menu global is provided by public/v2/js/sidebar-menu.js
          // eslint-disable-next-line no-undef
          patchMenuSafe();
          const inst = new window.Menu(el, { orientation: 'vertical', closeChildren: false });
          if (window.Helpers) {
            window.Helpers.mainMenu = inst;
            window.Helpers.scrollToActive(false);
          }
        } catch (e) { /* no-op */ }
      });
    } catch {}
  }

  function initFeather() {
    try { window.feather?.replace?.(); } catch {}
  }

  function initTooltips() {
    try {
      const list = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      list.forEach((el) => {
        try {
          const TT = window.bootstrap?.Tooltip;
          if (TT) TT.getOrCreateInstance(el);
        } catch {}
      });
    } catch {}
  }

  function initFullscreen() {
    const btn = document.getElementById('fullscreen-button');
    if (!btn) return;
    const onClick = (e) => {
      e.preventDefault();
      try {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen?.();
        } else {
          document.exitFullscreen?.();
        }
      } catch {}
    };
    btn.addEventListener('click', onClick);
    cleanup.push(() => btn.removeEventListener('click', onClick));
  }

  function initBurgers() {
    const STORAGE_KEY = 'bk_sidebar_theme';

    // Ensure an initial state exists and is applied from storage
    try {
      let cur = document.body.getAttribute('sidebar-data-theme')
        || localStorage.getItem(STORAGE_KEY)
        || 'sidebar-show';
      document.body.setAttribute('sidebar-data-theme', cur);
    } catch {}

    // No overlay version: keep logic simple & predictable on mobile
    function removeOverlayIfAny() {
      const el = document.getElementById('sidebar-overlay');
      if (el) try { el.remove(); } catch {}
    }

    const applyToggle = () => {
      const cur = document.body.getAttribute('sidebar-data-theme');
      const next = cur === 'sidebar-hide' ? 'sidebar-show' : 'sidebar-hide';
      document.body.setAttribute('sidebar-data-theme', next);
      try { localStorage.setItem(STORAGE_KEY, next); } catch {}
      // Keep it simple: no overlay created
      if (next === 'sidebar-hide') removeOverlayIfAny();
    };
    const header = document.getElementById('header-burger-menu');
    const sidebar = document.getElementById('sidebar-burger-menu');
    if (header) {
      header.addEventListener('click', applyToggle);
      cleanup.push(() => header.removeEventListener('click', applyToggle));
    }
    if (sidebar) {
      sidebar.addEventListener('click', applyToggle);
      cleanup.push(() => sidebar.removeEventListener('click', applyToggle));
    }

    // Close sidebar when clicking outside on small screens
    const onDocClick = (e) => {
      try {
        // Only auto-close on small screens
        const isSmall = window.innerWidth < 992; // < lg
        const isShown = isSmall && document.body.getAttribute('sidebar-data-theme') === 'sidebar-show';
        if (!isShown) return;
        const sidebarArea = document.getElementById('sidebar-area');
        const menuEl = document.getElementById('layout-menu');
        const isInsideSidebar = sidebarArea?.contains(e.target) || menuEl?.contains(e.target);
        const isToggleBtn = e.target?.closest?.('#header-burger-menu, #sidebar-burger-menu');
        if (!isInsideSidebar && !isToggleBtn) {
          document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
          try { localStorage.setItem(STORAGE_KEY, 'sidebar-hide'); } catch {}
          removeOverlayIfAny();
        }
      } catch {}
    };
    document.addEventListener('click', onDocClick, { capture: true });
    document.addEventListener('touchstart', onDocClick, { capture: true });
    cleanup.push(() => {
      document.removeEventListener('click', onDocClick, { capture: true });
      document.removeEventListener('touchstart', onDocClick, { capture: true });
    });

    // Close on ESC
    const onKey = (e) => {
      if (e.key === 'Escape') {
        const isShown = document.body.getAttribute('sidebar-data-theme') === 'sidebar-show';
        if (isShown) {
          document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
          try { localStorage.setItem(STORAGE_KEY, 'sidebar-hide'); } catch {}
          removeOverlayIfAny();
        }
      }
    };
    document.addEventListener('keydown', onKey);
    cleanup.push(() => document.removeEventListener('keydown', onKey));
    // Close sidebar upon clicking any link inside the menu on mobile
    try {
      const menu = document.getElementById('layout-menu');
      const closeOnLink = (ev) => {
        const isSmall = window.innerWidth < 992;
        const a = ev.target?.closest?.('a');
        if (isSmall && a && document.body.getAttribute('sidebar-data-theme') === 'sidebar-show') {
          document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
          try { localStorage.setItem(STORAGE_KEY, 'sidebar-hide'); } catch {}
        }
      };
      menu?.addEventListener?.('click', closeOnLink, { capture: true });
      cleanup.push(() => menu?.removeEventListener?.('click', closeOnLink, { capture: true }));
    } catch {}
  }

  function initAll() {
    initMenu();
    initFeather();
    initTooltips();
    initFullscreen();
    initBurgers();
  }

  onMounted(() => {
    initAll();
  });

  watch(() => usePage().url, () => {
    initAll();
    // Ensure sidebar closes after navigation on small screens
    try {
      if (window.innerWidth < 992) {
        document.body.setAttribute('sidebar-data-theme', 'sidebar-hide');
        try { localStorage.setItem('bk_sidebar_theme', 'sidebar-hide'); } catch {}
      }
    } catch {}
  });

  return {
    dispose() {
      cleanup.forEach((fn) => { try { fn(); } catch {} });
    }
  };
}
