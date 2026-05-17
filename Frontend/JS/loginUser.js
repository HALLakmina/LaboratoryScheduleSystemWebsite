import { logout as logoutApi } from '../API/userApi.js';

const clearStoredUser = () => {
    sessionStorage.removeItem('user');
    sessionStorage.removeItem('userRole');
};

const setStoredUser = (user) => {
    sessionStorage.setItem('user', JSON.stringify(user));
    sessionStorage.setItem('userRole', user?.role || '');
};

const getCurrentUserRole = () => {
    try {
        return sessionStorage.getItem('userRole') || '';
    } catch {
        return '';
    }
};

const getStoredUser = () => {
    try {
        return JSON.parse(sessionStorage.getItem('user') || 'null');
    } catch {
        return null;
    }
};

const initAuthNavButton = () => {
    const authBtn = document.getElementById('auth-nav-btn');
    const adminNavItems = Array.from(document.querySelectorAll('.admin-nav-item'));
    if (!authBtn) return;

    const loginHref = authBtn.getAttribute('data-login-href') || authBtn.getAttribute('href') || 'login.php';
    const userRole = getCurrentUserRole();
    const isLoggedIn = Boolean(userRole);
    const isAdmin = userRole === 'admin';

    authBtn.textContent = isLoggedIn ? 'LOGOUT' : 'LOGIN';
    authBtn.setAttribute('href', isLoggedIn ? '#' : loginHref);
    authBtn.classList.remove('bg-white', 'hover:bg-sky-400', 'bg-red-600', 'hover:bg-red-700', 'text-white');

    if (isLoggedIn) {
        authBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
    } else {
        authBtn.classList.add('bg-white', 'hover:bg-sky-400');
    }

    adminNavItems.forEach((adminNavItem) => {
        adminNavItem.classList.toggle('hidden', !isAdmin);
    });

    authBtn.addEventListener('click', async (event) => {
        if (!getCurrentUserRole()) return;

        event.preventDefault();
        const lastVisitedPage = window.location.href;
        try {
            await logoutApi();
        } catch (error) {
            console.error('Logout request failed:', error);
        } finally {
            clearStoredUser();
            window.location.href = lastVisitedPage;
        }
    });
};

export { getCurrentUserRole, getStoredUser, initAuthNavButton, setStoredUser };
