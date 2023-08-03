<?php
    $db_sever = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "laboratory_schedule_system";

    $DB_CON = mysqli_connect($db_sever, $db_user, $db_password, $db_name) or die ("Connection failed");
?>