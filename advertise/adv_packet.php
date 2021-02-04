<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
require(DOC_ROOT."/advertise/func_load_banners.php");

?><script type="text/javascript">
function ShowHideBlock(id) {
	if (gebi("adv-title"+id).className == 'adv-title-open') {
		gebi("adv-title"+id).className = 'adv-title-close';
	} else {
		gebi("adv-title"+id).className = 'adv-title-open';
	}
	$("#adv-block"+id).slideToggle("fast");
}
</script><?php

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

$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;

if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
	$sql_user = $mysqli->query("SELECT `wmid`,`wm_purse`,`money_rb` FROM `tb_users` WHERE `username`='$username'");
	if($sql_user->num_rows>0) {
		$row_user = $sql_user->fetch_assoc();
		$wmid_user = $row_user["wmid"];
		$wmr_user = $row_user["wm_purse"];
		$money_user_rb = $row_user["money_rb"];
	}else{
		$username = false;
		$wmid_user = false;
		$wmr_user = false;
		$money_user_rb = false;

		echo '<span class="msg-error">Пользователь не найден.</span><br>';
		include('footer.php');
		exit();
	}
	
	### Скидка рефералам ###
	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"]) && isset($my_referer)) {
		if($my_referer!=false) {
			$sql_p = $mysqli->query("SELECT `p_packet_1`,`p_packet_2`,`p_packet_3`,`p_packet_4`,`p_packet_5`,`discount_partner` FROM `tb_users_partner` WHERE `username`='$my_referer' AND `discount_partner`>'0'");
			if($sql_p->num_rows>0) {
				$row_p = $sql_p->fetch_array();
				$discount_partner = $row_p["discount_partner"];

				for($i=1; $i<=5; $i++) {
					$p_packet_ref[$i] = $row_p["p_packet_".$i];
					$my_discount[$i] = p_floor(($p_packet_ref[$i] * $discount_partner)/100, 2);
					$my_discount[$i] = number_format($my_discount[$i],2,".","");
				}
			}else{
				$my_discount[1] = 0;
				$my_discount[2] = 0;
				$my_discount[3] = 0;
				$my_discount[4] = 0;
				$my_discount[5] = 0;
			}
		}else{
			$my_discount[1] = 0;
			$my_discount[2] = 0;
			$my_discount[3] = 0;
			$my_discount[4] = 0;
			$my_discount[5] = 0;
		}
	}else{
		$my_discount[1] = 0;
		$my_discount[2] = 0;
		$my_discount[3] = 0;
		$my_discount[4] = 0;
		$my_discount[5] = 0;
	}
	### Скидка рефералам ###

}else{
	$username = false;
	$wmid_user = false;
	$wmr_user = false;
	$money_user_rb = false;
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		echo '<span class="msg-error">Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		include('footer.php');
		exit();
	}else{
		$id_pay = (isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"]))) ? intval(limpiar(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_packet` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();

			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];
			$packet = $row["packet"];
			$ip = $row["ip"];
			$method_pay = "-1";

			if($money_user_rb >= $money_pay) {

				$sql_p = $mysqli->query("SELECT * FROM `tb_config_packet` WHERE `packet`='$packet'");
				if($sql_p->num_rows>0) {
					$row_p = $sql_p->fetch_array();
					$ds_plan = $row_p["ds_plan"];
					$ds_timer = $row_p["ds_timer"];
					$slink_plan = $row_p["slink_plan"];
					$sban468_plan = $row_p["sban468_plan"];
					$sban100_plan = $row_p["sban100_plan"];
					$sban200_plan = $row_p["sban200_plan"];
					$txtob_plan = $row_p["txtob_plan"];
					$psdlink_plan = $row_p["psdlink_plan"];
					$frm_plan = $row_p["frm_plan"];
				}else{
					echo '<span class="msg-error">Ошибка! Такого пакета рекламы нет!</span>';
					include('footer.php');
					exit();
				}

				$ds_url = $row["ds_url"];
				$ds_title = $row["ds_title"];
				$ds_text = $row["ds_text"];

				$slink_url = $row["slink_url"];
				$slink_text = $row["slink_text"];

				$sban468_url = $row["sban468_url"];
				$sban468_urlban = $row["sban468_urlban"];
				$sban468_urlban_load = $row["sban468_urlban_load"];

				$sban100_url = $row["sban100_url"];
				$sban100_urlban = $row["sban100_urlban"];
				$sban100_urlban_load = $row["sban100_urlban_load"];

				$sban200_url = $row["sban200_url"];
				$sban200_urlban = $row["sban200_urlban"];
				$sban200_urlban_load = $row["sban200_urlban_load"];

				$txtob_url = $row["txtob_url"];
				$txtob_text = $row["txtob_text"];

				$psdlink_url = $row["psdlink_url"];
				$psdlink_text = $row["psdlink_text"];

				$frm_url = $row["frm_url"];
				$frm_text = $row["frm_text"];

				$mysqli->query("INSERT INTO `tb_ads_dlink` (`status`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`type_serf`,`active`,`color`,`timer`,`plan`,`url`,`title`,`description`,`totals`,`ip`) 
				VALUES('1','$merch_tran_id','$method_pay','$wmid_user','$username','".time()."','1','0','0','$ds_timer','$ds_plan','$ds_url','$ds_title','$ds_text','$ds_plan','$ip')") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_ads_slink` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `plan`, `date`, `date_end`, `url`, `description`,`ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '$slink_plan', '".time()."', '".(time()+$slink_plan*24*60*60)."', '$slink_url', '$slink_text', '$ip')") or die($mysqli->error);


				$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`, `urlbanner_load`, `ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '468x60', '$sban468_plan', '".time()."', '".(time()+$sban468_plan*24*60*60)."', '$sban468_url', '$sban468_urlban', '$sban468_urlban_load', '$ip')") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`, `urlbanner_load`, `ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '100x100', '$sban100_plan', '".time()."', '".(time()+$sban100_plan*24*60*60)."', '$sban100_url', '$sban100_urlban', '$sban100_urlban_load', '$ip')") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`, `urlbanner_load`, `ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '200x300', '$sban200_plan', '".time()."', '".(time()+$sban200_plan*24*60*60)."', '$sban200_url', '$sban200_urlban', '$sban200_urlban_load', '$ip')") or die($mysqli->error);


				$mysqli->query("INSERT INTO `tb_ads_txt` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `plan`, `date`, `date_end`, `url`, `description`,`ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username','$txtob_plan', '".time()."', '".(time()+$txtob_plan*24*60*60)."', '$txtob_url', '$txtob_text', '$ip')") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_ads_psevdo` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`,`plan`, `date`, `date_end`, `url`, `description`,`ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username','$psdlink_plan', '".time()."', '".(time()+$psdlink_plan*24*60*60)."', '$psdlink_url', '$psdlink_text', '$ip')") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_ads_frm` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`,`plan`, `date`, `date_end`, `url`, `description`,`ip`) 
				VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username','$frm_plan', '".time()."', '".(time()+$frm_plan*24*60*60)."', '$frm_url', '$frm_text', '$ip')") or die($mysqli->error);

				$mysqli->query("DELETE FROM `tb_ads_packet` WHERE `id`='$id_pay'");
				$mysqli->query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);

				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) 
				VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата пакета рекламы №$packet','Списано','rashod')") or die($mysqli->error);

				stat_pay('packet', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 1);

				require_once(DOC_ROOT."/merchant/func_cache.php");
				cache_stat_links();
				cache_frm_links();
				cache_txt_links();
				cache_rek_cep();
				cache_banners();

				echo '<span class="msg-ok">Ваша реклама успешно размещена!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
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
	$packet = (isset($_GET["p"])) ? abs(intval(limpiar($_GET["p"]))) : false;
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$laip = getRealIP();

	//Динамическая ссылка
	$url_ds = (isset($_POST["url_ds"])) ? limitatexto(limpiarez($_POST["url_ds"]),300) : false;
	$title_ds = (isset($_POST["title_ds"])) ? limitatexto(limpiarez($_POST["title_ds"]),60) : false;
	$text_ds = (isset($_POST["text_ds"])) ? limitatexto(limpiarez($_POST["text_ds"]),80) : false;
	$black_url_ds = @getHost($url_ds);

	//Статическая ссылка
	$url_slink = (isset($_POST["url_slink"])) ? limpiarez($_POST["url_slink"]) : false;
	$text_slink = (isset($_POST["text_slink"])) ? limpiarez($_POST["text_slink"]) : false;
	$block_slink = (isset($_POST["tarif_slink"])) ? intval(limpiarez($_POST["tarif_slink"])) : false;
	$black_url_slink = @getHost($url_slink);

	//Статический баннер 468x60
	$url_sban468 = (isset($_POST["url_sban468"])) ? limpiarez($_POST["url_sban468"]) : false;
	$urlbanner_sban468 = (isset($_POST["urlbanner_sban468"])) ? limpiarez($_POST["urlbanner_sban468"]) : false;
	$black_url_sban468 = @getHost($url_sban468);
	$black_urlbanner_sban468 = @getHost($urlbanner_sban468);

	//Статический баннер 100x100
	$url_sban100 = (isset($_POST["url_sban100"])) ? limpiarez($_POST["url_sban100"]) : false;
	$urlbanner_sban100 = (isset($_POST["urlbanner_sban100"])) ? limpiarez($_POST["urlbanner_sban100"]) : false;
	$black_url_sban100 = @getHost($url_sban100);
	$black_urlbanner_sban100 = @getHost($urlbanner_sban100);

	//Статический баннер 200x300
	$url_sban200 = (isset($_POST["url_sban200"])) ? limpiarez($_POST["url_sban200"]) : false;
	$urlbanner_sban200 = (isset($_POST["urlbanner_sban200"])) ? limpiarez($_POST["urlbanner_sban200"]) : false;
	$black_url_sban200 = @getHost($url_sban200);
	$black_urlbanner_sban200 = @getHost($urlbanner_sban200);

	//Текстовое объявления
	$url_txtob = (isset($_POST["url_txtob"])) ? limpiarez($_POST["url_txtob"]) : false;
	$text_txtob = (isset($_POST["text_txtob"])) ? limpiarez($_POST["text_txtob"]) : false;
	$black_url_txtob = @getHost($url_txtob);

	//Псевдо-динамическая ссылка
	$url_psdlink = (isset($_POST["url_psdlink"])) ? limpiarez($_POST["url_psdlink"]) : false;
	$text_psdlink = (isset($_POST["text_psdlink"])) ? limpiarez($_POST["text_psdlink"]) : false;
	$black_url_psdlink = @getHost($url_psdlink);

	//Фрейм
	$url_frm = (isset($_POST["url_frm"])) ? limpiarez($_POST["url_frm"]) : false;
	$text_frm = (isset($_POST["text_frm"])) ? limpiarez($_POST["text_frm"]) : false;
	$black_url_frm = @getHost($url_frm);

	$sql_bl_ds = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_ds'");
	$sql_bl_sl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_slink'");

	$sql_bl_u_468 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_sban468'");
	$sql_bl_ub_468 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_urlbanner_sban468'");

	$sql_bl_u_100 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_sban100'");
	$sql_bl_ub_100 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_urlbanner_sban100'");

	$sql_bl_u_200 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_sban200'");
	$sql_bl_ub_200 = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_urlbanner_sban200'");

	$sql_bl_txt = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_txtob'");
	$sql_bl_psd = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_psdlink'");
	$sql_bl_frm = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_frm'");


	if($sql_bl_ds->num_rows>0 && $black_url_ds!=false) {
		$row = $sql_bl_ds->fetch_assoc();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_ds==false | $url_ds=="http://" | $url_ds=="https://" | ((substr($url_ds, 0, 7) != "http://" && substr($url_ds, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт для динамической ссылки!</span>';
	}elseif(is_url($url_ds)!="true") {
		echo is_url($url_ds);
	}elseif($title_ds==false) {
		echo '<span class="msg-error">Не заполнено поле заголовок динамической ссылки.</span><br>';
	}elseif($text_ds==false) {
		echo '<span class="msg-error">Не заполнено поле описание динамической ссылки.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_ds)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_ds).'</span>';


	}elseif($sql_bl_psd->num_rows>0 && $black_url_psdlink!=false) {
		$row = $sql_bl_psd->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_psdlink==false | $url_psdlink=="http://" | $url_psdlink=="https://" | ((substr($url_psdlink, 0, 7) != "http://" && substr($url_psdlink, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт псевдо-динамической ссылки!</span>';
	}elseif(is_url($url_psdlink)!="true") {
		echo is_url($url_psdlink);
	}elseif($text_psdlink==false) {
		echo '<span class="msg-error">Не заполнено поле Описание для псевдо-динамической ссылки.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_psdlink)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_psdlink).'</span>';


	}elseif($sql_bl_sl->num_rows>0 && $black_url_slink!=false) {
		$row = $sql_bl_sl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_slink==false | $url_slink=="http://" | $url_slink=="https://" | ((substr($url_slink, 0, 7) != "http://" && substr($url_slink, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт статической ссылки!</span>';
	}elseif(is_url($url_slink)!="true") {
		echo is_url($url_slink);
	}elseif($text_slink==false) {
		echo '<span class="msg-error">Не заполнено поле Описание статической ссылки.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_slink)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_slink).'</span>';


	}elseif($sql_bl_frm->num_rows>0 && $black_url_frm!=false) {
		$row = $sql_bl_frm->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_frm==false | $url_frm=="http://" | $url_frm=="https://" | ((substr($url_frm, 0, 7) != "http://" && substr($url_frm, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт для ссылки во фрейме!</span>';
	}elseif(is_url($url_frm)!="true") {
		echo is_url($url_frm);
	}elseif($text_frm==false) {
		echo '<span class="msg-error">Не заполнено поле Описание для для ссылки во фрейме.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_frm)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_frm).'</span>';


	}elseif($sql_bl_txt->num_rows>0 && $black_url_txtob!=false) {
		$row = $sql_bl_txt->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_txtob==false | $url_txtob=="http://" | $url_txtob=="https://" | ((substr($url_txtob, 0, 7) != "http://" && substr($url_txtob, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт для текстового объявления!</span>';
	}elseif(is_url($url_txtob)!="true") {
		echo is_url($url_txtob);
	}elseif($text_txtob==false) {
		echo '<span class="msg-error">Не заполнено поле Описание для текстового объявления.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_txtob)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_txtob).'</span>';


	}elseif($sql_bl_u_468->num_rows>0 && $black_url_sban468!=false) {
		$row = $sql_bl_u_468->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_sban468==false | $url_sban468=="http://" | $url_sban468=="https://" | ((substr($url_sban468, 0, 7) != "http://" && substr($url_sban468, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL ссылки для баннера 468x60!</span>';
	}elseif(is_url($url_sban468)!="true") {
		echo is_url($url_sban468);
	}elseif($sql_bl_ub_468->num_rows>0 && $black_urlbanner_sban468!=false) {
		$row_ban = $sql_bl_ub_468->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row_ban["domen"].'/" target="_blank" style="color:#0000FF">'.$row_ban["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row_ban["cause"].'</span>';
	}elseif($urlbanner_sban468==false | $urlbanner_sban468=="http://" | $urlbanner_sban468=="https://" | ((substr($urlbanner_sban468, 0, 7) != "http://" && substr($urlbanner_sban468, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL баннера 468x60!</span>';
	}elseif(is_url_img($urlbanner_sban468)!="true") {
		echo is_url_img($urlbanner_sban468);
	}elseif(is_img_size('468','60',$urlbanner_sban468)!="true") {
		echo is_img_size('468','60',$urlbanner_sban468);
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_sban468)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_sban468).'</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($urlbanner_sban468)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($urlbanner_sban468).'</span>';
	}elseif(img_get_save($urlbanner_sban468)!="true") {
		echo img_get_save($urlbanner_sban468);



	}elseif($sql_bl_u_100->num_rows>0 && $black_url_sban100!=false) {
		$row = $sql_bl_u_100->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_sban100==false | $url_sban100=="http://" | $url_sban100=="https://" | ((substr($url_sban100, 0, 7) != "http://" && substr($url_sban100, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL ссылки для баннера 100x100!</span>';
	}elseif(is_url($url_sban100)!="true") {
		echo is_url($url_sban100);
	}elseif($sql_bl_ub_100->num_rows>0 && $black_urlbanner_sban100!=false) {
		$row_ban = $sql_bl_ub_100->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row_ban["domen"].'/" target="_blank" style="color:#0000FF">'.$row_ban["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row_ban["cause"].'</span>';
	}elseif($urlbanner_sban100==false | $urlbanner_sban100=="http://" | $urlbanner_sban100=="https://" | ((substr($urlbanner_sban100, 0, 7) != "http://" && substr($urlbanner_sban100, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL баннера 100x100!</span>';
	}elseif(is_url_img($urlbanner_sban100)!="true") {
		echo is_url_img($urlbanner_sban100);
	}elseif(is_img_size('100','100',$urlbanner_sban100)!="true") {
		echo is_img_size('100','100',$urlbanner_sban100);
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_sban100)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_sban100).'</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($urlbanner_sban100)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($urlbanner_sban100).'</span>';
	}elseif(img_get_save($urlbanner_sban100)!="true") {
		echo img_get_save($urlbanner_sban100);


	}elseif($sql_bl_u_200->num_rows>0 && $black_url_sban200!=false) {
		$row = $sql_bl_u_200->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url_sban200==false | $url_sban200=="http://" | $url_sban200=="https://" | ((substr($url_sban200, 0, 7) != "http://" && substr($url_sban200, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL ссылки для баннера 200x300!</span>';
	}elseif(is_url($url_sban200)!="true") {
		echo is_url($url_sban200);
	}elseif($sql_bl_ub_200->num_rows>0 && $black_urlbanner_sban200!=false) {
		$row_ban = $sql_bl_ub_200->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row_ban["domen"].'/" target="_blank" style="color:#0000FF">'.$row_ban["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row_ban["cause"].'</span>';
	}elseif($urlbanner_sban200==false | $urlbanner_sban200=="http://" | $urlbanner_sban200=="https://" | ((substr($urlbanner_sban200, 0, 7) != "http://" && substr($urlbanner_sban200, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указано URL баннера 200x300!</span>';
	}elseif(is_url_img($urlbanner_sban200)!="true") {
		echo is_url_img($urlbanner_sban200);
	}elseif(is_img_size('200','300',$urlbanner_sban200)!="true") {
		echo is_img_size('200','300',$urlbanner_sban200);
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url_sban200)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url_sban200).'</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($urlbanner_sban200)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($urlbanner_sban200).'</span>';
	}elseif(img_get_save($urlbanner_sban200)!="true") {
		echo img_get_save($urlbanner_sban200);

	}else{
		$urlbanner_sban468_load = img_get_save($urlbanner_sban468, 1);
		$urlbanner_sban100_load = img_get_save($urlbanner_sban100, 1);
		$urlbanner_sban200_load = img_get_save($urlbanner_sban200, 1);


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
       

		$sql_p = $mysqli->query("SELECT price FROM `tb_config_packet` WHERE `packet`='$packet'");
		if($sql_p->num_rows>0) {
			$row_p = $sql_p->fetch_array();
			$cena_packeta = $row_p["price"];
		}else{
			echo '<span class="msg-error">Ошибка такого пакета рекламы нет!</span><br>';
			include('footer.php');
			exit();
		}
		$summa = round($cena_packeta,2);
		$summa = number_format(($summa * (100-$cab_skidka)/100),2,".","");
		$money_add = $summa;

		$mysqli->query("DELETE FROM `tb_ads_packet` WHERE `status`='0' AND `date`<'".(time()-24*60*60)."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+1 WHERE `id`='1'") or die($mysqli->error);

		if($mysqli->query("SELECT `id` FROM `tb_ads_packet` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1")->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_packet` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`date`='".time()."',`packet`='$packet',`ds_url`='$url_ds',`ds_title`='$title_ds',`ds_text`='$text_ds',`slink_url`='$url_slink',`slink_text`='$text_slink',`sban468_url`='$url_sban468',`sban468_urlban`='$urlbanner_sban468',`sban468_urlban_load`='$urlbanner_sban468_load',`sban100_url`='$url_sban100',`sban100_urlban`='$urlbanner_sban100',`sban100_urlban_load`='$urlbanner_sban100_load',`sban200_url`='$url_sban200',`sban200_urlban`='$urlbanner_sban200',`sban200_urlban_load`='$urlbanner_sban200_load',`txtob_url`='$url_txtob',`txtob_text`='$text_txtob',`psdlink_url`='$url_psdlink',`psdlink_text`='$text_psdlink',`frm_url`='$url_frm',`frm_text`='$text_frm',`ip`='$laip',`money`='$money_add' WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_packet` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`packet`,`ds_url`,`ds_text`,`ds_title`,`slink_url`,`slink_text`,`sban468_url`,`sban468_urlban`,`sban468_urlban_load`,`sban100_url`,`sban100_urlban`,`sban100_urlban_load`,`sban200_url`,`sban200_urlban`,`sban200_urlban_load`,`txtob_url`,`txtob_text`,`psdlink_url`,`psdlink_text`,`frm_url`,`frm_text`,`ip`,`money`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','".time()."','$packet','$url_ds','$title_ds','$text_ds','$url_slink','$text_slink','$url_sban468','$urlbanner_sban468','$urlbanner_sban468_load','$url_sban100','$urlbanner_sban100','$urlbanner_sban100_load','$url_sban200','$urlbanner_sban200','$urlbanner_sban200_load','$url_txtob','$text_txtob','$url_psdlink','$text_psdlink','$url_frm','$text_frm','$laip','$money_add')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_packet` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;

		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="130"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>Пакет рекламы:</b></td><td>№'.$packet.'</td></tr>';
			echo '<tr><td><b>Способ оплаты:</b></td><td><b>'.$system_pay[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			
			@require_once("".$_SERVER['DOCUMENT_ROOT']."/curs/curs.php");
				$money_add_usd = number_format(round(($summa/$CURS_USD),2),2,".","");

			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';

			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоиомсть заказа:</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';
				
			}elseif($method_pay==7) {
						echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
						echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_usd,2,".","`").'</b> <b>USD</b></td></tr>';

			}else{
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}

		echo '</table>';

		$shp_item = "16";
		$inv_desc = "Оплата пакета рекламы №$packet, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay packet advertise N$packet, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once("".DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
	}
}else{

	if(isset($_GET["p"]) && intval(limpiar($_GET["p"]))>0) {
		$packet = (isset($_GET["p"])) ? abs(intval(limpiar($_GET["p"]))) : false;
		$packet = (isset($_GET["p"])) ? intval(limpiar($_GET["p"])) : false;

		$sql_p = $mysqli->query("SELECT * FROM `tb_config_packet` WHERE `packet`='$packet'");
		if($sql_p->num_rows>0) {
			$row_p = $sql_p->fetch_array();
			$ds_plan = $row_p["ds_plan"];
			$ds_timer = $row_p["ds_timer"];
			$slink_plan = $row_p["slink_plan"];
			$sban468_plan = $row_p["sban468_plan"];
			$sban100_plan = $row_p["sban100_plan"];
			$sban200_plan = $row_p["sban200_plan"];
			$txtob_plan = $row_p["txtob_plan"];
			$psdlink_plan = $row_p["psdlink_plan"];
			$frm_plan = $row_p["frm_plan"];
			$cena_packeta = $row_p["price"];
		}else{
			echo '<span class="msg-error">Ошибка такого пакета рекламы нет!</span><br>';
			include('footer.php');
			exit();
		}

		echo '<h5 class="sp" style="margin-top:0px; font-size:14px; font-weight:bold;">Пакет рекламы №'.$packet.'</h5>';
		echo '<div align="justify">';
			echo 'Стоимость пакета: <b style="color:#FF0000">'.$cena_packeta.'</b> <b>руб.</b>';
		echo '</div>';
		echo '<br><br>';

		echo '<form method="post" action="" id="newform">';
			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Динамическая ссылка</th></tr></thead>';
				echo '<tr><td width="200"><b>Количество показов</b></td><td>'.number_format($ds_plan,0,".","`").' по '.$ds_timer.' сек.</td></tr>';
				echo '<tr><td><b>Заголовок ссылки</b></td><td><input type="text" name="title_ds" maxlength="60" class="ok" value="" /></td></tr>';
				echo '<tr><td><b>Описание ссылки</b></td><td><input type="text" name="text_ds" maxlength="80" class="ok" value="" /></td></tr>';
				echo '<tr><td><b>URL сайта</b></td><td><input type="text" name="url_ds" maxlength="300" class="ok" value="http://" /></td></tr>';
			echo '</table><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Псевдо-динамическая ссылка</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($psdlink_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_psdlink" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>Текст ссылки:</b></td><td><input type="text" name="text_psdlink" maxlength="80" class="ok" value="" /></td></tr>';
			echo '</table><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Статическая ссылка</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($slink_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_slink" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>Текст ссылки:</b></td><td><input type="text" name="text_slink" maxlength="80" class="ok" value="" /></td></tr>';
			echo '</table><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Ссылка во Фрейме</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($frm_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_frm" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>Текст ссылки:</b></td><td><input type="text" name="text_frm" maxlength="80" class="ok" value="" /></td></tr>';
			echo '</table><br><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Текстовое объявление</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($txtob_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_txtob" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>Текст ссылки:</b></td><td><input type="text" name="text_txtob" maxlength="200" class="ok" value="" /></td></tr>';
			echo '</table><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Статический баннер 468x60</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($sban468_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_sban468" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>URL баннера:</b></td><td><input type="text" name="urlbanner_sban468" maxlength="160" class="ok" value="" /></td></tr>';
			echo '</table><br>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Статический баннер 100x100</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($sban100_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_sban100" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>URL баннера:</b></td><td><input type="text" name="urlbanner_sban100" maxlength="160" class="ok" value="" /></td></tr>';
			echo '</table>';

			echo '<table class="tables">';
				echo '<thead><tr><th class="top" colspan="2">Статический баннер 200x300</th></tr></thead>';
				echo '<tr><td><b>Количество дней:</b></td><td>'.number_format($sban200_plan,0,".","`").'</td></tr>';
				echo '<tr><td width="200"><b>URL ссылки:</b></td><td><input type="text" name="url_sban200" maxlength="160" class="ok" value="http://" /></td></tr>';
				echo '<tr><td><b>URL баннера:</b></td><td><input type="text" name="urlbanner_sban200" maxlength="160" class="ok" value="" /></td></tr>';
			echo '</table><br>';
		echo '</div>';
echo '<div class="blok" style="text-align:center;">';
	echo '<span id="adv-title3" class="adv-title-open" onclick="ShowHideBlock(3);">Выбрать способ оплаты</span>';
	echo '<div id="adv-block3" style="display:block;">';

		echo '<button id="method_pay"  name="method_pay" value="10" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rs1">';
      echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';

    if($site_pay_wm!=1) {
        echo '<div class="cash-wm1">';
    	  echo '<div class="cash-wm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="1" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-wm1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' (+0.8%) руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_ym!=1) {
        echo '<div class="cash-yd1">';
    	  echo '<div class="cash-yd1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="8" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-yd1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_robo!=1) {
        echo '<div class="cash-rb1">';
    	  echo '<div class="cash-rb1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="2" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rb1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}

	if($site_pay_mega!=1) {
    	  echo '<div class="cash-ik1">';
    	  echo '<div class="cash-ik1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="9" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ik1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_qw!=1) {
    	  echo '<div class="cash-qw1">';
    	  echo '<div class="cash-qw1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="6" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-qw1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
	
	 if($site_pay_payeer!=1) {
    	  echo '<div class="cash-pr1">';
    	  echo '<div class="cash-pr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="5" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pr1">';
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_pm!=1) {
    	  echo '<div class="cash-pm1">';
    	  echo '<div class="cash-pm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
     echo '<button id="method_pay" name="method_pay" value="7" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pm1" >';
     echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
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
    echo '<div><div><div><span class="line-green"><span style="color:#f6f9f6;">'.$cena_packeta.' руб.</span></span></div></div></div>';
	echo '</div> </button>';
	}
	
	 echo '</div>';
		echo '</form><br><br>';

	}else{
		echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:6px;">';
			echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Пакеты рекламы - что это?</span>';
			echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
				echo 'Пакеты рекламы на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; эта услуга позволяет Вам разместить сразу несколько видов рекламы по низкой стоимости. ';
				echo 'Данный сервис будет очень полезен и эффективен тем рекламодателям, которые проводят много рекламных кампаний на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b>. ';
				echo 'Заказывая пакеты рекламы, Вы получаете эффективный и не дорогой способ продвижения своего ресурса.';
				echo '<br>';
			echo '</div>';
		echo '</div>';

		$tabla = $mysqli->query("SELECT * FROM `tb_config_packet` ORDER BY packet ASC");
		if($tabla->num_rows>0) {
			while ($row = $tabla->fetch_array()) {
				echo '<table class="tables" style="margin:0 auto; width:100%;" id="newform">';
				echo '<thead><tr><th align="center" colspan="2"><b>Пакет рекламы №'.$row["packet"].'</b></th></tr></thead>';
				echo '<tbody>';
					echo '<tr>';
						echo '<td align="left"><b>Динамическая ссылка</b>, показов/сек.</td>';
						echo '<td align="right">'.$row["ds_plan"].'/'.$row["ds_timer"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Псевдо-динамические ссылки</b>, дней</td>';
						echo '<td align="right">'.$row["psdlink_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статическая ссылка</b>, дней</td>';
						echo '<td align="right">'.$row["slink_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статическая ссылка во Фрейме</b>, дней</td>';
						echo '<td align="right">'.$row["frm_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Текстовое объявление</b>, дней</td>';
						echo '<td align="right">'.$row["txtob_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статический баннер 468x60</b>, дней</td>';
						echo '<td align="right">'.$row["sban468_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статический баннер 100x100</b>, дней</td>';
						echo '<td align="right">'.$row["sban100_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статический баннер 200x300</b>, дней</td>';
						echo '<td align="right">'.$row["sban200_plan"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Стоимость пакета</b>, руб.</td>';
						echo '<td align="right"><b style="color:#FF0000;">'.$row["price"].'</b></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td colspan="2" align="center">';
							echo '<form method="GET" action="'.PHP_SELF.'">';
								echo '<input type="hidden" name="ads" value="'.$ads.'">';
								echo '<input type="hidden" name="p" value="'.$row["packet"].'">';
								echo '<input type="submit" value="Купить пакет рекламы №'.$row["packet"].'" class="sub-blue160" style="float:none;" />';
							echo '</form>';
						echo '</td>';
					echo '</tr>';
				echo '</tbody>';
				echo '</table><br><br>';
			}
		}else{
			echo '<span class="msg-error">Доступных пакетов рекламы нет</span>';
		}
	}

}

?>