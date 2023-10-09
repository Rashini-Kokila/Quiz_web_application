<?php
//session_start();
if(!isset($_SESSION["quiz_id"])) {
    header("Location: show_quiz.php");
    exit();
}
if(!isset($_SESSION["last_id"])) {
    header("Location: show_quiz.php");
    exit();
}
?>
<!DOCTYPE html>
<!-- saved from url=(0048)https://jthemes.net/themes/html/quizo/version-1/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quiz</title>
   <!-- FontAwesome-cdn include -->
   <link rel="stylesheet" href="./reference/all.min.css">
   <!-- Google fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com/">
   <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
   <link href="./reference/css2" rel="stylesheet">
   <!-- Bootstrap-css include -->
   <link rel="stylesheet" href="./reference/bootstrap.min.css">
   <!-- Animate-css include -->
   <link rel="stylesheet" href="./reference/animate.min.css">
   <!-- Main-StyleSheet include -->
   <link rel="stylesheet" href="./reference/style.css">
   
   <link rel="stylesheet" href="./reference/cmpt.css">
</head>