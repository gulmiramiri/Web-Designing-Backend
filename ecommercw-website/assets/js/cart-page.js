(function () {
  const base = window.APP_BASE || '';

  document.addEventListener('DOMContentLoaded', () => {
    if (window.CURRENT_USER_ID) {
      renderCartPage();
    }
  });

  async function renderCartPage() {
    const loading = document.getElementById('cartLoading');
    const empty = document.getElementById('cartEmpty');
    const content = document.getElementById('cartContent');
    const clearBtn = document.getElementById('clearCartBtn');

    const data = await apiFetch(base + 'api/cart.php');
    if (!data.success) {
      loading.innerHTML = '<p class="text-center text-gray-400 py-8">Failed to load cart.</p>';
      return;
    }

    loading.classList.add('hidden');

    if (!data.cart.items || data.cart.items.length === 0) {
      empty.classList.remove('hidden');
      content.classList.add('hidden');
      return;
    }

    empty.classList.add('hidden');
    content.classList.remove('hidden');
    clearBtn.classList.remove('hidden');

    const container = document.getElementById('cartItemsContainer');
    const totalItems = document.getElementById('cartTotalItems');
    const subtotalEl = document.getElementById('cartSubtotal');

    let totalQty = 0;

    container.innerHTML = data.cart.items.map(item => {
      totalQty += parseInt(item.quantity);
      return `
      <div class="flex items-center gap-4 p-4 cart-page-item" data-item-id="${item.id}">
        <div class="w-20 h-20 rounded-xl bg-gray-100 dark:bg-gray-700 overflow-hidden shrink-0">
          ${item.image ? `<img src="${base}uploads/${item.image}" class="w-full h-full object-cover">` : ''}
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-sm">${escapeHtml(item.title)}</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(item.sku || '')}</p>
          <p class="text-sm font-bold text-primary-600 dark:text-primary-400 mt-1">${formatPrice(item.price)}</p>
        </div>
        <div class="flex items-center gap-2">
          <button class="page-qty-dec w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" data-id="${item.id}">−</button>
          <input type="number" class="page-qty-input w-14 text-center text-sm font-medium bg-transparent border border-gray-300 dark:border-gray-600 rounded-lg py-1 px-2 focus:outline-none focus:ring-2 focus:ring-primary-500" value="${item.quantity}" min="1" max="${item.stock}" data-id="${item.id}">
          <button class="page-qty-inc w-8 h-8 rounded-full border border-gray-300 dark:border-gray-600 flex items-center justify-center text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" data-id="${item.id}">+</button>
        </div>
        <div class="text-right min-w-[80px]">
          <p class="font-bold text-sm page-item-subtotal">${formatPrice(item.subtotal)}</p>
        </div>
        <button class="page-item-remove text-gray-400 hover:text-red-600 transition-colors p-1" data-id="${item.id}">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        </button>
      </div>`;
    }).join('');

    totalItems.textContent = totalQty;
    subtotalEl.textContent = formatPrice(data.cart.total);

    // Event listeners
    container.querySelectorAll('.page-item-remove').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const result = await apiFetch(base + 'api/cart.php', {
          method: 'DELETE',
          body: JSON.stringify({ item_id: itemId })
        });
        if (result.success) {
          showToast('Item removed.', 'success');
          renderCartPage();
          if (typeof updateCartCount === 'function') updateCartCount();
        }
      });
    });

    container.querySelectorAll('.page-qty-dec').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const input = container.querySelector(`.page-qty-input[data-id="${itemId}"]`);
        const val = parseInt(input.value) || 1;
        if (val > 1) {
          input.value = val - 1;
          await updateCartItem(itemId, 'update', val - 1);
        }
      });
    });

    container.querySelectorAll('.page-qty-inc').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const input = container.querySelector(`.page-qty-input[data-id="${itemId}"]`);
        const val = parseInt(input.value) || 1;
        const max = parseInt(input.max) || 999;
        if (val < max) {
          input.value = val + 1;
          await updateCartItem(itemId, 'update', val + 1);
        } else {
          showToast('Cannot exceed available stock.', 'warning');
        }
      });
    });

    container.querySelectorAll('.page-qty-input').forEach(input => {
      input.addEventListener('change', async () => {
        const itemId = parseInt(input.dataset.id);
        const val = parseInt(input.value) || 1;
        const max = parseInt(input.max) || 999;
        if (val > max) {
          showToast('Cannot exceed available stock.', 'warning');
          input.value = max;
          await updateCartItem(itemId, 'update', max);
        } else if (val < 1) {
          input.value = 1;
          await updateCartItem(itemId, 'update', 1);
        } else {
          await updateCartItem(itemId, 'update', val);
        }
      });
    });

    clearBtn.addEventListener('click', async () => {
      const result = await apiFetch(base + 'api/cart.php', {
        method: 'DELETE',
        body: JSON.stringify({ clear_all: true })
      });
      if (result.success) {
        showToast('Cart cleared.', 'success');
        renderCartPage();
        if (typeof updateCartCount === 'function') updateCartCount();
      }
    });
  }

  async function updateCartItem(itemId, action, quantity) {
    const result = await apiFetch(base + 'api/cart.php', {
      method: 'PUT',
      body: JSON.stringify({ item_id: itemId, action: action, quantity: quantity })
    });
    if (result.success) {
      renderCartPage();
      if (typeof updateCartCount === 'function') updateCartCount();
    } else {
      showToast(result.message || 'Failed to update cart.', 'error');
    }
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
