import { initTimetablePage } from './timetable.js';
import { initAdminPanel, initAdminSideNav } from './admin.js';
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
    initTimetablePage,
].forEach((initializer) => initializer());
