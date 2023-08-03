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
    <style>
        .login-content
        {
            margin:10px;
            margin-top:10%;
            display:flex;
            justify-content: space-between;
            align-items: center;
        }
        .login-logo
        {
            width: 200px;
            height: 200px;
            margin-right: 5%;
            border-radius: 100%;
            box-shadow: 0 10px 10px rgba(0,0,0,.9);
        }
        .login-title
        {
            font-size:90px;
            font-weight: bold;
            text-shadow:0 8px 8px rgba(0,0,0,.9);
        }
        .content-box
        {
            display:flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color:white;
        }
        .login-form-item
        {
            display:flex;
            flex-direction: column;
            justify-content: center;
            background-color:white;
            padding:10px;
            width:400px;
            margin-right: 10px;
            border-radius: 10px;
            box-shadow: 5px 10px 10px rgba(255,255,255,0.5);
        }
        .login-hading
        {
            font-size:50px;
            font-weight:bold;
            border: none;
            text-decoration: underline red;

        }
        .login-item
        {
            display:flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-name
        {
            margin-top:30px;
            margin-right:20px;
            font-size:20px;
            font-weight: bold
        }
        .form-box
        {
            margin-right:20px;
            border:none;
            outline: none;
            background-color: none;
            border-bottom: 3px solid #00aaff;
            height:30px;
            width:70%;
            font-size: 20px;
        }
        .btn-content
        {
            display:flex;
            justify-content: center;
            align-items: center;
        }
        .login_btn
        {
            margin-top:30px;
            margin-bottom:20px;
            border:none;
            outline: none;
            width:100px;
            background-color: #00aaff;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            font-size: 25px;
        }
    </style>
    <main class="login-content">
        <div class="content-box">
            <div class="">
                <img src="./resources/website_image/Vavuniversity.png" class="login-logo">
            </div>
            <div class="login-title">
                <P>Laboratory Schedule <br>System</P>
            </div>
        </div>
        <form method='post' action='script.php' class="login-form-item">
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