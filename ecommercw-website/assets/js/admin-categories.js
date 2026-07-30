(function () {
  const base = window.APP_BASE;
  let deleteTargetId = null;

  const tableBody = document.getElementById('categoriesTableBody');
  const form = document.getElementById('categoryForm');

  document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    document.getElementById('addCategoryBtn').addEventListener('click', () => openCategoryForm());
    form.addEventListener('submit', submitCategoryForm);
    document.getElementById('categoryImage').addEventListener('change', previewImage);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
  });

  async function loadCategories() {
    tableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>`;
    const data = await apiFetch(base + 'api/categories.php');
    if (!data.success) {
      tableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Failed to load categories.</td></tr>`;
      return;
    }
    if (data.categories.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No categories yet.</td></tr>`;
      return;
    }
    tableBody.innerHTML = data.categories.map((c) => `
      <tr>
        <td class="px-4 py-3">
          <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden">
            ${c.image ? `<img src="${base}uploads/${c.image}" class="w-full h-full object-cover">` : ''}
          </div>
        </td>
        <td class="px-4 py-3 font-medium">${escapeHtml(c.name)}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">${c.product_count}</td>
        <td class="px-4 py-3 text-right space-x-2">
          <button data-edit="${c.id}" data-name="${escapeHtml(c.name)}" data-image="${c.image || ''}" class="text-primary-600 hover:underline">Edit</button>
          <button data-delete="${c.id}" class="text-red-600 hover:underline">Delete</button>
        </td>
      </tr>`).join('');

    tableBody.querySelectorAll('[data-edit]').forEach((btn) =>
      btn.addEventListener('click', () => openCategoryForm(Number(btn.dataset.edit), btn.dataset.name, btn.dataset.image)));
    tableBody.querySelectorAll('[data-delete]').forEach((btn) =>
      btn.addEventListener('click', () => { deleteTargetId = Number(btn.dataset.delete); openModal('deleteModal'); }));
  }

  function openCategoryForm(id = null, name = '', image = '') {
    form.reset();
    document.getElementById('categoryId').value = id || '';
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryFormTitle').textContent = id ? 'Edit Category' : 'Add Category';
    const preview = document.getElementById('categoryImagePreview');
    if (image) {
      preview.src = base + 'uploads/' + image;
      preview.classList.remove('hidden');
    } else {
      preview.classList.add('hidden');
    }
    openModal('categoryFormModal');
  }

  function previewImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('categoryImagePreview');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
  }

  async function submitCategoryForm(e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const name = document.getElementById('categoryName').value.trim();
    const fileInput = document.getElementById('categoryImage');
    const submitBtn = document.getElementById('categorySubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
      let data;
      if (id) {
        let imageFilename = null;
        if (fileInput.files[0]) {
          const uploadData = await apiFetch(base + 'api/upload.php', { method: 'POST', body: buildFormData(fileInput.files[0]) });
          if (!uploadData.success) {
            showToast(uploadData.message || 'Image upload failed.', 'error');
            return;
          }
          imageFilename = uploadData.filename;
        }
        const body = `id=${id}&name=${encodeURIComponent(name)}` + (imageFilename ? `&image=${encodeURIComponent(imageFilename)}` : '');
        data = await apiFetch(base + 'api/categories.php', { method: 'PUT', body });
      } else {
        const fd = new FormData();
        fd.append('name', name);
        if (fileInput.files[0]) fd.append('image', fileInput.files[0]);
        data = await apiFetch(base + 'api/categories.php', { method: 'POST', body: fd });
      }

      if (!data.success) {
        showToast(data.message || 'Failed to save category.', 'error');
        return;
      }
      showToast(id ? 'Category updated.' : 'Category created.', 'success');
      closeModal('categoryFormModal');
      loadCategories();
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Save Category';
    }
  }

  function buildFormData(file) {
    const fd = new FormData();
    fd.append('image', file);
    return fd;
  }

  async function confirmDelete() {
    if (!deleteTargetId) return;
    const data = await apiFetch(base + 'api/categories.php', { method: 'DELETE', body: `id=${deleteTargetId}` });
    if (!data.success) {
      showToast(data.message || 'Failed to delete category.', 'error');
      return;
    }
    showToast('Category deleted.', 'success');
    closeModal('deleteModal');
    loadCategories();
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
