<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <!-- <script type="module" src="./Js/main.js"></script>
        <script type="module" src="./Js/pageRouting.js"></script> -->
        <!-- <script type="module" src="./Js/customElements.js"></script> -->
        <title>Laboratory Scheduling System Timetable</title>
    </head>
    <body class="w-full bg-[url('../resources/img/Wallpaper.jpg')] bg-cover bg-center w-full bg-no-repeat backdrop-blur-xs h-svh overflow-auto"  id="index-content">
        <?php include __DIR__ . '/../Components/NavigationBar.php';?>
        <main class="w- full flex flex-col justify-center items-center p-2 pt-4">
            <section class="w-full flex flex-row">
                <form class="max-w-80 min-w-80 bg-white p-2 rounded-lg m-2">
                    <div class="flex flex-col items-left justify-center">
                        <select
                            name="filter" 
                            id="filter_by_years" 
                            class="bg-gray-200 w-full rounded-sm"
                        >
                            <option class="text-center font-bold">--FILTER--</option>
                        </select>
                    </div>
                </form>
                <form class="max-w-80 min-w-80 bg-white p-2 rounded-lg m-2">
                    <div class="flex flex-col items-left justify-center">
                        <select
                            name="filter" 
                            id="filter_by_subject" 
                            class="bg-gray-200 w-full rounded-sm"
                        >
                            <option class="text-center font-bold">--FILTER--</option>
                        </select>
                    </div>
                </form>
                <div class="bg-white p-2 rounded-lg m-2 flex items-center">
                    <button
                        type="button"
                        id="lecturer-request-form"
                        class="hidden bg-blue-500 p-2 rounded-sm font-bold text-white active:scale-95 text-sm"
                    >
                        LECTURE REQUEST
                    </button>
                </div>
            </section>
            <section class="w-screen overflow-x-scroll p-2" style="scrollbar-width: none;">
                <table id="timetable" class="w-full bg-white shadow-md rounded-lg text-sm text-left rtl:text-right">
                    <thead id="timetable-head" class="text-xs text-white uppercase bg-gray-900 "></thead>
                    <tbody id="timetable-body">
                    </tbody>
                </table>
            </section>
            <section id="scheduling-form" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
                <form class=" max-w-xs md:max-w-md w-full p-2 bg-white rounded-lg flex flex-col items-left justify-center my-8 mx-auto">
                    <input type="hidden" name="cell_id" id="cell_id" value="" />
                    <button type="button" id="scheduling-form-close" class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                    <div class="flex flex-col items-left justify-center">
                        <label for="years" class="mb-2 text-lg font-bold">Year's</label>
                        <select
                            name="years" 
                            id="years" 
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        >
                            <option>--</option>
                            <option>1st Year</option>
                            <option>2nd Year</option>
                            <option>3rd Year</option>
                            <option>4th Year</option>
                        </select>
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="subject_code" class="mb-2 text-lg font-bold">Subject Code</label>
                        <select
                            name="subject_code" 
                            id="subject_code" 
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        >
                            <option>--</option>
                            <option>TICT 1114</option>
                            <option>TICT 2212</option>
                            <option>TICT 2121</option>
                            <option>TICT 3134</option>
                        </select>
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="time_slot" class="mb-2 text-lg font-bold">Time Slot</label>
                        <select
                            name="time_slot"
                            id="time_slot"
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        >
                            <option value="">--</option>
                            <option value="8.00/9.00">8.00/9.00</option>
                            <option value="9.00/10.00">9.00/10.00</option>
                            <option value="10.00/11.00">10.00/11.00</option>
                            <option value="11.00/12.00">11.00/12.00</option>
                            <option value="1.00/2.00">1.00/2.00</option>
                            <option value="2.00/4.00">2.00/4.00</option>
                            <option value="3.00/4.00">3.00/4.00</option>
                            <option value="4.00/5.00">4.00/5.00</option>
                        </select>
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="day" class="mb-2 text-lg font-bold">Day</label>
                        <select
                            name="day"
                            id="day"
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        >
                            <option value="">--</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="lecture_group_select" class="mb-2 text-lg font-bold">Group</label>
                        <select
                            name="lecture_group_select"
                            id="lecture_group_select"
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        >
                            <option value="">--</option>
                        </select>
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="request_date" class="mb-2 text-lg font-bold">Date</label>
                        <input
                            type="date"
                            name="request_date"
                            id="request_date"
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        />
                    </div>
                    <div class="flex flex-col items-left justify-center">
                        <label for="request" class="mb-2 text-lg font-bold">Lecture Request</label>
                        <textarea 
                            name="request" 
                            id="request" 
                            placeholder="type your request...."
                            rows="5"
                            class="bg-gray-200 w-full p-2 mb-2 rounded-sm"
                        ></textarea>
                    </div>
                    <button class="bg-blue-500 p-2 rounded-lg w-40 font-bold text-white active:scale-95 self-center">SUBMIT</button>
                </form>
            </section>
            <section id="scheduling-form-view" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
                <div class=" max-w-xs md:max-w-md w-full p-2 bg-white rounded-lg flex flex-col items-left justify-center my-8 mx-auto">
                    <button type="button" id="scheduling-form-view-close" class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95">X</button>
                    <div id="lecture-action-badge" class="my-4 p-2 bg-gray-700 text-white text-center">
                        <p id="lecture-action" class="font-bold">SCHEDULING</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Subject Name</p>
                        <p id="subject-name" class="pl-4">Fundamental of ICT</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Subject Code</p>
                        <p id="subject-code" class="pl-4">TICT 1114</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Lecture In Charge</p>
                        <p id="lecture-in-charge" class="pl-4">Mis. Rukshani</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Lecturer's</p>
                        <p id="lecture" class="pl-4">Miss. Prunthavi</p>
                    </div>
                    <div class="pb-4">
                        <p  class="font-bold">Group</p>
                        <p id="lecture-group" class="pl-4">Group 02</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Lab</p>
                        <p id="lab" class="pl-4">-</p>
                    </div>
                    <button type="button" id="lecturer-request" class="hidden bg-blue-500 p-2 rounded-sm font-bold text-white active:scale-95 self-end text-sm">LECTURE REQUEST</button>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../Components/FooterBar.php';?>
    </body>
    <script type="module" src="../API/timetableApi.js"></script>
    <script type="module" src="../JS/main.js"></script>
</html>
