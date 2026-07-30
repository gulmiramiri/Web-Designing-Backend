<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'Admin Dashboard';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1">
    <h1 class="text-2xl font-bold mb-1">Dashboard Overview</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Key statistics for your store.</p>

    <div id="statsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
    </div>

    <h2 class="text-lg font-bold mb-4">Stock Overview</h2>
    <div id="stockStatsGrid" class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
    </div>

    <h2 class="text-lg font-bold mb-4">Support Tickets</h2>
    <div id="ticketStatsGrid" class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
      <div class="skeleton rounded-2xl h-28"></div>
    </div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
