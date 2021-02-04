<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
if(!DEFINED("TESTS_AJAX")) {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}elseif($type_ads!="tests") {
	$message_text = "ERROR: Hacking attempt!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

$mysqli->query("UPDATE `tb_ads_tests` SET `status`='3', `date_edit`='".time()."' WHERE `status`>'0' AND `status`<'4' AND `balance`<`cena_advs`");
$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1', `date_edit`='".time()."' WHERE `status`='3' AND `balance`>=`cena_advs`");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_hit' AND `howmany`='1'");
$tests_cena_hit = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_nacenka' AND `howmany`='1'");
$tests_nacenka = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_min_pay' AND `howmany`='1'");
$tests_min_pay = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_quest' AND `howmany`='1'");
$tests_cena_quest = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_color' AND `howmany`='1'");
$tests_cena_color = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_comis_del' AND `howmany`='1'");
$tests_comis_del = number_format($sql->fetch_object()->price, 0, ".", "");

for($i=1; $i<=4; $i++) {
	$tests_cena_revisit[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_revisit' AND `howmany`='$i'");
	$tests_cena_revisit[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

for($i=1; $i<=2; $i++) {
	$tests_cena_unic_ip[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_unic_ip' AND `howmany`='$i'");
	$tests_cena_unic_ip[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}


if($option == "GoLock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
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
			$message_text.= '<div align="center" style="float:left; padding-left:5px; padding-top:6px;"><span onClick="Lock(\''.$row["id"].'\', \'tests\', \'Lock\');" class="sub-red" style="float:none;" title="Заблокировать">Заблокировать</span></div>';

			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Lock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];
		$msg_lock = (isset($_POST["msg_lock"])) ? limitatexto(limpiarez($_POST["msg_lock"]), 255) : false;

		if($status==4) {
			$message_text = "Тест, уже заблокирован!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($msg_lock==false) {
			$message_text = 'Укажите причину блокировки!';
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{

			$mysqli->query("UPDATE `tb_ads_tests` SET `status`='4', `date_edit`='".time()."', `user_lock`='$username', `msg_lock`='$msg_lock' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$message_text = "Тест успешно заблокирован!";

			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "ViewClaims") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];

		$message_text = "";
		$message_text.= '<div class="box-modal" id="ModalClaimsTest" style="text-align:justify; width:900px;">';
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на тест №'.$id.'</div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';

				$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
				$message_text.= '<thead><tr align="center">';
					$message_text.= '<th width="120">Логин</th><th width="100">Дата</th><th width="100">IP</th><th>Текст жалобы</th><th width="18"></th>';
				$message_text.= '</thead></tr>';
				$message_text.= '</table>';

				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<table class="tables" style="margin:0 auto; padding:0;">';
					$sql = $mysqli->query("SELECT * FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='tests' ORDER BY `id` DESC");
					if($sql->num_rows>0) {
						while ($row = $sql->fetch_assoc()) {
							$message_text.= '<tr id="claims-'.$row["id"].'" align="center">';
								$message_text.= '<td width="120">'.$row["username"].'</td>';
								$message_text.= '<td width="100">'.DATE("d.m.Yг. H:i", $row["time"]).'</td>';
								$message_text.= '<td width="100">'.$row["ip"].'</td>';
								$message_text.= '<td>'.$row["claims"].'</td>';
								$message_text.= '<td width="20"><span class="clear-claims" title="Удалить жалобу" onClick="DelClaims('.$row["id"].', \'tests\', \'DelClaims\');"></span></td>';
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
		$message_text.= '<div class="box-modal" id="ModalClaimsTest" style="text-align:justify; width:900px;">';
			$message_text.= '<div class="box-modal-title">Просмотр жалоб на тест №'.$id.'</div>';
			$message_text.= '<div class="box-modal-close modalpopup-close"></div>';
			$message_text.= '<div class="box-modal-content" style="margin:1px; padding:5px 3px; font-size:11px;">';
				$message_text.= '<div id="table-content" style="overflow:auto;">';
					$message_text.= '<span class="msg-error">Тест не найден!</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';
		$message_text.= '</div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "DelClaims") {
	$sql = $mysqli->query("SELECT `ident` FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='tests'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$ident = $row["ident"];

		$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `id`='$id' AND `type`='tests'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("UPDATE `tb_ads_tests` SET `claims`=`claims`-'1' WHERE `id`='$ident'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$count_claims = $mysqli->query("SELECT `claims` FROM `tb_ads_tests` WHERE `id`='$ident'");
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
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		if($row["claims"] > 0) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `claims`='0' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
			$mysqli->query("DELETE FROM `tb_ads_claims` WHERE `ident`='$id' AND `type`='tests'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$message_text = "ERROR: Жалобы не обнаружены!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Add") {
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 55) : false;
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]), 1000) : false;
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	if (get_magic_quotes_gpc()) { $description = stripslashes($description); }
	$revisit = (isset($_POST["revisit"]) && (intval($_POST["revisit"])>=0 && intval($_POST["revisit"])<=4)) ? intval(limpiarez($_POST["revisit"])) : "0";
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(limpiarez($_POST["color"])) : "0";
	$unic_ip_user = (isset($_POST["unic_ip_user"]) && (intval($_POST["unic_ip_user"])>=0 && intval($_POST["unic_ip_user"])<=2)) ? intval($_POST["unic_ip_user"]) : "0";
	$date_reg_user = (isset($_POST["date_reg_user"]) && (intval($_POST["date_reg_user"])>=0 && intval($_POST["date_reg_user"])<=4)) ? intval($_POST["date_reg_user"]) : "0";
	$sex_user = ( isset($_POST["sex_user"]) && preg_match("|^[\d]{1,11}$|", limpiarez($_POST["sex_user"])) && intval(limpiarez($_POST["sex_user"]))>=0 && intval(limpiarez($_POST["sex_user"]))<=2 ) ? abs(intval(limpiarez($_POST["sex_user"]))) : 0;
	$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map(array($mysqli, 'real_escape_string'), $_POST["country"])) : false;
	$money_add = ( isset($_POST["money_add"]) && preg_match( "|^[\d]*[\.,]?[\d]{0,2}$|", abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) ) ) ? abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) : false;
	$method_pay = 0;
	$black_url = getHost($url);

	$revisit_tab[0] = "Доступно всем каждые 24 часа";
	$revisit_tab[1] = "Доступно всем каждые 3 дня";
	$revisit_tab[2] = "Доступно всем каждую неделю";
	$revisit_tab[3] = "Доступно всем каждые 2 недели";
	$revisit_tab[4] = "Доступно всем каждый месяц";

	$color_tab[0] = "Нет";
	$color_tab[1] = "Да";

	$unic_ip_user_tab[0] = "Нет";
	$unic_ip_user_tab[1] = "Да, 100% совпадение";
	$unic_ip_user_tab[2] = "Усиленный по маске до 2 чисел (255.255.X.X)";

	$date_reg_user_tab[0] = "Все пользователи проекта";
	$date_reg_user_tab[1] = "До 7 дней с момента регистрации";
	$date_reg_user_tab[2] = "От 7 дней с момента регистрации";
	$date_reg_user_tab[3] = "От 30 дней с момента регистрации";
	$date_reg_user_tab[4] = "От 90 дней с момента регистрации";

	$sex_user_tab[0] = "Все пользователи проекта";
	$sex_user_tab[1] = "Только мужчины";
	$sex_user_tab[2] = "Только женщины";

	for($i=1; $i<=5; $i++) {
		$quest[$i] = (isset($_POST["quest$i"])) ? limitatexto(limpiarez($_POST["quest$i"]), 300) : false;
	}

	for($i=1; $i<=5; $i++) {
		for($y=1; $y<=3; $y++) {
			$answ[$i][$y] = (isset($_POST["answ$i$y"])) ? limitatexto(limpiarez($_POST["answ$i$y"]), 30) : false;
		}
	}

	if(is_array($country)) {
		foreach($country as $key => $val) {
			if(array_search($val, $geo_cod_arr)) {
				$id_country = array_search($val, $geo_cod_arr);
				$country_arr[] = $val;
				$country_arr_ru[] = $geo_name_arr_ru[$val];
			}
		}
	}
	$country = isset($country_arr) ? trim(strtoupper(implode(", ", $country_arr))) : false;
	$country_to = isset($country_arr_ru) ? trim(strtoupper(implode(', ', $country_arr_ru))) : false;
	if($country_to!=false) {$country_to="$country_to";}else{$country_to="Нет";}

	if($quest[4]=="" | $answ[4][1]=="" | $answ[4][2]=="" | $answ[4][3]=="") {
		$quest[4] = ""; $answ[4][1] = ""; $answ[4][2] = ""; $answ[4][3] = "";
	}
	if($quest[5]=="" | $answ[5][1]=="" | $answ[5][2]=="" | $answ[5][3]=="") {
		$quest[5] = ""; $answ[5][1] = ""; $answ[5][2] = ""; $answ[5][3] = "";
	}
	if( ($quest[5]!="" && $answ[5][1]!="" && $answ[5][2]!="" && $answ[5][3]!="") && ($quest[4]=="" | $answ[4][1]=="" | $answ[4][2]=="" | $answ[4][3]=="") ) {
		$quest[4] = $quest[5]; $answ[4][1] = $answ[5][1]; $answ[4][2] = $answ[5][2]; $answ[4][3] = $answ[5][3];
		$quest[5] = ""; $answ[5][1] = ""; $answ[5][2] = ""; $answ[5][3] = "";
	}

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");

	if($title=="") {
		$message_text = "ERROR: Вы не указали заголовок теста!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($description=="") {
		$message_text = "ERROR: Вы не описали инструкцию к выполнению теста!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($sql_bl->num_rows>0 && $black_url!=false) {
		$row_bl = $sql_bl->fetch_array();
		$message_text = "ERROR: Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($url==false | $url=="http://" | $url=="https://") {
		$message_text = "ERROR: Вы не указали URL-адрес сайта!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		$message_text = "ERROR: Вы неверно указали URL-адрес сайта!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($quest[1]=="") {
		$message_text = "ERROR: Вы не указали первый вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($answ[1][1]=="" | $answ[1][2]=="" | $answ[1][3]=="") {
		$message_text = "ERROR: Вы не указали варианты ответа на первый вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($quest[2]=="") {
		$message_text = "ERROR: Вы не указали второй вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($answ[2][1]=="" | $answ[2][2]=="" | $answ[2][3]=="") {
		$message_text = "ERROR: Вы не указали варианты ответа на второй вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($quest[3]=="") {
		$message_text = "ERROR: Вы не указали третий вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($answ[3][1]=="" | $answ[3][2]=="" | $answ[3][3]=="") {
		$message_text = "ERROR: Вы не указали варианты ответа на третий вопрос!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($money_add=="") {
		$message_text = "ERROR: Cумма пополнения бюджета рекламной площадки введена не верно!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}elseif($money_add<$tests_min_pay) {
		$message_text = "ERROR: Минимальная сумма пополнения - ".number_format($tests_min_pay,2,".","")." руб.";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);

	}else{
		$summa_add = 0;
		if($quest[4]!="") $summa_add+= $tests_cena_quest;
		if($quest[5]!="") $summa_add+= $tests_cena_quest;

		$cena_user = ($tests_cena_hit + $summa_add) / (($tests_nacenka+100)/100);
		$cena_advs = ($tests_cena_hit + $summa_add + $tests_cena_revisit[$revisit] + $tests_cena_color * $color + $tests_cena_unic_ip[$unic_ip_user]);

		$cena_user = number_format($cena_user, 4, ".", "");
		$cena_advs = number_format($cena_advs, 4, ".", "");
		$money_add = number_format($money_add, 2, ".", "");

		$count_tests = floor(bcdiv($money_add, $cena_advs));

		if($quest[4]=="") unset($quest[4], $answ[4]);
		if($quest[5]=="") unset($quest[5], $answ[5]);

		$questions = serialize($quest);
		$answers = serialize($answ);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;

		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($sql_check->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`date`='".time()."',`date_edit`='".time()."',`title`='$title',`description`='$description',`url`='$url',`questions`='$questions',`answers`='$answers',`geo_targ`='$country',`revisit`='$revisit',`color`='$color',`date_reg_user`='$date_reg_user',`unic_ip_user`='$unic_ip_user',`sex_user`='$sex_user',`cena_user`='$cena_user',`cena_advs`='$cena_advs',`money`='$money_add',`balance`='0',`ip`='$laip' WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_tests`(`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_edit`,`title`,`description`,`url`,`questions`,`answers`,`geo_targ`,`revisit`,`color`,`date_reg_user`,`unic_ip_user`,`sex_user`,`cena_user`,`cena_advs`,`money`,`balance`,`ip`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','".time()."','".time()."','$title','$description','$url','$questions','$answers','$country','$revisit','$color','$date_reg_user','$unic_ip_user','$sex_user','$cena_user','$cena_advs','$money_add','0','$laip')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
		}

        	$sql_id = $mysqli->query("SELECT `id`,`description`,`questions`,`answers`,`geo_targ` FROM `tb_ads_tests` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row_id = $sql_id->fetch_array();
		        $id_zakaz = $row_id["id"];
		        $description_to = $row_id["description"];
			$questions_to = $row_id["questions"];
			$answers_to = $row_id["answers"];
			$geo_targ = (isset($row_id["geo_targ"]) && trim($row_id["geo_targ"])!=false) ? explode(", ", $row_id["geo_targ"]) : array();
		}else{
			$message_text = "ERROR: NO ID!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}

		$description_to = new bbcode($description_to);
		$description_to = $description_to->get_html();
		$description_to = str_replace("&amp;", "&", $description_to);

		$message_text = "";
		$message_text.= '<span class="msg-ok" style="margin-bottom:0px;">Предварительный просмотр!</span>';
		$message_text.= '<table class="tables" style="margin:0; padding:0;">';
		$message_text.= '<tr><td align="left" width="190">Счет № (ID)</td><td align="left">'.number_format($merch_tran_id, 0,".", "").' ('.$id_zakaz.')</td></tr>';
		$message_text.= '<tr><td align="left">Заголовок теста</td><td align="left">'.$title.'</td></tr>';
		$message_text.= '<tr><td align="left">Инструкции для тестирования</td><td align="left">'.$description_to.'</td></tr>';
		$message_text.= '<tr><td align="left">URL сайта</td><td align="left"><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';

		for($i=1; $i<=count($quest); $i++){
			$message_text.= '<tr><td align="left">Вопрос №'.$i.'</td><td align="left">'.$quest[$i].'</td></tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left">Варианты ответа</td>';
				$message_text.= '<td align="left">';
					for($y=1; $y<=3; $y++){
						$message_text.= '<span style="color: '.($y==1 ? "#009125;" : "#FF0000").'">'.$answ[$i][$y].'</span>'.($y!=3 ? "<br>" : "").'';
					}
				$message_text.= '</td>';
			$message_text.= '</tr>';
		}

		$message_text.= '<tr><td align="left">Технология тестирования</td><td align="left">'.$revisit_tab[$revisit].'</td></tr>';
		$message_text.= '<tr><td align="left">Выделить тест</td><td align="left">'.$color_tab[$color].'</td></tr>';
		$message_text.= '<tr><td align="left">Уникальный IP</td><td align="left">'.$unic_ip_user_tab[$unic_ip_user].'</td></tr>';
		$message_text.= '<tr><td align="left">По дате регистрации</td><td align="left">'.$date_reg_user_tab[$date_reg_user].'</td></tr>';
		$message_text.= '<tr><td align="left">По половому признаку</td><td align="left">'.$sex_user_tab[$sex_user].'</td></tr>';

		if(count($geo_targ)>0) {
			$message_text.= '<tr><td align="left">Геотаргетинг</td><td align="left">';
			for($i=0; $i<count($geo_targ); $i++){
				$message_text.= '&nbsp;<img src="//'.$_SERVER["HTTP_HOST"].'/img/flags/'.strtolower($geo_targ[$i]).'.gif" alt="" align="absmiddle" style="margin:0; padding:0; padding-left:1px;" />';
			}
			$message_text.= '</td></tr>';
		}else{
			$message_text.= '<tr><td align="left">Геотаргетинг</td><td align="left"><b>все страны</b></td></tr>';
		}

		$message_text.= '<tr><td align="left">Количество выполнений</td><td align="left">'.$count_tests.'</td></tr>';

		$message_text.= '<tr>';
			$message_text.= '<td align="left"><span onClick="PayAds(\''.$id_zakaz.'\');" class="sub-blue160" style="float:left;">Разместить тест</span></td>';
			$message_text.= '<td align="center" style="border-left:none;">';
				$message_text.= '<span onClick="DeleteAds(\''.$id_zakaz.'\');" class="sub-red" style="float:right;">Удалить</span>';
				$message_text.= '<span onClick="ChangeAds();" class="sub-green" style="float:right;">Изменить</span>';
			$message_text.= '</td>';
		$message_text.= '</tr>';

		$message_text.= '</table>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Delete") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `id`='$id' AND `status`='0'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$message_text = "OK";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "Start") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `id`='$id' AND `status`='0'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql_check->num_rows>0) {
		$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1',`date_edit`='".time()."',`balance`=`money` WHERE `id`='$id' AND `status`='0'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

		$message_text = "Тест успешно размещен!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "PlayPause") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];

		if($status == 0) {
			$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
			$message_text = "Для запуска, необходимо пополнить рекламный бюджет!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 1) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `status`='2', `date_edit`='".time()."' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 2) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1', `date_edit`='".time()."' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
			$message_text = "";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);

		}elseif($status == 3) {
			if($row["balance"]>=$row["cena_advs"]) {
				$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1', `date_edit`='".time()."' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

				$status_text = '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
				$message_text = "";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}else{
				$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
				$message_text = "Для запуска, необходимо пополнить рекламный бюджет!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}

		}elseif($status == 4) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `status`='2', `date_edit`='".time()."', `user_lock`='', `msg_lock`='' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$status_text = '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'tests\', \'PlayPause\');"></span>';
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
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "status" => iconv("CP1251", "UTF-8", $status_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "GetInfo") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$description = new bbcode($row["description"]);
		$description = $description->get_html();
		$description = str_replace("&amp;", "&", $description);
		$url = $row["url"];
		$questions = unserialize($row["questions"]);
		$answers = unserialize($row["answers"]);
		$geo_targ = (isset($row["geo_targ"]) && trim($row["geo_targ"])!=false) ? explode(", ", $row["geo_targ"]) : array();

		$revisit_tab[0] = "доступно всем каждые 24 часа";
		$revisit_tab[1] = "доступно всем каждые 3 дня";
		$revisit_tab[2] = "доступно всем каждую неделю";
		$revisit_tab[3] = "доступно всем каждые 2 недели";
		$revisit_tab[4] = "доступно всем каждый месяц";
		$color_tab[0] = "нет";
		$color_tab[1] = "да";
		$unic_ip_user_tab[0] = "нет";
		$unic_ip_user_tab[1] = "да, 100% совпадение";
		$unic_ip_user_tab[2] = "усиленный по маске до 2 чисел (255.255.X.X)";
		$date_reg_user_tab[0] = "все пользователи проекта";
		$date_reg_user_tab[1] = "до 7 дней с момента регистрации";
		$date_reg_user_tab[2] = "от 7 дней с момента регистрации";
		$date_reg_user_tab[3] = "от 30 дней с момента регистрации";
		$date_reg_user_tab[4] = "от 90 дней с момента регистрации";
		$sex_user_tab[0] = "все пользователи проекта";
		$sex_user_tab[1] = "только мужчины";
		$sex_user_tab[2] = "только женщины";

		$message_text = '<div align="justify" style="margin:5px; color: #333333; font-size:12px;">';
			$message_text.= '<h3 class="sp" style="margin-top:0px;">Инструкция для тестирования:</h3>';
			$message_text.= "$description<br><br>";
			$message_text.= '<h3 class="sp" style="margin-top:0px;">Ссылка для перехода:</h3>';
			$message_text.= "$url<br><br>";

			for($i=1; $i<=count($questions); $i++){
				$message_text.= '<h3 class="sp" style="margin-top:0px;">Вопрос №'.$i.':</h3>';
				$message_text.= "$questions[$i]<br>";
				$message_text.= '<div style="margin-left:20px;"><u>Ответы:</u><br>';
				for($y=1; $y<=3; $y++){
					$message_text.= '<span style="color: '.($y==1 ? "#009125;" : "#FF0000").'">'.$answers[$i][$y].'</span>'.($y!=3 ? "<br>" : "").'';
				}
				$message_text.= '</div><br>';
			}

			$message_text.= '<h3 class="sp" style="margin-top:0px;">Настройки:</h3>';
			$message_text.= 'Технология тестирования: <b>'.$revisit_tab[$row["revisit"]].'</b><br>';
			$message_text.= 'Выделить тест: <b>'.$color_tab[$row["color"]].'</b><br>';
			$message_text.= 'Уникальный IP: <b>'.$unic_ip_user_tab[$row["unic_ip_user"]].'</b><br>';
			$message_text.= 'По дате регистрации: <b>'.$date_reg_user_tab[$row["date_reg_user"]].'</b><br>';
			$message_text.= 'По половому признаку: <b>'.$sex_user_tab[$row["sex_user"]].'</b><br>';
			$message_text.= 'Гео-таргетинг:&nbsp;';

			if(count($geo_targ)>0) {
				for($i=0; $i<count($geo_targ); $i++){
					$message_text.= '&nbsp;<img src="//'.$_SERVER["HTTP_HOST"].'/img/flags/'.strtolower($geo_targ[$i]).'.gif" alt="" align="absmiddle" style="margin:0; padding:0; padding-left:1px;" />';
				}
			}else{
				$message_text.= '<b>все страны</b>';
			}
		$message_text.= '</div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "ClearStat") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];
		$goods_out = $row["goods_out"];
		$bads_out = $row["bads_out"];

		if($status == 0 | ($goods_out == 0 && $bads_out == 0)) {
			$message_text = "ERROR: Счётчик этой площадки уже равен 0";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$mysqli->query("UPDATE `tb_ads_tests` SET `goods_out`='0',`bads_out`='0' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$message_text = "OK";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "GoEdit") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];

		if($status==1) {
			$message_text = "Перед редактированием необходимо приостановить рекламную кампанию";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$message_text = "OK";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "EditAds") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];
		//$country = $row["country"];

		if($status==1) {
			$message_text = "Перед редактированием необходимо приостановить рекламную кампанию";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			
			global $mysqli;
			$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 55) : false;
			$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]), 1000) : false;
			$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
			if (get_magic_quotes_gpc()) { $description = stripslashes($description); }
			$revisit = (isset($_POST["revisit"]) && (intval($_POST["revisit"])>=0 && intval($_POST["revisit"])<=4)) ? intval(limpiarez($_POST["revisit"])) : "0";
			$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(limpiarez($_POST["color"])) : "0";
			$unic_ip_user = (isset($_POST["unic_ip_user"]) && (intval($_POST["unic_ip_user"])>=0 && intval($_POST["unic_ip_user"])<=2)) ? intval($_POST["unic_ip_user"]) : "0";
			$date_reg_user = (isset($_POST["date_reg_user"]) && (intval($_POST["date_reg_user"])>=0 && intval($_POST["date_reg_user"])<=4)) ? intval($_POST["date_reg_user"]) : "0";
			$sex_user = ( isset($_POST["sex_user"]) && preg_match("|^[\d]{1,11}$|", limpiarez($_POST["sex_user"])) && intval(limpiarez($_POST["sex_user"]))>=0 && intval(limpiarez($_POST["sex_user"]))<=2 ) ? abs(intval(limpiarez($_POST["sex_user"]))) : 0;
			$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? array_map(array($mysqli, 'real_escape_string'), $_POST["country"]) : false;
			$black_url = getHost($url);

			for($i=1; $i<=5; $i++) {
				$quest[$i] = (isset($_POST["quest$i"])) ? limitatexto(limpiarez($_POST["quest$i"]), 300) : false;

				for($y=1; $y<=3; $y++) {
					$answ[$i][$y] = (isset($_POST["answ$i$y"])) ? limitatexto(limpiarez($_POST["answ$i$y"]), 30) : false;
				}
			}

			if(is_array($country)) {
				foreach($country as $key => $val) {
					if(array_search($val, $geo_cod_arr)) {
						$id_country = array_search($val, $geo_cod_arr);
						$country_arr[] = $val;
					}
				}
			}
			$country = isset($country_arr) ? trim(strtoupper(implode(", ", $country_arr))) : false;

			if($quest[4]=="" | $answ[4][1]=="" | $answ[4][2]=="" | $answ[4][3]=="") {
				$quest[4] = ""; $answ[4][1] = ""; $answ[4][2] = ""; $answ[4][3] = "";
			}
			if($quest[5]=="" | $answ[5][1]=="" | $answ[5][2]=="" | $answ[5][3]=="") {
				$quest[5] = ""; $answ[5][1] = ""; $answ[5][2] = ""; $answ[5][3] = "";
			}
			if( ($quest[5]!="" && $answ[5][1]!="" && $answ[5][2]!="" && $answ[5][3]!="") && ($quest[4]=="" | $answ[4][1]=="" | $answ[4][2]=="" | $answ[4][3]=="") ) {
				$quest[4] = $quest[5]; $answ[4][1] = $answ[5][1]; $answ[4][2] = $answ[5][2]; $answ[4][3] = $answ[5][3];
				$quest[5] = ""; $answ[5][1] = ""; $answ[5][2] = ""; $answ[5][3] = "";
			}

			$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");

			if($title=="") {
				$message_text = "ERROR: Вы не указали заголовок теста!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($description=="") {
				$message_text = "ERROR: Вы не описали инструкцию к выполнению теста!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($sql_bl->num_rows>0 && $black_url!=false) {
				$row_bl = $sql_bl->fetch_array();
				$message_text = "ERROR: Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($url==false | $url=="http://" | $url=="https://") {
				$message_text = "ERROR: Вы не указали URL-адрес сайта!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
				$message_text = "ERROR: Вы неверно указали URL-адрес сайта!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($quest[1]=="") {
				$message_text = "ERROR: Вы не указали первый вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($answ[1][1]=="" | $answ[1][2]=="" | $answ[1][3]=="") {
				$message_text = "ERROR: Вы не указали варианты ответа на первый вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($quest[2]=="") {
				$message_text = "ERROR: Вы не указали второй вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($answ[2][1]=="" | $answ[2][2]=="" | $answ[2][3]=="") {
				$message_text = "ERROR: Вы не указали варианты ответа на второй вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($quest[3]=="") {
				$message_text = "ERROR: Вы не указали третий вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}elseif($answ[3][1]=="" | $answ[3][2]=="" | $answ[3][3]=="") {
				$message_text = "ERROR: Вы не указали варианты ответа на третий вопрос!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);

			}else{
				$summa_add = 0;
				if($quest[4]!="") $summa_add+= $tests_cena_quest;
				if($quest[5]!="") $summa_add+= $tests_cena_quest;

				$cena_user = ($tests_cena_hit + $summa_add) / (($tests_nacenka+100)/100);
				$cena_advs = ($tests_cena_hit + $summa_add + $tests_cena_revisit[$revisit] + $tests_cena_color * $color + $tests_cena_unic_ip[$unic_ip_user]);

				$cena_user = number_format($cena_user, 4, ".", "");
				$cena_advs = number_format($cena_advs, 4, ".", "");

				if($quest[4]=="") unset($quest[4], $answ[4]);
				if($quest[5]=="") unset($quest[5], $answ[5]);

				$questions = serialize($quest);
				$answers = serialize($answ);

				$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'");

				$mysqli->query("UPDATE `tb_ads_tests` SET 
					`date_edit`='".time()."',`title`='$title',`description`='$description',`url`='$url',`questions`='$questions',`answers`='$answers',
					`geo_targ`='$country',`revisit`='$revisit',`color`='$color',`date_reg_user`='$date_reg_user',
					`unic_ip_user`='$unic_ip_user',`sex_user`='$sex_user',`cena_user`='$cena_user',`cena_advs`='$cena_advs',`ip`='$laip' 
				WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

				if($revisit != $row["revisit"]) {
					$sql_v = $mysqli->query("SELECT `id` FROM `tb_ads_tests_visits` WHERE `ident`='$id' AND `time_next`>='".time()."'");
					if($sql_v->num_rows>0) {
						while($row_v = $sql_v->fetch_assoc()) {
							if($revisit==0) {
								$time_next = (1*24*60*60);
							}elseif($revisit==1) {
								$time_next = (3*24*60*60);
							}elseif($revisit==2) {
								$time_next = (7*24*60*60);
							}elseif($revisit==3) {
								$time_next = (14*24*60*60);
							}elseif($revisit==4) {
								$time_next = (30*24*60*60);
							}else{
								$time_next = (1*24*60*60);
							}

							$mysqli->query("UPDATE `tb_ads_tests_visits` SET `time_next`=`time_end`+'$time_next' WHERE `id`='".$row_v["id"]."'");
						}
					}
				}

				$message_text = "OK";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "FormAddMoney") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$message_text = 'Укажите сумму, которую вы хотите внести в бюджет рекламной площадки<br>(Минимум '.count_text($tests_min_pay, "рублей", "рубль", "рубля", "").')';
		$message_text.= '<input type="text" maxlength="10" id="money_add" value="100.00" class="payadv" onChange="AddMoney();" onKeyUp="AddMoney();" autocomplete="off" />';
		$message_text.= '<div align="center"><span onClick="AddMoney(\''.$row["id"].'\', \'tests\', \'AddMoney\');" class="sub-green" style="float:none;" title="Пополнить бюджет площадки">Пополнить</span></div>';
		$message_text.= '<div id="info-msg-addmoney" style="display: none"></div>';

		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "AddMoney") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$status = $row["status"];
		$cena_advs = $row["cena_advs"];
		$money = $row["money"];
		$balance = $row["balance"];
		$goods_out = $row["goods_out"];
		$bads_out = $row["bads_out"];
		$money_pay = ( isset($_POST["money_add"]) && preg_match( "|^[\d]*[\.,]?[\d]{0,2}$|", abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) ) ) ? abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) : false;

		if($money_pay<$tests_min_pay) {
			$message_text = "ERROR: Минимальная сумма пополнения - $tests_min_pay руб.";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			if($status=="0") {
				$mysqli->query("UPDATE `tb_ads_tests` SET `date_edit`='".time()."',`money`='$money_pay',`balance`='$money_pay' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
			}else{
				$mysqli->query("UPDATE `tb_ads_tests` SET `date_edit`='".time()."',`money`=`money`+'$money_pay',`balance`=`balance`+'$money_pay' WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
			}

			$money = number_format(($money+$money_pay), 2, ".", "`");
			$balance = ( isset($balance) && ($balance+$money_pay)<1 ) ? number_format(($balance+$money_pay), 4, ".", "") : number_format(($balance+$money_pay), 2, ".", "");
			$totals = number_format(floor(bcdiv($balance,$cena_advs)), 0, ".", "`");
			$message_text = "OK";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array(
				"result" => "OK", 
				"totals" => "$totals", 
				"money" => "$money", 
				"balance" => "".($balance < 1 ? number_format($balance, 4, ".", "`") : number_format($balance, 2, ".", "`"))."", 
				"goods_out" => "".number_format($goods_out, 0, ".", "`")."", 
				"bads_out" => "".number_format($bads_out, 0, ".", "`").""
				)) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "GoDel") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];
		$rek_name = $row["username"];
		$balance = $row["balance"];

		if($status==1) {
			$message_text = "Тест, прежде чем удалить, необходимо поставить на паузу!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			if($balance >= 1 && $rek_name != false) {
				$message_text = '<b>Как следует удалить тест?</b><br>';
				$message_text.= '<table id="newform" style="background:none; border:none; width:auto; margin:0 auto"><tr>';
					$message_text.= '<td align="center" style="background:none; border:none;"><input class="ok" type="checkbox" id="cashback" value="1" style="height:16px; width:16px; margin:0px;" autocomplete="off" /></td>';
					$message_text.= '<td align="center" style="background:none; border:none;"> - вернуть срадства на счет (минус '.$tests_comis_del.'%, комиссия за возврат средств при удалении)</td>';
				$message_text.= '</tr></table>';
				$message_text.= '<div align="center"><span onClick="DelCash(\''.$row["id"].'\', \'tests\', \'DelCash\');" class="sub-red" style="float:none;" title="Удалить тест">Удалить</span></div>';

				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}else{
				$message_text = '<b>Удалить тест?</b><br><br>';
				$message_text.= '<div align="center"><span onClick="DelCash(\''.$row["id"].'\', \'tests\', \'DelCash\');" class="sub-red" style="float:none;" title="Удалить тест">Удалить</span></div>';

				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}elseif($option == "DelCash") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];
		$rek_name = $row["username"];
		$balance = $row["balance"];
		$cashback = (isset($_POST["cashback"]) && preg_match("|^[\d]{1}$|", intval(limpiarez($_POST["cashback"])))) ? intval(limpiarez($_POST["cashback"])) : 0;

		if($status==1) {
			$message_text = "Тест, прежде чем удалить, необходимо поставить на паузу!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}elseif($status==2 | $status==3){
			if($cashback == 1 && $balance >= 1 && $rek_name != false) {

				$money_back = ($balance - $balance * ($tests_comis_del/100));
				$money_back = number_format($money_back, 2, ".", "");

				$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_rb`=`money_rb`+'$money_back'  WHERE `username`='$rek_name'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`status_pay`,`user`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
				VALUES('1','$rek_name','".DATE("d.m.Y H:i")."','".time()."','$money_back','Возврат средств с рекламной площадки (Тесты, ID:$id)', 'Зачислено', 'reklama')") or die(json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))));

				$message_text = "Рекламная площадка №$id успешно удалена.\nПользователю $rek_name возвращено $money_back руб.";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}else{
				$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

				$message_text = "Рекламная площадка №$id успешно удалена.";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}
		}else{
			$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `id`='$id'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $mysqli->error))) : $mysqli->error);

			$message_text = "OK";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}
	}else{
		$message_text = "ERROR: Тест не найден!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}

}else{
	$message_text = "ERROR: NO OPTION!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

?>