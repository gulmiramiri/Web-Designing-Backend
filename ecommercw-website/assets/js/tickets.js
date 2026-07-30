(function () {
  const base = window.APP_BASE || '';

  // Tickets list page
  const ticketsLoading = document.getElementById('ticketsLoading');
  const ticketsEmpty = document.getElementById('ticketsEmpty');
  const ticketsList = document.getElementById('ticketsList');
  const ticketsPagination = document.getElementById('ticketsPagination');
  const statusFilter = document.getElementById('ticketStatusFilter');

  if (ticketsList) {
    let currentPage = 1;
    loadTickets();

    if (statusFilter) {
      statusFilter.addEventListener('change', () => {
        currentPage = 1;
        loadTickets();
      });
    }
  }

  // Ticket create page
  const ticketForm = document.getElementById('ticketForm');
  if (ticketForm) {
    ticketForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('ticketSubmitBtn');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';

      const fd = new FormData();
      fd.append('subject', document.getElementById('ticketSubject').value.trim());
      fd.append('message', document.getElementById('ticketMessage').value.trim());
      const fileInput = document.getElementById('ticketImage');
      if (fileInput.files[0]) fd.append('image', fileInput.files[0]);

      const data = await apiFetch(base + 'api/tickets.php', { method: 'POST', body: fd });

      if (data.success) {
        showToast('Ticket created!', 'success');
        window.location.href = 'ticket_view.php?id=' + data.ticket_id;
      } else {
        showToast(data.message || 'Failed to create ticket.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Ticket';
      }
    });
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
        if (!confirm('Are you sure you want to close this ticket?')) return;
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
  }

  async function loadTickets() {
    ticketsLoading.classList.remove('hidden');
    ticketsEmpty.classList.add('hidden');
    ticketsList.classList.add('hidden');

    let url = base + `api/tickets.php?page=${currentPage}&per_page=10`;
    if (statusFilter && statusFilter.value) url += '&status=' + encodeURIComponent(statusFilter.value);

    const data = await apiFetch(url);

    ticketsLoading.classList.add('hidden');

    if (!data.success) {
      ticketsList.innerHTML = '<p class="text-center text-gray-400 py-8">Failed to load tickets.</p>';
      ticketsList.classList.remove('hidden');
      return;
    }

    if (!data.tickets || data.tickets.length === 0) {
      ticketsEmpty.classList.remove('hidden');
      ticketsPagination.innerHTML = '';
      return;
    }

    ticketsList.classList.remove('hidden');
    ticketsList.innerHTML = data.tickets.map(t => {
      const statusColors = {
        open: 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
        waiting_admin: 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300',
        waiting_user: 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300',
        closed: 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400',
      };
      const statusLabels = {
        open: 'Open',
        waiting_admin: 'Waiting for Admin',
        waiting_user: 'Waiting for You',
        closed: 'Closed',
      };
      const hasUnread = parseInt(t.unread_count) > 0;
      return `
        <a href="ticket_view.php?id=${t.id}" class="block bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 hover:shadow-md transition-all duration-300 ${hasUnread ? 'border-l-4 border-l-primary-500' : ''}">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono text-gray-400">#${escapeHtml(t.ticket_number)}</span>
                ${hasUnread ? '<span class="w-2 h-2 rounded-full bg-primary-500"></span>' : ''}
              </div>
              <h3 class="font-medium text-sm ${hasUnread ? 'font-bold' : ''}">${escapeHtml(t.subject)}</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${escapeHtml(t.user_name)} &middot; ${formatDate(t.created_at)}</p>
            </div>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full shrink-0 ${statusColors[t.status] || statusColors.open}">${statusLabels[t.status] || t.status}</span>
          </div>
        </a>`;
    }).join('');

    renderPagination(data.pagination);
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
      Ticket #${escapeHtml(ticket.ticket_number)} &middot; ${escapeHtml(ticket.user_name)} &middot; Created ${formatDate(ticket.created_at)} &middot; Updated ${formatDate(ticket.updated_at)}
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
      waiting_user: 'Waiting for You',
      closed: 'Closed',
    };
    const statusHtml = `<span class="text-xs font-medium px-2.5 py-1 rounded-full ${statusColors[ticket.status] || statusColors.open}">${statusLabels[ticket.status] || ticket.status}</span>`;

    const metaEl = document.getElementById('ticketMeta');
    metaEl.innerHTML += ` &middot; Status: ${statusHtml}`;

    // Close button
    const closeBtn = document.getElementById('closeTicketBtn');
    if (closeBtn) {
      if (isClosed) {
        closeBtn.classList.add('hidden');
      } else {
        closeBtn.classList.remove('hidden');
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
      const isOwn = parseInt(msg.user_id) === parseInt(window.CURRENT_USER_ID);
      const isAdmin = parseInt(msg.role_id) === 1;
      const isRead = msg.read_at !== null;
      return `
        <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
          <div class="max-w-[80%] ${isOwn ? 'bg-primary-50 dark:bg-primary-900/30' : 'bg-gray-50 dark:bg-gray-700'} rounded-2xl p-4 border ${isOwn ? 'border-primary-100 dark:border-primary-800' : 'border-gray-100 dark:border-gray-600'}">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs font-semibold">${escapeHtml(msg.full_name)}</span>
              <span class="text-xs px-1.5 py-0.5 rounded ${isAdmin ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300'}">${escapeHtml(msg.role_name)}</span>
            </div>
            <p class="text-sm whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
            ${msg.image ? `<img src="${base}uploads/${msg.image}" class="mt-2 max-h-40 rounded-lg">` : ''}
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-400">${formatDateTime(msg.created_at)}</span>
              ${isOwn ? `<span class="text-xs text-gray-400">${isRead ? 'Read' : 'Sent'}</span>` : ''}
            </div>
          </div>
        </div>`;
    }).join('');
  }

  function renderPagination(p) {
    if (!ticketsPagination) return;
    if (p.total_pages <= 1) { ticketsPagination.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= p.total_pages; i++) {
      html += `<button data-page="${i}" class="w-9 h-9 rounded-lg text-sm font-medium transition-colors duration-300 ${i === p.page ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'}">${i}</button>`;
    }
    ticketsPagination.innerHTML = html;
    ticketsPagination.querySelectorAll('button').forEach((btn) => {
      btn.addEventListener('click', () => {
        currentPage = Number(btn.dataset.page);
        loadTickets();
      });
    });
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
