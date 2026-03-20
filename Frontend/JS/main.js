// async function loadAppPage(){
//     const response = await fetch('App.html')
//     try{
//         if (!response.ok) {
//             throw new Error("Page not found: App.html");
//         }
//         const data  = await response.text();
//         document.getElementById("index-content").innerHTML = data;   
//     }
//     catch(error){
//       document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
//     };
// }

// async function loadFooterBar(){
//     try{
//         const response = await fetch('./Components/FooterBar.html')
//         if (!response.ok) {
//             throw new Error("Page not found: FooterBar.html");
//         }
//         const data =  await response.text();
//         document.body.insertAdjacentHTML("beforeend", data);
//     }
//     catch(error){
//         document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
//     }
// }

// async function loadNavigationBar (){
//     try{
//         const response = await fetch('./Components/NavigationBar.html')
//         if (!response.ok) {
//             throw new Error("Page not found: NavigationBar.html");
//         }
//         const data = await response.text()
//         document.getElementById("nav-content").innerHTML = data;
//     }
//     catch(error) {
//       document.getElementById("content").innerHTML = `<p style="color:red;">${error}</p>`;
//     };
// }


// loadAppPage()
// loadNavigationBar()
// loadFooterBar()

import { getTimetableData, getSubjectCodes, getYears, getTimeSlots, getColumnHeadings, getTimetableSettings } from '../API/timetableApi.js';
import { sendLecturerRequest } from '../API/lecturerRequestApi.js';
import { login as loginApi, logout as logoutApi } from '../API/userApi.js';

/**
 * Populates subject code select elements (filter_by_subject, subject_code) from API
 * Stores data in fullSubjectCodesData for year-based filtering
 */
const populateSubjectCodeSelects = async () => {
    try {
        const response = await getSubjectCodes();
        if (response.status !== '200' || !response.data) return;

        fullSubjectCodesData = response.data;

        const selectIds = ['filter_by_subject', 'subject_code'];
        selectIds.forEach(id => {
            const select = document.getElementById(id);
            if (!select) return;

            const defaultOption = select.id === 'filter_by_subject' ? '--FILTER--' : '--';
            select.innerHTML = `<option class="text-center font-bold" value="">${defaultOption}</option>`;
            response.data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.subject_cord || '';
                option.textContent = item.subject_cord + (item.subject ? ` - ${item.subject}` : '');
                select.appendChild(option);
            });
        });
    } catch (error) {
        console.error('Error populating subject code selects:', error);
    }
};

const isSubjectMatchYear = (subject, year) => {
    if (!year) return true;
    return String(subject.year) === String(year) || String(subject.year_id) === String(year);
};

/**
 * Updates a subject select dropdown based on selected year
 * Keeps current subject selection if still valid after filtering
 */
const updateSubjectSelectByYear = (subjectSelectId, year, defaultOptionText) => {
    const subjectSelect = document.getElementById(subjectSelectId);
    if (!subjectSelect) return;

    const previousValue = subjectSelect.value || '';
    const subjectsToShow = fullSubjectCodesData.filter(item => isSubjectMatchYear(item, year));

    subjectSelect.innerHTML = `<option class="text-center font-bold" value="">${defaultOptionText}</option>`;
    subjectsToShow.forEach(item => {
        const option = document.createElement('option');
        option.value = item.subject_cord || '';
        option.textContent = item.subject_cord + (item.subject ? ` - ${item.subject}` : '');
        subjectSelect.appendChild(option);
    });

    const isPreviousValueStillValid = subjectsToShow.some(item => String(item.subject_cord) === String(previousValue));
    subjectSelect.value = isPreviousValueStillValid ? previousValue : '';
};

/**
 * Updates timetable filter subject dropdown from filter_by_years
 */
const updateSubjectFilterByYear = (year) => {
    updateSubjectSelectByYear('filter_by_subject', year, '--FILTER--');
};

/**
 * Initialize year/subject dependency in scheduling form
 * years -> subject_code and subject_code -> years sync
 */
const initSchedulingFormFilters = () => {
    const yearSelect = document.getElementById('years');
    const subjectSelect = document.getElementById('subject_code');
    if (!yearSelect || !subjectSelect) return;

    yearSelect.addEventListener('change', () => {
        const selectedYear = yearSelect.value || '';
        updateSubjectSelectByYear('subject_code', selectedYear, '--');
    });

    subjectSelect.addEventListener('change', () => {
        const selectedSubjectCode = subjectSelect.value || '';
        if (!selectedSubjectCode) {
            yearSelect.value = '';
            return;
        }

        const matchedSubject = fullSubjectCodesData.find(item => String(item.subject_cord) === String(selectedSubjectCode));
        if (!matchedSubject) return;

        const matchedYear = String(matchedSubject.year || '');
        if (!matchedYear) return;

        if (String(yearSelect.value) !== matchedYear) {
            yearSelect.value = matchedYear;
            updateSubjectSelectByYear('subject_code', matchedYear, '--');
            subjectSelect.value = selectedSubjectCode;
        }
    });
};

/**
 * Populates year select elements (filter_by_years, years) from API
 */
const populateYearsSelects = async () => {
    try {
        const response = await getYears();
        if (response.status !== '200' || !response.data) return;
        fullYearsData = response.data;

        const selectIds = ['filter_by_years', 'years'];
        selectIds.forEach(id => {
            const select = document.getElementById(id);
            if (!select) return;

            const defaultOption = select.id === 'filter_by_years' ? '--FILTER--' : '--';
            select.innerHTML = `<option class="text-center font-bold" value="">${defaultOption}</option>`;

            response.data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.year;
                option.textContent = item.year;
                select.appendChild(option);
            });
        });
    } catch (error) {
        console.error('Error populating years selects:', error);
    }
};

const loadSchedulingReferenceData = async () => {
    try {
        const [timeSlotsResponse, columnHeadingsResponse, settingsResponse] = await Promise.all([
            getTimeSlots(),
            getColumnHeadings(),
            getTimetableSettings(),
        ]);

        fullTimeSlotsData = timeSlotsResponse.status === '200' && timeSlotsResponse.data ? timeSlotsResponse.data : [];
        fullColumnHeadingsData = columnHeadingsResponse.status === '200' && columnHeadingsResponse.data ? columnHeadingsResponse.data : [];
        fullTimetableSettingsData = settingsResponse.status === '200' ? settingsResponse.data : null;

        renderTimetableHead();
        populateTimeSlotSelect();
        populateDaySelect();
    } catch (error) {
        console.error('Error loading scheduling reference data:', error);
    }
};

const renderTimetableHead = () => {
    const tableHead = document.getElementById('timetable-head');
    if (!tableHead) return;

    tableHead.innerHTML = `
        <tr>
            <th scope="col" class="px-6 py-3">Time</th>
            ${fullColumnHeadingsData.map(item => `<th scope="col" class="px-6 py-3">${item.column_heading}</th>`).join('')}
        </tr>
    `;
};

const populateTimeSlotSelect = () => {
    const timeSlotSelect = document.getElementById('time_slot');
    if (!timeSlotSelect) return;

    timeSlotSelect.innerHTML = `<option value="">--</option>`;
    getActiveTimeSlots().forEach(item => {
        const option = document.createElement('option');
        option.value = formatTimeSlotLabel(item.start_time, item.end_time);
        option.textContent = formatTimeSlotLabel(item.start_time, item.end_time);
        timeSlotSelect.appendChild(option);
    });
};

const populateDaySelect = () => {
    const daySelect = document.getElementById('day');
    if (!daySelect) return;

    daySelect.innerHTML = `<option value="">--</option>`;
    fullColumnHeadingsData.forEach(item => {
        const option = document.createElement('option');
        option.value = item.column_heading;
        option.textContent = item.column_heading;
        daySelect.appendChild(option);
    });
};

let fullTimetableData = [];
let fullSubjectCodesData = [];
let fullYearsData = [];
let fullTimeSlotsData = [];
let fullColumnHeadingsData = [];
let fullTimetableSettingsData = null;

const formatTimePart = (timeValue) => {
    if (!timeValue) return '';

    const [rawHours, rawMinutes] = String(timeValue).split(':');
    const hours = Number(rawHours);
    const minutes = rawMinutes ?? '00';
    const displayHours = hours > 12 ? hours - 12 : hours;
    return `${displayHours}.${minutes}`;
};

const formatTimeSlotLabel = (startTime, endTime) => `${formatTimePart(startTime)}/${formatTimePart(endTime)}`;
const getTodayDateValue = () => new Date().toISOString().split('T')[0];

const getActiveTimeSlots = () => {
    if (!fullTimetableSettingsData || !Array.isArray(fullTimeSlotsData)) return fullTimeSlotsData;

    const breakRowNumber = Number(fullTimetableSettingsData.break_row_number || 0);
    if (!breakRowNumber) return fullTimeSlotsData;

    return fullTimeSlotsData.filter((_, index) => index !== breakRowNumber - 1);
};

const getCellNumberGrid = () => {
    if (!fullTimetableSettingsData) return [];

    const columnCount = Number(fullTimetableSettingsData.table_column_count || 0);
    const cellCount = Number(fullTimetableSettingsData.table_cell_count || 0);
    if (!columnCount || !cellCount) return [];

    const totalRows = Math.ceil(cellCount / columnCount);
    const grid = [];
    let currentCell = 1;

    for (let rowIndex = 0; rowIndex < totalRows; rowIndex++) {
        const row = [];
        for (let columnIndex = 0; columnIndex < columnCount; columnIndex++) {
            row.push(currentCell);
            currentCell += 1;
        }
        grid.push(row);
    }

    return grid;
};

/**
 * Renders the timetable table with the given data
 * @param {Array} data - Filtered timetable data
 */
const renderTimetableTable = (data) => {
    const tableBody = document.getElementById('timetable-body');
    if (!tableBody) return;

    const activeTimeSlots = getActiveTimeSlots();
    const columnCount = fullColumnHeadingsData.length;
    const cellGrid = getCellNumberGrid();
    const breakRowNumber = Number(fullTimetableSettingsData?.break_row_number || 0);
    const breakTimeSlot = breakRowNumber ? fullTimeSlotsData[breakRowNumber - 1] : null;
    const breakLabel = breakTimeSlot ? formatTimeSlotLabel(breakTimeSlot.start_time, breakTimeSlot.end_time) : 'Interval';

    tableBody.innerHTML = '';
    activeTimeSlots.forEach((timeSlot, rowIndex) => {
        let tableRow = '';
        if (breakRowNumber && rowIndex === breakRowNumber - 1) {
            tableRow += `
                <tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">${breakLabel}</td>
                    <td colspan="${columnCount}" class=""><p class="px-6 py-6 font-bold text-lg w-full h-full hover:bg-gray-400 text-center"> Interval </p></td>
                </tr>`;
        }
        tableRow += `<tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">`;
        const currentRow = cellGrid[rowIndex] || [];
        currentRow.forEach((cellId, columnIndex) => {
            const cellData = data.find(item => item.cell_id === cellId);
            if (columnIndex === 0) {
                tableRow += `<td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">${formatTimeSlotLabel(timeSlot.start_time, timeSlot.end_time)}</td>`;
            }
            tableRow += cellData
                ? `<td class=""><button type="button" class="timetable-cell-btn px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300" data-cell-id="${cellId}"> ${cellData.subject_cord || ''} </button></td>`
                : `<td class=""><button type="button" class="timetable-cell-btn px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300" data-cell-id="${cellId}">  </button></td>`;
        });
        tableRow += `</tr>`;
        tableBody.innerHTML += tableRow;
    });
};

/**
 * Filters timetable table by year and/or subject code
 * Uses filter_by_years and filter_by_subject dropdown values when not passed
 * @param {string} [year] - Year to filter by (optional)
 * @param {string} [subjectCode] - Subject code to filter by (optional)
 */
const filterTimetableTable = (year, subjectCode) => {
    const yearSelect = document.getElementById('filter_by_years');
    const subjectSelect = document.getElementById('filter_by_subject');

    const filterYear = year ?? (yearSelect?.value || '');
    const filterSubject = subjectCode ?? (subjectSelect?.value || '');

    const filteredData = fullTimetableData.filter(item => {
        const matchYear = !filterYear || String(item.year) === String(filterYear);
        const matchSubject = !filterSubject || String(item.subject_cord) === String(filterSubject);
        return matchYear && matchSubject;
    });

    renderTimetableTable(filteredData);
};

/**
 * Get current user role from sessionStorage (set on login)
 */
const getCurrentUserRole = () => {
    try {
        return sessionStorage.getItem('userRole') || '';
    } catch {
        return '';
    }
};

/**
 * Toggle navbar auth button between login/logout and handle logout click
 */
const initAuthNavButton = () => {
    const authBtn = document.getElementById('auth-nav-btn');
    if (!authBtn) return;

    const loginHref = authBtn.getAttribute('data-login-href') || authBtn.getAttribute('href') || 'login.php';
    const userRole = getCurrentUserRole();
    const isLoggedIn = Boolean(userRole);

    authBtn.textContent = isLoggedIn ? 'LOGOUT' : 'LOGIN';
    authBtn.setAttribute('href', isLoggedIn ? '#' : loginHref);
    authBtn.classList.remove('bg-white', 'hover:bg-sky-400', 'bg-red-600', 'hover:bg-red-700', 'text-white');
    if (isLoggedIn) {
        authBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
    } else {
        authBtn.classList.add('bg-white', 'hover:bg-sky-400');
    }

    authBtn.addEventListener('click', async (e) => {
        if (!Boolean(getCurrentUserRole())) return;

        e.preventDefault();
        const lastVisitedPage = window.location.href;
        try {
            await logoutApi();
        } catch (error) {
            console.error('Logout request failed:', error);
        } finally {
            sessionStorage.removeItem('user');
            sessionStorage.removeItem('userRole');
            window.location.href = lastVisitedPage;
        }
    });
};

/**
 * Always show scheduling-form-view on timetable cell click.
 * Logged users can open scheduling-form from lecturer-request button.
 */
const initSchedulingForm = () => {
    const formSection = document.getElementById('scheduling-form');
    const viewSection = document.getElementById('scheduling-form-view');
    const formElement = document.querySelector('#scheduling-form form');
    const cellIdInput = document.getElementById('cell_id');
    const yearSelect = document.getElementById('years');
    const subjectCodeSelect = document.getElementById('subject_code');
    const timeSlotSelect = document.getElementById('time_slot');
    const daySelect = document.getElementById('day');
    const requestDateInput = document.getElementById('request_date');
    const requestTextarea = document.getElementById('request');
    const closeBtn = document.getElementById('scheduling-form-close');
    const viewCloseBtn = document.getElementById('scheduling-form-view-close');
    const lecturerRequestBtn = document.getElementById('lecturer-request');
    const lecturerRequestFormBtn = document.getElementById('lecturer-request-form');
    const tableBody = document.getElementById('timetable-body');

    if (!formSection || !viewSection || !formElement || !cellIdInput || !yearSelect || !subjectCodeSelect || !timeSlotSelect || !daySelect || !requestDateInput || !requestTextarea || !tableBody || !lecturerRequestBtn || !lecturerRequestFormBtn) return;

    let selectedCellId = '';

    const setViewText = (id, value) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = value && String(value).trim() !== '' ? String(value) : '-';
    };

    const setLectureActionBadge = (action) => {
        const badge = document.getElementById('lecture-action-badge');
        if (!badge) return;

        badge.classList.remove('bg-green-700', 'bg-purple-800', 'bg-red-700', 'bg-gray-700');

        const normalized = String(action || '').toLowerCase();
        if (normalized === 'free') {
            badge.classList.add('bg-green-700');
            return;
        }
        if (normalized === 'active') {
            badge.classList.add('bg-purple-800');
            return;
        }
        if (normalized === 'cancel') {
            badge.classList.add('bg-red-700');
            return;
        }
        badge.classList.add('bg-gray-700');
    };

    const populateSchedulingFormView = (cellId) => {
        const cellData = fullTimetableData.find(item => String(item.cell_id) === String(cellId));
        const action = cellData?.action || 'free';

        setViewText('lecture-action', String(action).toUpperCase());
        setLectureActionBadge(action);
        setViewText('subject-name', cellData?.subject || '');
        setViewText('subject-code', cellData?.subject_cord || '');
        setViewText('lecture-in-charge', cellData?.lecturer_name || '');
        setViewText('lecture', cellData?.year || '');
        setViewText('lecture-group', cellData?.group_name || '');
        setViewText('lab', cellData?.lab || '');
    };

    const getCellScheduleMeta = (cellId) => {
        const cellGrid = getCellNumberGrid();
        const activeTimeSlots = getActiveTimeSlots();

        for (let rowIndex = 0; rowIndex < cellGrid.length; rowIndex++) {
            const columnIndex = cellGrid[rowIndex].indexOf(Number(cellId));
            if (columnIndex !== -1) {
                return {
                    timeSlot: activeTimeSlots[rowIndex] ? formatTimeSlotLabel(activeTimeSlots[rowIndex].start_time, activeTimeSlots[rowIndex].end_time) : '',
                    day: fullColumnHeadingsData[columnIndex]?.column_heading || '',
                };
            }
        }

        return {
            timeSlot: '',
            day: '',
        };
    };

    const resetSchedulingForm = () => {
        formElement.reset();
        cellIdInput.value = '';
        timeSlotSelect.value = '';
        daySelect.value = '';
        requestDateInput.value = getTodayDateValue();
    };

    const syncLecturerRequestButtons = (isLoggedIn) => {
        if (isLoggedIn) {
            lecturerRequestBtn.classList.remove('hidden');
            lecturerRequestFormBtn.classList.remove('hidden');
            return;
        }

        lecturerRequestBtn.classList.add('hidden');
        lecturerRequestFormBtn.classList.add('hidden');
    };

    const openSchedulingForm = () => {
        if (!Boolean(getCurrentUserRole())) return;

        const scheduleMeta = getCellScheduleMeta(selectedCellId);
        cellIdInput.value = selectedCellId;
        timeSlotSelect.value = scheduleMeta.timeSlot;
        daySelect.value = scheduleMeta.day;
        viewSection.classList.add('hidden');
        formSection.classList.remove('hidden');
    };

    tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.timetable-cell-btn');
        if (!btn) return;

        const isLoggedIn = Boolean(getCurrentUserRole());
        const cellId = btn.getAttribute('data-cell-id') || '';
        selectedCellId = cellId;
        populateSchedulingFormView(cellId);
        syncLecturerRequestButtons(isLoggedIn);

        viewSection.classList.remove('hidden');
        formSection.classList.add('hidden');
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            formSection.classList.add('hidden');
            resetSchedulingForm();
        });
    }

    if (viewCloseBtn) {
        viewCloseBtn.addEventListener('click', () => {
            viewSection.classList.add('hidden');
        });
    }

    lecturerRequestBtn.addEventListener('click', openSchedulingForm);
    lecturerRequestFormBtn.addEventListener('click', openSchedulingForm);
    syncLecturerRequestButtons(Boolean(getCurrentUserRole()));
    requestDateInput.min = getTodayDateValue();
    requestDateInput.value = getTodayDateValue();

    formElement.addEventListener('submit', async (e) => {
        e.preventDefault();

        const storedUser = JSON.parse(sessionStorage.getItem('user') || 'null');
        const lecturerId = storedUser?.id;
        const yearValue = yearSelect.value || '';
        const subjectCodeValue = subjectCodeSelect.value || '';
        const timeSlotValue = timeSlotSelect.value || '';
        const dayValue = daySelect.value || '';
        const requestDateValue = requestDateInput.value || '';
        const lecturerRequestValue = requestTextarea.value.trim();

        const selectedYear = fullYearsData.find(item => String(item.year) === String(yearValue));
        const selectedSubject = fullSubjectCodesData.find(item => (
            String(item.subject_cord) === String(subjectCodeValue) &&
            isSubjectMatchYear(item, yearValue)
        ));
        const selectedTimeSlot = fullTimeSlotsData.find(item => (
            formatTimeSlotLabel(item.start_time, item.end_time) === timeSlotValue
        ));
        const selectedColumnHeading = fullColumnHeadingsData.find(item => (
            String(item.column_heading).toLowerCase() === String(dayValue).toLowerCase()
        ));

        if (!lecturerId) {
            window.alert('Please log in again before sending a lecturer request.');
            return;
        }

        if (!selectedYear || !selectedSubject || !selectedTimeSlot || !selectedColumnHeading || !requestDateValue || !lecturerRequestValue) {
            window.alert('Please fill in year, subject code, time slot, day, date, and lecturer request.');
            return;
        }

        try {
            const result = await sendLecturerRequest({
                lecturer_id: lecturerId,
                subject_id: selectedSubject.subject_cord,
                year_id: selectedYear.id,
                timetable_time_slot_id: selectedTimeSlot.id,
                timetable_column_heading_id: selectedColumnHeading.id,
                date: requestDateValue,
                action: 'requested',
                lecturer_request: lecturerRequestValue,
            });

            if (result.status === '200') {
                window.alert(result.message || 'Lecturer request sent successfully.');
                formSection.classList.add('hidden');
                resetSchedulingForm();
                return;
            }

            window.alert(result.message || 'Failed to send lecturer request.');
        } catch (error) {
            window.alert(error.message || 'Network error. Please try again.');
        }
    });
};

const loadTimetableData = async () => {
    try {
        await loadSchedulingReferenceData();
        const timetableData = await getTimetableData();
        fullTimetableData = timetableData.data || [];
        renderTimetableHead();
        renderTimetableTable(fullTimetableData);

        const yearSelect = document.getElementById('filter_by_years');
        const subjectSelect = document.getElementById('filter_by_subject');
        if (yearSelect) {
            yearSelect.addEventListener('change', () => {
                const selectedYear = yearSelect.value || '';
                updateSubjectFilterByYear(selectedYear);
                filterTimetableTable();
            });
        }
        if (subjectSelect) subjectSelect.addEventListener('change', () => filterTimetableTable());

        initSchedulingFormFilters();
        initSchedulingForm();
    } catch (error) {
        console.error('Error fetching timetable data:', error);
    }
};



/**
 * Initialize login form: call login API on submit, show error or redirect on success
 */
const initLoginForm = () => {
    const form = document.getElementById('login-form');
    const errorEl = document.getElementById('login-error');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const access = document.getElementById('access')?.value?.trim() || '';
        const email = document.getElementById('email')?.value?.trim() || '';
        const password = document.getElementById('password')?.value || '';

        if (errorEl) {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
        }

        if (!email || !password || access === '-') {
            if (errorEl) {
                errorEl.textContent = 'Please select access, and enter email and password.';
                errorEl.classList.remove('hidden');
            }
            return;
        }

        try {
            const result = await loginApi(email, password);
            if (result.status === '200') {
                if (result.user) {
                    const selectedAccess = access.toLowerCase();
                    const apiRole = String(result.user.role || '').toLowerCase();
                    if (selectedAccess && selectedAccess !== apiRole) {
                        if (errorEl) {
                            errorEl.textContent = 'Selected access does not match your account role.';
                            errorEl.classList.remove('hidden');
                        }
                        return;
                    }
                    sessionStorage.setItem('user', JSON.stringify(result.user));
                    sessionStorage.setItem('userRole', result.user.role || '');
                }
                window.location.href = 'timetable.php';
            } else {
                if (errorEl) {
                    errorEl.textContent = result.message || 'Login failed.';
                    errorEl.classList.remove('hidden');
                }
            }
        } catch (err) {
            if (errorEl) {
                errorEl.textContent = err.message || 'Network error. Please try again.';
                errorEl.classList.remove('hidden');
            }
        }
    });
};

initAuthNavButton();
initLoginForm();

if (document.getElementById('timetable-body')) {
    loadTimetableData();
    populateSubjectCodeSelects();
    populateYearsSelects();
}
