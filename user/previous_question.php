<?php
include("session.php");
include("dbConnection.php");
//include("response.php");
$db= new DatabaseConnection;

$err=array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id=$_SESSION['user_id'];
    $quiz_id=$_POST["quiz_id"];
    $question_id=$_POST["question_id"];

    if (isset($_POST["answer_id"])){
        foreach ($_POST["answer_id"] as $row=>$option){
        $answers_id[]=$option;
        }
    }

    if(empty($err)){
        $get_user_answers="SELECT id FROM `user_answers` 
        WHERE `user_id`=$user_id AND `question_id`= $question_id";
        $result_get_user_answers =mysqli_query($db->con, $get_user_answers);
        $n_raws_of_user_answers = mysqli_num_rows($result_get_user_answers);
        if($n_raws_of_user_answers>0){
            $last_update=date("Y-m-d h:i:s");
            $is_correct=0;
            $modified_by=$_SESSION['user_id'];
            $expired=0;
            $j=0;
            if (isset($_POST["answer"])){
                $text_answer=$_POST["answer"];
                $answer_id=0;
                $id=$_POST["id"][$j];
                $q_insert_user_answer=("UPDATE user_answers 
                SET  `user_id`='$user_id',
                    `quiz_id`='$quiz_id',
                    `question_id`='$question_id',
                    `answer_id`='$answer_id',
                    `answer`='$text_answer',
                    `last_update`= '$last_update',
                    `modified_by`='$modified_by',
                    `is_correct`='$is_correct',
                    `expired`='$expired' 
                WHERE `id`='$id'");
                echo  $q_insert_user_answer;
                $r_q_insert_user_answer = mysqli_query($db->con, $q_insert_user_answer);//check query
                if($r_q_insert_user_answer) {
                    $last_id = $db->con->insert_id;
                    echo "Entries added!<br>";      
                }else {
                    echo "Entries  cannot added<br>";
                }
            }
            foreach ($answers_id as $key=>$answer_id){
                if($answer_id!=""){
                    $q_get_answers=("SELECT * FROM `answers` WHERE `id`=$answer_id");
                    $result_q_get_answers =mysqli_query($db->con, $q_get_answers);
                    $row_q_get_answers=mysqli_fetch_array($result_q_get_answers);
                    $answer=$row_q_get_answers['answer'];

                    $id=$_POST["id"][$j];
                    $q_insert_user_answer=("UPDATE user_answers 
                    SET  `user_id`='$user_id',
                        `quiz_id`='$quiz_id',
                        `question_id`='$question_id',
                        `answer_id`='$answer_id',
                        `answer`='$answer',
                        `last_update`= '$last_update',
                        `modified_by`='$modified_by',
                        `is_correct`='$is_correct',
                        `expired`='$expired' 
                    WHERE `id`='$id'");
                    echo  $q_insert_user_answer;
                    $r_q_insert_user_answer = mysqli_query($db->con, $q_insert_user_answer);//check query
                    if($r_q_insert_user_answer) {
                        $last_id = $db->con->insert_id;
                        echo "Entries added!<br>";      
                    }else {
                        echo "Entries  cannot added<br>";
                    }
                }
            }
        }else{
            $last_update=date("Y-m-d h:i:s");
            $is_correct=0;
            $modified_by=$_SESSION['user_id'];
            $expired=0;
            if (isset($_POST["answer"])){
                $text_answer=$_POST["answer"];
                $answer_id=0;
                $q_insert_user_answer=("INSERT INTO user_answers ( `user_id`,`quiz_id`,`question_id`,`answer_id`,`answer`,`last_update`,`modified_by`,`is_correct`,`expired`) 
                VALUES ('$user_id','$quiz_id','$question_id','$answer_id','$text_answer','$last_update','$modified_by','$is_correct','$expired')");
                echo  $q_insert_user_answer;
                $r_q_insert_user_answer = mysqli_query($db->con, $q_insert_user_answer);//check query
                if($r_q_insert_user_answer) {
                    $last_id = $db->con->insert_id;
                    echo "Entries added!<br>";      
                }else {
                    echo "Entries  cannot added<br>";
                }
            }
            foreach ($answers_id as $key=>$answer_id){
                if($answer_id!=""){
                    $q_get_answers=("SELECT * FROM `answers` WHERE `id`=$answer_id");
                    $result_q_get_answers =mysqli_query($db->con, $q_get_answers);
                    $row_q_get_answers=mysqli_fetch_array($result_q_get_answers);
                    $answer=$row_q_get_answers['answer'];
    
                    $q_insert_user_answer=("INSERT INTO user_answers ( `user_id`,`quiz_id`,`question_id`,`answer_id`,`answer`,`last_update`,`modified_by`,`is_correct`,`expired`) 
                    VALUES ('$user_id','$quiz_id','$question_id','$answer_id','$answer','$last_update','$modified_by','$is_correct','$expired')");
                    echo  $q_insert_user_answer;
                    $r_q_insert_user_answer = mysqli_query($db->con, $q_insert_user_answer);//check query
                    if($r_q_insert_user_answer) {
                        $last_id = $db->con->insert_id;
                        echo "Entries added!<br>";      
                    }else {
                        echo "Entries  cannot added<br>";
                    }
                }
            }
        }
    }
}
if(isset($_POST["next"])){
    header("Location:next_question.php?question_id= ".$question_id."");
    exit();
}
if (isset($_POST["previous"])) {
    $question_id=$_POST["question_id"];

    header("Location:previous_question.php?question_id= ".$question_id."");
    exit();

}

if (isset($_GET['question_id'])){
    $id=$_GET['question_id'];
    $user_id=$_SESSION['user_id'];

    $get_quiz_id= "SELECT * FROM `question` WHERE `id`='$id'";
    $r_get_quiz_id = mysqli_query($db->con, $get_quiz_id);
    $row_get_quiz_id = mysqli_fetch_array( $r_get_quiz_id);

    $quiz_id=$row_get_quiz_id["quiz_id"];
    $sequence=$row_get_quiz_id["sequence"];
    
    $q_get_question_details = "SELECT * FROM `question` 
    WHERE `quiz_id`='$quiz_id' AND `is_expired`=0 AND `sequence`<'$sequence' ORDER BY `sequence`DESC";
    $r_q_get_question_details =mysqli_query($db->con, $q_get_question_details);
    $n_raws = mysqli_num_rows($r_q_get_question_details);

}

include ("user_header.php");
?>
<body data-new-gr-c-s-check-loaded="14.1087.0" data-gr-ext-installed="">
    <div class="wrapper position-relative">
        <div class="container-fluid px-5">
            <div class="step_bar_content ps-5 pt-5">
                <h3 class="f-col text-uppercase d-inline-block">Quiz Questions and Answers</h3>
            </div>
            <div class="text-end" id="response"> 
                <h5 class="text-black d-inline-block" id="time"></h5>
            </div>
            <form class="multisteps_form position-relative" id="wizard" method="POST" action=""  novalidate="novalidate">
            <?php
            if ($r_q_get_question_details->num_rows> 0) {
                $row_q_question = mysqli_fetch_array($r_q_get_question_details);
            ?>
                <div class="progress_bar steps_bar mt-3 ps-5 d-inline-block">
                    <div class="step rounded-pill d-inline-block text-center position-relative active"><?php echo $row_q_question["sequence"] ?></div>
                </div>
                <div class="multisteps_form_panel active slideVert" data-animation="slideVert">
                    <div class="form_content">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form_title ps-5" id="load_question">
                                    <input type='hidden' name='quiz_id' value='<?php echo $row_q_question["quiz_id"]?>'>
                                    <input type='hidden' name='question_id' value='<?php echo $row_q_question["id"]?>'>
                                    <h4 class="f-col text-black"><?php echo $row_q_question["question"]?></h4>
                                </div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="form_img">
                                    <?php echo "<img src='.././question_images/thumbs/".$row_q_question['image']."' width='50' height='50'>"; ?>
                                </div>
                            </div>
                            <?php
                            $question_id=$row_q_question["id"];
                            $q_get_answers=("SELECT * FROM `answers` WHERE question_id=$question_id");
                            $result_q_get_answers =mysqli_query($db->con, $q_get_answers);
                            ?>
                            <div class="col-lg-4 text-end">
                                <div class="form_items radio-list">
                                    <ul class="text-uppercase list-unstyled">
                                        <li>
                                        <?php
                                        $options=array();
                                        $id=array();
                                        $get_user_answers="SELECT * FROM `user_answers` 
                                        WHERE `user_id`=$user_id AND `question_id`= $question_id";
                                        $result_get_user_answers =mysqli_query($db->con, $get_user_answers);
                                        while($row_get_user_answers = $result_get_user_answers->fetch_assoc()){
                                            $options_id[]=$row_get_user_answers["answer_id"];
                                            $options[]=$row_get_user_answers["answer"];
                                            $id[]=($row_get_user_answers['id']); 
                                        }
                                        if(!empty($options_id)){
                                            $i=1;
                                            $j=0;
                                            if($row_q_question["answer_type"]==1){ ?> 
                                                <input type='hidden' name='id[]' value='<?php echo $id[$j]?>'>                                                                                                           
                                                    <label for="opt" class="step_4 rounded-pill animate__animated animate__fadeInRight animate_25ms">
                                                        <textarea id="text" class="form-control" name="answer"><?php  echo $options[$j]?></textarea>
                                                    </label>                                                                        
                                                <?php
                                                }                                        
                                            if ($result_q_get_answers->num_rows> 0) {
                                                while($row_q_get_answers=mysqli_fetch_array($result_q_get_answers)){
                                                    ?>
                                                    <?php                                                
                                                    if(in_array($row_q_get_answers["id"],$options_id)){ 
                                                        if($row_q_question["answer_type"]==2){ ?>
                                                        <input type='hidden' name='id[]' value='<?php echo $id[$j]?>'>                                                                                                                                
                                                            <label for="<?php echo $i;?>" class="d-flex step_1 rounded-pill animate__animated animate__fadeInRight animate_25ms">
                                                                <span class="label-pointer rounded-circle text-center"><?php echo $i;?></span>
                                                                <input type="radio" id="<?php echo $i;?>" class="active" name="answer_id[]" value="<?php echo  $options_id[$j]?>" >
                                                                <span class="label-content d-inline-block text-center text-white rounded-pill">
                                                                    <?php  echo $options[$j]?>
                                                                </span>
                                                            </label>                                                    
                                                        <?php
                                                        }
                                                        if($row_q_question["answer_type"]==3){ ?>
                                                        <input type='hidden' name='id[]' value='<?php echo $id[$j]?>'>                                                                                    
                                                            <label for="<?php echo $i;?>" class="d-flex step_3 rounded animate__animated animate__fadeInRight animate_25ms">
                                                                <span class="label-pointer rounded text-center">
                                                                    <?php echo $i;?>
                                                                </span>
                                                                <input type="checkbox" id="<?php echo $i;?>" class="active" name="answer_id[]" value="<?php echo  $options_id[$j]?>" >
                                                                <span class="label-content d-inline-block text-center text-white rounded">
                                                                    <?php echo $options[$j]?>
                                                                </span>
                                                            </label> 
                                                        <?php
                                                        }
                                                        if(count($options)-1>$j){ 
                                                            $j++;             
                                                        }                              
                                                    }else{
                                                        if($row_q_question["answer_type"]==2){ ?>                                                                                                                                
                                                            <label for="<?php echo $i;?>" class="d-flex step_1 rounded-pill animate__animated animate__fadeInRight animate_25ms">
                                                                <span class="label-pointer rounded-circle text-center"><?php echo $i;?></span>
                                                                <input type="radio" id="<?php echo $i;?>" name="answer_id[]" value="<?php echo $row_q_get_answers["id"]?>">
                                                                <span class="label-content d-inline-block text-center rounded-pill">
                                                                    <?php echo $row_q_get_answers["answer"]?>
                                                                </span>
                                                            </label>                                                    
                                                        <?php
                                                        }
                                                        if($row_q_question["answer_type"]==3){ ?>                                                                                    
                                                            <label for="<?php echo $i;?>" class="d-flex step_3 rounded animate__animated animate__fadeInRight animate_25ms">
                                                                <span class="label-pointer rounded text-center">
                                                                    <?php echo $i;?>
                                                                </span>
                                                                <input type="checkbox" id="<?php echo $i;?>" name="answer_id[]" value="<?php echo $row_q_get_answers["id"]?>">
                                                                <span class="label-content d-inline-block text-center rounded">
                                                                    <?php echo $row_q_get_answers["answer"]?>
                                                                </span>
                                                            </label> 
                                                        <?php
                                                        }
                                                    }
                                                    $i++;
                                                }
                                            }
                                        }else{
                                            if($row_q_question["answer_type"]==1){ ?>                                                    
                                                <label for="opt" class="step_4 rounded-pill animate__animated animate__fadeInRight animate_25ms">
                                                    <textarea id="text" class="form-control" name="answer"></textarea>
                                                </label>                                                                    
                                            <?php
                                                }
                                            $i=1;
                                            if ($result_q_get_answers->num_rows> 0) { 
                                                while($row_q_get_answers=mysqli_fetch_array($result_q_get_answers)){
                                                    if($row_q_question["answer_type"]==2){ ?>                                                                                                                                
                                                        <label for="<?php echo $i;?>" class="d-flex step_1 rounded-pill animate__animated animate__fadeInRight animate_25ms">
                                                            <span class="label-pointer rounded-circle text-center"><?php echo $i;?></span>
                                                            <input type="radio" id="<?php echo $i;?>" name="answer_id[]" value="<?php echo $row_q_get_answers["id"]?>">
                                                            <span class="label-content d-inline-block text-center rounded-pill">
                                                                <?php echo $row_q_get_answers["answer"]?>
                                                            </span>
                                                        </label>                                                    
                                                    <?php
                                                    }
                                                    if($row_q_question["answer_type"]==3){ ?>                                                                                    
                                                        <label for="<?php echo $i;?>" class="d-flex step_3 rounded animate__animated animate__fadeInRight animate_25ms">
                                                            <span class="label-pointer rounded text-center">
                                                                <?php echo $i;?>
                                                            </span>
                                                            <input type="checkbox" id="<?php echo $i;?>" name="answer_id[]" value="<?php echo $row_q_get_answers["id"]?>">
                                                            <span class="label-content d-inline-block text-center rounded">
                                                                <?php echo $row_q_get_answers["answer"]?>
                                                            </span>
                                                        </label> 
                                                    <?php
                                                    }
                                                    $i++;
                                                }
                                            }
                                        }?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="form_btn py-5 d-flex justify-content-center align-items-center">
                        <?php
                        if($n_raws>1){
                        ?>
                            <input class="js-btn-next f_btn rounded-pill active text-uppercase" type="submit" value="Previous Question" name="previous" />                        
                        <?php
                        }
                        ?>                                                      
                            <input class="js-btn-next f_btn rounded-pill active text-uppercase" type="submit" value="Next Question" name="next" />
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
            </form>
        </div>
    </div>
<?php 
    include ("user_footer.php");
 ?>