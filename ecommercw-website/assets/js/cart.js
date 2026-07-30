(function () {
  const base = window.APP_BASE || '';

  async function loadCart() {
    const data = await apiFetch(base + 'api/cart.php');
    if (!data.success) {
      showToast('Failed to load cart.', 'error');
      return null;
    }
    return data.cart;
  }

  async function updateCartCount() {
    const cart = await loadCart();
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(b => {
      if (cart && cart.count > 0) {
        b.textContent = cart.count;
        b.classList.remove('hidden');
      } else {
        b.classList.add('hidden');
      }
    });
  }

  async function loadCartSidebar() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');
    const itemsContainer = document.getElementById('cartSidebarItems');
    const totalEl = document.getElementById('cartSidebarTotal');
    const countEl = document.getElementById('cartSidebarCount');

    if (!sidebar) return;

    const cart = await loadCart();
    if (!cart) return;

    if (countEl) countEl.textContent = cart.count;

    if (!cart.items || cart.items.length === 0) {
      itemsContainer.innerHTML = `
        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <p class="text-sm font-medium">Your cart is empty</p>
        </div>`;
      totalEl.textContent = '$0.00';
      return;
    }

    itemsContainer.innerHTML = cart.items.map(item => `
      <div class="flex gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl cart-item" data-item-id="${item.id}">
        <div class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-gray-600 overflow-hidden shrink-0">
          ${item.image ? `<img src="${base}uploads/${item.image}" class="w-full h-full object-cover">` : ''}
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-medium truncate">${escapeHtml(item.title)}</h4>
          <p class="text-xs text-gray-500 dark:text-gray-400">${formatPrice(item.price)} each</p>
          <div class="flex items-center gap-2 mt-2">
            <button class="cart-qty-dec w-6 h-6 rounded-full border border-gray-300 dark:border-gray-500 flex items-center justify-center text-xs hover:bg-gray-200 dark:hover:bg-gray-600" data-id="${item.id}">−</button>
            <span class="text-sm font-medium w-6 text-center qty-display">${item.quantity}</span>
            <button class="cart-qty-inc w-6 h-6 rounded-full border border-gray-300 dark:border-gray-500 flex items-center justify-center text-xs hover:bg-gray-200 dark:hover:bg-gray-600" data-id="${item.id}">+</button>
          </div>
        </div>
        <div class="flex flex-col items-end justify-between">
          <button class="cart-item-remove text-gray-400 hover:text-red-600 transition-colors" data-id="${item.id}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
          <span class="text-sm font-bold text-primary-600 dark:text-primary-400 item-subtotal">${formatPrice(item.subtotal)}</span>
        </div>
      </div>
    `).join('');

    totalEl.textContent = formatPrice(cart.total);

    // Attach event listeners
    itemsContainer.querySelectorAll('.cart-item-remove').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const result = await apiFetch(base + 'api/cart.php', {
          method: 'DELETE',
          body: JSON.stringify({ item_id: itemId })
        });
        if (result.success) {
          showToast('Item removed.', 'success');
          loadCartSidebar();
          updateCartCount();
        } else {
          showToast(result.message || 'Failed to remove item.', 'error');
        }
      });
    });

    itemsContainer.querySelectorAll('.cart-qty-dec').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const result = await apiFetch(base + 'api/cart.php', {
          method: 'PUT',
          body: JSON.stringify({ item_id: itemId, action: 'decrease' })
        });
        if (result.success) {
          loadCartSidebar();
          updateCartCount();
        }
      });
    });

    itemsContainer.querySelectorAll('.cart-qty-inc').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = parseInt(btn.dataset.id);
        const result = await apiFetch(base + 'api/cart.php', {
          method: 'PUT',
          body: JSON.stringify({ item_id: itemId, action: 'increase' })
        });
        if (result.success) {
          loadCartSidebar();
          updateCartCount();
        } else {
          showToast(result.message || 'Cannot increase quantity.', 'error');
        }
      });
    });
  }

  function toggleCart() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');
    if (!sidebar) return;
    const isOpen = !sidebar.classList.contains('translate-x-full');
    if (isOpen) {
      sidebar.classList.add('translate-x-full');
      overlay.classList.add('hidden');
    } else {
      sidebar.classList.remove('translate-x-full');
      overlay.classList.remove('hidden');
      loadCartSidebar();
    }
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  // Expose globally
  window.updateCartCount = updateCartCount;
  window.loadCartSidebar = loadCartSidebar;
  window.toggleCart = toggleCart;

  // Auto-load cart count on page load if logged in
  document.addEventListener('DOMContentLoaded', () => {
    if (window.CURRENT_USER_ID) {
      updateCartCount();

      // Cart toggle buttons
      document.querySelectorAll('[data-toggle-cart]').forEach(el => {
        el.addEventListener('click', (e) => {
          e.preventDefault();
          toggleCart();
        });
      });

      // Overlay click to close
      const overlay = document.getElementById('cartOverlay');
      if (overlay) {
        overlay.addEventListener('click', toggleCart);
      }
    }
  });
})();
