<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'View Ticket';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';

$ticketId = (int)($_GET['id'] ?? 0);
if ($ticketId <= 0) {
    header('Location: tickets.php');
    exit;
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1 min-w-0">
    <div id="ticketViewLoading" class="space-y-4">
      <div class="skeleton rounded-xl h-12 w-64"></div>
      <div class="skeleton rounded-xl h-32"></div>
      <div class="skeleton rounded-xl h-24"></div>
      <div class="skeleton rounded-xl h-24"></div>
    </div>

    <div id="ticketViewContent" class="hidden">
      <div class="flex items-center justify-between mb-6">
        <div>
          <a href="tickets.php" class="text-sm text-primary-600 hover:underline mb-1 inline-block">&larr; Back to Tickets</a>
          <h1 id="ticketSubjectDisplay" class="text-2xl font-bold"></h1>
          <p id="ticketMeta" class="text-sm text-gray-500 dark:text-gray-400 mt-1"></p>
        </div>
        <div class="flex gap-2">
          <button id="reopenTicketBtn" class="hidden px-4 py-2 text-sm rounded-lg border border-green-300 dark:border-green-800 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">Reopen</button>
          <button id="closeTicketBtn" class="hidden px-4 py-2 text-sm rounded-lg border border-red-300 dark:border-red-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Close Ticket</button>
        </div>
      </div>

      <div id="messagesContainer" class="space-y-4 mb-8"></div>

      <div id="replySection" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
        <h3 class="font-bold text-sm mb-3">Reply as Admin</h3>
        <form id="replyForm" enctype="multipart/form-data">
          <input type="hidden" id="replyTicketId" value="<?php echo $ticketId; ?>">
          <textarea id="replyMessage" rows="4" required placeholder="Type your reply..."
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
          <div class="flex items-center justify-between mt-3">
            <input type="file" id="replyImage" accept="image/*" class="text-sm">
            <button type="submit" id="replySubmitBtn" class="px-6 py-2 text-sm rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-colors">Send Reply</button>
          </div>
        </form>
      </div>

      <div id="ticketClosedMsg" class="hidden text-center py-8">
        <p class="text-gray-500 dark:text-gray-400">This ticket is closed.</p>
      </div>
    </div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin.js', '../assets/js/admin-tickets.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
