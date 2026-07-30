<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero -->
<section class="bg-gradient-to-br from-primary-50 to-white dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4">Shop Smarter with <span class="text-primary-600 dark:text-primary-400">ShopEase</span></h1>
    <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-8">Discover quality products across every category, curated and updated in real time.</p>
    <form id="heroSearchForm" class="max-w-xl mx-auto relative">
      <input type="text" id="heroSearchInput" placeholder="Search for products..."
             class="w-full rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-3 pl-5 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary-600 hover:bg-primary-700 text-white rounded-full p-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
      </button>
    </form>
  </div>
</section>

<!-- Categories -->
<section id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">Shop by Category</h2>
  </div>
  <div id="categoriesGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
    <!-- loading skeletons -->
    <?php for ($i = 0; $i < 6; $i++): ?>
      <div class="skeleton rounded-xl h-28"></div>
    <?php endfor; ?>
  </div>
</section>

<!-- Featured Products -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">Featured Products</h2>
  </div>
  <div id="featuredGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="skeleton rounded-xl h-64"></div>
    <?php endfor; ?>
  </div>
</section>

<!-- Latest Products -->
<section id="products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <h2 class="text-2xl font-bold">Latest Products</h2>
    <div class="flex items-center gap-3">
      <select id="categoryFilter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
        <option value="0">All Categories</option>
      </select>
    </div>
  </div>

  <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 min-h-[200px]">
    <?php for ($i = 0; $i < 8; $i++): ?>
      <div class="skeleton rounded-xl h-64"></div>
    <?php endfor; ?>
  </div>

  <div id="emptyState" class="hidden text-center py-16 text-gray-400">
    <p class="text-lg font-medium">No products found.</p>
    <p class="text-sm">Try a different search term or category.</p>
  </div>

  <div id="pagination" class="flex items-center justify-center gap-2 mt-10"></div>
</section>

<!-- About -->
<section id="about" class="bg-white dark:bg-gray-800 transition-colors duration-300 border-t border-gray-100 dark:border-gray-700">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid sm:grid-cols-3 gap-8 text-center">
    <div>
      <h3 class="font-bold text-lg mb-2">Fast Shipping</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Get your orders delivered quickly and reliably, wherever you are.</p>
    </div>
    <div>
      <h3 class="font-bold text-lg mb-2">Secure Payments</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Shop confidently with industry-standard security on every order.</p>
    </div>
    <div>
      <h3 class="font-bold text-lg mb-2">Quality Guaranteed</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Every product is vetted for quality before it reaches your door.</p>
    </div>
  </div>
</section>

<!-- Product Detail Modal -->
<div id="productModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" id="productModalBackdrop"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transition-colors duration-300">
    <button id="closeProductModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
    <div id="productModalContent" class="p-6 grid sm:grid-cols-2 gap-6"></div>
  </div>
</div>

<?php
$extraScripts = ['assets/js/home.js'];
require_once __DIR__ . '/includes/footer.php';
?>
