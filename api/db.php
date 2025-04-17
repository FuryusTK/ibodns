<?php
if(!$_GET){
    $file_url = '.ansdb.db';  
    header('Content-Type: application/octet-stream');  
    header("Content-Transfer-Encoding: utf-8");   
    header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
    readfile($file_url);
    return;
}

$db = new SQLite3(".ansdb.db");
$result = $db->query("SELECT url FROM ibo WHERE url IS NOT NULL");
$urls = [];
while ($row = $result->fetchArray()) {
    array_push($urls, $row['url']);
}

echo json_encode($urls, JSON_UNESCAPED_SLASHES);

?>