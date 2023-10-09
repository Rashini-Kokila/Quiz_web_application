<?php
include("session.php");
include("dbConnection.php");

$db= new DatabaseConnection;
$user_id='';


if (isset($_POST['start'])) {
    if(isset($_SESSION["quiz_id"])) {
        $quiz_id=$_SESSION["quiz_id"];
        header("Location:show_question.php?id=".$quiz_id."");
        exit();
    }
    $user_id=$_SESSION['user_id'];
    $quiz_id = $_POST['start'];
    
    $q_get_user_quiz_details= "SELECT * FROM `user_quiz` WHERE `quiz_id`= $quiz_id 
    AND `user_id`=$user_id ";
    $r_q_get_user_quiz_details =mysqli_query($db->con, $q_get_user_quiz_details);
    $n_raws = mysqli_num_rows( $r_q_get_user_quiz_details);
    if ( $n_raws> 0) {
        echo '<script type="text/javascript">

                window.onload = function () { alert("you already answered this quiz!!"); }

                </script>';
    }else{
        $q_get_quiz_details= "SELECT * FROM `quiz` WHERE `id`= $quiz_id ";
        $r_q_get_quiz_details =mysqli_query($db->con, $q_get_quiz_details);
        $row_q_get_quiz=mysqli_fetch_array( $r_q_get_quiz_details);
        $quiz_start_time=date("Y-m-d h:i:s");
     
        session_start();
            $_SESSION["quiz_id"]=$quiz_id;
            $_SESSION["start_time"]= $quiz_start_time;
            $_SESSION["duration"]=$row_q_get_quiz['time'];
    
        $quiz_end_time=date("Y-m-d h:i:s");
        $modified_by=$_SESSION['user_id'];
        $last_update=date("Y-m-d h:i:s");
        $expired=0;
        $insert_quiz_details=("INSERT INTO user_quiz ( `user_id`,`quiz_id`,`quiz_start_time`,`quiz_end_time`,`modified_by`,`last_update`,`expired`) 
        VALUES ('$user_id','$quiz_id','$quiz_start_time','$quiz_end_time','$modified_by','$last_update','$expired')");
        $r_insert_quiz_details =mysqli_query($db->con, $insert_quiz_details);//check query
        if ( $r_insert_quiz_details == TRUE) {
            $last_id = $db->con->insert_id;
            $_SESSION["last_id"]= $last_id;
            header("Location:show_question.php?id=".$quiz_id."");
        }else{
            echo 'error in'.$insert_quiz_details;
        }
    }
}

if (isset($_SESSION['user_id'])){
    $user_id=$_SESSION['user_id'];
    //view records
    $q_get_quiz_details= "SELECT * FROM quiz WHERE is_expired=0
    ORDER BY id DESC";
    $r_q_get_quiz_details =mysqli_query($db->con, $q_get_quiz_details);

    
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

    <div class="wrapper position-relative">
        <div class="container-fluid px-5">
            <div class="step_bar_content ps-5 pt-5 form_title">
                <h1 class="f-col text-uppercase d-inline-block">Quizzes</h1>
            </div>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <?php
                if ($r_q_get_quiz_details->num_rows> 0) {
                    while ($row = $r_q_get_quiz_details->fetch_assoc()) {                     
            ?>
                        <div class="card mb-3 bg-secondary" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-4">
                                <?php
                                    if(!empty( $row['image'])){
                                        echo "<img src='.././quiz_images/thumbs/".$row['image']."'class='img-fluid rounded-start'>"; 
                                    }?>
                                </div>
                                <div class="col-md-8">
                                <div class="card-body">
                                    <input type='hidden' name='time' value='<?php echo $row['time'];?>'>
                                    <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                    <p class="card-text">
                                    <?php echo $row['description']; ?>
                                    </p>
                                    <button type="submit" class="f_btn rounded-pill active text-uppercase" name="start" value="<?php echo $row['id']; ?>"> Start Quiz</button>
                                </div>
                                </div>
                            </div>
                        </div>
            <?php   }
                }
                ?>
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
<?php 

}
 ?>