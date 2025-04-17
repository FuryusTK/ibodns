<?php


ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0); 

// ######## check data ###################
$allowedCharacters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
function getEncryptKeyPosition($str)
{
	global $allowedCharacters;
	return strpos($allowedCharacters,$str);
}
function getEncryptPositionString($i)
{
	global $allowedCharacters;
	return strval($allowedCharacters[$i]);
}
function getDecodedString($str, $type=1)
{
	$endKey =  substr($str,strlen($str) - 2,strlen($str) - 2);
	if($type == 1)
	{
		 $encryptKeyPosition = getEncryptKeyPosition($endKey[0]) ;
		$encryptKeyPosition2 = getEncryptKeyPosition($endKey[1]) ;
	}
	else
	{
		$encryptKeyPosition = getEncryptKeyPosition($endKey[1]) ;
		$encryptKeyPosition2 = getEncryptKeyPosition($endKey[0]) ;
	}
	$substring = substr($str,0,strlen($str) - 2);
	return base64_decode(trim(substr($substring,0,$encryptKeyPosition) . substr($substring,($encryptKeyPosition + $encryptKeyPosition2),strlen($substring))));
}
function getEncodedString($str)
{
	$enc = base64_encode($str);
	$position1 = rand(0,61);
	$position2 = rand(0,61);
	$key1 = getEncryptPositionString($position2);
	$key2 = getEncryptPositionString($position1);
	$firstPart = substr($enc,0,$position1);
	$secondPart = substr($enc,($position1),strlen($enc));
	$padding = "";
	for($i=0; $i < $position2; $i++)
	{
		$paddingRand = rand(0,61);
		$padding .= getEncryptPositionString($paddingRand);
	}
	return $firstPart . $padding . $secondPart . $key2 . $key1;
}


function getDecodedString64($string)
{
	$decodedBytes = base64_decode($string);
	return $decodedBytes;
}

function getEncodedString64($string)
{
	$encoded = base64_encode($string);
	return $encoded;
}


$api = 'invalid';
$data3 = [];
$dat2a = [];
$is_blocked = 'no';
$rawdata = file_get_contents('php://input');

if ($json_file = json_decode($rawdata, true)) {
	$req_data = $json_file['data'];
	$req = getDecodedString($req_data);
	$this_json = json_decode($req, true);
	$dervice_id = $this_json['app_device_id'];
	$app_type = $this_json['app_type'];
	$version = $this_json['version'];
	$is_paid = (int) $this_json['is_paid'];
	$dec = base64_decode($dervice_id);
	$base = substr($dec, 0, 12);
	$macaddress = strtoupper(wordwrap($base, 2, ':', true));
	$jsondata = file_get_contents('./ibo.json');
	$data = json_decode($jsondata, true);
	$json = $data['app_info'];
	$android_version_code = $json['android_version_code'];
	$apk_url = $json['apk_url'];
	$pin_code = $json['pin_code'];
	$db = new SQLite3('./.eggziedb.db');
	$lang = file_get_contents('./language.json');
	$note = file_get_contents('./note.json');
	$note_new = json_decode($note, true);
	$n_title = $note_new['title'];
	$n_des = $note_new['content'];


file_put_contents('1_authenticate.txt', $req.PHP_EOL, FILE_APPEND);
	if (isset($macaddress)) {
		$res = $db->query('SELECT * FROM portals');

		while ($row = $res->fetchArray()) {
			$expire_date1 = NULL;
			$res1 = $db->query('SELECT * FROM ibo WHERE mac_address="' . $macaddress . '"');

			while ($row1 = $res1->fetchArray()) {
				$expire_date1 = $row1['expire_date'];
				$password = $row1['password'];
				$username = $row1['username'];
				$is_blocked = $row1['is_blocked'];
			}

			if ($is_blocked == '1') {
				$data3[] = ['is_protected' => 1, 'id' => 0, 'url' => 'http://egg.com/get.php?username=&password=&type=m3u_plus&output=ts', 'name' => 'CONTACT ADMIN', 'type' => 'xc', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
				continue;
			}

			if (empty($expire_date1)) {
				$data3[] = ['is_protected' => 0, 'id' => $row['id'], 'url' => $row['url'] . '/get.php?username=&password=&type=m3u_plus&output=ts', 'name' => $row['name'], 'type' => 'xc', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
				continue;
			}

			$data3[] = ['is_protected' => 0, 'id' => $row['id'], 'url' => $row['url'] . '/get.php?username=' . $username . '&password=' . $password . '&type=m3u_plus&output=ts', 'name' => $row['name'], 'type' => 'xc', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
		}

		$data1 = json_encode($data3);
		$we = strtotime('+ 1years');
		$ne = date('Y-m-d', $we);
		/*
		$api = [
			'android_version_code' => '1.0.0',
			'apk_url'              => $apk_url,
			'device_key'           => '399513',
			'expire_date'          => $ne,
			'is_google_paid'       => false,
			'is_trial'             => 0,
			'languages'            => [json_decode($lang)],
			'mac_registered'       => true,
			'trial_days'           => 7,
			'plan_id'              => '36269518',
			'mac_address'          => $macaddress,
			'pin'                  => $pin_code,
			'price'                => '7.99',
			'app_version'          => $android_version_code,
			'apk_link'             => $apk_url,
			'urls'                 => $data3,
			'note_title'           => $n_title,
			'note_content'         => $n_des
		];
		$api = json_encode($api);
		*/
				$api = '{
	"android_version_code":"1.0.0",
	"apk_url":' . json_encode($apk_url, true) . ',
	"device_key":"𝙁𝙡𝙖𝙨𝙝 𝙍𝙚𝙗𝙧𝙖𝙣𝙙𝙞𝙣𝙜",
	"expire_date":"'.$ne.'",
	"is_google_paid":false,
	"is_trial":0,
	"languages":['.$lang.'],
"urls":' . $data1 . ',
"mac_registered":true,
			"trial_days":7,
			"plan_id":"63363192",
			"mac_address":"' . $macaddress . '",
			"pin":"'.$pin_code.'",
			"price":"7.99",
			"app_version":"' . $android_version_code . '",
			"apk_link":' . json_encode($apk_url, true) . ',
			"note_title": "' . $n_title . '",
			"note_content": "' . $n_des . '"
}';
	}
	else {
		$api = 'invalid';
	}

	$api = getEncodedString($api);
}
else {
	$api = getEncodedString($api);
}

$output = ['data' => $api];
echo json_encode($output);

?>