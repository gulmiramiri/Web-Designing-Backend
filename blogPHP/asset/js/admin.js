(function() {
  'use strict';

  // ─── DELETE CONFIRMATION ───────────────────
  document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href*="delete"]');
    if (link) {
      if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        e.preventDefault();
      }
    }
  });

  // ─── ACTIVE SIDEBAR LINK ───────────────────
  var currentPath = window.location.pathname;
  document.querySelectorAll('.admin-sidebar a, #admin-mobile-menu a').forEach(function(link) {
    var href = link.getAttribute('href');
    if (href && currentPath.indexOf(href) !== -1) {
      link.classList.add('bg-amber-50', 'text-amber-600');
      link.classList.add('dark:bg-amber-900/30', 'dark:text-amber-400');
    }
  });

})();
