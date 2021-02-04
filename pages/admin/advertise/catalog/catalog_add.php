<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление ссылки в каталог</b></h3>';

function limpiarez($mensaje) {
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

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_min' AND `howmany`='1'");
$catalog_min = number_format($sql->fetch_object()->price, 0, ".", "");

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

	$sql_id = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
	if($sql_id->num_rows>0) {
		$row = $sql_id->fetch_assoc();
		$plan = $row["plan"];

		$mysqli->query("UPDATE `tb_ads_catalog` SET `status`='1', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);

		require_once(ROOT_DIR."/merchant/func_cache.php");
		cache_catalog();

		echo '<span class="msg-ok">Ссылка успешно размещена!</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'"></noscript>';
		exit();
	}else{
		echo '<span class="msg-error">Ошибка! Реклама не найдена.</span>';
		exit();
	}

}elseif(count($_POST)>0) {
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]),30) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"]))) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
	$method_pay = 0;
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
	}else{
		$color_to[0]="НЕТ";
		$color_to[1]="ДА";

		$mysqli->query("DELETE FROM `tb_ads_catalog` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_catalog` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_catalog` SET `wmid`='$wmid_user', `username`='$username', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `plan`='$plan', `url`='$url', `title`='$title', `color`='$color', `ip`='$laip', `money`='0' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_catalog` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_end`,`plan`,`url`,`title`,`color`,`ip`,`money`) 
			VALUES('0','".session_id()."','0','$method_pay','$wmid_user','$username','".time()."','".(time()+$plan*24*60*60)."','$plan','$url','$title','$color','$laip','0')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_catalog` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;
	
		echo '<table class="tables" style="width:600px">';
			echo '<thead><tr><th class="top" width="200">Параметр</a><th class="top">Значение</a></thead></tr>';
			echo '<tr><td><b>Заголовок ссылки:</b></td><td>'.$title.'</td></tr>';
			echo '<tr><td><b>URL ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.'</td></tr>';
			echo '<tr><td><b>Выделение цветом:</b></td><td>'.$color_to[$color].'</td></tr>';
			echo '<tr><td colspan="2"><div align="center"><form action="" method="post"><input type="hidden" name="id_pay" value="'.$id_zakaz.'"><input type="submit" value="Разместить ссылку" class="sub-blue160"></form></div></td></tr>';
		echo '</table><br/>';
		exit();
	}
}

?><script type="text/javascript" language="JavaScript"> 

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

</script><?php

echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
echo '<table class="tables" style="margin:2px auto; padding:0px;">';
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
	echo '<td align="left"><input type="text" name="plan" id="plan" maxlength="11" value="'.$catalog_min.'" class="ok12" style="margin:0; text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left">Выделить цветом</td>';
	echo '<td align="left">';
		echo '<select name="color" id="color" style="margin-bottom:1px;" onChange="PlanChange();" onClick="PlanChange();">';
			echo '<option value="0">Нет</option>';
			echo '<option value="1">Да</option>';
		echo '</select>';
	echo '</td>';
echo '</tr>';
echo '<tr>';
	echo '<td colspan="2"><div align="center"><input type="submit" value="Сохранить" class="sub-blue" /></div></td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>