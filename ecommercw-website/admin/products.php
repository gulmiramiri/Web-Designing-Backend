<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'Manage Products';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1 min-w-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">Products</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage your store's product catalog.</p>
      </div>
      <div class="flex items-center gap-2">
        <select id="stockFilter" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="">All Products</option>
          <option value="low">Low Stock (≤5)</option>
          <option value="out">Out of Stock</option>
        </select>
        <button id="addProductBtn" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex items-center gap-2 transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
          Add Product
        </button>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-left text-gray-500 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3 font-medium">Image</th>
              <th class="px-4 py-3 font-medium">Title</th>
              <th class="px-4 py-3 font-medium">SKU</th>
              <th class="px-4 py-3 font-medium">Category</th>
              <th class="px-4 py-3 font-medium">Price</th>
              <th class="px-4 py-3 font-medium">Stock</th>
              <th class="px-4 py-3 font-medium">Status</th>
              <th class="px-4 py-3 font-medium">Featured</th>
              <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="productsTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div id="productsPagination" class="flex items-center justify-center gap-2 mt-6"></div>
  </div>
</div>

<!-- Product Modal (Add/Edit) -->
<div id="productFormModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="productFormModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto transition-colors duration-300">
    <div class="p-6">
      <h2 id="productFormTitle" class="text-lg font-bold mb-4">Add Product</h2>
      <form id="productForm" class="space-y-4">
        <input type="hidden" id="productId">
        <div>
          <label class="block text-sm font-medium mb-1">Title</label>
          <input type="text" id="productTitle" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea id="productDescription" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Price ($)</label>
            <input type="number" step="0.01" min="0.01" id="productPrice" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select id="productCategory" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Stock Quantity</label>
            <input type="number" min="0" id="productStock" value="0" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">SKU</label>
            <input type="text" id="productSku" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Status</label>
          <select id="productStatus" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="active">Active</option>
            <option value="hidden">Hidden</option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" id="productFeatured" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
          <label for="productFeatured" class="text-sm">Featured product</label>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Image</label>
          <input type="file" id="productImage" accept="image/*" class="w-full text-sm">
          <img id="productImagePreview" class="hidden mt-2 h-20 w-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" data-close-modal="productFormModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Cancel</button>
          <button type="submit" id="productSubmitBtn" class="px-4 py-2 text-sm rounded-lg bg-primary-600 hover:bg-primary-700 text-white">Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Stock Modal -->
<div id="stockModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="stockModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 transition-colors duration-300">
    <h2 class="font-bold mb-2">Manage Stock</h2>
    <p id="stockProductName" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>
    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium mb-1">Current Stock</label>
        <p id="currentStockDisplay" class="text-lg font-bold text-primary-600"></p>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Adjust Stock</label>
        <div class="flex gap-2">
          <input type="number" id="stockAdjustInput" value="0" min="0" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div class="flex gap-2">
        <button id="setStockBtn" class="flex-1 px-4 py-2 text-sm rounded-lg bg-primary-600 hover:bg-primary-700 text-white">Set</button>
        <button id="increaseStockBtn" class="flex-1 px-4 py-2 text-sm rounded-lg bg-green-600 hover:bg-green-700 text-white">Increase</button>
        <button id="decreaseStockBtn" class="flex-1 px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white">Decrease</button>
      </div>
    </div>
    <div class="flex justify-end mt-4">
      <button data-close-modal="stockModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Close</button>
    </div>
  </div>
</div>

<!-- Comments Modal -->
<div id="commentsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="commentsModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-y-auto transition-colors duration-300">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold">Comments</h2>
        <span id="commentsModalProductName" class="text-sm text-gray-500 dark:text-gray-400 truncate ml-2"></span>
      </div>
      <div id="commentsModalBody" class="space-y-3"></div>
      <div class="flex justify-end mt-4">
        <button data-close-modal="commentsModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="deleteModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 transition-colors duration-300">
    <h2 class="font-bold mb-2">Delete Product</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete this product? This action cannot be undone.</p>
    <div class="flex justify-end gap-2">
      <button data-close-modal="deleteModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Cancel</button>
      <button id="confirmDeleteBtn" class="px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white">Delete</button>
    </div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin.js', '../assets/js/admin-products.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
