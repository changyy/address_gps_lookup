<?php
$gapi = 'https://maps.googleapis.com/maps/api/geocode/json';

$pwd = dirname(__FILE__);
$default_config = $pwd."/config.php";
if (file_exists($default_config))
	require $default_config;
// $google_api_key = 'Google API Key';
if (!isset($google_api_key)) {
	echo "[ERROR] google_api_key is not defined\n";
	exit;
}

if (!isset($record_dir))
	$record_dir = $pwd.'/record';
if (!isset($query_dir))
	$query_dir = $pwd.'/google_query';

if (!file_exists($record_dir)) {
	echo "[ERROR] record_dir is not defined\n";
	exit;
}

if (!file_exists($query_dir)) {
	echo "[ERROR] query_dir is not defined\n";
	exit;
}

$record_list = array();
if (is_dir($record_dir) && (($dh = opendir($record_dir)))) {
	while (false !== ($filename = readdir($dh))) {
		if ($filename == '.' || $filename == '..')
			continue;
		if (!file_exists($query_dir.'/'.$filename))
			array_push($record_list, $filename);
	}
}
if (!file_exists($query_dir) && !mkdir($query_dir)) {
	echo "ERROR at create dir: [$query_dir]\n";
	exit;
}
foreach($record_list as $log) {
	$in_path = $record_dir.'/'.$log;
	$out_path = $query_dir.'/'.$log;
	$out_url_path = $query_dir.'/'.$log.'-url';

	echo "[INFO] Query: $in_path\n";

	$target = array();
	foreach( explode("\n", file_get_contents($in_path)) as $address) {
		$address = trim($address);
		if (!empty($address))
			array_push($target, $address);
	}

	date_default_timezone_set('asia/taipei');
	foreach( $target as $query_target ) {
		echo date('Y-m-d H:i:s')."\n";sleep(1);
		$hash_check = $query_dir.'/'.md5($query_target);
		if (file_exists($hash_check))
			continue;
		$query_url = $gapi."?".http_build_query(array(
			'key' => $google_api_key,
			'address' => $query_target
		));
		file_put_contents($out_url_path, $query_url."\n", FILE_APPEND);
		$api_result = file_get_contents($query_url);
		file_put_contents($out_path, $api_result, FILE_APPEND);
		$json_obj = @json_decode($api_result);
		if (isset($json_obj->results) && is_array($json_obj->results) && count($json_obj->results) > 0 && isset($json_obj->results[0]->geometry) && isset($json_obj->results[0]->geometry->location) ) {
			file_put_contents($hash_check, json_encode($json_obj->results[0]->geometry->location));
		}
	}
}
