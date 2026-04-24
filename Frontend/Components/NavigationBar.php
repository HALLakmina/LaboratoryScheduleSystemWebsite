<?php
    include __DIR__ . '/../config.php';

    if (session_status() === PHP_SESSION_NONE) session_start();

    function isActive($file) {
        $current = basename($_SERVER['PHP_SELF']);
        return $current === $file;
    }

    function navItemClasses($isActive) {
        return $isActive
            ? 'bg-sky-500 text-white shadow-md'
            : 'bg-white/8 text-gray-100 hover:bg-white/15';
    }
?>
<nav class="w-full border-b border-white/15 bg-gray-950/95 shadow-lg backdrop-blur-sm">
    <div class="mx-auto flex w-full items-center justify-between gap-3 px-2 py-3 sm:px-4">
        <div class="flex min-w-0 items-center gap-2">
            <details class="relative sm:hidden z-20">
                <summary class="flex min-h-[42px] cursor-pointer list-none items-center justify-center rounded-lg bg-white/8 px-3 py-2 text-xs font-black uppercase tracking-wide text-gray-100 transition hover:bg-white/15">
                    Menu
                </summary>
                <div class="absolute left-0 top-[calc(100%+0.5rem)] z-40 min-w-[200px] rounded-lg border border-white/10 bg-gray-950/98 p-2 shadow-2xl">
                    <ul class="flex flex-col gap-2">
                        <li>
                            <a
                                href="<?php echo BASE_URL; ?>index.php"
                                class="flex min-h-[42px] items-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition <?php echo navItemClasses(isActive('index.php')); ?>"
                            >Home</a>
                        </li>
                        <li>
                            <a
                                href="<?php echo BASE_URL; ?>pages/timetable.php"
                                class="flex min-h-[42px] items-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition <?php echo navItemClasses(isActive('timetable.php')); ?>"
                            >Timetable</a>
                        </li>
                        <li>
                            <a
                                href="<?php echo BASE_URL; ?>pages/news.php"
                                class="flex min-h-[42px] items-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition <?php echo navItemClasses(isActive('news.php')); ?>"
                            >News</a>
                        </li>
                        <li class="admin-nav-item hidden">
                            <a
                                href="<?php echo BASE_URL; ?>pages/adminpanel/admin.php"
                                class="flex min-h-[42px] items-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition <?php echo navItemClasses(isActive('admin.php')); ?>"
                            >Admin</a>
                        </li>
                    </ul>
                </div>
            </details>

            <ul class="hidden min-w-0 items-center gap-2 sm:flex">
                <li class="shrink-0">
                    <a
                        href="<?php echo BASE_URL; ?>index.php"
                        class="flex min-h-[42px] items-center justify-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition lg:px-4 <?php echo navItemClasses(isActive('index.php')); ?>"
                    >Home</a>
                </li>
                <li class="shrink-0">
                    <a
                        href="<?php echo BASE_URL; ?>pages/timetable.php"
                        class="flex min-h-[42px] items-center justify-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition lg:px-4 <?php echo navItemClasses(isActive('timetable.php')); ?>"
                    >Timetable</a>
                </li>
                <li class="shrink-0">
                    <a
                        href="<?php echo BASE_URL; ?>pages/news.php"
                        class="flex min-h-[42px] items-center justify-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition lg:px-4 <?php echo navItemClasses(isActive('news.php')); ?>"
                    >News</a>
                </li>
                <li class="admin-nav-item hidden shrink-0">
                    <a
                        href="<?php echo BASE_URL; ?>pages/adminpanel/admin.php"
                        class="flex min-h-[42px] items-center justify-center rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide transition lg:px-4 <?php echo navItemClasses(isActive('admin.php')); ?>"
                    >Admin</a>
                </li>
            </ul>
        </div>

        <div class="shrink-0">
            <a
                id="auth-nav-btn"
                href="<?php echo BASE_URL; ?>pages/login.php"
                data-login-href="<?php echo BASE_URL; ?>pages/login.php"
                class="flex min-h-[42px] items-center justify-center rounded-lg bg-white px-3 py-2 text-xs font-black uppercase tracking-wide text-gray-950 transition hover:bg-sky-400 active:scale-95 sm:px-4"
            >Login</a>
        </div>
    </div>
</nav>
