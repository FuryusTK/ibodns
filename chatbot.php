<?php
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['btn-save'])) {
    file_put_contents('trial_mode', isset($_POST['trial']) ? $_POST['trial'] : '');   
    
    if(isset($_POST['app_dns'])){
        file_put_contents("app_dns", $_POST['app_dns']);
        file_put_contents("app_url", $_POST['app_url']);
    }
}

$IIIIIIIlI1lI = $_SESSION['id'];
$IIIIIIIlI1ll = new SQLite3('api/.anspanel.db');
$IIIIIIIlI1l1 = $IIIIIIIlI1ll->query('SELECT * FROM USERS WHERE id=\'1\'');
$IIIIIIIlI11I = $IIIIIIIlI1l1->fetchArray();
$IIIIIIIlI11l = $IIIIIIIlI11I['NAME'];
$IIIIIIIlI111 = $IIIIIIIlI11I['LOGO'];
echo '<!DOCTYPE html>'."\n";
echo '<html lang="en">'."\n";
echo "\n";
echo '<head>'."\n";
echo "\n";
$IIIIIIIllIII = file_get_contents('includes/eggzie.json');
$IIIIIIIllIIl = json_decode($IIIIIIIllIII,true);
$IIIIIIIllII1 = $IIIIIIIllIIl['info'];
$IIIIIIIllIlI = $IIIIIIIllII1['aa'];
$app_dns = file_get_contents('app_dns');
$app_url = file_get_contents('app_url');

$mode = file_get_contents('trial_mode');

$checked = $mode == 'trial' ? "checked" : "";
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
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

    <center><h1 class="h3 mb-1 text-gray-800">Ajuste de ChatBot</h1></center>

    <!-- Content Row -->
    <div class="row">
        <!-- First Column -->
        <div class="col-lg-12">
            <!-- Custom codes -->
            <div class="shadow h-100 card shadow mb-4">
                <div class="card-header py-3">
                    <center><h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-bullhorn"></i> ChatBot</h6></center>
                </div>
                <div class="card-body">
                    
                    <?php echo "<div>
                        <form method='POST'>
                        
                        <div class='form-check'>
                        <input class='form-check-input' name='trial' type='checkbox' value='trial' id='trial_check' $checked>
                          <label class='form-check-label' for='trial_check'>
                            Habilitar teste automático
                          </label>
                        </div>";
                    if ($mode == 'trial') {
                        echo "<b>Dados do chatBot</b><br><br>
                            <label>DNS</label>
                            <input class='form-control' type='text' name='app_dns' placeholder='Insira a DNS' value='$app_dns'/><br>
                            <label>LINK</label>
                            <input class='form-control' type='text' name='app_url' placeholder='Link do chatBot' value='$app_url' /><br>";
                    }
                            
                    echo "<center><button name='btn-save' class='btn btn-success'>Salvar</button></center>
                        </form>
                        </div>";
                    
                    ?>
                    
                    
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>