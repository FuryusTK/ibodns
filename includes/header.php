<?php
// ==============================================
// SEÇÃO: GERENCIAMENTO DE SESSÃO E SEGURANÇA
// ==============================================

// Inicia a sessão apenas se ainda não estiver ativa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Configurações de exibição de erros (apenas para desenvolvimento)
ini_set("display_errors", 1);        // Exibe erros na tela
ini_set("display_startup_errors", 0); // Não exibe erros de inicialização
error_reporting(32767);              // Nível máximo de reporte de erros

// Verifica se o usuário está logado, caso contrário redireciona para login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// ==============================================
// SEÇÃO: CONFIGURAÇÕES DO USUÁRIO
// ==============================================

// Obtém ID do usuário da sessão e verifica se é admin
$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];

// ==============================================
// SEÇÃO: BANCO DE DADOS
// ==============================================

// Conexão com os bancos de dados SQLite
$dbans = new SQLite3("./api/.ansdb.db");  // Banco principal de dados
$adb = new SQLite3('./api/.adb.db');      // Banco adicional (não utilizado neste trecho)

// Cria tabelas se não existirem no banco principal
$dbans->exec("CREATE TABLE IF NOT EXISTS ibo(id INTEGER PRIMARY KEY NOT NULL,mac_address VARCHAR(100),key VARCHAR(100),username VARCHAR(100),password VARCHAR(100),expire_date VARCHAR(100),dns VARCHAR(100),epg_url VARCHAR(100),title VARCHAR(100),url VARCHAR(100), type VARCHAR(100), id_user INT)");
$dbans->exec("CREATE TABLE IF NOT EXISTS playlist(id INTEGER PRIMARY KEY NOT NULL,mac_address VARCHAR(100),url VARCHAR(100),name VARCHAR(100))");
$dbans->exec("CREATE TABLE IF NOT EXISTS theme(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100), url VARCHAR(100))");

// Consulta temas e conta quantos existem
$res = $dbans->query("SELECT * FROM theme");
$rows = $dbans->query("SELECT COUNT(*) as count FROM theme");
$row = $rows->fetchArray();
$numRows = $row["count"];

// ==============================================
// SEÇÃO: CONFIGURAÇÕES DE URL E HOST
// ==============================================

// Define URLs para imagens usando o protocolo correto (HTTP/HTTPS)
$HOSTa = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/red.jpg";
$HOSTb = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/blue.jpg";
$HOSTc = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/green.jpg";
$HOSTa1 = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/g1.gif";

// ==============================================
// SEÇÃO: ESTATÍSTICAS DO USUÁRIO
// ==============================================

// Conta quantos MAC addresses o usuário tem registrados
$mac_count = $dbans->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = $id");
$mac_count = $mac_count->fetchArray()["count"];

// Conta quantos MAC addresses estão expirados
$expired_mac_count = $dbans->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = $id AND (active = 0 OR expire_date < date('today'))");
$expired_mac_count = $expired_mac_count->fetchArray()["count"];

// ==============================================
// SEÇÃO: CONFIGURAÇÕES DO PAINEL
// ==============================================

// Obtém informações do painel administrativo
$dbpans = new SQLite3("./api/.anspanel.db");
$resans = $dbpans->query("SELECT * FROM USERS WHERE ID='1'");
$rowans = $resans->fetchArray();
$nameans = $rowans["NAME"];  // Nome do painel
$logoans = $rowans["LOGO"];  // Caminho do logo

// ==============================================
// SEÇÃO: CONFIGURAÇÕES DE TEMA
// ==============================================

// Carrega configurações de tema de um arquivo JSON
echo "<!DOCTYPE html>\n<html lang=\"en\">\n\n<head>\n\n";
$jsondata111 = file_get_contents("./includes/ansibo.json");
$json111 = json_decode($jsondata111, true);
$col1 = $json111["info"];
$col2 = $col1["aa"];  // Cor/tema principal
$col3 = $col2;
?>

<!-- ============================================== -->
<!-- SEÇÃO: METADADOS E IMPORTAÇÕES -->
<!-- ============================================== -->

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<meta name="google" content="notranslate">
<script src="https://kit.fontawesome.com/3794d2f89f.js" crossorigin="anonymous"></script>
<title>IBO 09 Temas</title>
<link rel="shortcut icon" href="./img/atvwhite.png" type="image/png">
<link rel="icon" href="./img/logo.png" type="image/png">

<!-- ============================================== -->
<!-- SEÇÃO: ESTILOS E FONTES -->
<!-- ============================================== -->

<!-- CSS personalizado baseado no tema selecionado -->
<link href="css/sb-admin-<?= $col2 ?>.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.min.css">

<!-- Fontes e ícones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<style>
    .no-margin {
        margin-top: 0;
        margin-left: 5px;
        margin-bottom: 10px;
        padding: 0;
    }
</style>

</head>
<body id="page-top">

<!-- ============================================== -->
<!-- SEÇÃO: ESTRUTURA PRINCIPAL DO PAINEL -->
<!-- ============================================== -->

<!-- Page Wrapper -->
<div id="wrapper">

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-header-adm-rev sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Logo do painel -->
<?php if ($logoans != NULL): ?>
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="all_users.php">
        <div class="sidebar-brand-icon">
            <img class="img-profile rounded-circle" width="65px" src="img/atvwhite.png">
        </div>
    </a>
<?php else: ?>
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="users.php">
        <div class="sidebar-brand-icon">
            <img class="img-profile rounded-circle" width="65px" src="img/logo.png">
        </div>
    </a>
<?php endif; ?>

<hr class="sidebar-divider my-0">

<!-- ============================================== -->
<!-- SEÇÃO: MENU DE NAVEGAÇÃO -->
<!-- ============================================== -->

<!-- Menu - Usuários -->
<span class="text-menu-header">Usuários</span>
<li class="nav-item no-margin">
    <a class="nav-link" href="users.php">
        <i class="fas fa-fw fa-user-plus"></i>
        <span>Meus Clientes(<?= $mac_count ?>)</span>
    </a>
</li>
<li class="nav-item no-margin">
    <a class="nav-link" href="buscar.php">
        <i class="fa-solid fa-shuffle"></i>
        <span>Buscar Ativar Cliente</span></a>
</li>

<!-- Menu - Administrador (apenas para usuários admin) -->
<?php if ($isAdmin) { ?>
    <li class="nav-item no-margin">
        <a class="nav-link" href="all_users.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Clientes</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="chatbot.php">
            <i class="fas fa-robot"></i>
            <span>ChatBot</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="dnsmax.php">
            <i class="fa-solid fa-shuffle"></i>
            <span>Trocar DNS Massa</span></a>
    </li>
    
    <!-- Menu - Revendedores -->
    <span class="text-menu-header">Área do Revenda</span>
    <li class="nav-item no-margin">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages1" aria-expanded="true" aria-controls="collapsePages1">
            <i class="fa-solid fa-id-card"></i>
            <span>Revendas</span>
        </a>
        <div id="collapsePages1" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Área do Revenda:</h6>
                <a class="collapse-item" href="stores.php"><i class="fas fa-fw fa-infinity"></i><span> Revendas Premium</span></a>
            </div>
        </div>
    </li>
    
    <!-- Menu - Layout -->
    <span class="text-menu-header">Layout</span>
    <li class="nav-item no-margin">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages3" aria-expanded="true" aria-controls="collapsePages3">
            <i class="fa-solid fa-sliders"></i>
            <span>Customizar Layout</span>
        </a>
        <div id="collapsePages3" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Configuração design:</h6>
                <a class="collapse-item" href="layouts.php"><i class="fa-solid fa-palette"></i><span> Escolher Temas</span></a>
                <a class="collapse-item" href="logo.php"><i class="fa-solid fa-masks-theater"></i><span> Logo</span></a>
                <a class="collapse-item" href="Image.php"><i class="fa-solid fa-image"></i><span> Fundo</span></a>
            </div>
        </div>
    </li>

    <!-- Menu - Ajustes -->
    <span class="text-menu-header">Area Ajustes</span>
    <li class="nav-item no-margin">
        <a class="nav-link" href="autoads.php">
            <i class="fa-solid fa-film"></i>
            <span>Banner Auto / Man</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="ads.php">
            <i class="fa-solid fa-images"></i>
            <span>Banners Manual</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="UI_Setting.php">
            <i class="fa fa-picture-o"></i>
            <span>Estilo do anúncio</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="sport.php">
            <i class="fas fa-football-ball"></i>
            <span>Esporte</span></a>
    </li>
    
    <li class="nav-item no-margin">
        <a class="nav-link" href="note.php">
            <i class="fas fa-sms"></i>
            <span>Nota</span></a>
    <?php } ?>

<!-- Menu - Pagamentos -->
<span class="text-menu-header">Area Pagamento</span>
<li class="nav-item no-margin">
    <a class="nav-link" target="_blank" href="pagamento.php">
        <i class="fas fa-qrcode"></i>
        <span>Link Pagamento</span></a>
</li>

<li class="nav-item no-margin">
    <a class="nav-link" href="qrcode.php">
        <i class="fas fa-qrcode"></i>
        <span>QR Pagamento</span></a>
</li>

<li class="nav-item no-margin">
    <a class="nav-link" href="Alterar.php">
        <i class="fas fa-qrcode"></i>
        <span>Alterar Pagamento</span></a>
</li>

<!-- Menu - Administração -->
<span class="text-menu-header">Area Administrador</span>
<li class="nav-item no-margin">
    <a class="nav-link" href="https://flix-play.com/apks">
        <i class="fab fa-video"></i>
        <span>+ Scripts / Apks</span></a>
</li>

<li class="nav-item no-margin">
    <a class="nav-link" href="update.php">
        <i class="fab fa-video"></i>
        <span>Atualizar App</span></a>
</li>

<li class="nav-item no-margin">
    <a class="nav-link" href="profile.php">
        <i class="fas fa-fw fa-user"></i>
        <span>ADMIN</span></a>
</li>

<!-- Logout -->
<li class="nav-item no-margin">
    <a class="nav-link" href="logout.php">
        <i class="fas fa-fw fa fa-sign-out"></i>
        <span>Sair</span></a>
</li>

<!-- Exibe o ID do vendedor -->
<li class="nav-item2">
    <a class="nav-link2">
    <span>Código do vendedor: <b><?=$id ?></b></span>
    </a>
</li>

<!-- ============================================== -->
<!-- SEÇÃO: RODAPÉ E ELEMENTOS FINAIS -->
<!-- ============================================== -->

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

<footer class="sticky-footer">
    <div class="copyright text-center">
        <span></a></span> </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <?php
                    // Exibe o nome do painel
                    echo "<div><h5 class=\"m-0 text-primary\">" . $nameans . " </br></h5></div>" . "\n";
                    echo "\n          <!-- Topbar Navbar -->\n          <ul class=\"navbar-nav ml-auto\">\n\n\n            
                    <!-- Nav Item - Theme -->\n            
                    <!-- <li class=\"nav-item no-margin dropdown no-arrow mx-1\">\n            </li> -->\n            
                    <div class=\"topbar-divider d-none d-sm-block\"></div>\n\n            
                    <!-- Nav Item - Logout -->\n            
                    <li class=\"nav-item no-margin3 dropdown no-arrow mx-1\">\n              
                    <a class=\"nav-link dropdown-toggle\" href=\"logout.php\"><span class=\"badge badge-danger\">Sair</span>\n                
                    <i class=\"fas fa-sign-out-alt fa-sm fa-fw mr-2 text-red-400\"></i>\n              </a>\n            </li>\n\n          </ul>\n\n        </nav>\n        
                    <!-- End of Topbar -->\n\n";
                    ?>