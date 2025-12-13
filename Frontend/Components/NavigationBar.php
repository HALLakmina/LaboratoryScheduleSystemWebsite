<?php
    include __DIR__ . '/../config.php';
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    function isActive($file) {
        $current = basename($_SERVER['PHP_SELF']);
        return $current === $file ? 'text-white font-bold' : 'text-gray-100';
    }
?>
<nav class="w-full flex flex-row justify-between align-center p-2 bg-gray-950">
    <ul class="list-none flex flex-row align-center justify-center">
        <li class="p-2 hover:bg-sky-400 rounded-xs flex align-center">
            <a href="<?php echo BASE_URL; ?>index.php" class=" <?php echo isActive('index.php'); ?>"> HOME</a>
        </li>
        <li class="p-2 hover:bg-sky-400 rounded-xs flex align-center">
            <a href="<?php echo BASE_URL; ?>pages/timetable.php" class="<?php echo isActive('timetable.php'); ?>">TIMETABLE</a>
        </li>
        <li class="p-2 hover:bg-sky-400 rounded-xs flex align-center">
            <a href="<?php echo BASE_URL; ?>pages/news.php" class="<?php echo isActive('news.php'); ?>">NEWS</a>
        </li>
    </ul>
    <div class=" flex flex-row justify-between align-center p-2">
        <a href="<?php echo BASE_URL; ?>pages/login.php" class="bg-white p-2 hover:bg-sky-400 rounded-xs font-bold">LOGIN</a>
    </div>
</nav>