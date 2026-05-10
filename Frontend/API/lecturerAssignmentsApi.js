const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/lecturer-assignment';

const getResponsibilities = async () => {
    const response = await fetch(`${BASE_URL}/responsibilities`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
    });
    return response.json();
};

const createResponsibility = async (payload) => {
    const response = await fetch(`${BASE_URL}/responsibilities`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload),
    });
    return response.json();
};

const updateResponsibility = async (payload) => {
    const response = await fetch(`${BASE_URL}/responsibilities/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload),
    });
    return response.json();
};

const deleteResponsibility = async (id) => {
    const response = await fetch(`${BASE_URL}/responsibilities/delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id }),
    });
    return response.json();
};

const getAssignments = async () => {
    const response = await fetch(`${BASE_URL}/assignments`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
    });
    return response.json();
};

const createAssignment = async (payload) => {
    const response = await fetch(`${BASE_URL}/assignments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload),
    });
    return response.json();
};

const updateAssignment = async (payload) => {
    const response = await fetch(`${BASE_URL}/assignments/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify(payload),
    });
    return response.json();
};

const deleteAssignment = async (id) => {
    const response = await fetch(`${BASE_URL}/assignments/delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ id }),
    });
    return response.json();
};

export {
    getResponsibilities,
    createResponsibility,
    updateResponsibility,
    deleteResponsibility,
    getAssignments,
    createAssignment,
    updateAssignment,
    deleteAssignment,
};
