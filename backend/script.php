<?php
include_once("./dbConnection.php");
session_start();

if(isset($_POST["login"]))
{
    $username =mysqli_real_escape_string($DB_CON, $_POST["user"]); 
    $password = mysqli_real_escape_string($DB_CON, $_POST["password"]) ;

    $query = "SELECT * FROM lecture_details WHERE user_name = '$username' limit 1";
    $result = mysqli_query($DB_CON,  $query);
    $row = mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {
        if($row["user_name"]==$username AND $row["password"]==$password)
        {
            $_SESSION["user_name"]=$row["user_name"];
            $_SESSION["user_id"]=$row["id"];
            header("Location: ../index.php");
            die;
        }
        else
        {
            echo"user name or password is wrong";
        }
    }
    else
    {
        echo"enter valid information";
    }
}

if (isset($_GET["logout"]))
{
    unset($_SESSION["login"]);
    unset($_GET["logout"]);
    session_destroy();
    header("Location: ../index.php");
}
?>