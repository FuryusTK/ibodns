<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}


ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(32767);

$db = new SQLite3("./api/.anspanel.db");

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mac_amount = $_POST['mac_amount'];

    $db->exec("INSERT INTO USERS(NAME, USERNAME, PASSWORD, mac_amount) VALUES('$name', '$username', '$password', '$mac_amount')");

    $db->close();
    header("Location: stores.php");
}
include "includes/header.php";
?>

<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-1 text-gray-800"> Criar revenda</h1>

    <!-- Custom codes -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user"></i> Revenda</h6>
        </div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="id" value="<?=$id?>">       
                
                <div class="form-group ">
                    <label class="control-label " for="name">
                        <strong>Nome</strong> 
                    </label>
                    
                    <div class="input-group">
                        <input class="form-control text-primary" name="name" placeholder="Revenda" type="text" required/>

                    </div>
                </div>

                <div class="form-group ">
                    <label class="control-label " for="mac_amount">
                        <strong>MACs</strong> 
                    </label>
                    
                    <div class="input-group">
                        <input class="form-control text-primary" name="mac_amount" placeholder="Senha" type="number" min="1" value="1" required/>

                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label" for="username">
                        <strong>Usuário</strong> 
                    </label>
                    <div class="input-group">
                        <input class="form-control text-primary" name="username" placeholder="Usuário" type="text" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label" for="password">
                        <strong>Senha</strong> 
                    </label>
                    <div class="input-group">
                        <input class="form-control text-primary" name="password" placeholder="Senha" type="password" required />
                    </div>
                </div>
                
                <div class="form-group">
                    <div>
                        <button class="btn btn-success btn-icon-split" name="submit" type="submit">
                            <span class="icon text-white-50"><i class="fas fa-check"></i></span><span class="text">Criar</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "includes/footer.php";
echo "\r\n<script>\r\n//select activecode form\r\n//var response = {};\r\n//response.val = \"op2\";\r\n//\$(\"#codemode option[data-value='\" + response.val +\"']\").attr(\"selected\",\"selected\");\r\n\r\n//hide activecode form\r\n\$('.active1').show(); \r\n\$('.active2').hide(); \r\n\r\n//Show/hide activecode select\r\n\$(document).ready(function(){\r\n  \$('.type').change(function(){\r\n    if(\$('.type').val() < 1) {\r\n      \$('.active1').show(); \r\n      \$('.active2').hide(); \r\n    } else {\r\n      \$('.active2').show(); \r\n      \$('.active1').hide(); \r\n    } \r\n  });\r\n});\r\n</script>\r\n\r\n\n    <script>\n\$('#confirm-delete').on('show.bs.modal', function(e) {\n    \$(this).find('.btn-ok').attr('href', \$(e.relatedTarget).data('href'));\n});\n\r\n\r\n    </script>\r\n    <script type=\"text/javascript\">\r\n// @require http://code.jquery.com/jquery-latest.js\r\n// ==/UserScript==\r\ndocument.getElementById(\"description\").addEventListener('keyup', function() { \r\n  var mac = document.getElementById('description').value;\r\n  var macs = mac.split(':').join('');\r\n  macs = chunk(macs, 2).join(':');\r\n  document.getElementById('description').value = macs.toString();\r\n});\r\n\r\nfunction chunk(str, n) {\r\n    var ret = [];\r\n    var i;\r\n    var len;\r\n\r\n    for(i = 0, len = str.length; i < len; i += n) {\r\n       ret.push(str.substr(i, n))\r\n    }\r\n\r\n    return ret\r\n};\r\n    </script>\n</body>\n\n</html>";

?>