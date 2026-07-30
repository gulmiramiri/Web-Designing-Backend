(function () {
  const base = window.APP_BASE;
  let deleteTargetId = null;

  const tableBody = document.getElementById('usersTableBody');
  const form = document.getElementById('userForm');

  document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
    document.getElementById('addUserBtn').addEventListener('click', () => openUserForm());
    form.addEventListener('submit', submitUserForm);
    document.getElementById('confirmDeleteBtn').addEventListener('click', confirmDelete);
  });

  async function loadUsers() {
    tableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Loading...</td></tr>`;
    const data = await apiFetch(base + 'api/users.php');
    if (!data.success) {
      tableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Failed to load users.</td></tr>`;
      return;
    }
    if (data.users.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No users found.</td></tr>`;
      return;
    }
    tableBody.innerHTML = data.users.map((u) => `
      <tr>
        <td class="px-4 py-3 font-medium">${escapeHtml(u.full_name)}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">@${escapeHtml(u.username)}</td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">${escapeHtml(u.email)}</td>
        <td class="px-4 py-3">
          <span class="text-xs px-2 py-0.5 rounded-full capitalize ${u.role === 'admin' ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'}">${escapeHtml(u.role)}</span>
        </td>
        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">${new Date(u.created_at).toLocaleDateString()}</td>
        <td class="px-4 py-3 text-right space-x-2">
          <button data-edit="${u.id}" data-name="${escapeHtml(u.full_name)}" data-username="${escapeHtml(u.username)}" data-email="${escapeHtml(u.email)}" data-role="${u.role}" class="text-primary-600 hover:underline">Edit</button>
          <button data-delete="${u.id}" class="text-red-600 hover:underline">Delete</button>
        </td>
      </tr>`).join('');

    tableBody.querySelectorAll('[data-edit]').forEach((btn) =>
      btn.addEventListener('click', () => openUserForm(btn.dataset)));
    tableBody.querySelectorAll('[data-delete]').forEach((btn) =>
      btn.addEventListener('click', () => { deleteTargetId = Number(btn.dataset.delete); openModal('deleteModal'); }));
  }

  function openUserForm(ds = null) {
    form.reset();
    const id = ds ? ds.edit : '';
    document.getElementById('userId').value = id;
    document.getElementById('userFullName').value = ds ? ds.name : '';
    document.getElementById('userUsername').value = ds ? ds.username : '';
    document.getElementById('userEmail').value = ds ? ds.email : '';
    document.getElementById('userRole').value = ds ? ds.role : 'user';
    document.getElementById('userFormTitle').textContent = id ? 'Edit User' : 'Add User';
    document.getElementById('userPassword').required = !id;
    document.getElementById('usernameFieldWrapper').style.display = id ? 'none' : 'block';
    openModal('userFormModal');
  }

  async function submitUserForm(e) {
    e.preventDefault();
    const id = document.getElementById('userId').value;
    const submitBtn = document.getElementById('userSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
      let data;
      if (id) {
        const payload = {
          id,
          full_name: document.getElementById('userFullName').value.trim(),
          email: document.getElementById('userEmail').value.trim(),
          role: document.getElementById('userRole').value,
        };
        const newPassword = document.getElementById('userPassword').value;
        if (newPassword) payload.new_password = newPassword;
        data = await apiFetch(base + 'api/users.php', { method: 'PUT', body: payload });
      } else {
        const payload = {
          full_name: document.getElementById('userFullName').value.trim(),
          username: document.getElementById('userUsername').value.trim(),
          email: document.getElementById('userEmail').value.trim(),
          password: document.getElementById('userPassword').value,
          role: document.getElementById('userRole').value,
        };
        data = await apiFetch(base + 'api/users.php', { method: 'POST', body: payload });
      }

      if (!data.success) {
        showToast(data.message || 'Failed to save user.', 'error');
        return;
      }
      showToast(id ? 'User updated.' : 'User created.', 'success');
      closeModal('userFormModal');
      loadUsers();
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Save User';
    }
  }

  async function confirmDelete() {
    if (!deleteTargetId) return;
    const data = await apiFetch(base + 'api/users.php', { method: 'DELETE', body: `id=${deleteTargetId}` });
    if (!data.success) {
      showToast(data.message || 'Failed to delete user.', 'error');
      return;
    }
    showToast('User deleted.', 'success');
    closeModal('deleteModal');
    loadUsers();
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }
})();
