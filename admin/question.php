<?php
include("session.php");
include("dbConnection.php");

$db= new DatabaseConnection;
$err=array();
$file_ext='';
$filename='';
$tempname='';
$last_quiz_id='';
if (isset($_GET['last_quiz_id'])){
    $last_quiz_id=$_GET['last_quiz_id'];
}
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $question = $_POST['question'];

    if(isset($_POST['ans_type'])){
        $answer_type=$_POST['ans_type'];
    }

    $last_quiz_id=$_POST['last_quiz_id'];

    //question validation
    $question=SanitizeValues($question);
    if (empty($question)) {
        $err['question']="Question is required"; 
    }
    //answer validation
    if (isset($_POST["options"])){
        foreach ($_POST["options"] as $row=>$option){
        $answers[]=SanitizeValues($option);
        }
    }
    //image
    $filename = SanitizeValues($_FILES["image"]["name"]);
    if(!empty($filename)){
        $allowed_types = array('jpg', 'png', 'jpeg', 'gif');
        $maxsize = 2 * 1024 * 1024;
        //$filename = $_FILES["image"]["name"];
        $tempname = $_FILES["image"]["tmp_name"];
        $file_size = $_FILES['image']['size'];
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
        //check answer type
        $q_get_anwer_type= ("SELECT id FROM `answer_type` WHERE value='$answer_type'");
        echo $q_get_anwer_type;
        $result_q_get_anwer_type =mysqli_query($db->con, $q_get_anwer_type);
        $n_raws_anwer_type = mysqli_num_rows($result_q_get_anwer_type);
        if(!$n_raws_anwer_type){
            echo "query cannot success<br>";
        }
        $row_anwer_type = mysqli_fetch_array($result_q_get_anwer_type);
        $answer_type_id=$row_anwer_type["id"];

        //insert questions to the databse
        $last_update=date("Y-m-d h:i:s");
        $pathToImages = ".././question_images/";
        $pathToThumbs = ".././question_images/thumbs/";

        if(!empty($tempname)){    
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
            // move the uploaded image into the folder: image
            if (!move_uploaded_file($tempname,$pathToImages.$filename)) {
                echo "<h3>  Failed to upload image!</h3>";
            }
        }
        $is_expired=0;
        $sequence=0;
        $modified_by=$_SESSION['id'];
        $q_insert_question=("INSERT INTO question ( `quiz_id`,`sequence`,`question`, `image`,`answer_type`,`modified_by`,`last_update`,`is_expired`) 
        VALUES ('$last_quiz_id','$sequence','$question','$filename','$answer_type_id','$modified_by','$last_update','$is_expired')");
        echo  $q_insert_question;
        $r_q_insert_question = mysqli_query($db->con, $q_insert_question);//check query
        if($r_q_insert_question) {
            $last_id = $db->con->insert_id;
            echo "Entries added!<br>";      
        }else {
            echo "Entries  cannot added<br>";
        }
        //insert options to the database
        $modified_by=$_SESSION['id'];
        $last_update=date("Y-m-d h:i:s");
        $is_expired=0;
        $i=0;
        $num_of_answer=0;
        foreach ($answers as $key=>$answer){
            if($answer!=""){
                echo $i."<br>";
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
                VALUES('$last_id','$answer','$value','$modified_by','$last_update','$is_expired')");
                $r_q_insert_options = mysqli_query($db->con, $q_insert_options);//check query
                echo $q_insert_options."<br>";
                if(!$r_q_insert_options){ 
                    echo "Entries cannot added ";
                }
                $num_of_answer++;
            }
        }
        header("Location:tables.php");
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
include ("header.php");
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary"> Add Question</h4>
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
                        <input type="hidden" name="last_quiz_id" value="<?php echo $last_quiz_id; ?>"/>   
                        <div class="form-outline mb-3">
                            <label class="form-label" for="question">Add a question</label>
                            <input type="text" id="form3Example3" name="question" class="form-control form-control-lg"
                            placeholder="Enter question" />
                        </div>
                        <div class="answer_type">
                            <div class="form-outline mb-3">
                                <label class="form-label" for="answer_type">Select answer type</label><br>
                                <div id="radio_button">
                                    <input type="radio" name="ans_type" id="fixed_answer" value="1">
                                    &nbsp;Single Answer&nbsp;
                                </div>
                                <div id="radio_button">
                                    <input type="radio" name="ans_type" id="multiple_answers" value="2"
                                    >&nbsp;Multiple Answer&nbsp;
                                </div>
                                <div id="radio_button">
                                    <input type="radio" name="ans_type" id="text_answer" value="3"
                                    >&nbsp;Text answer
                                </div>
                            </div>
                        </div>
                        <!--options-->  
                        <div id="fixed">
                            <label class="form-label" for="fixed_answer">Enter answers and mark correct one</label><br>
                            <button class="btn btn-success" type="button" id="add_answer_fixed_button">Add answer</button>
                            <div class="input-group mb-3 " id="add_answer_fixed">
                                <div class="input-group-text">
                                    <input type="radio" name="is_correct[]" id="check_answer" class="radio_reset_cls"  value="0">
                                </div>
                                <input type="text" class="form-control" id="answer" placeholder="Answer" name="options[]">
                                <button id="removeRow" type="button" class="btn btn-danger">Remove</button>
                            </div>
                        </div>
                        <div id="MCQ">
                            <label class=" form-label" id="" for="MCQ_answer">Enter answer and mark correct one</label><br>
                            <button class="btn btn-primary" id="add_answer_check_box" type="button">Add answer</button>
                            <div class="input-group mb-3" id="add_answer_MCQ">
                                <div class="input-group-text">
                                    <input type="checkbox" name="is_correct[]" id="check_answer" class="checkbox_reset_cls"  value="0">
                                </div>
                                <input type="text" class="form-control" placeholder="Answer" name="options[]">
                                <button id="removeRow" type="button" class="btn btn-danger">Remove</button>
                            </div>                
                        </div>   
                        <div class="form-outline mb-3">
                            <label class="form-label" for="images">Add image</label>
                            <input type="file" id="image" name="image" class="form-control form-control-lg" placeholder="Enter image " />
                        </div>
                    </div>        
                    <div class="form-outline mb-3">
                        <input class="btn btn-primary btn-lg" type="submit" value="Add Question" name="submit" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->
        <?php 
            include ("footer.php");
        ?>
                    <!-- Bootstrap core JavaScript-->
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

            <!-- Core plugin JavaScript-->
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>

            <!-- Page level custom scripts -->
            <script type="text/javascript" src="js/sc.js"></script>
        </body>
    </html>