<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'Manage Support Tickets';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1 min-w-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">Support Tickets</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage customer support requests.</p>
      </div>
      <div class="flex items-center gap-2">
        <input type="text" id="ticketSearch" placeholder="Search tickets..."
               class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm py-2 px-3 w-48 focus:outline-none focus:ring-2 focus:ring-primary-500">
        <select id="ticketStatusFilter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="waiting_admin">Waiting for Admin</option>
          <option value="waiting_user">Waiting for User</option>
          <option value="closed">Closed</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6" id="ticketStatCards">
      <div class="skeleton rounded-xl h-20"></div>
      <div class="skeleton rounded-xl h-20"></div>
      <div class="skeleton rounded-xl h-20"></div>
      <div class="skeleton rounded-xl h-20"></div>
      <div class="skeleton rounded-xl h-20"></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-left text-gray-500 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3 font-medium">Ticket #</th>
              <th class="px-4 py-3 font-medium">Subject</th>
              <th class="px-4 py-3 font-medium">User</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 font-medium">Created</th>
              <th class="px-4 py-3 font-medium">Last Updated</th>
              <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="ticketsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="ticketsPagination" class="flex items-center justify-center gap-2 mt-6"></div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin.js', '../assets/js/admin-tickets.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
