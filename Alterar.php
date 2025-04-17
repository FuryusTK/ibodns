<?php
session_start();
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: index.php"); 
    exit();
}

// Conectar ao banco de dados de links de pagamento
$db3 = new SQLite3('./api/.payment_links.db');

// Criar a tabela de links de pagamento, se não existir
$db3->exec("CREATE TABLE IF NOT EXISTS payment_links (
    mac_address VARCHAR(100) PRIMARY KEY,
    payment_link VARCHAR(250)
)");

// Variáveis para armazenar os valores dos formulários
$update_mac_address = '';
$existing_payment_link = '';
$error_message = '';

// Processar o formulário de atualizar link de pagamento
if (isset($_POST["update_submit"])) {
    $update_mac_address = $_POST["update_mac_address"];
    $update_payment_link = $_POST["update_payment_link"];

    // Recuperar o link de pagamento existente
    $payment_res = $db3->query("SELECT payment_link FROM payment_links WHERE mac_address='" . $update_mac_address . "'");
    $payment_row = $payment_res->fetchArray();
    $existing_payment_link = $payment_row['payment_link'] ?? '';

    // Atualizar o link de pagamento existente
    if ($existing_payment_link !== '') {
        $stmt = $db3->prepare("UPDATE payment_links SET payment_link=:payment_link WHERE mac_address=:mac_address");
        $stmt->bindValue(':mac_address', $update_mac_address, SQLITE3_TEXT);
        $stmt->bindValue(':payment_link', $update_payment_link, SQLITE3_TEXT);
        $stmt->execute();

        header("Location: Alterar.php"); 
        exit();
    }
}

include "includes/header.php";
?>

<div class="container-fluid">
    <h1 class="h3 mb-1 text-gray-800">Gerenciar Links de Pagamento</h1>

    <!-- Exibir mensagem de erro, se houver -->
    <?php if ($error_message): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
    <?php endif; ?>

    <!-- Formulário para atualizar um link de pagamento existente -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit"></i> Atualizar Link de Pagamento</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label class="control-label" for="update_mac_address"><strong>Endereço MAC</strong></label>
                    <input type="text" class="form-control" name="update_mac_address" value="<?php echo htmlspecialchars($update_mac_address); ?>" required />
                </div>
                <div class="form-group">
                    <label class="control-label" for="update_payment_link"><strong>Link de Pagamento</strong></label>
                    <input type="text" class="form-control" name="update_payment_link" value="<?php echo htmlspecialchars($existing_payment_link); ?>" required />
                </div>
                <button type="submit" name="update_submit" class="btn btn-primary">Atualizar</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
