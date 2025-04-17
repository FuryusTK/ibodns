<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);

if($_SESSION['store_type'] == 2){
    header("Location: users_mac.php");
}

error_reporting(32767);
$db = new SQLite3("./api/.ansdb.db");
$db->exec("CREATE TABLE IF NOT EXISTS ibo(id INTEGER PRIMARY KEY NOT NULL,mac_address VARCHAR(100),key VARCHAR(100),username VARCHAR(100),password VARCHAR(100),expire_date VARCHAR(100),dns VARCHAR(100),epg_url VARCHAR(100),title VARCHAR(100),url VARCHAR(100), type VARCHAR(100), id_user INT)");
$res = $db->query("SELECT * FROM ibo WHERE id_user = " . $_SESSION['id']);
if (isset($_GET["delete"])) {
    $db->exec("DELETE FROM ibo WHERE id=" . $_GET["delete"]);
    $db->close();
    header("Location: users.php");
}
include "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .iframe-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%; /* Proporção 16:9 (ajuste conforme necessário) */
        }

        .responsive-iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

<div class="iframe-container">
    <iframe class="responsive-iframe" src="https://go.aftvnews.com" frameborder="0" allowfullscreen></iframe>
</div>

</body>
</html>

<?php
include "includes/footer.php";
?>