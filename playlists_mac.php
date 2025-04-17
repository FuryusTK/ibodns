<?php
session_start();
// Verificar se o usu®¢rio est®¢ autenticado e ®¶ um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}


ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);

$id_mac = $_SESSION['id'];
$mac_address = $_SESSION['mac_address'];

error_reporting(32767);
$db = new SQLite3("./api/.ansdb.db");

$macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$mac_address' AND store_type = 2");

$validStore = true;
while ($row = $macRes->fetchArray()) {
    if(empty($row['id_user'])){
        $validStore = false;
        break;
    }
}

if(!$validStore && isset($_POST['store_link'])){
    $dbUsers = new SQLite3("./api/.anspanel.db");
    
    $store_id = $_POST['store_id'];
    
    $ret = $dbUsers->query("SELECT COUNT(*) as count FROM USERS WHERE id = '$store_id'");
    $storeExists = $ret->fetchArray()['count'] > 0;
    
    if($storeExists){
    
        $res = $dbUsers->query("SELECT mac_amount FROM USERS WHERE id = '$store_id' ");
        $macCount = $res->fetchArray()['mac_amount'];
    
        $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$store_id' AND active = 1 AND expire_date > date('now')");
        $count = $res->fetchArray()['count'];
    
        $limited = $count >= $macCount;
        
        if(!$limited){
            $db->query("UPDATE ibo SET id_user = '$store_id' WHERE mac_address = '$mac_address' AND store_type = 2 AND (id_user = '' OR id_user IS NULL)");
            $validStore = true;
        }
    }
    
    
    $dbUsers->close();
}

include "includes/header_playlist.php";

function create_line($row){
    
    $playlist_password = $row['playlistpassword'];

    $iid = $row["id"];
    $imac = $row["mac_address"];
    $ikey = $row["key"];
    $iexpire_date = $row["expire_date"];
    if ($idns = $row["dns"] == NULL) {
        $iusername = "listm3u-id" . $row["id"];
        $idns = $row["url"];
    } else {
        $iusername = $row["username"];
        $idns = $row["dns"];
    }
    $iepg = $row["epg_url"];
    $ititle = $row["title"];
    
    $pwd_req = 0;

    if ($playlist_password) {
        $pwd_req = 1;
        $idns = "****";
        $iepg = "****";
        $ititle = "****";
    }
        
    echo "              <tbody class=\" text-primary\">\n";
    echo "                  <td>" . $iid . "</td>" . "\n";
    echo "                  <td>" . $imac . "</td>" . "\n";
    echo "                  <td>" . $iusername . "</td>" . "\n";
    echo "                  <td>" . $iexpire_date . "</td>" . "\n";
    echo "                  <td>" . $iexpire_date . "</td>" . "\n";
    echo "                  <td>" . $iexpire_date . "</td>" . "\n";
    echo "                  <td>" . $idns . "</td>" . "\n";
    echo "                  <td>" . $iepg . "</td>" . "\n";
    echo "                  <td>" . $ititle . "</td>" . "\n";
    echo "                  <td><a class=\"btn btn-icon\" href=\"./playlists_update.php?update=$iid\"><span class=\"icon text-white-50\"><i class=\"fa fa-pencil\" style=\"font-size:24px;color:blue\"></i></span></a></td>" . "\n";
    echo "\t\t\t\t</tr>\n\t\t\t</tbody>\n";
}

?>
<main role="main" class="col-15 pt-4 px-5">

<?php 
    if($validStore) {
        $macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$mac_address' AND store_type = 2");
    ?>
                <h2>Playlist Dashboard</h2>
                <div class="input-group">
                    <a class="btn btn-success btn-icon-split" id="button" href="playlists_create.php">
                        <span class="icon text-white-50"><i class="fas fa-check"></i></span><span class="text">Create</span>
                    </a>
                </div>

                <?php if($macRes) { ?>
                <p style="color: red">Proteja sua playlist com senha clicando em Editar, e configurando uma senha</p>
                <div class="table-responsive">
                    <table  id="myTable" class="table table-striped">
                        <thead class= "text-primary">
                            <tr>
                            <th>ID</th>
                            <th>Device ID</th>
                            <th>Username</th>
                            <th>Expire Date</th>
                            <th>Expire Date</th>
                            <th>Expire Date</th>
                            <th>DNS / M3U</th>
                            <th>EPG</th>
                            <th>Title</th>
                            <th>Edit</th>
                            </tr>
                        </thead>
                <?php } 

        while ($row = $macRes->fetchArray()) {
            create_line($row);
        }

         if($macRes) { ?>
            </table>
        </div>
        <?php } ?>
    <?php } else { 
        if(isset($storeExists) && !$storeExists) {
            echo "<div class='alert alert-danger'>C√≥digo de vendedor inv√°lido</div>";
        }
        if(isset($limited) && !$limited) {
            echo "<div class='alert alert-danger'>Limite de MACs excedidos para a loja</div>";
        }
    ?>
    <div class="row justify-text-center">
        <div id="main"><br>
            <form method="post">
                <h2>Para ativar esse dispositivo, insira o c√≥digo do vendedor: </h2>
                <div class="form-group">
                    <label class="control-label" for="epg_url">
                        <strong>C√≥digo do vendedor</strong>
                    </label>
                <div class="input-group">
                    <input type="text" class="form-control text-primary" name="store_id" />
                </div>
                <button class="btn btn-success" name="store_link" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
    <?php } ?>
    

</main>

<?php
include "includes/footer.php";
?>

<script type="text/javascript">
    let element = document.getElementById("mac"); 
    element.addEventListener('keydown', function() { 
      var mac = element.value;
      var macs = mac.split(':').join('');
      macs = chunk(macs, 2).join(':');
      element.value = macs.toString();
    });

    function chunk(str, n) {
        var ret = [];
        var i;
        var len;

        for(i = 0, len = str.length; i < len; i += n) {
           ret.push(str.substr(i, n));
        }

        return ret;
    };
</script>

<?php
echo "<script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n    </script>\n</body>\n
</html>";

?>