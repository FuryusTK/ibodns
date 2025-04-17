<?php

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
session_start();

$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];
$storeType = $_SESSION['store_type'];

$limited = false;
$errorMessage = ""; // Variável para armazenar a mensagem de erro
$db = new SQLite3("./api/.ansdb.db");
$db->exec("CREATE TABLE IF NOT EXISTS ibo(
    id INTEGER PRIMARY KEY NOT NULL,
    mac_address VARCHAR(100),
    key VARCHAR(100),
    username VARCHAR(100),
    password VARCHAR(100),
    expire_date VARCHAR(100),
    epg_url VARCHAR(100),
    title VARCHAR(100),
    url VARCHAR(100),
    type VARCHAR(100),
    id_user INTEGER,
    playlistpassword VARCHAR(100),
    active INTEGER
)");
$res = $db->query("SELECT * FROM ibo");

// Conecta ao banco de dados de links de pagamento
$db3 = new SQLite3('./api/.payment_links.db');
$db3->exec("CREATE TABLE IF NOT EXISTS payment_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    mac_address VARCHAR(100) NOT NULL,
    payment_link VARCHAR(255) NOT NULL
)");

if (isset($_POST["submit"])) {
    $address1 = strtoupper($_POST["mac_address"]);

    // Inicializa a variável para armazenar todas as mensagens de erro
    $errorMessage = "";

    // Verifica se o MAC Address já está registrado na tabela ibo
    $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE mac_address = '$address1'");
    $count = $res->fetchArray()['count'];

    if ($count > 0) {
        // Se o MAC Address já existe na tabela ibo, adiciona a mensagem de erro
        $errorMessage .= "<div class='alert alert-danger'>Já existe um registro para este MAC Address.</div>";
    }

    // Verifica se já existe um link de pagamento para o endereço MAC
    $res = $db3->query("SELECT payment_link FROM payment_links WHERE mac_address = '$address1'");
    $row = $res->fetchArray();

    if ($row) {
        // Se já existe um link de pagamento, adiciona a mensagem à variável de erro
        $errorMessage .= "<div class='alert alert-warning'>Já existe um link de pagamento cadastrado para este MAC Address: " . $row['payment_link'] . "</div>";
    }

    // Prossegue com o processo apenas se não houver erro de duplicidade de MAC Address
    if (!$count && !$limited) {
        $we = strtotime($_POST["expire_date"]);
        $ne = date("2050-12-20", $we);

        if ($storeType == 2) {
            $ne = date('2050-12-20', strtotime('+1 year'));
        }

        $line = $_POST["url"];
        $playlistpassword = $_POST["playlistpassword"] ?? "";

        // Verifica se o usuário atingiu o limite de MACs
        if (!$isAdmin) {
            $dbUsers = new SQLite3("./api/.anspanel.db");
            $res = $dbUsers->query("SELECT mac_amount FROM USERS WHERE id = '$id'");
            $macCount = $res->fetchArray()['mac_amount'];
            $dbUsers->close();

            $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$id' AND active = 1 AND expire_date > date('now')");
            $macCountInUse = $res->fetchArray()['count'];

            if ($macCountInUse >= $macCount) {
                $limited = true;
            }
        }

        if (!$limited) {
            // Inserir novo registro na tabela ibo
            $db->exec("INSERT INTO ibo (
                mac_address, key, username, password, expire_date, epg_url, title, url, type, id_user, playlistpassword, active
            ) VALUES (
                '" . $address1 . "', '" . $_POST["key"] . "', '" . $_POST["username"] . "', '" . $_POST["password"] . "', '" . $ne . "', 
                '" . $_POST["epg_url"] . "', '" . $_POST["title"] . "', '" . $line . "', '" . $_POST["type"] . "', 
                '$id', '$playlistpassword', 1
            )");
        }

        // Inserir ou atualizar o link de pagamento no banco de dados de pagamento
        $paymentLink = $_POST["payment_link"];
        $res = $db3->query("SELECT COUNT(*) as count FROM payment_links WHERE mac_address = '$address1'");
        $countPaymentLink = $res->fetchArray()['count'];

        if ($countPaymentLink > 0) {
            // Atualiza o link de pagamento existente
            $db3->exec("UPDATE payment_links SET payment_link = '$paymentLink' WHERE mac_address = '$address1'");
        } else {
            // Inserir novo link de pagamento
            $db3->exec("INSERT INTO payment_links (mac_address, payment_link) VALUES ('$address1', '$paymentLink')");
        }

        if (!isset($_SESSION['macs'])) {
            $_SESSION['macs'] = [];
        }

        $macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$address1'");
        while ($row = $macRes->fetchArray()) {
            if (!sessionContains($row)) {
                array_push($_SESSION['macs'], $row);
            }
        }

        header("Location: users.php");
        exit; // Interrompe a execução do script
    }

    $db->close();
    $db3->close();
}

function sessionContains($searchRow) {
    foreach ($_SESSION['macs'] as $session_row) {
        if ($session_row['id'] == $searchRow['id']) {
            return true;
        }
    }
    return false;
}

include "includes/header.php";

if ($limited) {
    echo "<div class='alert alert-danger'>Limite de MACs excedido!</div>";
}

if ($errorMessage) {
    echo $errorMessage; // Exibe a mensagem de erro, incluindo avisos
}

?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-1 text-gray-800"> Ativar Usuário</h1>

    <!-- Códigos Personalizados -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user"></i> Detalhes do Usuário</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label class="control-label" for="mac_address">
                        <strong>MAC do Dispositivo</strong>
                    </label>
                    <input type="text" name="mac_address" id="mac_address" class="form-control" placeholder="MAC do Dispositivo" required>
                </div>
                <div class="form-group">
                    <label class="control-label" for="username">
                        <strong>Nome de Usuário</strong>
                    </label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Nome de Usuário" required>
                </div>

                <div class="form-group">
                    <label class="control-label" for="url">
                        <strong>URL M3u</strong>
                    </label>
                    <input type="text" name="url" id="url" class="form-control" placeholder="URL" required>
                </div>
                
                <div class="form-group">
                    <label class="control-label" for="payment_link">
                        <strong>Link de Pagamento</strong>
                    </label>
                    <input type="text" name="payment_link" id="payment_link" class="form-control" placeholder="Link de Pagamento">
                </div>

                <button class="btn btn-primary btn-block" type="submit" name="submit">Ativar</button>
            </form>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
