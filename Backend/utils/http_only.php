<?php
// After validating user credentials and generating JWT
    

    function setHttpCookies($jwt, $name){
        $token = $jwt; // your generated JWT string

    // Cookie options
        $cookieOptions = [
            'expires' => time() + (60 * 60 * 24), // 1 day
            'path' => '/',                        // accessible to entire domain
            'domain' => '',         // change this to your domain
            'secure' => true,                     // true = only sent over HTTPS
            'httponly' => true,                   // prevents JS access
            'samesite' => 'Strict'                // prevents CSRF (can be 'Lax' or 'None' too)
        ];

        // Set cookie
        setcookie($name, $token, $cookieOptions);
    }
?>
