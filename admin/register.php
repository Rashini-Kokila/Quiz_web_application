<?php
include("dbConnection.php");

$db= new DatabaseConnection;
$err=array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  //get the values
  $name = $_REQUEST['name'];
  $email= $_REQUEST['email'];
  $password= $_REQUEST['password'];
  $comPassword= $_REQUEST['comPassword'];

  //name validation
  $name=SanitizeValues($name);
  if (empty($name)) {
      $err['name']="Name is required"; 
  }else{
      $length = strlen(trim($name));
      if ( $length < 3 || $length > 250) {
          $err['name']="length of name must be between 3 and 250!";
      }
  }
  //email validation
  $email=SanitizeValues($email);
    if(empty($email)) {
        $err['email']="email is required"; 
    }else {
        if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) {
            $err['email']="Enter valid email address";
        }else{
          $sql1 = "SELECT * FROM user WHERE email='$email' AND password='$password'";
          $r_query=mysqli_query($db->con, $sql1);
          if(mysqli_num_rows($r_query)){
            $err['email']="The email address already exists";
          }
        }
    }
  //password validation
  $password=SanitizeValues($password);
  if (empty($password)) {
    $err['password']="password is required";
  }else{
    if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password)) {
      $err['password']= "Password must be at least 8 characters in length must contain at least one number,
      one upper case letter, one lower case letter and one special character.";
     }
  }

  // form validation for confirm password
  $comPassword=SanitizeValues($comPassword);
  if($comPassword!=$password){
    $err['comPassword']="Confirm Password does not Matched";
  }

  //insert data after all validation
  if(empty($err)){
    $last_update=date("Y-m-d h:i:s");
    $is_expired=0;
    // Securing password using password_hash
    $secure_pass = md5($password);//password_hash($password, PASSWORD_BCRYPT);
    echo $secure_pass;
    $sql =("INSERT INTO `admin` ( `user_name`, `email`, `password`,`modified_by`,`last_updat`,`is_expired`) 
    VALUES ('$name','$email','$secure_pass','$last_update','$is_expired')");
    //echo $sql;
    
    $query = mysqli_query($db->con, $sql);//check query
    if($query) {
      echo "Entries added!<br>";
      header("Location: index.php");
    }else {
      echo "Entries  cannot added<br>";
    }
  }

}
function SanitizeValues($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
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
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="myform">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control form-control-user" id="exampleFirstName"
                                    placeholder="Name">                                    
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                                    name="email" placeholder="Email Address">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="exampleInputPassword" name="password" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="exampleRepeatPassword" name="comPassword" placeholder="Repeat Password">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">Register</button>
                                <hr>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>