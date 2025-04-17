<?php


ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
session_start();

$isAdmin = $_SESSION['admin'];

if(!$isAdmin){
    header("Location: login.php");
    exit();
}

error_reporting(32767);

if (isset($_GET["delete"])) {
    $db = new SQLite3("./api/.anspanel.db");

    $delete_id = $_GET['delete'];
    print_r($_GET);

    echo $delete_id;
    $db->exec("DELETE FROM USERS WHERE id= '$delete_id'");
    $db->close();

    $db = new SQLite3("api/.ansdb.db");
    $db->exec("DELETE FROM ibo WHERE id_user = '$delete_id'");
    $db->close();

    header("Location: stores_mac.php");
}
include "includes/header.php";
?>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirmar</h2>
            </div>
            <div class="modal-body">
                Você realmente quer apagar esse usuário?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Apagar</a>
            </div>
        </div>
    </div>
</div>
<main role="main" class="col-15 pt-4 px-5">
    <div class="row justify-text-center"><div class="chartjs-size-monitor" style="position:absolute ; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
        <div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
            <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
        </div>
        <div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
            <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
        <div id="main">
            
          <!-- Page Heading -->
            <br>
            <h2>Revendas de Ativação Anual</h2>
            <div class="input-group ">
                <a button class="btn btn-success btn-icon-split" id="button" href="stores_mac_create.php">
                    <span class="icon text-white-50"><i class="fas fa-check"></i></span><span class="text">Criar</span>
                    </button>
                </a>&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      
                <div class="input-group-prepend">
                    <span class="input-group-text" style="font-size:24px;color:#1cc88a">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <input class="form-control" type="text" id="search" placeholder="Search"  name="search_value"/>
            </div>
        </div>
        <div class="table-responsive">
            <table id="myTable" class="table table-striped table-sm">
            <thead class= "text-primary">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Mac</th>
                    <th>MACs Disponíveis</th>
                    <th>Editar</th>
                    <th>Apagar</th>
                </tr>
            </thead>

<?php
$db = new SQLite3("./api/.anspanel.db");
$db2 = new SQLite3("./api/.ansdb.db");
$res = $db->query("SELECT * FROM USERS WHERE (ADMIN != 1 OR ADMIN IS NULL) AND store_type = 2");
while ($row = $res->fetchArray()) {
    $id = $row["id"];
    $name = $row["NAME"];
    $username = $row["USERNAME"];
    $macs = $row["mac_amount"];
    
    $macsUsed = $db2->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$id' AND active = 1 AND expire_date > date('now')");
    $macsUsed = $macsUsed->fetchArray()['count'];
    
    $macsAvailable = $macs - $macsUsed;
    
    echo "              <tbody class=\" text-primary\">\n";
    echo "                  <td>" . $id . "</td>" . "\n";
    echo "                  <td>" . $name . "</td>" . "\n";
    echo "                  <td>" . $username . "</td>" . "\n";
    echo "                  <td>" . $macs . "</td>" . "\n";
    echo "                  <td>" . $macsAvailable . "</td>" . "\n";
    echo "                  <td><a class=\"btn btn-icon\" href=\"./stores_mac_update.php?update=" . $id . "\"><span class=\"icon text-white-50\"><i class=\"fa fa-pencil\" style=\"font-size:24px;color:blue\"></i></span></a></td>" . "\n";
    echo "                  <td><a class=\"btn btn-icon\" href=\"#\" data-href=\"./stores_mac.php?delete=" . $id . "\" data-toggle=\"modal\" data-target=\"#confirm-delete\"><span class=\"icon text-white-50\"><i class=\"fa fa-trash\" style=\"font-size:24px;color:red\"></i></span></a></td>" . "\n";
    echo "\t\t\t\t</tr>\n\t\t\t</tbody>\n";
}
echo "\t\t\t</table>\n\t\t</div>\n</main>\n\n    <br><br><br>\n";
include "includes/footer.php";
?>

<script>
$("#search").keyup(function () {
    var value = this.value.toLowerCase().trim();

    $("table tr").each(function (index) {
        if (!index) return;
        $(this).find("td").each(function () {
            var id = $(this).text().toLowerCase().trim();
            var not_found = (id.indexOf(value) == -1);
            $(this).closest('tr').toggle(!not_found);
            return not_found;
        });
    });
});

$('#confirm-delete').on('show.bs.modal', function(e) {
    $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
});
</script>
</body>
</html>

