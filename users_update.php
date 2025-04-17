<?php

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
session_start();

$db = new SQLite3("./api/.ansdb.db");
$res = $db->query("SELECT * FROM ibo WHERE id='" . $_GET["update"] . "'");
$row = $res->fetchArray();
$id_mac = $row["id"];
$mac_address = $row["mac_address"];
$key = $row["key"];
$expire_date = $row["expire_date"];
$username = $row["username"];
$password = $row["password"];
$dns = $row["dns"];
// Removido o campo epg_url
$title = $row["title"];
$url = $row["url"];
$type = $row["type"];
$playlistpassword = $row['playlistpassword'];
$id_user = $row['id_user'];
$active = $row['active'];

// Verifica se active está vazio e atribui "1" (Ativado) por padrão
$active_default = empty($active) ? 1 : $active;

$pwd_req = !empty($playlistpassword);
$storeType = $_SESSION['store_type'];
$auth = false;

if (isset($_POST['auth'])) {
    $auth = $playlistpassword == $_POST['password'];
} else if (isset($_POST["submit"])) {
    $auth = true;
    $address1 = strtoupper($_POST["mac_address"]);
    if ($_POST["type"] == "0") {
        $line = $_POST["dns"] . "/get.php?username=" . $_POST["username"] . "&password=" . $_POST["password"] . "&type=m3u_plus&output=ts";
    } else {
        $line = $_POST["url"];
    }

    $playlistpassword = "";
    if (isset($_POST["playlistpassword"])) {
        $playlistpassword = $_POST["playlistpassword"];
    }

    $active = $_POST["active"] == 1 ? 1 : 'NULL';

    if ($active === 'NULL') {
        $ne = date('Y-m-d', strtotime('-1 day'));
    } else {
        $we = strtotime($_POST["expire_date"]);
        $ne = date("Y-m-d", $we);
    }

    $db->exec("UPDATE ibo SET
        mac_address='" . $address1 . "',
        key='" . $_POST["key"] . "',
        expire_date='" . $ne . "',
        username='" . $_POST["username"] . "',
        password='" . $_POST["password"] . "',
        dns='" . $_POST["dns"] . "',
        title='" . $_POST["title"] . "',
        url='" . $line . "',
        type='" . $_POST["type"] . "',
        playlistpassword='$playlistpassword',
        id_user='" . $_POST["id_user"] . "',
        active=$active
        WHERE id='" . $_POST["id"] . "'");

    $return = "all_users.php";

    if (isset($_SESSION['macs'])) {
        $res = $db->query("SELECT * FROM ibo WHERE id = " . $_POST["id"]);
        $row = $res->fetchArray();
        $db->close();

        for ($i = 0; $i < count($_SESSION['macs']); $i++) {
            $session_row = $_SESSION['macs'][$i];
            if ($session_row['id'] == $_POST["id"]) {
                $_SESSION['macs'][$i] = $row;
            }
        }
    }

    header("Location: $return");
}

include "includes/header.php";
echo "        <div class=\"container-fluid\">\n\n          <!-- Página Principal -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Atualizar Usuário</h1>\n\n              <!-- Códigos Personalizados -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-user\"></i> Editar Usuário</h6>\n                </div>
    <div class=\"card-body\"><form method=\"post\">";

if (!$pwd_req || $auth) {
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"mac_address\">\n                                        <strong>MAC</strong> \n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"hidden\" name=\"id\" value=\"" . $id_mac . "\">" . "\n";
    echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"mac_address\" value=\"" . $mac_address . "\" type=\"text\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n                        <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"key\">\n                                       <strong>Chave</strong> \n                                   </label>\n                                   <div class=\"input-group\">\n";
    echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"key\" value=\"136115\" type=\"text\" readonly/>" . "\n";
    echo "                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"title\">\n                                        <strong>Cliente</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"title\" value=\"" . $title . "\" id=\"discription\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n \r\n                  <div class=\"form-group\">\n                                    \r\n                              <div>\n   \r\n                              <strong style=\"display:none;\">Selecione o modo de login: </strong>\n";
    // A parte "Selecione o modo de login:" foi ocultada
    echo "<input type=\"hidden\" class=\"form-control text-primary\" name=\"type\" value=\"" . $type . "\" />\r\n";

    echo "                          <div class=\"active2\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"uls\">\n                                        <strong>URL M3U8</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"url\" value=\"" . $url . "\" id=\"discription\" />" . "\n";
    echo "                                    </div>\n                                </div>\n                            </div>\n                          <div class=\"active1\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"expire_date\">\n                                        <strong>Válidade</strong>\n                                    </label>\n                                    <div class=\"input-group\"><input type=\"text\" class=\"form-control text-primary\" name=\"expire_date\" placeholder=\"YYYY-MM-DD\" id=\"datetimepicker\" value=\"" . $expire_date . "\" /> " . "\n";
    echo "                                    </div>\n\n                                </div>";

    // Campo EPG URL removido

    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"id_user\">\n                                        <strong>Código Revenda</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
// Verifica se o valor de id_user é vazio e atribui "1" como padrão
    $id_user_default = empty($id_user) ? "1" : $id_user;
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"id_user\" value=\"" . $id_user_default . "\" id=\"id_user\" pattern=\"[0-9]*\" title=\"Apenas números\" required/>" . "\n";
    echo "                                    </div>\n                                </div>\n";

    // Campo Status com "Ativado" já selecionado
    echo "<div class=\"form-group\">\n                                    <label class=\"control-label \" for=\"active\">\n                                        <strong>Status</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <select class=\"form-control text-primary\" name=\"active\" id=\"active\">\n";
    echo "                                            <option value=\"1\"" . ($active_default == 1 ? " selected" : "") . ">Ativado</option>\n";
    echo "                                            <option value=\"NULL\"" . ($active_default === 'NULL' ? " selected" : "") . ">Desativado</option>\n";
    echo "                                        </select>\n";
    echo "                                    </div>\n                                </div>\n";

} else {
    echo "<div class=\"form-group\">\n    <label for=\"password\">Digite a senha de playlist:</label>\n    <input type=\"password\" name=\"password\" class=\"form-control\" id=\"password\" required>\n</div>";
}

echo "<button type=\"submit\" name=\"submit\" class=\"btn btn-primary\">Salvar</button>\n</form>\n</div></div></div></div>";

include "includes/footer.php";

?>
