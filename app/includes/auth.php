<?php 
$self = $_SERVER['PHP_SELF'];
if(!strpos($self, "login.php") && !strpos($self, "register.php")){
    if(!isset($_SESSION['userdata']['id'])){
        redirect('./app/login.php');
    }
}
?>