<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

if(!DEFINED("SENT_EMAILS_AJAX")) {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));

}elseif($type_ads != "sent_emails") {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

$message_text = "";

$sql_p = $mysqli->query("SELECT `sitewmr` FROM `tb_site` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$site_wmr = $sql_p->fetch_object()->sitewmr;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_sent_emails' AND `howmany`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$cena_sent_emails = number_format($sql->fetch_object()->price, 2, ".", "");

$method_pay_to[1] = "WebMoney Merchant";
$method_pay_to[2] = "RoboKassa";
$method_pay_to[3] = "Wallet One";
$method_pay_to[4] = "Interkassa";
$method_pay_to[5] = "Payeer";
$method_pay_to[6] = "Qiwi";
$method_pay_to[7] = "PerfectMoney";
$method_pay_to[8] = "YandexMoney";
$method_pay_to[9] = "MegaKassa";
$method_pay_to[20] = "FreeKassa";
$method_pay_to[21] = "AdvCash";
$method_pay_to[10] = "Рекламный счет";

if($option == "del") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_emails` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_emails` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$result_text = "OK"; $message_text = "Рассылка удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Рассылка не найдена, возможно она уже была удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "add") {
	$subject = (isset($_POST["subject"])) ? limitatexto(limpiarez($_POST["subject"]), 250) : false;
	$message = isset($_POST["message"]) ? limitatexto(limpiarez($_POST["message"]), 2000) : false;
	$message = get_magic_quotes_gpc() ? stripslashes($message) : $message;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", trim($_POST["method_pay"]))) ? intval(trim($_POST["method_pay"])) : 1;
	$method_pay = isset($method_pay_to[$method_pay]) ? $method_pay : false;

	if($subject == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали тему сообщения!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($message == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали текст сообщения!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(strlen($message) < 100) {
		$result_text = "ERROR"; $message_text = "В тексте сообщения должно быть не менее 100 символов!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($method_pay == false) {
		$result_text = "ERROR"; $message_text = "Не указан способ оплаты!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$summa = number_format(($cena_sent_emails * (100-$cab_skidka)/100), 2, ".", "");

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;

		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$mysqli->query("DELETE FROM `tb_ads_emails` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_emails` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_check->num_rows>0) {
			$id_zakaz = $sql->fetch_object()->id;

			$mysqli->query("UPDATE `tb_ads_emails` SET `method_pay`='$method_pay',`merch_tran_id`='$merch_tran_id',`wmid`='$user_wmid',`username`='$user_name',`subject`='$subject',`message`='$message',`date`='".time()."',`ip`='$my_lastiplog',`money`='$summa' WHERE `id`='$id_zakaz'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}else{
			$mysqli->query("INSERT INTO `tb_ads_emails`(`status`,`method_pay`,`session_ident`,`merch_tran_id`,`wmid`,`username`,`subject`,`message`,`ip`,`date`,`money`) 
			VALUES('0','$method_pay','".session_id()."','$merch_tran_id','$user_wmid','$user_name','$subject','$message','$my_lastiplog','".time()."','$summa')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			$id_zakaz = $mysqli->query("SELECT LAST_INSERT_ID() AS last_id FROM `tb_ads_emails`")->fetch_object()->last_id;
		}

        	$sql_id = $mysqli->query("SELECT `id`,`subject`,`message` FROM `tb_ads_emails` WHERE `id`='$id_zakaz' AND `status`='0'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_id->num_rows>0) {
			$row_id = $sql_id->fetch_assoc();
		        $subject_to = $row_id["subject"];
			$message_to = desc_bb($row_id["message"]);
		}else{
		        $subject_to = $subject;
			$message_to = desc_bb($message);
		}

		$message_text.= '<div id="PreView" style="display:block;">';

			$message_text.= '<h3 class="sp"><b>'.$subject_to.'</b> <span style="font-size:13px;">[предварительный просмотр рассылки]</span></h3>';
			$message_text.= '<table align="center" border="0" cellpadding="6" cellspacing="0" style="width:100%; background-color:#EBF1E7;">';
			$message_text.= "<tbody>";
			$message_text.= '<tr><td align="center">';
				$message_text.= '<table align="center" cellpadding="0" cellspacing="0" style="border:1px solid #DDD; width:100%; background-color:#FFF;">';
				$message_text.= "<tbody>";
				$message_text.= '<tr><td style="background-color:#009E58; font-size:14px; line-height:16px; text-align:center; text-shadow: 1px 1px 1px #000; padding:15px; color:#FFF; font-weight: normal;">Здравствуйте, (<i style="font-size:13px;">здесь будет логин пользователя проекта</i>).</td></tr>';
				$message_text.= '<tr><td align="left" style="font-size:12px; font-family:Arial,Helvetica,sans-serif; line-height:20px; padding:20px;">';
					$message_text.= "Вы получили это письмо рекламодателя, так как являетесь зарегистрированным пользователем системы ".strtoupper($_SERVER["HTTP_HOST"])."<br><br>";
					$message_text.= "<b>Содержание письма:</b>"."<br>";
					$message_text.= "$message_to";
				$message_text.= '</td></tr>';
				$message_text.= '<tr><td align="left" style="border-top:1px solid #DDD; font-size:12px; padding:10px 20px;">';
					$message_text.= 'Чтобы отписаться от получения писем, войдите в свой акааунт и в профиле снимите галочку в чекбоксе «отправлять рекламные письма на e-mail».'."<br><br>";
					$message_text.= '<i>Это автоматическое сообщение, отвечать на него не надо, мы все равно не получим Ваш ответ на него.</i>'."<br>";
				$message_text.= '</td></tr>';
				$message_text.= "</tbody>";
				$message_text.= "</table>";
			$message_text.= '</td></tr>';
			$message_text.= "</tbody>";
			$message_text.= "</table>";

			$message_text.= '<div align="center" style="margin:5px auto 10px auto;">';
				$message_text.= '<span onClick="GoToPay(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-blue" style="float:none; display:inline-block;">Продолжить</span>';
				$message_text.= '<span onClick="ChangeAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-green" style="float:none; display:inline-block;">Изменить</span>';
				$message_text.= '<span onClick="DeleteAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-red" style="float:none; display:inline-block;">Удалить</span>';
			$message_text.= '</div>';

		$message_text.= '</div>';

		$shp_item = "15";
		$inv_desc = "Оплата рекламы: рассылка на e-mail, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: sent by e-mail, order:$merch_tran_id";
		$money_add = number_format($summa, 2, ".", "");

		$message_text.= '<div id="ToPaySys" style="display:none;"><table class="tables">';
			$message_text.= '<thead><tr><th align="center" colspan="2">Информация о заказе</th></tr></thead>';
			$message_text.= '<tr><td align="left" width="190">Счет №</td><td align="left">'.number_format($merch_tran_id, 0,".", "").'</td></tr>';
			$message_text.= '<tr><td align="left">ID рекламы</td><td align="left">'.$id_zakaz.'</td></tr>';
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
			$message_text.= '<tr><td align="center" colspan="2">';
				if($method_pay == 10 && $user_name != false) {
					$message_text.= '<span onClick="PayAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-blue160" style="float:none;">Оплатить</span>';
				}elseif($method_pay == 10 && $user_name == false) {
					$message_text.= '<span class="msg-error">Для оплаты с рекламного счета необходимо авторизоваться!</span>';
				}else{
					require_once(ROOT_DIR."/method_pay/method_pay_json.php");
				}
			$message_text.= '</td></tr>';
		$message_text.= '</table></div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "pay") {
	if($user_name==false) {
		$result_text = "ERROR"; $message_text = "Для оплаты с рекламного счета необходимо авторизоваться!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_emails` WHERE `id`='$id' AND `status`='0' AND `username`='$user_name' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
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
				$mysqli->query("UPDATE `tb_ads_emails` SET `status`='1',`date`='".time()."',`wmid`='$user_wmid' WHERE `id`='$id' AND `status`='0' AND `username`='$user_name'  ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				$mysqli->query("INSERT INTO `tb_history` (`status_pay`,`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
				VALUES('1','$user_name','$user_id','".DATE("d.m.Y H:i")."','".time()."','$money_pay','Оплата рекламы (рассылка на e-mail, ID:$id)','Оплачено','reklama')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				stat_pay("sent_emails", $money_pay);
				ads_wmid($user_wmid, $user_wmr, $user_name, $money_pay);
				konkurs_ads_new($user_wmid, $user_name, $money_pay);
				ActionRef(number_format($money_pay,2,".",""), $user_name);
				invest_stat($money_pay, 4);

				$result_text = "OK"; $message_text = "Оплата прошла успешно. Рассылка успешно запущена!";
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