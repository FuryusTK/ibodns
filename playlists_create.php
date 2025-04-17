<?php

ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);
session_start();

$id = $_SESSION['id'];
$storeType = $_SESSION['store_type'];
$mac_address = strtoupper($_SESSION["mac_address"]);

$type = 1;
$expire = date('Y-m-d', strtotime('+3 days'));

$limited = false;
$db = new SQLite3("./api/.ansdb.db");
$db->exec("CREATE TABLE IF NOT EXISTS ibo(id INTEGER PRIMARY KEY NOT NULL,\r\nmac_address VARCHAR(100),\r\nkey VARCHAR(100),\r\nusername VARCHAR(100),\r\npassword VARCHAR(100),\r\nexpire_date VARCHAR(100),\r\ndns VARCHAR(100),\r\nepg_url VARCHAR(100),\r\ntitle VARCHAR(100),\r\nurl VARCHAR(100),\r\ntype VARCHAR(100))");
$res = $db->query("SELECT * FROM ibo");
if (isset($_POST["submit"])) {
    $line = $_POST["url"];
    $playlistpassword = "";
    if(isset($_POST["playlistpassword"])){
        $playlistpassword = $_POST["playlistpassword"];
    }
    
    $res = $db->query("SELECT id_user FROM ibo WHERE mac_address = '$mac_address' AND store_type = 2");
    $store_id = $res->fetchArray()['id_user'];


    $dbUsers = new SQLite3("./api/.anspanel.db");
    $res = $dbUsers->query("SELECT mac_amount FROM USERS WHERE id = '$store_id' ");
    $macCount = $res->fetchArray()['mac_amount'];
    $dbUsers->close();

    $res = $db->query("SELECT COUNT(*) as count FROM ibo WHERE id_user = '$store_id' AND active = 1 AND expire_date > date('now')");
    $count = $res->fetchArray()['count'];

    if ($count >= $macCount) {
        $limited = true;
    }
    if(!$limited){
        $db->exec("INSERT INTO ibo(mac_address, expire_date, epg_url, title, url, type, id_user, store_type, playlistpassword) VALUES('$mac_address', '$expire', '" . $_POST["epg_url"] . "', '" . $_POST["title"] . "', '$line', '$type', '$store_id', 2, '$playlistpassword')");
        
        if (!isset($_SESSION['macs'])){
            $_SESSION['macs'] = [];    
        }
        
        $macRes = $db->query("SELECT * FROM ibo WHERE mac_address = '$address1'");
        while($row = $macRes->fetchArray()){
            if(!sessionContains($row)){
                array_push($_SESSION['macs'], $row);   
            }
        }
        
        header("Location: playlists_mac.php");
    }

    $db->close();
}

function sessionContains($searchRow){
    foreach ($_SESSION['macs'] as $session_row){
        if($session_row['id'] == $searchRow['id']){
            return true;
        }
    }
    
    return false;
}

include "includes/header_playlist.php";

if($limited){
    echo "<div class='alert alert-danger'>Limite de MACs excedido!</div>";
}
echo "        <div class=\"container-fluid\">\n\n          <!-- Page Heading -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Activate Playlist</h1>\n\n              <!-- Custom codes -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-user\"></i> User Details</h6>\n                </div>\n                <div class=\"card-body\">\n                        <form method=\"post\">          \n                        <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"mac_address\">\n                                        <strong>Device MAC</strong> \n                                    </label>\n                                    <div class=\"input-group\">\n                                        
    
    <div class='form-control'>$mac_address</div>
    
    <input type=\"hidden\" name=\"type\" value=\"$type\"/>
    
</div>\n                                </div>\n";

echo "<div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"title\">\n                                        <strong>Server Name</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
    echo "                                        <input type=\"text\" class=\"form-control text-primary\" name=\"title\" placeholder=\"Enter Server Name\" required/>" . "\n";
    echo "</div>\n                                </div>\n \r\n

    <div class=\"active2\">\n                                 <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"dns\">\n                                        <strong>List M3U</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"url\" placeholder=\"Enter Url List M3u\" id=\"discription\" />\n                                    </div>\n                                </div>\n                       </div>\n                                 <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"epg_url\">\n                                        <strong>EPG Url</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"epg_url\" placeholder=\"Enter EPG Guide\" id=\"epg_url\"/>\n                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <div class=\"input-group\">\n                                        <input type=\"hidden\" class=\"form-control text-primary\" name=\"expire_date\" value='$expire' placeholder=\"YYYY-MM-DD\" id=\"datetimepicker\" autocomplete=\"off\"/> \n                                    </div>\n\n                                </div>\n                                <div class=\"form-group\">\n                                    <div>";

        echo "<div class=\"form-group \">
                    <label class=\"control-label \" for=\"playlistpassword\"><strong>Playlist Password (Optional)</strong></label>
                <div class=\"input-group\">\n                                        <input type=\"text\" class=\"form-control text-primary\" name=\"playlistpassword\" placeholder=\"Enter Playlist Password\" id=\"playlistpassword\"/>
            </div>
        </div>";

        echo "<button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Submit</span>\n                        </button>\n                                    </div>\n\n                                </div>\n                            </form>\n                    </div>\n                </div>\n                </div>\n    <br><br><br>\n";
include "includes/footer.php";
echo "<script type=\"text/javascript\">\r\n// @require http://code.jquery.com/jquery-latest.js\r\n// ==/UserScript==\r\ndocument.getElementById(\"description\").addEventListener('keyup', function() { \r\n  var mac = document.getElementById('description').value;\r\n  var macs = mac.split(':').join('');\r\n  macs = chunk(macs, 2).join(':');\r\n  document.getElementById('description').value = macs.toString();\r\n});\r\n\r\nfunction chunk(str, n) {\r\n    var ret = [];\r\n    var i;\r\n    var len;\r\n\r\n    for(i = 0, len = str.length; i < len; i += n) {\r\n       ret.push(str.substr(i, n))\r\n    }\r\n\r\n    return ret\r\n};\r\n    </script>\n</body>\n\n</html>";

?>