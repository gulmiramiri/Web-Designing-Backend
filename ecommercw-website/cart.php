<?php
$pageTitle = 'Shopping Cart';
require_once __DIR__ . '/includes/functions.php';
requirePageLogin('login.php');
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold">Shopping Cart</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">Review and manage your items.</p>
    </div>
    <button id="clearCartBtn" class="text-sm text-red-600 hover:text-red-700 font-medium px-3 py-2 rounded-lg border border-red-200 dark:border-red-900 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors hidden">
      Clear Cart
    </button>
  </div>

  <div id="cartLoading" class="space-y-4">
    <div class="skeleton rounded-xl h-24"></div>
    <div class="skeleton rounded-xl h-24"></div>
    <div class="skeleton rounded-xl h-24"></div>
  </div>

  <div id="cartEmpty" class="hidden text-center py-16">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <h2 class="text-xl font-bold mb-2">Your cart is empty</h2>
    <p class="text-gray-500 dark:text-gray-400 mb-6">Browse our products and add some items to your cart.</p>
    <a href="index.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">Continue Shopping</a>
  </div>

  <div id="cartContent" class="hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div id="cartItemsContainer" class="divide-y divide-gray-100 dark:divide-gray-700"></div>
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-gray-500 dark:text-gray-400">Subtotal (<span id="cartTotalItems">0</span> items)</span>
        <span id="cartSubtotal" class="text-xl font-bold text-primary-600 dark:text-primary-400">$0.00</span>
      </div>
      <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">Shipping and taxes calculated at checkout.</p>
      <a href="index.php" class="block text-center bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">Continue Shopping</a>
    </div>
  </div>
</div>

<?php
$extraScripts = ['assets/js/cart.js', 'assets/js/cart-page.js'];
require_once __DIR__ . '/includes/footer.php';
?>
