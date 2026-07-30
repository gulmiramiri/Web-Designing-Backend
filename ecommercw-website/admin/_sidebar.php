<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$navItems = [
    'index.php'      => ['label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    'products.php'    => ['label' => 'Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    'categories.php'  => ['label' => 'Categories', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
    'users.php'       => ['label' => 'Users', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4'],
    'orders.php'      => ['label' => 'Orders / Sales', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
];
?>
<aside class="md:w-56 shrink-0">
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-3 transition-colors duration-300 md:sticky md:top-20">
    <nav class="flex md:flex-col gap-1 overflow-x-auto">
      <?php foreach ($navItems as $href => $item): ?>
        <a href="<?php echo e($href); ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium whitespace-nowrap transition-colors duration-300 <?php echo $currentPage === $href ? 'bg-primary-600 text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-700'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $item['icon']; ?>" />
          </svg>
          <?php echo e($item['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>
</aside>
