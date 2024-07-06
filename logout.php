<?php

/*
a place to simply destroy session variables or all session variables

Unset specific session variables or destroy the entire session
unset($_SESSION['Username']); // Unset specific session variable
session_destroy(); // Destroy the entire session
Redirect the user to the login page or any other page after logout

*/

session_start();
session_destroy(); // Destroy the entire session
header("Location: login.php");
exit();
?>
