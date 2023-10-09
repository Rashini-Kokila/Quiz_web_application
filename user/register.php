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
    $last_modified=date("Y-m-d h:i:s");
    $is_expired=0;
    // Securing password using password_hash
    $secure_pass = md5($password);//password_hash($password, PASSWORD_BCRYPT);
    echo $secure_pass;
    $sql =("INSERT INTO user ( `user_name`, `email`, `password`,`last_modified`,`is_expired`) 
    VALUES ('$name','$email','$secure_pass','$last_modified','$is_expired')");
    //echo $sql;
    
    $query = mysqli_query($db->con, $sql);//check query
    if($query) {
      echo "Entries added!<br>";
      header("Location: login.php");
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
<html>
    <head>
        <title>Registration Page</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <style>
        .errorc{color:#FF0000;}
        </style>
      </head>
    <body>
      <section class="vh-100" style="background-color: #eee;">
        <div class="container h-100">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
              <div class="card text-black" style="border-radius: 25px;">
                <div class="card-body p-md-5">
                  <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
      
                      <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>
      
                      <form class="mx-1 mx-md-4" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="myform">
      
                        <div class="d-flex flex-row align-items-center mb-4">
                            <div class="form-outline flex-fill mb-0">
                              <span class="errorc">
                                <?php echo $err['name']??'' ?>
                              </span>
                                <input type="text" name="name" id="form3Example1c" class="form-control" />
                                <label class="form-label" for="form3Example1c">Your Name</label>
                          </div>
                        </div>
      
                        <div class="d-flex flex-row align-items-center mb-4">
                            <div class="form-outline flex-fill mb-0">
                              <span class="errorc">
                                <?php echo $err['email']??'' ?>
                              </span>
                                <input type="email" id="form3Example3c" name="email" class="form-control" />
                                <label class="form-label" for="form3Example3c">Your Email</label>
                            </div>
                        </div>
      
                        <div class="d-flex flex-row align-items-center mb-4">
                            <div class="form-outline flex-fill mb-0">
                              <span class="errorc">
                                <?php echo $err['password']??'' ?>
                              </span>
                                <input type="password" id="form3Example4c" name="password" class="form-control" />
                                <label class="form-label" for="form3Example4c">Password</label>
                            </div>
                        </div>
      
                        <div class="d-flex flex-row align-items-center mb-4">
                            <div class="form-outline flex-fill mb-0">
                              <span class="errorc">
                                <?php echo $err['comPassword']??'' ?>
                              </span>
                                <input type="password" id="form3Example4cd" name="comPassword" class="form-control" />
                                <label class="form-label" for="form3Example4cd">Repeat your password</label>
                            </div>
                        </div>
      
                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                          
                          <button type="submit" class="btn btn-primary btn-lg">Register</button>
                          
                        </div>
      
                      </form>
      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </body>
</html>