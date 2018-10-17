<?php
$DB_NAME = "matcha";
$DB_USER = "root";
$DB_PASSWORD = "samsung"; //samsung
$DB_DSN = "mysql:host=". $_SERVER['SERVER_NAME'];
session_start();

function ft_escape_str($string){
    return (filter_var($string, FILTER_SANITIZE_STRING));
}

function validate_password($password) {
    if (strlen($password) < 8)
        return "Password too short, atleast 8 charecters";
    if (!preg_match('/\d/', $password))
        return "Password must contain a digit";
    if (!preg_match('/[^A-Za-z0-9]/', $password))
        return "Password must contain a special character";
    return true;
}


function validate_email($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Invalid email format"; 
    }else
        return true;
}