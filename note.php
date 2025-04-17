<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}
$file = "./api/language.json";
// Ler o conteúdo do arquivo JSON existente
$jsondata = file_get_contents($file);
// Decodificar o JSON em um array associativo
$data = json_decode($jsondata, true);

// Inicializar arrays para armazenar títulos, conteúdos e nomes dos países
$titles = [];
$contents = [];
$countries = [];
$countryCodes = [];

// Verificar se o JSON está no formato de uma lista e contém itens
if (is_array($data)) {
    foreach ($data as $index => $item) {
        $titles[$index] = $item["words"]["ibo_pro_description"];
        $contents[$index] = $item["words"]["ibo_pro_general_player"];
        $contentsto_add_manage[$index] = $item["words"]["to_add_manage"];
        $countries[$index] = $item["name"];
        $countryCodes[$index] = $item["code"];
    }
} else {
    // Lidar com o erro de formato inesperado do JSON
    die("Erro ao ler o arquivo JSON.");
}

$file1 = "./api/note.json";
// Ler o conteúdo do arquivo JSON existente
$jsondata1 = file_get_contents($file1);
// Decodificar o JSON em um array associativo
$data1 = json_decode($jsondata1, true);

$selectedCountry = isset($_GET['country']) ? $_GET['country'] : null;
$countryIndex = array_search($selectedCountry, $countryCodes);

if (isset($_POST["submit"])) {
    if ($data !== null && $countryIndex !== false) {
        // Dados a serem substituídos
        $replacementData = [
            "ibo_pro_description" => $_POST["title"],
            "ibo_pro_general_player" => $_POST["content"],
            "to_add_manage" => $_POST["content_to_add_manage"]
        ];

        // Substituir os dados no array existente
        $data[$countryIndex]["words"] = array_replace_recursive($data[$countryIndex]["words"], $replacementData);

        // Converter o array de volta para JSON
        $newJsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Escrever o JSON atualizado de volta no arquivo
        file_put_contents($file, $newJsonData);
    } else {
        die("Erro ao ler o arquivo JSON.");
    }

    if ($data1 !== null) {
        // Dados a serem substituídos    
        $replacementData1 = [
            "title" => $_POST["title"], 
            "content" => $_POST["content"],
            "content_to_add_manage" => $_POST["content_to_add_manage"]
        ];
        $newData1 = array_replace_recursive($data1, $replacementData1);
        $newJsonData1 = json_encode($newData1, JSON_UNESCAPED_UNICODE);
        file_put_contents($file1, $newJsonData1);
    } else {
        die("Erro ao ler o arquivo JSON.");
    }

    header("Location: note.php?r=atualizado&country=$selectedCountry");
    exit;
}
?>

<?php include 'includes/header.php'; ?>

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
    .horizontal-space {
        margin-right: 20px;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card-header {
        background: linear-gradient(45deg, #007bff, #00aaff);
        color: white;
        border-bottom: 2px solid #0056b3;
        border-radius: 10px 10px 0 0;
    }
    .card-body {
        background-color: #f7f9fc;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s ease-in-out;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004494;
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        transition: background-color 0.3s ease-in-out;
    }
    .btn-secondary:hover {
        background-color: #565e64;
        border-color: #4e555b;
    }
    .form-control {
        border-radius: 5px;
    }
    .input-group-text {
        background-color: #e9ecef;
    }
    .form-group label {
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <?php if (isset($_GET['r'])): ?>
            <?php $result = $_GET['r']; ?>
            <?php switch ($result):
                case "atualizado": ?>
                    <script>
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom',
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Mensagem Atualizada com Sucesso!'
                        });
                    </script>
                    <?php break; ?>
            <?php endswitch; ?>
        <?php endif; ?>
    
    <div class="card-body">
        <?php if ($selectedCountry === null): ?>
            <div class="col-lg-12">
                <h1 class="h3 mb-1 text-gray-800">Mensagens</h1>
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fa fa-globe"></i> Selecionar País</h6>
                </div>
                <form method="get">
                    <div class="form-group">
                        <label for="country"><strong>Selecione um País:</strong></label>
                        <select class="form-control" name="country" id="country">
                            <?php foreach ($countries as $index => $country): ?>
                                <option value="<?php echo $countryCodes[$index]; ?>"><?php echo $country; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Selecionar</button>
                </form>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card border-left-primary shadow h-100 card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-white"><i class="fa fa-bullhorn"></i> Editar Mensagens para <?php echo $countries[$countryIndex]; ?></h6>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="title"><strong>Título:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="TITLE" name="title" value="<?php echo $titles[$countryIndex]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="content"><strong>Mensagem:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="MESSAGE" name="content" value="<?php echo $contents[$countryIndex]; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="content_to_add_manage"><strong>Mensagem 2:</strong></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="MESSAGE2" name="content_to_add_manage" value="<?php echo $contentsto_add_manage[$countryIndex]; ?>">
                                    </div>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Salvar</button>
                            </form>
                            <br>
                            <a href="note.php" class="btn btn-secondary">Voltar à Seleção de País</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>    
</div>

<?php include 'includes/footer.php'; ?>
