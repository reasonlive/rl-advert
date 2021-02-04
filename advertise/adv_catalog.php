<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
include_once(ROOT_DIR."/includes/tcy_pr.php");

function limpiarez($mensaje) {
	global $mysqli;
	$mensaje = trim($mensaje);
	$mensaje = str_replace("'", "", $mensaje);
	$mensaje = str_replace("`", "", $mensaje);
	$mensaje = str_replace('"', "&#34;", $mensaje);
	$mensaje = str_replace("?", "&#063;", $mensaje);
	$mensaje = str_replace("$", "&#036;", $mensaje);

	$mensaje = preg_replace("#([-0-9a-z_\.]+@[-0-9a-z_\.]+\.[a-z]{2,6})#i", "", $mensaje);
	$mensaje = preg_replace("'<script[^>]*?>.*?</script>'si", "", $mensaje);
	$mensaje = preg_replace("'<[^>]*?>.*?'si", "", $mensaje);

	$mensaje = $mysqli->real_escape_string(trim($mensaje));
	$mensaje = htmlspecialchars(trim($mensaje), NULL, "CP1251");

	$mensaje = str_replace("  ", " ", $mensaje);
	$mensaje = str_replace("&amp amp ", "&", $mensaje);
	$mensaje = str_replace("&amp;amp;", "&", $mensaje);
	$mensaje = str_replace("&&", "&", $mensaje);
	$mensaje = str_replace("http://http://", "http://", $mensaje);
	$mensaje = str_replace("https://https://", "https://", $mensaje);
	$mensaje = str_replace("&amp;", "&", $mensaje);
	$mensaje = str_replace("&#063;", "?", $mensaje);

	return $mensaje;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_cena' AND `howmany`='1'");
$catalog_cena = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_cena_color' AND `howmany`='1'");
$catalog_cena_color = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_min' AND `howmany`='1'");
$catalog_min = number_format($sql->fetch_object()->price, 0, ".", "");

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
		$id_pay = (isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"]))) ? intval(limpiar(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_assoc();
			$plan = $row["plan"];
			$color = $row["color"];
			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];

			if($money_user>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer_1!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer_1'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1', `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_catalog` SET `status`='1', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) 
				VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы [каталог сайтов], ID:#$id_pay','Списано','rashod')") or die($mysqli->error);

				stat_pay("catalog", $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				ActionRef(number_format($money_pay,2,".",""), $username);

				require_once(DOC_ROOT."/merchant/func_cache.php");
				cache_catalog();

				echo '<span class="msg-ok">Ваша ссылка успешно размещена в каталоге!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
				include('footer.php');
				exit();
			}else{
				echo '<span class="msg-error">На вашем рекламном счете недостаточно средств для оплаты заказа!</span>';
				include('footer.php');
				exit();
			}
		}else{
			echo '<span class="msg-error">Заказа рекламы с №'.$id_pay.' не существует, либо заказ уже был оплачен!</span>';
			include('footer.php');
			exit();
		}
	}
}



if(count($_POST)>0) {
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 30) : false;
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$color = (isset($_POST["color"]) && preg_match("|^[0-1]{1}$|", trim($_POST["color"])) ) ? intval(limpiarez(trim($_POST["color"]))) : 0;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$laip = getRealIP();
	$black_url = @getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");

	if($title==false) {
		echo '<span class="msg-error">Не заполнено поле заголовок ссылки.</span><br>';
	}elseif($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на сайт!</span>';
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
	}elseif(is_url($url)!="true") {
		echo is_url($url);
	}elseif($plan==false | $plan<$catalog_min) {
		echo '<span class="msg-error">Минимальное количество дней - '.$catalog_min.'.</span><br>';
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
		$system_pay[10] = "Рекламный счет";

		$color_to[0]="НЕТ";
		$color_to[1]="ДА (".number_format(($plan * $color * $catalog_cena_color),2,".","'")." руб.)";

		$summa = $plan * ($catalog_cena + $color * $catalog_cena_color);
		$summa = number_format(($summa * (100-$cab_skidka)/100),2,".","");

		$mysqli->query("DELETE FROM `tb_ads_catalog` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_tran_id = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tran_id->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_catalog` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_catalog` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`plan`='$plan',`date`='".time()."',`date_end`='".(time() + $plan*24*60*60)."',`color`='$color',`url`='$url',`title`='$title',`ip`='$laip',`money`='$summa' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_catalog` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`plan`,`date`,`date_end`,`color`,`url`,`title`,`ip`,`money`) 
			VALUES ('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','$plan','".time()."','".(time() + $plan*24*60*60)."','$color','$url','$title','$laip','$summa')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_catalog` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;
	
		echo '<span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="220"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>Заголовок ссылки:</b></td><td>'.$title.'</td></tr>';
			echo '<tr><td><b>URL ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.' ('.number_format(($plan * $catalog_cena),2,".","'").' руб.)</td></tr>';
			echo '<tr><td><b>Выделение цветом:</b></td><td>'.$color_to[$color].'</td></tr>';
			if(isset($cab_text)) echo "$cab_text";
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
		echo '</table>';

		$shp_item = "22";
		$inv_desc = "Оплата рекламы: ссылка в каталоге, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: catalog links, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once(ROOT_DIR."/method_pay/method_pay.php");
	}

	include('footer.php');
	exit();
}

?><script type="text/javascript" language="JavaScript"> 

function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}

function SbmFormB() {
	var title = $.trim($("#title").val());
	var url = $.trim($("#url").val());
	var plan = $.trim($("#plan").val());

	if (title == '') {
		$("#title").focus().attr("class", "err");
		alert("Вы не указали заголовок ссылки.");
		return false;
	} else if (url == '' | url == 'http://' | url == 'https://') {
		$("#url").focus().attr("class", "err");
		alert("Вы не указали URL-адрес сайта.");
		return false;
	} else if (plan == '' | plan < <?php echo $catalog_min;?>) {
		$("#plan").focus().attr("class", "err12");
		alert("Минимальное количество дней - <?php echo $catalog_min;?>.");
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
	var color = $.trim($("#color").val());
	var catalog_cena = <?php echo $catalog_cena;?>;
	var cena_color = <?php echo $catalog_cena_color;?>;
	var price_one = (catalog_cena + color * cena_color);
	var price_all = plan * price_one;

	$("#price_one").html('<td align="left">Стоимость показа в сутки</td><td align="left"><span style="color:#228B22;">' + number_format(price_one, 2, '.', ' ') + ' руб.</span></td>');
	$("#price_all").html('<td align="left">Стоимость заказа</td><td align="left"><span style="color:#FF0000;">' + number_format(price_all, 2, '.', ' ') + ' руб.</span></td>');
}

</script><?php

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:0px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Статическая ссылка в каталоге - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 5px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Основой продвижения вашего сайта в поисковых системах являются важные показатели <b>тИЦ</b> (Яндекс) и <b>PageRank</b> (Google). ';
		echo 'Лучшим способом наращивания этих показателей является размещение статической ссылки вашего ресурса на сайтах, которые уже имеют солидный показатель. ';
		echo 'Поэтому размещение вашей статической ссылки в нашем каталоге - идеальное решение! ';
		//echo 'На данный момент показатели <b style="color:#3A5FCD">'.strtoupper(array_shift(explode(".", $_SERVER["HTTP_HOST"]))).'</b> тИЦ = <b>'.(isset($_TCY) ? $_TCY : "0").'</b>, а PageRank = <b>'.(isset($_PR) ? $_PR : "0").'</b>.';
		echo 'На данный момент показатели <b style="color:#3A5FCD">'.strtoupper(array_shift(explode(".", $_SERVER["HTTP_HOST"]))).'</b> тИЦ = <b>'.(isset($_TCY) ? $_TCY : "0").'</b>.';
	echo '</div>';
echo '</div>';

echo '<a href="/catalog.php" class="book-title" target="_blank">Каталог сайтов<span>Перейти в каталог сайтов</span></a>';

echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
echo '<table class="tables">';
echo '<thead><tr><th width="200">Параметр</th><th>Значение</th></tr></thead>';
echo '<tbody>';
echo '<tr>';
	echo '<td align="left"><b>Заголовок ссылки</b></td>';
	echo '<td align="left"><input type="text" id="title" name="title" maxlength="30" value="" style="margin-bottom:1px;" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left"><b>URL сайта</b></td>';
	echo '<td align="left"><input type="text" id="url" name="url" maxlength="300" value="http://" style="margin-bottom:1px;" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left">Количество дней</td>';
	echo '<td align="left"><input type="text" name="plan" id="plan" maxlength="11" value="'.$catalog_min.'" class="ok12" style="margin:0; text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');">&nbsp;&nbsp;&nbsp;[минимум '.$catalog_min.']</td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left">Выделить цветом</td>';
	echo '<td align="left">';
		echo '<select name="color" id="color" style="margin-bottom:1px;" onChange="PlanChange();" onClick="PlanChange();">';
			echo '<option value="0">Нет</option>';
			echo '<option value="1">Да (+'.number_format($catalog_cena_color,2,".","'").' руб./сутки)</option>';
		echo '</select>';
	echo '</td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left">Способ оплаты</td>';
	echo '<td align="left">';
		echo '<select name="method_pay" style="margin-bottom:1px;">';
			require_once(ROOT_DIR."/method_pay/method_pay_form.php");
		echo '</select>';
	echo '</td>';
echo '</tr>';
echo '<tr id="price_one"></tr>';
echo '<tr id="price_all"></tr>';
echo '<tr>';
	echo '<td colspan="2" align="center"><input type="submit" value="Оформить заказ" class="sub-blue160" style="float:none;" /></td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>
<script language="JavaScript"> PlanChange();</script>