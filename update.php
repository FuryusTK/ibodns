<?php
/*
 * Este código está licenciado sob a [Sua Licença Personalizada].
 * Consulte o arquivo LICENSE na raiz do projeto para mais detalhes.
 */
?>
<?php
session_start();

// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php?error=unauthorized");
    exit();
}

// Configuração de exibição de erros com base no ambiente
$appEnv = getenv('APP_ENV') ?: 'production';
if ($appEnv === 'development') {
    ini_set("display_errors", 0);
    ini_set("display_startup_errors", 0);
    error_reporting(E_ALL);
} else {
    ini_set("display_errors", 0);
    ini_set("display_startup_errors", 0);
    error_reporting(0);
}

// Função para atualizar o arquivo JSON
function updateJsonFile($filePath, $replacementData) {
    if (!file_exists($filePath)) {
        throw new Exception("O arquivo JSON não foi encontrado.");
    }

    $jsonData = @file_get_contents($filePath);
    if ($jsonData === false) {
        throw new Exception("Erro ao ler o arquivo JSON.");
    }

    $arrayData = json_decode($jsonData, true);
    if ($arrayData === null) {
        throw new Exception("Erro ao decodificar o arquivo JSON.");
    }

    $newArrayData = array_replace_recursive($arrayData, $replacementData);
    $newJsonData = json_encode($newArrayData, JSON_PRETTY_PRINT);

    if (@file_put_contents($filePath, $newJsonData) === false) {
        throw new Exception("Erro ao salvar o arquivo JSON.");
    }
}

// Processar o formulário de envio
if (isset($_POST["submit"])) {
    $versionCode = $_POST["android_version_code"];
    $apkUrl = $_POST["apk_url"];

    // Validação dos dados
    if (!is_numeric($versionCode)) {
        die("O código da versão deve ser numérico.");
    }
    if (!filter_var($apkUrl, FILTER_VALIDATE_URL)) {
        die("A URL fornecida não é válida.");
    }

    // Atualizar o arquivo JSON
    $replacementData = ["app_info" => ["android_version_code" => $versionCode, "apk_url" => $apkUrl]];
    updateJsonFile("./api/update.json", $replacementData);

    // Redirecionar com mensagem de sucesso
    header("Location: update.php?message=success");
    exit();
}

// Ler os dados do JSON para exibição no formulário
$filePath = "./api/update.json";
if (!file_exists($filePath)) {
    die("O arquivo JSON não foi encontrado.");
}

$jsonData = file_get_contents($filePath);
$data = json_decode($jsonData, true);

if ($data === null) {
    die("Erro ao decodificar o arquivo JSON.");
}

$json = $data["app_info"];
$avc = htmlspecialchars($json["android_version_code"], ENT_QUOTES, 'UTF-8');
$apkurl = htmlspecialchars($json["apk_url"], ENT_QUOTES, 'UTF-8');

// Mensagem de sucesso
$message = "";
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $message = "<div class=\"alert alert-primary\" id=\"flash-msg\"><h4><i class=\"icon fa fa-check\"></i>Apk Details Updated!</h4></div>";
}

include "includes/header.php";
echo $message;
echo "        
<div class=\"container-fluid\">\n\n          
<!-- Page Heading -->\n          
<h1 class=\"h3 mb-1 text-gray-800\">Apk Update</h1>\n         \n          
<!-- Content Row -->\n          
<div class=\"row\">\n\n            
<!-- First Column -->\n            
<div class=\"col-lg-12\">\n\n              
<!-- Custom codes -->\n                
<div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                
<div class=\"card-header py-3\">\n                
<h6 class=\"m-0 font-weight-bold text-primary\">
<i class=\"fa fa-refresh\"></i> Apk Update</h6>\n                </div>\n                
<div class=\"card-body\">\n\t\t\t\t\t\t\t<form method=\"post\">\n\t\t\t\t\t\t\t<div class=\"form-group\">\n                            
<h6 class=\"form-text\"><strong>Version Code</strong></h6>\n";
echo "\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" placeholder=\"Version Code\" name=\"android_version_code\" value=\"" . $avc . "\">" . "\n";
echo "\t\t\t\t\t\t\t</div>\n\t\t\t                
<form method=\"post\">\n\t\t\t\t\t\t\t<div class=\"form-group\">\n                            
<h6 class=\"form-text\"><strong>Download Url</strong></h6>\n";
echo "\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" placeholder=\"http://link to app.apk\" name=\"apk_url\" value=\"" . $apkurl . "\">" . "\n";
echo "\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t<button class=\"btn btn-primary btn-icon-split\" name=\"submit\" type=\"submit\">\n                        
<span class=\"icon text-white-50\">
<i class=\"fas fa-check\"></i></span><span class=\"text\">Submit</span>\n                        
</button>\n                            
</div>\n                    
</form>\n                
</div>\n              
</div>\n<br><br><br><br>\n            
</div>\n            
</div>\n            \n\n                                         <br><br><br>\n";
include "includes/footer.php";
echo "                
<script> \n\$(document).ready(function () {\n    \$(\"#flash-msg\").delay(3000).fadeOut(\"slow\");\n});\n  
</script>\n</body>\n\n</html>";
?>