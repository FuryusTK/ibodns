<?php


ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
session_start();

$id_store = $_SESSION['id'];

error_reporting(32767);
$db = new SQLite3("./api/.ansdb.db");

// if (isset($_GET["delete"])) {
//     $db->exec("DELETE FROM ibo WHERE id=" . $_GET["delete"]);
//     $db->close();
    
//     for($i = 0; $i < count($_SESSION['macs']); $i++){
//         $session_row = $_SESSION['macs'][$i];
//         if($session_row['id'] == $_GET["delete"]){
//             unset($_SESSION['macs'][$i]);
//         }
//     }
    
//     header("Location: users_mac.php");
// }

if (isset($_GET["activate"])) {
    $new_expire_date = date('Y-m-d', strtotime('+1 year'));
    $db->exec("UPDATE ibo SET active = 1, expire_date = '$new_expire_date'  WHERE id=" . $_GET["activate"]);
    
    if(isset($_SESSION['macs'])){
        for($i = 0; $i < count($_SESSION['macs']); $i++){
            $session_row = $_SESSION['macs'][$i];
            if($session_row['id'] == $_GET["activate"]){
                $_SESSION['macs'][$i]['active'] = 1;
                $_SESSION['macs'][$i]['expire_date'] = $new_expire_date;
            }
        }
    }else{
        $res = $db->query("SELECT * FROM ibo WHERE id = ". $_GET["activate"]);
        $_SESSION['macs'] = [];
        while ($row = $res->fetchArray()) {
            array_push($_SESSION['macs'], $row);
        }
    }
    $db->close();
    header("Location: users_mac.php");
}

if($_POST) {
    $mac_address = strtoupper($_POST['mac_address']);
    $db = new SQLite3("./api/.ansdb.db");
    $macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$mac_address' AND id_user = '$id_store'");
}

include "includes/header_mac.php";
?>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm</h2>
            </div>
            <div class="modal-body">
                Do you really want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>
<?php
    $dbUsers = new SQLite3("./api/.anspanel.db");
    $res = $dbUsers->query("SELECT mac_amount FROM USERS WHERE id = '$id_store' ");
    $macCount = $res->fetchArray()['mac_amount'];
    $dbUsers->close();
                
    $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$id_store' AND active = 1 AND expire_date > date('now')");
    $count = $res->fetchArray()['count'];
    
    $available = $macCount - $count;

    
?>

<main role="main" class="col-15 pt-4 px-5">
    
    <div class="row justify-text-center">
        <div id="main"><br>
            
            <form method="post">
                <h2>Ativações de Macs</h2>
                <div class='alert alert-warning'><b>Você pode registrar mais <?= $available; ?> MACs (Limite de <?= $macCount; ?>)</b></div>
                <div class="input-group">
                    <a class="btn btn-success btn-icon-split" id="button" href="users_create.php">
                        <span class="icon text-white-50"><i class="fas fa-check"></i></span><span class="text">Criar novo</span>
                    </a>
                </div>
                <br>
                <div style="display: flex; margin-bottom: 20px;">
                    <input class="form-control" type="text" autocomplete="false" id="mac" placeholder="MAC" name="mac_address" maxlength="17" style="text-transform: uppercase" />
                        <button style="margin-left: 10px;" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
  

    <?php if($macRes || isset($_SESSION['macs'])) { ?>
    <div class="table-responsive">
        <table  id="myTable" class="table table-striped table-sm">
            <thead class= "text-primary">
                <tr>
                <th>ID</th>
                <th>Device ID</th>
                <th>Username</th>
                <th>Expire Date</th>
                <th>DNS / M3U</th>
                <th>EPG</th>
                <th>Title</th>
                <!--<th>Edit</th>-->
                <!--<th>Delete</th>-->
                </tr>
            </thead>
    <?php } ?>
      </div>

<?php
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
    
    $today = date('Y-m-d');
    $active = $row['active'] && $iexpire_date >= $today;
    
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
    echo "                  <td>" . $idns . "</td>" . "\n";
    echo "                  <td>" . $iepg . "</td>" . "\n";
    echo "                  <td>" . $ititle . "</td>" . "\n";
    // echo "                  <td><a class=\"btn btn-icon\" href=\"./users_update.php?update=$iid\"><span class=\"icon text-white-50\"><i class=\"fa fa-pencil\" style=\"font-size:24px;color:blue\"></i></span></a></td>" . "\n";
    // echo "                  <td><a class=\"btn btn-icon\" href=\"#\" data-href=\"./users_mac.php?delete=$iid&pwdreq=$pwd_req\" data-toggle=\"modal\" data-target=\"#confirm-delete\"><span class=\"icon text-white-50\"><i class=\"fa fa-trash\" style=\"font-size:24px;color:red\"></i></span></a></td>" . "\n";
    if(!$active){
        echo "                  <td><a class=\"btn btn-sm btn-success\" href=\"./users_mac.php?activate=$iid\">Ativar</a></td>";
    }
    echo "\t\t\t\t</tr>\n\t\t\t</tbody>\n";
}
if($macRes){
    while ($row = $macRes->fetchArray()) {
        create_line($row);
    }
}else if(isset($_SESSION['macs'])) {
    foreach ($_SESSION['macs'] as $mac_row){
        create_line($mac_row);
    }
}
?>
    <?php if($macRes || isset($_SESSION['macs'])) { ?>
        </table>
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