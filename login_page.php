<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='./css/main-style-sheet.css'>
</head>
<body>
    <main class="login-content">
        <div class="content-box">
            <div class="">
                <img src="./resources/website_image/Vavuniversity.png" class="login-logo">
            </div>
            <div class="login-title">
                <P>Laboratory Schedule <br>System</P>
            </div>
        </div>
        <form method='post' action='./backend/script.php' class="login-form-item">
            <div class="login-hading">
                <p>Login</p>
            </div>
            <div class="login-item">
                <label for="" class="form-name">User Name</label>
                <input type='text' name='user' placeholder="Type hear" class="form-box">
                <label for="" class="form-name">Password</label>
                <input type="password" name='password' placeholder="*********" class="form-box">
            </div>
            <div class="btn-content">
                <input type="submit" name='login' value='Login' class="login_btn">
            </div>
        </form>
        
    </main>
<?php include("./components/footer_bar.php");?>