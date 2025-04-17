<?php
// ==============================================
// SEÇÃO 1: FUNÇÃO PARA CAPTURA DE IP DO USUÁRIO
// ==============================================
function getIPAddress() {
    $ipAddress = 'undefined';
    
    // Tenta obter IP de várias fontes possíveis (headers HTTP e variáveis de servidor)
    if (isset($_SERVER)) {
        $ipAddress = $_SERVER['REMOTE_ADDR']; // IP base
        
        // Verifica headers adicionais que podem conter IP real em caso de proxy
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
    } else {
        // Fallback para variáveis de ambiente se $_SERVER não estiver disponível
        $ipAddress = getenv('REMOTE_ADDR');
        
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        }
    }
    
    // Proteção básica contra XSS ao retornar o IP
    return htmlspecialchars($ipAddress, ENT_QUOTES, 'UTF-8');
}

// ==============================================
// SEÇÃO 2: INICIALIZAÇÃO E CONFIGURAÇÕES
// ==============================================
session_start(); // Inicia a sessão para armazenar dados de login

// Carrega configurações de tema de um arquivo JSON
$jsondata111 = file_get_contents("./includes/ansibo.json");
$json111 = json_decode($jsondata111, true);
$col1 = $json111["info"];
$col2 = $col1["aa"]; // Cor/tema do painel

// ==============================================
// SEÇÃO 3: BANCO DE DADOS E USUÁRIOS
// ==============================================
// Conexão com o banco de dados SQLite
$db_check1 = new SQLite3("api/.anspanel.db");

// Cria tabela de usuários se não existir
$db_check1->exec("CREATE TABLE IF NOT EXISTS USERS(id INT PRIMARY KEY, NAME TEXT, USERNAME TEXT, PASSWORD TEXT, LOGO TEXT)");

// Verifica se há usuários cadastrados
$rows = $db_check1->query("SELECT COUNT(*) as count FROM USERS");
$row = $rows->fetchArray();
$numRows = $row["count"];

// Se não houver usuários, cria um padrão (admin/admin)
if ($numRows == 0) {
    $db_check1->exec("INSERT INTO USERS(id, NAME, USERNAME, PASSWORD, LOGO) VALUES('1','Seu Nome','admin','admin','img/logo.png')");
}

// Obtém dados do primeiro usuário para exibir informações
$res_login = $db_check1->query("SELECT * FROM USERS WHERE id='1'");
$row_login = $res_login->fetchArray();
$name_login = $row_login["NAME"];
$logo_login = $row_login["LOGO"];

// ==============================================
// SEÇÃO 4: PROCESSAMENTO DO LOGIN
// ==============================================
if (isset($_POST["login"])) {
    // Verificação básica de credenciais (ATENÇÃO: vulnerável a SQL Injection)
    $sql_check = "SELECT * FROM USERS WHERE USERNAME='" . $_POST["username"] . "' AND PASSWORD='" . $_POST["password"] . "'";
    $ret_check = $db_check1->query($sql_check);

    // Extrai dados do usuário se encontrado
    while ($row_check = $ret_check->fetchArray()) {
        $id_check = $row_check["id"];
        $store_type = $row_check["store_type"];
        $NAME = $row_check["NAME"];
        $LOGO_check = $row_check["LOGO"];
        $isAdmin = $row_check['ADMIN'];
    }

    // Feedback para credenciais inválidas
    if (empty($id_check)) {
        $message = "<div class=\"alert alert-danger\" id=\"flash-msg\"><h4><i class=\"icon fa fa-times\"></i>Usuário ou senha inválidos!</h4></div>";
        echo $message;
    } else {
        // Armazena dados na sessão se login for válido
        $_SESSION["admin"] = $isAdmin;
        $_SESSION["N"] = $id_check;
        $_SESSION["id"] = $id_check;
        $_SESSION["store_type"] = $store_type;

        // Redireciona para página adequada conforme tipo de loja
        $path = "all_users";
        if ($store_type == '2') {
            $path .= '_mac';
        }
        header("Location: $path.php");
    }
    $db_check1->close();
}

// ==============================================
// SEÇÃO 5: CARREGAMENTO DA IMAGEM DE LOGO
// ==============================================
$date = date("d-m-Y H:i:s");
$IPADDRESS = getIPAddress(); // Obtém IP do visitante

// Carrega configurações da imagem de logo de um JSON
$jsonFilex = './img/logo/logo_filenames.json';
$jsonDatax = file_get_contents($jsonFilex);
$imageDatax = json_decode($jsonDatax, true);

// Processa o caminho da imagem conforme método de upload (arquivo ou URL)
$filenamex = $imageDatax[0]['ImageName'];
$uploadmethord = $imageDatax[0]['Upload_type'];

if ($uploadmethord == "by_file") {
    $string = $filenamex;
    $firstLetterRemoved = substr($string, 1); // Remove primeiro caractere (geralmente uma barra)
    $imageFilex = "$firstLetterRemoved";
    $methord = "   Upload Method";
} elseif ($uploadmethord == "by_url") {
    $imageFilex = "$filenamex";
    $methord = "   URL Method";
} else {
    // Imagem padrão caso não haja configuração
    $imageFilex = "https://c4.wallpaperflare.com/wallpaper/159/71/731/errors-minimalism-typography-red-wallpaper-preview.jpg";
    $methord = "";
}
?>

<!-- ============================================== -->
<!-- SEÇÃO 6: HTML - PÁGINA DE LOGIN -->
<!-- ============================================== -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Configurações básicas da página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ibo Rev 09 Temas</title>
    
    <!-- Inclusão de bibliotecas e estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="css/sb-admin-<?php echo $col2; ?>.css" rel="stylesheet"> <!-- Tema dinâmico -->
    <link rel="icon" href="img/atvwhite.png" type="image/png">
    
    <!-- Estilos customizados -->
    <style>
        /* Layout responsivo para mobile */
        @media (max-width: 767px) {
            body { padding-top: 40px; background-color: black; color: white; }
            .container { padding: 0 20px; }
        }
        
        /* Estilos gerais */
        body { font-family: 'Roboto', sans-serif; }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }
        
        /* Estilo do formulário */
        .form-container {
            background-color: #000;
            padding: 20px;
            border-radius: 10px;
            border-top: 5px solid #4e73df;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            width: 100%;
            max-width: 400px;
        }
        
        /* Outros elementos de formulário */
        .form-group { margin-bottom: 1.5rem; }
        .btn { padding: 5px 20px; font-size: 16px; }
        .btn2 { padding: 10px 20px; font-size: 20px; }
        .password-toggle-icon { cursor: pointer; user-select: none; }
        .outside-image { width: 30%; max-width: 100%; margin-bottom: 20px; }
        .form-title { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px; }
        .image-container { display: flex; justify-content: center; margin-bottom: 20px; }
    </style>
</head>

<body class="bg-gradient-primary">
    <!-- Container principal -->
    <div class="container">
        <!-- Formulário de login -->
        <div class="form-container">
            <!-- Logo dinâmico -->
            <div class="image-container">
                <img src="<?= $imageFilex ?>" alt="Logo do Sistema" class="img-fluid outside-image">
            </div>
            
            <div class="form-title">Painel IBO Revenda 09 Temas Teste Auto</div>
            
            <!-- Formulário -->
            <form method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="NOME DE USUÁRIO" name="username" required autofocus />
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" placeholder="SENHA" name="password" required />
                        <button type="button" class="btn btn-secondary password-toggle-icon" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button class="btn2 btn-lg btn btn-primary btn-block" name="login" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-easing@1.4.1/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    
    <!-- Função para mostrar/esconder senha -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('[name="password"]');
            const passwordIcon = document.querySelector('.password-toggle-icon i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye-slash';
            }
        }
    </script>
</body>
</html>