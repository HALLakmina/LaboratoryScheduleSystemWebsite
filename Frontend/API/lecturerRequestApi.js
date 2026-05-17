const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/lecturer-request';

const sendLecturerRequest = async (payload) => {
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
        console.error('Error sending lecturer request:', error);
        throw error;
    }
};

const getLecturerRequests = async () => {
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
        console.error('Error fetching lecturer requests:', error);
        throw error;
    }
};

const updateLecturerRequest = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/update`, {
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
        console.error('Error updating lecturer request:', error);
        throw error;
    }
};

const checkLecturerRequestAvailability = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/check-availability`, {
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
        console.error('Error checking lecturer request availability:', error);
        throw error;
    }
};

const deleteLecturerRequest = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
            body: JSON.stringify({ id }),
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error deleting lecturer request:', error);
        throw error;
    }
};

export {
    sendLecturerRequest,
    getLecturerRequests,
    updateLecturerRequest,
    checkLecturerRequestAvailability,
    deleteLecturerRequest,
};
