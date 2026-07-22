<footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 mt-20" role="contentinfo">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

      <!-- Brand -->
      <div>
        <p class="text-lg font-bold text-gray-800 dark:text-gray-100 font-[family-name:var(--font-blog-heading)]">My Blog</p>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">A simple PHP blog sharing stories and ideas.</p>
      </div>

      <!-- Quick Links -->
      <div>
        <p class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Quick Links</p>
        <ul class="space-y-2">
          <li><a href="<?= url("index.php") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Home</a></li>
          <li><a href="<?= url("posts.php") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Posts</a></li>
        </ul>
      </div>

      <!-- Account -->
      <div>
        <p class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Account</p>
        <ul class="space-y-2">
          <?php if (!isset($_SESSION["user"])) { ?>
            <li><a href="<?= url("auth/login.php") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Log In</a></li>
            <li><a href="<?= url("auth/register.php") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Sign Up</a></li>
          <?php } else { ?>
            <li><a href="<?= url("admin/") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Admin Panel</a></li>
            <li><a href="<?= url("auth/logout.php") ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors duration-200">Log Out</a></li>
          <?php } ?>
        </ul>
      </div>

    </div>

    <hr class="my-8 border-gray-200 dark:border-gray-700">

    <p class="text-center text-sm text-gray-400 dark:text-gray-500">&copy; <?= date("Y") ?> My Blog. All rights reserved.</p>
  </div>
</footer>
<script src="<?= asset("asset/js/app.js") ?>"></script>
