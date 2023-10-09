<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_DATABASE','mlrs');
    //connecting to the database
class DatabaseConnection{
    
    public function __construct(){
        $con =new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);

        if ($con->connect_error){
            die("Connection failed!");
        }
        //echo "connceted";
        return $this->con=$con;
    }
}
?>
