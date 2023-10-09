<?php
//Start the session
//if user already logged in then redirect user to welcome page
session_start();
    if(!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
?>