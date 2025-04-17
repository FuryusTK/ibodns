<?php
session_start();

if (isset($_SESSION['id'])) {
	header("Location: users.php");
}else{
	header("Location: login.php");
}

?>