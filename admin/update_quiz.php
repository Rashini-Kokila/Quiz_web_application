<?php
include("session.php");
include ("dbConnection.php");
$db= new DatabaseConnection;

$filename='';
$err=array();
//get categories
$q_get_category="SELECT * FROM category WHERE is_expired=0";
$result_q_get_category =mysqli_query($db->con, $q_get_category);
if(!$result_q_get_category->num_rows> 0){
    echo "error";
}

if (isset($_POST['update'])){
    $id=$_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $category=$_POST['category'];

    $title=SanitizeValues($title);
    //time
    $time=SanitizeValues($time);
    if (empty($time)) {
        $err['time']="Time is required"; 
    }
    //
    $description=SanitizeValues($description);
    //image
    //$filename = SanitizeValues($_FILES["image"]["name"]);
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
        //insert questions to the databse
        $last_update=date("Y-m-d h:i:s");
        $pathToImages = ".././quiz_images/";
        $pathToThumbs = ".././quiz_images/thumbs/";

        if(empty($_FILES['new_image']['tmp_name']) && !empty($_POST['image'])){
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
        $q_update_quiz="UPDATE `quiz` 
        SET `title`='$title',
            `description`='$description',
            `time`='$time',
            `image`='$filename',
            `category_id`='$category',
            `modified_by`='$modified_by',
            `last_update`='$last_update',
            `is_expired`='$is_expired' 
            WHERE `id`='$id'"; 
        $r_q_update_quiz = mysqli_query($db->con, $q_update_quiz);//check query
        if($r_q_update_quiz) {
            $last_quiz_id = $db->con->insert_id;
            echo "Entries added!<br>";
            header("Location:update_quiz.php?id=".$id."");      
        }else {
            echo "Entries  cannot added<br>";
        }
    }
}
if(isset($_POST["set_sequence"])){
    $quiz_id=$_POST["quiz_id"];
    foreach ($_POST["sequence"] as $row=>$number){
        $numbers[]=SanitizeValues($number);
        echo $number. "<br>";
    }
    foreach ($_POST["questions"] as $row=>$qs_id){
        $qs_ids[]=SanitizeValues($qs_id);
        echo $qs_id. "<br>";
    }
    $temp=array_unique($numbers);
    if(sizeof($numbers)!=sizeof($temp)){
        $err['sequence']="numbers must be different";
    }
    if(empty($err)){
        $i=0;
        foreach ($numbers as $row => $number){
            echo $i;
            $question_id=$_POST["questions"][$row];
            //echo $row;
            $q_for_delete = "UPDATE question SET `sequence`='$number'
            WHERE `id`='$question_id' and `quiz_id`='$quiz_id'";
            echo $q_for_delete;
            $r_q_for_delete =mysqli_query($db->con, $q_for_delete);//check query
            if ($r_q_for_delete == TRUE) {
                echo "Record update successfully."; 
            }else{
                echo "Error:" . $q_for_delete . "<br>" . $db->con->error;
            }
            $i++;
        }
        header("Location:update_quiz.php?id=".$quiz_id."");
        exit();
    }else{
        header("Location:update_quiz.php?id=".$quiz_id."");
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
 //Delete question records 
 if (isset($_GET['delete_record'])) {
    $id = $_GET['delete_record'];
    $get_quiz_id="SELECT * FROM question WHERE `id`='$id'";
    $r_q_get_quiz_id =mysqli_query($db->con, $get_quiz_id);
    $row_get_quiz_id = mysqli_fetch_array($r_q_get_quiz_id);
    $quiz_id=$row_get_quiz_id["quiz_id"];

    $is_expired=1;
    $last_update=date("Y-m-d h:i:s");
    $q_for_delete = "UPDATE question SET `last_update`='$last_update',
    `is_expired`='$is_expired' WHERE `id`='$id'";

    $r_q_for_delete =mysqli_query($db->con, $q_for_delete);//check query
    if ($r_q_for_delete == TRUE) {
        echo "Record deleted successfully.";
        header("Location:update_quiz.php?id=".$quiz_id."");
    }else{
        echo "Error:" . $q_for_delete . "<br>" . $db->con->error;
    }
}
 //Delete quiz records 
 if (isset($_GET['delete_quiz_record'])) {
    $q_id = $_GET['delete_quiz_record'];
    echo $q_id;
    $q_del_quiz_image="UPDATE quiz SET `image`=''
    WHERE `id`='$q_id'";
    $r_q_del_quiz_image =mysqli_query($db->con, $q_del_quiz_image);//check query
    if ($r_q_del_quiz_image == TRUE) {
        echo "Record deleted successfully.";
        header("Location:update_quiz.php?id=".$q_id."");
    }else{
        echo "Error:" . $q_del_quiz_image . "<br>" . $db->con->error;
    }
}

if (isset($_GET['id'])) {
    $q_id = $_GET['id']; 
    $q_get_quiz_details = "SELECT * FROM `quiz` WHERE `id`='$q_id'";
    $r_q_get_quiz_details =mysqli_query($db->con, $q_get_quiz_details);  
  
    if ($r_q_get_quiz_details->num_rows > 0) {
        while ($row = $r_q_get_quiz_details->fetch_assoc()) {
            $q_id = $row['id'];
            $title = $row['title'];
            $description = $row['description'];
            $time= $row['time'];
            $image = SanitizeValues($row['image']);
            $category_id = $row['category_id'];

            $q_get_category_id="SELECT * FROM `category` WHERE `id`='$category_id'";
            $result_q_get_category_id =mysqli_query($db->con, $q_get_category_id);
            $n_raws_used_category = mysqli_num_rows( $result_q_get_category_id);
            if($n_raws_used_category){
                $row_category_id = mysqli_fetch_array($result_q_get_category_id);
                $category=$row_category_id["category_name"];
            }

            $q_get_quiz_question="SELECT * FROM `question` WHERE `quiz_id`='$q_id' AND `is_expired`=0";
            $r_q_get_quiz_question =mysqli_query($db->con, $q_get_quiz_question);
    }
    include ("header.php");
     
?>
<script type="text/javascript">
    function delete_record(id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='update_quiz.php?delete_record='+id;
        }
    }
    function delete_quiz_record(q_id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='update_quiz.php?delete_quiz_record='+q_id;
        }
    }
</script>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Update Quiz</h4>
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
                    <div class="form-outline mb-3">
                        <input type="hidden" id="form3Example3" value="<?php echo $q_id; ?>" name="id" class="form-control form-control-lg"
                             />
                        </div>   
                        <div class="form-outline mb-3">
                            <label class="form-label" for="question">Enter Title</label>
                            <input type="text" id="form3Example3" value="<?php echo $title; ?>" name="title" class="form-control form-control-lg"
                            placeholder="Enter quiz title" />
                        </div>
                        <div class="form-outline mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" rows="5" id="description" name="description" placeholder="Enter description"> <?php echo $description; ?></textarea>
                        </div>
                        <div class="form-outline mb-3">
                            <label for="description" class="form-label">Image</label>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="file" id="image" name="new_image" class="form-control form-control-lg" placeholder="Enter image " />
                                </div>
                                <div class=" col-sm-6">
                                    <?php
                                        if( !empty($image)){ 
                                        ?>
                                            <img src=".././quiz_images/thumbs/<?php echo $image;?>" height="50" width="50" value="<?=$image;?>"/>
                                            <input type="hidden" name="image" value="<?=$image;?>">
                                            <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_quiz_record(<?php echo $q_id; ?>)">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash"></i>
                                                </span>                                        
                                            </a>
                                    <?php
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <div class="form-outline mb-3">
                                    <label class="form-label" for="time">Time</label>
                                    <input type="text" id="myTextBox" value="<?php echo $time; ?>" name="time" class="form-control form-control-lg"
                                    placeholder="Enter Time" />
                                    <small id="emcheck" class="form-text text-muted invalid-feedback errorc">                
                                    numbers only
                                </small> 
                                </div>
                            </div>
                            <div class=" col-sm-6">
                                <div class="form-outline mb-3">
                                    <label class="form-label" for="question">Select Category</label>
                                    <select id="category" name="category" class="form-control form-control-lg ">
                                    <?php
                                    echo '<option value="'.$category_id.'">'.$category.'</option>';
                                    if($result_q_get_category->num_rows > 0){
                                        while($row_category= $result_q_get_category->fetch_assoc()){
                                            echo '<option value="'.$row_category["id"].'">'.$row_category["category_name"].'</option>';
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>       
                    <div class="form-outline mb-3">
                        <input class="btn btn-primary btn-lg" type="submit" value="Edit Quiz " name="update" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

    <!-- Begin Page Content -->
    <div class="container-fluid">
            <div class="card shadow mb-4">
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
                <div class="card-header py-3">
                    <h4 class="m-0 font-weight-bold text-primary">Questions of quiz</h4>
                    <a class="btn btn-info btn-icon-split btn-sm float-right" href="question.php?last_quiz_id=<?php echo $q_id; ?>">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus">Add Question</i>
                        </span>                                        
                    </a>                   
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="formi" enctype="multipart/form-data"> 
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Quiz Title</th>
                                        <th>Quiz Sequence
                                            <input class="btn btn-info btn-sm float-right" type="submit" value="Add sequence" name="set_sequence" />
                                        </th>
                                        <th>Question</th>
                                        <th>Image</th>
                                        <th>Answer type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>                                
                                    <?php
                                        if ($r_q_get_quiz_question->num_rows> 0) {
                                            while ($row_q = $r_q_get_quiz_question->fetch_assoc()) { 
                                                $answer_type_id=$row_q['answer_type']; //get answer type
                                                $q_get_anwer_type= ("SELECT * FROM `answer_type` WHERE id='$answer_type_id'");
                                                $result_q_get_anwer_type =mysqli_query($db->con, $q_get_anwer_type);
                                                $row_anwer_type = mysqli_fetch_array($result_q_get_anwer_type);
                                    ?>
                                            <tr>
                                                <td><?php echo $row_q['id']; ?></td>
                                                <td><?php echo $title; ?></td>
                                                <td>
                                                <div class="input-group mb-3">
                                                    <input type="hidden" name="quiz_id" value="<?php echo $row_q['quiz_id']; ?>">
                                                    <input type="hidden" name="questions[]" value="<?php echo $row_q['id']; ?>">
                                                    <input class="form-control form-control-sm" type='text' name='sequence[]' value="<?php echo $row_q['sequence']; ?>">
                                                </div>                                             
                                                </td>
                                                <td><?php echo $row_q['question']; ?></td>                                            
                                                <td><?php                                                
                                                        echo "<img src='.././question_images/thumbs/".$row_q['image']."' width='50' height='50'>";
                                                    ?>
                                                </td>
                                                <td><?php echo $row_anwer_type['type']; ?></td>
                                                <td>
                                                    <div class="btn-group">                            
                                                        <a class="btn btn-primary btn-icon-split btn-sm" href="update_question.php?question_id=<?php echo $row_q['id']; ?>">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-edit">Update</i>
                                                            </span>
                                                        </a>
                                                        <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_record(<?php echo $row_q['id']; ?>)">
                                                            <span class="icon text-white-50">
                                                                <i class="fas fa-trash"></i>
                                                            </span>                                        
                                                        </a>
                                                    </div>                                            
                                                </td>
                                            </tr>
                                    <?php  }
                                        }else{
                                            echo " unsuccess";
                                        }
                                    ?>  
                                </tbody>
                            </form>
                        </table>
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
<?php
    } 
}
?>