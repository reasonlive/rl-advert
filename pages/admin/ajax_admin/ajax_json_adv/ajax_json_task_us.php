<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

if(!DEFINED("TASK_US_AJAX")) {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);

}elseif($type_ads!="task_us") {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}


if($option == "ViewClaims") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task_pay` WHERE `user_id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];

		$message_text = "";
		$message_text.= '<div class="box-modal" id="ModalClaimsSurf" style="text-align:justify; width:900px;">';
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на исполнителя заданий <b>'.$row["user_name"].'</b></div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';

				$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
				$message_text.= '<thead><tr align="center">';
					$message_text.= '<th width="120">Логин рекламодателя</th><th width="100">Дата</th><th width="100">IP</th><th>Текст жалобы</th><th width="18"></th>';
				$message_text.= '</thead></tr>';
				$message_text.= '</table>';

				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
					$sql = $mysqli->query("SELECT * FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='task_us' ORDER BY `id` DESC");
					if($sql->num_rows>0) {
						while ($row = $sql->fetch_assoc()) {
							$message_text.= '<tr id="claims-'.$row["id"].'" align="center">';
								$message_text.= '<td width="120">'.$row["username"].'</td>';
								$message_text.= '<td width="100">'.DATE("d.m.Yг. H:i", $row["time"]).'</td>';
								$message_text.= '<td width="100">'.$row["ip"].'</td>';
								$message_text.= '<td>'.$row["claims"].'</td>';
								$message_text.= '<td width="20"><span class="clear-claims" title="Удалить жалобу" onClick="DelClaims('.$row["id"].', \'task_us\', \'DelClaims\');"></span></td>';
							$message_text.= '</tr>';
						}
					}else{
						$message_text.= '<tr align="center"><td colspan="4"><b>Жалобы не обнаружены</b></td></tr>';
					}
					$message_text.= '</table>';
				$message_text.= '</div>';

			$message_text.= '</div>';
		$message_text.= '</div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "";
		$message_text.= '<div class="box-modal" id="ModalClaimsSurf" style="text-align:justify; width:900px;">';
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на исполнителя зазаданий <b>'.$row["user_name"].'</b></div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';
				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<span class="msg-error">Пользователь не найден!</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';
		$message_text.= '</div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "DelClaims") {
	$sql = $mysqli->query("SELECT `ident` FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='task_us'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$ident = $row["ident"];

		$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='task_us'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("UPDATE `tb_ads_task_pay` SET `claims`=`claims`-'1' WHERE `user_id`='$ident'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$count_claims = $mysqli->query("SELECT `claims` FROM `tb_ads_task_pay` WHERE `user_id`='$ident'");
		$count_claims = number_format($count_claims->fetch_object()->claims, 0, ".", "");

		$message_text = "";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text), "ident" => "$ident", "count_claims" => "$count_claims")) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Жалоба не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "DelAllClaims") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task_pay` WHERE `user_id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$mysqli->query("UPDATE `tb_ads_task_pay` SET `claims`='0' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='task_us'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$message_text = "";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Пользователь не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}else{
	$message_text = "ERROR: NO OPTION!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

?>