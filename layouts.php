<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}
include "includes/header.php";

$jsonData = file_get_contents('./api/theme_change/Setting.json');
$data = json_decode($jsonData, true);

$tema_atual = "";

foreach($data as $item){
    if($item["RTXSetting"] === "mLayout") {
        $tema_atual = $item["PanalData"];
        break;
    }
}

$temas = [
    
    "theme_d" => "Tema 1",
    "theme_2" => "Tema 2",
    "theme_3" => "Tema 3",
    "theme_4" => "Tema 4",
    "theme_5" => "Tema 5",
    "theme_6" => "Tema 6",
    "theme_7" => "Tema 7",
    "theme_8" => "Tema 8",
    "theme_9" => "Tema 9"
        
];

$tema_atual_escolhido = isset($temas[$tema_atual]) ? $temas[$tema_atual] : "Tema Descolhecido";

?>
<style>
    .custom-button {
        padding: 10px 20px;
    }
    .image-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 20px;
    }
    .image-container {
        flex: 1 1 300px; /* Flex-grow, flex-shrink, and base width */
        max-width: 300px;
        margin: 10px;
        text-align: center;
        background-color: #FFF;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 10px;
    }
    .image-container img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }
    .horizontal-space {
        margin-right: 20px;
    }
    label, select, input {
        background: #F8F9FC;
        padding: 10px 20px 10px 20px;
        margin-left: 10px;
        border: none;
        border-radius: 10px;
        box-shadow: 5px 5px 5px 0 rgba(0,0,0,0.35);
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .active-theme {
        border: 3px solid #4e73df; /* Cor de destaque */
        box-shadow: 0 0 10px rgba(78, 115, 223, 0.5); /* Sombra para destaque */
        transform: scale(1.05); /* Aumenta ligeiramente o tamanho */
        transition: all 0.3s ease; /* Animação suave */
    }
</style>

<div class="container-fluid">
    <!-- Page Heading -->
    <center><h1 class="h3 mb-1 text-gray-800">Escolha o Tema</h1></center>
    <!-- Custom codes -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-cogs"></i> Tema atual ( <?= $tema_atual_escolhido; ?> )</h6>
        </div>
        <div class="card-body">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $selectedOption = $_POST['options'];
                
                $tema = $temas[$selectedOption];
                
                echo "O tema escolhido é: $tema";

                // Read existing JSON data from file
                $jsonData = file_get_contents('./api/theme_change/Setting.json');
                $data = json_decode($jsonData, true);

                // Update first record in JSON data
                $data[0]["RTXSetting"] = "mLayout";
                $data[0]["PanalData"] = $selectedOption;

                // Encode the updated data back to JSON
                $jsonData = json_encode($data, JSON_PRETTY_PRINT);

                // Write the updated JSON data to file
                file_put_contents('./api/theme_change/Setting.json', $jsonData);
            }
            ?>
            <form class="form-theme" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <select name="options" id="options">
                    <?php foreach ($temas as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $key === $tema_atual ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
                <input type="submit" class="btn btn-primary btn-icon-split custom-button" value="Ativar">
            </form>
            <br><br>
            <div class="image-row">
                <?php foreach ($temas as $key => $value): ?>
                    <div class="image-container <?= $key === $tema_atual ? 'active-theme' : '' ?>">
                        <p><?= $value ?></p>
                        <img src="./img_custom/layout/<?= str_replace('theme_', '', $key) ?>.jpg" alt="<?= $key ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php
include "includes/footer.php";
?>
</body>
</html>
