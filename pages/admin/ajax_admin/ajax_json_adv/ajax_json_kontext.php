<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

if(!DEFINED("KONTEXT_AJAX")) {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);

}elseif($type_ads!="kontext") {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}


if($option == "PlayPause") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_kontext` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];

		if($status == 0) {
			$status_text = "";
			$message_text = "Для запуска, необходимо пополнить рекламный бюджет!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 1) {
			$mysqli->query("UPDATE `tb_ads_kontext` SET `status`='2' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'kontext\', \'PlayPause\');"></span>';
			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 2) {
			$mysqli->query("UPDATE `tb_ads_kontext` SET `status`='1' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'kontext\', \'PlayPause\');"></span>';
			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 3) {
			$status_text = "";
			$message_text = "Для запуска, необходимо пополнить рекламный бюджет!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}else{
			$status_text = "";
			$message_text = "ERROR: Статус не определен!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$status_text = "";
		$message_text = "ERROR: Рекламная площадка не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Delete") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_kontext` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_kontext` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$message_text = "OK";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Рекламная площадка не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}else{
	$message_text = "ERROR: NO OPTION!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

?>