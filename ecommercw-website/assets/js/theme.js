/**
 * Dark Mode / Light Mode controller.
 * - Reads saved preference from localStorage.
 * - Falls back to system preference (prefers-color-scheme) if none is saved.
 * - Persists user choice and applies the Tailwind `dark` class strategy.
 */
(function () {
  const root = document.documentElement;
  const STORAGE_KEY = 'theme';

  function applyTheme(theme) {
    if (theme === 'dark') {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
  }

  function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY);
  }

  function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function setTheme(theme) {
    localStorage.setItem(STORAGE_KEY, theme);
    applyTheme(theme);
  }

  // Apply initial theme immediately (header.php also does an early inline
  // version of this to prevent flash-of-wrong-theme before Tailwind loads).
  const initial = getStoredTheme() || getSystemTheme();
  applyTheme(initial);

  // React to system theme changes only if the user hasn't set an explicit preference.
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!getStoredTheme()) {
      applyTheme(e.matches ? 'dark' : 'light');
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('themeToggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        const isDark = root.classList.contains('dark');
        setTheme(isDark ? 'light' : 'dark');
      });
    }

    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileBtn && mobileMenu) {
      mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    }

    // User dropdown menu toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenu = document.getElementById('userMenu');
    if (userMenuBtn && userMenu) {
      userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userMenu.classList.toggle('hidden');
      });
      document.addEventListener('click', (e) => {
        if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
          userMenu.classList.add('hidden');
        }
      });
    }

    // Logout buttons (desktop + mobile)
    ['logoutBtn', 'logoutBtnMobile'].forEach((id) => {
      const btn = document.getElementById(id);
      if (btn) {
        btn.addEventListener('click', async () => {
          const base = window.APP_BASE || '';
          try {
            await apiFetch(base + 'api/logout.php', { method: 'POST' });
          } catch (e) { /* noop */ }
          window.location.href = base + 'index.php';
        });
      }
    });
  });
})();
