<?php
//db call
$db = new SQLite3("./db/studiolivecode_qrcode.db");

//table name
$table_name = "qrcode";

//current file var
$base_file = basename($_SERVER["SCRIPT_NAME"]);

//create if not
$db->exec("CREATE TABLE IF NOT EXISTS " . $table_name . "(id INTEGER PRIMARY KEY, qrcode TEXT)");

$res = $db->query("SELECT COUNT(*) as count FROM " . $table_name. "");
$row = $res->fetchArray();
$numRows = $row["count"];
if ($numRows == 0) {
    $db->exec("INSERT INTO " . $table_name . "(id, qrcode) VALUES('1', 'https://wa.me/554197211993')");   
}

$res = $db->query("SELECT qrcode FROM " . $table_name . " WHERE id='1'");
$row = $res->fetchArray();
$qrcode = $row["qrcode"];

if (!empty($qrcode)) {
    // Exiba a imagem do QR Code
    $qrcodeUrl = "https://image-charts.com/chart?chs=500x500&cht=qr&chl=" . urlencode($qrcode);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>QR Code Styling</title>
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            #qrcode-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: fill;
            }
        </style>
    </head>
    <body>
        <img id="qrcode-img" src="<?= $qrcodeUrl ?>">
    </body>
    </html>
    <?php
}
?>