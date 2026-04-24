import { login as loginApi } from '../API/userApi.js';
import { setStoredUser } from './loginUser.js';

const initLoginForm = () => {
    const form = document.getElementById('login-form');
    const errorEl = document.getElementById('login-error');
    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const access = document.getElementById('access')?.value?.trim() || '';
        const email = document.getElementById('email')?.value?.trim() || '';
        const password = document.getElementById('password')?.value || '';

        if (errorEl) {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
        }

        if (!email || !password || access === '-') {
            if (errorEl) {
                errorEl.textContent = 'Please select access, and enter email and password.';
                errorEl.classList.remove('hidden');
            }
            return;
        }

        try {
            const result = await loginApi(email, password);
            if (result.status === '200') {
                if (result.user) {
                    const selectedAccess = access.toLowerCase();
                    const apiRole = String(result.user.role || '').toLowerCase();
                    if (selectedAccess && selectedAccess !== apiRole) {
                        if (errorEl) {
                            errorEl.textContent = 'Selected access does not match your account role.';
                            errorEl.classList.remove('hidden');
                        }
                        return;
                    }

                    setStoredUser(result.user);
                }

                window.location.href = 'timetable.php';
                return;
            }

            if (errorEl) {
                errorEl.textContent = result.message || 'Login failed.';
                errorEl.classList.remove('hidden');
            }
        } catch (error) {
            if (errorEl) {
                errorEl.textContent = error.message || 'Network error. Please try again.';
                errorEl.classList.remove('hidden');
            }
        }
    });
};

export { initLoginForm };
