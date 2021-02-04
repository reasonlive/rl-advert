<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
?>

<script type="text/javascript">
function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}

function setChecked(type){
	var nodes = document.getElementsByTagName("input");
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == "country[]") {
			if(type == "paste") nodes[i].checked = true;
			else  nodes[i].checked = false;
		}
	}
}
</script>

<?php
function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("?","&#063;",$mensaje);
	$mensaje = str_replace(">","&#062;",$mensaje);
	$mensaje = str_replace("<","&#060;",$mensaje);
	$mensaje = str_replace("'","&#039;",$mensaje);
	$mensaje = str_replace("`","&#096;",$mensaje);
	$mensaje = str_replace("$","&#036;",$mensaje);
	$mensaje = str_replace('"',"&#034;",$mensaje);
	$mensaje = str_replace("  "," ",$mensaje);
	$mensaje = str_replace("&amp amp ","&",$mensaje);
	$mensaje = str_replace("&amp;amp;","&",$mensaje);
	$mensaje = str_replace("&&","&",$mensaje);
	$mensaje = str_replace("http://http://","http://",$mensaje);
	$mensaje = str_replace("https://https://","https://",$mensaje);
	$mensaje = str_replace("&#063;","?",$mensaje);
	return $mensaje;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_cena_hits_aserf' AND `howmany`='1'");
$cena_hits_aserf = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_nacenka_hits_aserf' AND `howmany`='1'");
$nacenka_hits_aserf = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_min_hits_aserf' AND `howmany`='1'");
$min_hits_aserf = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_timer_aserf_ot' AND `howmany`='1'");
$timer_aserf_ot = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_timer_aserf_do' AND `howmany`='1'");
$timer_aserf_do = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_cena_timer_aserf' AND `howmany`='1'");
$cena_timer_aserf = $sql->fetch_object()->price;

if(isset($_GET["editurl"])) {
	if(isset($_SESSION["checkurl"])) unset($_SESSION["checkurl"]);
	if(isset($_SESSION["checkurl_ok"])) unset($_SESSION["checkurl_ok"]);
}

$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;

if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
	$sql_user = $mysqli->query("SELECT `wmid`,`wm_purse`,`money_rb` FROM `tb_users` WHERE `username`='$username'");
	if($sql_user->num_rows>0) {
		$row_user = $sql_user->fetch_row();
		$wmid_user = $row_user["0"];
		$wmr_user = $row_user["1"];
		$money_user = $row_user["2"];
	}else{
		$username = false;
		$wmid_user = false;
		$wmr_user = false;
		$money_user = false;

		echo '<span class="msg-error">Пользователь не найден.</span><br>';
		include('footer.php');
		exit();
	}

}else{
	$username = false;
	$wmid_user = false;
	$wmr_user = false;
	$money_user = false;
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		echo '<span class="msg-error">Ошибка! Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		include('footer.php');
		exit();
	}else{
		$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_autoyoutube` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$plan = $row["plan"];
			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];

			if($money_user>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1', `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_autoyoutube` SET `status`='1', `date`='".time()."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы: автосерфинг YouTube - $plan шт.','Списано','rashod')") or die($mysqli->error);

				if(isset($_SESSION["checkurl"])) unset($_SESSION["checkurl"]);
				if(isset($_SESSION["checkurl_ok"])) unset($_SESSION["checkurl_ok"]);

				stat_pay('autoserfyou', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 2);

				echo '<span class="msg-ok">Ваша ролилик успешно размещен!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
				include('footer.php');
				exit();
			}else{
				echo '<span class="msg-error">Ошибка! На вашем рекламном счету недостаточно средств для оплаты заказа!</span>';
				include('footer.php');
				exit();
			}
		}else{
			echo '<span class="msg-error">Ошибка! Заказа рекламы с №'.$id_pay.' не существует, либо заказ уже был оплачен!</span>';
			include('footer.php');
			exit();
		}
	}
}


if(count($_POST)>0) {
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),80) : false;

	if(isset($_SESSION["checkurl"]) | isset($_SESSION["checkurl_ok"])) {
		$url = (isset($_SESSION["checkurl"])) ? limpiarez($_SESSION["checkurl"]) : false;
	}else{
		$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	}

	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) >= $min_hits_aserf ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$timer = ( isset($_POST["timer"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["timer"])) && intval(limpiarez(trim($_POST["timer"]))) >= $timer_aserf_ot  && intval(limpiarez(trim($_POST["timer"]))) <= $timer_aserf_do ) ? intval(limpiarez(trim($_POST["timer"]))) : false;
	$limits = ( isset($_POST["limits"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["limits"])) ) ? intval(limpiarez(trim($_POST["limits"]))) : false;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$laip = getRealIP();
	$black_url = @getHost($url);
	$p = explode("youtu.be/", $_POST["url"]);
	
	$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map(array($mysqli, 'real_escape_string'), $_POST["country"])) : false;
	//$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map('mysql_real_escape_string', $_POST["country"])) : false;
	$geo_cod_arr = array(
		1  => 'RU', 2  => 'UA', 3  => 'BY', 4  => 'MD', 
		5  => 'KZ', 6  => 'AM', 7  => 'UZ', 8  => 'LV', 
		9  => 'DE', 10 => 'GE',	11 => 'LT', 12 => 'FR', 
		13 => 'AZ', 14 => 'US', 15 => 'VN', 16 => 'PT', 
		17 => 'GB', 18 => 'BE', 19 => 'ES', 20 => 'CN', 
		21 => 'TJ', 22 => 'EE', 23 => 'IT', 24 => 'KG', 
		25 => 'IL', 26 => 'CA', 27 => 'TM', 28 => 'BG', 
		29 => 'IR', 30 => 'GR', 31 => 'TR', 32 => 'PL', 
		33 => 'FI', 34 => 'EG', 35 => 'SE', 36 => 'RO',
	);
	$geo_name_arr = array(
		1  => 'Россия', 	2  => 'Украина', 	3  => 'Белорусия', 	4  => 'Молдавия',
 		5  => 'Казахстан', 	6  => 'Армения', 	7  => 'Узбекистан', 	8  => 'Латвия',
		9  => 'Германия', 	10 => 'Грузия', 	11 => 'Литва', 		12 => 'Франция', 
		13 => 'Азербайджан', 	14 => 'США', 		15 => 'Вьетнам', 	16 => 'Португалия',
		17 => 'Англия', 	18 => 'Бельгия', 	19 => 'Испания', 	20 => 'Китай',
		21 => 'Таджикистан', 	22 => 'Эстония', 	23 => 'Италия', 	24 => 'Киргизия',
		25 => 'Израиль', 	26 => 'Канада', 	27 => 'Туркменистан', 	28 => 'Болгария',
		29 => 'Иран', 		30 => 'Греция', 	31 => 'Турция', 	32 => 'Польша',
		33 => 'Финляндия', 	34 => 'Египет', 	35 => 'Швеция', 	36 => 'Румыния',
	);
	if(is_array($country)) {
		foreach($country as $key => $val) {
			if(array_search($val, $geo_cod_arr)) {
				$id_country = array_search($val, $geo_cod_arr);
				$country_arr[] = $val;
				$country_arr_ru[] = $geo_name_arr[$id_country];
			}
		}
	}
	$country = isset($country_arr) ? trim(strtoupper(implode(', ', $country_arr))) : false;
	$country_to = isset($country_arr_ru) ? trim(strtoupper(implode(', ', $country_arr_ru))) : false;
	if($country_to!=false) {$country_to="$country_to";}else{$country_to="НЕТ";}

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	if($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на видеоролик youtube!</span>';
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на видеоролик youtube!</span>';
	}elseif(is_url($url)!="true") {
		echo is_url($url);
	}elseif(count($p)<='1') {
		echo '<span class="msg-error">Не верно указана ссылка на видеоролик youtube!</span>';
	/*}elseif(!isset($_SESSION["checkurl"]) | !isset($_SESSION["checkurl_ok"])) {
		$_SESSION["checkurl"] = $url; 
		echo '<script type="text/javascript"> top.document.location.href = "http://'.$_SERVER["HTTP_HOST"].'/check_url_as_you.php"; </script>';
		echo ' <noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL=http://'.$_SERVER["HTTP_HOST"].'/check_url_as_you.php"></noscript>';
		include('footer.php');
		exit();*/
	}elseif($description==false) {
		echo '<span class="msg-error">Не заполнено поле Описание видеоролика.</span><br>';
	}elseif($plan<$min_hits_aserf) {
		echo '<span class="msg-error">Минимальный заказ - '.$min_hits_aserf.' показов.</span><br>';
	}elseif($limits!=false && $limits<$min_hits_aserf) {
		echo '<span class="msg-error">Ограничение количества показов в сутки должно быть не менее '.$min_hits_aserf.' просмотров либо 0 - без ограничений.</span>';
	//}elseif($limits!=false && $limits>$plan) {
	//	echo '<span class="msg-error">Ограничение количества показов в сутки не может быть больше чем заказанное количество визитов ('.$plan.').</span><br></div></div>';
	}elseif($timer==false) {
		echo '<span class="msg-error">Время просмотра должно быть в пределах от '.$timer_aserf_ot.' сек. до '.$timer_aserf_do.' сек.</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url).'</span>';
	}else{
		$system_pay[1] = "WebMoney";
$system_pay[2] = "RoboKassa";
$system_pay[3] = "Wallet One";
$system_pay[4] = "InterKassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[9] = "MegaKassa";
$system_pay[20] = "FreeKassa";
$system_pay[21] = "AdvCash";
        $system_pay[10] = "Рекламный счет";
        

		if($limits>0) {
			if($limits>$plan) {
				$limits_table = $plan; $limits_text = "Без ограничений";
			}else{
				$limits_table = $limits; $limits_text = $limits;
			}
		}else{
			$limits_table = $plan; $limits_text = "Без ограничений";
		}

		if($timer>$timer_aserf_ot) {
			$timer_to = "$timer (".number_format(($plan * ($timer-$timer_aserf_ot) * ($cena_timer_aserf*(100+$nacenka_hits_aserf)/100)),2,".","")." руб.)";
		}else{
			$timer_to = "$timer (0.00 руб.)";
		}
		
		$p_time = explode("?", $p[1]);  
		$img_youtube='https://img.youtube.com/vi/'.$p_time[0].'/1.jpg';
	    $ytflag=1;

		$summa = ($plan * ( ($timer - $timer_aserf_ot) * $cena_timer_aserf + $cena_hits_aserf) * (100+$nacenka_hits_aserf)/100);
		$summa = round($summa,2);
		$summa = number_format(($summa * (100-$cab_skidka)/100),2,".","");

		$mysqli->query("DELETE FROM `tb_ads_autoyoutube` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+1 WHERE `id`='1'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_autoyoutube` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_autoyoutube` SET `img_youtube`='$img_youtube', `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username', `geo_targ`='$country', `timer`='$timer', `date`='".time()."', `plan`='$plan', `totals`='$plan', `limits`='$limits_table',`limits_now`='$limits_table', `url`='$url', `description`='$description', `check_url`='1', `ip`='$laip', `money`='$summa' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_autoyoutube` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`geo_targ`,`timer`,`date`,`plan`,`totals`,`limits`,`limits_now`,`members`,`url`,`description`,`check_url`,`claims`,`ip`,`money`, `ytflag`, `img_youtube`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','$country','$timer','".time()."','$plan','$plan','$limits_table','$limits_table','0','$url','$description','1','0','$laip','$summa','$ytflag', '$img_youtube')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_autoyoutube` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;

		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="150"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>URL видеоролика:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Описание видеоролика:</b></td><td><a href="'.$url.'" target="_blank">'.$description.'</a></td></tr>';
			echo '<tr><td><b>Количество просмотров:</b></td><td>'.$plan.' ('.number_format(($plan * $cena_hits_aserf * (100+$nacenka_hits_aserf)/100),2,".","").' руб.)</td></tr>';
			echo '<tr><td><b>Ограничение количества показов в сутки:</b></td><td>'.$limits_text.'</td></tr>';
			echo '<tr><td><b>Таймер, сек.:</b></td><td>'.$timer_to.'</td></tr>';
			echo '<tr><td><b>Геотаргетинг:</b></td><td>'.$country_to.'</td></tr>';
			echo "$cab_text";
			echo '<tr><td><b>Способ оплаты:</b></td><td><b>'.$system_pay[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			
			@require_once("".$_SERVER['DOCUMENT_ROOT']."/curs/curs.php");
				$money_add_usd = number_format(round(($summa/$CURS_USD),2),2,".","");
				
			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';
			
			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';
				
			}elseif($method_pay==7) {
						echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
						echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_usd,2,".","`").'</b> <b>USD</b></td></tr>';
			}else{
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
		echo '</table>';

		$shp_item = "24";
		$inv_desc = "Оплата рекламы: авто-серфинг YouTube, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: autoyoutube-serf, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once("".DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
	}
}else{
	?>

	<script type="text/javascript" language="JavaScript"> 
	
	function SetChecked(type){
	    var nodes = document.getElementsByTagName("input");
	    for (var i = 0; i < nodes.length; i++) {
		    if (nodes[i].name == "country[]") {
			    if(type == "paste") nodes[i].checked = true;
			    else  nodes[i].checked = false;
		    }
	    }
    }

	function gebi(id){
		return document.getElementById(id)
	}

	function SbmFormB() {
		arrayElem = document.forms["formzakaz"];
		var col=0;

		for (var i=0;i<arrayElem.length;i++){
			if ((document.forms["formzakaz"].url.value == '')|(document.forms["formzakaz"].url.value == 'http://')) {
				alert('Вы не указали URL-адрес видеоролика');
				arrayElem[i+0].style.background = "#FFDBDB";
				arrayElem[i+0].focus();
				return false;
			}else{
				arrayElem[i+0].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].description.value == '')|(document.forms["formzakaz"].description.value == 'http://')) {
				alert('Вы не указали Описание видеоролика');
				arrayElem[i+1].style.background = "#FFDBDB";
				arrayElem[i+1].focus();
				return false;
			}else{
				arrayElem[i+1].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].plan.value == '')|(document.forms["formzakaz"].plan.value < <?=$min_hits_aserf;?> )) {
				alert('Мнимальное количество визитов <?=$min_hits_aserf;?>');
				arrayElem[i+2].style.background = "#FFDBDB";
				arrayElem[i+2].focus();
				return false;
			}else{
				arrayElem[i+2].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].timer.value == '')|(document.forms["formzakaz"].timer.value < <?=$timer_aserf_ot;?> )|(document.forms["formzakaz"].timer.value > <?=$timer_aserf_do;?> )) {
				alert('Время просмотра должно быть в пределах от <?=$timer_aserf_ot;?> сек. до <?=$timer_aserf_do;?> сек.');
				arrayElem[i+3].style.background = "#FFDBDB";
				arrayElem[i+3].focus();
				return false;
			}else{
				arrayElem[i+3].style.background = "#FFFFFF";
			}
		}

		document.forms["formzakaz"].submit();
		return true;
	}

	function obsch(){
		var plan = gebi('plan').value;
		var timer = gebi('timer').value;

		if(timer<<?=$timer_aserf_ot;?>) { timer = <?=$timer_aserf_ot;?>}
		if(timer><?=$timer_aserf_do;?>) { timer = <?=$timer_aserf_do;?>}

		var cena_hits = <?php echo ($cena_hits_aserf*(100+$nacenka_hits_aserf)/100);?>;
		var cena_timer = <?php echo ($cena_timer_aserf*(100+$nacenka_hits_aserf)/100);?>;

		var price = ((cena_hits + (timer-<?=$timer_aserf_ot;?>) * cena_timer) * plan);

		gebi('pricet').innerHTML = '<b>Стоимость заказа:</b>';
		gebi('price').innerHTML = '<b style="color:#228B22;">' + price.toFixed(2) + ' руб.';
		gebi('price1').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price2').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price3').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price4').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price5').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price6').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price7').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price8').innerHTML = '<b style="color:#f6f9f6;">' + (price/60).toFixed(2) + '</b>';
		//gebi('price9').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price10').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('price11').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
	}
	</script>

	<?php

	echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:15px;">';
		echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Авто-серфинг <span style="color: #3F3F3F;">You</span><span style="border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;">Tube</span> - что это?</span>';
		echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Доступная, эффективная и недорогая реклама на <b>'.strtoupper($_SERVER["HTTP_HOST"]).'</b> — прекрасная возможность привлечения целевой аудитории на ваш интернет-ресурс. Тысячи потенциальных потребителей смогут в полной мере ознакомиться с вашей продукцией или услугами.';
	echo '</div>';
	
	echo '<span id="adv-title-rules" class="adv-title-close_1" onclick="ShowHideBlock(\'-rules\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Как правильно разместить видеоролик</span>';
	echo '<div id="adv-block-rules" style="display:block; padding:5px 7px 10px 7px; text-align:center; background-color:#FFFFFF;">';
		echo '<span class="warning-info" style="margin-bottom:0; font-weight:normal;">';
                        echo 'Для того что бы разместить свой видеоролик вам следует:<br>';
                        echo 'на сайте You Tube нажать на плеере с видео правую кнопку мыши в выпадающем меню<br>';
                        echo 'выбрать копировать ссылку на видео и вставить в поле URL видео!<br>';
		echo '</span>';
	//echo '<br/>';
	echo '<br><br>';

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';

		//if(isset($_SESSION["checkurl"]) | isset($_SESSION["checkurl_ok"])) {
			echo '<thead>';
				echo '<tr><th colspan="2" class="top">Форма заказа рекламы</th></tr>';
			echo '</thead>';

			echo '<tr>';
				echo '<td width="180"><b>URL видеоролика:</b></td>';
				echo '<td><input type="text" name="url" maxlength="160" value="https://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				//echo '<td><input type="text" name="url" maxlength="160" value="'.limpiarez($_SESSION["checkurl"]).'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';" disabled="disabled"><br><a href="'.$_SERVER["PHP_SELF"].'?ads='.$ads.'&editurl">Указать другую ссылку</a></td>';
			echo '</tr>';                                                                                                                                                                                         

			echo '<tr>';
				echo '<td><b>Описание видеоролика:</b></td>';
				echo '<td><input type="text" name="description" maxlength="80" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Количество просмотров:</b></td>';
				echo '<td><input type="text" name="plan" id="plan" maxlength="7" value="'.$min_hits_aserf.'" class="ok12" style="text-align:center;" onChange="obsch();" onKeyUp="obsch();" onkeydown="this.style.background=\'#FFFFFF\';">&nbsp;&nbsp;&nbsp;(минимум - '.$min_hits_aserf.')</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Время просмотра:</b></td>';
				echo '<td><input type="text" name="timer" id="timer" maxlength="3" value="'.$timer_aserf_ot.'" class="ok12" style="text-align:center;" onChange="obsch();" onKeyUp="obsch();" onkeydown="this.style.background=\'#FFFFFF\';">&nbsp;&nbsp;&nbsp;(от '.$timer_aserf_ot.' до '.$timer_aserf_do.' сек.)</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Ограничение количества показов в сутки:</b></td>';
				echo '<td><input type="text" name="limits" id="limits" maxlength="7" value="0" class="ok12" style="text-align:center;" onChange="obsch();" onKeyUp="obsch();" onkeydown="this.style.background=\'#FFFFFF\';">&nbsp;&nbsp;&nbsp;(0 - без ограничений)</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td id="pricet"></td>';
				echo '<td id="price"></td>';
			echo '</tr>';
			echo '<tr><td colspan=2><span id="adv-title2" class="adv-title-close" onclick="ShowHideBlock(2);">Настройки географического таргетинга</span></td></tr>';
	        echo '<tr><td colspan=2><div id="adv-block2" style="display:none;"><table class="tables">';
	        echo '<tbody>';
		        echo '<tr>';
			       echo '<td colspan="2" align="center" style="border-right:none;"><a id="paste" onclick="SetChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
			       echo '<td colspan="2" align="center" style="border-left:none;"><a onclick="SetChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
		        echo '</tr>';
		        include(DOC_ROOT."/advertise/func_geotarg.php");
				echo '</tbody>';
			echo '</table>';
			echo '</div>';
			echo '</div>';
			echo '<div class="blok" style="text-align:center;">';
	echo '<span id="adv-title3" class="adv-title-open" onclick="ShowHideBlock(3);">Выбрать способ оплаты</span>';
	echo '<div id="adv-block3" style="display:block;">';

		echo '<button id="method_pay"  name="method_pay" value="10" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rs1">';
      echo '<div><div><div><span class="line-green"><span id="price1"></span> руб.</span></div></div></div>';
	  echo '</div> </button>';
	
    if($site_pay_wm!=1) {
        echo '<div class="cash-wm1">';
    	  echo '<div class="cash-wm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="1" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-wm1">';
    echo '<div><div><div><span class="line-green"><span id="price2"></span> руб. (+0.8%)</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_ym!=1) {
        echo '<div class="cash-yd1">';
    	  echo '<div class="cash-yd1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="8" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-yd1">';
    echo '<div><div><div><span class="line-green"><span id="price3"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_robo!=1) {
        echo '<div class="cash-rb1">';
    	  echo '<div class="cash-rb1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="2" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rb1">';
    echo '<div><div><div><span class="line-green"><span id="price4"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
/*	
	if($site_pay_wo!=1) {
    	  echo '<div class="cash-wo1">';
    	  echo '<div class="cash-wo1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button name="method_pay" value="3" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-wo1">';
    echo '<div><div><div><span class="line-green"><span id="price9"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
*/	
	if($site_pay_mega!=1) {
    	  echo '<div class="cash-ik1">';
    	  echo '<div class="cash-ik1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="9" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ik1">';
    echo '<div><div><div><span class="line-green"><span id="price5"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
  
    if($site_pay_qw!=1) {
    	  echo '<div class="cash-qw1">';
    	  echo '<div class="cash-qw1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="6" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-qw1">';
    echo '<div><div><div><span class="line-green"><span id="price6"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_payeer!=1) {
    	  echo '<div class="cash-pr1">';
    	  echo '<div class="cash-pr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="5" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pr1">';
    echo '<div><div><div><span class="line-green"><span id="price7"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_pm!=1) {
    	  echo '<div class="cash-pm1">';
    	  echo '<div class="cash-pm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
     echo '<button id="method_pay" name="method_pay" value="7" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pm1" >';
     echo '<div><div><div><span class="line-green"><span id="price8"></span> USD</span></div></div></div>';
	 echo '</div> </button>';
	 
	}
	
	if($site_pay_free!=1) {
    	  echo '<div class="cash-fr1">';
    	  echo '<div class="cash-fr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
    	  echo '</div>';
	}else{
    echo '<button name="method_pay" value="20" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-fr1">';
    echo '<div><div><div><span class="line-green"><span id="price10"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	
	}
	
	if($site_pay_advcash!=1) {
    	  echo '<div class="cash-ah1">';
    	  echo '<div class="cash-ah1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
    	  echo '</div>';
	}else{
    echo '<button name="method_pay" value="21" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ah1">';
    echo '<div><div><div><span class="line-green"><span id="price11">1</span> руб.</span></div></div></div>';
	echo '</div> </button>';
	
	}
	
	echo '</div>';

		/*}else{
			echo '<thead>';
				echo '<tr><th colspan="3" class="top">Форма заказа рекламы</th></tr>';
			echo '</thead>';
echo '<table class="tables">';
			echo '<tr>';
				echo '<td width="180"><b>URL сайта (ссылка):</b></td>';
				echo '<td><input type="text" name="url" maxlength="160" value="http://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '<td width="160" align="right"><input type="submit" value="Проверить ссылку" class="sub-blue160" style="float:none;" /></td>';
			echo '</tr>';
		}*/
	echo '</table>';
	echo '</form>';
}

?>
<script language="JavaScript">obsch();</script>