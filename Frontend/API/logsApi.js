const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/logs';

const getActionLogs = async (page = 1, perPage = 20) => {
    const params = new URLSearchParams({ page, per_page: perPage });
    const response = await fetch(`${BASE_URL}/action-logs?${params}`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
    });
    return response.json();
};

export { getActionLogs };
