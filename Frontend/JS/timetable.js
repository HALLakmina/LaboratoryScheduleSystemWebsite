import { getTimetableData, getTemporaryTimetableData, getSubjectCodes, getYears, getTimeSlots, getColumnHeadings, getTimetableSettings, getLectureGroups, getTimetableCells } from '../API/timetableApi.js';
import { sendLecturerRequest } from '../API/lecturerRequestApi.js';
import { getCurrentUserRole, getStoredUser } from './loginUser.js';

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
        const [timeSlotsResponse, columnHeadingsResponse, settingsResponse, lectureGroupsResponse, timetableCellsResponse] = await Promise.all([
            getTimeSlots(),
            getColumnHeadings(),
            getTimetableSettings(),
            getLectureGroups(),
            getTimetableCells(),
        ]);

        fullTimeSlotsData = timeSlotsResponse.status === '200' && timeSlotsResponse.data ? timeSlotsResponse.data : [];
        fullColumnHeadingsData = columnHeadingsResponse.status === '200' && columnHeadingsResponse.data ? columnHeadingsResponse.data : [];
        fullTimetableSettingsData = settingsResponse.status === '200' ? settingsResponse.data : null;
        fullLectureGroupsData = lectureGroupsResponse.status === '200' && lectureGroupsResponse.data ? lectureGroupsResponse.data : [];
        fullTimetableCellsData = timetableCellsResponse.status === '200' && timetableCellsResponse.data ? timetableCellsResponse.data : [];

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
    const activeColumnHeadings = getActiveColumnHeadings();

    tableHead.innerHTML = `
        <tr>
            <th scope="col" class="px-6 py-3">Time</th>
            ${activeColumnHeadings.map(item => `<th scope="col" class="px-6 py-3">${item.column_heading}</th>`).join('')}
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
    const activeColumnHeadings = getActiveColumnHeadings();

    daySelect.innerHTML = `<option value="">--</option>`;
    activeColumnHeadings.forEach(item => {
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

const getColumnHeadingById = (columnHeadingId) => (
    fullColumnHeadingsData.find((item) => String(item.id) === String(columnHeadingId)) || null
);

const getTimeSlotById = (timeSlotId) => (
    fullTimeSlotsData.find((item) => String(item.id) === String(timeSlotId)) || null
);

let fullTimetableData = [];
let permanentTimetableData = [];
let temporaryTimetableData = [];
let fullSubjectCodesData = [];
let fullYearsData = [];
let fullTimeSlotsData = [];
let fullColumnHeadingsData = [];
let fullTimetableSettingsData = null;
let fullLectureGroupsData = [];
let fullTimetableCellsData = [];

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
const WEEKDAY_INDEX_MAP = {
    sunday: 0,
    monday: 1,
    tuesday: 2,
    wednesday: 3,
    thursday: 4,
    friday: 5,
    saturday: 6,
};

const getActiveColumnHeadings = () => (
    Array.isArray(fullColumnHeadingsData)
        ? fullColumnHeadingsData
            .filter((item) => String(item.status || 'active') === 'active')
            .sort((left, right) => Number(left.column_heading_number || 0) - Number(right.column_heading_number || 0))
        : []
);

const getActiveTimeSlots = () => {
    if (!fullTimetableSettingsData || !Array.isArray(fullTimeSlotsData)) return fullTimeSlotsData;

    const breakRowNumber = Number(fullTimetableSettingsData.break_row_number || 0);
    if (!breakRowNumber) return fullTimeSlotsData;

    return fullTimeSlotsData.filter((_, index) => index !== breakRowNumber - 1);
};

const getTimetableCellGrid = () => {
    if (!fullTimetableSettingsData || !Array.isArray(fullTimetableCellsData)) return [];

    const columnCount = Number(fullTimetableSettingsData.table_column_count || 0);
    if (!columnCount || !fullTimetableCellsData.length) return [];

    const orderedCellIds = fullTimetableCellsData
        .slice()
        .sort((left, right) => Number(left.id || 0) - Number(right.id || 0))
        .map((item) => Number(item.id || 0))
        .filter((value) => value > 0);

    const grid = [];
    for (let index = 0; index < orderedCellIds.length; index += columnCount) {
        grid.push(orderedCellIds.slice(index, index + columnCount));
    }

    return grid;
};

const findTimetableCellByRefs = (timeSlotId, columnHeadingId) => (
    fullTimetableCellsData.find((item) => (
        String(item.time_slot_id || '') === String(timeSlotId)
        && String(item.column_heading_id || '') === String(columnHeadingId)
    )) || null
);

const formatDateKey = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const getCurrentWeekDateRange = () => {
    const today = new Date();
    const currentDay = today.getDay();
    const sundayOffset = -currentDay;
    const sunday = new Date(today);
    sunday.setHours(0, 0, 0, 0);
    sunday.setDate(today.getDate() + sundayOffset);

    const saturday = new Date(sunday);
    saturday.setDate(sunday.getDate() + 6);

    return {
        start: formatDateKey(sunday),
        end: formatDateKey(saturday),
    };
};

const getThisWeekHeadingDateMap = () => {
    const { start } = getCurrentWeekDateRange();
    const sunday = new Date(`${start}T00:00:00`);
    const map = {};
    const activeColumnHeadings = getActiveColumnHeadings();

    activeColumnHeadings.forEach((heading, index) => {
        const date = new Date(sunday);
        date.setDate(sunday.getDate() + index);
        map[String(heading.id || '')] = formatDateKey(date);
    });

    return map;
};

const buildEffectiveTimetableData = (permanentData, temporaryData) => {
    const buildScheduleKey = (item) => `${item?.time_slot_id || ''}:${item?.column_heading_id || ''}`;
    const mergedByScheduleKey = new Map();

    (permanentData || []).forEach(item => {
        mergedByScheduleKey.set(buildScheduleKey(item), { ...item, data_source: 'permanent' });
    });

    (temporaryData || []).forEach(item => {
        mergedByScheduleKey.set(buildScheduleKey(item), {
            ...item,
            action: item.action || (item.subject_cord ? 'pending' : 'free'),
            data_source: 'temporary',
        });
    });

    return Array.from(mergedByScheduleKey.values()).sort((left, right) => {
        const leftTimeSlotId = Number(left.time_slot_id || 0);
        const rightTimeSlotId = Number(right.time_slot_id || 0);
        if (leftTimeSlotId !== rightTimeSlotId) {
            return leftTimeSlotId - rightTimeSlotId;
        }

        return Number(left.column_heading_id || 0) - Number(right.column_heading_id || 0);
    });
};

/**
 * Renders the timetable table with the given data
 * @param {Array} data - Filtered timetable data
 */
const renderTimetableTable = (data) => {
    const tableBody = document.getElementById('timetable-body');
    if (!tableBody) return;

    const activeTimeSlots = getActiveTimeSlots();
    const activeColumnHeadings = getActiveColumnHeadings();
    const columnCount = activeColumnHeadings.length;
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
        activeColumnHeadings.forEach((heading, columnIndex) => {
            const linkedCell = findTimetableCellByRefs(timeSlot.id, heading.id);
            const cellId = linkedCell?.id || '';
            const cellData = data.find((item) => (
                String(item.time_slot_id || '') === String(timeSlot.id)
                && String(item.column_heading_id || '') === String(heading.id)
            ));
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
    const lecturerRequestFormContainer = document.getElementById('lecturer-request-form-container');
    const tableBody = document.getElementById('timetable-body');

    if (!formSection || !viewSection || !formElement || !cellIdInput || !yearSelect || !subjectCodeSelect || !timeSlotSelect || !daySelect || !lectureGroupSelect || !requestDateInput || !requestTextarea || !tableBody || !lecturerRequestBtn || !lecturerRequestFormBtn || !lecturerRequestFormContainer) return;

    let selectedCellId = '';
    let selectedScheduleMeta = {
        timeSlot: '',
        day: '',
    };

    const setViewText = (id, value) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = value && String(value).trim() !== '' ? String(value) : '-';
    };

    const setLectureActionBadge = (action) => {
        const badge = document.getElementById('lecture-action-badge');
        if (!badge) return;

        badge.classList.remove('bg-green-700', 'bg-purple-800', 'bg-red-700', 'bg-amber-600', 'bg-gray-700');

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
        if (normalized === 'pending') {
            badge.classList.add('bg-amber-600');
            return;
        }
        badge.classList.add('bg-gray-700');
    };

    const populateSchedulingFormView = (cellId) => {
        const cellData = fullTimetableData.find(item => String(item.cell_id) === String(cellId));
        const action = cellData?.action || 'free';
        const actionLabel = cellData?.data_source === 'temporary' && String(action).toLowerCase() === 'pending'
            ? 'TEMPORARY LECTURE'
            : String(action).toUpperCase();

        setViewText('lecture-action', actionLabel);
        setLectureActionBadge(action);
        setViewText('subject-name', cellData?.subject || '');
        setViewText('subject-code', cellData?.subject_cord || '');
        setViewText('lecture-in-charge', cellData?.lecturer_name || '');
        setViewText('lecture', cellData?.year || '');
        setViewText('lecture-group', cellData?.group_name || '');
        setViewText('lab', cellData?.lab || '');
    };

    const getCellScheduleMeta = (cellId) => {
        const matchedCell = fullTimetableCellsData.find((item) => Number(item.id) === Number(cellId));
        const matchedTimeSlot = getTimeSlotById(matchedCell?.time_slot_id || '');
        const matchedHeading = getColumnHeadingById(matchedCell?.column_heading_id || '');

        if (matchedCell) {
            return {
                timeSlot: matchedTimeSlot ? formatTimeSlotLabel(matchedTimeSlot.start_time, matchedTimeSlot.end_time) : '',
                day: matchedHeading?.column_heading || '',
            };
        }

        return {
            timeSlot: '',
            day: '',
        };
    };

    const getCellScheduleMetaFromButton = (buttonElement) => {
        const cellElement = buttonElement?.closest('td');
        const rowElement = buttonElement?.closest('tr');
        if (!cellElement || !rowElement) {
            return {
                timeSlot: '',
                day: '',
            };
        }

        const rowCells = Array.from(rowElement.children);
        const clickedCellIndex = rowCells.indexOf(cellElement);
        const headingIndex = clickedCellIndex - 1;
        const activeColumnHeadings = getActiveColumnHeadings();
        const rowTimeLabel = rowCells[0]?.textContent?.trim() || '';
        const headingLabel = activeColumnHeadings[headingIndex]?.column_heading || '';

        return {
            timeSlot: rowTimeLabel,
            day: headingLabel,
        };
    };

    const setSelectValueSafe = (selectElement, value) => {
        if (!selectElement) return;

        const normalizedValue = String(value || '');
        const hasMatchingOption = Array.from(selectElement.options).some(
            (option) => String(option.value) === normalizedValue
        );

        selectElement.value = hasMatchingOption ? normalizedValue : '';
    };

    const normalizeDayValue = (value) => String(value || '').trim().toLowerCase();

    const formatDateInputValue = (date) => {
        const safeDate = new Date(date);
        safeDate.setHours(0, 0, 0, 0);
        return formatDateKey(safeDate);
    };

    const getNextDateForDay = (dayValue) => {
        const targetDayIndex = WEEKDAY_INDEX_MAP[normalizeDayValue(dayValue)];
        if (targetDayIndex === undefined) {
            return getTodayDateValue();
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const currentDayIndex = today.getDay();
        const dayOffset = (targetDayIndex - currentDayIndex + 7) % 7;
        const nextDate = new Date(today);
        nextDate.setDate(today.getDate() + dayOffset);
        return formatDateInputValue(nextDate);
    };

    const isMatchingSelectedDay = (dateValue, dayValue) => {
        const targetDayIndex = WEEKDAY_INDEX_MAP[normalizeDayValue(dayValue)];
        if (!dateValue || targetDayIndex === undefined) {
            return false;
        }

        const selectedDate = new Date(`${dateValue}T00:00:00`);
        return selectedDate.getDay() === targetDayIndex;
    };

    const syncRequestDateWithDay = ({ forceNextValidDate = false } = {}) => {
        const selectedDay = daySelect.value || '';

        const todayValue = getTodayDateValue();
        requestDateInput.min = todayValue;
        requestDateInput.setCustomValidity('');
        
        console.log(requestDateInput)
        if (!selectedDay) {
            if (forceNextValidDate) {
                requestDateInput.value = todayValue;
            }
            return;
        }
        
        const nextValidDate = getNextDateForDay(selectedDay);
        requestDateInput.min = nextValidDate;
        
        if (forceNextValidDate || !requestDateInput.value || requestDateInput.value < nextValidDate || !isMatchingSelectedDay(requestDateInput.value, selectedDay)) {
            requestDateInput.value = nextValidDate;
        }
    };

    const validateRequestDateForSelectedDay = () => {
        const selectedDay = daySelect.value || '';
        const selectedDate = requestDateInput.value || '';

        if (!selectedDay || !selectedDate) {
            requestDateInput.setCustomValidity('');
            return true;
        }

        if (!isMatchingSelectedDay(selectedDate, selectedDay)) {
            requestDateInput.setCustomValidity(`Please select a ${selectedDay} date.`);
            requestDateInput.reportValidity();
            return false;
        }

        requestDateInput.setCustomValidity('');
        return true;
    };

    const resetSchedulingForm = () => {
        formElement.reset();
        cellIdInput.value = '';
        timeSlotSelect.value = '';
        daySelect.value = '';
        lectureGroupSelect.value = '';
        requestDateInput.value = getTodayDateValue();
        requestDateInput.min = getTodayDateValue();
        requestDateInput.setCustomValidity('');
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
            lecturerRequestFormContainer.classList.remove('hidden');
            lecturerRequestFormContainer.classList.add('flex');
            return;
        }

        lecturerRequestBtn.classList.add('hidden');
        lecturerRequestFormContainer.classList.add('hidden');
        lecturerRequestFormContainer.classList.remove('flex');
    };

    const openSchedulingForm = () => {
        if (!Boolean(getCurrentUserRole())) return;

        const scheduleMeta = {
            timeSlot: selectedScheduleMeta.timeSlot || getCellScheduleMeta(selectedCellId).timeSlot,
            day: selectedScheduleMeta.day || getCellScheduleMeta(selectedCellId).day,
        };
        resetSchedulingForm();
        cellIdInput.value = selectedCellId;
        setSelectValueSafe(timeSlotSelect, scheduleMeta.timeSlot);
        setSelectValueSafe(daySelect, scheduleMeta.day);
        syncRequestDateWithDay({ forceNextValidDate: true });
        hideSchedulingModal(viewSection);
        showSchedulingModal(formSection);
    };

    tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.timetable-cell-btn');
        if (!btn) return;

        const isLoggedIn = Boolean(getCurrentUserRole());
        const cellIdFromButton = btn.getAttribute('data-cell-id') || '';
        const buttonScheduleMeta = getCellScheduleMetaFromButton(btn);
        const selectedHeading = getActiveColumnHeadings().find((item) => String(item.column_heading) === String(buttonScheduleMeta.day));
        const selectedTimeSlot = getActiveTimeSlots().find((item) => (
            formatTimeSlotLabel(item.start_time, item.end_time) === buttonScheduleMeta.timeSlot
        ));
        const resolvedCell = findTimetableCellByRefs(selectedTimeSlot?.id || '', selectedHeading?.id || '');

        selectedCellId = cellIdFromButton || String(resolvedCell?.id || '');
        selectedScheduleMeta = buttonScheduleMeta;
        populateSchedulingFormView(selectedCellId);
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
    daySelect.addEventListener('change', () => {
        syncRequestDateWithDay({ forceNextValidDate: true });
    });
    requestDateInput.addEventListener('change', () => {
        validateRequestDateForSelectedDay();
    });

    formElement.addEventListener('submit', async (e) => {
        e.preventDefault();

        const storedUser = getStoredUser();
        const lecturerId = storedUser?.id;
        const yearValue = yearSelect.value || '';
        const subjectCodeValue = subjectCodeSelect.value || '';
        const timeSlotValue = timeSlotSelect.value || '';
        const dayValue = daySelect.value || '';
        const lectureGroupValue = lectureGroupSelect.value || '';
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
        const selectedColumnHeading = getActiveColumnHeadings().find(item => (
            String(item.column_heading).toLowerCase() === String(dayValue).toLowerCase()
        ));

        if (!lecturerId) {
            window.alert('Please log in again before sending a lecturer request.');
            return;
        }

        if (!validateRequestDateForSelectedDay()) {
            return;
        }

        if (!selectedYear || !selectedSubject || !selectedTimeSlot || !selectedColumnHeading || !lectureGroupValue || !requestDateValue || !lecturerRequestValue) {
            window.alert('Please fill in year, subject code, time slot, day, group, date, and lecturer request.');
            return;
        }

        try {
            const result = await sendLecturerRequest({
                lecturer_id: lecturerId,
                subject_id: selectedSubject.subject_cord,
                year_id: selectedYear.id,
                lecture_group_id: lectureGroupValue,
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
        const currentWeek = getCurrentWeekDateRange();
        const [timetableData, temporaryDataResponse] = await Promise.all([
            getTimetableData(),
            getTemporaryTimetableData(currentWeek.start, currentWeek.end),
        ]);

        permanentTimetableData = Array.isArray(timetableData.data) ? timetableData.data : [];
        temporaryTimetableData = Array.isArray(temporaryDataResponse.data) ? temporaryDataResponse.data : [];
        fullTimetableData = buildEffectiveTimetableData(permanentTimetableData, temporaryTimetableData);
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

const initTimetablePage = () => {
    if (!document.getElementById('timetable-body')) {
        return;
    }

    loadTimetableData();
    populateSubjectCodeSelects();
    populateYearsSelects();
};

export {
    initTimetablePage,
};




