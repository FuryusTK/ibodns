<?php 
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

//db call
$db = new SQLite3("./api/db/studiolivecode_qrcode.db");

//table name
$table_name = "qrcode";

//current file var
$base_file = basename($_SERVER["SCRIPT_NAME"]);

//create if not
$db->exec("CREATE TABLE IF NOT EXISTS " . $table_name . "(id INTEGER PRIMARY KEY, qrcode TEXT)");

$res = $db->query("SELECT COUNT(*) as count FROM " . $table_name. "");
$row = $res->fetchArray();
$numRows = $row["count"];
if ($numRows == 0) {
    $db->exec("INSERT INTO " . $table_name . "(id, qrcode) VALUES('1', 'https://wa.me/554197211993')");   
}

$res = $db->query("SELECT qrcode FROM " . $table_name . " WHERE id='1'");
$row = $res->fetchArray();
$qrcode = $row["qrcode"];

if (isset($_POST["submit"])) {
    // Atualize o valor do QR Code no banco de dados
    $newQrcode = $_POST["qrcode"];
    $db->exec("UPDATE qrcode SET qrcode='" . $newQrcode . "' WHERE id='1'");

    $db->close();
    header("Location: qrcode.php?r=atualizado");
}

?>
<?php include 'includes/header.php';?>

<style>
    .container-fluid {
        padding: 20px;
    }
    
    .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .card-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ffffff;
    background-color: #363636;  /* Cor de fundo do texto do QR */
    padding: 10px 15px;
    border-radius: 10px;
    display: inline-block;
    width: 100%; /* A largura da cor de fundo do texto QR */
    text-align: center; /* Centraliza o texto */
}

    .preview-image {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-top: 20px;
    }

    .form-control {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: #003366;
        border: none;
        padding: 10px 20px;
        font-weight: bold;
        transition: transform 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
        animation: pulse 1.5s infinite;
    }

    .btn-primary:hover {
        background-color: #002244;
    }

    .btn i {
        margin-right: 5px;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<div class="container-fluid">
    <div class="card radius-10">
        <div class="card-body">
            <center><h4 class="card-title">OBS: Pegar endereço da sua pagina de LINK PAGAMENTO, e inserir no campo abaixo:</h4></center>
            <center>
            <?php
            $qrcodeUrl = "https://image-charts.com/chart?chs=500x500&cht=qr&chl=" . $qrcode;
            if (!empty($qrcode)) {
                ?>
                <img class="preview-image" src="<?= $qrcodeUrl ?>" alt="Uploaded Image" width="30%" height="auto">
                <?php
            } else {
                ?>
                <img class="preview-image" src="assets/img/noqr.png" alt="No QR Code Available" width="50%" height="auto">
                <?php
            }
            ?>
            </center>
            <form class="forms-sample" method="post" enctype="multipart/form-data">
                <div class="form-group mb-4"> 
                    <br>
                    <input type="text" class="form-control" name="qrcode" id="qrcode" value="<?= $qrcode ?>">
                </div>
                <center><button type="submit" name="submit" class="btn btn-primary mr-2"><i class='bx bx-check'></i>Enviar</button></center>
            </form>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>

<?php 
if (isset($_GET["r"])) {
    $result = $_GET["r"];
    switch ($result) {
        case "atualizado":
            echo "<script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom',
                showConfirmButton: false,
                timer: 2000,
            });
            Toast.fire({
                icon: 'success',
                title: 'QRCODE Atualizado com Sucesso!'
            });
            </script>";
            break;
    }
}
?>
