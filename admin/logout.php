<?php 
    session_start();
    session_unset();
    setcookie('user_login',$id,time()-60*60*24*30);
    // Destroy session Redirecting To Login Page
    if(session_destroy()){
        header('location: login.php');
    }
?>