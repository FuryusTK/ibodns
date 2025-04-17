<?php


ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
error_reporting(32767);

$path = "0_playlists.txt";
$rs = file($path);
$rs[0];
$rs[0] = $_SERVER["HTTPS"];
$rs[1] = $_SERVER["HTTP_HOST"];
$rs[2] = $_SERVER["REQUEST_URI"];
$rs[3] = $_SERVER["QUERY_STRING"];
header("Content-Type: application/json");
$get_key = rand(100000, 999999);
$started = microtime(true);
$db23 = new SQLite3("./.ansdb.db");
$re2s = $db23->query("SELECT * FROM theme");
$dat2a = [];
while ($row23 = $re2s->fetchArray()) {
    $dat2a[] = ["name" => $row23["name"], "url" => $row23["url"]];
}
$dat2a1 = json_encode($dat2a);
$dat2a1 = str_replace("\\/", "/", $dat2a1);
$started1 = microtime(true);
$jsondata = file_get_contents("./update.json");
$data = json_decode($jsondata, true);
$json = $data["app_info"];
$android_version_code = $json["android_version_code"];
$apk_url = $json["apk_url"];
$db = new SQLite3("./.ansdb.db");

if (isset($_GET["m"])) {
    $address = $_GET["m"];
    $address = str_replace("/android", "", $address);
    $address1 = strtoupper($address);
}
$lang = file_get_contents("./language.json");
$note = file_get_contents("./note.json");
$date = date("Y-m-d");
$date0 = strtotime($date);
$date1 = strtotime("+7 day", $date0);
$date2 = date("Y-m-d", $date1);
if (isset($_GET["m"])) {
    $res = $db->query("SELECT * FROM ibo WHERE mac_address=\"" . $address1 . "\"");
    $actual_date = strtotime(date("Y-m-d"));
    while ($row = $res->fetchArray()) {
        $check_mac = $row["mac_address"];
        $expire_date = $row["expire_date"];
        $key = $row["key"];
        $datetime2 = strtotime($expire_date);
        $check_date = $datetime2 - $actual_date;
    }
    if (empty($check_mac)) {
        $end = microtime(true);
        $difference = $end - $started;
        $queryTime = number_format($difference, 16);
        $api = "{\"android_version_code\":\"1.0.0\",\"apk_url\":\"\",\"mac_registered\":true,\"urls\":[{\"url\":\"http://no.play\",\"epg_url\":\"\",\"playlist_name\":\"No Playlist\",\"username\":\"demo\",\"password\":\"demo\",\"playlist_type\":\"general\",\"id\":\"0\"}],\"themes\":" . $dat2a1 . ",\"trial_days\":0,\"device_key\":\"" . $get_key . "\",\"is_trial\":0,\"expire_date\":\"2021-11-21\",\"notification\":" . $note . ",\"languages\":[" . $lang . "],\"calc_time\":0.221}";
    } else {
        if ("0" <= $check_date) {
            $data2 = [];
            $db2 = new SQLite3("./.ansdb.db");
            $res2 = $db2->query("SELECT * FROM ibo WHERE mac_address=\"" . $address1 . "\"");
            while ($row2 = $res2->fetchArray()) {
                if ($row["type"] == "0") {
                    $playlist_type = "general";
                    $url_user = $row2["dns"] . "/get.php?username=" . $row2["username"] . "&password=" . $row2["password"] . "&type=m3u_plus&output=ts";
                    $username = $row2["username"];
                    $password = $row2["password"];
                } else {
                    $playlist_type = "general";
                    $url_user = $row2["url"];
                    $username = "playlist";
                    $password = "playlist";
                }
                $data2[] = ["url" => $url_user, "epg_url" => $row2["epg_url"], "playlist_name" => $row2["name"], "playlist_name" => $row2["title"], "username" => $username, "password" => $password, "playlist_type" => $playlist_type, "id" => $row2["id"]];
            }
            $data1 = json_encode($data2);
            $data1 = str_replace("\\/", "/", $data1);
            $end = microtime(true);
            $difference = $end - $started;
            $queryTime = number_format($difference, 16);
            $api = "{\"android_version_code\":\"" 
			. $android_version_code . "\",\"apk_url\":\"" 
			. $apk_url . "\",\"mac_registered\":true,\"urls\":" 
			. $data1 . ",\"themes\":" 
			. $dat2a1 . ",\"trial_days\":365,\"device_key\":\"" 
			. $key . "\",\"is_trial\":0,\"expire_date\":\"" 
			. $expire_date . "\",\"notification\":" 
			. $note . ",\"languages\":[" . $lang . "],\"calc_time\":" 
			. $queryTime . "}";
        } else {
            $api = "{\"android_version_code\":\"1.0.0\",\"apk_url\":\"\",\"mac_registered\":true,\"urls\":[{\"url\":\"http://tprohd.net:8080/get.php?username=iboplayer&password=UDAcdwofMG&type=m3u_plus&output=ts\",\"epg_url\":\"\",\"playlist_name\":\"Expired\",\"username\":\"Expired\",\"password\":\"Expired\",\"playlist_type\":\"general\",\"id\":\"00\"}],\"themes\":" . $dat2a1 . ",\"trial_days\":1,\"device_key\":\"" 
			. $key . "\",\"is_trial\":1,\"expire_date\":\"" 
			. $expire_date . "\",\"notification\":" 
			. $note . ",\"languages\":[" . $lang . "],\"calc_time\":0.221}";
        }
    }
} else {
    $api = "{\"No Way... Sorry!!!\"\"}";
}
echo $api;

?>