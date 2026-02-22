const BASE_URL = 'http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/timetable';

const getTimetableData = async () => {
    try {
        const response = await fetch(BASE_URL,{
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching timetable data:', error);
        throw error;
    }
};

const getSubjectCodes = async () => {
    try {
        const response = await fetch(`${BASE_URL}/subjectCodes`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching subject codes:', error);
        throw error;
    }
};

const getYears = async () => {
    try {
        const response = await fetch(`${BASE_URL}/years`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching years:', error);
        throw error;
    }
};

export {
    getTimetableData,
    getSubjectCodes,
    getYears
}