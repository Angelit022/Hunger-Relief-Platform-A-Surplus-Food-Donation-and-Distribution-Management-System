<?php
//Initialize the session
session_start();

//Unset all the session variable
$_SESSION = array();

//end the session
session_destroy();

//Redirect to the homepage
header("location: index.php");
exit();
?>
