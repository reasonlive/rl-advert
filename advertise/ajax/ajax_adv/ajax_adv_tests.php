<?php
if(!DEFINED("TESTS_AJAX")) {die ("Hacking attempt!");}
if($type_ads != "tests") {die ("Hacking attempt!");}
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

$sql_p = $mysqli->query("SELECT `sitewmr` FROM `tb_site` WHERE `id`='1'");
$site_wmr = $sql_p->fetch_object()->sitewmr;

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

$method_pay_to[1] = "WebMoney";
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
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `id`='$id' AND `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
		exit("OK");
	}else{
		exit("ERROR");
	}

}elseif($option == "add") {
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 55) : false;
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]), 2000) : false;
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	if (get_magic_quotes_gpc()) { $description = stripslashes($description); }
	$revisit = (isset($_POST["revisit"]) && (intval($_POST["revisit"])>=0 && intval($_POST["revisit"])<=4)) ? intval(limpiarez($_POST["revisit"])) : "0";
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(limpiarez($_POST["color"])) : "0";
	$unic_ip_user = (isset($_POST["unic_ip_user"]) && (intval($_POST["unic_ip_user"])>=0 && intval($_POST["unic_ip_user"])<=2)) ? intval($_POST["unic_ip_user"]) : "0";
	$date_reg_user = (isset($_POST["date_reg_user"]) && (intval($_POST["date_reg_user"])>=0 && intval($_POST["date_reg_user"])<=4)) ? intval($_POST["date_reg_user"]) : "0";
	$sex_user = ( isset($_POST["sex_user"]) && preg_match("|^[\d]{1,11}$|", limpiarez($_POST["sex_user"])) && intval(limpiarez($_POST["sex_user"]))>=0 && intval(limpiarez($_POST["sex_user"]))<=2 ) ? abs(intval(limpiarez($_POST["sex_user"]))) : 0;
	$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map(array($mysqli, 'real_escape_string'), $_POST["country"])) : false;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$black_url = getHost($url);

	//$money_add = ( isset($_POST["money_add"]) && preg_match( "|^[-+]?[\d]*[\.,]?[\d]*$|", abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) ) ) ? abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) : false;
	//$money_add = ( isset($_POST["money_add"]) && preg_match( "|^[-+]?[\d]*[\.,]?[\d]{0,2}$|", abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) ) ) ? abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) : false;
	  $money_add = ( isset($_POST["money_add"]) && preg_match( "|^[\d]*[\.,]?[\d]{0,2}$|", abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) ) ) ? abs(str_replace(",", ".", limpiarez($_POST["money_add"]))) : false;

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
		echo "ERROR"; exit("Вы не указали заголовок теста!");
	}elseif($description=="") {
		echo "ERROR"; exit("Вы не описали инструкцию к выполнению теста!");
	}elseif($sql_bl->num_rows>0 && $black_url!=false) {
		$row_bl = $sql_bl->fetch_array();
		echo "ERROR"; exit("Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."");
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo "ERROR"; exit("Вы не указали URL-адрес сайта!");
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo "ERROR"; exit("Вы неверно указали URL-адрес сайта!");
	}elseif($quest[1]=="") {
		echo "ERROR"; exit("Вы не указали первый вопрос!");
	}elseif($answ[1][1]=="" | $answ[1][2]=="" | $answ[1][3]=="") {
		echo "ERROR"; exit("Вы не указали варианты ответа на первый вопрос!");
	}elseif($quest[2]=="") {
		echo "ERROR"; exit("Вы не указали второй вопрос!");
	}elseif($answ[2][1]=="" | $answ[2][2]=="" | $answ[2][3]=="") {
		echo "ERROR"; exit("Вы не указали варианты ответа на второй вопрос!");
	}elseif($quest[3]=="") {
		echo "ERROR"; exit("Вы не указали третий вопрос!");
	}elseif($answ[3][1]=="" | $answ[3][2]=="" | $answ[3][3]=="") {
		echo "ERROR"; exit("Вы не указали варианты ответа на третий вопрос!");
	}elseif($money_add=="") {
		echo "ERROR"; exit("Cумма пополнения бюджета рекламной площадки введена не верно");
	}elseif($money_add<$tests_min_pay) {
		echo "ERROR"; exit("Минимальная сумма пополнения - ".number_format($tests_min_pay,2,".","")." руб.");
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		echo "ERROR"; exit(SFB_YANDEX($url));
	}else{
		$summa_dd = 0;
		if($quest[4]!="") $summa_dd+= $tests_cena_quest;
		if($quest[5]!="") $summa_dd+= $tests_cena_quest;

		$cena_user = ($tests_cena_hit + $summa_dd) / (($tests_nacenka+100)/100);
		$cena_advs = ($tests_cena_hit + $summa_dd + $tests_cena_revisit[$revisit] + $tests_cena_color * $color + $tests_cena_unic_ip[$unic_ip_user]);

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

		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die($mysqli->error);
		$mysqli->query("DELETE FROM `tb_ads_tests` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_tests` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($sql_check->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_tests` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`date`='".time()."',`date_edit`='".time()."',`title`='$title',`description`='$description',`url`='$url',`questions`='$questions',`answers`='$answers',`geo_targ`='$country',`revisit`='$revisit',`color`='$color',`date_reg_user`='$date_reg_user',`unic_ip_user`='$unic_ip_user',`sex_user`='$sex_user',`cena_user`='$cena_user',`cena_advs`='$cena_advs',`money`='$money_add',`balance`='0',`ip`='$laip' WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_tests`(`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_edit`,`title`,`description`,`url`,`questions`,`answers`,`geo_targ`,`revisit`,`color`,`date_reg_user`,`unic_ip_user`,`sex_user`,`cena_user`,`cena_advs`,`money`,`balance`,`ip`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','".time()."','".time()."','$title','$description','$url','$questions','$answers','$country','$revisit','$color','$date_reg_user','$unic_ip_user','$sex_user','$cena_user','$cena_advs','$money_add','0','$laip')") or die($mysqli->error);
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
			echo "ERROR"; exit("NO ID");
		}

		$description_to = new bbcode($description_to);
		$description_to = $description_to->get_html();
		$description_to = str_replace("&amp;", "&", $description_to);

		echo "OK";

		echo '<span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td align="left" width="190">Счет № (ID)</td><td align="left">'.number_format($merch_tran_id, 0,".", "").' ('.$id_zakaz.')</td></tr>';
			echo '<tr><td align="left">Заголовок теста</td><td align="left">'.$title.'</td></tr>';
			echo '<tr><td align="left">Инструкции для тестирования</td><td align="left">'.$description_to.'</td></tr>';
			echo '<tr><td align="left">URL сайта</td><td align="left"><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';

			for($i=1; $i<=count($quest); $i++){
				echo '<tr><td align="left">Вопрос №'.$i.'</td><td align="left">'.$quest[$i].'</td></tr>';
				echo '<tr>';
					echo '<td align="left">Варианты ответа</td>';
					echo '<td align="left">';
						for($y=1; $y<=3; $y++){
							echo '<span style="color: '.($y==1 ? "#009125;" : "#FF0000").'">'.$answ[$i][$y].'</span>'.($y!=3 ? "<br>" : "").'';
						}
					echo '</td>';
				echo '</tr>';
			}

			echo '<tr><td align="left">Технология тестирования</td><td align="left">'.$revisit_tab[$revisit].'</td></tr>';
			echo '<tr><td align="left">Выделить тест</td><td align="left">'.$color_tab[$color].'</td></tr>';
			echo '<tr><td align="left">Уникальный IP</td><td align="left">'.$unic_ip_user_tab[$unic_ip_user].'</td></tr>';
			echo '<tr><td align="left">По дате регистрации</td><td align="left">'.$date_reg_user_tab[$date_reg_user].'</td></tr>';
			echo '<tr><td align="left">По половому признаку</td><td align="left">'.$sex_user_tab[$sex_user].'</td></tr>';

			echo '<tr>';
				echo '<td align="left">Геотаргетинг</td>';
				echo '<td>';
					if(count($geo_targ)>0) {
						for($i=0; $i<count($geo_targ); $i++){
							echo '<img src="//'.$_SERVER["HTTP_HOST"].'/img/flags/'.strtolower($geo_targ[$i]).'.gif" alt="'.$geo_name_arr_ru[strtoupper($geo_targ[$i])].'" title="'.$geo_name_arr_ru[strtoupper($geo_targ[$i])].'" align="absmiddle" style="margin:0; padding:0; padding-left:1px;" /> ';
						}
					}else{
						echo 'все страны';
					}
				echo '</td>';
			echo '</tr>';

			echo '<tr><td align="left">Количество выполнений</td><td align="left">'.$count_tests.'</td></tr>';
			//if(isset($cab_text)) echo "$cab_text";
			echo '<tr><td align="left">Способ оплаты</td><td align="left"><b>'.$method_pay_to[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';

			if($method_pay==8) {
				if(($money_add*0.005)<0.01) {$money_add_ym = $money_add + 0.01;}else{$money_add_ym = number_format(($money_add*1.005),2,".","");}

				echo '<tr><td>Стоимость заказа:</td><td><b style="color:green;">'.number_format($money_add,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td>Сумма к оплате</td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';
			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($money_add * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоиомсть заказа</b></td><td><b style="color:#76B15D;">'.number_format($money_add,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';

			}else{
				echo '<tr><td>Сумма к оплате</td><td><b style="color:#FF0000;">'.number_format($money_add,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
		echo '</table>';

		$shp_item = "21";
		$inv_desc = "Оплата рекламы: тесты, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: tests, order:$merch_tran_id";
		$money_add = number_format($money_add,2,".","");

		echo '<table class="tables" cellspacing="0" cellpadding="0">';
		echo '<tr align="center">';
			echo '<td align="left" width="160" style="border-right:none;">';
				if($method_pay==10 && $username!=false) {
					echo '<span onClick="PayAds(\''.$id_zakaz.'\');" class="sub-blue160" style="float:left;">Оплатить</span>';
				}elseif($method_pay==10 && $username==false) {
					echo '';
				}else{
					require_once(ROOT_DIR."/method_pay/method_pay.php");
				}
			echo '</td>';
			echo '<td align="center" style="border-left:none;">';
				echo '<span onClick="DeleteAds(\''.$id_zakaz.'\');" class="sub-red" style="float:right;">Удалить</span>';
				echo '<span onClick="ChangeAds();" class="sub-green" style="float:right;">Изменить</span>';
			echo '</td>';
		echo '</tr>';
		echo '</table>';

		if($method_pay==10 && $username==false) {
			echo '<span class="msg-error">Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		}

		if($method_pay==10 && $username!=false) {
			?><script type="text/javascript" language="JavaScript">
			function PayAds(id) {
				$.ajax({
					type: "POST", url: "/advertise/ajax/ajax_adv_add.php?rnd="+Math.random(), 
					data: {'op':'pay', 'type':'tests', 'id':id}, 
					beforeSend: function() { $("#loading").slideToggle(); }, 
					success: function(data) {
						$("#loading").slideToggle();
						if (data == "OK") {
							$("html, body").animate({scrollTop:0}, 700);

							gebi("OrderForm").style.display = "";
							gebi("OrderForm").innerHTML = '<span class="msg-ok">Ваш тест успешно размещен!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
							setTimeout(function() {document.location.href = "/cabinet_ads?ads=tests";}, 2000); clearTimeout();
							return false;
						}else{
							gebi("info-msg-pay").style.display = "";
							gebi("info-msg-pay").innerHTML = '<span class="msg-error">' + data + '</span>';
							setTimeout(function() {$("#info-msg-pay").fadeOut("slow")}, 5000); clearTimeout();
							return false;
						}
					}
				});
			}
			</script><?php
		}

		exit();	
	}

}elseif($option == "pay") {
	if($username==false) {
		exit("Для оплаты с рекламного счета необходимо авторизоваться!");
	}else{
		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_tests` WHERE `id`='$id' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];

			if($money_user_rb>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer_1!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer_1'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1',`money_rb`=`money_rb`-'$money_pay',`money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1',`date`='".time()."',`wmid`='$wmid_user',`money`='$money_pay',`balance`='$money_pay' WHERE `id`='$id' AND `status`='0' AND `username`='$username'  ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`, `date`, `amount`, `method`, `status`, `tipo`) 
				VALUES('$username', '".DATE("d.m.Y H:i")."', '$money_pay', 'Пополнение баланса рекламной площадки (Тесты, ID:$id)', 'Оплачено', 'reklama')") or die($mysqli->error);

				stat_pay("tests", $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);

				exit("OK");

			}else{
				exit("На вашем рекламном счету недостаточно средств для оплаты рекламы!");
			}
		}else{
			exit("Заказа рекламы с №$id не существует, либо заказ уже был оплачен!");
		}
	}
}else{
	exit("ERROR NO OPTION!");
}

?>