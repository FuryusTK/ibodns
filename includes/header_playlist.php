<?php


session_start();
ini_set("display_errors", 1);
ini_set("display_startup_errors", 0);
error_reporting(32767);
if (!isset($_SESSION['id'])) {
    header("Location: login_mac.php");
    exit();
}

$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];
$mac_address = strtoupper($_SESSION['mac_address']);

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
$mac_count = $dbans->query("SELECT COUNT(*) as count FROM ibo WHERE mac_address = '$mac_address'");
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
echo "  
<meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n  
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">\n  
<meta name=\"description\" content=\"\">\n  <meta name=\"author\" content=\"\">\n  
<meta name=\"google\" content=\"notranslate\">\n  
<script src=\"https://kit.fontawesome.com/3794d2f89f.js\" crossorigin=\"anonymous\"></script>\n  
<title>Loja e Apps IBO Manager Panel</title>\n    
<link rel=\"shortcut icon\" href=\"./img/logo.png\" type=\"image/png\">\n    
<link rel=\"icon\" href=\"./img/logo.png\" type=\"image/png\">\n  <!-- Custom styles for this template-->\n";
echo "  
<link href=\"css/sb-admin-" . $col2 . ".css\" rel=\"stylesheet\">" . "\n";
echo "  
<link rel=\"stylesheet\" type=\"text/css\" href=\"css/jquery.datetimepicker.min.css\"/>\n  
<!-- Custom fonts for this template-->\n  
<link href=\"vendor/fontawesome-free/css/all.min.css\" rel=\"stylesheet\" type=\"text/css\">\n  
<link href=\"https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i\" rel=\"stylesheet\">\n \n</head> \n<body id=\"page-top\">\n\n  <!-- Page Wrapper -->\n  <div id=\"wrapper\">\n\n    
<!-- Sidebar -->\n    
<ul class=\"navbar-nav bg-gradient-primary sidebar toggled sidebar-dark accordion\" id=\"accordionSidebar\">\n\n";

echo "\n      </a>\n\n      
<!-- Divider -->\n      <hr class=\"sidebar-divider my-0\">\n\n      
<!-- Nav Item -->\n      
<li class=\"nav-item\">\n        
<a class=\"nav-link\" href=\"playlists_mac.php\">\n          
<i class=\"fas fa-fw fa-user-plus\"></i>\n";
echo "          <span>Playlists (" . $mac_count . ")</span></a></li>";
?>

<li class="nav-item">
    <a class="nav-link" href="logout_playlist.php">
    <i class="fas fa-fw fa fa-sign-out"></i>
    <span>Sair</span></a>
</li>

<footer class="sticky-footer">
    <div class="copyright text-center">
        <span></a></span>    </ul>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light  topbar mb-4 static-top shadow">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
<i class="fa fa-bars"></i>
</button>

<?php
//echo "<li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"note.php\">\n          <i class=\"fas fa-fw fa fa-bullhorn\"></i>\n          <span>Notifications</span></a>\n      </li>\n      \n      <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"theme.php\">\n          <i class=\"fas fa-fw fa-paint-brush\"></i>\n          <span>Themes</span></a>\n      </li>\n      \n    <!--  <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"sports.php\">\n          <i class=\"fas fa-futbol\"></i> \n          <span>Sport Guide</span></a>\n      </li> -->\n      \n      <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"update.php\">\n          <i class=\"fas fa-fw fa fa-refresh\"></i>\n          <span>Apk Update</span></a>\n      </li>\n\n      <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"snoop.php\">\n          <i class=\"fas fa-fw fa-eye\"></i>\n          <span>Snoop</span></a>\n      </li>\n      \n      <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"profile.php\">\n          <i class=\"fas fa-fw fa-user\"></i>\n          <span>Profile</span></a>\n      </li>\n      <li class=\"nav-item\">\n        <a class=\"nav-link\" href=\"logout.php\">\n          <i class=\"fas fa-fw fa fa-sign-out\"></i>\n          <span>Logout</span></a>\n      </li>\n      \n      <li class=\"nav-item\">\n        <a class=\"nav-link collapsed\" href=\"#\" data-toggle=\"collapse\" data-target=\"#collapsePages\" aria-expanded=\"true\" aria-controls=\"collapsePages\">\n          <i class=\"fas fa-fw fa-cloud\"></i>\n          <span>Cloud</span>\n        </a>\n        <div id=\"collapsePages\" class=\"collapse\" aria-labelledby=\"headingPages\" data-parent=\"#accordionSidebar\">\n        <div class=\"bg-white py-2 collapse-inner rounded\">\n            <h6 class=\"collapse-header\">Cloud Uploads:</h6>\n        <a class=\"collapse-item\" href=\"https://www.filelinked.com/login\" target=\"_blank\"><i class=\"fas fa-fw fa-share\"></i><span> FileLinked</span></a>\n        <a class=\"collapse-item\" href=\"https://db.tt\" target=\"_blank\"><i class=\"fas fa-fw fa-share\"></i><span> DropBox</span></a>\n        <a class=\"collapse-item\" href=\"https://mega.nz/\" target=\"_blank\"><i class=\"fas fa-fw fa-share\"></i><span> Mega</span></a>\n          </div>\n        </div>\n      </li>\n\n      <!-- Divider -->\n      <hr class=\"sidebar-divider d-none d-md-block\">\n\n      <!-- Sidebar Toggler (Sidebar) -->\n      <div class=\"text-center d-none d-md-inline\">\n        <button class=\"rounded-circle border-0\" id=\"sidebarToggle\"></button>\n      </div>\n         <footer class=\"sticky-footer\">\n          <div class=\"copyright text-center\">\n            <span></a></span>    </ul>\n    <!-- End of Sidebar -->\n\n    <!-- Content Wrapper -->\n    <div id=\"content-wrapper\" class=\"d-flex flex-column\">\n\n      <!-- Main Content -->\n      <div id=\"content\">\n\n        <!-- Topbar -->\n        <nav class=\"navbar navbar-expand navbar-light  topbar mb-4 static-top shadow\">\n\n          <!-- Sidebar Toggle (Topbar) -->\n          <button id=\"sidebarToggleTop\" class=\"btn btn-link d-md-none rounded-circle mr-3\">\n            <i class=\"fa fa-bars\"></i>\n          </button>\n";
echo "<div><h5 class=\"m-0 text-primary\">Painel Revenda<br>" . $nameans . " </br></h5></div>" . "\n";
echo "\n          <!-- Topbar Navbar -->\n          <ul class=\"navbar-nav ml-auto\">\n\n\n            <!-- Nav Item - Theme -->\n            <li class=\"nav-item dropdown no-arrow mx-1\">\n  </li>\n            <div class=\"topbar-divider d-none d-sm-block\"></div>\n\n            <!-- Nav Item - Logout -->\n            <li class=\"nav-item dropdown no-arrow mx-1\">\n              <a class=\"nav-link dropdown-toggle\" href=\"logout_playlist.php\"><span class=\"badge badge-danger\">Logout</span>\n                <i class=\"fas fa-sign-out-alt fa-sm fa-fw mr-2 text-red-400\"></i>\n              </a>\n            </li>\n\n          </ul>\n\n        </nav>\n        <!-- End of Topbar -->\n\n";

?>