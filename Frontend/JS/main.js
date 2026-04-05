import { initTimetablePage } from './timetable.js';
import { initAdminPanel, initAdminSideNav } from './admin.js';
import { initNewsPage } from './news.js';
import { initLoginForm } from './login.js';
import { initAuthNavButton } from './loginUser.js';

[
    initAuthNavButton,
    initLoginForm,
    initNewsPage,
    initAdminSideNav,
    initAdminPanel,
    initTimetablePage,
].forEach((initializer) => initializer());
