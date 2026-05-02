import { getTimetableData, getTemporaryTimetableData, getSubjectCodes, getYears, getTimeSlots, getColumnHeadings, getTimetableSettings, getLectureGroups, getTimetableCells, getLabs } from '../API/timetableApi.js';
import { sendLecturerRequest } from '../API/lecturerRequestApi.js';
import { getCurrentUserRole, getStoredUser } from './loginUser.js';
import { bindAsyncFormSubmit } from './utils.js';

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
        const [timeSlotsResponse, columnHeadingsResponse, settingsResponse, lectureGroupsResponse, timetableCellsResponse, labsResponse] = await Promise.all([
            getTimeSlots(),
            getColumnHeadings(),
            getTimetableSettings(),
            getLectureGroups(),
            getTimetableCells(),
            getLabs(),
        ]);

        fullTimeSlotsData = timeSlotsResponse.status === '200' && timeSlotsResponse.data ? timeSlotsResponse.data : [];
        fullColumnHeadingsData = columnHeadingsResponse.status === '200' && columnHeadingsResponse.data ? columnHeadingsResponse.data : [];
        fullTimetableSettingsData = settingsResponse.status === '200' ? settingsResponse.data : null;
        fullLectureGroupsData = lectureGroupsResponse.status === '200' && lectureGroupsResponse.data ? lectureGroupsResponse.data : [];
        fullTimetableCellsData = timetableCellsResponse.status === '200' && timetableCellsResponse.data ? timetableCellsResponse.data : [];
        fullLabsData = labsResponse.status === '200' && labsResponse.data ? labsResponse.data : [];

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
            <th scope="col" class="sticky left-0 z-30 min-w-[96px] bg-gray-950 px-4 py-4 text-left text-[11px] font-black uppercase tracking-[0.28em] text-sky-100 shadow-[4px_0_12px_rgba(15,23,42,0.18)]">Time</th>
            ${activeColumnHeadings.map(item => `<th scope="col" class="min-w-[160px] bg-gray-950 px-4 py-4 text-left text-[11px] font-black uppercase tracking-[0.28em] text-sky-100">${item.column_heading}</th>`).join('')}
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
let fullLabsData = [];
let currentDisplayedTimetableData = [];

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
    const normalizeRecord = (item, source) => ({
        ...item,
        action: item.action || (item.subject_cord ? 'active' : 'free'),
        data_source: source,
        unique_record_id: source === 'temporary'
            ? `temporary:${item.temporary_timetable_id || `${item.time_slot_id || ''}:${item.column_heading_id || ''}:${item.subject_cord || ''}:${item.lecture_group_id || ''}:${item.lab_id || ''}`}`
            : `permanent:${item.timetable_id || `${item.time_slot_id || ''}:${item.column_heading_id || ''}:${item.subject_cord || ''}:${item.lecture_group_id || ''}:${item.lab_id || ''}`}`,
    });

    return [
        ...(permanentData || []).map((item) => normalizeRecord(item, 'permanent')),
        ...(temporaryData || []).map((item) => normalizeRecord(item, 'temporary')),
    ].sort((left, right) => {
        const leftTimeSlotId = Number(left.time_slot_id || 0);
        const rightTimeSlotId = Number(right.time_slot_id || 0);
        if (leftTimeSlotId !== rightTimeSlotId) {
            return leftTimeSlotId - rightTimeSlotId;
        }

        const leftColumnHeadingId = Number(left.column_heading_id || 0);
        const rightColumnHeadingId = Number(right.column_heading_id || 0);
        if (leftColumnHeadingId !== rightColumnHeadingId) {
            return leftColumnHeadingId - rightColumnHeadingId;
        }

        return String(left.unique_record_id || '').localeCompare(String(right.unique_record_id || ''));
    });
};

const buildSlotGroups = (records = []) => {
    const groupedRecords = new Map();

    records.forEach((record) => {
        const slotKey = `${record?.time_slot_id || ''}:${record?.column_heading_id || ''}`;
        const existingSlot = groupedRecords.get(slotKey) || {
            time_slot_id: record?.time_slot_id || '',
            column_heading_id: record?.column_heading_id || '',
            records: [],
        };

        existingSlot.records.push(record);
        groupedRecords.set(slotKey, existingSlot);
    });

    return groupedRecords;
};

const buildLabAllocationEntries = (slotGroup) => {
    const labs = Array.isArray(fullLabsData) ? fullLabsData : [];
    const records = Array.isArray(slotGroup?.records) ? slotGroup.records : [];
    const usedRecords = records.slice(0, labs.length);
    const overflowRecords = records.slice(labs.length);

    const labEntries = labs.map((lab, index) => {
        const record = usedRecords[index] || null;
        return {
            lab,
            record,
            action: record?.action || 'free',
            lectureId: record?.subject_cord || '',
            isFree: !record,
        };
    });

    return {
        slotKey: `${slotGroup?.time_slot_id || ''}:${slotGroup?.column_heading_id || ''}`,
        time_slot_id: slotGroup?.time_slot_id || '',
        column_heading_id: slotGroup?.column_heading_id || '',
        records,
        labs: labEntries,
        labCount: labs.length,
        lectureCount: records.length,
        usedLabCount: usedRecords.length,
        overflowCount: overflowRecords.length,
        overflowRecords,
    };
};

/**
 * Renders the timetable table with the given data
 * @param {Array} data - Filtered timetable data
 */
const renderTimetableTable = (data) => {
    const tableBody = document.getElementById('timetable-body');
    if (!tableBody) return;
    currentDisplayedTimetableData = Array.isArray(data) ? data : [];

    const activeTimeSlots = getActiveTimeSlots();
    const activeColumnHeadings = getActiveColumnHeadings();
    const columnCount = activeColumnHeadings.length;
    const breakRowNumber = Number(fullTimetableSettingsData?.break_row_number || 0);
    const breakTimeSlot = breakRowNumber ? fullTimeSlotsData[breakRowNumber - 1] : null;
    const breakLabel = breakTimeSlot ? formatTimeSlotLabel(breakTimeSlot.start_time, breakTimeSlot.end_time) : 'Interval';
    const slotGroups = buildSlotGroups(data);
    const totalLabCount = fullLabsData.length;

    tableBody.innerHTML = '';
    activeTimeSlots.forEach((timeSlot, rowIndex) => {
        let tableRow = '';
        if (breakRowNumber && rowIndex === breakRowNumber - 1) {
            tableRow += `
                <tr class="border-b border-gray-200">
                    <td scope="row" class="sticky left-0 z-10 bg-white px-4 py-4 text-sm font-black text-gray-950 shadow-[4px_0_12px_rgba(148,163,184,0.15)] whitespace-nowrap">${breakLabel}</td>
                    <td colspan="${columnCount}" class="bg-white">
                        <p class="px-4 py-6 text-center text-base font-black uppercase tracking-[0.2em] text-gray-500">Interval</p>
                    </td>
                </tr>`;
        }
        tableRow += `<tr class="border-b border-gray-200 odd:bg-white even:bg-slate-50/90">`;
        activeColumnHeadings.forEach((heading, columnIndex) => {
            const linkedCell = findTimetableCellByRefs(timeSlot.id, heading.id);
            const cellId = linkedCell?.id || '';
            const slotKey = `${timeSlot.id}:${heading.id}`;
            const slotGroup = slotGroups.get(slotKey) || {
                time_slot_id: timeSlot.id,
                column_heading_id: heading.id,
                records: [],
            };
            const lectureCount = slotGroup.records.length;
            const usedLabs = Math.min(lectureCount, totalLabCount);
            const overflowCount = Math.max(lectureCount - totalLabCount, 0);
            const summaryText = lectureCount > 0 ? `${lectureCount} Lecture${lectureCount > 1 ? 's' : ''}` : 'No Lectures';
            const usageText = totalLabCount > 0 ? `${usedLabs}/${totalLabCount} Labs Used` : 'No Labs';
            const lectureCodesMarkup = lectureCount > 0
                ? `<div class="flex flex-wrap gap-2">
                        ${slotGroup.records.map((record) => `
                            <span class="rounded-full bg-gray-950 px-2.5 py-1 text-[11px] font-black uppercase tracking-wide text-white">
                                ${record?.subject_cord || '-'}
                            </span>
                        `).join('')}
                    </div>`
                : `<p class="text-xs font-bold text-gray-500">No lecture codes</p>`;
            const overflowMarkup = overflowCount > 0
                ? `<p class="text-[11px] font-black uppercase tracking-wide text-red-600">${overflowCount} Overflow</p>`
                : `<p class="text-[11px] font-black uppercase tracking-wide ${lectureCount > 0 ? 'text-emerald-700' : 'text-gray-500'}">${lectureCount > 0 ? 'Available' : 'Free Slot'}</p>`;

            if (columnIndex === 0) {
                tableRow += `<td scope="row" class="sticky left-0 z-10 min-w-[96px] whitespace-nowrap bg-inherit px-4 py-4 text-sm font-black text-gray-950 shadow-[4px_0_12px_rgba(148,163,184,0.15)]">${formatTimeSlotLabel(timeSlot.start_time, timeSlot.end_time)}</td>`;
            }
            tableRow += `
                <td class="min-w-[160px] align-top p-0">
                    <button
                        type="button"
                        class="timetable-cell-btn flex min-h-[112px] w-full flex-col items-start gap-3 border-l border-t border-white/70 px-4 py-4 text-left transition active:scale-[0.98] ${overflowCount > 0 ? 'bg-red-50 hover:bg-red-100' : lectureCount > 0 ? 'bg-white hover:bg-sky-50' : 'bg-transparent hover:bg-gray-100'}"
                        data-cell-id="${cellId}"
                        data-time-slot-id="${timeSlot.id}"
                        data-column-heading-id="${heading.id}"
                    >
                        <div class="flex w-full flex-col gap-3">
                            ${lectureCodesMarkup}
                        </div>
                        <div class="mt-auto flex w-full flex-col gap-1">
                            <p class="text-sm font-black text-gray-950">${summaryText}</p>
                            <p class="text-xs font-bold text-gray-600">${usageText}</p>
                        </div>
                        ${overflowMarkup}
                    </button>
                </td>`;
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
    const labAllocationModal = document.getElementById('lab-allocation-modal');
    const labAllocationCloseBtn = document.getElementById('lab-allocation-close');
    const labAllocationTitle = document.getElementById('lab-allocation-title');
    const labAllocationSummary = document.getElementById('lab-allocation-summary');
    const labAllocationOverflow = document.getElementById('lab-allocation-overflow');
    const labAllocationList = document.getElementById('lab-allocation-list');
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

    if (!formSection || !viewSection || !labAllocationModal || !labAllocationCloseBtn || !labAllocationTitle || !labAllocationSummary || !labAllocationOverflow || !labAllocationList || !formElement || !cellIdInput || !yearSelect || !subjectCodeSelect || !timeSlotSelect || !daySelect || !lectureGroupSelect || !requestDateInput || !requestTextarea || !tableBody || !lecturerRequestBtn || !lecturerRequestFormBtn || !lecturerRequestFormContainer) return;

    let selectedCellId = '';
    let selectedScheduleMeta = {
        timeSlotId: '',
        columnHeadingId: '',
        timeSlot: '',
        day: '',
        cellId: '',
    };
    let selectedViewRecord = null;

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
        if (normalized === 'temporary_lecture') {
            badge.classList.add('bg-amber-600');
            return;
        }
        badge.classList.add('bg-gray-700');
    };

    const getActionLabel = (record) => {
        const normalizedAction = String(record?.action || 'free').toLowerCase();
        if (normalizedAction === 'temporary_lecture') {
            return 'TEMPORARY LECTURE';
        }

        return String(record?.action || 'free').toUpperCase();
    };

    const populateSchedulingFormView = (viewRecord = null) => {
        const record = viewRecord || selectedViewRecord || null;
        const action = record?.action || 'free';

        setViewText('lecture-action', getActionLabel(record));
        setLectureActionBadge(action);
        setViewText('subject-name', record?.subject || '');
        setViewText('subject-code', record?.subject_cord || record?.lectureId || '');
        setViewText('lecture-in-charge', record?.lecturer_name || '');
        setViewText('lecture', record?.year || '');
        setViewText('lecture-group', record?.group_name || '');
        setViewText('lab', record?.lab || record?.lab_name || '');
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

        if (!selectedDay) {
            if (forceNextValidDate) {
                requestDateInput.value = todayValue;
            }
            return;
        }

        const nextValidDate = getNextDateForDay(selectedDay);

        if (forceNextValidDate || !requestDateInput.value || requestDateInput.value < todayValue || !isMatchingSelectedDay(requestDateInput.value, selectedDay)) {
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
        cellIdInput.value = selectedScheduleMeta.cellId || selectedCellId;
        setSelectValueSafe(timeSlotSelect, scheduleMeta.timeSlot);
        setSelectValueSafe(daySelect, scheduleMeta.day);
        syncRequestDateWithDay({ forceNextValidDate: true });
        hideSchedulingModal(viewSection);
        showSchedulingModal(formSection);
    };

    const openSchedulingFormView = (viewRecord) => {
        selectedViewRecord = viewRecord;
        populateSchedulingFormView(viewRecord);
        syncLecturerRequestButtons(Boolean(getCurrentUserRole()));
        hideSchedulingModal(labAllocationModal);
        hideSchedulingModal(formSection);
        showSchedulingModal(viewSection);
    };

    const renderLabAllocationModal = (slotAllocation) => {
        if (!slotAllocation) return;

        labAllocationTitle.textContent = `${selectedScheduleMeta.day || '-'} · ${selectedScheduleMeta.timeSlot || '-'}`;
        labAllocationSummary.textContent = `${slotAllocation.lectureCount} Lecture${slotAllocation.lectureCount !== 1 ? 's' : ''} · ${slotAllocation.usedLabCount}/${slotAllocation.labCount} Labs Used`;

        if (slotAllocation.overflowCount > 0) {
            labAllocationOverflow.classList.remove('hidden');
            labAllocationOverflow.textContent = `${slotAllocation.overflowCount} lecture${slotAllocation.overflowCount !== 1 ? 's are' : ' is'} waiting because this slot has more lectures than available labs.`;
        } else {
            labAllocationOverflow.classList.add('hidden');
            labAllocationOverflow.textContent = '';
        }

        labAllocationList.innerHTML = slotAllocation.labs.length
            ? slotAllocation.labs.map((entry) => {
                const badgeClass = entry.action === 'free'
                    ? 'bg-green-700'
                    : entry.action === 'cancel'
                        ? 'bg-red-700'
                        : entry.action === 'temporary_lecture'
                            ? 'bg-amber-600'
                            : 'bg-purple-800';
                const label = entry.record ? getActionLabel(entry.record) : 'FREE';
                return `
                    <button
                        type="button"
                        class="lab-allocation-item w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-4 text-left shadow-sm transition hover:bg-sky-50 active:scale-[0.99]"
                        data-lab-id="${entry.lab?.id || ''}"
                        data-record-id="${entry.record?.unique_record_id || ''}"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-wide text-gray-500">${entry.lab?.lab_name || 'Lab'}</p>
                                <p class="pt-1 text-base font-black text-gray-950">${entry.lectureId || '-'}</p>
                            </div>
                            <span class="${badgeClass} rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-wide text-white">${label}</span>
                        </div>
                    </button>
                `;
            }).join('')
            : `<div class="rounded-lg bg-gray-100 px-4 py-6 text-center font-bold text-gray-500">No labs available.</div>`;
    };

    tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.timetable-cell-btn');
        if (!btn) return;

        const timeSlotId = btn.getAttribute('data-time-slot-id') || '';
        const columnHeadingId = btn.getAttribute('data-column-heading-id') || '';
        const buttonScheduleMeta = getCellScheduleMetaFromButton(btn);
        const resolvedCell = findTimetableCellByRefs(timeSlotId, columnHeadingId);
        const slotGroup = buildSlotGroups(currentDisplayedTimetableData).get(`${timeSlotId}:${columnHeadingId}`) || {
            time_slot_id: timeSlotId,
            column_heading_id: columnHeadingId,
            records: [],
        };

        selectedCellId = String(resolvedCell?.id || btn.getAttribute('data-cell-id') || '');
        selectedScheduleMeta = {
            ...buttonScheduleMeta,
            timeSlotId,
            columnHeadingId,
            cellId: selectedCellId,
        };
        selectedViewRecord = null;
        renderLabAllocationModal(buildLabAllocationEntries(slotGroup));
        hideSchedulingModal(formSection);
        hideSchedulingModal(viewSection);
        showSchedulingModal(labAllocationModal);
    });

    labAllocationList.addEventListener('click', (e) => {
        const labButton = e.target.closest('.lab-allocation-item');
        if (!labButton) return;

        const selectedLab = fullLabsData.find((item) => String(item.id) === String(labButton.getAttribute('data-lab-id') || '')) || null;
        const selectedRecordId = labButton.getAttribute('data-record-id') || '';
        const matchedRecord = currentDisplayedTimetableData.find((item) => String(item.unique_record_id || '') === String(selectedRecordId)) || null;

        const viewRecord = matchedRecord
            ? {
                ...matchedRecord,
                lab: selectedLab?.lab_name || matchedRecord.lab || '',
                lab_name: selectedLab?.lab_name || matchedRecord.lab || '',
            }
            : {
                action: 'free',
                subject: '',
                subject_cord: '',
                lecturer_name: '',
                year: '',
                group_name: '',
                lab: selectedLab?.lab_name || '',
                lab_name: selectedLab?.lab_name || '',
            };

        openSchedulingFormView(viewRecord);
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

    labAllocationCloseBtn.addEventListener('click', () => {
        hideSchedulingModal(labAllocationModal);
    });

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

    bindAsyncFormSubmit(formElement, async (e) => {
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
    }, { busyLabel: 'Sending Request...' });
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




