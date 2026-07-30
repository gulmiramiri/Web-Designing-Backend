<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'Orders';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1 min-w-0">
    <div class="mb-6">
      <h1 class="text-2xl font-bold">Orders / Cart Activity</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">See what users are adding to their carts.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-left text-gray-500 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3 font-medium">User</th>
              <th class="px-4 py-3 font-medium">Email</th>
              <th class="px-4 py-3 font-medium">Product</th>
              <th class="px-4 py-3 font-medium">Qty</th>
              <th class="px-4 py-3 font-medium">Price</th>
              <th class="px-4 py-3 font-medium">Date</th>
              <th class="px-4 py-3 font-medium">Reply</th>
            </tr>
          </thead>
          <tbody id="ordersTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="ordersPagination" class="flex items-center justify-center gap-2 mt-6"></div>

    <!-- Reply Modal -->
    <div id="replyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/40" id="replyModalOverlay"></div>
      <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6 z-10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold">Reply to Cart Activity</h3>
          <button id="replyModalClose" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <div id="replyModalInfo" class="mb-4 text-sm text-gray-500 dark:text-gray-400"></div>
        <textarea id="replyMessageInput" rows="4" placeholder="Type your reply..." class="w-full border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-300 resize-none"></textarea>
        <div class="flex justify-end gap-3 mt-4">
          <button id="replyModalCancel" class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
          <button id="replyModalSubmit" class="px-4 py-2 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-colors">Send Reply</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin-orders.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
