<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление ссылки во фрейм</b></h3>';

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

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

	$sql_id = $mysqli->query("SELECT * FROM `tb_ads_frm` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
	if($sql_id->num_rows>0) {
		$row = $sql_id->fetch_array();

		$mysqli->query("UPDATE `tb_ads_frm` SET `status`='1', `date`='".time()."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_frm_links();

		echo '<span class="msg-ok">Ссылка успешно размещена!</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
		exit();
	}else{
		echo '<span class="msg-error">Ошибка! Реклама не найдена.</span>';
		exit();
	}

}elseif(count($_POST)>0) {
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),255) : false;
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"]))) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$method_pay = 0;
	$laip = getRealIP();
	$black_url = @getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	if($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row["cause"].'</span>';
	}elseif($description==false) {
		echo '<span class="msg-error">Не заполнено поле Описание ссылки.</span><br>';
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на сайт!</span>';
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
	}elseif(is_url($url)!="true") {
		echo is_url($url);
	}elseif($plan<1) {
		echo '<span class="msg-error">Минимальное количество дней - 1.</span><br>';
	}else{
		$mysqli->query("DELETE FROM `tb_ads_frm` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_frm` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_frm` SET `wmid`='$wmid_user', `username`='$username', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `plan`='$plan', `url`='$url', `description`='$description', `ip`='$laip', `money`='0' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_frm` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_end`,`plan`,`url`,`description`,`ip`,`money`) 
			VALUES('0','".session_id()."','0','$method_pay','$wmid_user','$username','".time()."','".(time()+$plan*24*60*60)."','$plan','$url','$description','$laip','0')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_frm` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;
	
		echo '<table class="tables">';
			echo '<thead><tr><th class="top" width="200">Параметр</a><th class="top">Значение</a></thead></tr>';
			echo '<tr><td><b>URL ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Описание ссылки:</b></td><td>'.$description.'</td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.'</td></tr>';
			echo '<tr><td colspan="2"><div align="center"><form action="" method="post"><input type="hidden" name="id_pay" value="'.$id_zakaz.'"><input type="submit" value="Разместить ссылку" class="sub-blue160"></form></div></td></tr>';
		echo '</table><br/>';
		exit();
	}
}

?><script type="text/javascript" language="JavaScript"> 

function gebi(id){
	return document.getElementById(id)
}

function descchange() {
	var desc = gebi('desc').value;
	if(desc.length > 255) {
		gebi('desc').value = desc.substr(0,255);
	}
	gebi('count').innerHTML = 'Осталось <b>'+(255-desc.length)+'</b> символов';
}

function SbmFormB() {
	arrayElem = document.forms["formzakaz"];
	var col=0;

	for (var i=0;i<arrayElem.length;i++){
		if ((document.forms["formzakaz"].url.value == '')|(document.forms["formzakaz"].url.value == 'http://')) {
			alert('Вы не указали URL-адрес сайта');
			arrayElem[i+0].style.background = "#FFDBDB";
			arrayElem[i+0].focus();
			return false;
		}else{
			arrayElem[i+0].style.background = "#FFFFFF";
		}
		if ((document.forms["formzakaz"].description.value == '')) {
			alert('Вы не указали Описание ссылки');
			arrayElem[i+1].style.background = "#FFDBDB";
			arrayElem[i+1].focus();
			return false;
		}else{
			arrayElem[i+1].style.background = "#FFFFFF";
		}
		if ((document.forms["formzakaz"].plan.value == '')) {
			alert('Вы не указали количество дней');
			arrayElem[i+2].style.background = "#FFDBDB";
			arrayElem[i+2].focus();
			return false;
		}else{
			arrayElem[i+2].style.background = "#FFFFFF";
		}
		if ((document.forms["formzakaz"].plan.value < 1 )) {
			alert('Минимальное количество дней 1');
			arrayElem[i+2].style.background = "#FFDBDB";
			arrayElem[i+2].focus();
			return false;
		}else{
			arrayElem[i+2].style.background = "#FFFFFF";
		}
	}

	document.forms["formzakaz"].submit();
	return true;
}

</script><?php

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';
		echo '<thead><tr>';
			echo '<th class="top">Параметр</a>';
			echo '<th class="top">Значение</a>';
		echo '</thead></tr>';
		echo '<tbody>';
		echo '<tr>';
			echo '<td width="160"><b>URL сайта (ссылка):</b></td>';
			echo '<td><input type="text" name="url" maxlength="160" value="http://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Описание ссылки:</b></td>';
			echo '<td>';
				echo '<textarea name="description" id="desc" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';" onChange="descchange();" onKeyUp="descchange();"></textarea>';
				echo '<div align="right" id="count" style="color:#696969;"></div>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Количество дней:</b></td>';
			echo '<td><input type="text" name="plan" id="plan" maxlength="7" value="" class="ok12" style="text-align:right;" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2"><div align="center"><input type="submit" value="Сохранить" class="sub-blue160" /></div></td>';
		echo '</tr>';
		echo '</tbody>';
	echo '</table>';
	echo '</form>';
?>

<script language="JavaScript">descchange();</script>