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
$db->exec("CREATE TABLE IF NOT EXISTS theme(id INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL, name VARCHAR(100), url VARCHAR(100))");
$res = $db->query("SELECT * FROM theme");
$rows = $db->query("SELECT COUNT(*) as count FROM theme");
$row = $rows->fetchArray();
$numRows = $row["count"];
$HOSTa = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/red.jpg";
$HOSTb = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/blue.jpg";
$HOSTc = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/green.jpg";
$HOSTa1 = $lurl = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off" ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/img/g1.gif";
if ($numRows == 0) {
    $db->exec("INSERT INTO theme(name,url) VALUES('Red','" . $HOSTa . "')\r\n\t,('Blue','" . $HOSTb . "')\r\n\t,('Green','" . $HOSTc . "')\r\n\t,('Gif Edition','" . $HOSTa1 . "')\r\n\t");
}
if (isset($_GET["delete"])) {
    $db->exec("DELETE FROM theme WHERE id=" . $_GET["delete"]);
    header("Location: theme.php");
}
include "includes/header.php";
echo "<div class=\"modal fade\" id=\"confirm-delete\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">\n    <div class=\"modal-dialog\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <h2>Confirm</h2>\n            </div>\n            <div class=\"modal-body\">\n                Do you really want to delete?\n            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>\n                <a class=\"btn btn-danger btn-ok\">Delete</a>\n            </div>\n        </div>\n    </div>\n</div>\n<main role=\"main\" class=\"col-15 pt-4 px-5\"><div class=\"row justify-text-center\"><div class=\"chartjs-size-monitor\" style=\"position:absolute ; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;\"><div class=\"chartjs-size-monitor-expand\" style=\"position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;\"><div style=\"position:absolute;width:1000000px;height:1000000px;left:0;top:0\"></div></div><div class=\"chartjs-size-monitor-shrink\" style=\"position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;\"><div style=\"position:absolute;width:200%;height:200%;left:0; top:0\"></div></div></div>\n          <div id=\"main\">\n            \n\n          <!-- Page Heading -->\n          <h1 class=\" h3 mb-1 text-gray-800\"> Themes</h1>\n                        <a button class=\"btn btn-success btn-icon-split\" id=\"button\" href=\"./theme_create.php\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Create</span>\n                        </button></a>\n          </div>\n\t\t<div class=\"table-responsive\">\n\t\t\t<table class=\"table table-striped table-sm\">\n\t\t\t<thead class= \"text-primary\">\n\t\t\t\t<tr>\n\t\t\t\t<th>Name</th>\n\t\t\t\t<th>Image Url</th>\n\t\t\t\t<th>Image Preview</th>\n\t\t\t\t<th>Edit</th>\n\t\t\t\t<th>Delete</th>\n\t\t\t\t</tr>\n\t\t\t</thead>\n";
while ($row = $res->fetchArray()) {
    $NName = $row["name"];
    $UUrl = $row["url"];
    $IIdd = $row["id"];
    echo "\t\t\t<tbody>\n\t\t\t\t<tr>\n";
    echo "\t\t\t\t<td>" . $NName . "</td>" . "\n";
    echo "\t\t\t\t<td>" . $UUrl . "</td>" . "\n";
    echo "\t\t\t\t<td><img src=\"" . $UUrl . "\" alt=\"\" border=3 height=80 width=120></img></td>" . "\n";
    echo "\t\t\t\t<td><a class=\"btn btn-icon\" href=\"./theme_update.php?update=" . $IIdd . "\"><span class=\"icon text-white-50\"><i class=\"fa fa-pencil\" style=\"font-size:24px;color:blue\"></i></span></a></td>" . "\n";
    echo "                <td><a class=\"btn btn-icon\" href=\"#\" data-href=\"./theme.php?delete=" . $IIdd . "\" data-toggle=\"modal\" data-target=\"#confirm-delete\"><span class=\"icon text-white-50\"><i class=\"fa fa-trash\"  style=\"font-size:24px;color:red\"></i></span></a></td>" . "\n";
    echo "\t\t\t\t</tr>\n\t\t\t</tbody>\n";
}
echo "\t\t\t</table>\n\t\t</div>\n</main>\n\n    <br><br><br>\n";
include "includes/footer.php";
echo "    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n    </script>\n</body>\n\n</html>";

?>