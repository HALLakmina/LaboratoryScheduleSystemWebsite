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

import { getTimetableData, getSubjectCodes, getYears, createYear, updateYear, deleteYear, getTimeSlots, getColumnHeadings, getTimetableSettings, getLectureGroups, createLectureGroup, updateLectureGroup, deleteLectureGroup, getLabs, createLab, updateLab, deleteLab, getTimetableCells, createTimetableRecord, updateTimetableRecord, deleteTimetableRecord, updateTimetableSettings, resetTimetableSettings, createColumnHeading, updateColumnHeading, deleteColumnHeading, createTimeSlot, updateTimeSlot, deleteTimeSlot, createSubject, updateSubject, deleteSubject } from '../API/timetableApi.js';
import { sendLecturerRequest, getLecturerRequests, updateLecturerRequest, checkLecturerRequestAvailability, deleteLecturerRequest } from '../API/lecturerRequestApi.js';
import { getNews, createNews, updateNews, deleteNews } from '../API/newsApi.js';
import { getUsers, createUser, updateUser, deleteUser, resetUserPassword, login as loginApi, logout as logoutApi } from '../API/userApi.js';

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
        const [timeSlotsResponse, columnHeadingsResponse, settingsResponse, lectureGroupsResponse] = await Promise.all([
            getTimeSlots(),
            getColumnHeadings(),
            getTimetableSettings(),
            getLectureGroups(),
        ]);

        fullTimeSlotsData = timeSlotsResponse.status === '200' && timeSlotsResponse.data ? timeSlotsResponse.data : [];
        fullColumnHeadingsData = columnHeadingsResponse.status === '200' && columnHeadingsResponse.data ? columnHeadingsResponse.data : [];
        fullTimetableSettingsData = settingsResponse.status === '200' ? settingsResponse.data : null;
        fullLectureGroupsData = lectureGroupsResponse.status === '200' && lectureGroupsResponse.data ? lectureGroupsResponse.data : [];

        renderTimetableHead();
        populateTimeSlotSelect();
        populateDaySelect();
        populateLectureGroupSelect();
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

const populateLectureGroupSelect = () => {
    const lectureGroupSelect = document.getElementById('lecture_group_select');
    if (!lectureGroupSelect) return;

    lectureGroupSelect.innerHTML = `<option value="">--</option>`;
    fullLectureGroupsData.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id || '';
        option.textContent = item.group_name || '';
        lectureGroupSelect.appendChild(option);
    });
};

let fullTimetableData = [];
let fullSubjectCodesData = [];
let fullYearsData = [];
let fullTimeSlotsData = [];
let fullColumnHeadingsData = [];
let fullTimetableSettingsData = null;
let fullLectureGroupsData = [];

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

const getStoredUser = () => {
    try {
        return JSON.parse(sessionStorage.getItem('user') || 'null');
    } catch {
        return null;
    }
};

/**
 * Toggle navbar auth button between login/logout and handle logout click
 */
const initAuthNavButton = () => {
    const authBtn = document.getElementById('auth-nav-btn');
    const adminNavItem = document.getElementById('admin-nav-item');
    if (!authBtn) return;

    const loginHref = authBtn.getAttribute('data-login-href') || authBtn.getAttribute('href') || 'login.php';
    const userRole = getCurrentUserRole();
    const isLoggedIn = Boolean(userRole);
    const isAdmin = userRole === 'admin';

    authBtn.textContent = isLoggedIn ? 'LOGOUT' : 'LOGIN';
    authBtn.setAttribute('href', isLoggedIn ? '#' : loginHref);
    authBtn.classList.remove('bg-white', 'hover:bg-sky-400', 'bg-red-600', 'hover:bg-red-700', 'text-white');
    if (isLoggedIn) {
        authBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
    } else {
        authBtn.classList.add('bg-white', 'hover:bg-sky-400');
    }

    if (adminNavItem) {
        adminNavItem.classList.toggle('hidden', !isAdmin);
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

const initNewsPage = async () => {
    const newsList = document.getElementById('news-list');
    const newsViewer = document.getElementById('news-viewer');
    const newsViewerClose = document.getElementById('news-viewer-close');
    const newsViewerImage = document.getElementById('news-viewer-image');
    const newsViewerTitle = document.getElementById('news-viewer-title');
    const newsViewerMeta = document.getElementById('news-viewer-meta');
    const newsViewerDescription = document.getElementById('news-viewer-description');

    if (!newsList || !newsViewer || !newsViewerClose || !newsViewerImage || !newsViewerTitle || !newsViewerMeta || !newsViewerDescription) {
        return;
    }

    const getImageUrl = (filePath) => {
        if (!filePath) return '';
        if (String(filePath).startsWith('http')) return filePath;
        return `http://localhost/LaboratoryScheduleSystemWebsite/${String(filePath).replace(/^\/+/, '')}`;
    };

    const buildMetaText = (newsItem) => {
        const parts = [];
        if (newsItem.start_date) parts.push(`Start: ${newsItem.start_date}`);
        if (newsItem.end_date) parts.push(`End: ${newsItem.end_date}`);
        if (newsItem.start_at) parts.push(`From: ${newsItem.start_at}`);
        if (newsItem.end_at) parts.push(`To: ${newsItem.end_at}`);
        return parts.join(' | ');
    };

    const truncateText = (text, maxLength = 160) => {
        const safeText = String(text || '').trim();
        if (safeText.length <= maxLength) return safeText;
        return `${safeText.slice(0, maxLength).trim()}...`;
    };

    const openNewsViewer = (newsItem) => {
        newsViewerImage.src = getImageUrl(newsItem.file_path);
        newsViewerImage.alt = newsItem.title || 'news image';
        newsViewerTitle.textContent = newsItem.title || 'Untitled News';
        newsViewerMeta.textContent = buildMetaText(newsItem);
        newsViewerDescription.textContent = newsItem.description || 'No description available.';
        newsViewer.classList.remove('hidden');
    };

    newsViewerClose.addEventListener('click', () => {
        newsViewer.classList.add('hidden');
    });

    try {
        const response = await getNews();
        const newsItems = response.status === '200' && Array.isArray(response.data) ? response.data : [];

        if (newsItems.length === 0) {
            newsList.innerHTML = `<div class="w-full bg-white rounded-lg p-6 text-center font-bold text-gray-600">No news available.</div>`;
            return;
        }

        newsList.innerHTML = newsItems.map(newsItem => `
            <button
                type="button"
                class="image-card w-sm md:w-md rounded-lg bg-white p-2 m-2 hover:bg-gray-200 active:bg-blue-200 text-left"
                data-news-id="${newsItem.id}"
            >
                <img
                    src="${getImageUrl(newsItem.file_path)}"
                    alt="${newsItem.title || 'news image'}"
                    class="w-full min-h-[200px] h-[220px] object-cover rounded-sm bg-gray-200"
                />
                <p class="text-lg font-bold pt-2">${newsItem.title || 'Untitled News'}</p>
                <p class="text-sm text-gray-600 pb-2">${buildMetaText(newsItem)}</p>
                <p class="w-full min-h-[100px] h-full overflow-y-scroll" style="scrollbar-width: none;">
                    ${truncateText(newsItem.description, 180) || 'No description available.'}
                </p>
            </button>
        `).join('');

        newsList.addEventListener('click', (e) => {
            const card = e.target.closest('.image-card');
            if (!card) return;

            const selectedNews = newsItems.find(item => String(item.id) === String(card.getAttribute('data-news-id')));
            if (!selectedNews) return;

            openNewsViewer(selectedNews);
        });
    } catch (error) {
        console.error('Error loading news page:', error);
        newsList.innerHTML = `<div class="w-full bg-white rounded-lg p-6 text-center font-bold text-red-600">Failed to load news.</div>`;
    }
};

const initAdminSideNav = () => {
    const adminSideNav = document.getElementById('admin-side-nav');
    const adminNavToggle = document.getElementById('admin-nav-toggle');
    const adminSideNavLabel = document.getElementById('admin-side-nav-label');
    const adminSideNavMenu = document.getElementById('admin-side-nav-menu');

    if (!adminSideNav || !adminNavToggle || !adminSideNavLabel || !adminSideNavMenu) {
        return;
    }

    const syncAdminNavToggle = (isOpen) => {
        adminNavToggle.textContent = isOpen ? 'CLOSE' : 'MENU';
        adminNavToggle.classList.toggle('bg-red-600', isOpen);
        adminNavToggle.classList.toggle('hover:bg-red-700', isOpen);
        adminNavToggle.classList.toggle('bg-gray-950', !isOpen);
        adminNavToggle.classList.toggle('hover:bg-sky-700', !isOpen);
        adminSideNav.classList.toggle('-translate-x-[70%]', !isOpen);
        adminSideNav.classList.toggle('bg-transparent', !isOpen);
        adminSideNav.classList.toggle('shadow-none', !isOpen);
        adminSideNav.classList.toggle('bg-white/95', isOpen);
        adminSideNav.classList.toggle('shadow-2xl', isOpen);
        adminSideNavLabel.classList.toggle('opacity-0', !isOpen);
        adminSideNavMenu.classList.toggle('opacity-0', !isOpen);
        adminSideNavMenu.classList.toggle('pointer-events-none', !isOpen);
        adminSideNavMenu.classList.toggle('pointer-events-auto', isOpen);
    };

    const isAdminNavOpen = () => !adminSideNav.classList.contains('-translate-x-[70%]');

    const openAdminNav = () => {
        syncAdminNavToggle(true);
    };

    const closeAdminNav = () => {
        syncAdminNavToggle(false);
    };

    adminNavToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        if (isAdminNavOpen()) {
            closeAdminNav();
            return;
        }

        openAdminNav();
    });

    document.addEventListener('admin-nav-close', closeAdminNav);
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1280) {
            adminSideNav.classList.remove('-translate-x-[70%]', 'bg-white/20', 'shadow-none');
            adminSideNav.classList.add('bg-white/95', 'shadow-2xl');
            adminSideNavLabel.classList.remove('opacity-0');
            adminSideNavMenu.classList.remove('opacity-0', 'pointer-events-none');
            adminSideNavMenu.classList.add('pointer-events-auto');
            return;
        }

        if (!isAdminNavOpen()) {
            syncAdminNavToggle(false);
        }
    });

    syncAdminNavToggle(false);
};

const initAdminPanel = async () => {
    const adminPanel = document.getElementById('admin-panel');
    if (!adminPanel) return;

    const currentUser = getStoredUser();
    if (!currentUser || getCurrentUserRole() !== 'admin') {
        window.location.href = '../timetable.php';
        return;
    }

    const statsContainer = document.getElementById('admin-stats');
    const timetableSummary = document.getElementById('admin-timetable-summary');
    const settingsTableContainer = document.getElementById('admin-settings-table');
    const columnHeadingsContainer = document.getElementById('admin-column-headings');
    const timeSlotsContainer = document.getElementById('admin-time-slots');
    const requestsContainer = document.getElementById('admin-requests-list');
    const newsContainer = document.getElementById('admin-news-list');
    const yearsContainer = document.getElementById('admin-years-list');
    const groupsContainer = document.getElementById('admin-groups-list');
    const labsContainer = document.getElementById('admin-labs-list');
    const subjectsContainer = document.getElementById('admin-subjects-list');
    const usersContainer = document.getElementById('admin-users-list');
    const manageTimetableContainer = document.getElementById('admin-manage-timetable-list');
    const refreshButton = document.getElementById('admin-refresh-btn');
    const newsCreateButton = document.getElementById('admin-news-create-btn');
    const newsFormModal = document.getElementById('admin-news-form-modal');
    const newsForm = document.getElementById('admin-news-form');
    const newsFormCloseButton = document.getElementById('admin-news-form-close');
    const newsFormCancelButton = document.getElementById('admin-news-form-cancel');
    const newsFormTitle = document.getElementById('admin-news-form-title');
    const newsIdInput = document.getElementById('admin-news-id');
    const newsTitleInput = document.getElementById('admin-news-title');
    const newsDescriptionInput = document.getElementById('admin-news-description');
    const newsStartDateInput = document.getElementById('admin-news-start-date');
    const newsEndDateInput = document.getElementById('admin-news-end-date');
    const newsStartAtInput = document.getElementById('admin-news-start-at');
    const newsEndAtInput = document.getElementById('admin-news-end-at');
    const requestConfirmModal = document.getElementById('admin-request-confirm-modal');
    const requestConfirmForm = document.getElementById('admin-request-confirm-form');
    const requestConfirmCloseButton = document.getElementById('admin-request-confirm-close');
    const requestConfirmCancelButton = document.getElementById('admin-request-confirm-cancel');
    const requestConfirmIdInput = document.getElementById('admin-request-confirm-id');
    const requestConfirmLecturerIdInput = document.getElementById('admin-request-confirm-lecturer-id');
    const requestConfirmSubjectIdInput = document.getElementById('admin-request-confirm-subject-id');
    const requestConfirmYearIdInput = document.getElementById('admin-request-confirm-year-id');
    const requestConfirmTimeSlotIdInput = document.getElementById('admin-request-confirm-time-slot-id');
    const requestConfirmColumnIdInput = document.getElementById('admin-request-confirm-column-id');
    const requestConfirmGroupIdInput = document.getElementById('admin-request-confirm-group-id');
    const requestConfirmTimeSlotInput = document.getElementById('admin-request-confirm-time-slot');
    const requestConfirmDayInput = document.getElementById('admin-request-confirm-day');
    const requestConfirmLecturerNameInput = document.getElementById('admin-request-confirm-lecturer-name');
    const requestConfirmYearInput = document.getElementById('admin-request-confirm-year');
    const requestConfirmSubjectInput = document.getElementById('admin-request-confirm-subject');
    const requestConfirmGroupInput = document.getElementById('admin-request-confirm-group');
    const requestConfirmLabSelect = document.getElementById('admin-request-confirm-lab');
    const requestConfirmDateInput = document.getElementById('admin-request-confirm-date');
    const requestConfirmDescriptionInput = document.getElementById('admin-request-confirm-description');
    const requestCheckAvailabilityButton = document.getElementById('admin-request-check-availability');
    const requestCheckResult = document.getElementById('admin-request-check-result');
    const yearCreateButton = document.getElementById('admin-year-create-btn');
    const yearFormModal = document.getElementById('admin-year-form-modal');
    const yearForm = document.getElementById('admin-year-form');
    const yearFormCloseButton = document.getElementById('admin-year-form-close');
    const yearFormCancelButton = document.getElementById('admin-year-form-cancel');
    const yearFormTitle = document.getElementById('admin-year-form-title');
    const yearIdInput = document.getElementById('admin-year-id');
    const yearNameInput = document.getElementById('admin-year-name');
    const groupCreateButton = document.getElementById('admin-group-create-btn');
    const groupFormModal = document.getElementById('admin-group-form-modal');
    const groupForm = document.getElementById('admin-group-form');
    const groupFormCloseButton = document.getElementById('admin-group-form-close');
    const groupFormCancelButton = document.getElementById('admin-group-form-cancel');
    const groupFormTitle = document.getElementById('admin-group-form-title');
    const groupIdInput = document.getElementById('admin-group-id');
    const groupNameInput = document.getElementById('admin-group-name');
    const labCreateButton = document.getElementById('admin-lab-create-btn');
    const labFormModal = document.getElementById('admin-lab-form-modal');
    const labForm = document.getElementById('admin-lab-form');
    const labFormCloseButton = document.getElementById('admin-lab-form-close');
    const labFormCancelButton = document.getElementById('admin-lab-form-cancel');
    const labFormTitle = document.getElementById('admin-lab-form-title');
    const labIdInput = document.getElementById('admin-lab-id');
    const labNameInput = document.getElementById('admin-lab-name');
    const labLocationInput = document.getElementById('admin-lab-location');
    const userCreateButton = document.getElementById('admin-user-create-btn');
    const userFormModal = document.getElementById('admin-user-form-modal');
    const userForm = document.getElementById('admin-user-form');
    const userFormCloseButton = document.getElementById('admin-user-form-close');
    const userFormCancelButton = document.getElementById('admin-user-form-cancel');
    const userFormTitle = document.getElementById('admin-user-form-title');
    const userIdInput = document.getElementById('admin-user-id');
    const userInitialsInput = document.getElementById('admin-user-initials');
    const userInitialsStandForInput = document.getElementById('admin-user-initials-stand-for');
    const userFirstNameInput = document.getElementById('admin-user-first-name');
    const userLastNameInput = document.getElementById('admin-user-last-name');
    const userHonorificsSelect = document.getElementById('admin-user-honorifics');
    const userRoleSelect = document.getElementById('admin-user-role');
    const userNicInput = document.getElementById('admin-user-nic');
    const userEmailInput = document.getElementById('admin-user-email');
    const userMobileInput = document.getElementById('admin-user-mobile');
    const userPasswordFields = document.getElementById('admin-user-password-fields');
    const userPasswordInput = document.getElementById('admin-user-password');
    const userConfirmPasswordInput = document.getElementById('admin-user-confirm-password');
    const subjectCreateButton = document.getElementById('admin-subject-create-btn');
    const subjectFormModal = document.getElementById('admin-subject-form-modal');
    const subjectForm = document.getElementById('admin-subject-form');
    const subjectFormCloseButton = document.getElementById('admin-subject-form-close');
    const subjectFormCancelButton = document.getElementById('admin-subject-form-cancel');
    const subjectFormTitle = document.getElementById('admin-subject-form-title');
    const subjectIdInput = document.getElementById('admin-subject-id');
    const subjectCodeInput = document.getElementById('admin-subject-code');
    const subjectNameInput = document.getElementById('admin-subject-name');
    const subjectYearSelect = document.getElementById('admin-subject-year');
    const settingsFormModal = document.getElementById('admin-settings-form-modal');
    const settingsForm = document.getElementById('admin-settings-form');
    const settingsFormCloseButton = document.getElementById('admin-settings-form-close');
    const settingsIdInput = document.getElementById('admin-settings-id');
    const settingsRowsInput = document.getElementById('admin-settings-rows');
    const settingsColumnsInput = document.getElementById('admin-settings-columns');
    const settingsBreakRowInput = document.getElementById('admin-settings-break-row');
    const settingsFormCancelButton = document.getElementById('admin-settings-form-cancel');
    const columnHeadingCreateButton = document.getElementById('admin-column-heading-create-btn');
    const columnHeadingFormModal = document.getElementById('admin-column-heading-form-modal');
    const columnHeadingForm = document.getElementById('admin-column-heading-form');
    const columnHeadingFormCloseButton = document.getElementById('admin-column-heading-form-close');
    const columnHeadingFormTitle = document.getElementById('admin-column-heading-form-title');
    const columnHeadingIdInput = document.getElementById('admin-column-heading-id');
    const columnHeadingNameInput = document.getElementById('admin-column-heading-name');
    const columnHeadingNumberInput = document.getElementById('admin-column-heading-number');
    const columnHeadingFormCancelButton = document.getElementById('admin-column-heading-form-cancel');
    const timeSlotCreateButton = document.getElementById('admin-time-slot-create-btn');
    const timeSlotFormModal = document.getElementById('admin-time-slot-form-modal');
    const timeSlotForm = document.getElementById('admin-time-slot-form');
    const timeSlotFormCloseButton = document.getElementById('admin-time-slot-form-close');
    const timeSlotFormTitle = document.getElementById('admin-time-slot-form-title');
    const timeSlotIdInput = document.getElementById('admin-time-slot-id');
    const timeSlotStartInput = document.getElementById('admin-time-slot-start');
    const timeSlotEndInput = document.getElementById('admin-time-slot-end');
    const timeSlotFormCancelButton = document.getElementById('admin-time-slot-form-cancel');
    const timetableFormModal = document.getElementById('admin-timetable-form-modal');
    const timetableForm = document.getElementById('admin-timetable-form');
    const timetableCreateButton = document.getElementById('admin-timetable-create-btn');
    const timetableFormCancelButton = document.getElementById('admin-timetable-form-cancel');
    const timetableFormCloseButton = document.getElementById('admin-timetable-form-close');
    const timetableFormTitle = document.getElementById('admin-timetable-form-title');
    const timetableIdInput = document.getElementById('admin-timetable-id');
    const timetableCellIdInput = document.getElementById('admin-timetable-cell-id');
    const timetableSubjectSelect = document.getElementById('admin-timetable-subject');
    const timetableGroupSelect = document.getElementById('admin-timetable-group');
    const timetableLabSelect = document.getElementById('admin-timetable-lab');
    const timetableActionSelect = document.getElementById('admin-timetable-action');
    const timetableDaySelect = document.getElementById('admin-timetable-day');
    const timetableTimeSlotSelect = document.getElementById('admin-timetable-time-slot');
    const adminNavButtons = Array.from(document.querySelectorAll('.admin-nav-btn'));
    const adminSections = Array.from(document.querySelectorAll('[data-admin-section]'));

    if (!statsContainer || !timetableSummary || !settingsTableContainer || !columnHeadingsContainer || !timeSlotsContainer || !requestsContainer || !newsContainer || !yearsContainer || !groupsContainer || !labsContainer || !subjectsContainer || !usersContainer || !manageTimetableContainer || !refreshButton || !newsCreateButton || !newsFormModal || !newsForm || !newsFormCloseButton || !newsFormCancelButton || !newsFormTitle || !newsIdInput || !newsTitleInput || !newsDescriptionInput || !newsStartDateInput || !newsEndDateInput || !newsStartAtInput || !newsEndAtInput || !requestConfirmModal || !requestConfirmForm || !requestConfirmCloseButton || !requestConfirmCancelButton || !requestConfirmIdInput || !requestConfirmLecturerIdInput || !requestConfirmSubjectIdInput || !requestConfirmYearIdInput || !requestConfirmTimeSlotIdInput || !requestConfirmColumnIdInput || !requestConfirmGroupIdInput || !requestConfirmTimeSlotInput || !requestConfirmDayInput || !requestConfirmLecturerNameInput || !requestConfirmYearInput || !requestConfirmSubjectInput || !requestConfirmGroupInput || !requestConfirmLabSelect || !requestConfirmDateInput || !requestConfirmDescriptionInput || !requestCheckAvailabilityButton || !requestCheckResult || !yearCreateButton || !yearFormModal || !yearForm || !yearFormCloseButton || !yearFormCancelButton || !yearFormTitle || !yearIdInput || !yearNameInput || !groupCreateButton || !groupFormModal || !groupForm || !groupFormCloseButton || !groupFormCancelButton || !groupFormTitle || !groupIdInput || !groupNameInput || !labCreateButton || !labFormModal || !labForm || !labFormCloseButton || !labFormCancelButton || !labFormTitle || !labIdInput || !labNameInput || !labLocationInput || !userCreateButton || !userFormModal || !userForm || !userFormCloseButton || !userFormCancelButton || !userFormTitle || !userIdInput || !userInitialsInput || !userInitialsStandForInput || !userFirstNameInput || !userLastNameInput || !userHonorificsSelect || !userRoleSelect || !userNicInput || !userEmailInput || !userMobileInput || !userPasswordFields || !userPasswordInput || !userConfirmPasswordInput || !subjectCreateButton || !subjectFormModal || !subjectForm || !subjectFormCloseButton || !subjectFormCancelButton || !subjectFormTitle || !subjectIdInput || !subjectCodeInput || !subjectNameInput || !subjectYearSelect || !settingsFormModal || !settingsForm || !settingsFormCloseButton || !settingsIdInput || !settingsRowsInput || !settingsColumnsInput || !settingsBreakRowInput || !settingsFormCancelButton || !columnHeadingCreateButton || !columnHeadingFormModal || !columnHeadingForm || !columnHeadingFormCloseButton || !columnHeadingFormTitle || !columnHeadingIdInput || !columnHeadingNameInput || !columnHeadingNumberInput || !columnHeadingFormCancelButton || !timeSlotCreateButton || !timeSlotFormModal || !timeSlotForm || !timeSlotFormCloseButton || !timeSlotFormTitle || !timeSlotIdInput || !timeSlotStartInput || !timeSlotEndInput || !timeSlotFormCancelButton || !timetableFormModal || !timetableForm || !timetableCreateButton || !timetableFormCancelButton || !timetableFormCloseButton || !timetableFormTitle || !timetableIdInput || !timetableCellIdInput || !timetableSubjectSelect || !timetableGroupSelect || !timetableLabSelect || !timetableActionSelect || !timetableDaySelect || !timetableTimeSlotSelect || !adminNavButtons.length || !adminSections.length) {
        return;
    }

    const adminState = {
        timetableSettings: null,
        years: [],
        timeSlots: [],
        columnHeadings: [],
        timetableCells: [],
        lectureGroups: [],
        labs: [],
        timetableRecords: [],
        lecturerRequests: [],
        newsItems: [],
        subjects: [],
        users: [],
    };

    const escapeHtml = (value) => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');

    const buildCard = (label, value, themeClass = 'bg-white text-gray-950') => `
        <article class="${themeClass} rounded-2xl p-4 shadow-md">
            <p class="text-xs uppercase tracking-[0.25em] font-black opacity-70">${escapeHtml(label)}</p>
            <p class="text-3xl font-black pt-2">${escapeHtml(value)}</p>
        </article>
    `;

    const formatNewsMeta = (newsItem) => {
        const parts = [];
        if (newsItem.start_date) parts.push(newsItem.start_date);
        if (newsItem.end_date) parts.push(newsItem.end_date);
        if (newsItem.start_at) parts.push(newsItem.start_at);
        if (newsItem.end_at) parts.push(newsItem.end_at);
        return parts.join(' | ') || 'No schedule set';
    };

    const formatTimeSlotRange = (item) => formatTimeSlotLabel(item.start_time, item.end_time);
    const getRequestStatusClass = (status) => {
        const normalized = String(status || '').toLowerCase();
        if (normalized === 'confirmed') return 'bg-green-100 text-green-700';
        if (normalized === 'canceled') return 'bg-red-100 text-red-700';
        return 'bg-amber-100 text-amber-700';
    };

    const getTimetableStatusClass = (status) => {
        const normalized = String(status || '').toLowerCase();
        if (normalized === 'active') return 'bg-purple-100 text-purple-800';
        if (normalized === 'cancel') return 'bg-red-100 text-red-700';
        return 'bg-green-100 text-green-700';
    };

    const getOpenAdminModalCount = () => ([
        newsFormModal,
        requestConfirmModal,
        yearFormModal,
        groupFormModal,
        labFormModal,
        userFormModal,
        subjectFormModal,
        settingsFormModal,
        columnHeadingFormModal,
        timeSlotFormModal,
        timetableFormModal,
    ].filter(modal => modal && !modal.classList.contains('hidden')).length);

    const lockPageScroll = () => {
        document.body.classList.add('overflow-hidden');
    };

    const unlockPageScrollIfSafe = () => {
        if (getOpenAdminModalCount() === 0) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    const showAdminModal = (modalElement) => {
        if (!modalElement) return;
        modalElement.classList.remove('hidden');
        lockPageScroll();
        modalElement.scrollTop = 0;
    };

    const hideAdminModal = (modalElement) => {
        if (!modalElement) return;
        modalElement.classList.add('hidden');
        unlockPageScrollIfSafe();
    };

    const showAdminSection = (targetSectionId) => {
        adminSections.forEach(section => {
            section.classList.toggle('hidden', section.id !== targetSectionId);
        });

        adminNavButtons.forEach(button => {
            const isActive = button.getAttribute('data-admin-target') === targetSectionId;
            button.classList.toggle('bg-gray-950', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('text-gray-900', !isActive);
            button.classList.toggle('hover:bg-sky-700', isActive);
            button.classList.toggle('bg-gray-100', !isActive);
            button.classList.toggle('hover:bg-sky-100', !isActive);
        });
    };

    adminNavButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetSectionId = button.getAttribute('data-admin-target') || 'admin-overview-section';
            showAdminSection(targetSectionId);
            if (window.innerWidth < 1280) document.dispatchEvent(new CustomEvent('admin-nav-close'));
        });
    });

    showAdminSection('admin-overview-section');

    const getAuditValue = () => currentUser.email || String(currentUser.id || '');

    const resetSettingsForm = () => {
        settingsForm.reset();
        settingsIdInput.value = '';
    };

    const openSettingsForm = (settings) => {
        if (!settings) return;
        settingsIdInput.value = settings.id || '';
        settingsRowsInput.value = settings.table_row_count || 0;
        settingsColumnsInput.value = settings.table_column_count || 0;
        settingsBreakRowInput.value = settings.break_row_number || 0;
        settingsBreakRowInput.max = settings.table_row_count || 0;
        showAdminModal(settingsFormModal);
    };

    const hideSettingsForm = () => {
        hideAdminModal(settingsFormModal);
        resetSettingsForm();
    };

    const resetColumnHeadingForm = () => {
        columnHeadingForm.reset();
        columnHeadingIdInput.value = '';
    };

    const openColumnHeadingForm = (record = null) => {
        resetColumnHeadingForm();
        columnHeadingNumberInput.max = adminState.timetableSettings?.table_column_count || 0;
        if (record) {
            columnHeadingFormTitle.textContent = 'Update Column Heading';
            columnHeadingIdInput.value = record.id || '';
            columnHeadingNameInput.value = record.column_heading || '';
            columnHeadingNumberInput.value = record.column_number || '';
        } else {
            columnHeadingFormTitle.textContent = 'Add Column Heading';
        }
        showAdminModal(columnHeadingFormModal);
    };

    const hideColumnHeadingForm = () => {
        hideAdminModal(columnHeadingFormModal);
        resetColumnHeadingForm();
    };

    const resetTimeSlotForm = () => {
        timeSlotForm.reset();
        timeSlotIdInput.value = '';
    };

    const openTimeSlotForm = (record = null) => {
        resetTimeSlotForm();
        if (record) {
            timeSlotFormTitle.textContent = 'Update Time Slot';
            timeSlotIdInput.value = record.id || '';
            timeSlotStartInput.value = record.start_time || '';
            timeSlotEndInput.value = record.end_time || '';
        } else {
            timeSlotFormTitle.textContent = 'Add Time Slot';
        }
        showAdminModal(timeSlotFormModal);
    };

    const hideTimeSlotForm = () => {
        hideAdminModal(timeSlotFormModal);
        resetTimeSlotForm();
    };

    const resetNewsForm = () => {
        newsForm.reset();
        newsIdInput.value = '';
    };

    const openNewsForm = (record = null) => {
        resetNewsForm();
        if (record) {
            newsFormTitle.textContent = 'Update News';
            newsIdInput.value = record.id || '';
            newsTitleInput.value = record.title || '';
            newsDescriptionInput.value = record.description || '';
            newsStartDateInput.value = record.start_date || '';
            newsEndDateInput.value = record.end_date || '';
            newsStartAtInput.value = record.start_at || '';
            newsEndAtInput.value = record.end_at || '';
        } else {
            newsFormTitle.textContent = 'New News';
        }
        showAdminModal(newsFormModal);
    };

    const hideNewsForm = () => {
        hideAdminModal(newsFormModal);
        resetNewsForm();
    };

    const populateRequestConfirmLabOptions = () => {
        requestConfirmLabSelect.innerHTML = `<option value="">Select lab</option>`;
        adminState.labs.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id || '';
            option.textContent = `${item.lab_name || ''}${item.lab_location ? ` - ${item.lab_location}` : ''}`;
            requestConfirmLabSelect.appendChild(option);
        });
    };

    const resetRequestConfirmForm = () => {
        requestConfirmForm.reset();
        requestConfirmIdInput.value = '';
        requestConfirmLecturerIdInput.value = '';
        requestConfirmSubjectIdInput.value = '';
        requestConfirmYearIdInput.value = '';
        requestConfirmTimeSlotIdInput.value = '';
        requestConfirmColumnIdInput.value = '';
        requestConfirmGroupIdInput.value = '';
        requestConfirmLabSelect.value = '';
        requestCheckResult.textContent = '';
        requestCheckResult.className = 'text-sm font-bold text-gray-600 text-right';
    };

    const openRequestConfirmForm = (record) => {
        populateRequestConfirmLabOptions();
        resetRequestConfirmForm();
        requestConfirmIdInput.value = record.id || '';
        requestConfirmLecturerIdInput.value = record.lecturer_id || '';
        requestConfirmSubjectIdInput.value = record.subject_id || '';
        requestConfirmYearIdInput.value = record.year_id || '';
        requestConfirmTimeSlotIdInput.value = record.timetable_time_slot_id || '';
        requestConfirmColumnIdInput.value = record.timetable_column_heading_id || '';
        requestConfirmGroupIdInput.value = record.group_id || '';
        requestConfirmTimeSlotInput.value = record.start_time && record.end_time ? formatTimeSlotRange(record) : '';
        requestConfirmDayInput.value = record.column_heading || '';
        requestConfirmLecturerNameInput.value = record.lecturer_name || '';
        requestConfirmYearInput.value = record.year || '';
        requestConfirmSubjectInput.value = record.subject || record.subject_id || '';
        requestConfirmGroupInput.value = record.group_name || '';
        requestConfirmDateInput.value = record.date || '';
        requestConfirmDescriptionInput.value = record.lecturer_request || '';
        showAdminModal(requestConfirmModal);
    };

    const hideRequestConfirmForm = () => {
        hideAdminModal(requestConfirmModal);
        resetRequestConfirmForm();
    };

    const resetYearForm = () => {
        yearForm.reset();
        yearIdInput.value = '';
    };

    const openYearForm = (record = null) => {
        resetYearForm();
        if (record) {
            yearFormTitle.textContent = 'Update Year';
            yearIdInput.value = record.id || '';
            yearNameInput.value = record.year || '';
        } else {
            yearFormTitle.textContent = 'New Year';
        }
        showAdminModal(yearFormModal);
    };

    const hideYearForm = () => {
        hideAdminModal(yearFormModal);
        resetYearForm();
    };

    const resetGroupForm = () => {
        groupForm.reset();
        groupIdInput.value = '';
    };

    const openGroupForm = (record = null) => {
        resetGroupForm();
        if (record) {
            groupFormTitle.textContent = 'Update Group';
            groupIdInput.value = record.id || '';
            groupNameInput.value = record.group_name || '';
        } else {
            groupFormTitle.textContent = 'New Group';
        }
        showAdminModal(groupFormModal);
    };

    const hideGroupForm = () => {
        hideAdminModal(groupFormModal);
        resetGroupForm();
    };

    const resetLabForm = () => {
        labForm.reset();
        labIdInput.value = '';
    };

    const openLabForm = (record = null) => {
        resetLabForm();
        if (record) {
            labFormTitle.textContent = 'Update Lab';
            labIdInput.value = record.id || '';
            labNameInput.value = record.lab_name || '';
            labLocationInput.value = record.lab_location || '';
        } else {
            labFormTitle.textContent = 'New Lab';
        }
        showAdminModal(labFormModal);
    };

    const hideLabForm = () => {
        hideAdminModal(labFormModal);
        resetLabForm();
    };

    const populateSubjectYearOptions = () => {
        setSelectOptions(
            subjectYearSelect,
            adminState.years,
            'Select year',
            (item) => item.id,
            (item) => item.year || ''
        );
    };

    const resetSubjectForm = () => {
        subjectForm.reset();
        subjectIdInput.value = '';
    };

    const openSubjectForm = (record = null) => {
        populateSubjectYearOptions();
        resetSubjectForm();
        if (record) {
            subjectFormTitle.textContent = 'Update Subject';
            subjectIdInput.value = record.subject_id || record.id || '';
            subjectCodeInput.value = record.subject_cord || '';
            subjectNameInput.value = record.subject || '';
            subjectYearSelect.value = record.year_id || '';
        } else {
            subjectFormTitle.textContent = 'New Subject';
        }
        showAdminModal(subjectFormModal);
    };

    const hideSubjectForm = () => {
        hideAdminModal(subjectFormModal);
        resetSubjectForm();
    };

    const resetUserForm = () => {
        userForm.reset();
        userIdInput.value = '';
        userRoleSelect.value = '';
        userPasswordInput.value = '';
        userConfirmPasswordInput.value = '';
        userPasswordFields.classList.remove('hidden');
        userPasswordInput.required = true;
        userConfirmPasswordInput.required = true;
    };

    const openUserForm = (record = null) => {
        resetUserForm();
        if (record) {
            userFormTitle.textContent = 'Update User';
            userIdInput.value = record.id || '';
            userInitialsInput.value = record.initials || '';
            userInitialsStandForInput.value = record.initials_stand_for || '';
            userFirstNameInput.value = record.first_name || '';
            userLastNameInput.value = record.last_name || '';
            userHonorificsSelect.value = record.honorifics || '';
            userRoleSelect.value = record.role || '';
            userNicInput.value = record.nic || '';
            userEmailInput.value = record.email || '';
            userMobileInput.value = record.mobile_number || '';
            userPasswordFields.classList.add('hidden');
            userPasswordInput.required = false;
            userConfirmPasswordInput.required = false;
        } else {
            userFormTitle.textContent = 'New User';
        }
        showAdminModal(userFormModal);
    };

    const hideUserForm = () => {
        hideAdminModal(userFormModal);
        resetUserForm();
    };

    const setSelectOptions = (selectElement, items, defaultLabel, getValue, getLabel) => {
        selectElement.innerHTML = `<option value="">${defaultLabel}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = getValue(item);
            option.textContent = getLabel(item);
            selectElement.appendChild(option);
        });
    };

    const populateAdminTimetableFormOptions = () => {
        setSelectOptions(
            timetableSubjectSelect,
            adminState.subjects,
            'Select subject code',
            (item) => item.subject_cord || '',
            (item) => `${item.subject_cord || ''}${item.subject ? ` - ${item.subject}` : ''}`
        );
        setSelectOptions(
            timetableGroupSelect,
            adminState.lectureGroups,
            'Select group',
            (item) => item.id,
            (item) => item.group_name || ''
        );
        setSelectOptions(
            timetableLabSelect,
            adminState.labs,
            'Select lab',
            (item) => item.id,
            (item) => `${item.lab_name || ''}${item.lab_location ? ` - ${item.lab_location}` : ''}`
        );
        setSelectOptions(
            timetableDaySelect,
            adminState.columnHeadings,
            'Select day',
            (item) => item.id,
            (item) => item.column_heading || ''
        );
        setSelectOptions(
            timetableTimeSlotSelect,
            getActiveTimeSlots(),
            'Select time slot',
            (item) => item.id,
            (item) => formatTimeSlotLabel(item.start_time, item.end_time)
        );
    };

    const getCellMetaByCellNumber = (cellNumber) => {
        const targetCellNumber = Number(cellNumber);
        const cellGrid = getCellNumberGrid();
        const activeTimeSlots = getActiveTimeSlots();

        for (let rowIndex = 0; rowIndex < cellGrid.length; rowIndex++) {
            const columnIndex = cellGrid[rowIndex]?.indexOf(targetCellNumber);
            if (columnIndex !== -1 && columnIndex !== undefined) {
                const matchedTimeSlot = activeTimeSlots[rowIndex];
                const matchedDay = adminState.columnHeadings[columnIndex];
                const matchedCell = adminState.timetableCells.find(item => Number(item.cell_number) === targetCellNumber);

                return {
                    dayId: matchedDay?.id || '',
                    dayLabel: matchedDay?.column_heading || '',
                    timeSlotId: matchedTimeSlot?.id || '',
                    timeSlotLabel: matchedTimeSlot ? formatTimeSlotLabel(matchedTimeSlot.start_time, matchedTimeSlot.end_time) : '',
                    timetableCellReferenceId: matchedCell?.id || '',
                };
            }
        }

        return {
            dayId: '',
            dayLabel: '',
            timeSlotId: '',
            timeSlotLabel: '',
            timetableCellReferenceId: '',
        };
    };

    const getTimetableCellReferenceId = (dayId, timeSlotId) => {
        const dayIndex = adminState.columnHeadings.findIndex(item => String(item.id) === String(dayId));
        const timeSlotIndex = getActiveTimeSlots().findIndex(item => String(item.id) === String(timeSlotId));
        const cellNumber = (getCellNumberGrid()[timeSlotIndex] || [])[dayIndex];
        const matchedCell = adminState.timetableCells.find(item => Number(item.cell_number) === Number(cellNumber));
        return matchedCell?.id || '';
    };

    const resetTimetableForm = () => {
        timetableForm.reset();
        timetableIdInput.value = '';
        timetableCellIdInput.value = '';
        timetableActionSelect.value = 'free';
    };

    const hideTimetableForm = () => {
        hideAdminModal(timetableFormModal);
        resetTimetableForm();
    };

    const openTimetableForm = (record = null) => {
        populateAdminTimetableFormOptions();
        resetTimetableForm();

        if (record) {
            timetableFormTitle.textContent = 'Update Timetable Record';
            const cellMeta = getCellMetaByCellNumber(record.cell_id);
            timetableIdInput.value = record.timetable_id || '';
            timetableCellIdInput.value = record.timetable_cell_reference_id || cellMeta.timetableCellReferenceId || '';
            timetableSubjectSelect.value = record.subject_cord || '';
            timetableGroupSelect.value = record.lecture_group_id || '';
            timetableLabSelect.value = record.lab_id || '';
            timetableActionSelect.value = record.action || 'free';
            timetableDaySelect.value = cellMeta.dayId || '';
            timetableTimeSlotSelect.value = cellMeta.timeSlotId || '';
        } else {
            timetableFormTitle.textContent = 'New Timetable Record';
        }

        showAdminModal(timetableFormModal);
    };

    const renderAdminPanel = () => {
        const {
            timetableSettings,
            timeSlots,
            columnHeadings,
            timetableRecords,
            lecturerRequests,
            newsItems,
            years,
            lectureGroups,
            labs,
            subjects,
            users,
        } = adminState;

        statsContainer.innerHTML = [
            buildCard('Lecturer Requests', lecturerRequests.length, 'bg-white/95 text-gray-950'),
            buildCard('News Posts', newsItems.length, 'bg-sky-200 text-gray-950'),
            buildCard('Subjects', subjects.length, 'bg-amber-100 text-gray-950'),
            buildCard('Users', users.length, 'bg-emerald-100 text-gray-950'),
        ].join('');

        timetableSummary.innerHTML = timetableSettings ? [
            buildCard('Columns', timetableSettings.table_column_count, 'bg-gray-100 text-gray-950'),
            buildCard('Rows', timetableSettings.table_row_count, 'bg-gray-100 text-gray-950'),
            buildCard('Cells', timetableSettings.table_cell_count, 'bg-gray-100 text-gray-950'),
            buildCard('Break Row', timetableSettings.break_row_number, 'bg-gray-100 text-gray-950'),
        ].join('') : `<div class="md:col-span-4 bg-red-50 text-red-700 rounded-xl p-4 font-bold">No timetable settings found.</div>`;

        settingsTableContainer.innerHTML = timetableSettings ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Rows</th>
                        <th class="px-4 py-3">Columns</th>
                        <th class="px-4 py-3">Cells</th>
                        <th class="px-4 py-3">Break Row</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-3 font-bold">${escapeHtml(timetableSettings.table_row_count)}</td>
                        <td class="px-4 py-3">${escapeHtml(timetableSettings.table_column_count)}</td>
                        <td class="px-4 py-3">${escapeHtml(timetableSettings.table_cell_count)}</td>
                        <td class="px-4 py-3">${escapeHtml(timetableSettings.break_row_number)}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="admin-settings-update-btn" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                <button type="button" id="admin-settings-reset-btn" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Reset</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No timetable settings found.</div>`;

        columnHeadingsContainer.innerHTML = columnHeadings.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Column Name</th>
                        <th class="px-4 py-3">Column Number</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${columnHeadings.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.column_heading)}</td>
                            <td class="px-4 py-3">${escapeHtml(item.column_number)}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-column-heading-action="update" data-column-heading-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-column-heading-action="delete" data-column-heading-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No column headings available.</div>`;

        timeSlotsContainer.innerHTML = timeSlots.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Start Time</th>
                        <th class="px-4 py-3">End Time</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${timeSlots.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.start_time)}</td>
                            <td class="px-4 py-3">${escapeHtml(item.end_time)}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-time-slot-action="update" data-time-slot-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-time-slot-action="delete" data-time-slot-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No time slots available.</div>`;

        manageTimetableContainer.innerHTML = timetableRecords.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Subject Name</th>
                        <th class="px-4 py-3">Subject Code</th>
                        <th class="px-4 py-3">Lecture In Charge</th>
                        <th class="px-4 py-3">Lecture</th>
                        <th class="px-4 py-3">Group</th>
                        <th class="px-4 py-3">Lab</th>
                        <th class="px-4 py-3">Day</th>
                        <th class="px-4 py-3">Time Slot</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${timetableRecords.map(item => {
                        const cellMeta = getCellMetaByCellNumber(item.cell_id);
                        return `
                            <tr class="border-b border-gray-200 align-top">
                                <td class="px-4 py-3 font-bold">${escapeHtml(item.subject || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(item.subject_cord || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(item.lecturer_name || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(item.year || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(item.group_name || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(item.lab || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(cellMeta.dayLabel || '-')}</td>
                                <td class="px-4 py-3">${escapeHtml(cellMeta.timeSlotLabel || '-')}</td>
                                <td class="px-4 py-3"><span class="px-3 py-1 rounded-full text-xs font-black ${getTimetableStatusClass(item.action)}">${escapeHtml(item.action || 'free')}</span></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" data-timetable-action="update" data-timetable-id="${escapeHtml(item.timetable_id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                        <button type="button" data-timetable-action="delete" data-timetable-id="${escapeHtml(item.timetable_id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No timetable records found.</div>`;

        requestsContainer.innerHTML = lecturerRequests.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Lecturer</th>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Day</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Request</th>
                        <th class="px-4 py-3">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    ${lecturerRequests.map(item => `
                        <tr class="border-b border-gray-200 align-top">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.lecturer_name || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(item.subject || item.subject_id || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(item.date || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(item.column_heading || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(item.start_time && item.end_time ? formatTimeSlotRange(item) : '-')}</td>
                            <td class="px-4 py-3"><span class="px-3 py-1 rounded-full text-xs font-black ${getRequestStatusClass(item.action)}">${escapeHtml(item.action || 'requested')}</span></td>
                            <td class="px-4 py-3 max-w-[280px]">${escapeHtml(item.lecturer_request || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-request-action="confirmed" data-request-id="${escapeHtml(item.id)}" class="bg-green-600 text-white px-3 py-2 rounded-lg font-black hover:bg-green-700">Confirm</button>
                                    <button type="button" data-request-action="canceled" data-request-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Cancel</button>
                                    <button type="button" data-request-action="delete" data-request-id="${escapeHtml(item.id)}" class="bg-gray-900 text-white px-3 py-2 rounded-lg font-black hover:bg-gray-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No lecturer requests found.</div>`;

        newsContainer.innerHTML = newsItems.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Schedule</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${newsItems.map(item => `
                        <tr class="border-b border-gray-200 align-top">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.title || 'Untitled News')}</td>
                            <td class="px-4 py-3">${escapeHtml(formatNewsMeta(item))}</td>
                            <td class="px-4 py-3 max-w-[320px]">${escapeHtml(String(item.description || '').slice(0, 180) || 'No description.')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-news-action="update" data-news-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-news-action="delete" data-news-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No news available.</div>`;

        yearsContainer.innerHTML = years.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Year</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${years.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.year || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-year-action="update" data-year-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-year-action="delete" data-year-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No years available.</div>`;

        groupsContainer.innerHTML = lectureGroups.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Group Name</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${lectureGroups.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.group_name || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-group-action="update" data-group-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-group-action="delete" data-group-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No groups available.</div>`;

        labsContainer.innerHTML = labs.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Lab Name</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${labs.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.lab_name || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(item.lab_location || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-lab-action="update" data-lab-id="${escapeHtml(item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-lab-action="delete" data-lab-id="${escapeHtml(item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No labs available.</div>`;

        subjectsContainer.innerHTML = subjects.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Subject Code</th>
                        <th class="px-4 py-3">Subject Name</th>
                        <th class="px-4 py-3">Year</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${subjects.map(item => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml(item.subject_cord)}</td>
                            <td class="px-4 py-3">${escapeHtml(item.subject)}</td>
                            <td class="px-4 py-3">${escapeHtml(item.year || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-subject-action="update" data-subject-id="${escapeHtml(item.subject_id || item.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-subject-action="delete" data-subject-id="${escapeHtml(item.subject_id || item.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No subjects available.</div>`;

        usersContainer.innerHTML = users.length ? `
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 uppercase text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Mobile</th>
                        <th class="px-4 py-3">NIC</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 font-bold">${escapeHtml([user.first_name, user.last_name].filter(Boolean).join(' ') || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(user.role || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(user.email || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(user.mobile_number || '-')}</td>
                            <td class="px-4 py-3">${escapeHtml(user.nic || '-')}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-user-action="update" data-user-id="${escapeHtml(user.id)}" class="bg-sky-600 text-white px-3 py-2 rounded-lg font-black hover:bg-sky-700">Update</button>
                                    <button type="button" data-user-action="reset-password" data-user-id="${escapeHtml(user.id)}" class="bg-amber-500 text-white px-3 py-2 rounded-lg font-black hover:bg-amber-600">Reset Password</button>
                                    <button type="button" data-user-action="delete" data-user-id="${escapeHtml(user.id)}" class="bg-red-600 text-white px-3 py-2 rounded-lg font-black hover:bg-red-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        ` : `<div class="bg-gray-100 rounded-xl px-4 py-6 text-gray-500 font-bold text-center">No users available.</div>`;
    };

    const loadAdminData = async () => {
        const [
            settingsResponse,
            timeSlotsResponse,
            columnHeadingsResponse,
            lecturerRequestsResponse,
            newsResponse,
            subjectsResponse,
            yearsResponse,
            usersResponse,
            timetableResponse,
            lectureGroupsResponse,
            labsResponse,
            timetableCellsResponse,
        ] = await Promise.all([
            getTimetableSettings(),
            getTimeSlots(),
            getColumnHeadings(),
            getLecturerRequests(),
            getNews(),
            getSubjectCodes(),
            getYears(),
            getUsers(),
            getTimetableData(),
            getLectureGroups(),
            getLabs(),
            getTimetableCells(),
        ]);

        adminState.timetableSettings = settingsResponse.status === '200' ? settingsResponse.data : null;
        adminState.timeSlots = timeSlotsResponse.status === '200' && Array.isArray(timeSlotsResponse.data) ? timeSlotsResponse.data : [];
        adminState.columnHeadings = columnHeadingsResponse.status === '200' && Array.isArray(columnHeadingsResponse.data) ? columnHeadingsResponse.data : [];
        adminState.timetableRecords = timetableResponse.status === '200' && Array.isArray(timetableResponse.data) ? timetableResponse.data : [];
        adminState.lectureGroups = lectureGroupsResponse.status === '200' && Array.isArray(lectureGroupsResponse.data) ? lectureGroupsResponse.data : [];
        adminState.labs = labsResponse.status === '200' && Array.isArray(labsResponse.data) ? labsResponse.data : [];
        adminState.timetableCells = timetableCellsResponse.status === '200' && Array.isArray(timetableCellsResponse.data) ? timetableCellsResponse.data : [];
        adminState.lecturerRequests = lecturerRequestsResponse.status === '200' && Array.isArray(lecturerRequestsResponse.data) ? lecturerRequestsResponse.data : [];
        adminState.newsItems = newsResponse.status === '200' && Array.isArray(newsResponse.data) ? newsResponse.data : [];
        adminState.subjects = subjectsResponse.status === '200' && Array.isArray(subjectsResponse.data) ? subjectsResponse.data : [];
        adminState.years = yearsResponse.status === '200' && Array.isArray(yearsResponse.data) ? yearsResponse.data : [];
        adminState.users = usersResponse.status === '200' && Array.isArray(usersResponse.data)
            ? usersResponse.data
            : [];

        fullTimetableSettingsData = adminState.timetableSettings;
        fullTimeSlotsData = adminState.timeSlots;
        fullColumnHeadingsData = adminState.columnHeadings;
        fullSubjectCodesData = adminState.subjects;
        fullYearsData = adminState.years;
    };

    const reloadAdminPanel = async () => {
        try {
            refreshButton.disabled = true;
            refreshButton.textContent = 'Refreshing...';
            await loadAdminData();
            renderAdminPanel();
        } finally {
            refreshButton.disabled = false;
            refreshButton.textContent = 'Refresh Panel';
        }
    };

    refreshButton.addEventListener('click', reloadAdminPanel);
    newsCreateButton.addEventListener('click', openNewsForm);
    newsFormCancelButton.addEventListener('click', hideNewsForm);
    newsFormCloseButton.addEventListener('click', hideNewsForm);
    yearCreateButton.addEventListener('click', openYearForm);
    yearFormCancelButton.addEventListener('click', hideYearForm);
    yearFormCloseButton.addEventListener('click', hideYearForm);
    groupCreateButton.addEventListener('click', openGroupForm);
    groupFormCancelButton.addEventListener('click', hideGroupForm);
    groupFormCloseButton.addEventListener('click', hideGroupForm);
    labCreateButton.addEventListener('click', openLabForm);
    labFormCancelButton.addEventListener('click', hideLabForm);
    labFormCloseButton.addEventListener('click', hideLabForm);
    requestConfirmCancelButton.addEventListener('click', hideRequestConfirmForm);
    requestConfirmCloseButton.addEventListener('click', hideRequestConfirmForm);
    userCreateButton.addEventListener('click', openUserForm);
    userFormCancelButton.addEventListener('click', hideUserForm);
    userFormCloseButton.addEventListener('click', hideUserForm);
    subjectCreateButton.addEventListener('click', openSubjectForm);
    subjectFormCancelButton.addEventListener('click', hideSubjectForm);
    subjectFormCloseButton.addEventListener('click', hideSubjectForm);
    settingsFormCancelButton.addEventListener('click', hideSettingsForm);
    settingsFormCloseButton.addEventListener('click', hideSettingsForm);
    columnHeadingCreateButton.addEventListener('click', openColumnHeadingForm);
    columnHeadingFormCancelButton.addEventListener('click', hideColumnHeadingForm);
    columnHeadingFormCloseButton.addEventListener('click', hideColumnHeadingForm);
    timeSlotCreateButton.addEventListener('click', openTimeSlotForm);
    timeSlotFormCancelButton.addEventListener('click', hideTimeSlotForm);
    timeSlotFormCloseButton.addEventListener('click', hideTimeSlotForm);
    timetableCreateButton.addEventListener('click', () => {
        openTimetableForm();
        showAdminSection('admin-manage-timetable');
    });
    timetableFormCancelButton.addEventListener('click', hideTimetableForm);
    timetableFormCloseButton.addEventListener('click', hideTimetableForm);

    settingsTableContainer.addEventListener('click', async (e) => {
        const updateButton = e.target.closest('#admin-settings-update-btn');
        if (updateButton) {
            openSettingsForm(adminState.timetableSettings);
            return;
        }

        const resetButton = e.target.closest('#admin-settings-reset-btn');
        if (!resetButton || !adminState.timetableSettings) return;

        const isConfirmed = window.confirm('Do you want to reset the timetable settings?');
        if (!isConfirmed) return;

        try {
            const result = await resetTimetableSettings({
                id: adminState.timetableSettings.id,
                updated_by: getAuditValue(),
            });
            window.alert(result.message || 'Timetable settings reset successfully.');
            hideSettingsForm();
            hideColumnHeadingForm();
            hideTimeSlotForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to reset timetable settings.');
        }
    });

    settingsForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            const result = await updateTimetableSettings({
                id: settingsIdInput.value || '',
                table_row_count: settingsRowsInput.value || 0,
                table_column_count: settingsColumnsInput.value || 0,
                break_row_number: settingsBreakRowInput.value || 0,
                updated_by: getAuditValue(),
            });

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to update timetable settings.');
                return;
            }

            window.alert(result.message || 'Timetable settings updated successfully.');
            hideSettingsForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to update timetable settings.');
        }
    });

    columnHeadingsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-column-heading-action]');
        if (!actionButton) return;

        const headingId = actionButton.getAttribute('data-column-heading-id') || '';
        const headingAction = actionButton.getAttribute('data-column-heading-action') || '';
        const selectedHeading = adminState.columnHeadings.find(item => String(item.id) === String(headingId));
        if (!selectedHeading) return;

        if (headingAction === 'update') {
            openColumnHeadingForm(selectedHeading);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this column heading?');
        if (!isConfirmed) return;

        try {
            const result = await deleteColumnHeading(headingId);
            window.alert(result.message || 'Column heading deleted successfully.');
            hideColumnHeadingForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to delete column heading.');
        }
    });

    columnHeadingForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: columnHeadingIdInput.value || '',
            column_heading: columnHeadingNameInput.value.trim(),
            column_number: columnHeadingNumberInput.value || '',
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateColumnHeading(payload)
                : await createColumnHeading(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save column heading.');
                return;
            }

            window.alert(result.message || 'Column heading saved successfully.');
            hideColumnHeadingForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to save column heading.');
        }
    });

    timeSlotsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-time-slot-action]');
        if (!actionButton) return;

        const timeSlotId = actionButton.getAttribute('data-time-slot-id') || '';
        const timeSlotAction = actionButton.getAttribute('data-time-slot-action') || '';
        const selectedTimeSlot = adminState.timeSlots.find(item => String(item.id) === String(timeSlotId));
        if (!selectedTimeSlot) return;

        if (timeSlotAction === 'update') {
            openTimeSlotForm(selectedTimeSlot);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this time slot?');
        if (!isConfirmed) return;

        try {
            const result = await deleteTimeSlot(timeSlotId);
            window.alert(result.message || 'Time slot deleted successfully.');
            hideTimeSlotForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to delete time slot.');
        }
    });

    timeSlotForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: timeSlotIdInput.value || '',
            start_time: timeSlotStartInput.value || '',
            end_time: timeSlotEndInput.value || '',
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateTimeSlot(payload)
                : await createTimeSlot(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save time slot.');
                return;
            }

            window.alert(result.message || 'Time slot saved successfully.');
            hideTimeSlotForm();
            await reloadAdminPanel();
            showAdminSection('admin-timetable-settings');
        } catch (error) {
            window.alert(error.message || 'Failed to save time slot.');
        }
    });

    manageTimetableContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-timetable-action]');
        if (!actionButton) return;

        const timetableId = actionButton.getAttribute('data-timetable-id') || '';
        const timetableAction = actionButton.getAttribute('data-timetable-action') || '';
        const selectedRecord = adminState.timetableRecords.find(item => String(item.timetable_id) === String(timetableId));
        if (!selectedRecord) return;

        if (timetableAction === 'update') {
            openTimetableForm(selectedRecord);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this timetable record?');
        if (!isConfirmed) {
            return;
        }

        try {
            const result = await deleteTimetableRecord(timetableId);
            window.alert(result.message || 'Timetable record deleted successfully.');
            hideTimetableForm();
            await reloadAdminPanel();
            showAdminSection('admin-manage-timetable');
        } catch (error) {
            window.alert(error.message || 'Failed to delete timetable record.');
        }
    });

    timetableForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const selectedDayId = timetableDaySelect.value || '';
        const selectedTimeSlotId = timetableTimeSlotSelect.value || '';
        const resolvedCellId = getTimetableCellReferenceId(selectedDayId, selectedTimeSlotId);
        const updatedByValue = currentUser.email || String(currentUser.id || '');

        if (!resolvedCellId) {
            window.alert('Please select a valid day and time slot.');
            return;
        }

        const payload = {
            id: timetableIdInput.value || '',
            cell_id: resolvedCellId,
            lecture_group_id: timetableGroupSelect.value || '',
            lab_id: timetableLabSelect.value || '',
            subject_cord: timetableSubjectSelect.value || '',
            action: timetableActionSelect.value || 'free',
            created_by: updatedByValue,
            updated_by: updatedByValue,
        };

        try {
            const result = payload.id
                ? await updateTimetableRecord(payload)
                : await createTimetableRecord(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save timetable record.');
                return;
            }

            window.alert(result.message || 'Timetable record saved successfully.');
            hideTimetableForm();
            await reloadAdminPanel();
            showAdminSection('admin-manage-timetable');
        } catch (error) {
            window.alert(error.message || 'Failed to save timetable record.');
        }
    });

    requestsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-request-action]');
        if (!actionButton) return;

        const requestId = actionButton.getAttribute('data-request-id') || '';
        const requestAction = actionButton.getAttribute('data-request-action') || '';
        const selectedRequest = adminState.lecturerRequests.find(item => String(item.id) === String(requestId));
        if (!selectedRequest) return;

        try {
            if (requestAction === 'delete') {
                const result = await deleteLecturerRequest(requestId);
                window.alert(result.message || 'Lecturer request deleted successfully.');
            } else if (requestAction === 'confirmed') {
                openRequestConfirmForm(selectedRequest);
                return;
            } else {
                const result = await updateLecturerRequest({
                    id: selectedRequest.id,
                    lecturer_id: selectedRequest.lecturer_id,
                    subject_id: selectedRequest.subject_id,
                    year_id: selectedRequest.year_id,
                    timetable_time_slot_id: selectedRequest.timetable_time_slot_id,
                    timetable_column_heading_id: selectedRequest.timetable_column_heading_id,
                    date: selectedRequest.date,
                    action: requestAction,
                    lecturer_request: selectedRequest.lecturer_request,
                    created_by: selectedRequest.lecturer_name || getAuditValue(),
                    updated_by: getAuditValue(),
                });
                window.alert(result.message || 'Lecturer request updated successfully.');
            }

            await reloadAdminPanel();
        } catch (error) {
            window.alert(error.message || 'Failed to update lecturer request.');
        }
    });

    requestConfirmForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!requestConfirmLabSelect.value) {
            window.alert('Please select a lab before confirming this lecturer request.');
            return;
        }

        try {
            const result = await updateLecturerRequest({
                id: requestConfirmIdInput.value || '',
                lecturer_id: requestConfirmLecturerIdInput.value || '',
                subject_id: requestConfirmSubjectIdInput.value || '',
                year_id: requestConfirmYearIdInput.value || '',
                timetable_time_slot_id: requestConfirmTimeSlotIdInput.value || '',
                timetable_column_heading_id: requestConfirmColumnIdInput.value || '',
                lecture_group_id: requestConfirmGroupIdInput.value || '',
                lab_id: requestConfirmLabSelect.value || '',
                date: requestConfirmDateInput.value || '',
                action: 'confirmed',
                lecturer_request: requestConfirmDescriptionInput.value || '',
                created_by: requestConfirmLecturerNameInput.value || getAuditValue(),
                updated_by: getAuditValue(),
            });

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to confirm lecturer request.');
                return;
            }

            window.alert(result.message || 'Lecturer request confirmed successfully.');
            hideRequestConfirmForm();
            await reloadAdminPanel();
            showAdminSection('admin-requests');
        } catch (error) {
            window.alert(error.message || 'Failed to confirm lecturer request.');
        }
    });

    requestCheckAvailabilityButton.addEventListener('click', async () => {
        requestCheckResult.textContent = 'Checking...';
        requestCheckResult.className = 'text-sm font-bold text-sky-700 text-right';

        try {
            const result = await checkLecturerRequestAvailability({
                timetable_time_slot_id: requestConfirmTimeSlotIdInput.value || '',
                timetable_column_heading_id: requestConfirmColumnIdInput.value || '',
                date: requestConfirmDateInput.value || '',
            });

            if (result.status !== '200') {
                requestCheckResult.textContent = result.message || 'Failed to check booking.';
                requestCheckResult.className = 'text-sm font-bold text-red-700 text-right';
                return;
            }

            if (result.data?.is_booked) {
                const bookedRecord = result.data.record || {};
                requestCheckResult.textContent = `Already booked: ${bookedRecord.subject_cord || '-'}${bookedRecord.group_name ? ` / ${bookedRecord.group_name}` : ''}${bookedRecord.lab_name ? ` / ${bookedRecord.lab_name}` : ''}`;
                requestCheckResult.className = 'text-sm font-bold text-red-700 text-right';
                return;
            }

            requestCheckResult.textContent = 'This request date is available.';
            requestCheckResult.className = 'text-sm font-bold text-green-700 text-right';
        } catch (error) {
            requestCheckResult.textContent = error.message || 'Failed to check booking.';
            requestCheckResult.className = 'text-sm font-bold text-red-700 text-right';
        }
    });

    newsForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(newsForm);
        if (newsIdInput.value) {
            formData.append('id', newsIdInput.value);
        }
        if (!newsIdInput.value) {
            formData.append('created_by', currentUser.id);
        }
        formData.append('updated_by', currentUser.id);

        try {
            const result = newsIdInput.value
                ? await updateNews(formData)
                : await createNews(formData);
            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save news.');
                return;
            }

            window.alert(result.message || 'News saved successfully.');
            hideNewsForm();
            await reloadAdminPanel();
            showAdminSection('admin-news');
        } catch (error) {
            window.alert(error.message || 'Failed to save news.');
        }
    });

    newsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-news-action]');
        if (!actionButton) return;

        const newsId = actionButton.getAttribute('data-news-id') || '';
        if (!newsId) return;

        const selectedNews = adminState.newsItems.find(item => String(item.id) === String(newsId));
        if (!selectedNews) return;

        const newsAction = actionButton.getAttribute('data-news-action') || '';
        if (newsAction === 'update') {
            openNewsForm(selectedNews);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this news record?');
        if (!isConfirmed) return;

        try {
            const result = await deleteNews(newsId);
            window.alert(result.message || 'News deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-news');
        } catch (error) {
            window.alert(error.message || 'Failed to delete news.');
        }
    });

    yearForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: yearIdInput.value || '',
            year: yearNameInput.value.trim(),
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateYear(payload)
                : await createYear(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save year.');
                return;
            }

            window.alert(result.message || 'Year saved successfully.');
            hideYearForm();
            await reloadAdminPanel();
            showAdminSection('admin-years');
        } catch (error) {
            window.alert(error.message || 'Failed to save year.');
        }
    });

    yearsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-year-action]');
        if (!actionButton) return;

        const yearId = actionButton.getAttribute('data-year-id') || '';
        const yearAction = actionButton.getAttribute('data-year-action') || '';
        const selectedYear = adminState.years.find(item => String(item.id) === String(yearId));
        if (!selectedYear) return;

        if (yearAction === 'update') {
            openYearForm(selectedYear);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this year record?');
        if (!isConfirmed) return;

        try {
            const result = await deleteYear(yearId);
            window.alert(result.message || 'Year deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-years');
        } catch (error) {
            window.alert(error.message || 'Failed to delete year.');
        }
    });

    groupForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: groupIdInput.value || '',
            group_name: groupNameInput.value.trim(),
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateLectureGroup(payload)
                : await createLectureGroup(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save group.');
                return;
            }

            window.alert(result.message || 'Group saved successfully.');
            hideGroupForm();
            await reloadAdminPanel();
            showAdminSection('admin-groups');
        } catch (error) {
            window.alert(error.message || 'Failed to save group.');
        }
    });

    groupsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-group-action]');
        if (!actionButton) return;

        const groupId = actionButton.getAttribute('data-group-id') || '';
        const groupAction = actionButton.getAttribute('data-group-action') || '';
        const selectedGroup = adminState.lectureGroups.find(item => String(item.id) === String(groupId));
        if (!selectedGroup) return;

        if (groupAction === 'update') {
            openGroupForm(selectedGroup);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this group record?');
        if (!isConfirmed) return;

        try {
            const result = await deleteLectureGroup(groupId);
            window.alert(result.message || 'Group deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-groups');
        } catch (error) {
            window.alert(error.message || 'Failed to delete group.');
        }
    });

    labForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: labIdInput.value || '',
            lab_name: labNameInput.value.trim(),
            lab_location: labLocationInput.value.trim(),
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateLab(payload)
                : await createLab(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save lab.');
                return;
            }

            window.alert(result.message || 'Lab saved successfully.');
            hideLabForm();
            await reloadAdminPanel();
            showAdminSection('admin-labs');
        } catch (error) {
            window.alert(error.message || 'Failed to save lab.');
        }
    });

    labsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-lab-action]');
        if (!actionButton) return;

        const labId = actionButton.getAttribute('data-lab-id') || '';
        const labAction = actionButton.getAttribute('data-lab-action') || '';
        const selectedLab = adminState.labs.find(item => String(item.id) === String(labId));
        if (!selectedLab) return;

        if (labAction === 'update') {
            openLabForm(selectedLab);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this lab record?');
        if (!isConfirmed) return;

        try {
            const result = await deleteLab(labId);
            window.alert(result.message || 'Lab deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-labs');
        } catch (error) {
            window.alert(error.message || 'Failed to delete lab.');
        }
    });

    subjectForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id: subjectIdInput.value || '',
            subject_cord: subjectCodeInput.value.trim(),
            subject: subjectNameInput.value.trim(),
            year_id: subjectYearSelect.value || '',
            created_by: getAuditValue(),
            updated_by: getAuditValue(),
        };

        try {
            const result = payload.id
                ? await updateSubject(payload)
                : await createSubject(payload);

            if (result.status !== '200') {
                window.alert(result.message || 'Failed to save subject.');
                return;
            }

            window.alert(result.message || 'Subject saved successfully.');
            hideSubjectForm();
            await reloadAdminPanel();
            showAdminSection('admin-subjects');
        } catch (error) {
            window.alert(error.message || 'Failed to save subject.');
        }
    });

    subjectsContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-subject-action]');
        if (!actionButton) return;

        const subjectId = actionButton.getAttribute('data-subject-id') || '';
        const subjectAction = actionButton.getAttribute('data-subject-action') || '';
        const selectedSubject = adminState.subjects.find(item => String(item.subject_id || item.id) === String(subjectId));
        if (!selectedSubject) return;

        if (subjectAction === 'update') {
            openSubjectForm(selectedSubject);
            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this subject record?');
        if (!isConfirmed) return;

        try {
            const result = await deleteSubject(subjectId);
            window.alert(result.message || 'Subject deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-subjects');
        } catch (error) {
            window.alert(error.message || 'Failed to delete subject.');
        }
    });

    userForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const auditValue = getAuditValue();
        const isUpdateMode = Boolean(userIdInput.value);
        const passwordValue = userPasswordInput.value || '';
        const confirmPasswordValue = userConfirmPasswordInput.value || '';

        if (!isUpdateMode) {
            if (!passwordValue) {
                window.alert('Password is required for a new user.');
                return;
            }

            if (passwordValue !== confirmPasswordValue) {
                window.alert('Password and confirm password must match.');
                return;
            }
        }

        const userPayload = {
            id: userIdInput.value || '',
            initials: userInitialsInput.value.trim(),
            initials_stand_for: userInitialsStandForInput.value.trim(),
            first_name: userFirstNameInput.value.trim(),
            last_name: userLastNameInput.value.trim(),
            honorifics: userHonorificsSelect.value || '',
            nic: userNicInput.value.trim(),
            email: userEmailInput.value.trim(),
            mobile_number: userMobileInput.value.trim(),
            role: userRoleSelect.value || '',
            created_by: auditValue,
            updated_by: auditValue,
        };

        try {
            const result = userPayload.id
                ? await updateUser(userPayload)
                : await createUser({
                    ...userPayload,
                    password: passwordValue,
                });
            if (result.status !== '200') {
                const validationErrors = Array.isArray(result.errors) ? result.errors.join('\n') : '';
                window.alert(validationErrors || result.message || 'Failed to save user.');
                return;
            }

            window.alert(result.message || 'User saved successfully.');
            hideUserForm();
            await reloadAdminPanel();
            showAdminSection('admin-users');
        } catch (error) {
            window.alert(error.message || 'Failed to save user.');
        }
    });

    usersContainer.addEventListener('click', async (e) => {
        const actionButton = e.target.closest('[data-user-action]');
        if (!actionButton) return;

        const userId = actionButton.getAttribute('data-user-id') || '';
        const userAction = actionButton.getAttribute('data-user-action') || '';
        const selectedUser = adminState.users.find(item => String(item.id) === String(userId));
        if (!selectedUser) return;

        if (userAction === 'update') {
            openUserForm(selectedUser);
            return;
        }

        if (userAction === 'reset-password') {
            const currentPassword = window.prompt('Enter your current login password to authorize this reset:');
            if (currentPassword === null) return;

            const newPassword = window.prompt(`Enter a new password for ${selectedUser.email || selectedUser.first_name || 'this user'}:`);
            if (newPassword === null) return;

            const confirmNewPassword = window.prompt('Confirm the new password:');
            if (confirmNewPassword === null) return;

            if (!newPassword.trim()) {
                window.alert('New password is required.');
                return;
            }

            if (newPassword !== confirmNewPassword) {
                window.alert('New password and confirm password must match.');
                return;
            }

            try {
                const result = await resetUserPassword({
                    target_user_id: selectedUser.id,
                    actor_user_id: currentUser.id,
                    current_password: currentPassword,
                    new_password: newPassword,
                    updated_by: getAuditValue(),
                });

                if (result.status !== '200') {
                    window.alert(result.message || 'Failed to reset password.');
                    return;
                }

                window.alert(result.message || 'Password reset successfully.');
                await reloadAdminPanel();
                showAdminSection('admin-users');
            } catch (error) {
                window.alert(error.message || 'Failed to reset password.');
            }

            return;
        }

        const isConfirmed = window.confirm('Do you want to delete this user?');
        if (!isConfirmed) return;

        try {
            const result = await deleteUser(userId);
            window.alert(result.message || 'User deleted successfully.');
            await reloadAdminPanel();
            showAdminSection('admin-users');
        } catch (error) {
            window.alert(error.message || 'Failed to delete user.');
        }
    });

    try {
        await reloadAdminPanel();
    } catch (error) {
        console.error('Error loading admin panel:', error);
        adminPanel.innerHTML = `<div class="bg-red-50 text-red-700 rounded-2xl p-6 font-bold">Failed to load admin panel data.</div>`;
    }
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
    const lectureGroupSelect = document.getElementById('lecture_group_select');
    const requestDateInput = document.getElementById('request_date');
    const requestTextarea = document.getElementById('request');
    const closeBtn = document.getElementById('scheduling-form-close');
    const viewCloseBtn = document.getElementById('scheduling-form-view-close');
    const lecturerRequestBtn = document.getElementById('lecturer-request');
    const lecturerRequestFormBtn = document.getElementById('lecturer-request-form');
    const tableBody = document.getElementById('timetable-body');

    if (!formSection || !viewSection || !formElement || !cellIdInput || !yearSelect || !subjectCodeSelect || !timeSlotSelect || !daySelect || !lectureGroupSelect || !requestDateInput || !requestTextarea || !tableBody || !lecturerRequestBtn || !lecturerRequestFormBtn) return;

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
        lectureGroupSelect.value = '';
        requestDateInput.value = getTodayDateValue();
    };

    const showSchedulingModal = (modalElement) => {
        if (!modalElement) return;
        modalElement.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        modalElement.scrollTop = 0;
    };

    const hideSchedulingModal = (modalElement) => {
        if (!modalElement) return;
        modalElement.classList.add('hidden');
        if (formSection.classList.contains('hidden') && viewSection.classList.contains('hidden')) {
            document.body.classList.remove('overflow-hidden');
        }
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
        hideSchedulingModal(viewSection);
        showSchedulingModal(formSection);
    };

    tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.timetable-cell-btn');
        if (!btn) return;

        const isLoggedIn = Boolean(getCurrentUserRole());
        const cellId = btn.getAttribute('data-cell-id') || '';
        selectedCellId = cellId;
        populateSchedulingFormView(cellId);
        syncLecturerRequestButtons(isLoggedIn);

        hideSchedulingModal(formSection);
        showSchedulingModal(viewSection);
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideSchedulingModal(formSection);
            resetSchedulingForm();
        });
    }

    if (viewCloseBtn) {
        viewCloseBtn.addEventListener('click', () => {
            hideSchedulingModal(viewSection);
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
                hideSchedulingModal(formSection);
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
initNewsPage();
initAdminSideNav();
initAdminPanel();

if (document.getElementById('timetable-body')) {
    loadTimetableData();
    populateSubjectCodeSelects();
    populateYearsSelects();
}
