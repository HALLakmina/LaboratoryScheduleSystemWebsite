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

const getTimeSlots = async () => {
    try {
        const response = await fetch(`${BASE_URL}/timeSlots`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching time slots:', error);
        throw error;
    }
};

const getColumnHeadings = async () => {
    try {
        const response = await fetch(`${BASE_URL}/columnHeadings`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching column headings:', error);
        throw error;
    }
};

const getTimetableSettings = async () => {
    try {
        const response = await fetch(`${BASE_URL}/settings`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching timetable settings:', error);
        throw error;
    }
};

const createYear = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/years`, {
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
        console.error('Error creating year:', error);
        throw error;
    }
};

const updateYear = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/years/update`, {
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
        console.error('Error updating year:', error);
        throw error;
    }
};

const deleteYear = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/years/delete`, {
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
        console.error('Error deleting year:', error);
        throw error;
    }
};

const getLectureGroups = async () => {
    try {
        const response = await fetch(`${BASE_URL}/lectureGroups`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching lecture groups:', error);
        throw error;
    }
};

const createLectureGroup = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/lectureGroups`, {
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
        console.error('Error creating lecture group:', error);
        throw error;
    }
};

const updateLectureGroup = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/lectureGroups/update`, {
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
        console.error('Error updating lecture group:', error);
        throw error;
    }
};

const deleteLectureGroup = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/lectureGroups/delete`, {
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
        console.error('Error deleting lecture group:', error);
        throw error;
    }
};

const getLabs = async () => {
    try {
        const response = await fetch(`${BASE_URL}/labs`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching labs:', error);
        throw error;
    }
};

const createLab = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/labs`, {
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
        console.error('Error creating lab:', error);
        throw error;
    }
};

const updateLab = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/labs/update`, {
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
        console.error('Error updating lab:', error);
        throw error;
    }
};

const deleteLab = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/labs/delete`, {
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
        console.error('Error deleting lab:', error);
        throw error;
    }
};

const getTimetableCells = async () => {
    try {
        const response = await fetch(`${BASE_URL}/cells`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching timetable cells:', error);
        throw error;
    }
};

const createTimetableRecord = async (payload) => {
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
        console.error('Error creating timetable record:', error);
        throw error;
    }
};

const updateTimetableRecord = async (payload) => {
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
        console.error('Error updating timetable record:', error);
        throw error;
    }
};

const deleteTimetableRecord = async (id) => {
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
        console.error('Error deleting timetable record:', error);
        throw error;
    }
};

const updateTimetableSettings = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/settings/update`, {
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
        console.error('Error updating timetable settings:', error);
        throw error;
    }
};

const resetTimetableSettings = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/settings/reset`, {
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
        console.error('Error resetting timetable settings:', error);
        throw error;
    }
};

const createColumnHeading = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/columnHeadings`, {
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
        console.error('Error creating column heading:', error);
        throw error;
    }
};

const updateColumnHeading = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/columnHeadings/update`, {
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
        console.error('Error updating column heading:', error);
        throw error;
    }
};

const deleteColumnHeading = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/columnHeadings/delete`, {
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
        console.error('Error deleting column heading:', error);
        throw error;
    }
};

const createTimeSlot = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/timeSlots`, {
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
        console.error('Error creating time slot:', error);
        throw error;
    }
};

const updateTimeSlot = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/timeSlots/update`, {
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
        console.error('Error updating time slot:', error);
        throw error;
    }
};

const deleteTimeSlot = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/timeSlots/delete`, {
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
        console.error('Error deleting time slot:', error);
        throw error;
    }
};

const createSubject = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/subjects`, {
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
        console.error('Error creating subject:', error);
        throw error;
    }
};

const updateSubject = async (payload) => {
    try {
        const response = await fetch(`${BASE_URL}/subjects/update`, {
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
        console.error('Error updating subject:', error);
        throw error;
    }
};

const deleteSubject = async (id) => {
    try {
        const response = await fetch(`${BASE_URL}/subjects/delete`, {
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
        console.error('Error deleting subject:', error);
        throw error;
    }
};

export {
    getTimetableData,
    getSubjectCodes,
    getYears,
    createYear,
    updateYear,
    deleteYear,
    getTimeSlots,
    getColumnHeadings,
    getTimetableSettings,
    getLectureGroups,
    createLectureGroup,
    updateLectureGroup,
    deleteLectureGroup,
    getLabs,
    createLab,
    updateLab,
    deleteLab,
    getTimetableCells,
    createTimetableRecord,
    updateTimetableRecord,
    deleteTimetableRecord,
    updateTimetableSettings,
    resetTimetableSettings,
    createColumnHeading,
    updateColumnHeading,
    deleteColumnHeading,
    createTimeSlot,
    updateTimeSlot,
    deleteTimeSlot,
    createSubject,
    updateSubject,
    deleteSubject,
}
