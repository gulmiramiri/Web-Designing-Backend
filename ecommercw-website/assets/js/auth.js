document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const togglePassword = document.getElementById('togglePassword');

  if (togglePassword) {
    togglePassword.addEventListener('click', () => {
      const input = document.getElementById('password');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  }

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors(loginForm);
      setLoading('loginBtn', 'loginBtnText', 'loginSpinner', true, 'Logging in...');

      const payload = {
        identifier: document.getElementById('identifier').value.trim(),
        password: document.getElementById('password').value,
        remember: document.getElementById('remember').checked,
      };

      const data = await apiFetch('api/login.php', { method: 'POST', body: payload });

      setLoading('loginBtn', 'loginBtnText', 'loginSpinner', false, 'Log In');

      if (!data.success) {
        showToast(data.message || 'Login failed.', 'error');
        return;
      }

      showToast('Welcome back, ' + data.user.full_name + '!', 'success');
      setTimeout(() => {
        window.location.href = data.user.role === 'admin' ? 'admin/index.php' : 'dashboard.php';
      }, 600);
    });
  }

  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors(registerForm);
      setLoading('registerBtn', 'registerBtnText', 'registerSpinner', true, 'Creating account...');

      const payload = {
        full_name: document.getElementById('full_name').value.trim(),
        username: document.getElementById('username').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value,
        confirm_password: document.getElementById('confirm_password').value,
      };

      const data = await apiFetch('api/register.php', { method: 'POST', body: payload });

      setLoading('registerBtn', 'registerBtnText', 'registerSpinner', false, 'Create Account');

      if (!data.success) {
        if (data.errors) {
          Object.entries(data.errors).forEach(([field, message]) => {
            const el = registerForm.querySelector(`[data-error="${field}"]`);
            if (el) { el.textContent = message; el.classList.remove('hidden'); }
          });
        }
        showToast(data.message || 'Registration failed.', 'error');
        return;
      }

      showToast('Account created! Redirecting to login...', 'success');
      setTimeout(() => { window.location.href = 'login.php'; }, 1000);
    });
  }

  function clearErrors(form) {
    form.querySelectorAll('[data-error]').forEach((el) => {
      el.textContent = '';
      el.classList.add('hidden');
    });
  }

  function setLoading(btnId, textId, spinnerId, loading, text) {
    const btn = document.getElementById(btnId);
    const label = document.getElementById(textId);
    const spinner = document.getElementById(spinnerId);
    if (!btn) return;
    btn.disabled = loading;
    btn.classList.toggle('opacity-70', loading);
    label.textContent = text;
    spinner.classList.toggle('hidden', !loading);
  }
});
