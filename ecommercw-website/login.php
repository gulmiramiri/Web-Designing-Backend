<?php
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<section class="max-w-md mx-auto px-4 py-16">
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 transition-colors duration-300">
    <h1 class="text-2xl font-bold text-center mb-1">Welcome Back</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Log in to your ShopEase account</p>

    <form id="loginForm" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1" for="identifier">Username or Email</label>
        <input type="text" id="identifier" name="identifier" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-red-500 mt-1 hidden" data-error="identifier"></p>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1" for="password">Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" required
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
          </button>
        </div>
        <p class="text-xs text-red-500 mt-1 hidden" data-error="password"></p>
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2">
          <input type="checkbox" id="remember" name="remember" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
          Remember Me
        </label>
      </div>

      <button type="submit" id="loginBtn"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg transition-colors duration-300 flex items-center justify-center gap-2">
        <span id="loginBtnText">Log In</span>
        <svg id="loginSpinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
      </button>
    </form>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
      Don't have an account? <a href="register.php" class="text-primary-600 dark:text-primary-400 font-medium">Register</a>
    </p>
  </div>
</section>

<?php
$extraScripts = ['assets/js/auth.js'];
require_once __DIR__ . '/includes/footer.php';
?>
