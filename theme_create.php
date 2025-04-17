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
if (isset($_POST["submit"])) {
    $db->exec("INSERT INTO theme(name, url) VALUES('" . $_POST["name"] . "','" . $_POST["url"] . "')");
    header("Location: theme.php");
}
include "includes/header.php";
echo "        <div class=\"container-fluid\">\n\n          <!-- Page Heading -->\n          <h1 class=\"h3 mb-1 text-gray-800\"> Update Theme</h1>\n\n              <!-- Custom codes -->\n                <div class=\"card border-left-primary shadow h-100 card shadow mb-4\">\n                <div class=\"card-header py-3\">\n                <h6 class=\"m-0 font-weight-bold text-primary\"><i class=\"fas fa-paint-brush\"></i> Add Theme</h6>\n                </div>\n                <div class=\"card-body\">\n                        <form method=\"post\">          \n                        <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"name\">\n                                        <strong>Name</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input class=\"form-control text-primary\" id=\"description\" name=\"name\" placeholder=\"Theme Name\" type=\"text\" required/>\n                                    </div>\n                                </div>\n                                <div class=\"form-group \">\n                                    <label class=\"control-label \" for=\"url\">\n                                        <strong>Image Url</strong>\n                                    </label>\n                                    <div class=\"input-group\">\n                                        <input class=\"form-control text-primary\" id=\"description\" name=\"url\" placeholder=\"Enter Url\" type=\"text\" required/>\n                                    </div>\n                                </div>\n                                <div class=\"form-group\">\n                                    <div>\n                                        <button class=\"btn btn-success btn-icon-split\" name=\"submit\" type=\"submit\">\n                        <span class=\"icon text-white-50\"><i class=\"fas fa-check\"></i></span><span class=\"text\">Submit</span>\n                        </button>\n                                    </div>\n\n                            </form>\n                            \n                                </div>\n                        </div>\n                    </div>\n                </div>\n\n    <br><br><br>\n";
include "includes/footer.php";
echo "    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n    </script>\n</body>\n\n</html>";

?>