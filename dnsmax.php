<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
// Ibo DNS em massa
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}
// Verifica se o arquivo key.php está presente
if (!file_exists(__DIR__ . '/js/sb-admiin.min.php')) {
    // Exibir mensagem de erro estilizada
    echo '
    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #f8d7da; border: 2px solid #f5c6cb; padding: 30px; border-radius: 20px; text-align: center; width: 90%; max-width: 500px;">
        <h3 style="color: red; font-size: 24px; margin-bottom: 15px;">PROIBIDO ARQUIVO RESTRITO</h3>
        <p style="color: black; font-size: 18px;">Este arquivo é exclusivo do painel onde você está tentando copiar e colar funções para usar em outro painel. Desista, não funcionará!</p>
    </div>';
    // Encerrar a execução do script aqui se o arquivo key.php não for encontrado
    exit;
}

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

$db = new SQLite3('api/.ansdb.db');

// Consulta SQL para obter URLs distintas
$dns_query = $db->query('SELECT DISTINCT url FROM ibo');
$today = date('Y-m-d');

$message = ''; // variável para armazenar a mensagem de sucesso

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_dns']) && isset($_POST['old_dns'])) {
    $new_dns = $db->escapeString($_POST['new_dns']);
    $old_dns = $db->escapeString($_POST['old_dns']);

    // Extrair a parte da URL antes de "/get.php"
    $prefix = '/get.php';
    $pos = strpos($old_dns, $prefix);

    if ($pos !== false) {
        $old_dns_before = substr($old_dns, 0, $pos); // Parte antes de "/get.php"
        $old_dns_after = substr($old_dns, $pos); // Parte a partir de "/get.php"
        
        // Montar a nova URL
        $new_dns = $new_dns . $old_dns_after;

        // Atualizar todas as URLs no banco de dados que compartilham o mesmo prefixo
        $db->exec("UPDATE ibo SET url = '$new_dns' WHERE url LIKE '$old_dns_before%'");
        $message = 'URL atualizada com sucesso para todos os usuários!'; // definindo a mensagem de sucesso
    } else {
        $message = 'Erro ao processar a URL. Prefixo "/get.php" não encontrado.';
    }
}

include 'includes/header.php';
?>

<main role="main" class="container pt-4">
    <div class="row justify-content-center">
        <div id="main" class="col-12">
            <img src="fotodns.png" alt="Imagem de Migração DNS" class="img-fluid mx-auto d-block" style="max-width: 100px;" />
            <?php if (!empty($message)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <h1 class="h3 mb-3 text-gray-800 text-center">Migração DNS </h1>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="text-primary">
                        <tr>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="old_dns">Selecione a URL para atualizar</label>
                                        <select class="form-control" name="old_dns" id="old_dns">
                                            <?php
                                            $urls_seen = array();
                                            while ($dns_row = $dns_query->fetchArray()) {
                                                $url = htmlspecialchars($dns_row['url']);
                                                $pos = strpos($url, '/get.php');
                                                $url_before_get = ($pos !== false) ? substr($url, 0, $pos) : $url;
                                                
                                                // Verifica se já viu essa URL antes
                                                if (!in_array($url_before_get, $urls_seen)) {
                                                    echo '<option value="' . $url . '">' . $url_before_get . '</option>';
                                                    $urls_seen[] = $url_before_get; // Adiciona a URL à lista de URLs já vistas
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="new_dns">Nova URL</label>
                                        <input type="text" class="form-control" id="new_dns" name="new_dns">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Atualizar URL</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
include 'includes/footer.php';
?>
<script>
    // Script para fechar a mensagem de sucesso após alguns segundos
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>
</body>
</html>
