<?php
$pageTitle = 'My Support Tickets';
require_once __DIR__ . '/includes/functions.php';
requirePageLogin('login.php');
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold">Support Tickets</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">View and manage your support requests.</p>
    </div>
    <div class="flex items-center gap-2">
      <select id="ticketStatusFilter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
        <option value="">All Status</option>
        <option value="open">Open</option>
        <option value="waiting_admin">Waiting for Admin</option>
        <option value="waiting_user">Waiting for You</option>
        <option value="closed">Closed</option>
      </select>
      <a href="ticket_create.php" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex items-center gap-2 transition-colors duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        New Ticket
      </a>
    </div>
  </div>

  <div id="ticketsLoading" class="space-y-3">
    <div class="skeleton rounded-xl h-20"></div>
    <div class="skeleton rounded-xl h-20"></div>
    <div class="skeleton rounded-xl h-20"></div>
  </div>

  <div id="ticketsEmpty" class="hidden text-center py-16">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
    <h2 class="text-xl font-bold mb-2">No tickets yet</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Create a support ticket and we'll get back to you.</p>
    <a href="ticket_create.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">Create Ticket</a>
  </div>

  <div id="ticketsList" class="hidden space-y-3"></div>
  <div id="ticketsPagination" class="flex items-center justify-center gap-2 mt-6"></div>
</div>

<?php
$extraScripts = ['assets/js/tickets.js'];
require_once __DIR__ . '/includes/footer.php';
?>
