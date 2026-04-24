<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <title>Laboratory Scheduling System Timetable</title>
    </head>
    <body class="min-w-[320px] min-h-svh overflow-auto bg-[url('../resources/img/Wallpaper.jpg')] bg-cover bg-center bg-no-repeat backdrop-blur-xs" id="index-content">
        <?php include __DIR__ . '/../Components/NavigationBar.php';?>
        <main class="mx-auto flex w-full flex-col gap-4 px-2 pb-6 pt-4 sm:px-4">

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                <form class="rounded-2xl bg-white/95 p-3 shadow-lg ring-1 ring-gray-200/80">
                    <div class="flex flex-col gap-2">
                        <label for="filter_by_years" class="text-xs font-black uppercase tracking-[0.28em] text-gray-500">Filter Year</label>
                        <select
                            name="filter"
                            id="filter_by_years"
                            class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option class="text-center font-bold">--FILTER--</option>
                        </select>
                    </div>
                </form>
                <form class="rounded-2xl bg-white/95 p-3 shadow-lg ring-1 ring-gray-200/80">
                    <div class="flex flex-col gap-2">
                        <label for="filter_by_subject" class="text-xs font-black uppercase tracking-[0.28em] text-gray-500">Filter Subject</label>
                        <select
                            name="filter"
                            id="filter_by_subject"
                            class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option class="text-center font-bold">--FILTER--</option>
                        </select>
                    </div>
                </form>
                <div id="lecturer-request-form-container" class="hidden rounded-2xl bg-white/95 p-3 shadow-lg ring-1 ring-gray-200/80 items-center">
                    <button
                        type="button"
                        id="lecturer-request-form"
                        class="w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-black uppercase tracking-wide text-white shadow-md transition hover:bg-sky-700 active:scale-95 xl:min-w-[220px]"
                    >
                        Lecture Request
                    </button>
                </div>
            </section>

            <section class="overflow-hidden rounded-lg bg-white/92 shadow-2xl ring-1 ring-white/70">
                <div class="max-h-[calc(100svh-15rem)] w-full overflow-x-auto overflow-y-auto" style="scrollbar-width: thin;">
                    <table id="timetable" class="w-full min-w-[860px] border-separate border-spacing-0 text-sm text-left rtl:text-right">
                        <thead id="timetable-head" class="sticky top-0 z-20"></thead>
                        <tbody id="timetable-body"></tbody>
                    </table>
                </div>
            </section>

            <section id="scheduling-form" class="hidden fixed inset-0 z-30 overflow-y-auto bg-gray-950/50 p-4">
                <form class="mx-auto my-8 flex w-full max-w-xs flex-col gap-4 rounded-3xl bg-white p-4 shadow-2xl sm:max-w-md sm:p-5">
                    <input type="hidden" name="cell_id" id="cell_id" value="" />
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.28em] text-gray-500">Request Lecture</p>
                            <h2 class="pt-1 text-2xl font-black text-gray-950">Scheduling Form</h2>
                        </div>
                        <button type="button" id="scheduling-form-close" class="self-start w-8 rounded-lg bg-red-500 p-1 font-bold text-white active:scale-95" aria-label="Close">X</button>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="years" class="text-sm font-black uppercase tracking-wide text-gray-600">Year</label>
                        <select
                            name="years"
                            id="years"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option>--</option>
                            <option>1st Year</option>
                            <option>2nd Year</option>
                            <option>3rd Year</option>
                            <option>4th Year</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="subject_code" class="text-sm font-black uppercase tracking-wide text-gray-600">Subject Code</label>
                        <select
                            name="subject_code"
                            id="subject_code"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option>--</option>
                            <option>TICT 1114</option>
                            <option>TICT 2212</option>
                            <option>TICT 2121</option>
                            <option>TICT 3134</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="time_slot" class="text-sm font-black uppercase tracking-wide text-gray-600">Time Slot</label>
                        <select
                            name="time_slot"
                            id="time_slot"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
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
                    <div class="flex flex-col gap-2">
                        <label for="day" class="text-sm font-black uppercase tracking-wide text-gray-600">Day</label>
                        <select
                            name="day"
                            id="day"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option value="">--</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="lecture_group_select" class="text-sm font-black uppercase tracking-wide text-gray-600">Group</label>
                        <select
                            name="lecture_group_select"
                            id="lecture_group_select"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        >
                            <option value="">--</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="request_date" class="text-sm font-black uppercase tracking-wide text-gray-600">Date</label>
                        <input
                            type="date"
                            name="request_date"
                            id="request_date"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="request" class="text-sm font-black uppercase tracking-wide text-gray-600">Lecture Request</label>
                        <textarea
                            name="request"
                            id="request"
                            placeholder="Type your request..."
                            rows="5"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white"
                        ></textarea>
                    </div>
                    <button class="mt-2 w-full rounded-2xl bg-sky-600 px-4 py-3 text-sm font-black uppercase tracking-wide text-white shadow-md transition hover:bg-sky-700 active:scale-95">Submit</button>
                </form>
            </section>

            <section id="scheduling-form-view" class="hidden fixed inset-0 z-30 overflow-y-auto bg-gray-950/50 p-4">
                <div class="mx-auto my-8 flex w-full max-w-xs flex-col rounded-3xl bg-white p-4 shadow-2xl sm:max-w-md sm:p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.28em] text-gray-500">Lecture Details</p>
                            <h2 class="pt-1 text-2xl font-black text-gray-950">Time Slot Overview</h2>
                        </div>
                        <button type="button" id="scheduling-form-view-close" class="self-start w-8 rounded-lg bg-red-500 p-1 font-bold text-white active:scale-95">X</button>
                    </div>
                    <div id="lecture-action-badge" class="my-4 rounded-2xl bg-gray-700 p-3 text-center text-white">
                        <p id="lecture-action" class="font-bold">SCHEDULING</p>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Subject Name</p>
                            <p id="subject-name" class="pt-1 font-bold text-gray-950">Fundamental of ICT</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Subject Code</p>
                            <p id="subject-code" class="pt-1 font-bold text-gray-950">TICT 1114</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Lecture In Charge</p>
                            <p id="lecture-in-charge" class="pt-1 font-bold text-gray-950">Mis. Rukshani</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Lecturer's</p>
                            <p id="lecture" class="pt-1 font-bold text-gray-950">Miss. Prunthavi</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Group</p>
                            <p id="lecture-group" class="pt-1 font-bold text-gray-950">Group 02</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3">
                            <p class="text-xs font-black uppercase tracking-wide text-gray-500">Lab</p>
                            <p id="lab" class="pt-1 font-bold text-gray-950">-</p>
                        </div>
                    </div>
                    <button type="button" id="lecturer-request" class="hidden mt-5 self-end rounded-xl bg-sky-600 px-4 py-3 text-sm font-black uppercase tracking-wide text-white shadow-md active:scale-95">Lecture Request</button>
                </div>
            </section>

            <section id="lab-allocation-modal" class="hidden fixed inset-0 z-30 overflow-y-auto bg-gray-950/50 p-4">
                <div class="mx-auto my-8 w-full max-w-md rounded-3xl bg-white p-4 shadow-2xl sm:p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.25em] text-gray-500">Lab Allocation</p>
                            <h2 id="lab-allocation-title" class="pt-1 text-xl font-black text-gray-950">Sunday � 8.00/9.00</h2>
                            <p id="lab-allocation-summary" class="pt-2 text-sm font-bold text-gray-600">0 Lectures � 0/0 Labs Used</p>
                        </div>
                        <button type="button" id="lab-allocation-close" class="self-start w-8 rounded-lg bg-red-500 p-1 font-bold text-white active:scale-95">X</button>
                    </div>
                    <div id="lab-allocation-overflow" class="hidden mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-black text-red-700"></div>
                    <div id="lab-allocation-list" class="mt-4 flex flex-col gap-3"></div>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../Components/FooterBar.php';?>
    </body>
    <script type="module" src="../API/timetableApi.js"></script>
    <script type="module" src="../JS/main.js"></script>
</html>
