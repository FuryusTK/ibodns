<?php
ini_set("log_errors", 1);
ini_set("error_log", "errorlog.txt");

function decode($encoded) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    
    $length = strlen($encoded);
    
    $pos1 = strpos($chars, $encoded[$length - 2]);
    $pos2 = strpos($chars, $encoded[$length - 1]);
    
    $decoded = substr($encoded, 0, $length - 2);
    
    $decoded = substr($decoded, 0, $pos1) . substr($decoded, $pos1 + $pos2);
    return base64_decode($decoded);
}

function formatMac($mac) {
    // Remove qualquer caractere não alfanumérico
    $mac = preg_replace('/[^A-Za-z0-9]/', '', $mac);
    
    // Se o mac for menor que 12 caracteres, adicionar padding
    if (strlen($mac) < 12) {
        $mac = str_pad($mac, 12, '0');
    } elseif (strlen($mac) > 12) {
        // Se o mac for maior que 12 caracteres, truncar
        $mac = substr($mac, 0, 12);
    }

    // Formatar o MAC com dois pontos
    $mac = preg_replace('~..(?!$)~', '\0:', $mac);

    return strtoupper($mac);
}

function trial($mac) {
    global $db2, $res;
    
    $res = $db2->query('SELECT * FROM ibo WHERE mac_address="'.$mac .'"');
    $count = 0;
    while ($row = $res->fetchArray()) {
        $count++;
    }
    
    if ($count == 0) {
        $json = '{"receiveMessageAppId":"com.whatsapp","receiveMessagePattern":["*"],"senderName":"API DE CADASTRO","groupName":"","senderMesage":"api_cadastro","senderMessage":"api_cadastro","messageDateTime":' . time() . ',"isMessageFromGroup":false}';

        $url_server = file_get_contents("../app_url");
      
        $dns = file_get_contents("../app_dns");

        file_put_contents(__DIR__ . '/_debug_app_url.json', $url_server);
        file_put_contents(__DIR__ . '/_debug_dns.json', $dns);

        $ch = curl_init($url_server);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ]
        );

        $jsonRetorno = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            file_put_contents(__DIR__ . '/_debug_response_error.json', json_encode($error_msg, JSON_PRETTY_PRINT));
        }

        file_put_contents(__DIR__ . '/_debug_response.json', json_encode($jsonRetorno, JSON_PRETTY_PRINT));

        
        $username = null;
        $password = null;

        if (isset($jsonRetorno['username']) && isset($jsonRetorno['password'])) {
            $username = $jsonRetorno['username'];
            $password = $jsonRetorno['password'];
        }
        
        if (empty($username) && empty($password) && isset($jsonRetorno['data'][0]['message'])) {
            $user_pass = explode("|", $jsonRetorno['data'][0]['message']);

            if (isset($user_pass[0]) && isset($user_pass[1])) {
                $username = $user_pass[0];
                $password = $user_pass[1];
            }
        }
        
        if (isset($jsonRetorno['payUrl'])) {
            $payUrl = $jsonRetorno['payUrl'];
        }
        
        if (!empty($username) && !empty($password)) {
            
            //$expire_date2 = date('Y-m-d', strtotime("+365 DAY"));
            $expire_date2 = date('Y-m-d', strtotime("+365 DAY"));
            
            $url = $dns . "/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus&output=ts";
            
            $db2->exec("INSERT INTO ibo (mac_address, username, password, expire_date, title, url, type) VALUES ('$mac', '$username', '$password', '$expire_date2', 'Teste Automatico', '$url', 1)");
 
 
            
            $db3 = new SQLite3('./.payment_links.db');
            $db3->exec("CREATE TABLE IF NOT EXISTS payment_links(mac_address VARCHAR(100), payment_link VARCHAR(250))");
            
            $mac_escaped = SQLite3::escapeString($mac);

            $stmt = $db3->prepare("DELETE FROM payment_links WHERE mac_address = :mac_address");
            $stmt->bindValue(':mac_address', $mac_escaped, SQLITE3_TEXT);
            $stmt->execute();

            $stmt = $db3->prepare("INSERT INTO payment_links (mac_address, payment_link) VALUES (:mac_address, :payment_link)");
            $stmt->bindValue(':mac_address', $mac_escaped, SQLITE3_TEXT);
            $stmt->bindValue(':payment_link', $payUrl, SQLITE3_TEXT);
            $stmt->execute();

            sleep(30); // Adicionando a pausa de 30 segundos aqui
        } else {
            $db2->exec("INSERT INTO ibo (mac_address, title, type) VALUES ('$mac', 'NOVO*** (SEM DNS, USUARIO E SENHA)', 1)");
            sleep(30); // Adicionando a pausa de 30 segundos aqui
        }
        
    }
}

function defaultHandle($mac) {
    global $db2, $res;
    if(!isset($mac)) return;
    
    $res = $db2->query('SELECT * FROM ibo WHERE mac_address="'.$mac .'"');
    $count = 0;
    while ($row = $res->fetchArray()) {
        $count++;
    }
    
    if ($count == 0) {
        $expire_date = date('Y-m-d', strtotime("+365 DAYS")); // Calcula a data atual mais 365 dias
        $db2->exec("INSERT INTO ibo (mac_address, title, expire_date, type) VALUES ('$mac', 'Ative no WhatsApp', '$expire_date', 1)");
    }
}

$data = json_decode(file_get_contents('php://input'), true);

if($data){
    $data = $data['data'];
    $data = json_decode(decode($data), true);
    $mac = base64_decode($data['app_device_id']);
    $mac = formatMac($mac);
}

$db1 = new SQLite3('./.ansdb.db');
$res1 = $db1->query('SELECT * FROM theme');
while ($row1 = $res1->fetchArray()) {
    $themes[] = ['name'=>$row1['name'],'url'=>$row1['url']];
}

$themes = json_encode($themes);
$ibo_json = file_get_contents('./ibo.json');
$ibo_data = json_decode($ibo_json,true);

$app_info = $ibo_data['app_info'];
$android_version_code = $app_info['android_version_code'];
$apk_url = $app_info['apk_url'];
$db2 = new SQLite3('./.ansdb.db');
$languages = file_get_contents('./language.json');
$notification = file_get_contents('./note.json');

if (isset($mac)) {
    $mode = file_get_contents('../trial_mode');
    if ($mode == 'trial'){
        trial($mac);
    } else {
        defaultHandle($mac);
    }
    
    while ($row = $res->fetchArray()) {
        $expire_date = $row['expire_date'];
    }
    if (empty($expire_date)) {
        $api = file_get_contents('./nr.json');
        if (isset($paths[0])) {
            $mac = strtoupper($paths[0]);
        } else {
            $mac = 'No Mac when page loaded';
        }
        $date = date('d-m-Y H:i:s');
        $db2 = new SQLite3('./catch.db');
        $db2->exec('CREATE TABLE IF NOT EXISTS catch(id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,device TEXT,date TEXT)');
        $db2->exec('INSERT INTO catch(device, date) VALUES(\''.$mac .'\', \''.$date .'\')');
    } else {
        $db3 = new SQLite3('./.ansdb.db');
        $res3 = $db3->query('SELECT * FROM ibo WHERE mac_address="'.$mac .'"');
        while ($row3 = $res3->fetchArray()) {
            $url = $row3['url'];
            $username = $row3['username'];
            $password = $row3['password'];
    
            if ($url) {
                $url .= "/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus&output=ts";
            }
            
            $urls[] = ['is_protected'=>1,'id'=>md5($password .$row3['id']),'url'=>$url,'name'=>$row3['title'],'username'=>$username ,'password'=>$row3['password'],'epg_url'=>$row3['url'] .'/xmltv.php','pin'=>'0000','playlist_type'=>'xc'];
        }
        $urls = json_encode($urls);
        
        $api = '{
            "android_version_code": "2.9",
            "apk_url": "'.$apk_url .'",
            "mac_address" : "'. $mac . '",
            "device_key": "136115",
            "expire_date": "'.$expire_date .'",
            "is_google_paid": true,
            "is_trial": 0,
            "notification": ' . $notification . ',
            "urls": '.$urls .',
            "mac_registered": true,
            "trial_days": 7,
            "plan_id": "03370629",
            "pin": "0000",
            "price": "0",
            "app_version": "2.9",
            "apk_link": "",
            "themes":'.$themes .',
            "languages":'.$languages .'
        }
        ';
    }
} else {
    $api = 'invalid';
}

$api = base64_encode($api) . "aa";
echo "{\"data\": \"$api\"}";

return;
?>
