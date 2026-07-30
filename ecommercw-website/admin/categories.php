<?php
require_once __DIR__ . '/../includes/functions.php';
requirePageLogin('../login.php');
requirePageAdmin();
$pageTitle = 'Manage Categories';
$isAdmin = true;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row gap-8">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="flex-1 min-w-0">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">Categories</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Organize your products into categories.</p>
      </div>
      <button id="addCategoryBtn" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex items-center gap-2 transition-colors duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Add Category
      </button>
    </div>

    <div id="categoriesTableWrapper" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-left text-gray-500 dark:text-gray-400">
            <tr>
              <th class="px-4 py-3 font-medium">Image</th>
              <th class="px-4 py-3 font-medium">Name</th>
              <th class="px-4 py-3 font-medium">Products</th>
              <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="categoriesTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Category Modal (Add/Edit) -->
<div id="categoryFormModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="categoryFormModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto transition-colors duration-300">
    <div class="p-6">
      <h2 id="categoryFormTitle" class="text-lg font-bold mb-4">Add Category</h2>
      <form id="categoryForm" class="space-y-4">
        <input type="hidden" id="categoryId">
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input type="text" id="categoryName" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Image</label>
          <input type="file" id="categoryImage" accept="image/*" class="w-full text-sm">
          <img id="categoryImagePreview" class="hidden mt-2 h-16 w-16 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" data-close-modal="categoryFormModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Cancel</button>
          <button type="submit" id="categorySubmitBtn" class="px-4 py-2 text-sm rounded-lg bg-primary-600 hover:bg-primary-700 text-white">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
  <div class="modal-backdrop absolute inset-0 bg-black/50" data-close-modal="deleteModal"></div>
  <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6 transition-colors duration-300">
    <h2 class="font-bold mb-2">Delete Category</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure? Products in this category will no longer be categorized.</p>
    <div class="flex justify-end gap-2">
      <button data-close-modal="deleteModal" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600">Cancel</button>
      <button id="confirmDeleteBtn" class="px-4 py-2 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white">Delete</button>
    </div>
  </div>
</div>

<?php
$extraScripts = ['../assets/js/admin.js', '../assets/js/admin-categories.js'];
require_once __DIR__ . '/../includes/footer.php';
?>
