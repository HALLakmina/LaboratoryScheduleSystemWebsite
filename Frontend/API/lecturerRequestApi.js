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

export {
    sendLecturerRequest,
};
