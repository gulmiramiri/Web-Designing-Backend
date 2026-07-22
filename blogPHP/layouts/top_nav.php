<nav class="fixed top-0 left-0 right-0 z-50 h-16 bg-white/95 backdrop-blur-md border-b border-gray-200 dark:bg-gray-900/95 dark:border-gray-700 shadow-sm" role="navigation" aria-label="Main navigation">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center justify-between">

    <!-- Logo -->
    <a href="<?= url("index.php") ?>" class="flex items-center gap-2 no-underline" aria-label="Home">
      <span class="text-xl font-bold text-gray-800 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">My Blog</span>
    </a>

    <!-- Desktop nav + theme toggle + auth -->
    <div class="hidden md:flex items-center gap-6">
      <a href="<?= url("index.php") ?>" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Home</a>
      <a href="<?= url("posts.php") ?>" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Posts</a>

      <!-- Theme toggle (desktop) -->
      <button onclick="toggleTheme()" class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 cursor-pointer" aria-label="Toggle dark mode">
        <svg class="dark:hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
        <svg class="hidden dark:block" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>

      <div class="flex items-center gap-3 ml-2">
        <?php
        session_start();
        if (!isset($_SESSION["user"])) {
        ?>
          <a href="<?= url("auth/login.php") ?>" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Log In</a>
          <a href="<?= url("auth/register.php") ?>" class="text-sm font-medium bg-amber-400 text-gray-900 px-4 py-2 rounded-full hover:bg-amber-500 transition-colors duration-200">Sign Up</a>
        <?php } else { ?>
          <span class="text-sm text-gray-500 dark:text-gray-400">Welcome, <strong class="text-gray-800 dark:text-gray-200"><?= htmlspecialchars($_SESSION["user"]) ?></strong></span>
          <a href="<?= url("admin/") ?>" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Admin</a>
          <a href="<?= url("auth/logout.php") ?>" class="text-sm font-medium text-red-500 hover:text-red-600 transition-colors duration-200">Log Out</a>
        <?php } ?>
      </div>
    </div>

    <!-- Mobile hamburger -->
    <button id="mobile-menu-btn" class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
      <svg id="menu-icon-open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      <svg id="menu-icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>

  </div>

  <!-- Mobile menu panel -->
  <div id="mobile-menu" class="md:hidden hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg" role="menu">
    <div class="px-4 py-3 space-y-1">
      <a href="<?= url("index.php") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200" role="menuitem">Home</a>
      <a href="<?= url("posts.php") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200" role="menuitem">Posts</a>

      <!-- Theme toggle (mobile) -->
      <button onclick="toggleTheme()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200 cursor-pointer" role="menuitem">
        <svg class="dark:hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
        <svg class="hidden dark:block" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        <span class="dark:hidden">Light Mode</span>
        <span class="hidden dark:inline">Dark Mode</span>
      </button>

      <hr class="my-2 border-gray-200 dark:border-gray-700">
      <?php if (!isset($_SESSION["user"])) { ?>
        <a href="<?= url("auth/login.php") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200" role="menuitem">Log In</a>
        <a href="<?= url("auth/register.php") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium bg-amber-400 text-gray-900 text-center hover:bg-amber-500 transition-colors duration-200" role="menuitem">Sign Up</a>
      <?php } else { ?>
        <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Welcome, <strong class="text-gray-800 dark:text-gray-200"><?= htmlspecialchars($_SESSION["user"]) ?></strong></div>
        <a href="<?= url("admin/") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200" role="menuitem">Admin Panel</a>
        <a href="<?= url("auth/logout.php") ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors duration-200" role="menuitem">Log Out</a>
      <?php } ?>
    </div>
  </div>
</nav>

<script>
(function() {
  var btn = document.getElementById('mobile-menu-btn');
  var menu = document.getElementById('mobile-menu');
  var iconOpen = document.getElementById('menu-icon-open');
  var iconClose = document.getElementById('menu-icon-close');

  if (btn && menu) {
    btn.addEventListener('click', function() {
      var expanded = btn.getAttribute('aria-expanded') === 'true' ? false : true;
      menu.classList.toggle('hidden');
      btn.setAttribute('aria-expanded', expanded);
      iconOpen.classList.toggle('hidden');
      iconClose.classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
      if (!btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
      }
    });
  }
})();
</script>
