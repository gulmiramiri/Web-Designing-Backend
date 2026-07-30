document.addEventListener('DOMContentLoaded', () => {
  const profileForm = document.getElementById('profileForm');
  const passwordForm = document.getElementById('passwordForm');

  profileForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fullName = document.getElementById('profileFullName').value.trim();
    const email = document.getElementById('profileEmail').value.trim();

    const payload = { id: window.CURRENT_USER_ID, full_name: fullName };
    if (email) payload.email = email;

    const data = await apiFetch('api/users.php', { method: 'PUT', body: payload });

    if (!data.success) {
      showToast(data.message || 'Update failed.', 'error');
      return;
    }
    document.getElementById('summaryName').textContent = fullName;
    showToast('Profile updated successfully.', 'success');
  });

  passwordForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;

    if (newPassword.length < 8) {
      showToast('New password must be at least 8 characters.', 'error');
      return;
    }

    const data = await apiFetch('api/users.php', {
      method: 'PUT',
      body: { id: window.CURRENT_USER_ID, current_password: currentPassword, new_password: newPassword },
    });

    if (!data.success) {
      showToast(data.message || 'Password update failed.', 'error');
      return;
    }
    passwordForm.reset();
    showToast('Password updated successfully.', 'success');
  });
});
