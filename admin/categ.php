<?php
include("session.php");
include("dbConnection.php");

$db= new DatabaseConnection;
$err=array();

//get categories
$q_get_category="SELECT * FROM category WHERE is_expired=0";
$result_q_get_category =mysqli_query($db->con, $q_get_category);

if(isset($_POST["save"])){
    $new_category=$_POST['new_category'];

    //category validation
    $new_category=SanitizeValues($new_category);
    if (empty($new_category)) {
        $err['new_category']="Field is empty"; 
    }
    if(empty($err)){
        $modified_by=$_SESSION['id'];
        $is_expired=0;
        $last_update=date("Y-m-d h:i:s");
        $get_used_category="SELECT * FROM category WHERE `category_name`='$new_category' 
        and 'is_expired'=0";
        $r_q_get_used_category =mysqli_query($db->con, $get_used_category);
        $n_raws_used_category = mysqli_num_rows($r_q_get_used_category);
        if($n_raws_used_category){
            $err['new_category']="This category is already entered";
        }else{
            $q_insert_new_category="INSERT INTO category (`category_name`,`modified_by`, `last_update`,`is_expired`)
             VALUES ('$new_category','$modified_by','$last_update','$is_expired')";
            //echo $q_insert_new_category;
            $r_q_insert_new_category = mysqli_query($db->con, $q_insert_new_category);
            if(!$r_q_insert_new_category){
                echo "Entries cannot added ";
            }else{
                header("Location:categ.php");
                exit(); 
            }
        }
    }
}
//Sanitize values
function SanitizeValues($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//Delete records
if (isset($_GET['delete_record'])) {
    $id = $_GET['delete_record'];
    $is_expired=1;
    $last_update=date("Y-m-d h:i:s");
    $get_used_category="SELECT * FROM quiz WHERE `category_id`='$id'";
    $r_q_get_used_category =mysqli_query($db->con, $get_used_category);
    $n_raws_used_category = mysqli_num_rows($r_q_get_used_category);
    if($n_raws_used_category){
        $err['delete']="This category is already used in quiz";
    }else{
        $q_for_delete = "UPDATE category SET `last_update`='$last_update',
        `is_expired`='$is_expired' WHERE `id`='$id'";
        $r_q_for_delete =mysqli_query($db->con, $q_for_delete);//check query
        if ($r_q_for_delete == TRUE) {
            echo "Record deleted successfully.";
            header('Location:categ.php');
        }else{
            echo "Error:" . $q_for_delete . "<br>" . $db->con->error;
        } 
    }
}

include ("header.php");
?>
<script type="text/javascript">
    function delete_record(id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='categ.php?delete_record='+id;
        }
    }
</script>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Add Category</h4>
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
                        <label class="form-label" for="question">Enter Category</label>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" id="new_category" class="form-control" name="new_category">
                            </div>
                            <div class=" col-sm-6">
                                <input class="btn btn-primary" type="submit" value="save " name="save" />
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th> Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                            <?php
                            if ($result_q_get_category->num_rows> 0) {  
                                while($row_category=$result_q_get_category->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo $row_category['category_name']; ?></td>
                                    <td>
                                        <div class="btn-group">                            
                                            <a class="btn btn-primary btn-icon-split btn-sm" href="update_category.php?id=<?php echo $row_category['id']; ?>">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-edit"></i>
                                                </span>
                                            </a>
                                            <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_record(<?php echo $row_category['id']; ?>)">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash"></i>
                                                </span>                                        
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                }
                                    }
                                    ?>
                            </tbody>
                        </table>
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
        </body>
    </html>