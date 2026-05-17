<?php
namespace Backend\Utils;
    class HttpOnlyCookie{

        public function setCookie($name, $value, $options = []) {

            $defaults = [
                "expires"  => time() + 3600,
                "path"     => "/",
                "secure"   => false,
                "httponly" => true,
                "samesite" => "Lax"
            ];

            $settings = array_merge($defaults, $options);

            setcookie(
                $name,
                $value,
                [
                    "expires"  => $settings["expires"],
                    "path"     => $settings["path"],
                    "secure"   => $settings["secure"],
                    "httponly" => $settings["httponly"],
                    "samesite" => $settings["samesite"],
                ]
            );
        }
        
        public function get($name){
            return $_COOKIE[$name] ?? null;
        }
    }
?>