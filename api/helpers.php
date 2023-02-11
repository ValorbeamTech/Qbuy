<?php
// all helper functions 
function omit_newlines($string): string{
        return trim(preg_replace('/\s+/', ' ', $string));
}

function generate_random_string($length_of_string): string{
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str_result),0, $length_of_string);
}

function hash_password($string): string{
        return password_hash($string, PASSWORD_BCRYPT);
}

function verify_password($user_password, $hash_password){
        return password_verify($user_password, $hash_password);
}

function getHttpRequestHeaders(){
        // required headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
                header('Access-Control-Allow-Origin: *');
                header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
                header("HTTP/1.1 200 OK");
        }
}