<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
require_once(ROOT_DIR."/merchant/func_cache.php");

if(!DEFINED("PAY_ROW_AJAX")) {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));

}elseif($type_ads != "pay_row") {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

$message_text = "";

$sql_p = $mysqli->query("SELECT `sitewmr` FROM `tb_site` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$site_wmr = $sql_p->fetch_object()->sitewmr;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_pay_row' AND `howmany`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$cena_pay_row = number_format($sql->fetch_object()->price, 2, ".", "");


if($option == "Delete") {
	$sql_check = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_row` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_check->num_rows>0) {
		$row = $sql_check->fetch_assoc();
		$status = $row["status"];

		$mysqli->query("DELETE FROM `tb_ads_pay_row` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($status == 1) $mysqli->query("UPDATE `tb_ads_pay_row` SET `status`='1' WHERE `status`='3' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

		cache_pay_row();

		$result_text = "OK"; $message_text = "Рекламная площадка удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Рекламная площадка с ID:$id не найдена, возможно она уже была удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Add") {
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$description = isset($_POST["description"]) ? limitatexto(limpiarez($_POST["description"]), 60) : false;
	$description = get_magic_quotes_gpc() ? stripslashes($description) : $description;
	$method_pay = 0;
	$black_url = getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

	if($description == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали описание ссылки!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($sql_bl->num_rows > 0 && $black_url != false) {
		$row_bl = $sql_bl->fetch_assoc();
		$result_text = "ERROR"; $message_text = "Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($url == false | $url == "http://" | $url == "https://") {
		$result_text = "ERROR"; $message_text = "Вы не указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		$result_text = "ERROR"; $message_text = "Вы неверно указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$summa = number_format($cena_pay_row, 2, ".", "");

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;

		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$mysqli->query("DELETE FROM `tb_ads_pay_row` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_pay_row` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_check->num_rows>0) {
			$id_zakaz = $sql_check->fetch_object()->id;

			$mysqli->query("UPDATE `tb_ads_pay_row` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$user_wmid',`username`='$user_name',`date`='".time()."',`url`='$url',`description`='$description',`money`='$summa',`ip`='$my_lastiplog' WHERE `id`='$id_zakaz'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}else{
			$mysqli->query("INSERT INTO `tb_ads_pay_row` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`url`,`description`,`money`,`ip`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$user_wmid','$user_name','".time()."','$url','$description','$summa','$my_lastiplog')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			$id_zakaz = $mysqli->query("SELECT LAST_INSERT_ID() AS last_id FROM `tb_ads_pay_row`")->fetch_object()->last_id;
		}

        	$sql_id = $mysqli->query("SELECT `id`,`url`,`description` FROM `tb_ads_pay_row` WHERE `id`='$id_zakaz' AND `status`='0'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_id->num_rows>0) {
			$row_id = $sql_id->fetch_assoc();
		        $id_zakaz = $row_id["id"];
		        $pay_row_url = $row_id["url"];
			$pay_row_desc = $row_id["description"];
		}else{
		        $pay_row_url = $url;
			$pay_row_desc = $description;
		}

		$message_text.= '<div id="PreView" style="display:block;">';
			$message_text.= '<table class="tables">';
				$message_text.= '<thead><tr><th align="center" colspan="2">Информация о рекламе</th></tr></thead>';
				$message_text.= '<tr><td align="left" width="200">ID рекламы</td><td align="left">'.$id_zakaz.'</td></tr>';
				$message_text.= '<tr><td align="left">Описание ссылки</td><td align="left">'.$pay_row_desc.'</td></tr>';
				$message_text.= '<tr><td align="left">URL сайта</td><td align="left"><a href="'.$pay_row_url.'" target="_blank">'.$pay_row_url.'</a></td></tr>';
			$message_text.= '</table>';

			$message_text.= '<div align="center" style="margin:5px auto 10px auto;">';
				$message_text.= '<span onClick="AddAdv(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-blue" style="float:none; display:inline-block;">Разместить</span>';
				$message_text.= '<span onClick="ChangeAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-green" style="float:none; display:inline-block;">Изменить</span>';
				$message_text.= '<span onClick="DeleteAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-red" style="float:none; display:inline-block;">Удалить</span>';
			$message_text.= '</div>';
		$message_text.= '</div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Start") {
	$sql_id = $mysqli->query("SELECT * FROM `tb_ads_pay_row` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_id->num_rows>0) {
		$row = $sql_id->fetch_assoc();
		$status = $row["status"];
		$money_pay = $row["money"];
		$merch_tran_id = $row["merch_tran_id"];

		$mysqli->query("UPDATE `tb_ads_pay_row` SET `status`='1', `date`='".time()."' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		cache_pay_row();

		$result_text = "OK"; $message_text = "Рекламная площадка успешно опубликована!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Заказа рекламы с ID:$id не существует, либо заказ уже был оплачен!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "LoadForm") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_pay_row` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$message_text.= '<div id="newform" onkeypress="CtrlEnter(event);">';
			$message_text.= '<table class="tables" style="border:none; margin:0; padding:10px; width:100%;">';
			$message_text.= '<thead><tr><th align="center" colspan="2">Редактирование ссылки ID:'.$id.'</th></thead></tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left" width="220"><b>Описание ссылки</b></td>';
				$message_text.= '<td align="left"><input type="text" id="description" maxlength="60" value="'.$row["description"].'" class="ok"></td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left"><b>URL сайта</b> (включая http://)</td>';
				$message_text.= '<td align="left"><input type="text" id="url" maxlength="300" value="'.$row["url"].'" class="ok"></td>';
			$message_text.= '</tr>';
			$message_text.= '</table>';
		$message_text.= '</div>';

		$message_text.= '<div align="center"><span id="Save" onClick="SaveAds(\''.$id.'\', \'pay_row\', \'Save\');" class="sub-blue160" style="float:none; width:160px;">Сохранить изменения</span></div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Рекламная площадка с ID:$id не найдена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Save") {
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$description = isset($_POST["description"]) ? limitatexto(limpiarez($_POST["description"]), 60) : false;
	$description = get_magic_quotes_gpc() ? stripslashes($description) : $description;
	$black_url = getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

	if($description == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали описание ссылки!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($sql_bl->num_rows > 0 && $black_url != false) {
		$row_bl = $sql_bl->fetch_assoc();
		$result_text = "ERROR"; $message_text = "Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($url == false | $url == "http://" | $url == "https://") {
		$result_text = "ERROR"; $message_text = "Вы не указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		$result_text = "ERROR"; $message_text = "Вы неверно указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_pay_row` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_check->num_rows>0) {

			$mysqli->query("UPDATE `tb_ads_pay_row` SET `date`='".time()."',`url`='$url',`description`='$description' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			cache_pay_row();

			$result_text = "OK"; $message_text = "$description";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}else{
			$result_text = "ERROR"; $message_text = "Ошибка! Рекламная площадка с ID:$id не найдена!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}

}else{
	$result_text = "ERROR"; $message_text = "ERROR: NO OPTION!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

?>