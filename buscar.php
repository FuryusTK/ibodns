<?php
session_start();
$id = $_SESSION['id'];
$isAdmin = $_SESSION['admin'];
$storeType = $_SESSION['store_type'];

// Conectar ao banco de dados ansdb.db
$db = new SQLite3('./api/.ansdb.db');

// Variáveis para armazenar os valores dos formulários
$mac_address = '';
$id_user = '';
$existing_username = '';
$existing_url = '';
$error_message = '';
$success_message = '';

// Processar o formulário de busca
if (isset($_POST["search_submit"])) {
    $mac_address = $_POST["mac_address"];

    // Verificar se o campo está vazio
    if (empty($mac_address)) {
        $error_message = 'O endereço MAC não pode estar vazio.';
    } else {
        // Recuperar as informações do banco de dados
        $stmt = $db->prepare("SELECT id_user, username, url FROM ibo WHERE mac_address = :mac_address");
        
        if ($stmt) {
            $stmt->bindValue(':mac_address', $mac_address, SQLITE3_TEXT);
            $result = $stmt->execute();

            if ($result) {
                $row = $result->fetchArray(SQLITE3_ASSOC);

                if ($row) {
                    $id_user = $row['id_user'];
                    $existing_username = $row['username'];
                    $existing_url = $row['url'];
                } else {
                    $error_message = 'Nenhuma informação encontrada para o endereço MAC fornecido.';
                }
            } else {
                $error_message = 'Erro ao executar a consulta.';
            }
        } else {
            $error_message = 'Erro ao preparar a declaração SQL.';
        }
    }
}

// Processar o formulário de atualização
if (isset($_POST["update_submit"])) {
    $mac_address = $_POST["mac_address"];
    $id_user = $_POST["id_user"];
    $update_username = $_POST["update_username"];
    $update_url = $_POST["update_url"];

    // Atualizar as informações no banco de dados
    $stmt = $db->prepare("UPDATE ibo SET id_user = :id_user, username = :username, url = :url WHERE mac_address = :mac_address");

    if ($stmt) {
        $stmt->bindValue(':mac_address', $mac_address, SQLITE3_TEXT);
        $stmt->bindValue(':id_user', $id_user, SQLITE3_TEXT);
        $stmt->bindValue(':username', $update_username, SQLITE3_TEXT);
        $stmt->bindValue(':url', $update_url, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            $success_message = 'Informações atualizadas com sucesso.';
        } else {
            $error_message = 'Erro ao atualizar as informações.';
        }
    } else {
        $error_message = 'Erro ao preparar a declaração SQL.';
    }
}

include "includes/header.php";
?>

<div class="container-fluid">
    <h1 class="h3 mb-1 text-gray-800">Gerenciar Informações do MAC</h1>

    <!-- Exibir mensagem de erro, se houver -->
    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <!-- Exibir mensagem de sucesso, se houver -->
    <?php if ($success_message): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
    <?php endif; ?>

    <!-- Formulário de busca -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-search"></i> Buscar Informações do MAC</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label class="control-label" for="mac_address"><strong>Endereço MAC</strong></label>
                    <input type="text" class="form-control" id="mac_address" name="mac_address" value="<?php echo htmlspecialchars($mac_address); ?>" required />
                </div>
                <button type="submit" name="search_submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>
    </div>

    <!-- Formulário de atualização -->
    <?php if ($existing_username || $existing_url): ?>
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit"></i> Atualizar Informações</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label class="control-label" for="id_user"><strong>ID do Usuário</strong></label>
                    <input type="text" class="form-control" id="id_user" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="update_username"><strong>Nome de Usuário</strong></label>
                    <input type="text" class="form-control" id="update_username" name="update_username" value="<?php echo htmlspecialchars($existing_username); ?>" required />
                </div>
                <div class="form-group">
                    <label class="control-label" for="update_url"><strong>URL</strong></label>
                    <input type="text" class="form-control" id="update_url" name="update_url" value="<?php echo htmlspecialchars($existing_url); ?>" required />
                </div>
                <input type="hidden" name="mac_address" value="<?php echo htmlspecialchars($mac_address); ?>" />
                <button type="submit" name="update_submit" class="btn btn-primary">Atualizar</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
