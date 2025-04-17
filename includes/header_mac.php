<?php


session_start();
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];

$dbans = new SQLite3("./api/.ansdb.db");
$dbans->exec("CREATE TABLE IF NOT EXISTS ibo(id INTEGER PRIMARY KEY NOT NULL,mac_address VARCHAR(100),key VARCHAR(100),username VARCHAR(100),password VARCHAR(100),expire_date VARCHAR(100),dns VARCHAR(100),epg_url VARCHAR(100),title VARCHAR(100),url VARCHAR(100), type VARCHAR(100), id_user INT)");
$dbans->exec("CREATE TABLE IF NOT EXISTS playlist(id INTEGER PRIMARY KEY NOT NULL,mac_address VARCHAR(100),url VARCHAR(100),name VARCHAR(100))");
$dbans->exec("CREATE TABLE IF NOT EXISTS theme(id INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL, name VARCHAR(100), url VARCHAR(100))");
$res = $dbans->query("SELECT * FROM theme");
$rows = $dbans->query("SELECT COUNT(*) as count FROM theme");
$row = $rows->fetchArray();
$numRows = $row["count"];
$HOSTa = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/red.jpg";
$HOSTb = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/blue.jpg";
$HOSTc = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/green.jpg";
$HOSTa1 = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/g1.gif";
$mac_count = $dbans->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = $id");
$mac_count = $mac_count->fetchArray();
$mac_count = $mac_count["count"];
$dbpans = new SQLite3("./api/.anspanel.db");
$resans = $dbpans->query("SELECT * \n\t\t\t\t  FROM USERS \n\t\t\t\t  WHERE ID='1'");
$rowans = $resans->fetchArray();
$nameans = $rowans["NAME"];
$logoans = $rowans["LOGO"];
echo "<!DOCTYPE html>\n<html lang=\"en\">\n\n<head>\n\n";
$jsondata111 = file_get_contents("./includes/ansibo.json");
$json111 = json_decode($jsondata111, true);
$col1 = $json111["info"];
$col2 = $col1["aa"];
$col3 = $col2;

?>

<!DOCTYPE html>
<html lang="pt-br">

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<meta name="google" content="notranslate">
<script src="https://kit.fontawesome.com/3794d2f89f.js" crossorigin="anonymous"></script>
<title>Painel Revenda</title>
<link rel="shortcut icon" href="./img/logo.png" type="image/png">
<link rel="icon" href="./img/logo.png" type="image/png">
<!-- Custom styles for this template-->
<link href="css/sb-admin-<?= $col2 ?>.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.min.css">
<!-- Custom fonts for this template-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<style>
    .no-margin {
        margin-top: 20px;
        margin-left: 0;
        padding: 0;
    }
</style>
</head>
<body id="page-top">


<div id="wrapper">


<ul class="navbar-nav bg-gradient-primary sidebar toggled sidebar-dark accordion" id="accordionSidebar">

<?php if ($logoans != NULL): ?>
   
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="colours.php">
        <div class="sidebar-brand-icon">
            <img class="img-profile rounded-circle" width="65px" src="img/logo.png">
        </div>
    </a>
<?php else: ?>
    
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="colours.php">
        <div class="sidebar-brand-icon">
            <img class="img-profile rounded-circle" width="65px" src="img/logo.png">
        </div>
    </a>
<?php endif; ?>

<hr class="sidebar-divider my-0">

<!-- Nav Item -->
<li class="nav-item no-margin">
    <a class="nav-link" href="users_mac.php">
        <i class="fas fa-fw fa-user-plus"></i>
        <span>Macs Ativos (<?= $mac_count ?>)</span>
    </a>
</li>

<li class="nav-item no-margin">
    <a class="nav-link" href="logout.php">
    <i class="fas fa-fw fa fa-sign-out"></i>
    <span>Logout</span></a>
</li>
<li class="nav-item no-margin">
    <a class="nav-link">
    <span>Código da Loja: <b><?=$id ?></b></span>
</li>


<footer class="sticky-footer">
    <div class="copyright text-center">
        <span></a></span>    </ul>

<div id="content-wrapper" class="d-flex flex-column">


<div id="content">


<nav class="navbar navbar-expand navbar-light  topbar mb-4 static-top shadow">

<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
<i class="fa fa-bars"></i>
</button>


<div>
    <h5 class="m-0 text-primary"><?= $nameans ?></h5>
</div>


<ul class="navbar-nav ml-auto">

    <li class="nav-item dropdown no-arrow mx-1">
    </li>
    <div class="topbar-divider d-none d-sm-block"></div>

    <li class="nav-item dropdown no-arrow mx-1">
        <a class="nav-link dropdown-toggle" href="logout.php">
            <span class="badge badge-danger">Logout</span>
            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-red-400"></i>
        </a>
    </li>

</ul>

</nav>

