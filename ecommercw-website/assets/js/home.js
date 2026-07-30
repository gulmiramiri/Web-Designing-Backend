(function () {
  var state = {
    page: 1,
    perPage: 8,
    search: new URLSearchParams(window.location.search).get('search') || '',
    categoryId: 0,
  };

  var productsGrid   = document.getElementById('productsGrid');
  var featuredGrid   = document.getElementById('featuredGrid');
  var categoriesGrid = document.getElementById('categoriesGrid');
  var categoryFilter = document.getElementById('categoryFilter');
  var pagination     = document.getElementById('pagination');
  var emptyState     = document.getElementById('emptyState');

  var isUserLoggedIn = !!window.CURRENT_USER_ID;

  document.addEventListener('DOMContentLoaded', function () {
    if (state.search) {
      document.getElementById('heroSearchInput').value = state.search;
      var navInput = document.getElementById('navSearchInput');
      if (navInput) navInput.value = state.search;
    }

    loadCategories();
    loadFeatured();
    loadProducts();

    document.getElementById('heroSearchForm').addEventListener('submit', function (e) {
      e.preventDefault();
      state.search = document.getElementById('heroSearchInput').value.trim();
      state.page = 1;
      loadProducts();
      document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
    });

    var navSearchForm = document.getElementById('navSearchForm');
    if (navSearchForm) {
      navSearchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        state.search = document.getElementById('navSearchInput').value.trim();
        state.page = 1;
        loadProducts();
        document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
      });
    }

    categoryFilter.addEventListener('change', function () {
      state.categoryId = Number(categoryFilter.value);
      state.page = 1;
      loadProducts();
    });

    document.getElementById('closeProductModal').addEventListener('click', closeProductModal);
    document.getElementById('productModalBackdrop').addEventListener('click', closeProductModal);
  });

  async function loadCategories() {
    var data = await apiFetch('api/categories.php');
    if (!data.success) {
      categoriesGrid.innerHTML = '<p class="col-span-full text-center text-gray-400">Unable to load categories.</p>';
      return;
    }

    if (data.categories.length === 0) {
      categoriesGrid.innerHTML = '<p class="col-span-full text-center text-gray-400">No categories yet.</p>';
    } else {
      var html = '';
      for (var i = 0; i < data.categories.length; i++) {
        var c = data.categories[i];
        var img = c.image ? '<img src="uploads/' + c.image + '" alt="' + escapeHtml(c.name) + '" class="w-full h-full object-cover">' : categoryIconSvg();
        html += '<button class="category-card group flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-primary-400 hover:shadow-md transition-all duration-300" data-id="' + c.id + '">' +
          '<div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden">' + img + '</div>' +
          '<span class="text-sm font-medium text-center">' + escapeHtml(c.name) + '</span>' +
          '<span class="text-xs text-gray-400">' + c.product_count + ' items</span>' +
        '</button>';
      }
      categoriesGrid.innerHTML = html;
    }

    var filterHtml = '<option value="0">All Categories</option>';
    for (var i = 0; i < data.categories.length; i++) {
      filterHtml += '<option value="' + data.categories[i].id + '">' + escapeHtml(data.categories[i].name) + '</option>';
    }
    categoryFilter.innerHTML = filterHtml;

    var categoryCards = document.querySelectorAll('.category-card');
    for (var i = 0; i < categoryCards.length; i++) {
      (function(btn) {
        btn.addEventListener('click', function () {
          state.categoryId = Number(btn.dataset.id);
          state.page = 1;
          categoryFilter.value = String(state.categoryId);
          loadProducts();
          document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
        });
      })(categoryCards[i]);
    }
  }

  async function loadFeatured() {
    var data = await apiFetch('api/products.php?featured=1&per_page=4');
    if (!data.success || data.products.length === 0) {
      featuredGrid.innerHTML = '<p class="col-span-full text-center text-gray-400 py-8">No featured products right now.</p>';
      return;
    }
    featuredGrid.innerHTML = buildProductHtml(data.products);
    attachCardEvents(featuredGrid);
  }

  async function loadProducts() {
    var skeletons = '';
    for (var i = 0; i < state.perPage; i++) {
      skeletons += '<div class="skeleton rounded-xl h-64"></div>';
    }
    productsGrid.innerHTML = skeletons;
    emptyState.classList.add('hidden');

    var params = new URLSearchParams({ page: state.page, per_page: state.perPage });
    if (state.search) params.set('search', state.search);
    if (state.categoryId) params.set('category_id', state.categoryId);

    var data = await apiFetch('api/products.php?' + params.toString());

    if (!data.success) {
      productsGrid.innerHTML = '';
      showToast(data.message || 'Failed to load products.', 'error');
      return;
    }

    if (data.products.length === 0) {
      productsGrid.innerHTML = '';
      emptyState.classList.remove('hidden');
      pagination.innerHTML = '';
      return;
    }

    productsGrid.innerHTML = buildProductHtml(data.products);
    attachCardEvents(productsGrid);
    renderPagination(data.pagination);
  }

  function buildProductHtml(products) {
    var html = '';
    for (var i = 0; i < products.length; i++) {
      var p = products[i];
      var inStock = parseInt(p.stock) > 0;
      var imgHtml = p.image
        ? '<img src="uploads/' + p.image + '" alt="' + escapeHtml(p.title) + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">'
        : '<div class="w-full h-full flex items-center justify-center text-gray-300">' + productIconSvg() + '</div>';
      var featHtml = p.featured == 1 ? '<span class="text-xs bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 px-2 py-0.5 rounded-full">Featured</span>' : '';
      var stockBadge = renderStockBadge(p.stock);

      var actionHtml = '';
      if (isUserLoggedIn) {
        var btnClass = inStock ? 'bg-primary-600 hover:bg-primary-700 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed';
        var disabledAttr = inStock ? '' : 'disabled';
        var btnLabel = inStock ? 'Add to Cart' : 'Out of Stock';
        actionHtml = '<button data-add-cart-card="' + p.id + '" class="add-to-cart-card-btn mt-2 w-full text-xs font-medium px-3 py-2 rounded-lg flex items-center justify-center gap-1.5 transition-colors duration-300 ' + btnClass + '" ' + disabledAttr + '>' +
          '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>' +
          btnLabel +
        '</button>';
      } else {
        actionHtml = '<a href="login.php" class="mt-2 block w-full text-center border border-primary-300 dark:border-primary-700 text-primary-600 dark:text-primary-400 text-xs font-medium px-3 py-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors duration-300">Login to Buy</a>';
      }

      html += '<div class="product-card group bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col" data-id="' + p.id + '">' +
          '<div class="aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden cursor-pointer" data-view-product="' + p.id + '">' + imgHtml + '</div>' +
          '<div class="p-4 flex flex-col flex-1">' +
            '<span class="text-xs font-medium text-primary-600 dark:text-primary-400 mb-1 cursor-pointer" data-view-product="' + p.id + '">' + escapeHtml(p.category_name || 'Uncategorized') + '</span>' +
            '<h3 class="font-semibold text-sm mb-1 line-clamp-2 cursor-pointer" data-view-product="' + p.id + '">' + escapeHtml(p.title) + '</h3>' +
            '<p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3 flex-1 cursor-pointer" data-view-product="' + p.id + '">' + escapeHtml(p.description || '') + '</p>' +
            '<div class="flex items-center justify-between mt-auto mb-2">' +
              '<span class="font-bold text-primary-700 dark:text-primary-400">' + formatPrice(p.price) + '</span>' +
              featHtml +
            '</div>' +
            stockBadge +
            actionHtml +
          '</div>' +
        '</div>';
    }
    return html;
  }

  function renderPagination(p) {
    if (p.total_pages <= 1) {
      pagination.innerHTML = '';
      return;
    }
    var html = '';
    html += pageBtn('&laquo;', Math.max(1, p.page - 1), p.page === 1);
    for (var i = 1; i <= p.total_pages; i++) {
      html += pageBtn(i, i, false, i === p.page);
    }
    html += pageBtn('&raquo;', Math.min(p.total_pages, p.page + 1), p.page === p.total_pages);
    pagination.innerHTML = html;

    var btns = pagination.querySelectorAll('button[data-page]');
    for (var i = 0; i < btns.length; i++) {
      (function(btn) {
        btn.addEventListener('click', function () {
          state.page = Number(btn.dataset.page);
          loadProducts();
          document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
        });
      })(btns[i]);
    }
  }

  function pageBtn(label, page, disabled, active) {
    active = active || false;
    var cls = '';
    if (active) cls = 'bg-primary-600 text-white';
    else if (disabled) cls = 'text-gray-300 dark:text-gray-600 cursor-not-allowed';
    else cls = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';
    var disabledAttr = disabled ? 'disabled' : '';
    return '<button data-page="' + page + '" ' + disabledAttr + ' class="w-9 h-9 rounded-lg text-sm font-medium transition-colors duration-300 ' + cls + '">' + label + '</button>';
  }

  function renderStockBadge(stock) {
    if (stock == 0) return '<span class="text-xs bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full font-medium">Out of Stock</span>';
    if (stock <= 3) return '<span class="text-xs bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-medium">Only ' + stock + ' left</span>';
    return '<span class="text-xs bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">In Stock (' + stock + ')</span>';
  }

  function attachCardEvents(container) {
    var viewEls = container.querySelectorAll('[data-view-product]');
    for (var i = 0; i < viewEls.length; i++) {
      (function(el) {
        el.addEventListener('click', function () { openProductModal(Number(el.dataset.viewProduct)); });
      })(viewEls[i]);
    }

    var cartBtns = container.querySelectorAll('[data-add-cart-card]');
    for (var i = 0; i < cartBtns.length; i++) {
      (function(btn) {
        btn.addEventListener('click', async function (e) {
          if (btn.disabled) return;
          e.stopPropagation();
          var productId = parseInt(btn.dataset.addCartCard);
          btn.disabled = true;
          btn.innerHTML = 'Adding...';
          var result = await apiFetch('api/cart.php', {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, quantity: 1 })
          });
          if (result.success) {
            showToast('Added to cart!', 'success');
            if (typeof updateCartCount === 'function') updateCartCount();
            if (typeof loadCartSidebar === 'function') loadCartSidebar();
          } else {
            showToast(result.message || 'Failed to add to cart.', 'error');
          }
          btn.disabled = false;
          btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg> Add to Cart';
        });
      })(cartBtns[i]);
    }
  }

  async function openProductModal(id) {
    var modal = document.getElementById('productModal');
    var content = document.getElementById('productModalContent');
    content.innerHTML = '<div class="skeleton rounded-xl h-64"></div><div class="space-y-3"><div class="skeleton h-6 rounded w-3/4"></div><div class="skeleton h-4 rounded w-1/2"></div><div class="skeleton h-20 rounded"></div></div>';
    modal.classList.remove('hidden');

    var data = await apiFetch('api/products.php?id=' + id);
    if (!data.success) {
      content.innerHTML = '<p class="col-span-full text-center text-gray-400">Product not found.</p>';
      return;
    }
    var p = data.product;

    var modalHtml = '<div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden">';
    if (p.image) {
      modalHtml += '<img src="uploads/' + p.image + '" class="w-full h-full object-cover" alt="' + escapeHtml(p.title) + '">';
    } else {
      modalHtml += '<div class="w-full h-full flex items-center justify-center text-gray-300">' + productIconSvg() + '</div>';
    }
    modalHtml += '</div><div class="flex flex-col">' +
      '<span class="text-xs font-medium text-primary-600 dark:text-primary-400 mb-2">' + escapeHtml(p.category_name || 'Uncategorized') + '</span>' +
      '<h2 class="text-xl font-bold mb-2">' + escapeHtml(p.title) + '</h2>' +
      '<p class="text-2xl font-bold text-primary-700 dark:text-primary-400 mb-2">' + formatPrice(p.price) + '</p>' +
      '<div class="mb-3">' + renderStockBadge(p.stock) + '</div>' +
      '<p class="text-sm text-gray-500 dark:text-gray-400 flex-1 mb-4">' + escapeHtml(p.description || 'No description available.') + '</p>';

    if (p.stock > 0 && isUserLoggedIn) {
      modalHtml += '<div class="flex items-center gap-2 mb-3">' +
        '<button class="qty-btn w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="modalQtyDec">&minus;</button>' +
        '<span id="modalQtyDisplay" class="w-8 text-center font-medium text-sm">1</span>' +
        '<button class="qty-btn w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" id="modalQtyInc">+</button>' +
      '</div>' +
      '<button data-add-cart="' + p.id + '" class="add-to-cart-btn w-full bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors duration-300">' +
        '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>' +
        'Add to Cart</button>';
    }

    if (!isUserLoggedIn) {
      modalHtml += '<p class="mt-3 text-xs text-center text-gray-400"><a href="login.php" class="text-primary-600 hover:underline">Login</a> to add items to cart.</p>';
    }

    // --- Comments Section ---
    modalHtml += '</div>' +
      '<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 col-span-full">' +
        '<h3 class="text-lg font-bold mb-4">Comments</h3>' +
        '<div id="commentsContainer" class="space-y-4"><p class="text-sm text-gray-400">Loading comments...</p></div>' +
      '</div>';

    content.innerHTML = modalHtml;
    content.setAttribute('data-product-id', p.id);

    if (p.stock > 0 && isUserLoggedIn) {
      var qty = 1;
      var qtyDisplay = document.getElementById('modalQtyDisplay');
      var qtyDec = document.getElementById('modalQtyDec');
      var qtyInc = document.getElementById('modalQtyInc');
      var maxQty = parseInt(p.stock) || 1;

      if (qtyDec) {
        qtyDec.addEventListener('click', function () {
          if (qty > 1) { qty--; qtyDisplay.textContent = qty; }
        });
      }
      if (qtyInc) {
        qtyInc.addEventListener('click', function () {
          if (qty < maxQty) { qty++; qtyDisplay.textContent = qty; }
        });
      }
    }

    loadComments(p.id);

    var addBtn = content.querySelector('[data-add-cart]');
    if (addBtn) {
      addBtn.addEventListener('click', async function (e) {
        e.stopPropagation();
        var productId = parseInt(addBtn.dataset.addCart);
        var result = await apiFetch('api/cart.php', {
          method: 'POST',
          body: JSON.stringify({ product_id: productId, quantity: qty || 1 })
        });
        if (result.success) {
          showToast('Added to cart!', 'success');
          if (typeof updateCartCount === 'function') updateCartCount();
          if (typeof loadCartSidebar === 'function') loadCartSidebar();
        } else {
          showToast(result.message || 'Failed to add to cart.', 'error');
        }
      });
    }
  }

  async function loadComments(productId) {
    var container = document.getElementById('commentsContainer');
    if (!container) return;

    var data = await apiFetch('api/comments.php?product_id=' + productId);
    if (!data.success) {
      container.innerHTML = '<p class="text-sm text-gray-400">Failed to load comments.</p>';
      return;
    }

    var comments = data.comments || [];
    var html = '';

    if (comments.length === 0) {
      html += '<p class="text-sm text-gray-500 dark:text-gray-400">No comments yet. Be the first to comment!</p>';
    } else {
      for (var i = 0; i < comments.length; i++) {
        var c = comments[i];
        var isAdmin = c.role_id == 1;
        var userLabel = isAdmin ? 'Admin' : escapeHtml(c.full_name);
        html += '<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">' +
          '<div class="flex items-center gap-2 mb-2">' +
            '<span class="font-medium text-sm">' + userLabel + '</span>' +
            '<span class="text-xs text-gray-400">' + c.created_at + '</span>' +
          '</div>' +
          '<p class="text-sm">' + escapeHtml(c.comment) + '</p>';

        // Show admin replies
        if (c.replies && c.replies.length > 0) {
          for (var j = 0; j < c.replies.length; j++) {
            var r = c.replies[j];
            html += '<div class="ml-6 mt-3 pl-4 border-l-2 border-primary-300 dark:border-primary-600">' +
              '<div class="flex items-center gap-2 mb-1">' +
                '<span class="font-medium text-sm text-primary-600 dark:text-primary-400">Admin</span>' +
                '<span class="text-xs text-gray-400">' + r.created_at + '</span>' +
              '</div>' +
              '<p class="text-sm">' + escapeHtml(r.comment) + '</p>' +
            '</div>';
          }
        }

        html += '</div>';
      }
    }

    // Comment form for logged-in users
    if (isUserLoggedIn) {
      html += '<form id="commentForm" class="mt-4">' +
        '<textarea id="commentInput" rows="3" placeholder="Write a comment..." class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>' +
        '<button type="submit" class="mt-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-300">Post Comment</button>' +
      '</form>';
    }

    container.innerHTML = html;

    // Handle comment submission
    var commentForm = document.getElementById('commentForm');
    if (commentForm) {
      commentForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        var input = document.getElementById('commentInput');
        var commentText = input.value.trim();
        if (!commentText) return;

        var submitBtn = commentForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting...';

        var result = await apiFetch('api/comments.php', {
          method: 'POST',
          body: JSON.stringify({ product_id: productId, comment: commentText })
        });

        if (result.success) {
          input.value = '';
          showToast('Comment posted!', 'success');
          loadComments(productId);
        } else {
          showToast(result.message || 'Failed to post comment.', 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Post Comment';
        }
      });
    }
  }

  function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  }

  function categoryIconSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>';
  }

  function productIconSvg() {
    return '<svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>';
  }
})();
