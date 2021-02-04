<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require(DOC_ROOT."/advertise/func_load_banners.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление статического баннера</b></h3>';

$type_banner_arr = array(
	"468x60" 	=> "(все страницы, в шапке сайта)", 
	"468x60_frm" 	=> "(во фрейме просмотра рекламы)", 
	"200x300" 	=> "(все страницы)", 
	"100x100" 	=> "(все страницы)",
	"728x90" 	=> "(главная страница)"
);

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
	$mensaje = str_replace("&&","&",$mensaje);
	$mensaje = str_replace("http://http://","http://",$mensaje);
	$mensaje = str_replace("https://https://","https://",$mensaje);
	$mensaje = str_replace("&#063;","?",$mensaje);
	return $mensaje;
}


if(count($_POST)>0 && isset($_POST["id_pay"])) {
	$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

	$sql_id = $mysqli->query("SELECT `id`,`money` FROM `tb_ads_banner` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
	if($sql_id->num_rows>0) {
		$mysqli->query("UPDATE `tb_ads_banner` SET `status`='1', `date`='".time()."', `date_end`=`date`+`plan`*'".(24*60*60)."' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username'  ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_banners();

		echo '<span class="msg-ok">Реклама успешно размещена!</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'"></noscript>';
		exit();
	}else{
		echo '<span class="msg-error">Ошибка! Реклама не найдена.</span>';
		exit();
	}
}

if(count($_POST)>0) {
	$url = (isset($_POST["url"])) ? $func->limitatexto(limpiarez($_POST["url"]),300) : false;
	$urlbanner = (isset($_POST["urlbanner"])) ? $func->limitatexto(limpiarez($_POST["urlbanner"]),300) : false;
	$type_banner = (isset($_POST["type_banner"])) ? limpiarez($_POST["type_banner"]) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) > 0) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$method_pay = 0;
	$laip = getRealIP();
	$black_url = getHost($url);
	$black_url_ban = getHost($urlbanner);

	$size_banner_arr = explode("_", $type_banner);
	$size_banner = $size_banner_arr[0];

	$wh = explode("x", $size_banner);
	$w = $wh["0"];
	$h = $wh["1"];

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	$sql_bl_ban = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_ban'");

	if(array_key_exists($type_banner, $type_banner_arr) === false) {
		echo '<span class="msg-error">Некорректно указан тип баннера!</span>';
	}elseif($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_assoc();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row["cause"].'</span>';
	}elseif($sql_bl_ban->num_rows>0 && $black_url_ban!=false) {
		$row_ban = $sql_bl_ban->fetch_assoc();
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
	}elseif($plan<1) {
		echo '<span class="msg-error">Минимальное количество дней - 1.</span><br>';

	}elseif(img_get_save($urlbanner)!="true") {
		echo img_get_save($urlbanner);

	}else{
		$urlbanner_orig = $urlbanner;
		$urlbanner_load = img_get_save($urlbanner, 1);

		$mysqli->query("DELETE FROM `tb_ads_banner` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_banner` WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_banner` SET `merch_tran_id`='0',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username',`type`='$type_banner',`plan`='$plan',`date`='".time()."',`date_end`='".(time()+$plan*24*60*60)."',`url`='$url',`urlbanner`='$urlbanner',`urlbanner_load`='$urlbanner_load',`ip`='$laip' WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_banner` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`type`,`plan`,`date`,`date_end`,`url`,`urlbanner`,`urlbanner_load`,`ip`) 
			VALUES('0','".session_id()."','0','$method_pay','$wmid_user','$username','$type_banner','$plan','".time()."','".(time()+$plan*24*60*60)."','$url','$urlbanner','$urlbanner_load','$laip')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_banner` WHERE `status`='0' AND `type`='$type_banner' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;

		echo '<table class="tables">';
			echo '<thead><tr><th class="top" width="200">Параметр</a><th class="top">Значение</a></thead></tr>';
			echo '<tr><td><b>Баннер:</b></td><td>'.$size_banner.' '.(array_key_exists($type_banner, $type_banner_arr) ? $type_banner_arr[$type_banner] : false).'</td></tr>';
			echo '<tr><td><b>URL сайта:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>URL баннера:</b></td><td><a href="'.$urlbanner.'" target="_blank">'.$urlbanner.'</a></td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.'</td></tr>';
			echo '<tr><td><div align="center"><form action="" method="post"><input type="hidden" name="id_pay" value="'.$id_zakaz.'"><input type="submit" value="Разместить баннер" class="sub-blue160"></form></div></td><td><a href="'.$url.'" target="_blank"><img src="//'.$_SERVER["HTTP_HOST"].''.$urlbanner_load.'" width="'.$w.'" height="'.$h.'" border="0" alt="" title="" /></td></tr>';
		echo '</table><br/>';
		exit();
	}

	exit();
}

?><script type="text/javascript" language="JavaScript"> 
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
</script><?php

echo '<form method="POST" action="" onSubmit="return SbmFormB(); return false;" id="newform">';
echo '<table class="tables">';
echo '<thead><tr><th width="200">Параметр</th><th>Значение</th></tr></thead>';
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
		echo '<td align="left"><b>URL баннера</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="urlbanner" name="urlbanner" maxlength="300" value="http://" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Количество дней</td>';
		echo '<td align="left"><input type="text" id="plan" name="plan" maxlength="7" value="" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');">&nbsp;&nbsp;&nbsp;(минимум 1)</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2" align="center"><input type="submit" value="Далее" class="sub-blue160" style="float:none;" /></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>