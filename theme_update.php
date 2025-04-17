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
$res = $db->query("SELECT * FROM theme WHERE id='" . $_GET["update"] . "'");
$row = $res->fetchArray();
$NName = $row["name"];
$UUrl = $row["url"];
$IIdd = $row["id"];
if (isset($_POST["submit"])) {
    $db->exec("UPDATE theme SET name='" . $_POST["name"] . "'," . "\r\n\t\t\t\t\t\t\t" . "  url='" . $_POST["url"] . "'" . "\r\n\t\t\t\t\t\t" . "  WHERE " . "\r\n\t\t\t\t\t\t\t" . "  id='" . $_POST["id"] . "'");
    header("Location: theme.php");
}
include "includes/header.php";
echo "        <div class=\"container-fluid\">\n\n          <!-- Page Heading -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Themes</h1>\n\n              <!-- Custom codes -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fa fa-picture-o\"></i> Edit theme</h6>\n                </div>\n                <div class=\"card-body\">\n                        <form method=\"post\">\n                        <div class=\"form control\">\n                                    <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"name\"><strong>Name</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n";
echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"id\"  value=\"" . $IIdd . "\" type=\"hidden\"/>" . "\n";
echo "                                        <input class=\"form-control text-primary\" id=\"description\" name=\"name\"  value=\"" . $NName . "\" type=\"text\"/>" . "\n";
echo "                                    </div>\n                                    </div>\n                                    <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"url\"><strong>Image Url</strong>\n                                    </label>\n";
echo "                                        <input class=\"form-control text-primary\" id=\"url\" name=\"url\" value=\"" . $UUrl . "\" type=\"text\"/>" . "\n";
echo "                                    </div>\n                                <div class=\"form-group\">\n                                        <button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Submit</span>\n                        </button>\n                                    </div>\n\n                                </div>\n                            </form>\n                </div>\n              </div>\n            </div>\n";
include "includes/footer.php";
echo "</body>\n\n</html>";

?>