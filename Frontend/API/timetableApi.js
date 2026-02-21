const getTimetableData = async () => {
    try {
        const response = await fetch('http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/timetable',{
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
}

export {
    getTimetableData
}