(function () {
  const base = window.APP_BASE || '';

  // Tickets list page
  const tableBody = document.getElementById('ticketsTableBody');
  const pagination = document.getElementById('ticketsPagination');
  const statusFilter = document.getElementById('ticketStatusFilter');
  const searchInput = document.getElementById('ticketSearch');
  const statCards = document.getElementById('ticketStatCards');

  if (tableBody) {
    let currentPage = 1;
    let searchTimeout = null;

    loadTicketStats();
    loadTickets();

    if (statusFilter) {
      statusFilter.addEventListener('change', () => {
        currentPage = 1;
        loadTickets();
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          currentPage = 1;
          loadTickets();
        }, 400);
      });
    }
  }

  // Ticket view page
  const ticketViewLoading = document.getElementById('ticketViewLoading');
  const ticketViewContent = document.getElementById('ticketViewContent');

  if (ticketViewContent) {
    const params = new URLSearchParams(window.location.search);
    const ticketId = parseInt(params.get('id'));

    if (ticketId) {
      loadTicketView(ticketId);
    }

    // Reply form
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
      replyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('replySubmitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        const fd = new FormData();
        fd.append('ticket_id', ticketId);
        fd.append('message', document.getElementById('replyMessage').value.trim());
        const fileInput = document.getElementById('replyImage');
        if (fileInput.files[0]) fd.append('image', fileInput.files[0]);

        const data = await apiFetch(base + 'api/ticket_messages.php', { method: 'POST', body: fd });

        if (data.success) {
          showToast('Reply sent!', 'success');
          document.getElementById('replyMessage').value = '';
          document.getElementById('replyImage').value = '';
          loadTicketView(ticketId);
        } else {
          showToast(data.message || 'Failed to send reply.', 'error');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Reply';
        }
      });
    }

    // Close ticket
    const closeBtn = document.getElementById('closeTicketBtn');
    if (closeBtn) {
      closeBtn.addEventListener('click', async () => {
        if (!confirm('Close this ticket?')) return;
        const data = await apiFetch(base + 'api/tickets.php', {
          method: 'PUT',
          body: JSON.stringify({ id: ticketId, action: 'close' })
        });
        if (data.success) {
          showToast('Ticket closed.', 'success');
          loadTicketView(ticketId);
        } else {
          showToast(data.message || 'Failed to close ticket.', 'error');
        }
      });
    }

    // Reopen ticket
    const reopenBtn = document.getElementById('reopenTicketBtn');
    if (reopenBtn) {
      reopenBtn.addEventListener('click', async () => {
        if (!confirm('Reopen this ticket?')) return;
        const data = await apiFetch(base + 'api/tickets.php', {
          method: 'PUT',
          body: JSON.stringify({ id: ticketId, action: 'reopen' })
        });
        if (data.success) {
          showToast('Ticket reopened.', 'success');
          loadTicketView(ticketId);
        } else {
          showToast(data.message || 'Failed to reopen ticket.', 'error');
        }
      });
    }
  }

  async function loadTicketStats() {
    const data = await apiFetch(base + 'api/stats.php');
    if (!data.success || !statCards) return;
    const s = data.stats;

    statCards.innerHTML = [
      { label: 'Total Tickets', value: s.total_tickets, color: 'from-blue-500 to-blue-600' },
      { label: 'Open', value: s.open_tickets, color: 'from-yellow-500 to-yellow-600' },
      { label: 'Closed', value: s.closed_tickets, color: 'from-green-500 to-green-600' },
      { label: 'Waiting Admin', value: s.waiting_admin, color: 'from-purple-500 to-purple-600' },
      { label: 'Waiting User', value: s.waiting_user, color: 'from-orange-500 to-orange-600' },
    ].map(item => `
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 transition-colors duration-300">
        <p class="text-lg font-bold bg-gradient-to-br ${item.color} bg-clip-text text-transparent">${item.value}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">${item.label}</p>
      </div>`).join('');
  }

  async function loadTickets(page) {
    if (page) currentPage = page;
    tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>`;

    let url = base + `api/tickets.php?page=${currentPage}&per_page=15`;
    if (statusFilter && statusFilter.value) url += '&status=' + encodeURIComponent(statusFilter.value);
    if (searchInput && searchInput.value.trim()) url += '&search=' + encodeURIComponent(searchInput.value.trim());

    const data = await apiFetch(url);
    if (!data.success) {
      tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Failed to load tickets.</td></tr>`;
      return;
    }

    if (!data.tickets || data.tickets.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No tickets found.</td></tr>`;
      pagination.innerHTML = '';
      return;
    }

    tableBody.innerHTML = data.tickets.map(t => {
      const statusColors = {
        open: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        waiting_admin: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
        waiting_user: 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
        closed: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
      };
      const statusLabels = {
        open: 'Open',
        waiting_admin: 'Waiting Admin',
        waiting_user: 'Waiting User',
        closed: 'Closed',
      };
      const hasUnread = parseInt(t.unread_count) > 0;

      return `
      <tr class="${hasUnread ? 'bg-primary-50/50 dark:bg-primary-900/10' : ''}">
        <td class="px-4 py-3 font-mono text-xs">${escapeHtml(t.ticket_number)} ${hasUnread ? '<span class="w-2 h-2 inline-block rounded-full bg-primary-500"></span>' : ''}</td>
        <td class="px-4 py-3 font-medium ${hasUnread ? 'font-bold' : ''}">${escapeHtml(t.subject)}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">${escapeHtml(t.user_name || t.user_email || '')}</td>
        <td class="px-4 py-3"><span class="text-xs font-medium px-2 py-1 rounded-full ${statusColors[t.status] || statusColors.open}">${statusLabels[t.status] || t.status}</span></td>
        <td class="px-4 py-3 text-xs text-gray-500">${formatDate(t.created_at)}</td>
        <td class="px-4 py-3 text-xs text-gray-500">${formatDate(t.updated_at)}</td>
        <td class="px-4 py-3 text-right">
          <a href="ticket_view.php?id=${t.id}" class="text-primary-600 hover:underline text-xs">View</a>
        </td>
      </tr>`;
    }).join('');

    renderPagination(data.pagination);
  }

  function renderPagination(p) {
    if (p.total_pages <= 1) { pagination.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= p.total_pages; i++) {
      html += `<button data-page="${i}" class="w-9 h-9 rounded-lg text-sm font-medium transition-colors duration-300 ${i === p.page ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'}">${i}</button>`;
    }
    pagination.innerHTML = html;
    pagination.querySelectorAll('button').forEach((btn) => {
      btn.addEventListener('click', () => loadTickets(Number(btn.dataset.page)));
    });
  }

  async function loadTicketView(ticketId) {
    ticketViewLoading.classList.remove('hidden');
    ticketViewContent.classList.add('hidden');

    const data = await apiFetch(base + `api/tickets.php?id=${ticketId}`);

    ticketViewLoading.classList.add('hidden');

    if (!data.success) {
      ticketViewContent.innerHTML = '<p class="text-center text-gray-400 py-8">Ticket not found.</p>';
      ticketViewContent.classList.remove('hidden');
      return;
    }

    ticketViewContent.classList.remove('hidden');

    const ticket = data.ticket;
    const messages = data.messages || [];
    const isClosed = ticket.status === 'closed';

    document.getElementById('ticketSubjectDisplay').textContent = escapeHtml(ticket.subject);
    document.getElementById('ticketMeta').innerHTML = `
      Ticket #${escapeHtml(ticket.ticket_number)} &middot; ${escapeHtml(ticket.user_name)} (${escapeHtml(ticket.user_email || '')}) &middot; Created ${formatDate(ticket.created_at)}
    `;

    const statusColors = {
      open: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
      waiting_admin: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
      waiting_user: 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
      closed: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
    };
    const statusLabels = {
      open: 'Open',
      waiting_admin: 'Waiting for Admin',
      waiting_user: 'Waiting for User',
      closed: 'Closed',
    };
    const statusHtml = `<span class="text-xs font-medium px-2.5 py-1 rounded-full ${statusColors[ticket.status] || statusColors.open}">${statusLabels[ticket.status] || ticket.status}</span>`;

    const metaEl = document.getElementById('ticketMeta');
    metaEl.innerHTML += ` &middot; Status: ${statusHtml}`;

    // Buttons
    const closeBtn = document.getElementById('closeTicketBtn');
    const reopenBtn = document.getElementById('reopenTicketBtn');
    if (closeBtn && reopenBtn) {
      if (isClosed) {
        closeBtn.classList.add('hidden');
        reopenBtn.classList.remove('hidden');
      } else {
        closeBtn.classList.remove('hidden');
        reopenBtn.classList.add('hidden');
      }
    }

    // Reply section
    const replySection = document.getElementById('replySection');
    const closedMsg = document.getElementById('ticketClosedMsg');
    if (replySection && closedMsg) {
      if (isClosed) {
        replySection.classList.add('hidden');
        closedMsg.classList.remove('hidden');
      } else {
        replySection.classList.remove('hidden');
        closedMsg.classList.add('hidden');
      }
    }

    // Messages
    const container = document.getElementById('messagesContainer');
    container.innerHTML = messages.map(msg => {
      const isAdmin = parseInt(msg.role_id) === 1;
      return `
        <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
          <div class="max-w-[80%] ${isAdmin ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-100 dark:border-primary-800' : 'bg-gray-50 dark:bg-gray-700 border-gray-100 dark:border-gray-600'} rounded-2xl p-4 border">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs font-semibold">${escapeHtml(msg.full_name)}</span>
              <span class="text-xs px-1.5 py-0.5 rounded ${isAdmin ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300'}">${escapeHtml(msg.role_name)}</span>
            </div>
            <p class="text-sm whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
            ${msg.image ? `<img src="${base}uploads/${msg.image}" class="mt-2 max-h-40 rounded-lg">` : ''}
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-400">${formatDateTime(msg.created_at)}</span>
              <span class="text-xs text-gray-400">${msg.read_at ? 'Read' : 'Unread'}</span>
            </div>
          </div>
        </div>`;
    }).join('');
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr.replace(' ', 'T') + 'Z');
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
  }

  function formatDateTime(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr.replace(' ', 'T') + 'Z');
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
