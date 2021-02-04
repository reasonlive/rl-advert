<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
require(DOC_ROOT."/advertise/func_load_banners.php");

function limpiarez($mensaje) {
	$mensaje = htmlspecialchars(trim($mensaje), NULL, "CP1251");
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

$type_banner_arr = array(
	"468x60" 	=> "(все страницы, в шапке сайта)", 
	"468x60_frm" 	=> "(во фрейме просмотра рекламы)", 
	"200x300" 	=> "(все страницы)", 
	"100x100" 	=> "(все страницы)",
	"728x90" 	=> "(главная страница)"
);

foreach ($type_banner_arr as $key => $val) {
	$sql_price = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='banner".$key."' AND `howmany`='1'");
	$cena_banner[$key] = $sql_price->num_rows>0 ? number_format($sql_price->fetch_object()->price, 2, ".", "") : false;
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
	$my_discount = 0;
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		echo '<span class="msg-error">Ошибка! Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		include('footer.php');
		exit();
	}else{
		$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_banner` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$plan = $row["plan"];
			$money_pay = $row["money"];
			$type_banner = $row["type"];
			$merch_tran_id = $row["merch_tran_id"];
			$start_cena = $row["start_cena"];

			$size_banner_arr = explode("_", $type_banner);
			$size_banner = $size_banner_arr[0];

			if($money_rb>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer_1!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer_1'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1', `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_banner` SET `status`='1', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) 
				VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы [баннер $size_banner], ID:#$id_pay','Списано','rashod')") or die($mysqli->error);

				stat_pay('banners', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 4);
				ActionRef(number_format($money_pay,2,".",""), $username);

				require_once(DOC_ROOT."/merchant/func_cache.php");
				cache_banners();

				if($size_banner!="728x90") PartnerSet($username, "p_b", $start_cena, $plan, $size_banner);

				echo '<span class="msg-ok">Ваш баннер успешно размещен!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
				include('footer.php');
				exit();
			}else{
				echo '<span class="msg-error">Ошибка! На вашем рекламном счете недостаточно средств для оплаты заказа!</span>';
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
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$urlbanner = (isset($_POST["urlbanner"])) ? limpiarez($_POST["urlbanner"]) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) >= 1 ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$type_banner = (isset($_POST["type_banner"]) && array_key_exists($_POST["type_banner"], $type_banner_arr) !== false) ? limpiarez($_POST["type_banner"]) : "";
	$laip = getRealIP();
	$black_url = getHost($url);
	$black_url_ban = getHost($url);

	$size_banner_arr = explode("_", $type_banner);
	$size_banner = $size_banner_arr[0];

	$wh = explode("x", $size_banner);
	$w = isset($wh["0"]) ? intval($wh["0"]) : false;
	$h = isset($wh["1"]) ? intval($wh["1"]) : false;

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	$sql_bl_ban = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_ban'");

	$sql_price = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='banner".$type_banner."' AND `howmany`='1'");
	$cena_banner = $sql_price->num_rows>0 ? number_format($sql_price->fetch_object()->price, 2, ".", "") : false;

	if(array_key_exists($type_banner, $type_banner_arr) === false | $type_banner === false) {
		echo '<span class="msg-error">Некорректно указан тип баннера!</span>';

	}elseif($cena_banner === false) {
		echo '<span class="msg-error">Не удалось определить стоимость баннера!</span>';

	}elseif($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row["cause"].'</span>';

	}elseif($sql_bl_ban->num_rows>0 && $black_url_ban!=false) {
		$row_ban = $sql_bl_ban->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row_ban["domen"].'/" target="_blank" style="color:#0000FF">'.$row_ban["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row_ban["cause"].'</span>';

	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на сайт!</span>';

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';

	}elseif($urlbanner==false | $urlbanner=="http://" | $urlbanner=="https://") {
		echo '<span class="msg-error">Не указана ссылка на баннер!</span>';

	}elseif((substr($urlbanner, 0, 7) != "http://" && substr($urlbanner, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на баннер!</span>';

	}elseif(is_url($url)!="true") {
		echo is_url($url);

	}elseif(is_url_img($urlbanner)!="true") {
		echo is_url_img($urlbanner);

	}elseif(is_img_size($w, $h, $urlbanner)!="true") {
		echo is_img_size($w, $h, $urlbanner);

	}elseif($plan==false && $plan<1) {
		echo '<span class="msg-error">Минимальный заказ - 1 день.</span><br>';

	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url).'</span>';
	}elseif(@getHost($urlbanner)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($urlbanner)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($urlbanner).'</span>';

	}elseif(img_get_save($urlbanner)!="true") {
		echo img_get_save($urlbanner);

	}else{
		$urlbanner_orig = $urlbanner;
		$urlbanner_load = img_get_save($urlbanner, 1);

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

		### Скидка рефералам ###
		if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"]) && isset($my_referer)) {
			if($my_referer!=false) {
				$sql_p = $mysqli->query("SELECT `p_b".$size_banner."`,`discount_partner` FROM `tb_users_partner` WHERE `username`='$my_referer' AND `discount_partner`>'0'");
				if($sql_p->num_rows>0) {
					$row_p = $sql_p->fetch_row();
					$ref_p_b = $row_p["0"];
					$discount_partner = $row_p["1"];
					$my_discount = p_floor(($ref_p_b * $discount_partner)/100, 2);
					$my_discount = number_format($my_discount,2,".","");
				}else{
					$my_discount = 0;
				}
			}else{
				$my_discount = 0;
			}
		}else{
			$my_discount = 0;
		}
		### Скидка рефералам ###

		$summa = round(($cena_banner * $plan),2);
		$start_cena = number_format($summa,2,".","");
		$summa = number_format(($summa * (100-($cab_skidka+$my_discount))/100),2,".","");

		$mysqli->query("DELETE FROM `tb_ads_banner` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_banner` WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_banner` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`type`='$type_banner',`plan`='$plan',`date`='".time()."',`date_end`='".(time()+$plan*24*60*60)."',`url`='$url',`urlbanner`='$urlbanner',`urlbanner_load`='$urlbanner_load',`ip`='$laip',`money`='$summa',`start_cena`='$start_cena' WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_banner` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`type`,`plan`,`date`,`date_end`,`url`,`urlbanner`,`urlbanner_load`,`ip`,`money`,`start_cena`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','$type_banner','$plan','".time()."','".(time()+$plan*24*60*60)."','$url','$urlbanner','$urlbanner_load','$laip','$summa','$start_cena')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_banner` WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;

		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="220"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>Баннер:</b></td><td>'.$size_banner.' '.(array_key_exists($type_banner, $type_banner_arr) ? $type_banner_arr[$type_banner] : false).'</td></tr>';
			echo '<tr><td><b>URL сайта:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>URL баннера:</b></td><td><a href="'.$urlbanner.'" target="_blank">'.$urlbanner.'</a></td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.'</td></tr>';
			if(isset($cab_text)) echo "$cab_text";
			if(isset($my_discount) && $my_discount>0) echo '<tr><td><b>Скидка от вашего реферера:</b></td><td>'.$my_discount.'%</td></tr>';
			echo '<tr><td><b>Способ оплаты:</b></td><td><b>'.$system_pay[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';
			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоиомсть заказа:</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';

			}else{
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
			//echo '<tr><td align="center" colspan="2"><a href="'.$url.'" target="_blank"><img src="'.$urlbanner.'" width="'.$w.'" height="'.$h.'" border="0" alt="" title="" /></td></tr>';
		echo '</table>';

		$shp_item = "8";
		$inv_desc = "Оплата рекламы: баннер $type_banner, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: banner $type_banner, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once(DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
	}

	include('footer.php');
	exit();
}

?>

<script type="text/javascript" language="JavaScript">
function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}
function SbmFormB() {
	var url = $.trim($("#url").val());
	var urlbanner = $.trim($("#urlbanner").val());
	var plan = $.trim($("#plan").val());

	if (url == '' | url == 'http://' | url == 'https://') {
		$("#url").focus().attr("class", "err");
		alert("Вы не указали URL-адрес сайта");
		return false;
	} else if (urlbanner == '' | urlbanner == 'http://' | urlbanner == 'https://') {
		$("#urlbanner").focus().attr("class", "err");
		alert("Вы не указали URL-адрес баннера");
		return false;
	} else if (plan == '' | plan < 1) {
		$("#plan").focus().attr("class", "err12");
		alert("Минимальное количество дней - 1");
		return false;
	} else {
		return true;
	}
}

function number_format(number, decimals, dec_point, thousands_sep) {
	var minus = "";
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	if(number < 0){
		minus = "-";
		number = number*-1;
	}
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function(n, prec) {
		var k = Math.pow(10, prec);
		return '' + (Math.round(n * k) / k).toFixed(prec);
	};
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return minus + s.join(dec);
}

function PlanChange(){
	var plan = $.trim($("#plan").val());
	var type_banner = $.trim($("#type_banner").val());

	if(type_banner=="468x60") {
		var price_one = <?=$cena_banner["468x60"];?>;
	}else if(type_banner=="468x60_frm") {
		var price_one = <?=$cena_banner["468x60_frm"];?>;
	}else if(type_banner=="200x300") {
		var price_one = <?=$cena_banner["200x300"];?>;
	}else if(type_banner=="100x100") {
		var price_one = <?=$cena_banner["100x100"];?>;
	}else if(type_banner=="728x90") {
		var price_one = <?=$cena_banner["728x90"];?>;
	}

	var price_all = plan * price_one;

	$("#price_one").html('<td align="left">Стоимость показа в сутки</td><td align="left"><span style="color:#228B22;">' + number_format(price_one, 2, '.', ' ') + ' руб.</span></td>');
	$("#price_all").html('<td align="left">Стоимость заказа</td><td align="left"><span style="color:#FF0000;">' + number_format(price_all, 2, '.', ' ') + ' руб.</span></td>');
	$("#prices").html('<span style="color:#FF0000;">' + price_all.toFixed(2) + ' руб.</span>');
		$("#prices1").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices2").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices3").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices4").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices5").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices6").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices7").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices8").html('<b style="color:#f6f9f6;">' + (price_all/60).toFixed(2) + '</b>');
		//gebi('prices9').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		$("#prices10").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		$("#prices11").html('<b style="color:#f6f9f6;">' + price_all.toFixed(2) + '</b>');
		
}

</script>

<?php

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:10px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:1px;">Баннерная реклама - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Главное преимущество баннерной рекламы на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; это максимально качественная аудитория. Пользователь не получает вознаграждения за клик по вашей рекламе. ';
		echo 'А если уже кликнул - значит ему ваш ресурс действительно интересен. Баннеры отображаются в ротаторе в случайном порядке. ';
		echo '<br>';
	echo '</div>';
echo '</div>';

echo '<form method="POST" action="" onSubmit="return SbmFormB(); return false;" id="newform">';
echo '<table class="tables">';
echo '<thead><tr><th width="220">Параметр</th><th>Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Баннер</b></td>';
		echo '<td align="left">';
			echo '<select id="type_banner" name="type_banner" onChange="PlanChange();" onClick="PlanChange();">';
				foreach ($type_banner_arr as $key => $val) {
					$size_banner_arr = explode("_", $key);
					$size_banner = $size_banner_arr[0];
					echo '<option value="'.$key.'">'.$size_banner.' '.$val.'</option>';
				}
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="url" name="url" maxlength="300" value="http://" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr>';
	//echo '<td align="left"><b>URL баннера / картинка на PC:</b><br /><span style="color:red;">(выбрать один из вариантов)</span></td>';
		echo '<td align="left"><b>URL баннера</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="urlbanner" name="urlbanner" maxlength="300" value="http://" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"><br />';
		//echo '<input type="file" id="openfile" style="display: none;>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Количество дней</td>';
		echo '<td align="left"><input type="text" id="plan" name="plan" maxlength="7" value="1" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');">&nbsp;&nbsp;&nbsp;(минимум 1)</td>';
	echo '</tr>';
	//echo '<tr>';
		//echo '<td align="left">Способ оплаты</td>';
		//echo '<td align="left">';
			//echo '<select name="method_pay" style="margin-bottom:1px;">';
			//require_once("".DOC_ROOT."/method_pay/method_pay_form.php");
			//echo '</select>';
		//echo '</td>';
	//echo '</tr>';
		echo '<tr id="price_one"></tr>';
	echo '<tr id="price_all"></tr>';
		
echo '</tbody>';
echo '</table>';
//echo '</div>';
		echo '<div class="blok" style="text-align:center;">';
	echo '<span id="adv-title3" class="adv-title-open" onclick="ShowHideBlock(3);">Выбрать способ оплаты</span>';
	echo '<div id="adv-block3" style="display:block;">';
	//	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
		echo '<button id="method_pay"  name="method_pay" value="10" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rs1">';
      echo '<div><div><div><span class="line-green"><span id="prices1"></span> руб.</span></div></div></div>';
	  echo '</div> </button>';
	//	}else{
			
//	}
    
    if($site_pay_wm!=1) {
        echo '<div class="cash-wm1">';
    	  echo '<div class="cash-wm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="1" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-wm1">';
    echo '<div><div><div><span class="line-green"><span id="prices2"></span> руб. (+0.8%)</span></div></div></div>';
	echo '</div> </button>';
	}
 
 if($site_pay_ym!=1) {
        echo '<div class="cash-yd1">';
    	  echo '<div class="cash-yd1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="8" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-yd1">';
    echo '<div><div><div><span class="line-green"><span id="prices3"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_robo!=1) {
        echo '<div class="cash-rb1">';
    	  echo '<div class="cash-rb1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="2" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rb1">';
    echo '<div><div><div><span class="line-green"><span id="prices4"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_mega!=1) {
    	  echo '<div class="cash-ik1">';
    	  echo '<div class="cash-ik1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="9" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ik1">';
    echo '<div><div><div><span class="line-green"><span id="prices5"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_qw!=1) {
    	  echo '<div class="cash-qw1">';
    	  echo '<div class="cash-qw1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="6" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-qw1">';
    echo '<div><div><div><span class="line-green"><span id="prices6"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_payeer!=1) {
    	  echo '<div class="cash-pr1">';
    	  echo '<div class="cash-pr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="5" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pr1">';
    echo '<div><div><div><span class="line-green"><span id="prices7"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_pm!=1) {
    	  echo '<div class="cash-pm1">';
    	  echo '<div class="cash-pm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
     echo '<button id="method_pay" name="method_pay" value="7" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pm1" >';
     echo '<div><div><div><span class="line-green"><span id="prices8"></span> USD</span></div></div></div>';
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
    echo '<div><div><div><span class="line-green"><span id="prices10">1</span> руб.</span></div></div></div>';
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
    echo '<div><div><div><span class="line-green"><span id="prices11">1</span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	 echo '</div>';
echo '</form>';

?>

<script language="JavaScript"> PlanChange();</script>