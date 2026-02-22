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

/**
 * Updates filter_by_subject dropdown based on selected year
 * Shows only subjects for the selected year, or all subjects if no year selected
 * @param {string} year - Selected year value (empty string shows all subjects)
 */
const updateSubjectFilterByYear = (year) => {
    const subjectSelect = document.getElementById('filter_by_subject');
    if (!subjectSelect) return;

    const defaultOption = '--FILTER--';
    subjectSelect.innerHTML = `<option class="text-center font-bold" value="">${defaultOption}</option>`;

    const subjectsToShow = year
        ? fullSubjectCodesData.filter(item => String(item.year) === String(year))
        : fullSubjectCodesData;

    subjectsToShow.forEach(item => {
        const option = document.createElement('option');
        option.value = item.subject_cord || '';
        option.textContent = item.subject_cord + (item.subject ? ` - ${item.subject}` : '');
        subjectSelect.appendChild(option);
    });

    subjectSelect.value = '';
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
                ? `<td class=""><button class="px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300"> ${cellData.subject_cord || ''} </button></td>`
                : `<td class=""><button class="px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300">  </button></td>`;
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
    } catch (error) {
        console.error('Error fetching timetable data:', error);
    }
};



loadTimetableData();
populateSubjectCodeSelects();
populateYearsSelects();