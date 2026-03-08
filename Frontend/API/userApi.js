const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/user';

/**
 * Get all users - GET /api/v1/user
 */
const getUsers = async () => {
    try {
        const response = await fetch(BASE_URL, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching users:', error);
        throw error;
    }
};

/**
 * Create user - POST /api/v1/user
 * @param {Object} payload - User data (initials, initials_stand_for, first_name, last_name, honorifics, nic, email, mobile_number, password, role, created_by, updated_by)
 */
const createUser = async (payload) => {
    try {
        const response = await fetch(BASE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error creating user:', error);
        throw error;
    }
};

/**
 * User login - POST /api/v1/user/login
 * @param {string} email - User email
 * @param {string} password - User password
 */
const login = async (email, password) => {
    try {
        const response = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({ email, password }),
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error logging in:', error);
        throw error;
    }
};

/**
 * User logout - POST /api/v1/user/logout
 */
const logout = async () => {
    try {
        const response = await fetch(`${BASE_URL}/logout`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error logging out:', error);
        throw error;
    }
};

export {
    getUsers,
    createUser,
    login,
    logout,
};
