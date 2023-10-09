<?php
include("session.php");
require_once('dbConnection.php');
$db= new DatabaseConnection;

if (isset($_POST['update'])){
    $id=$_POST['id'];
    $category = $_POST['category'];
    $modified_by=$_SESSION['id'];
    $last_update=date("Y-m-d h:i:s");

    $q_update_category="UPDATE `category` 
        SET `category_name`='$category',
        `modified_by`='$modified_by',
        `last_update`='$last_update' WHERE `id`='$id'"; 
        $r_q_update_category = mysqli_query($db->con, $q_update_category);//check query
        echo $q_update_category;
        if(!$r_q_update_category) {
            echo "Entries  cannot added<br>";   
        }else {
            header("Location:categ.php"); 
        }
}

if (isset($_GET['id'])) {
    $id = $_GET['id']; 
    //get categories
    $q_get_category="SELECT * FROM category WHERE `id`='$id'";
    $result_q_get_category =mysqli_query($db->con, $q_get_category);

    if ($result_q_get_category->num_rows> 0) {  
        while($row_category=$result_q_get_category->fetch_assoc()){
            $category_id = $row_category['id'];
            $category_name = $row_category['category_name'];
        }
    }

}
include ("header.php");
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">Edit Category</h4>
        </div>
        <div class="card-body">
            <div class="container mt-3 ">                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="formi" enctype="multipart/form-data">
                    <div class="form-outline mb-3">
                        <label class="form-label" for="question">Edit Category</label>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" id="new_category" value="<?php echo $category_name; ?>" class="form-control" name="category">
                                <input type="hidden" id="new_category" value="<?php echo $category_id; ?>"  name="id">
                            </div>
                            <div class=" col-sm-6">
                                <input class="btn btn-primary" type="submit" value="update " name="update" />
                            </div>
                        </div>
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
    
    <script type="text/javascript" src="js/sc.js"></script>
        </body>
    </html>