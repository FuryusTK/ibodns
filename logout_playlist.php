<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}


session_destroy();
setcookie("auth", "");
header("location: login_mac.php");

?>