const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/news';

const getNews = async () => {
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
        console.error('Error fetching news:', error);
        throw error;
    }
};

const getNewsById = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/byId?id=${encodeURIComponent(id)}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include',
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching news by id:', error);
        throw error;
    }
};

const createNews = async (formData) => {
    try {
        const response = await fetch(BASE_URL, {
            method: 'POST',
            credentials: 'include',
            body: formData,
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error creating news:', error);
        throw error;
    }
};

const updateNews = async (formData) => {
    try {
        const response = await fetch(`${BASE_URL}/update`, {
            method: 'POST',
            credentials: 'include',
            body: formData,
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error updating news:', error);
        throw error;
    }
};

const deleteNews = async (id) => {
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
        console.error('Error deleting news:', error);
        throw error;
    }
};

export {
    getNews,
    getNewsById,
    createNews,
    updateNews,
    deleteNews,
};
