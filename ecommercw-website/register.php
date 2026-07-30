<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>

<section class="max-w-md mx-auto px-4 py-16">
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 transition-colors duration-300">
    <h1 class="text-2xl font-bold text-center mb-1">Create Account</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Join ShopEase in just a minute</p>

    <form id="registerForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1" for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-red-500 mt-1 hidden" data-error="full_name"></p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="username">Username</label>
        <input type="text" id="username" name="username" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-red-500 mt-1 hidden" data-error="username"></p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="email">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-red-500 mt-1 hidden" data-error="email"></p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="password">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-gray-400 mt-1">At least 8 characters.</p>
        <p class="text-xs text-red-500 mt-1 hidden" data-error="password"></p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-red-500 mt-1 hidden" data-error="confirm_password"></p>
      </div>

      <button type="submit" id="registerBtn"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
        <span id="registerBtnText">Create Account</span>
        <svg id="registerSpinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
      Already have an account? <a href="login.php" class="text-primary-600 dark:text-primary-400 font-medium">Log In</a>
    </p>
  </div>
</section>

<?php
$extraScripts = ['assets/js/auth.js'];
require_once __DIR__ . '/includes/footer.php';
?>
