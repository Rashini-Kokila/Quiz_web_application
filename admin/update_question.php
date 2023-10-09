<?php
include("session.php");
include ("dbConnection.php");
$db= new DatabaseConnection;
$err=array();
$filename='';
$add_ans_type='';
$count0fAn='';
if (isset($_POST["update"])){
    $id=$_POST['id'];
    $quiz_id=$_POST['quiz_id'];
    $question = $_POST['question'];
    $answer_type=$_POST['ans_type'];

    //question validation
    $question=SanitizeValues($question);
    if (empty($question)) {
        $err['question']="Question is required"; 
    }
    //answer validation
    if(!empty($_POST["options"])){
        foreach ($_POST["options"] as $row=>$option){
        $answers[]=SanitizeValues($option);
        $count0fAn=count($answers);
        }
    }
    //add answers
    if(!empty($_POST["add_options"])){
        foreach ($_POST["add_options"] as $row=>$add_option){
        $add_answers[]=SanitizeValues($add_option);
        }
    }
    //image
    if(!empty($_FILES["new_image"]["tmp_name"])&&(!empty($_POST['image']))||
        !empty($_FILES['new_image']['tmp_name']) && (empty($_POST['image']))){
        $allowed_types = array('jpg', 'png', 'jpeg', 'gif');
        $maxsize = 2 * 1024 * 1024;
        $filename = $_FILES["new_image"]["name"];
        $tempname = $_FILES["new_image"]["tmp_name"];
        $file_size = $_FILES['new_image']['size'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

        $imgProperties = getimagesize($tempname);
        $img_type=$imgProperties[2];

        if(!in_array(strtolower($file_ext), $allowed_types)) {
            $err['image']="({$file_ext} file type is not allowed)<br / >";
        }
        // Verify file size - 2MB max
        if ($file_size > $maxsize) {
            $err['image']= "Error: File size is larger than the allowed limit.";
        }
    }

    if(empty($err)){
        $answer_type_id=Get_answer_type($answer_type); //get answer type

        //insert questions to the databse
        $last_update=date("Y-m-d h:i:s");
        $pathToImages = ".././question_images/";
        $pathToThumbs = ".././question_images/thumbs/";
        
        if(empty(($_FILES['new_image']['tmp_name'])) && !empty($_POST['image'])){
            $filename = $_POST['image'];
        }elseif(!empty($_FILES['new_image']['tmp_name']) && !empty($_POST['image'])||
        !empty($_FILES['new_image']['tmp_name']) && (empty($_POST['image']))){    
            if($img_type==IMAGETYPE_JPEG){
                $source=imagecreatefromjpeg($tempname);
                $resizeImg = image_resize($source,$imgProperties[0],$imgProperties[1]);
                imagegif($resizeImg,$pathToThumbs.$filename);
            }elseif ($img_type == IMAGETYPE_PNG ) {
                $source = imagecreatefrompng($tempname);            
                $resizeImg = image_resize($source,$imgProperties[0],$imgProperties[1]);
                imagepng($resizeImg,$pathToThumbs.$filename);
            } elseif ($img_type == IMAGETYPE_GIF ) {
                $source = imagecreatefromgif($tempname);
                $resizeImg = image_resize($source,$imgProperties[0],$imgProperties[1]);
                imagegif($resizeImg,$pathToThumbs.$filename);
            }
            if (!move_uploaded_file($tempname,$pathToImages.$filename)) {
                echo "<h3>  Failed to upload image!</h3>";
            }
        }
        $modified_by=$_SESSION['id'];
        $is_expired=0;
        $q_update_question="UPDATE `question` 
        SET `id`='$id',
            `quiz_id`='$quiz_id', 
            `question`='$question',
            `image`='$filename',
            `answer_type`='$answer_type_id',
            `modified_by` ='$modified_by',
            `last_update`='$last_update',
            `is_expired`='$is_expired' 
            WHERE `id`='$id'"; 
        
        //echo  $q_update_question;
        $r_q_update_question = mysqli_query($db->con, $q_update_question);//check query
        if(!$r_q_update_question) {
            echo "Entries  cannot added<br>";
        }
        //update options to the database
        $modified_by=$_SESSION['id'];
        $last_update=date("Y-m-d h:i:s");
        $is_expired=0;
            if(isset($_POST["add_ans_type"])){
                $add_ans_type=SanitizeValues($_POST["add_ans_type"]);
            }
            if($add_ans_type!=$answer_type && !empty($add_ans_type)
              || empty($_POST["options"])){
                //delete existing answers when change the answer type
                $q_del_answer="DELETE from `answers` WHERE `question_id`='$id'";
                echo $q_del_answer;
                $r_q_del_answer = mysqli_query($db->con, $q_del_answer);
                if(!$r_q_del_answer){ 
                    echo "Entries cannot delete ";
                }
                //update answer type when change the answer type
                $answer_type_id=Get_answer_type($add_ans_type); //get answer type
                $q_update_answer_type="UPDATE question SET `answer_type`='$answer_type_id'
                WHERE `id`='$id' ";
                echo  $q_update_answer_type;
                $r_q_update_question = mysqli_query($db->con, $q_update_answer_type);
                if($add_ans_type==1){ 
                    $i=0;
                    $num_of_answer=0;
                    foreach ($add_answers as $key=>$answer){
                        if($answer!=""){
                            echo $i."<br>";
                            echo $num_of_answer ."<br>"; 
                            $value=0;               
                            if(isset($_POST["is_correct"])){
                                if($_POST["is_correct"][$i]==strval($num_of_answer)){
                                    $value=1;
                                    $i++;
                                } else {
                                    $value=0; 
                                }
                            }
                            $q_insert_options=("INSERT INTO answers(`question_id`,`answer`,`is_correct`,`modified_by`,`last_update`,`is_expired`)
                            VALUES('$id','$answer','$value','$modified_by','$last_update','$is_expired')");
                            $r_q_insert_options = mysqli_query($db->con, $q_insert_options);//check query
                            echo $q_insert_options."<br>";
                            if(!$r_q_insert_options){ 
                                echo "Entries cannot added ";
                            } 
                        }
                        $num_of_answer++;
                    }
                }else{
                    $i=0;
                    $num_of_answer=0;
                    foreach ($add_answers as $key=>$answer){
                        if($answer!=""){
                            echo $i."<br>";
                            echo $num_of_answer ."<br>";  
                            $value=0;             
                            if(isset($_POST["is_correct_c"])){
                                //$num_of_answer=0;
                                echo $num_of_answer ."<br>"; 
                                if($_POST["is_correct_c"][$i]==strval($num_of_answer)){
                                    $value=1;
                                    $i++;
                                } else {
                                    $value=0; 
                                }
                            }
                            $num_of_answer++;
                            $q_insert_options=("INSERT INTO answers(`question_id`,`answer`,`is_correct`,`modified_by`,`last_update`,`is_expired`)
                            VALUES('$id','$answer','$value','$modified_by','$last_update','$is_expired')");
                            $r_q_insert_options = mysqli_query($db->con, $q_insert_options);//check query
                            echo $q_insert_options."<br>";
                            if(!$r_q_insert_options){ 
                                echo "Entries cannot added ";
                            } 
                        }
                    }  
                }
                header("Location:update_question.php?question_id=".$id."");
                exit();
            }elseif(!empty($_POST["options"])){
                $i=0;
                $num_of_answer=0;
                foreach ($answers as $key=>$answer){
                    if($answer!=""){
                        echo $i."<br>";
                        echo $num_of_answer ."<br>";
                        $value=0;                 
                        if(isset($_POST["is_correct"])){
                            if($_POST["is_correct"][$i]==strval($num_of_answer)){
                                $value=1;
                                $i++;
                            } else {
                                $value=0; 
                            }
                        }
                        $answer_id=$_POST["answer_id"][$num_of_answer];
                        $q_update_options="UPDATE answers
                        SET `id`='$answer_id',
                            `question_id`='$id',
                            `answer`='$answer',
                            `is_correct`='$value',
                            `modified_by`='$modified_by',
                            `last_update`='$last_update',
                            `is_expired`='$is_expired'
                            WHERE `id`='$answer_id'";
                        $r_q_update_options = mysqli_query($db->con, $q_update_options);//check query
                        echo $q_update_options."<br>";
                        if(!$r_q_update_options){ 
                            echo "Entries cannot added ";
                        }
                        $num_of_answer++;
                    }
                }
                if(!empty($add_answers) && $add_ans_type==$answer_type){
                    if($add_ans_type==1){
                        foreach ($add_answers as $key=>$answer){
                            if($answer!=""){
                                echo $i."<br>";
                                echo $num_of_answer ."<br>";                 
                                if(isset($_POST["is_correct"])){
                                    if($_POST["is_correct"][$i]==strval($num_of_answer)){
                                        $value=1;
                                        $i++;
                                    } else {
                                        $value=0; 
                                    }
                                }
                                $num_of_answer++;
                                $q_insert_options=("INSERT INTO answers(`question_id`,`answer`,`is_correct`,`modified_by`,`last_update`,`is_expired`)
                                VALUES('$id','$answer','$value','$modified_by','$last_update','$is_expired')");
                                $r_q_insert_options = mysqli_query($db->con, $q_insert_options);//check query
                                echo $q_insert_options."<br>";
                                if(!$r_q_insert_options){ 
                                    echo "Entries cannot added ";
                                } 
                            }
                        }
                    }else{
                        $i=0;
                        $num_of_answer=0;
                        foreach ($add_answers as $key=>$answer){
                            if($answer!=""){
                                echo $i."<br>";
                                echo $num_of_answer ."<br>";  
                                $value=0;             
                                if(isset($_POST["is_correct_c"])){
                                    //$num_of_answer=0;
                                    echo $num_of_answer ."<br>"; 
                                    if($_POST["is_correct_c"][$i]==strval($num_of_answer)){
                                        $value=1;
                                        $i++;
                                    } else {
                                        $value=0; 
                                    }
                                }
                                $num_of_answer++;
                                $q_insert_options=("INSERT INTO answers(`question_id`,`answer`,`is_correct`,`modified_by`,`last_update`,`is_expired`)
                                VALUES('$id','$answer','$value','$modified_by','$last_update','$is_expired')");
                                $r_q_insert_options = mysqli_query($db->con, $q_insert_options);//check query
                                echo $q_insert_options."<br>";
                                if(!$r_q_insert_options){ 
                                    echo "Entries cannot added ";
                                } 
                            }
                        } 
                    }
                }
            }
      
        header("Location:update_question.php?question_id=".$id."");
        exit();
    }
}
//Sanitize values
function SanitizeValues($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
//create canvas for image
function image_resize($source,$width,$height) {
    $new_width =800;
    $new_height =600;
    $thumbImg=imagecreatetruecolor($new_width,$new_height);
    imagecopyresampled($thumbImg,$source,0,0,0,0,$new_width,$new_height,$width,$height);
    return $thumbImg;
}
function Get_answer_type($data){
    $db= new DatabaseConnection;
    $q_get_anwer_type= ("SELECT id FROM `answer_type` WHERE value='$data'");
    //echo $q_get_anwer_type."<br>";
    $result_q_get_anwer_type =mysqli_query($db->con, $q_get_anwer_type);
    $n_raws_anwer_type = mysqli_num_rows($result_q_get_anwer_type);
    if(!$n_raws_anwer_type){
        echo "query cannot success<br>";
    }
    $row_anwer_type = mysqli_fetch_array($result_q_get_anwer_type);
    $answer_type_id=$row_anwer_type["id"];
    return $answer_type_id;
}
//Delete question records 
if (isset($_GET['delete_question_record'])) {
    echo 'success';
    $id = $_GET['delete_question_record'];
    echo $id;
    $q_del_question_image="UPDATE question SET `image`=''
    WHERE `id`='$id'";
    $r_q_del_question_image =mysqli_query($db->con, $q_del_question_image);//check query
    if ($r_q_del_question_image == TRUE) {
        echo "Record deleted successfully.";
        header("Location:update_question.php?question_id=".$id."");
    }else{
        echo "Error:" . $q_del_question_image . "<br>" . $db->con->error;
    }
}
//Delete answer records 
if (isset($_GET['delete_answer_record'])) {
    $answer_id = $_GET['delete_answer_record'];
    $q_get_answer_details = "SELECT question_id FROM `answers` WHERE `id`='$answer_id'";
    $r_q_get_answer_details =mysqli_query($db->con, $q_get_answer_details);
    $row_get_question_id = mysqli_fetch_array($r_q_get_answer_details);
    $question_id=$row_get_question_id["question_id"];
    echo $question_id;

    $q_del_answer="DELETE from `answers` WHERE `id`='$answer_id'";
    $r_q_del_answer = mysqli_query($db->con, $q_del_answer);
    if($r_q_del_answer){
        header("Location:update_question.php?question_id=".$question_id."");
    }else{
        echo "Entries cannot delete ";
    }
}


if (isset($_GET['question_id'])) {
    $id = $_GET['question_id']; 
    $q_get_question_details = "SELECT * FROM `question` WHERE `id`='$id'";
    $r_q_get_question_details =mysqli_query($db->con, $q_get_question_details);  

    $q_get_answer_details = "SELECT * FROM `answers` WHERE `question_id`='$id'";
    $r_q_get_answer_details =mysqli_query($db->con, $q_get_answer_details);
  
    if ($r_q_get_question_details->num_rows > 0) {
        while ($row = $r_q_get_question_details->fetch_assoc()) {
            $question_id = $row['id'];
            $quiz_id = $row['quiz_id'];
            $question = $row['question'];
            $image = SanitizeValues($row['image']);
            $answer_type_id = $row['answer_type'];

            $q_get_anwer_type= ("SELECT * FROM `answer_type` WHERE `id`='$answer_type_id'");
            //echo $q_get_anwer_type."<br>";
            $result_q_get_anwer_type =mysqli_query($db->con, $q_get_anwer_type);
            $row_anwer_type = mysqli_fetch_array($result_q_get_anwer_type);
            $answer_type_name=$row_anwer_type["type"];
            $ans_value=$row_anwer_type["value"];
        }
include ("header.php");   
?>

<script type="text/javascript">
    function delete_question_record(id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='update_question.php?delete_question_record='+id;
        }
    }
    function delete_answer_record(answer_id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='update_question.php?delete_answer_record='+answer_id;
        }
    }
</script>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Question Details</h4>
        </div>
        <div class="card-body">
            <div class="container mt-3 ">
            <?php
                // display error messages
                if(!empty($err))  {
                    echo "<div class='row'><div class='col-md-9'><div class='alert alert-danger'>";
                    foreach ($err as $error) {
                        echo "$error <br>";
                    }
                    echo "</div></div></div>";
                }
            ?> 
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="formi" enctype="multipart/form-data">
                    <div class="question">                    
                        <input type="hidden" id="form3Example3" value="<?php echo $question_id; ?>" name="id" class="form-control form-control-lg"
                            />                        
                        <div class="form-outline mb-3">
                            <input type="hidden" id="form3Example3" value="<?php echo $quiz_id; ?>" name="quiz_id" class="form-control form-control-lg"
                           />
                        </div>    
                        <div class="form-outline mb-3">
                            <label class="form-label" for="question">Question</label>
                            <input type="text" id="form3Example3" value="<?php echo $question; ?>" name="question" class="form-control form-control-lg"
                            placeholder="Enter question" />
                        </div>
                        <div class="form-outline mb-3">
                            <label class="form-label" for="answer_type_">Answer Type</label>
                            <input type="hidden" id="form3Example3" value="<?php echo $ans_value; ?>" name="ans_type" />
                            <input type="text" id="form3Example3" value="<?php echo $answer_type_name; ?>" class="form-control form-control-lg"
                           />
                        </div>
                        <div class="form-outline mb-3">
                            <label class="form-label" for="answer_type_">Answer </label>
                            <?php
                            if ($r_q_get_answer_details->num_rows > 0) {
                                $i=0;
                                while ($row = $r_q_get_answer_details->fetch_assoc()) {
                                    if($ans_value==1){                                    
                                        ?> 
                                        <div class="input-group mb-3" id="">
                                            <div class="input-group-text">
                                                <?php
                                                if($row["is_correct"]==1){
                                                ?>
                                                    <input type="radio" name="is_correct[]" id="check_answer" class="radio_reset_cls" value="<?= $i; ?>" checked="checked">
                                                <?php                                                
                                                }else{
                                                ?>
                                                    <input type="radio" name="is_correct[]" id="check_answer" class="radio_reset_cls" value="<?= $i; ?>">
                                                <?php                                                
                                                }?>                                           
                                            </div>
                                            <input type="hidden" class="form-control" placeholder="Answer_id" name="answer_id[]"  value="<?php echo $row["id"]; ?>">
                                            <input type="text" class="form-control" placeholder="Answer" name="options[]"  value="<?php echo $row["answer"]; ?>"> 
                                            <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_answer_record(<?php echo $row["id"]; ?>)" >
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash"></i>
                                                </span>                                        
                                            </a>               
                                        </div>
                                <?php
                                    }elseif($ans_value==2){
                                        ?> 
                                        <div class="input-group mb-3" id="">
                                            <div class="input-group-text">
                                            <?php
                                                if($row["is_correct"]==1){
                                                ?>
                                                    <input type="checkbox" name="is_correct[]" id="check_answer" class="checkbox_reset_cls"  value="<?= $i; ?>" checked="checked">
                                                <?php                                                
                                                }else{
                                                ?>
                                                <input type="checkbox" name="is_correct[]" id="check_answer" class="checkbox_reset_cls"  value="<?= $i; ?>">
                                                <?php                                                
                                                }?>                                         
                                            </div>
                                            <input type="hidden" class="form-control" placeholder="Answer_id" name="answer_id[]"  value="<?php echo $row["id"]; ?>">
                                            <input type="text" class="form-control" placeholder="Answer" name="options[]"  value="<?php echo $row["answer"]; ?>">
                                            <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_answer_record(<?php echo $row["id"]; ?>)" >
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash"></i>
                                                </span>                                        
                                            </a>                  
                                        </div>
                                <?php
                                    }                               
                                    $i++
                                ?>
                                <?php
                                }
                            }
                            ?>
                        </div>
                        <div class="answer_type">
                            <div class="form-outline mb-3">
                                <label class="form-label" for="answer_type">If you want to change or add answers select answer type</label><br>
                                <div id="radio_button">
                                    <input type="radio" name="add_ans_type" id="fixed_answer" value="1">
                                    &nbsp;Single Answer&nbsp;
                                </div>
                                <div id="radio_button">
                                    <input type="radio" name="add_ans_type" id="multiple_answers" value="2"
                                    >&nbsp;Multiple Answer&nbsp;
                                </div>
                                <div id="radio_button">
                                    <input type="radio" name="add_ans_type" id="text_answer" value="3"
                                    >&nbsp;text answer
                                </div>
                            </div>
                        </div>
                       
                        <div id="fixed">
                            <label class="form-label" for="fixed_answer">Enter answers and mark correct one</label><br>
                            <button class="btn btn-primary" type="button" id="add_answer_fixed_button">Add answer</button>
                            <div class="input-group mb-3 add_answer_fixed" id="add_answer_fixed">
                                <div class="input-group-text">
                                    <input type="radio" name="is_correct[]" id="check_answer" class="radio_reset_cls"  value="0">
                                </div>
                                <input type="text" class="form-control" id="answer" placeholder="Answer" name="add_options[]">
                                <button id="removeRow" type="button" class="btn btn-danger">Remove</button>
                            </div>
                        </div>
                        <div id="MCQ">
                            <label class=" form-label" id="" for="MCQ_answer">Enter answer and mark correct one</label><br>
                            <button class="btn btn-primary add_answer_check_box" id="add_answer_check_box" type="button">Add answer</button>
                            <div class="input-group mb-3" id="add_answer_MCQ">
                                <div class="input-group-text">
                                    <input type="checkbox" name="is_correct_c[]" id="check_answer" class="checkbox_reset_cls"  value="0">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer" name="add_options[]">
                                <button id="removeRow" type="button" class="btn btn-danger">Remove</button>
                            </div>                
                        </div>
                        <div class="form-outline mb-3">
                            <label for="description" class="form-label">Image</label>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="file" id="image" name="new_image" class="form-control form-control-lg" placeholder="Enter image " />
                                </div>
                                <div class=" col-sm-6">
                                    <?php
                                    if(!empty( $image)){ 
                                    ?>
                                        <img src=".././question_images/thumbs/<?php echo $image;?>" height="50" width="50" value="<?=$image;?>" id="imgD"/>
                                        <input type="hidden" name="image" value="<?=$image;?> " id="imgShow">
                                        <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_question_record(<?php echo $id; ?>)" id="delButton">
                                            <span class="icon text-white-50" id="delButton">
                                                <i class="fas fa-trash"></i>
                                            </span>                                        
                                        </a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>        
                    <div class="form-outline mb-3">
                        <input class="btn btn-primary btn-lg" type="submit" value="update" name="update" />
                    </div>
                </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
<?php 
    include ("footer.php");
 ?>
            </div>
            <!-- Bootstrap core JavaScript-->
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

            <!-- Core plugin JavaScript-->
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>
            
            <!-- Page level plugins -->
            <script src="vendor/chart.js/Chart.min.js"></script>

            <!-- Page level custom scripts -->
            <script type="text/javascript" src="js/sc.js"></script>
        </body>
    </html>
    <?php
    } else{
        $id = $_GET['question_id'];
       header('Location: update_question.php?question_id='.$id.'');
    } 
}
?>