/**
 * Shared fetch wrapper + toast notification helper.
 * Used by every page that talks to the PHP API.
 */

async function apiFetch(url, options = {}) {
  const opts = Object.assign({ credentials: 'same-origin' }, options);

  if (opts.body && !(opts.body instanceof FormData) && typeof opts.body !== 'string') {
    opts.headers = Object.assign({ 'Content-Type': 'application/json' }, opts.headers || {});
    opts.body = JSON.stringify(opts.body);
  }

  let response;
  try {
    response = await fetch(url, opts);
  } catch (networkError) {
    showToast('Network error. Please check your connection.', 'error');
    throw networkError;
  }

  let data = {};
  try {
    data = await response.json();
  } catch (parseError) {
    data = { success: false, message: 'Unexpected server response.' };
  }

  if (!response.ok && !data.hasOwnProperty('success')) {
    data.success = false;
  }

  return data;
}

function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const colors = {
    success: 'bg-green-600',
    error: 'bg-red-600',
    info: 'bg-primary-600',
    warning: 'bg-yellow-500',
  };

  const toast = document.createElement('div');
  toast.className = `toast ${colors[type] || colors.info} text-white text-sm font-medium px-4 py-3 rounded-lg shadow-lg flex items-start justify-between gap-2`;
  toast.innerHTML = `<span>${message}</span><button class="opacity-80 hover:opacity-100" aria-label="Dismiss">&times;</button>`;

  toast.querySelector('button').addEventListener('click', () => removeToast(toast));
  container.appendChild(toast);

  setTimeout(() => removeToast(toast), 4000);
}

function removeToast(toast) {
  toast.classList.add('toast-out');
  setTimeout(() => toast.remove(), 280);
}

function formatPrice(value) {
  return '$' + Number(value).toFixed(2);
}

function debounce(fn, delay = 400) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(null, args), delay);
  };
}
