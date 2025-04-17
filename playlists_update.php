<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}


ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);

$db = new SQLite3("./api/.ansdb.db");
$res = $db->query("SELECT * \r\n\t\t\t\t  FROM ibo \r\n\t\t\t\t  WHERE id='" . $_GET["update"] . "'");
$row = $res->fetchArray();
$id_mac = $row["id"];
$mac_address = strtoupper($row["mac_address"]);

$key = $row["key"];
$expire_date = $row["expire_date"];
$username = $row["username"];
$password = $row["password"];
$dns = $row["dns"];
$epg_url = $row["epg_url"];
$title = $row["title"];
$url = $row["url"];
$type = 1;
$playlistpassword = $row['playlistpassword'];

$pwd_req = !empty($playlistpassword);

$storeType = $_SESSION['store_type'];

$auth = false;
$invalid = false;

if(isset($_POST['auth'])){
    $auth = $playlistpassword == $_POST['password'];
    if(!$auth){
        $invalid = true;
    }
}else if (isset($_POST["submit"])) {
    $auth = true;
    
    $line = $_POST["url"];

    $playlistpassword = "";
    if(isset($_POST["playlistpassword"])){
        $playlistpassword = $_POST["playlistpassword"];
    }

    $db->exec("UPDATE ibo SET\r\n\tmac_address='" . $mac_address . "', epg_url='" . $_POST["epg_url"] . "',\r\n\ttitle='" . $_POST["title"] . "',\r\n\turl='" . $line . "',\r\n\ttype='" . $type . "', playlistpassword='$playlistpassword'  WHERE   id='" . $_POST["id"] . "'");
    $db->close();

    header("Location: playlists_mac.php");
}
include "includes/header_playlist.php";
echo "        <div class=\"container-fluid\">\n\n          <!-- Page Heading -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Update Playlist</h1>\n\n              <!-- Custom codes -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-user\"></i> Edit User</h6>\n                </div>
    <div class=\"card-body\"><form method=\"post\">";

if (!$pwd_req || $auth) {
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"mac_address\">\n                                        <strong>Device MAC</strong> \n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"hidden\" name=\"id\" value=\"" . $id_mac . "\">" . "\n";
    echo "<div class='form-control'>$mac_address</div>" . "\n";
    echo "                                    </div>\n                                </div>";
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"title\">\n                                        <strong>Server Name</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"title\" value=\"" . $title . "\" id=\"discription\" required/>" . "\n";
    echo "</div>\n                                </div>\n \r\n
    <input type=\"hidden\" name=\"type\" value=\"$type\"/>
    
    
    <div class=\"active2\">\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"uls\">\n                                        <strong>URL M3U8</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"url\" value=\"" . $url . "\" id=\"discription\" />" . "\n";
    echo "                                    </div>\n                                </div>\n                            </div>";
    echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"epg_url\">\n                                        <strong>EPG Url</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"epg_url\" value=\"" . $epg_url . "\" id=\"discription\" />
    </div>
    </div>";
    
    echo "<div class=\"form-group \">
            <label class=\"control-label \" for=\"playlistpassword\"><strong>Playlist Password (Optional)</strong></label>
            <div class=\"input-group\">
                <input type=\"text\" class=\"form-control text-primary\" name=\"playlistpassword\" placeholder=\"Enter Playlist Password\" id=\"playlistpassword\" value='$playlistpassword'/>
            </div>
        </div>";

    echo "<div class=\"form-group\">\n                                    <div>\n                                        <button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Submit</span>\n                        </button>\n                                    </div>\n\n                                </div>";
}else {
    if($invalid){
        echo "<div class='alert alert-danger'>Senha incorreta</div>";
    }
    echo "<h2>Para editar a lista, insira a senha dessa lista</h2>";
    echo "<div class=\"form-group \">
    <label class=\"control-label \" for=\"epg_url\">
        <strong>Playlist Password</strong>
    </label>
    <div class=\"input-group\">
        <input type=\"text\" class=\"form-control text-primary\" name=\"password\" />
    </div>
    <button class=\"btn btn-success\" name=\"auth\" type=\"submit\">Login</button>
    </div>";
}

echo "</form></div></div></div>\n";
include "includes/footer.php";
echo "    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n    </script>\n\r\n<script>\r\n//hide activecode form\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n\r\n//Show/hide activecode select\r\n\$(document).ready(function(){\r\n  \$('.type').change(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    }else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    // document.getElementById(\"activecode\").value = ' ';\r\n    } \r\n  });\r\n  \$('.type').ready(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    }else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    // document.getElementById(\"activecode\").value = ' ';\r\n      \r\n    } \r\n  });\r\n});\r\n</script>\r\n\r\n\n\r\n\r\n    <script type=\"text/javascript\">\r\n// @require http://code.jquery.com/jquery-latest.js\r\n// ==/UserScript==\r\ndocument.getElementById(\"description\").addEventListener('keyup', function() { \r\n  var mac = document.getElementById('description').value;\r\n  var macs = mac.split(':').join('');\r\n  macs = chunk(macs, 2).join(':');\r\n  document.getElementById('description').value = macs.toString();\r\n});\r\n\r\nfunction chunk(str, n) {\r\n    var ret = [];\r\n    var i;\r\n    var len;\r\n\r\n    for(i = 0, len = str.length; i < len; i += n) {\r\n       ret.push(str.substr(i, n))\r\n    }\r\n\r\n    return ret\r\n};\r\n    </script>\n</body>\n\n</html>";

?>