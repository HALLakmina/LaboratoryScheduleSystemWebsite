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

import { getTimetableData } from '../API/timetableApi.js';



const loadTimetableData = async () => {
    try {
        const timetableData = await getTimetableData();
        const data = timetableData.data;
        const tableRows = 8;
        const tableColumns = 5;
        const tableCall = [
            [1,2,3,4,5],
            [6,7,8,9,10],
            [11,12,13,14,15],
            [16,17,18,19,20],
            [21,22,23,24,25],
            [26,27,28,29,30],
            [31,32,33,34,35],
            [36,37,38,39,40]
        ]
        const time = [
            '8.00/9.00',
            '9.00/10.00',
            '10.00/11.00',
            '11.00/12.00',
            '1.00/2.00',
            '2.00/4.00',
            '3.00/4.00',
            '4.00/5.00'
        ]
        console.log(data);
        const tableBody = document.getElementById('timetable-body');
        for(let i = 0; i < tableRows; i++){
            let tableRow = '';
            if(i === 6){
                tableRow += `
                    <tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">12.00/1.00</td>
                        <td colspan="5" class=""><p class="px-6 py-6 font-bold text-lg w-full h-full hover:bg-gray-400 text-center"> Interval </p></td>
                    </tr>`;
            }
            tableRow += `
                <tr class="odd:bg-white even:bg-gray-200 border-b border-gray-200">`;
            for(let j = 0; j < tableColumns; j++){
                const getCellId = tableCall[i][j];
                // Find the cell data that matches the current cell ID
                const cellData = data.find(item => item.cell_id === getCellId);

                if (j === 0) {
                    // Add the time column only for the first column (leftmost)
                    tableRow += `
                        <td scope="row" class="px-6 py-4 font-medium text-gray-950 font-bold whitespace-nowrap">${time[i]}</td>`;
                }
                
                if (cellData) {
                    tableRow += `
                        <td class=""><button class="px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300"> ${cellData.Subject_cord || ''} </button></td>`;
                } else {
                    tableRow += `
                        <td class=""><button class="px-6 py-4 w-full h-full hover:bg-gray-400 text-left active:bg-blue-300">  </button></td>`;
                }
            }
            tableRow += `</tr>`;
            tableBody.innerHTML += tableRow;
        }
    } catch (error) {
        console.error('Error fetching timetable data:', error);
    }
}

loadTimetableData();