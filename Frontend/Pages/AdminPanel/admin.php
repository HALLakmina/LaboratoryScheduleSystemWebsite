<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <title>Laboratory Scheduling System Admin Panel</title>
    </head>
    <body class="w-full bg-[url('../../resources/img/Wallpaper.jpg')] bg-cover bg-center bg-no-repeat backdrop-blur-xs min-h-svh overflow-auto" id="index-content">
        <?php include __DIR__ . '/../../Components/NavigationBar.php';?>
        <main id="admin-panel" class="w-full p-4 md:p-6">
            <section class="w-full">
                <aside id="admin-side-nav" class="fixed left-0 top-18 bottom-0 z-10 w-[320px] -translate-x-[70%] overflow-y-auto bg-transparent p-4 shadow-none transition-all duration-300 xl:translate-x-0 xl:bg-white/95 xl:shadow-2xl">
                    <div class="relative flex items-center justify-between gap-3 pt-4">
                        <p id="admin-side-nav-label" class="text-sm font-black uppercase tracking-[0.25em] text-gray-500 opacity-0 xl:opacity-100 transition-opacity duration-300">Navigation</p>
                        <button id="admin-nav-toggle" type="button" class=" absolute right-0 ml-auto rounded-lg bg-gray-950 px-4 py-3 text-sm font-black text-white shadow-lg pointer-events-auto hover:bg-sky-700 xl:hidden">
                            MENU
                        </button>
                    </div>
                    <nav id="admin-side-nav-menu" class="pt-4 flex flex-col gap-2 text-sm font-bold opacity-0 pointer-events-none xl:opacity-100 xl:pointer-events-auto transition-opacity duration-300">
                        <button type="button" data-admin-target="admin-overview-section" class="admin-nav-btn bg-gray-950 text-white hover:bg-sky-700 rounded-lg px-4 py-3 text-left">Overview</button>
                        <button type="button" data-admin-target="admin-timetable-settings" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Timetable Settings</button>
                        <button type="button" data-admin-target="admin-manage-timetable" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Manage Timetable</button>
                        <button type="button" data-admin-target="admin-requests" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Incoming Requests</button>
                        <button type="button" data-admin-target="admin-news" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">News</button>
                        <button type="button" data-admin-target="admin-years" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Years</button>
                        <button type="button" data-admin-target="admin-groups" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Groups</button>
                        <button type="button" data-admin-target="admin-labs" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Labs</button>
                        <button type="button" data-admin-target="admin-subjects" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Subjects</button>
                        <button type="button" data-admin-target="admin-users" class="admin-nav-btn bg-gray-100 hover:bg-sky-100 rounded-lg px-4 py-3 text-left">Users</button>
                    </nav>
                </aside>

                <div class="flex flex-col gap-6 xl:ml-[344px]">
                    <section id="admin-overview-section" data-admin-section class="bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="w-full rounded-lg bg-gray-950/90 text-white p-6 shadow-xl mb-6">
                            <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Admin Workspace</p>
                            <h1 class="text-3xl md:text-5xl font-black pt-2">Laboratory Scheduling Control Panel</h1>
                            <p class="pt-3 text-gray-200 max-w-3xl">
                                Manage timetable structure, incoming lecturer requests, news publishing, subject listings, and lecturer records from one place.
                            </p>
                        </div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Overview</p>
                        <h2 class="text-2xl font-black">Admin Panel Home</h2>
                        <p class="pt-2 text-sm text-gray-600">A quick summary of the current timetable system, requests, content, and lecturer records.</p>
                        <div id="admin-stats" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-5"></div>
                    </section>

                    <section id="admin-timetable-settings" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Timetable</p>
                                <h2 class="text-2xl font-black">Columns, Rows, Breaks, Time Slots</h2>
                                <p class="pt-2 text-sm text-gray-600">Live timetable structure and lecture layout from the current database configuration.</p>
                            </div>
                            <button id="admin-refresh-btn" type="button" class="bg-gray-950 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Refresh Panel</button>
                        </div>
                        <div id="admin-timetable-summary" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-5"></div>

                        <div class="pt-6 flex flex-col gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-black text-lg">Timetable Setting</p>
                                        <p class="text-sm text-gray-600 pt-1">Only one settings row is allowed. Update it or reset it.</p>
                                    </div>
                                </div>
                                <div id="admin-settings-table" class="pt-4 overflow-x-auto"></div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-black text-lg">Column Headings</p>
                                        <p class="text-sm text-gray-600 pt-1">Manage heading label, display order, unique heading number, and status. Maximum record count depends on the timetable Columns value.</p>
                                    </div>
                                    <button id="admin-column-heading-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Add Column Heading</button>
                                </div>
                                <div id="admin-column-headings" class="pt-4 overflow-x-auto"></div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-black text-lg">Time Slots</p>
                                        <p class="text-sm text-gray-600 pt-1">Manage unique time slot numbers and the start/end time range. Maximum record count depends on the timetable Rows value.</p>
                                    </div>
                                    <button id="admin-time-slot-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Add Time Slot</button>
                                </div>
                                <div id="admin-time-slots" class="pt-4 overflow-x-auto"></div>
                            </div>
                        </div>
                    </section>

                    <section id="admin-manage-timetable" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Timetable Records</p>
                                <h2 class="text-2xl font-black">CRUD Manage Timetable</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete timetable records with subject, group, lab, day, time slot, and status details.</p>
                            </div>
                            <button id="admin-timetable-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New Timetable Record</button>
                        </div>
                        <div id="admin-manage-timetable-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-requests" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Lecturer Requests</p>
                        <h2 class="text-2xl font-black">Incoming Lecturer Requests</h2>
                        <p class="pt-2 text-sm text-gray-600">Review each request, confirm or cancel it, or remove it if needed.</p>
                        <div id="admin-requests-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-news" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">News</p>
                                <h2 class="text-2xl font-black">Manage News</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete published news records.</p>
                            </div>
                            <button id="admin-news-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New News</button>
                        </div>
                        <div id="admin-news-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-years" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Years</p>
                                <h2 class="text-2xl font-black">Manage Years</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete academic year records.</p>
                            </div>
                            <button id="admin-year-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New Year</button>
                        </div>
                        <div id="admin-years-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-groups" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Groups</p>
                                <h2 class="text-2xl font-black">Manage Lecture Groups</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete lecture group records.</p>
                            </div>
                            <button id="admin-group-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New Group</button>
                        </div>
                        <div id="admin-groups-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-labs" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Labs</p>
                                <h2 class="text-2xl font-black">Manage Labs</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete lab records with their location details.</p>
                            </div>
                            <button id="admin-lab-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New Lab</button>
                        </div>
                        <div id="admin-labs-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-subjects" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Subjects</p>
                                <h2 class="text-2xl font-black">Manage Subjects</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete subject records by year.</p>
                            </div>
                            <button id="admin-subject-create-btn" type="button" class="bg-sky-600 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">New Subject</button>
                        </div>
                        <div id="admin-subjects-list" class="pt-5 overflow-x-auto"></div>
                    </section>

                    <section id="admin-users" data-admin-section class="hidden bg-white/92 rounded-lg p-5 shadow-lg lg:h-[calc(100svh-10rem)] lg:overflow-y-auto">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Users</p>
                                <h2 class="text-2xl font-black">Manage Users</h2>
                                <p class="pt-2 text-sm text-gray-600">Create, update, and delete admin and lecturer accounts.</p>
                            </div>
                            <button id="admin-user-create-btn" type="button" class="bg-emerald-600 text-white font-black px-4 py-3 rounded-lg hover:bg-emerald-700">New User</button>
                        </div>
                        <div id="admin-users-list" class="pt-5 overflow-x-auto"></div>
                    </section>
                </div>
            </section>
        </main>

        <section id="admin-timetable-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-timetable-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-2 gap-4 my-8 mx-auto">
                <div class="md:col-span-2 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Timetable Record</p>
                        <h3 id="admin-timetable-form-title" class="text-2xl font-black">New Timetable Record</h3>
                    </div>
                    <button type="button" id="admin-timetable-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>

                <input type="hidden" id="admin-timetable-id" name="id">
                <input type="hidden" id="admin-timetable-cell-id" name="cell_id">

                <select id="admin-timetable-subject" name="subject_cord" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <option value="">Select subject code</option>
                </select>

                <select id="admin-timetable-group" name="lecture_group_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <option value="">Select group</option>
                </select>

                <select id="admin-timetable-lab" name="lab_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <option value="">Select lab</option>
                </select>

                <select id="admin-timetable-action" name="action" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="free">FREE</option>
                    <option value="active">ACTIVE</option>
                    <option value="cancel">CANCEL</option>
                </select>

                <select id="admin-timetable-day" name="day" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="">Select day</option>
                </select>

                <select id="admin-timetable-time-slot" name="time_slot" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="">Select time slot</option>
                </select>

                <div class="md:col-span-2 flex gap-3 justify-end">
                    <button id="admin-timetable-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save Record</button>
                </div>
            </form>
        </section>

        <section id="admin-settings-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-settings-form" class="w-full max-w-3xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-4 gap-4 my-8 mx-auto">
                <div class="md:col-span-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Timetable Setting</p>
                        <h3 class="text-2xl font-black">Update Timetable Setting</h3>
                    </div>
                    <button type="button" id="admin-settings-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-settings-id">
                <input type="number" id="admin-settings-rows" min="0" placeholder="Rows" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="number" id="admin-settings-columns" min="0" placeholder="Columns" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="number" id="admin-settings-break-row" min="0" placeholder="Break row number" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div class="flex gap-3 md:justify-end md:col-span-1">
                    <button id="admin-settings-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-4 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Save</button>
                </div>
            </form>
        </section>

        <section id="admin-column-heading-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-column-heading-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-4 gap-4 my-8 mx-auto">
                <div class="md:col-span-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Column Heading</p>
                        <h3 id="admin-column-heading-form-title" class="text-2xl font-black">Add Column Heading</h3>
                    </div>
                    <button type="button" id="admin-column-heading-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-column-heading-id">
                <input type="text" id="admin-column-heading-name" placeholder="Column heading" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="number" id="admin-column-heading-number" min="1" placeholder="Column number" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="number" id="admin-column-heading-heading-number" min="1" placeholder="Heading number" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <select id="admin-column-heading-status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="active">Active</option>
                    <option value="deactive">Deactive</option>
                </select>
                <div class="flex gap-3 md:justify-end">
                    <button id="admin-column-heading-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-4 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Save</button>
                </div>
            </form>
        </section>

        <section id="admin-time-slot-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-time-slot-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-4 gap-4 my-8 mx-auto">
                <div class="md:col-span-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Time Slot</p>
                        <h3 id="admin-time-slot-form-title" class="text-2xl font-black">Add Time Slot</h3>
                    </div>
                    <button type="button" id="admin-time-slot-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-time-slot-id">
                <input type="number" id="admin-time-slot-number" min="1" placeholder="Time slot number" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="time" id="admin-time-slot-start" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="time" id="admin-time-slot-end" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div class="flex gap-3 md:justify-end">
                    <button id="admin-time-slot-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-4 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-4 py-3 rounded-lg hover:bg-sky-700">Save</button>
                </div>
            </form>
        </section>

        <section id="admin-news-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-news-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-2 gap-4 my-8 mx-auto" enctype="multipart/form-data">
                <div class="md:col-span-2 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">News</p>
                        <h3 id="admin-news-form-title" class="text-2xl font-black">New News</h3>
                    </div>
                    <button type="button" id="admin-news-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-news-id" name="id">
                <input type="text" id="admin-news-title" name="title" placeholder="News title" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 font-bold" required>
                <input type="file" id="admin-news-image" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                <textarea id="admin-news-description" name="description" placeholder="News description" class="md:col-span-2 min-h-[120px] rounded-lg border border-gray-300 bg-white px-4 py-3"></textarea>
                <input type="date" id="admin-news-start-date" name="start_date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                <input type="date" id="admin-news-end-date" name="end_date" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                <input type="time" id="admin-news-start-at" name="start_at" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                <input type="time" id="admin-news-end-at" name="end_at" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                <div class="md:col-span-2 flex gap-3 justify-end">
                    <button id="admin-news-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save News</button>
                </div>
            </form>
        </section>

        <section id="admin-request-confirm-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-request-confirm-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-2 gap-4 my-8 mx-auto">
                <div class="md:col-span-2 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Lecturer Request</p>
                        <h3 class="text-2xl font-black">Confirm Lecturer Request</h3>
                    </div>
                    <button type="button" id="admin-request-confirm-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-request-confirm-id" name="id">
                <input type="hidden" id="admin-request-confirm-lecturer-id" name="lecturer_id">
                <input type="hidden" id="admin-request-confirm-subject-id" name="subject_id">
                <input type="hidden" id="admin-request-confirm-year-id" name="year_id">
                <input type="hidden" id="admin-request-confirm-time-slot-id" name="timetable_time_slot_id">
                <input type="hidden" id="admin-request-confirm-column-id" name="timetable_column_heading_id">
                <input type="hidden" id="admin-request-confirm-group-id" name="lecture_group_id">

                <input type="text" id="admin-request-confirm-time-slot" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Time Slot" readonly>
                <input type="text" id="admin-request-confirm-day" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Column Heading Name" readonly>
                <input type="text" id="admin-request-confirm-lecturer-name" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Lecturer Name" readonly>
                <input type="text" id="admin-request-confirm-year" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Year" readonly>
                <input type="text" id="admin-request-confirm-subject" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Subject" readonly>
                <input type="text" id="admin-request-confirm-group" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Group" readonly>
                <select id="admin-request-confirm-lab" name="lab_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="">Select lab</option>
                </select>
                <input type="date" id="admin-request-confirm-date" class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" readonly>
                <textarea id="admin-request-confirm-description" class="md:col-span-2 min-h-[120px] rounded-lg border border-gray-300 bg-gray-100 px-4 py-3" placeholder="Description" readonly></textarea>
                <div class="md:col-span-2 flex items-center justify-between gap-3">
                    <button id="admin-request-check-availability" type="button" class="bg-sky-600 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Check Booking</button>
                    <p id="admin-request-check-result" class="text-sm font-bold text-gray-600 text-right"></p>
                </div>

                <div class="md:col-span-2 flex gap-3 justify-end">
                    <button id="admin-request-confirm-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-green-600 text-white font-black px-5 py-3 rounded-lg hover:bg-green-700">Confirm Request</button>
                </div>
            </form>
        </section>

        <section id="admin-subject-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-subject-form" class="w-full max-w-3xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-3 gap-4 my-8 mx-auto">
                <div class="md:col-span-3 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Subject</p>
                        <h3 id="admin-subject-form-title" class="text-2xl font-black">New Subject</h3>
                    </div>
                    <button type="button" id="admin-subject-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-subject-id" name="id">
                <input type="text" id="admin-subject-code" name="subject_cord" placeholder="Subject code" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-subject-name" name="subject" placeholder="Subject name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <select id="admin-subject-year" name="year_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="">Select year</option>
                </select>
                <div class="md:col-span-3 flex gap-3 justify-end">
                    <button id="admin-subject-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save Subject</button>
                </div>
            </form>
        </section>
        <section id="admin-year-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-year-form" class="w-full max-w-2xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 gap-4 my-8 mx-auto">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Year</p>
                        <h3 id="admin-year-form-title" class="text-2xl font-black">New Year</h3>
                    </div>
                    <button type="button" id="admin-year-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-year-id" name="id">
                <input type="text" id="admin-year-name" name="year" placeholder="Year name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div class="flex gap-3 justify-end">
                    <button id="admin-year-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save Year</button>
                </div>
            </form>
        </section>
        <section id="admin-group-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-group-form" class="w-full max-w-2xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 gap-4 my-8 mx-auto">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Group</p>
                        <h3 id="admin-group-form-title" class="text-2xl font-black">New Group</h3>
                    </div>
                    <button type="button" id="admin-group-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-group-id" name="id">
                <input type="text" id="admin-group-name" name="group_name" placeholder="Group name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div class="flex gap-3 justify-end">
                    <button id="admin-group-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save Group</button>
                </div>
            </form>
        </section>
        <section id="admin-lab-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-lab-form" class="w-full max-w-3xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 gap-4 my-8 mx-auto">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">Lab</p>
                        <h3 id="admin-lab-form-title" class="text-2xl font-black">New Lab</h3>
                    </div>
                    <button type="button" id="admin-lab-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-lab-id" name="id">
                <input type="text" id="admin-lab-name" name="lab_name" placeholder="Lab name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-lab-location" name="lab_location" placeholder="Lab location" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div class="flex gap-3 justify-end">
                    <button id="admin-lab-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save Lab</button>
                </div>
            </form>
        </section>
        <section id="admin-user-form-modal" class="hidden fixed inset-0 bg-gray-950/50 z-30 overflow-y-auto p-4">
            <form id="admin-user-form" class="w-full max-w-4xl bg-white rounded-lg p-5 shadow-2xl grid grid-cols-1 md:grid-cols-2 gap-4 my-8 mx-auto">
                <div class="md:col-span-2 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-gray-500 font-black">User</p>
                        <h3 id="admin-user-form-title" class="text-2xl font-black">New User</h3>
                    </div>
                    <button type="button" id="admin-user-form-close" class="self-start bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95" aria-label="Close">X</button>
                </div>
                <input type="hidden" id="admin-user-id" name="id">
                <input type="text" id="admin-user-initials" name="initials" placeholder="Initials" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-user-initials-stand-for" name="initials_stand_for" placeholder="Initials stand for" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-user-first-name" name="first_name" placeholder="First name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-user-last-name" name="last_name" placeholder="Last name" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <select id="admin-user-honorifics" name="honorifics" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <option value="">Select honorifics</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>
                    <option value="Miss">Miss</option>
                    <option value="Dr">Dr</option>
                    <option value="Prof">Prof</option>
                    <option value="Eng">Eng</option>
                </select>
                <select id="admin-user-role" name="role" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                    <option value="">Select role</option>
                    <option value="admin">Admin</option>
                    <option value="lecturer">Lecturer</option>
                </select>
                <input type="text" id="admin-user-nic" name="nic" placeholder="NIC" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="email" id="admin-user-email" name="email" placeholder="Email" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <input type="text" id="admin-user-mobile" name="mobile_number" placeholder="Mobile number" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3" required>
                <div id="admin-user-password-fields" class="contents">
                    <input type="password" id="admin-user-password" name="password" placeholder="Password" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                    <input type="password" id="admin-user-confirm-password" name="confirm_password" placeholder="Confirm password" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3">
                </div>
                <div class="md:col-span-2 flex gap-3 justify-end">
                    <button id="admin-user-form-cancel" type="button" class="bg-gray-200 text-gray-900 font-black px-5 py-3 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="bg-gray-950 text-white font-black px-5 py-3 rounded-lg hover:bg-sky-700">Save User</button>
                </div>
            </form>
        </section>
        <?php include __DIR__ . '/../../Components/FooterBar.php';?>
    </body>
    <script type="module" src="../../JS/main.js"></script>
</html>
