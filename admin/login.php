<?php
include("dbConnection.php");

$db= new DatabaseConnection;
$err=array();
$id='';

  //var_dump($_COOKIE);
  if(isset($_COOKIE["user_login"])) {
    $value=$_COOKIE["user_login"];

    $q_get_cookie_data=("SELECT * FROM cookies_data WHERE cookie_det='".$value."'"); 
    $r_query_get_cookie_data=mysqli_query($db->con, $q_get_cookie_data);
    $row = $r_query_get_cookie_data->fetch_assoc();
    $user_id=$row['user_id'];

    $q_get_user_data = ("SELECT * FROM `admin` WHERE id ='".$user_id."'");
    $r_query_get_user_data=mysqli_query($db->con, $q_get_user_data);
    $rs_query_get_user_data = mysqli_num_rows($r_query_get_user_data);
    $r_query_get_user_data = mysqli_fetch_array($r_query_get_user_data);
    if($rs_query_get_user_data){
      session_start();
      $_SESSION['email'] = $r_query_get_user_data['email'];
      $_SESSION['user_name'] = $r_query_get_user_data['user_name'];
      $_SESSION['id'] = $r_query_get_user_data['id'];
      header("Location:dashboard.php");

    }
  }else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email= $_REQUEST['email'];
      $password= $_REQUEST['password'];
      
      //validate email
      $email=SanitizeValues($email);
      if (empty($email)) {
        $err['email']="please enter your email here"; 
      }

      //validate password
      $password=SanitizeValues($password);
      if (empty($password)) {
        $err['password']="please enter your password here"; 
      }

      // Check Email/Username exists Or not
      $secure_pass = md5($password);
      $q_get_user_deta_for_check = "SELECT * FROM `admin` 
      WHERE email='$email' AND password='$secure_pass'";
      $r_query_get_user_deta_for_check = mysqli_query($db->con, $q_get_user_deta_for_check);

      if (mysqli_num_rows($r_query_get_user_deta_for_check) == 1) {
        $row = mysqli_fetch_array($r_query_get_user_deta_for_check);
      }else{
        $err['email']="Check your email and password";
      }

      if(empty($err)){
        if ($row['email'] === $email && $row['password'] === $secure_pass) {
          echo "Logged in!";
          $id=$row['id'];
          session_start();
          $_SESSION['email'] = $row['email'];
          $_SESSION['user_name'] = $row['user_name'];
          $_SESSION['id'] = $row['id'];

          // Check user click or not click Remember me Checkbox Button
          if (isset($_POST['remember'])) { //if user click or checked checkbox then set cookies
            $spacialKey=$id."cookie##sdfdskljljeruyoe";
            setcookie('user_login',"$spacialKey",time()+60*60*24*30);
            //insert cookie details to the dtabase
              $sql =("INSERT INTO cookies_data (`cookie_det`,`id`) 
              VALUES ('$spacialKey','$id')");      
              $query = mysqli_query($db->con, $sql);//check query
              if($query) {
                echo "Entries added!<br>";
              }else {
                echo "Entries  cannot added<br>";
              }

          }else{
            if (isset($_COOKIE["user_login"])) {
              setcookie("user_login", "");
            }
            header("Location:dashboard.php");

          }
          // redirect to welcome page
        header("Location: dashboard.php");
        exit();
        }
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

    <title>SB Admin 2 - Login</title>

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

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
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
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="myform">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                name="email" placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="exampleInputPassword" name="password" placeholder="Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" name="remember" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" name ="submit" id="submit " value="submit" class="btn btn-primary btn-user btn-block"
                                        style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                                        <hr>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
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