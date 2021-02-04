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

$method_pay_to[1] = "WebMoney";
$method_pay_to[2] = "RoboKassa";
$method_pay_to[3] = "Wallet One";
$method_pay_to[4] = "InterKassa";
$method_pay_to[5] = "Payeer";
$method_pay_to[6] = "Qiwi";
$method_pay_to[7] = "PerfectMoney";
$method_pay_to[8] = "YandexMoney";
$method_pay_to[9] = "MegaKassa";
$method_pay_to[20] = "FreeKassa";
$method_pay_to[21] = "AdvCash";
$method_pay_to[10] = "Рекламный счет";

if($option == "del") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_pay_row` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_pay_row` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$result_text = "OK"; $message_text = "Реклама удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Реклама не найдена, возможно она уже была удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "add") {
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$description = isset($_POST["description"]) ? limitatexto(limpiarez($_POST["description"]), 60) : false;
	$description = get_magic_quotes_gpc() ? stripslashes($description) : $description;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", trim($_POST["method_pay"]))) ? intval(trim($_POST["method_pay"])) : 1;
	$method_pay = isset($method_pay_to[$method_pay]) ? $method_pay : false;
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

	}elseif($method_pay == false) {
		$result_text = "ERROR"; $message_text = "Не указан способ оплаты!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		$result_text = "ERROR"; $message_text = SFB_YANDEX($url);
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$summa = number_format(($cena_pay_row * (100-$cab_skidka)/100), 2, ".", "");

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

		$shp_item = "31";
		$inv_desc = "Оплата рекламы: платная строка, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: pay row, order:$merch_tran_id";
		$money_add = number_format($summa, 2, ".", "");

		//$message_text.= '<span class="msg-ok" style="margin-bottom:4px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		$message_text.= '<table class="tables">';
			$message_text.= '<thead><tr><th align="center" colspan="2">Информация о заказе</th></tr></thead>';
			$message_text.= '<tr><td align="left" width="190">Счет №</td><td align="left">'.number_format($merch_tran_id, 0,".", "").'</td></tr>';
			$message_text.= '<tr><td align="left">ID рекламы</td><td align="left">'.$id_zakaz.'</td></tr>';
			$message_text.= '<tr><td align="left">Описание ссылки</td><td align="left">'.$description.'</td></tr>';
			$message_text.= '<tr><td align="left">URL сайта</td><td align="left"><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			$message_text.= isset($cab_text) ? $cab_text : false;
			$message_text.= '<tr><td align="left">Способ оплаты</td><td align="left"><b>'.$method_pay_to[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				$message_text.= '<tr><td>Стоимость заказа:</td><td><b style="color:green;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				$message_text.= '<tr><td>Сумма к оплате</td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';

			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				$message_text.= '<tr><td><b>Стоиомсть заказа</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				$message_text.= '<tr><td><b>Сумма к оплате</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';
			}else{
				$message_text.= '<tr><td>Сумма к оплате</td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
			$message_text.= '<tr>';
				$message_text.= '<td align="center" style="border-right:none;">';
					if($method_pay == 10 && $user_name != false) {
						$message_text.= '<span onClick="PayAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-blue160" style="float:none;">Оплатить</span>';
					}elseif($method_pay == 10 && $user_name == false) {
						$message_text.= '<span class="msg-error">Для оплаты с рекламного счета необходимо авторизоваться!</span>';
					}else{
						require_once(ROOT_DIR."/method_pay/method_pay_json.php");
					}
				$message_text.= '</td>';
				$message_text.= '<td align="center" style="border-left:none;">';
					$message_text.= '<span onClick="DeleteAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-red" style="float:right; display:inline-block;">Удалить</span>';
					$message_text.= '<span onClick="ChangeAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-green" style="float:right; display:inline-block;">Изменить</span>';
				$message_text.= '</td>';
			$message_text.= '</tr>';
		$message_text.= '</table>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "pay") {
	if($user_name==false) {
		$result_text = "ERROR"; $message_text = "Для оплаты с рекламного счета необходимо авторизоваться!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_pay_row` WHERE `id`='$id' AND `status`='0' AND `username`='$user_name' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_assoc();
			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];

			if($user_money_rb >= $money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($user_referer_1!=false) $mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$user_referer_1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1',`money_rb`=`money_rb`-'$money_pay',`money_rek`=`money_rek`+'$money_pay' WHERE `username`='$user_name'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
				$mysqli->query("UPDATE `tb_ads_pay_row` SET `status`='1',`date`='".time()."',`wmid`='$user_wmid' WHERE `id`='$id' AND `status`='0' AND `username`='$user_name'  ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				$mysqli->query("INSERT INTO `tb_history` (`status_pay`,`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
				VALUES('1','$user_name','$user_id','".DATE("d.m.Y H:i")."','".time()."','$money_pay','Оплата рекламы (платная строка, ID:$id)','Оплачено','reklama')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				stat_pay("pay_row", $money_pay);
				ads_wmid($user_wmid, $user_wmr, $user_name, $money_pay);
				konkurs_ads_new($user_wmid, $user_name, $money_pay);
				invest_stat($money_pay, 4);
				ActionRef(number_format($money_pay,2,".",""), $user_name);
				cache_pay_row();

				$result_text = "OK"; $message_text = "Ваша реклама успешно размещена!<br>Спасибо, что пользуетесь услугами нашего сервиса!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}else{
				$result_text = "ERROR"; $message_text = "На вашем рекламном счету недостаточно средств для оплаты рекламы!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}
		}else{
			$result_text = "ERROR"; $message_text = "Ошибка! Заказа рекламы с №$id не существует, либо заказ уже был оплачен!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}
}else{
	$result_text = "ERROR"; $message_text = "ERROR: NO OPTION!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

?>