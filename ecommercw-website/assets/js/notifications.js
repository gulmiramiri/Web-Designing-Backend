(function () {
  const base = window.APP_BASE || '';
  let pollInterval = null;

  document.addEventListener('DOMContentLoaded', () => {
    if (!window.CURRENT_USER_ID) return;

    loadNotifications();

    // Poll every 30 seconds
    pollInterval = setInterval(loadNotifications, 30000);

    // Bell toggle
    const bellBtn = document.getElementById('notifBellBtn');
    const panel = document.getElementById('notifPanel');
    if (bellBtn && panel) {
      bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.toggle('hidden');
        loadNotifications();
      });

      document.addEventListener('click', (e) => {
        if (!panel.classList.contains('hidden') && !panel.contains(e.target) && e.target !== bellBtn && !bellBtn.contains(e.target)) {
          panel.classList.add('hidden');
        }
      });
    }

    // Mark all as read
    const markBtn = document.getElementById('markNotifRead');
    if (markBtn) {
      markBtn.addEventListener('click', async () => {
        await apiFetch(base + 'api/notifications.php', {
          method: 'PUT',
          body: JSON.stringify({ all: true })
        });
        loadNotifications();
      });
    }
  });

  async function loadNotifications() {
    const data = await apiFetch(base + 'api/notifications.php');
    if (!data.success) return;

    const badge = document.getElementById('notifBadge');
    if (badge) {
      if (data.unread_count > 0) {
        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
        badge.classList.remove('hidden');
      } else {
        badge.classList.add('hidden');
      }
    }

    const list = document.getElementById('notifList');
    if (!list) return;

    if (!data.notifications || data.notifications.length === 0) {
      list.innerHTML = '<div class="p-4 text-center text-sm text-gray-400">No notifications.</div>';
      return;
    }

    list.innerHTML = data.notifications.map(n => {
      const timeAgo = getTimeAgo(n.created_at);
      const isUnread = n.is_read == 0;
      let link = '';
      if (n.ticket_number) {
        link = `<a href="${base}ticket_view.php?id=${n.ticket_id}" class="text-xs text-primary-600 hover:underline">View ticket</a>`;
      } else if (n.cart_activity_id) {
        link = `<a href="${base}cart.php" class="text-xs text-primary-600 hover:underline">View cart</a>`;
      }
      return `
        <div class="p-3 ${isUnread ? 'bg-primary-50 dark:bg-primary-900/20' : ''} hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" data-id="${n.id}">
          <p class="text-sm ${isUnread ? 'font-semibold' : ''}">${escapeHtml(n.message)}</p>
          <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-gray-400">${timeAgo}</span>
            ${link}
          </div>
        </div>`;
    }).join('');

    // Click to mark as read
    list.querySelectorAll('[data-id]').forEach(el => {
      el.addEventListener('click', async () => {
        const id = parseInt(el.dataset.id);
        await apiFetch(base + 'api/notifications.php', {
          method: 'PUT',
          body: JSON.stringify({ id: id })
        });
        loadNotifications();
      });
    });
  }

  function getTimeAgo(dateStr) {
    const now = new Date();
    const date = new Date(dateStr.replace(' ', 'T') + 'Z');
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    if (diffMins < 1) return 'just now';
    if (diffMins < 60) return diffMins + 'm ago';
    const diffHrs = Math.floor(diffMins / 60);
    if (diffHrs < 24) return diffHrs + 'h ago';
    const diffDays = Math.floor(diffHrs / 24);
    return diffDays + 'd ago';
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
