<?php
    session_start();
    include_once("./backend/DBConnection.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <!--link rel='stylesheet' type='text/css' media='screen' href='main.css'>
        <script src='main.js'></!--script-->
        <link rel='stylesheet' type='text/css' href='./css/main-style-sheet.css'>

        <script src="./JQuery/jQuery-3.6.4.compressed.production .js"></script>
        <script src="./JQuery/jQuery-3.6.4.slim.build.compressed.production .js"></script>
        <script src="./JQuery/jQuery-3.6.4.slim.build.uncompressed.development .js"></script>
        <script src="./JQuery/jQuery-3.6.4.uncompressed.development .js"></script>
    </head>
    <body>
        <header>
            <nav class="">
                <uL class="nav-list">
                    <li><a href='./index.php' class="nav-link">HOME</a></li>
                    <li><a href='./timetable.php' class="nav-link">TIMETABLE</a></li>
                    <li><a href='./news.php' class="nav-link">NEWS</a></li>
                </uL>
                <?php
                    if(isset($_SESSION['user_name']))
                    {
                    ?>
                        <a href='lecture_profile.php' class="nav-profile"><?php echo $_SESSION['user_name'];?></a>
                    <?php
                    }
                    else
                    {
                    ?>
                        <a href='login_page.php' class="nav-join">LECTURE LOGIN</a>
                    <?php
                    }
                    ?>
            </nav>
        </header>
