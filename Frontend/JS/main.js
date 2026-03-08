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

import { getTimetableData, getSubjectCodes, getYears } from '../API/timetableApi.js';
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

const TABLE_CELLS = [
    [1,2,3,4,5],
    [6,7,8,9,10],
    [11,12,13,14,15],
    [16,17,18,19,20],
    [21,22,23,24,25],
    [26,27,28,29,30],
    [31,32,33,34,35],
    [36,37,38,39,40]
];
const TABLE_TIMES = [
    '8.00/9.00', '9.00/10.00', '10.00/11.00', '11.00/12.00',
    '1.00/2.00', '2.00/4.00', '3.00/4.00', '4.00/5.00'
];

let fullTimetableData = [];
let fullSubjectCodesData = [];

/**
 * Renders the timetable table with the given data
 * @param {Array} data - Filtered timetable data
 */
const renderTimetableTable = (data) => {
    const tableBody = document.getElementById('timetable-body');
    if (!tableBody) return;

    tableBody.innerHTML = '';
    for (let i = 0; i < 8; i++) {
        let tableRow = '';
        if (i === 4) {
            tableRow += `
                <tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">12.00/1.00</td>
                    <td colspan="5" class=""><p class="px-6 py-6 font-bold text-lg w-full h-full hover:bg-gray-400 text-center"> Interval </p></td>
                </tr>`;
        }
        tableRow += `<tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">`;
        for (let j = 0; j < 5; j++) {
            const cellId = TABLE_CELLS[i][j];
            const cellData = data.find(item => item.cell_id === cellId);
            if (j === 0) {
                tableRow += `<td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">${TABLE_TIMES[i]}</td>`;
            }
            tableRow += cellData
                ? `<td class=""><button type="button" class="timetable-cell-btn px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300" data-cell-id="${cellId}"> ${cellData.subject_cord || ''} </button></td>`
                : `<td class=""><button type="button" class="timetable-cell-btn px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300" data-cell-id="${cellId}">  </button></td>`;
        }
        tableRow += `</tr>`;
        tableBody.innerHTML += tableRow;
    }
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
    const cellIdInput = document.getElementById('cell_id');
    const closeBtn = document.getElementById('scheduling-form-close');
    const viewCloseBtn = document.getElementById('scheduling-form-view-close');
    const lecturerRequestBtn = document.getElementById('lecturer-request');
    const tableBody = document.getElementById('timetable-body');

    if (!formSection || !viewSection || !cellIdInput || !tableBody || !lecturerRequestBtn) return;

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

    tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.timetable-cell-btn');
        if (!btn) return;

        const isLoggedIn = Boolean(getCurrentUserRole());
        const cellId = btn.getAttribute('data-cell-id') || '';
        selectedCellId = cellId;
        populateSchedulingFormView(cellId);

        if (isLoggedIn) {
            lecturerRequestBtn.classList.remove('hidden');
        } else {
            lecturerRequestBtn.classList.add('hidden');
        }

        viewSection.classList.remove('hidden');
        formSection.classList.add('hidden');
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            formSection.classList.add('hidden');
            if (cellIdInput) cellIdInput.value = '';
        });
    }

    if (viewCloseBtn) {
        viewCloseBtn.addEventListener('click', () => {
            viewSection.classList.add('hidden');
        });
    }

    lecturerRequestBtn.addEventListener('click', () => {
        if (!Boolean(getCurrentUserRole())) return;

        cellIdInput.value = selectedCellId;
        viewSection.classList.add('hidden');
        formSection.classList.remove('hidden');
    });
};

const loadTimetableData = async () => {
    try {
        const timetableData = await getTimetableData();
        fullTimetableData = timetableData.data || [];
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
