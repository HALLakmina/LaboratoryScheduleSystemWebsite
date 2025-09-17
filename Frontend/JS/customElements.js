class PageRouter extends HTMLElement {
    constructor() {
        super();
        this.innerHTML = `<nav class="w-full flex flex-row justify-between items-center p-2 bg-gray-950">
            <ul class="list-none flex flex-row align-center justify-center">
                <li class="p-2">
                    <a class="p-2 hover:bg-sky-400 rounded-xs text-white font-bold" href="./index.php">HOME</a>
                </li>
                <li class="p-2">
                    <a class="p-2 hover:bg-sky-400 rounded-xs text-white font-bold" href="./timetable.php">TIMETABLE</a>
                </li>
                <li class="p-2">
                    <a class="p-2 hover:bg-sky-400 rounded-xs text-white font-bold" href="./news.php">NEWS</a>
                </li>
            </ul>
            <div class=" flex flex-row justify-between items-center p-2">
                <a class="bg-white p-2 hover:bg-sky-400 rounded-xs font-bold" href="./login.php">LOGIN</a>
            </div>
        </nav>`;
    }
}


class PageFooter extends HTMLElement {
    constructor() {
        super();
        this.innerHTML = `<footer class="text-center fixed bottom-0 left-0 right-0 p-2 bg-gray-950">
            <p class="text-white font-bold">Copyright <span class="text-sky-400">&copy;</span> 2025 My Website. All rights reserved.</p>
        </footer>`;
    }
}

// 2. Register the element with a tag name
customElements.define('page-router', PageRouter);
customElements.define('page-footer', PageFooter);