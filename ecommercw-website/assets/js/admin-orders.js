(function () {
  var base = window.APP_BASE || '';
  var currentPage = 1;
  var tableBody = document.getElementById('ordersTableBody');
  var pagination = document.getElementById('ordersPagination');

  var replyModal = document.getElementById('replyModal');
  var replyModalOverlay = document.getElementById('replyModalOverlay');
  var replyModalClose = document.getElementById('replyModalClose');
  var replyModalCancel = document.getElementById('replyModalCancel');
  var replyModalInfo = document.getElementById('replyModalInfo');
  var replyMessageInput = document.getElementById('replyMessageInput');
  var replyModalSubmit = document.getElementById('replyModalSubmit');
  var currentActivityId = null;

  document.addEventListener('DOMContentLoaded', function () {
    loadOrders();

    // Modal close handlers
    function closeModal() {
      replyModal.classList.add('hidden');
      currentActivityId = null;
      replyMessageInput.value = '';
    }

    if (replyModalOverlay) replyModalOverlay.addEventListener('click', closeModal);
    if (replyModalClose) replyModalClose.addEventListener('click', closeModal);
    if (replyModalCancel) replyModalCancel.addEventListener('click', closeModal);

    if (replyModalSubmit) {
      replyModalSubmit.addEventListener('click', submitReply);
    }

    replyMessageInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.ctrlKey) {
        e.preventDefault();
        submitReply();
      }
    });
  });

  async function loadOrders(page) {
    page = page || 1;
    currentPage = page;
    tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>';

    var data = await apiFetch(base + 'api/cart-activity.php?page=' + page);
    if (!data.success) {
      tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Failed to load orders.</td></tr>';
      return;
    }

    if (data.activities.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No orders yet.</td></tr>';
      pagination.innerHTML = '';
      return;
    }

    var html = '';
    for (var i = 0; i < data.activities.length; i++) {
      var a = data.activities[i];
      var total = (parseFloat(a.price) || 0) * (parseInt(a.quantity) || 0);
      var hasReply = parseInt(a.reply_count) > 0;
      html += '<tr>' +
        '<td class="px-4 py-3 font-medium">' + escapeHtml(a.full_name) + '</td>' +
        '<td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">' + escapeHtml(a.email) + '</td>' +
        '<td class="px-4 py-3">' +
          '<div class="flex items-center gap-2">' +
            (a.image ? '<div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-700 overflow-hidden flex-shrink-0"><img src="' + base + 'uploads/' + a.image + '" class="w-full h-full object-cover"></div>' : '') +
            '<span>' + escapeHtml(a.product_title) + '</span>' +
          '</div>' +
        '</td>' +
        '<td class="px-4 py-3">' + a.quantity + '</td>' +
        '<td class="px-4 py-3">' + formatPrice(total) + '</td>' +
        '<td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">' + a.created_at + '</td>' +
        '<td class="px-4 py-3">' +
          '<button class="reply-btn text-xs font-medium text-primary-600 hover:text-primary-700 transition-colors" data-id="' + a.id + '" data-user="' + escapeHtml(a.full_name) + '" data-product="' + escapeHtml(a.product_title) + '" data-reply="' + escapeHtml(a.last_reply || '') + '" data-count="' + a.reply_count + '">' +
            (hasReply ? 'View Reply' : 'Reply') +
          '</button>' +
        '</td>' +
      '</tr>';
    }
    tableBody.innerHTML = html;

    // Attach reply button handlers
    var replyBtns = tableBody.querySelectorAll('.reply-btn');
    for (var i = 0; i < replyBtns.length; i++) {
      (function (btn) {
        btn.addEventListener('click', function () {
          openReplyModal(parseInt(btn.dataset.id), btn.dataset.user, btn.dataset.product, btn.dataset.reply, parseInt(btn.dataset.count));
        });
      })(replyBtns[i]);
    }

    renderPagination(data.pagination);
  }

  function openReplyModal(activityId, userName, productName, lastReply, replyCount) {
    currentActivityId = activityId;
    replyModalInfo.innerHTML =
      '<strong>' + escapeHtml(userName) + '</strong> added <strong>' + escapeHtml(productName) + '</strong> to cart.' +
      (replyCount > 0 ? '<div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded text-xs">Previous reply: ' + escapeHtml(lastReply) + '</div>' : '');
    replyMessageInput.value = '';
    replyMessageInput.focus();
    replyModal.classList.remove('hidden');
  }

  async function submitReply() {
    var message = replyMessageInput.value.trim();
    if (!message) {
      showToast('Please enter a reply message.', 'warning');
      return;
    }

    replyModalSubmit.disabled = true;
    replyModalSubmit.textContent = 'Sending...';

    var data = await apiFetch(base + 'api/cart-activity-reply.php', {
      method: 'POST',
      body: { cart_activity_id: currentActivityId, message: message }
    });

    replyModalSubmit.disabled = false;
    replyModalSubmit.textContent = 'Send Reply';

    if (data.success) {
      showToast('Reply sent successfully.', 'success');
      replyModal.classList.add('hidden');
      currentActivityId = null;
      replyMessageInput.value = '';
      loadOrders(currentPage);
    } else {
      showToast(data.message || 'Failed to send reply.', 'error');
    }
  }

  function renderPagination(p) {
    if (p.total_pages <= 1) { pagination.innerHTML = ''; return; }
    var html = '';
    for (var i = 1; i <= p.total_pages; i++) {
      var active = i === p.page ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700';
      html += '<button data-page="' + i + '" class="w-9 h-9 rounded-lg text-sm font-medium transition-colors duration-300 ' + active + '">' + i + '</button>';
    }
    pagination.innerHTML = html;
    var btns = pagination.querySelectorAll('button');
    for (var i = 0; i < btns.length; i++) {
      (function (btn) {
        btn.addEventListener('click', function () { loadOrders(parseInt(btn.dataset.page)); });
      })(btns[i]);
    }
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  }
})();
