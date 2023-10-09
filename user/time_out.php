<?php
include("session.php");
include("dbConnection.php");

$db= new DatabaseConnection;
if(!isset($_SESSION["last_id"])) {
    header("Location: show_quiz.php");
    exit();
}
$quiz_end_time=date("Y-m-d h:i:s");
$last_id=$_SESSION["last_id"];
$last_update=date("Y-m-d h:i:s");
$modified_by=00;
$update_quiz_endtime=("UPDATE user_quiz SET  
`quiz_end_time`='$quiz_end_time',`last_update`='$last_update',`modified_by`='$modified_by' WHERE `id`='$last_id'");
$r_update_quiz_endtime = mysqli_query($db->con, $update_quiz_endtime);//check query
if($r_update_quiz_endtime) {
    unset($_SESSION['quiz_id']);
    unset($_SESSION['duration']);
    unset($_SESSION['last_id']);
    //echo "Entries added!<br>";      
}else {
    echo "Entries  cannot added<br>";
} 

if(isset($_POST['button'])){
    header("Location:show_quiz.php");
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
   <body data-new-gr-c-s-check-loaded="14.1087.0" data-gr-ext-installed="">

   <!-- Earnings (Monthly) Card Example -->
   <div class="wrapper position-relative">
        <div class="container-fluid px-5">
            <div class=" py-5 d-flex justify-content-center align-items-center">
                <h1 class="f-col text-red text-uppercase">TIME OUT</h1>
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
                <div class="form_btn py-5 d-flex justify-content-center align-items-center">
                    <button type="submit" name="button"> Back to Quiz</button>
                </div> 
            </form>          
        </div>
    </div>
<!-- jQuery-js include -->
<script src="./reference/jquery-3.6.0.min.js.download"></script>
   <!-- Bootstrap-js include -->
   <script src="./reference/bootstrap.min.js.download"></script>
   <!-- jQuery-validate-js include -->
   <script src="./reference/jquery.validate.min.js.download"></script>
   <!-- Custom-js include -->
   <script src="./reference/script.js.download"></script>
   <script src="./reference/scr.js"></script>

</body><grammarly-desktop-integration data-grammarly-shadow-root="true"></grammarly-desktop-integration></html>