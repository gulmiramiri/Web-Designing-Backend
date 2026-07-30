<?php
$pageTitle = 'Create Support Ticket';
require_once __DIR__ . '/includes/functions.php';
requirePageLogin('login.php');
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Create Support Ticket</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400">Describe your issue and we'll help you out.</p>
  </div>

  <form id="ticketForm" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 space-y-4 transition-colors duration-300" enctype="multipart/form-data">
    <div>
      <label class="block text-sm font-medium mb-1">Subject</label>
      <input type="text" id="ticketSubject" required placeholder="e.g., Order issue, Product question..."
             class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Message</label>
      <textarea id="ticketMessage" rows="6" required placeholder="Describe your issue in detail..."
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Attachment (optional)</label>
      <input type="file" id="ticketImage" accept="image/*" class="w-full text-sm">
    </div>
    <div class="flex justify-end gap-2 pt-2">
      <a href="tickets.php" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</a>
      <button type="submit" id="ticketSubmitBtn" class="px-6 py-2 text-sm rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-colors">Submit Ticket</button>
    </div>
  </form>
</div>

<?php
$extraScripts = ['assets/js/tickets.js'];
require_once __DIR__ . '/includes/footer.php';
?>
