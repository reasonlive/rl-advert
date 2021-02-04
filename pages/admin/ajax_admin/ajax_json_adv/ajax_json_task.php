<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

if(!DEFINED("TASK_AJAX")) {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);

}elseif($type_ads!="task") {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}


if($option == "PlayPause") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];

	if($status == "pay") {
				
	
				$mysqli->query("UPDATE `tb_ads_task` SET `status`='pause' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

				$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				$message_text = "";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

		}elseif($status == "pause") {
				

				$mysqli->query("UPDATE `tb_ads_task` SET `status`='pay' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

				$status_text = '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				$message_text = "";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			

		}elseif($status == 4) {
			$mysqli->query("UPDATE `tb_ads_task` SET `status`='pay', `user_lock`='', `msg_lock`='' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
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

	}elseif($option == "GoLock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];

		if($status==4) {
			$message_text = '';
			$message_text.= '<div align="left" style="padding-left:40px;"><span style="color:#4F4F4F;">Заблокировал:</span> <b>'.$row["user_lock"].'</b></div>';
			$message_text.= '<div align="left" style="padding-left:40px;"><span style="color:#4F4F4F;">Причина блокировки:</span> <b>'.$row["msg_lock"].'</b></div>';

			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$message_text = '';
			$message_text.= '<div align="center" style="float:left; padding:9px 5px; font-weight:bold;">Укажите причину блокировки:</div>';
			$message_text.= '<div id="newform" align="center" style="float:left; width:calc(100% - 300px); padding:5px 5px 3px 0px;"><input class="ok" type="text" id="msg_lock" value="" maxlength="255" autocomplete="off" onKeyDown="$(this).attr(\'class\', \'ok\');" /></div>';
			$message_text.= '<div align="center" style="float:left; padding-left:5px; padding-top:6px;"><span onClick="Lock(\''.$row["id"].'\', \'task\', \'Lock\');" class="sub-red" style="float:none;" title="Заблокировать">Заблокировать</span></div>';

			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Рекламная площадка не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Lock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];
		$msg_lock = (isset($_POST["msg_lock"])) ? limitatexto(limpiarez($_POST["msg_lock"]), 255) : false;

		if($status==4) {
			$message_text = "Рекламная площадка уже заблокирована!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($msg_lock==false) {
			$message_text = 'Укажите причину блокировки!';
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$mysqli->query("UPDATE `tb_ads_task` SET `status`='4', `user_lock`='$username', `msg_lock`='$msg_lock' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$message_text = "Рекламная площадка успешно заблокирована!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Рекламная площадка не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}
	
}elseif($option == "ViewClaims") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];

		$message_text = "";
		$message_text.= '<div class="box-modal" id="ModalClaimsSurf" style="text-align:justify; width:900px;">';
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на рекламную площадку №'.$id.'</div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';

				$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
				$message_text.= '<thead><tr align="center">';
					$message_text.= '<th width="120">Логин</th><th width="100">Дата</th><th width="100">IP</th><th>Текст жалобы</th><th width="18"></th>';
				$message_text.= '</thead></tr>';
				$message_text.= '</table>';

				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
					$sql = $mysqli->query("SELECT * FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='task' ORDER BY `id` DESC");
					if($sql->num_rows>0) {
						while ($row = $sql->fetch_assoc()) {
							$message_text.= '<tr id="claims-'.$row["id"].'" align="center">';
								$message_text.= '<td width="120">'.$row["username"].'</td>';
								$message_text.= '<td width="100">'.DATE("d.m.Yг. H:i", $row["time"]).'</td>';
								$message_text.= '<td width="100">'.$row["ip"].'</td>';
								$message_text.= '<td>'.$row["claims"].'</td>';
								$message_text.= '<td width="20"><span class="clear-claims" title="Удалить жалобу" onClick="DelClaims('.$row["id"].', \'task\', \'DelClaims\');"></span></td>';
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
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на рекламную площадку №'.$id.'</div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';
				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<span class="msg-error">Рекламная площадка не найдена!</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';
		$message_text.= '</div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "DelClaims") {
	$sql = $mysqli->query("SELECT `ident` FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='task'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$ident = $row["ident"];

		$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='task'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("UPDATE `tb_ads_task` SET `claims`=`claims`-'1' WHERE `id`='$ident'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$count_claims = $mysqli->query("SELECT `claims` FROM `tb_ads_task` WHERE `id`='$ident'");
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
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$mysqli->query("UPDATE `tb_ads_task` SET `claims`='0' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='task'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$message_text = "";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Рекламная площадка не найдена!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Delete") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_task` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

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