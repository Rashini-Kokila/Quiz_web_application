<?php
include("session.php");
include("dbConnection.php");

$db= new DatabaseConnection;
$err=array();
$category='';
$filename='';

//get categories
$q_get_category="SELECT * FROM category WHERE is_expired=0";
$result_q_get_category =mysqli_query($db->con, $q_get_category);

if(isset($_POST["add_quiz"])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $time = $_POST['time'];
    $category=$_POST['category'];

    //title
    $title=SanitizeValues($title);
    if (empty($title)) {
        $err['title']="Title is required"; 
    }
    //validate description
    $description=SanitizeValues($description);
    if (empty($description)) {
        $err['description']="Description is required"; 
    }

    //time
    $time=SanitizeValues($time);
    if (empty($time)) {
        $err['time']="Time is required"; 
    }elseif (!is_numeric($time)) {
        $err['time']="Numbers only"; 
    }
    //image
    $filename = SanitizeValues($_FILES["image"]["name"]);
    if(!empty($filename)){
        $allowed_types = array('jpg', 'png', 'jpeg', 'gif');
        $maxsize = 2 * 1024 * 1024;
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
        //insert questions to the databse
        $last_update=date("Y-m-d h:i:s");
        $pathToImages = ".././quiz_images/";
        $pathToThumbs = ".././quiz_images/thumbs/";

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
         $modified_by=$_SESSION['id'];
        $is_expired=0;
        $q_insert_quiz=("INSERT INTO quiz ( `title`, `description`,`time`,`image`,`category_id`,`modified_by`,`last_update`,`is_expired`) 
        VALUES ('$title','$description','$time','$filename','$category','$modified_by','$last_update','$is_expired')");
        //echo  $q_insert_quiz;
        $r_q_insert_quiz = mysqli_query($db->con, $q_insert_quiz);//check query
        if($r_q_insert_quiz) {
            $last_quiz_id = $db->con->insert_id;
            //echo "Entries added!<br>";      
        }else {
            echo "Entries  cannot added<br>";
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
            <h4 class="m-0 font-weight-bold text-primary">Add Quiz</h4>
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
                    <div class="form-outline mb-3">
                        <label class="form-label" for="question">Enter Title</label>
                        <input type="text" id="form3Example3" name="title" class="form-control form-control-lg"
                        placeholder="Enter quiz title" />
                    </div>
                    <div class="form-outline mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" rows="5" id="description" name="description" placeholder="Enter description"></textarea>
                    </div>
                    <div class="form-outline mb-3">
                        <label class="form-label" for="images">Add image</label>
                        <input type="file" id="image" name="image" class="form-control form-control-lg" placeholder="Enter image " />
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="form-outline mb-3">
                                <label class="form-label" for="time">Time</label>
                                <input type="text" id="myTextBox" name="time" class="form-control form-control-lg"
                                placeholder="Enter Time"/>
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
                                    echo'<option> Select..</option>';
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
                    <br>
                    <div class="form-outline mb-3">
                        <input class="btn btn-primary btn-lg" type="submit" value="Add Quiz " name="add_quiz" />
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