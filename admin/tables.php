<?php
include("session.php"); 
include("dbConnection.php");

$db= new DatabaseConnection;
//view records
$q_get_quiz_details= "SELECT * FROM quiz WHERE is_expired=0
ORDER BY id DESC";
$r_q_get_quiz_details =mysqli_query($db->con, $q_get_quiz_details);

//Delete records
if (isset($_GET['delete_record'])) {
    $id = $_GET['delete_record'];
    $is_expired=1;
    $last_update=date("Y-m-d h:i:s");
    $q_for_delete = "UPDATE quiz SET `last_update`='$last_update',
    `is_expired`='$is_expired' WHERE `id`='$id'";

    $r_q_for_delete =mysqli_query($db->con, $q_for_delete);//check query
    if ($r_q_for_delete == TRUE) {
        echo "Record deleted successfully.";
        header('Location:tables.php');
    }else{
        echo "Error:" . $q_for_delete . "<br>" . $db->con->error;
    }
}
include ("header.php");

 ?>
 <script type="text/javascript">
    function delete_record(id){
        if(confirm('Are You Sure to Delete this Record?')){
            window.location.href='tables.php?delete_record='+id;
        }
    }
</script>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card shadow mb-4">
        <a class="btn btn-success btn-icon-split btn-block" href="quiz.php">
            <span class="icon text-white-50 py-3">
                <i class="fas fa-plus">Add Quiz</i>
            </span>
        </a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h3 class="m-0 font-weight-bold text-primary">Quiz Details</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Time</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th> ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Time</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody> 
                    <?php
                        if ($r_q_get_quiz_details->num_rows> 0) {
                            while ($row = $r_q_get_quiz_details->fetch_assoc()) { 
                    ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['time']; ?></td>
                                <td><?php echo "<img src='.././quiz_images/thumbs/".$row['image']."' width='50' height='50'>"; ?>
                                </td>
                                <?php
                                $category_id=$row['category_id'];
                                $q_get_category_id="SELECT `*` FROM `category` WHERE `id`='$category_id'";                           
                                $result_q_get_category_id =mysqli_query($db->con, $q_get_category_id);
                                $n_raws_aget_category_id = mysqli_num_rows($result_q_get_category_id);
                                if($n_raws_aget_category_id){
                                        $row_category_id = mysqli_fetch_array($result_q_get_category_id);                            
                                ?>
                                        <td><?php echo $row_category_id["category_name"]; ?></td>
                                <?php
                                }else{
                                    ?>
                                        <td>empty category</td>
                                <?php
                                }?>
                                <td>
                                    <div class="btn-group">                            
                                        <a class="btn btn-primary btn-icon-split btn-sm" href="update_quiz.php?id=<?php echo $row['id']; ?>">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-edit">Update</i>
                                            </span>
                                        </a>
                                        <a class="btn btn-info btn-icon-split btn-sm" href="question.php?last_quiz_id=<?php echo $row['id']; ?>">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-plus">Add Question</i>
                                            </span>                                        
                                        </a>
                                        <a class="btn btn-danger btn-icon-split btn-sm" href="javascript: delete_record(<?php echo $row['id']; ?>)">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-trash"></i>
                                            </span>                                        
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    <?php  }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->


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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

</body>

</html>