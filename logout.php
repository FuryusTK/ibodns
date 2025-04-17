<?php


session_start();
session_destroy();
setcookie("auth", "");
header("location: login.php");

?>