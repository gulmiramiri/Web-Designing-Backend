<?php
require_once __DIR__ . '/includes/functions.php';
requirePageLogin();
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<section class="max-w-4xl mx-auto px-4 py-12">
  <h1 class="text-2xl font-bold mb-1">My Dashboard</h1>
  <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Manage your profile and account settings.</p>

  <div class="grid md:grid-cols-3 gap-8">
    <!-- Profile summary -->
    <div class="md:col-span-1 space-y-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 text-center transition-colors duration-300">
        <div class="w-20 h-20 rounded-full bg-primary-100 dark:bg-gray-700 text-primary-600 dark:text-primary-400 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
          <?php echo e(mb_strtoupper(mb_substr($_SESSION['full_name'] ?? 'U', 0, 1))); ?>
        </div>
        <h2 class="font-semibold" id="summaryName"><?php echo e($_SESSION['full_name']); ?></h2>
        <p class="text-sm text-gray-500 dark:text-gray-400" id="summaryUsername">@<?php echo e($_SESSION['username']); ?></p>
        <span class="inline-block mt-3 text-xs px-3 py-1 rounded-full bg-primary-50 dark:bg-gray-700 text-primary-700 dark:text-primary-300 capitalize"><?php echo e($_SESSION['role']); ?></span>
      </div>

      <!-- Quick Links -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-4 transition-colors duration-300">
        <h3 class="font-semibold text-sm mb-3">Quick Links</h3>
        <div class="space-y-2">
          <a href="cart.php" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Shopping Cart
          </a>
          <a href="tickets.php" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
            Support Tickets
          </a>
          <a href="ticket_create.php" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Ticket
          </a>
        </div>
      </div>
    </div>

    <!-- Forms -->
    <div class="md:col-span-2 space-y-8">
      <!-- Edit Profile -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
        <h3 class="font-semibold mb-4">Edit Profile</h3>
        <form id="profileForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Full Name</label>
            <input type="text" id="profileFullName" value="<?php echo e($_SESSION['full_name']); ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" id="profileEmail" placeholder="you@example.com"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors duration-300">Save Changes</button>
        </form>
      </div>

      <!-- Change Password -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
        <h3 class="font-semibold mb-4">Change Password</h3>
        <form id="passwordForm" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Current Password</label>
            <input type="password" id="currentPassword" required
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">New Password</label>
            <input type="password" id="newPassword" required
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors duration-300">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php
$extraScripts = ['assets/js/dashboard.js'];
require_once __DIR__ . '/includes/footer.php';
?>
