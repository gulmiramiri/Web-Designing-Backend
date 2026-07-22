(function() {
  'use strict';

  // ─── BACK TO TOP ───────────────────────────
  var toTopBtn = document.createElement('button');
  toTopBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>';
  toTopBtn.setAttribute('aria-label', 'Back to top');
  toTopBtn.className = 'fixed bottom-6 right-6 z-50 w-11 h-11 bg-amber-400 text-gray-900 rounded-full shadow-lg flex items-center justify-center hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300 transition-all duration-300 opacity-0 invisible scale-90 cursor-pointer';
  toTopBtn.id = 'back-to-top';
  document.body.appendChild(toTopBtn);

  var ticking = false;
  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(function() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollY > 300) {
          toTopBtn.classList.remove('opacity-0', 'invisible', 'scale-90');
          toTopBtn.classList.add('opacity-100', 'visible', 'scale-100');
        } else {
          toTopBtn.classList.add('opacity-0', 'invisible', 'scale-90');
          toTopBtn.classList.remove('opacity-100', 'visible', 'scale-100');
        }
        ticking = false;
      });
      ticking = true;
    }
  }
  window.addEventListener('scroll', onScroll, { passive: true });

  toTopBtn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && toTopBtn.classList.contains('visible')) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });

  // ─── CONFIRM DIALOG ────────────────────────
  document.addEventListener('click', function(e) {
    var target = e.target.closest('[data-confirm]');
    if (target) {
      var msg = target.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) {
        e.preventDefault();
      }
    }
  });

})();
