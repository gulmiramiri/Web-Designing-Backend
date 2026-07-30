(function () {
  const base = window.APP_BASE;
  let currentPage = 1;
  let deleteTargetId = null;
  let stockTargetId = null;
  let categoriesCache = [];

  const tableBody = document.getElementById('productsTableBody');
  const pagination = document.getElementById('productsPagination');
  const form = document.getElementById('productForm');
  const stockFilter = document.getElementById('stockFilter');

  document.addEventListener('DOMContentLoaded', () => {
    loadCategoriesForSelect();
    loadProducts();

    document.getElementById('addProductBtn').addEventListener('click', () => openProductForm());
    form.addEventListener('submit', submitProductForm);
    document.getElementById('productImage').addEventListener('change', previewImage);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);

    document.getElementById('setStockBtn').addEventListener('click', () => adjustStock('set'));
    document.getElementById('increaseStockBtn').addEventListener('click', () => adjustStock('increase'));
    document.getElementById('decreaseStockBtn').addEventListener('click', () => adjustStock('decrease'));

    stockFilter.addEventListener('change', () => {
      currentPage = 1;
      loadProducts();
    });
  });

  async function loadCategoriesForSelect() {
    const data = await apiFetch(base + 'api/categories.php');
    if (!data.success) return;
    categoriesCache = data.categories;
    const select = document.getElementById('productCategory');
    select.innerHTML = data.categories.map((c) => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
  }

  async function loadProducts(page = 1) {
    currentPage = page;
    tableBody.innerHTML = `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>`;

    let url = base + `api/products.php?page=${page}&per_page=10`;
    if (stockFilter.value === 'low') url += '&low_stock=5';
    else if (stockFilter.value === 'out') url += '&low_stock=0';

    const data = await apiFetch(url);
    if (!data.success) {
      tableBody.innerHTML = `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Failed to load products.</td></tr>`;
      return;
    }
    if (data.products.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">No products yet. Click "Add Product" to create one.</td></tr>`;
      pagination.innerHTML = '';
      return;
    }

    tableBody.innerHTML = data.products.map((p) => {
      let stockLabel = '';
      if (p.stock == 0) stockLabel = '<span class="text-red-600 font-medium">Out of Stock</span>';
      else if (p.stock <= 5) stockLabel = `<span class="text-orange-600 font-medium">${p.stock}</span>`;
      else stockLabel = `<span class="text-green-600 font-medium">${p.stock}</span>`;

      const statusLabel = p.status === 'active'
        ? '<span class="text-xs bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">Active</span>'
        : '<span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">Hidden</span>';

      return `
      <tr>
        <td class="px-4 py-3">
          <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden">
            ${p.image ? `<img src="${base}uploads/${p.image}" class="w-full h-full object-cover">` : ''}
          </div>
        </td>
        <td class="px-4 py-3 font-medium">${escapeHtml(p.title)}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">${escapeHtml(p.sku || '—')}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">${escapeHtml(p.category_name || '—')}</td>
        <td class="px-4 py-3">${formatPrice(p.price)}</td>
        <td class="px-4 py-3">${stockLabel}</td>
        <td class="px-4 py-3">${statusLabel}</td>
        <td class="px-4 py-3">${p.featured == 1 ? '<span class="text-xs bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Yes</span>' : '<span class="text-xs text-gray-400">No</span>'}</td>
        <td class="px-4 py-3 text-right space-x-1">
          <button data-edit="${p.id}" class="text-primary-600 hover:underline text-xs">Edit</button>
          <button data-stock="${p.id}" class="text-green-600 hover:underline text-xs">Stock</button>
          <button data-comments="${p.id}" class="text-blue-600 hover:underline text-xs">Comments</button>
          <button data-delete="${p.id}" class="text-red-600 hover:underline text-xs">Delete</button>
        </td>
      </tr>`;
    }).join('');

    tableBody.querySelectorAll('[data-edit]').forEach((btn) =>
      btn.addEventListener('click', () => openProductForm(Number(btn.dataset.edit))));
    tableBody.querySelectorAll('[data-stock]').forEach((btn) =>
      btn.addEventListener('click', () => openStockModal(Number(btn.dataset.stock))));
    tableBody.querySelectorAll('[data-comments]').forEach((btn) =>
      btn.addEventListener('click', () => openCommentsModal(Number(btn.dataset.comments))));
    tableBody.querySelectorAll('[data-delete]').forEach((btn) =>
      btn.addEventListener('click', () => { deleteTargetId = Number(btn.dataset.delete); openModal('deleteModal'); }));

    renderPagination(data.pagination);
  }

  function renderPagination(p) {
    if (p.total_pages <= 1) { pagination.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= p.total_pages; i++) {
      html += `<button data-page="${i}" class="w-9 h-9 rounded-lg text-sm font-medium transition-colors duration-300 ${i === p.page ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'}">${i}</button>`;
    }
    pagination.innerHTML = html;
    pagination.querySelectorAll('button').forEach((btn) =>
      btn.addEventListener('click', () => loadProducts(Number(btn.dataset.page))));
  }

  async function openProductForm(id = null) {
    form.reset();
    document.getElementById('productId').value = '';
    document.getElementById('productImagePreview').classList.add('hidden');
    document.getElementById('productFormTitle').textContent = id ? 'Edit Product' : 'Add Product';
    document.getElementById('productStock').value = 0;
    document.getElementById('productSku').value = '';
    document.getElementById('productStatus').value = 'active';

    if (id) {
      const data = await apiFetch(base + `api/products.php?id=${id}`);
      if (data.success) {
        const p = data.product;
        document.getElementById('productId').value = p.id;
        document.getElementById('productTitle').value = p.title;
        document.getElementById('productDescription').value = p.description || '';
        document.getElementById('productPrice').value = p.price;
        document.getElementById('productCategory').value = p.category_id;
        document.getElementById('productFeatured').checked = p.featured == 1;
        document.getElementById('productStock').value = p.stock;
        document.getElementById('productSku').value = p.sku || '';
        document.getElementById('productStatus').value = p.status || 'active';
        if (p.image) {
          const preview = document.getElementById('productImagePreview');
          preview.src = base + 'uploads/' + p.image;
          preview.classList.remove('hidden');
        }
      }
    }
    openModal('productFormModal');
  }

  async function openStockModal(id) {
    stockTargetId = id;
    const data = await apiFetch(base + `api/products.php?id=${id}`);
    if (data.success) {
      document.getElementById('stockProductName').textContent = data.product.title;
      document.getElementById('currentStockDisplay').textContent = data.product.stock;
      document.getElementById('stockAdjustInput').value = 0;
    }
    openModal('stockModal');
  }

  async function adjustStock(action) {
    if (!stockTargetId) return;
    const amount = parseInt(document.getElementById('stockAdjustInput').value) || 0;
    if (amount < 0 && action !== 'set') {
      showToast('Please enter a valid number.', 'error');
      return;
    }

    const data = await apiFetch(base + `api/products.php?id=${stockTargetId}`);
    if (!data.success) return;

    let newStock = parseInt(data.product.stock);
    if (action === 'set') newStock = amount;
    else if (action === 'increase') newStock += amount;
    else if (action === 'decrease') newStock = Math.max(0, newStock - amount);

    const result = await apiFetch(base + 'api/products.php', {
      method: 'PUT',
      body: {
        id: stockTargetId,
        title: data.product.title,
        description: data.product.description || '',
        price: data.product.price,
        category_id: data.product.category_id,
        featured: data.product.featured,
        stock: newStock,
        sku: data.product.sku || '',
        status: data.product.status || 'active',
      }
    });

    if (result.success) {
      showToast(`Stock updated to ${newStock}.`, 'success');
      closeModal('stockModal');
      loadProducts(currentPage);
    } else {
      showToast(result.message || 'Failed to update stock.', 'error');
    }
  }

  function previewImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('productImagePreview');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
  }

  async function submitProductForm(e) {
    e.preventDefault();
    const id = document.getElementById('productId').value;
    const submitBtn = document.getElementById('productSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
      let imageFilename = null;
      const fileInput = document.getElementById('productImage');
      if (id && fileInput.files[0]) {
        const uploadData = await uploadImage(fileInput.files[0]);
        if (!uploadData.success) {
          showToast(uploadData.message || 'Image upload failed.', 'error');
          return;
        }
        imageFilename = uploadData.filename;
      }

      const payload = {
        title: document.getElementById('productTitle').value.trim(),
        description: document.getElementById('productDescription').value.trim(),
        price: document.getElementById('productPrice').value,
        category_id: document.getElementById('productCategory').value,
        featured: document.getElementById('productFeatured').checked ? 1 : 0,
        stock: parseInt(document.getElementById('productStock').value) || 0,
        sku: document.getElementById('productSku').value.trim(),
        status: document.getElementById('productStatus').value,
      };

      let data;
      if (id) {
        if (imageFilename) payload.image = imageFilename;
        payload.id = id;
        data = await apiFetch(base + 'api/products.php', { method: 'PUT', body: payload });
      } else {
        data = await apiFetch(base + 'api/products.php', { method: 'POST', body: buildFormDataWithFile(payload, fileInput.files[0]) });
      }

      if (!data.success) {
        showToast(data.message || 'Failed to save product.', 'error');
        return;
      }

      showToast(id ? 'Product updated.' : 'Product created.', 'success');
      closeModal('productFormModal');
      loadProducts(currentPage);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Save Product';
    }
  }

  function buildFormDataWithFile(payload, file) {
    const fd = new FormData();
    Object.entries(payload).forEach(([k, v]) => fd.append(k, v));
    if (file) fd.append('image', file);
    return fd;
  }

  async function uploadImage(file) {
    const fd = new FormData();
    fd.append('image', file);
    return apiFetch(base + 'api/upload.php', { method: 'POST', body: fd });
  }

  async function confirmDelete() {
    if (!deleteTargetId) return;
    const data = await apiFetch(base + 'api/products.php', { method: 'DELETE', body: `id=${deleteTargetId}` });
    if (!data.success) {
      showToast(data.message || 'Failed to delete product.', 'error');
      return;
    }
    showToast('Product deleted.', 'success');
    closeModal('deleteModal');
    loadProducts(currentPage);
  }

  async function openCommentsModal(productId) {
    const data = await apiFetch(base + 'api/products.php?id=' + productId);
    if (!data.success) { showToast('Product not found.', 'error'); return; }
    const p = data.product;

    document.getElementById('commentsModalProductName').textContent = p.title;
    document.getElementById('commentsModalBody').dataset.productId = productId;
    document.getElementById('commentsModalBody').innerHTML = '<p class="text-sm text-gray-400">Loading comments...</p>';
    openModal('commentsModal');

    loadAdminComments(productId);
  }

  async function loadAdminComments(productId) {
    const container = document.getElementById('commentsModalBody');
    const data = await apiFetch(base + 'api/comments.php?product_id=' + productId);
    if (!data.success) {
      container.innerHTML = '<p class="text-sm text-gray-400">Failed to load comments.</p>';
      return;
    }

    const comments = data.comments || [];
    let html = '';

    if (comments.length === 0) {
      html = '<p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>';
    } else {
      for (let i = 0; i < comments.length; i++) {
        const c = comments[i];
        const isAdmin = c.role_id == 1;
        const userLabel = isAdmin ? 'Admin' : escapeHtml(c.full_name);
        html += '<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-3">' +
          '<div class="flex items-center gap-2 mb-2">' +
            '<span class="font-medium text-sm">' + userLabel + '</span>' +
            '<span class="text-xs text-gray-400">' + c.created_at + '</span>' +
          '</div>' +
          '<p class="text-sm">' + escapeHtml(c.comment) + '</p>';

        if (c.replies && c.replies.length > 0) {
          for (let j = 0; j < c.replies.length; j++) {
            const r = c.replies[j];
            html += '<div class="ml-6 mt-3 pl-4 border-l-2 border-primary-300 dark:border-primary-600">' +
              '<div class="flex items-center gap-2 mb-1">' +
                '<span class="font-medium text-sm text-primary-600 dark:text-primary-400">Admin</span>' +
                '<span class="text-xs text-gray-400">' + r.created_at + '</span>' +
              '</div>' +
              '<p class="text-sm">' + escapeHtml(r.comment) + '</p>' +
            '</div>';
          }
        }

        // Admin reply form for each comment
        html += '<div class="mt-2">' +
          '<form class="admin-reply-form" data-parent-id="' + c.id + '">' +
            '<div class="flex gap-2">' +
              '<input type="text" placeholder="Write a reply..." class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-1.5 px-3 text-xs focus:outline-none focus:ring-2 focus:ring-primary-500">' +
              '<button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors duration-300">Reply</button>' +
            '</div>' +
          '</form>' +
        '</div>';

        html += '</div>';
      }
    }

    container.innerHTML = html;

    // Handle reply forms
    container.querySelectorAll('.admin-reply-form').forEach((form) => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = form.querySelector('input');
        const replyText = input.value.trim();
        if (!replyText) return;

        const parentId = form.dataset.parentId;
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = '...';

        const result = await apiFetch(base + 'api/comments.php', {
          method: 'POST',
          body: JSON.stringify({ product_id: parseInt(container.dataset.productId), parent_id: parseInt(parentId), comment: replyText })
        });

        if (result.success) {
          input.value = '';
          showToast('Reply posted.', 'success');
          loadAdminComments(parseInt(container.dataset.productId));
        } else {
          showToast(result.message || 'Failed to post reply.', 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Reply';
        }
      });
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
