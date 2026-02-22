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
            </section>
            <section class="w-screen overflow-x-scroll p-2" style="scrollbar-width: none;">
                <table class="w-full bg-white shadow-md rounded-lg text-sm text-left rtl:text-right">
                    <thead class="text-xs text-white uppercase bg-gray-900 ">
                        <tr>
                            <th scope="col" class="px-6 py-3">Time</th>
                            <th scope="col" class="px-6 py-3">Monday</th>
                            <th scope="col" class="px-6 py-3">Tuesday</th>
                            <th scope="col" class="px-6 py-3">Wednesday</th>
                            <th scope="col" class="px-6 py-3">Thursday</th>
                            <th scope="col" class="px-6 py-3">Friday</th>
                        </tr>
                    </thead>
                    <tbody id="timetable-body">
                    </tbody>
                </table>
            </section>
            <section class="hidden absolute top-0 bottom-0 left-0 right-0 bg-gray-950/50 flex flex-col justify-center items-center">
                <form class=" max-w-xs md:max-w-md w-full p-2 bg-white rounded-lg flex flex-col items-left justify-center">
                    <button class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95">X</button>
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
            <section class="hidden absolute top-0 bottom-0 left-0 right-0 bg-gray-950/50 flex flex-col justify-center items-center">
                <div class=" max-w-xs md:max-w-md w-full p-2 bg-white rounded-lg flex flex-col items-left justify-center">
                    <button class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95">X</button>
                    <div class="my-4 p-2 bg-purple-800 text-white text-center">
                        <p class="font-bold">SCHEDULING</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Subject Name</p>
                        <p class="pl-4">Fundamental of ICT</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Subject Code</p>
                        <p class="pl-4">TICT 1114</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Lecture In Charge</p>
                        <p class="pl-4">Mis. Rukshani</p>
                    </div>
                    <div class="pb-4">
                        <p class="font-bold">Lecturer's</p>
                        <p class="pl-4">Miss. Prunthavi</p>
                    </div>
                    <button class="bg-blue-500 p-2 rounded-sm font-bold text-white active:scale-95 self-end text-sm">LECTURE REQUEST</button>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../Components/FooterBar.php';?>
    </body>
    <script type="module" src="../API/timetableApi.js"></script>
    <script type="module" src="../JS/main.js"></script>
</html>