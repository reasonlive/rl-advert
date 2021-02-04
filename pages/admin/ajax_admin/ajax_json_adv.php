<?php
session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
if(!DEFINED("CABINET")) DEFINE("CABINET", true);
sleep(0);

$json_result = array();
$json_result["result"] = "";
$json_result["status"] = "";
$json_result["message"] = "";
$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	require(ROOT_DIR."/config.php");
	require(ROOT_DIR."/funciones.php");
	require(ROOT_DIR."/cabinet/cab_func.php");
	require(ROOT_DIR."/merchant/func_mysql.php");

	function myErrorHandler($errno, $errstr, $errfile, $errline, $js_result) {
		$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";
		$message_text = false;
		$errfile = str_replace($_SERVER["DOCUMENT_ROOT"], "", $errfile);

		switch ($errno) {
			case(1): $message_text = "Fatal error[$errno]: $errstr in line $errline in $errfile"; break;
			case(2): $message_text = "Warning[$errno]: $errstr in line $errline in $errfile"; break;
			case(8): $message_text = "Notice[$errno]: $errstr in line $errline in $errfile"; break;
			default: $message_text = "[$errno] $errstr in line $errline in $errfile"; break;
		}
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}
	$set_error_handler = set_error_handler('myErrorHandler', E_ALL);

	$geo_cod_arr = array(
		 1 => 'RU',  2 => 'UA',  3 => 'BY',  4 => 'MD',  5 => 'KZ',  6 => 'AM',  7 => 'UZ',  8 => 'LV',  9 => 'DE', 10 => 'GE', 
		11 => 'LT', 12 => 'FR', 13 => 'AZ', 14 => 'US', 15 => 'VN', 16 => 'PT', 17 => 'GB', 18 => 'BE', 19 => 'ES', 20 => 'CN', 
		21 => 'TJ', 22 => 'EE', 23 => 'IT', 24 => 'KG', 25 => 'IL', 26 => 'CA', 27 => 'TM', 28 => 'BG', 29 => 'IR', 30 => 'GR', 
		31 => 'TR', 32 => 'PL', 33 => 'FI', 34 => 'EG', 35 => 'SE', 36 => 'RO'
	);

	$geo_name_arr_ru = array(
		'RU' => 'Россия', 	'UA' => 'Украина', 	'BY' => 'Белоруссия', 	'MD' => 'Молдавия', 	'KZ' => 'Казахстан', 	'AM' => 'Армения', 
		'UZ' => 'Узбекистан',	'LV' => 'Латвия',	'DE' => 'Германия', 	'GE' => 'Грузия', 	'LT' => 'Литва', 	'FR' => 'Франция', 
		'AZ' => 'Азербайджан', 	'US' => 'США', 		'VN' => 'Вьетнам', 	'PT' => 'Португалия', 	'GB' => 'Англия', 	'BE' => 'Бельгия', 
		'ES' => 'Испания', 	'CN' => 'Китай',	'TJ' => 'Таджикистан',  'EE' => 'Эстония', 	'IT' => 'Италия', 	'KG' => 'Киргизия',
		'IL' => 'Израиль', 	'CA' => 'Канада', 	'TM' => 'Туркменистан', 'BG' => 'Болгария',	'IR' => 'Иран', 	'GR' => 'Греция', 
		'TR' => 'Турция', 	'PL' => 'Польша',	'FI' => 'Финляндия', 	'EG' => 'Египет', 	'SE' => 'Швеция', 	'RO' => 'Румыния'
	);

	$auth_user = auth_log_pass(2);

	if($auth_user["status"] == "FALSE") {
		$message_text = "ERROR: Необходимо авторизоваться!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($auth_user["status"] == "TRUE") {
		$user_id = $auth_user["user_id"];
		$username = $auth_user["user_log"];
		$money_user_rb = $auth_user["user_money_rb"];
		$money_user_rek = $auth_user["user_money_rek"];
		$my_referer_1 = $auth_user["user_referer_1"];
		$my_referer_2 = $auth_user["user_referer_2"];
		$my_referer_3 = $auth_user["user_referer_3"];
		$wmid_user = $auth_user["user_wmid"];
		$wmr_user = $auth_user["user_wm_purse"];

		$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(limpiarez($_POST["id"])))) ? intval(limpiarez($_POST["id"])) : false;
		$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", limpiarez($_POST["op"])) ) ? limpiarez($_POST["op"]) : false;
		$type_ads = ( isset($_POST["type"]) && preg_match("|^[a-zA-Z0-9\-_-]{1,20}$|", limpiarez($_POST["type"])) ) ? limpiarez($_POST["type"]) : false;
		$laip = getRealIP();

		if($type_ads=="tests") {
			if(!DEFINED("TESTS_AJAX")) DEFINE("TESTS_AJAX", true);
			include("ajax_json_adv/ajax_json_tests.php");
			exit();

		}elseif($type_ads=="surfing") {
			if(!DEFINED("SURFING_AJAX")) DEFINE("SURFING_AJAX", true);
			include("ajax_json_adv/ajax_json_surfing.php");
			exit();

		}elseif($type_ads=="kontext") {
			if(!DEFINED("KONTEXT_AJAX")) DEFINE("KONTEXT_AJAX", true);
			include("ajax_json_adv/ajax_json_kontext.php");
			exit();
			
		}elseif($type_ads=="youtube") {
			if(!DEFINED("YOUTUBE_AJAX")) DEFINE("YOUTUBE_AJAX", true);
			include("ajax_json_adv/ajax_json_youtube.php");
			exit();
			
		}elseif($type_ads=="task") {
			if(!DEFINED("TASK_AJAX")) DEFINE("TASK_AJAX", true);
			include("ajax_json_adv/ajax_json_task.php");
			exit();
			
		}elseif($type_ads=="task_us") {
			if(!DEFINED("TASK_US_AJAX")) DEFINE("TASK_US_AJAX", true);
			include("ajax_json_adv/ajax_json_task_us.php");
			exit();
			
		}elseif($type_ads=="mails") {
			if(!DEFINED("MAILS_AJAX")) DEFINE("MAILS_AJAX", true);
			include("ajax_json_adv/ajax_json_mails.php");
			exit();

		}else{
			$message_text = "ERROR: Тип рекламы не определен!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Необходимо авторизоваться!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}
}else{
	$message_text = "ERROR: Не корректный запрос!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

?>