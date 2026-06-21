import { initTimetablePage } from './timetable.js';
import { initAdminPanel, initAdminSideNav, initLecturerAssignmentsPanel, initLogsPanel } from './admin.js';
import { initNewsPage } from './news.js';
import { initLoginForm } from './login.js';
import { initAuthNavButton } from './loginUser.js';
import { initLoadingSystem } from './utils.js';

initLoadingSystem();

[
    initAuthNavButton,
    initLoginForm,
    initNewsPage,
    initAdminSideNav,
    initAdminPanel,
    initLecturerAssignmentsPanel,
    initLogsPanel,
    initTimetablePage,
].forEach((initializer) => initializer());
