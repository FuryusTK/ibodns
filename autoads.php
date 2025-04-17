<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}
ini_set('display_errors', 0);
include('includes/header.php');
?>
<style>
    .container-fluid {
        padding: 20px;
    }
    
    .card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .card-header {
        background-color: #007bff;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .card-header h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0;
    }
    
    label {
        font-weight: bold;
        margin-right: 10px;
    }
    
    select, input[type="submit"] {
        width: 100%;
        max-width: 300px;
        padding: 10px;
        margin-top: 10px;
        border: none;
        border-radius: 5px;
    }
    
    select {
        background-color: #F8F9FC;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }
    
    .custom-button {
        background-color: #1a7431;
        color: white;
        text-transform: uppercase;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        cursor: pointer;
        animation: pulse 1.5s infinite;
    }

    .custom-button:hover {
        background-color: #155c26;
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
    
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }
</style>

<div class="container-fluid">
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-body">
            <div class="card ctcard">
                <div class="card-header">
                    <center>
                        <h2><i class="fa fa-file-image-o"></i> Modo do banner</h2>
                    </center>
                </div>
                <div class="card-body">
                    <?php
                    $db = new SQLite3('./api/db/.db_ads.db');
    
                    if (!$db) {
                        die("Database connection error.");
                    }
    
                    $query = "CREATE TABLE IF NOT EXISTS adsstatus (id INTEGER PRIMARY KEY, adstype TEXT)";
                    if ($db->exec($query)) {
                    } else {
                        echo "Error creating table: " . $db->lastErrorMsg() . "<br>";
                    }
    
                    $query = "SELECT COUNT(*) FROM adsstatus";
                    $result = $db->querySingle($query);
    
    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $ad_item = $_POST["ad_item"];
    
                        if (empty($ad_item)) {
                            die("Please select an ad item.");
                        }
    
                        if ($result === 0) {
                            $updateQuery = "INSERT INTO adsstatus (adstype) VALUES (:ad_item)";
                        } else {
                            $updateQuery = "UPDATE adsstatus SET adstype = :ad_item WHERE id = 1";
                        }
    
                        $stmt = $db->prepare($updateQuery);
                        $stmt->bindValue(':ad_item', $ad_item, SQLITE3_TEXT);
    
                        if ($stmt->execute()) {
                            echo "Modo '$ad_item' foi atualizado com sucesso ✅<br>";
    
                            // Move o arquivo allads.php conforme a seleção
                            if ($ad_item === "Manualads") {
                                $source = "./Manualads/allads.php";
                                $destination = "./api/allads.php";
                                if (file_exists($source)) {
                                    copy($source, $destination);
                                } else {
                                    echo "Error: File 'allads.php' not found in 'Manualads' folder.<br>";
                                }
                            } elseif ($ad_item === "Autoads") {
                                $source = "./Autoads/allads.php";
                                $destination = "./api/allads.php";
                                if (file_exists($source)) {
                                    copy($source, $destination);
                                } else {
                                    echo "Error: File 'allads.php' not found in 'Autoads' folder.<br>";
                                }
                            }
                        } else {
                            echo "Error updating record: " . $db->lastErrorMsg();
                        }
                    }
                    ?>
    
                    <form method="POST" action="">
                        <div class="ctinput">
                            <center><label for="ad_item">Escolha o modo</label></center>
                            <select name="ad_item" id="ad_item">
                                <option value="Autoads">Automático</option>
                                <option value="Manualads">Manual</option>
                            </select>
                        </div>
    
                        <input type="submit" name="submit" value="Salvar" class="custom-button">
                    </form>
    
                    <?php
                    $db->close();
                    ?>
    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
