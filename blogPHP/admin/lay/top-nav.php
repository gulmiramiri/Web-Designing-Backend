<header class="h-14 bg-gray-900 dark:bg-gray-950 text-white fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 sm:px-6 shadow-lg">
  <div class="flex items-center gap-4">
    <a href="<?= url("index.php") ?>" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">&larr; Site</a>
    <span class="text-gray-600">|</span>
    <span class="font-semibold text-sm tracking-wide">Admin Panel</span>
  </div>
  <a href="<?= url("auth/logout.php") ?>" class="text-sm text-gray-400 hover:text-red-400 transition-colors duration-200">Logout</a>
</header>
