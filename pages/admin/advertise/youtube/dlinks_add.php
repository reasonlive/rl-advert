<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require(DOC_ROOT."/merchant/func_mysql.php");
require(DOC_ROOT."/advertise/func_load_banners.php");

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление YouTube серфинга</b></h1>';

?><script type="text/javascript">
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

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_min_hits' AND `howmany`='1'");
$youtube_min_hits = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_timer_ot' AND `howmany`='1'");
$youtube_timer_ot = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='youtube_timer_do' AND `howmany`='1'");
$youtube_timer_do = $sql->fetch_object()->price;

if(count($_POST)>0) {
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]),60) : false;
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]),300) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) >= $youtube_min_hits ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$timer = ( isset($_POST["timer"]) && preg_match("|^[\d]{1,3}$|", trim($_POST["timer"])) && intval(limpiarez(trim($_POST["timer"]))) >= $youtube_timer_ot  && intval(limpiarez(trim($_POST["timer"]))) <= $youtube_timer_do ) ? intval(limpiarez(trim($_POST["timer"]))) : false;
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
	$revisit = (isset($_POST["revisit"]) && (intval($_POST["revisit"])==0 | intval($_POST["revisit"])==1 | intval($_POST["revisit"])==2)) ? intval(trim($_POST["revisit"])) : "0";
	$type_serf = (isset($_POST["type_serf"]) && (intval($_POST["type_serf"])==1 | intval($_POST["type_serf"])==2 | intval($_POST["type_serf"])==3 | intval($_POST["type_serf"])==4)) ? intval(trim($_POST["type_serf"])) : "1";

	if($type_serf==2 | $type_serf==4) {
		$color = 0;
		$title = false;
		$description = (isset($_POST["url_banner"])) ? limitatexto(limpiarez($_POST["url_banner"]),300) : false;
		$black_url = @getHost($url);
		$black_url_banner = @getHost($description);
	}else{
		$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),80) : false;
		$black_url = @getHost($url);
		$black_url_banner = false;
	}

	$active = (isset($_POST["active"]) && (intval($_POST["active"])==0 | intval($_POST["active"])==1)) ? intval(trim($_POST["active"])) : "0";
	$content = (isset($_POST["content"]) && (intval($_POST["content"])==0 | intval($_POST["content"])==1)) ? intval($_POST["content"]) : "0";
	$method_pay = 0;
	$nolimit = (isset($_POST["nolimit"])) ? intval(trim($_POST["nolimit"])) : "0";
	$limit_d = ( isset($_POST["limit_d"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["limit_d"])) ) ? intval(limpiarez(trim($_POST["limit_d"]))) : 0;
	$limit_h = ( isset($_POST["limit_h"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["limit_h"])) ) ? intval(limpiarez(trim($_POST["limit_h"]))) : 0;
	$laip = getRealIP();

	$new_users = (isset($_POST["new_users"]) && (intval($_POST["new_users"])==0 | intval($_POST["new_users"])==1)) ? intval($_POST["new_users"]) : "0";
	$unic_ip = (isset($_POST["unic_ip"]) && (intval($_POST["unic_ip"])==0 | intval($_POST["unic_ip"])==1 | intval($_POST["unic_ip"])==2)) ? intval($_POST["unic_ip"]) : "0";
	$no_ref = (isset($_POST["no_ref"]) && (intval($_POST["no_ref"])==0 | intval($_POST["no_ref"])==1)) ? intval($_POST["no_ref"]) : "0";
	$sex_adv = (isset($_POST["sex_adv"]) && (intval($_POST["sex_adv"])==0 | intval($_POST["sex_adv"])==1 | intval($_POST["sex_adv"])==2)) ? intval($_POST["sex_adv"]) : "0";
	$to_ref = (isset($_POST["to_ref"]) && (intval($_POST["to_ref"])==0 | intval($_POST["to_ref"])==1 | intval($_POST["to_ref"])==2)) ? intval($_POST["to_ref"]) : "0";

	$p = explode("youtu.be/", $_POST["url"]);  
	$p_time = explode("?", $p[1]);  
	$img_youtube='https://img.youtube.com/vi/'.$p_time[0].'/1.jpg';
	$ytflag=1;
	
	$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map(array($mysqli, 'real_escape_string'), $_POST["country"])) : false;
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
		1  => 'Россия', 	2  => 'Украина', 	3  => 'Белоруссия', 	4  => 'Молдавия',
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
				$country_arr[] = $val;
				$country_arr_ru[] = $geo_name_arr[$key+1];
			}
		}
	}
	$country = isset($country_arr) ? trim(strtoupper(implode(', ', $country_arr))) : false;
	$country_to = isset($country_arr_ru) ? trim(strtoupper(implode(', ', $country_arr_ru))) : false;
	if($country_to!=false) {$country_to="$country_to";}else{$country_to="Нет";}

	if($nolimit>0) {
		$plan = 0;
		$timer = 20;
		$up_list = 1;
		$color = 1;
		$active = 0;
		$revisit = 0;
		$unic_ip = 1;
	}

	if($nolimit==1) {
		$nolimitdate = time() + 7*24*60*60;
	}elseif($nolimit==2) {
		$nolimitdate = time() + 14*24*60*60;
	}elseif($nolimit==3) {
		$nolimitdate = time() + 21*24*60*60;
	}elseif($nolimit==4) {
		$nolimitdate = time() + 30*24*60*60;
	}else{
		$nolimit = 0;
		$nolimitdate = 0;
	}


	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	$sql_bl_banner = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url_banner'");

	if($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на сайт!</span>';
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
	}elseif(is_url($url)!="true") {
		echo is_url($url);
	}elseif(($type_serf==1 | $type_serf==3) && $title==false) {
		echo '<span class="msg-error">Вы не указали заголовок ссылки.</span><br>';
	}elseif(($type_serf==1 | $type_serf==3) && $description==false) {
		echo '<span class="msg-error">Вы не указали краткое описание ссылки.</span><br>';

	}elseif(($type_serf==2 | $type_serf==4) && $sql_bl_banner->num_rows>0 && $black_url_banner!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	/*}elseif(($type_serf==2 | $type_serf==4) && ($description==false | $description=="http://" | $description=="https://")) {
		echo '<span class="msg-error">Не указана ссылка на баннер!</span>';
	}elseif(($type_serf==2 | $type_serf==4) && ((substr($description, 0, 7) != "http://" && substr($description, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на баннер!</span>';
	}elseif(($type_serf==2 | $type_serf==4) && is_url($url)!="true") {
		echo is_url($url);
	}elseif(($type_serf==2 | $type_serf==4) && is_url_img($description)!="true") {
		echo is_url_img($description);
	}elseif(($type_serf==2 | $type_serf==4) && is_img_size("468", "60", $description)!="true") {
		echo is_img_size("468", "60", $description);*/
	}elseif(count($p)<='1') {
		echo '<span class="msg-error">Не верно указана ссылка youtub!</span>';
	}elseif($nolimit==0 && $plan<$youtube_min_hits) {
		echo '<span class="msg-error">Минимальный заказ - '.$youtube_min_hits.' показов.</span><br>';
	}elseif($limit_d!=false && $limit_d<$youtube_min_hits) {
		echo '<span class="msg-error">Ограничение количества показов в сутки должно быть не менее '.$youtube_min_hits.' просмотров либо 0 - без ограничений.</span>';
	}elseif($limit_h!=false && $limit_h<$youtube_min_hits) {
		echo '<span class="msg-error">Ограничение количества показов в час должно быть не менее '.$youtube_min_hits.' просмотров либо 0 - без ограничений.</span>';
	}elseif($timer==false) {
		echo '<span class="msg-error">Время просмотра должно быть в пределах от '.$youtube_timer_ot.' сек. до '.$youtube_timer_do.' сек.</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
			exit(SFB_YANDEX($url));
		}elseif(@getHost($description)!=$_SERVER["HTTP_HOST"] && ($type_serf==2 | $type_serf==4) && SFB_YANDEX($description)!=false) {
			exit(SFB_YANDEX($description));

	}else{
		if(($type_serf==2 | $type_serf==4)) {
			$urlbanner_load = img_get_save($description, 1);
		}else{
			$urlbanner_load = false;
		}

		if($limit_d>0) {$limit_d_to="$limit_d показов в сутки";}else{$limit_d_to="Без ограничений";}
		if($limit_h>0) {$limit_h_to="$limit_h показов в час";}else{$limit_h_to="Без ограничений";}

		$mysqli->query("DELETE FROM `tb_ads_youtube` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$mysqli->query("INSERT INTO `tb_ads_youtube` (`ytflag`, `img_youtube`, `status`,`session_ident`,`merch_tran_id`,`method_pay`,`type_serf`,`date`,`wmid`,`username`,`geo_targ`,`content`,`active`,`revisit`,`color`,`timer`,`nolimit`,`limit_d`,`limit_h`,`limit_d_now`,`limit_h_now`,`new_users`,`unic_ip`,`no_ref`,`sex_adv`,`to_ref`,`url`,`title`,`description`,`urlbanner_load`,`plan`,`totals`,`ip`,`money`) 
		VALUES('$ytflag', '$img_youtube','1','".session_id()."','0','$method_pay','$type_serf','".time()."','$wmid_user','$username','$country','$content','$active','$revisit','$color','$timer','$nolimitdate','$limit_d','$limit_h','$limit_d','$limit_h','$new_users','$unic_ip','$no_ref','$sex_adv','$to_ref','$url','$title','$description','$urlbanner_load','$plan','$plan','$laip','0')") or die($mysqli->error);

		if($type_serf==3) {
			$mysqli->query("UPDATE `tb_users` SET `youtube_serf`=`youtube_serf`+'200' WHERE `username`='$username'") or die($mysqli->error);

		}elseif($type_serf==4) {
			$mysqli->query("UPDATE `tb_users` SET `ban_serf`=`ban_serf`+'200' WHERE `username`='$username'") or die($mysqli->error);
		}

		echo '<span class="msg-ok">Реклама добавлена</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 2000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}

}


?><script type="text/javascript" language="JavaScript"> 

function SbmFormB() {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	var url = $.trim($("#url").val());
	var url_banner = $.trim($("#url_banner").val());
	var type_serf = $.trim($("#type_serf").val());
	var plan = $.trim($("#plan").val());
	var nolimit = $.trim($("#nolimit").val());
	var timer = $.trim($("#timer").val());

	if(url == '' | url == 'http://' | url == 'https://') {
		$("#url").focus().attr("class", "err");
		alert("Вы не указали URL-адрес сайта");
		return false;

	} else if( (type_serf == '1' | type_serf == '3' ) && title == '') {
		$("#title").focus().attr("class", "err");
		alert("Вы не указали заголовок ссылки");
		return false;

	} else if( (type_serf == '1' | type_serf == '3' ) && description == '') {
		$("#description").focus().attr("class", "err");
		alert("Вы не указали описание ссылки");
		return false;

	} else if( (type_serf == '2' | type_serf == '4' ) && (url_banner == '' | url_banner == 'http://' | url_banner == 'https://') ) {
		$("#url_banner").focus().attr("class", "err");
		alert("Вы не указали URL-адрес баннера");
		return false;

	} else if(nolimit == '0' && (plan == '' | plan < <?=$youtube_min_hits;?>) ) {
		$("#plan").focus().attr("class", "err12");
		alert("Минимальное количество визитов - <?=$youtube_min_hits;?>");
		return false;

	} else if(nolimit == '0' && (timer == '' | timer < <?=$youtube_timer_ot;?> | timer > <?=$youtube_timer_do;?>) ) {
		$("#timer").focus().attr("class", "err12");
		alert("Время просмотра должно быть в пределах от <?=$youtube_timer_ot;?> сек. до <?=$youtube_timer_do;?> сек.");
		return false;

	} else {
		return true;
	}
}

function PlanChange(){
	var nolimit = $.trim($("#nolimit").val());
	var timer = $.trim($("#timer").val());
	var color = $.trim($("#color").val());
	var type_serf = $.trim($("#type_serf").val());

	if(type_serf==2 | type_serf==4) {
		var color = 0;
		$("#type_serf_a").css("display", "none");
		$("#type_serf_b").css("display", "none");
		$("#type_serf_c").css("display", "table-row");
		$("#type_serf_d").css("display", "none");
	} else { 
		$("#type_serf_a").css("display", "table-row");
		$("#type_serf_b").css("display", "table-row");
		$("#type_serf_c").css("display", "none");
		$("#type_serf_d").css("display", "table-row");
	}

	if(timer<<?=$youtube_timer_ot;?>) { timer = <?=$youtube_timer_ot;?>}
	if(timer><?=$youtube_timer_do;?>) { timer = <?=$youtube_timer_do;?>}

	if(nolimit>0) {
		$("#bl1").hide();
		$("#bl11").html('<b>Не ограничено</b>');
		$("#bl2").hide();
		$("#bl21").html('<b>20</b> сек.');
		$("#bl3").hide();
		$("#bl31").html('<b>Да</b>');
		$("#bl4").css("display", "none");
		$("#bl41").hide();
		$("#bl5").hide();
		$("#bl51").html('<b>Нет</b>');
		$("#bl6").hide();
		$("#bl61").html('<b>Каждые 24 часа</b>');
		$("#bl7").hide();
		$("#bl71").html('<b>Да</b>');
	}else{
		$("#bl1").show();
		$("#bl11").html('');
		$("#bl2").show();
		$("#bl21").html('');
		$("#bl3").show();
		$("#bl31").html('');
		$("#bl4").show();
		$("#bl41").html('');
		$("#bl5").show();
		$("#bl51").html('');
		$("#bl6").show();
		$("#bl61").html('');
		$("#bl7").show();
		$("#bl71").html('');
	}
}
</script><?php

echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
echo '<table class="tables" style="margin:0px auto;">';
echo '<thead><tr align="center"><th>Параметр</th><th>Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Выберите тип сёрфинга</b></td>';
		echo '<td>';
			echo '<select id="type_serf" name="type_serf" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="1">Динамический серфинг</option>';
				echo '<option value="2">Баннерный серфинг</option>';
				echo '<option value="3">Динамический серфинг - VIP (Вам будет доступно 200 просмотров ссылок в VIP серфинге)</option>';
				echo '<option value="4">Баннерный серфинг - VIP (Вам будет доступно 200 просмотров баннеров в VIP серфинге)</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Безлимитная реклама</b></td>';
		echo '<td>';
			echo '<select id="nolimit" name="nolimit" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">1 неделя</option>';
				echo '<option value="2">2 недели</option>';
				echo '<option value="3">3 недели</option>';
				echo '<option value="4">1 месяц</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b></td>';
		echo '<td><input type="text" id="url" name="url" maxlength="300" value="http://" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr id="type_serf_a">';
		echo '<td align="left"><b>Заголовок ссылки</b></td>';
		echo '<td><input type="text" id="title" name="title" maxlength="60" value="" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr id="type_serf_b">';
		echo '<td align="left"><b>Краткое описание ссылки</b></td>';
		echo '<td><input type="text" id="description" name="description" maxlength="80" value="" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr id="type_serf_c" style="display: none;">';
		echo '<td align="left"><b>URL баннера 468х60</b></td>';
		echo '<td><input type="text" id="url_banner" name="url_banner" maxlength="300" value="" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Количество просмотров</b></td>';
		echo '<td><div id="bl1"><input type="text" id="plan" name="plan" maxlength="7" value="1000" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');"></div><div id="bl11" style="text-align:left;"></div></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Время просмотра</b></td>';
		echo '<td><div id="bl2"><input type="text" id="timer" name="timer" maxlength="3" value="20" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');"></div><div id="bl21" style="text-align:left;"></div></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';

echo '<span id="adv-title1" class="adv-title-close" onclick="ShowHideBlock(1);">Дополнительные настройки</span>';
echo '<div id="adv-block1" class="tables" style="display:none; margin:0 auto; padding:0;">';
	echo '<table class="tables" style="margin:0 auto;">';
	echo '<tbody>';
		echo '<tr id="type_serf_d">';
			echo '<td align="left" width="220">Выделить цветом</td>';
			echo '<td><div id="bl3">';
				echo '<select id="color" name="color" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Нет</option>';
					echo '<option value="1">Да</option>';
				echo '</select>';
			echo '</div><div id="bl31" style="text-align:left;"></div></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left" width="220">Активное окно</td>';
			echo '<td><div id="bl5">';
				echo '<select id="active" name="active" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Нет</option>';
					echo '<option value="1">Да</option>';
				echo '</select>';
			echo '</div><div id="bl51" style="text-align:left;"></div></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Доступно для просмотра</b></td>';
			echo '<td><div id="bl6">';
				echo '<select id="revisit" name="revisit" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Каждые 24 часа</option>';
					echo '<option value="1">Каждые 48 часов</option>';
					echo '<option value="2">1 раз</option>';
				echo '</select>';
			echo '</div><div id="bl61" style="text-align:left;"></div></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Уникальный IP</td>';
			echo '<td><div id="bl7">';
				echo '<select id="unic_ip" name="unic_ip" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Нет</option>';
					echo '<option value="1">Да (100% совпадение)</option>';
					echo '<option value="2">Усиленный по маске до 2 чисел (255.255.X.X)</option>';
				echo '</select>';
			echo '</div><div id="bl71" style="text-align:left;"></div></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Показывать только новичкам</td>';
			echo '<td>';
				echo '<select id="new_users" name="new_users" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Всем пользователям проекта</option>';
					echo '<option value="1">Да (До 7 дней с момента регистрации)</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Показывать</td>';
			echo '<td>';
				echo '<select id="no_ref" name="no_ref" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Всем пользователям проекта</option>';
					echo '<option value="1">Пользователям без реферера на '.$_SERVER["HTTP_HOST"].'</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">По половому признаку</td>';
			echo '<td>';
				echo '<select id="sex_adv" name="sex_adv" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Всем пользователям проекта</option>';
					echo '<option value="1">Только мужчины</option>';
					echo '<option value="2">Только женщины</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Показывать только рефералам</td>';
			echo '<td>';
				echo '<select id="to_ref" name="to_ref" onChange="PlanChange();" onClick="PlanChange();">';
					echo '<option value="0">Всем пользователям проекта</option>';
					echo '<option value="1">Рефералам 1-го уровня</option>';
					echo '<option value="2">Рефералам всех уровней</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Контент 18+</td>';
			echo '<td>';
				echo '<input id="content" name="content" type="checkbox" value="1" style="width:18px; height:18px; margin:0px; padding:0; display:block; float:left;" />';
				echo '<span style="margin-top:2px; padding-left:5px; font-weight:bold; display:block; float:left;"> - на моем сайте присутствуют материалы для взрослых</span>';
			echo '</td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '<table class="tables" style="margin:0 auto;">';
	echo '<tbody>';
		echo '<tr>';
			echo '<td align="left" width="220">Ограничение показов в сутки</td>';
			echo '<td width="125"><input type="text" id="limit_d" name="limit_d" maxlength="11" value="0" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');"></td>';
			echo '<td align="left" rowspan="2">(0 - без ограничений)</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Ограничение показов в час</td>';
			echo '<td width="125"><input type="text" id="limit_h" name="limit_h" maxlength="11" value="0" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');"></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';
echo '</div>';

echo '<span id="adv-title2" class="adv-title-close" onclick="ShowHideBlock(2);">Гео-таргетинг</span>';
echo '<div id="adv-block2" class="tables" style="display:none; margin:0 auto; padding:0;">';
	echo '<table class="tables" style="margin:0 auto;">';
	echo '<tbody>';
		echo '<tr>';
			echo '<td colspan="2" align="center"><a onclick="setChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
			echo '<td colspan="2" align="center"><a onclick="setChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
		echo '</tr>';
		include($_SERVER["DOCUMENT_ROOT"]."/advertise/func_geotarg.php");
	echo '</tbody>';
	echo '</table>';
echo '</div>';

echo '<table class="tables" style="margin:0 auto;">';
echo '<tbody>';
	echo '<tr>';
		echo '<td colspan="2" align="center"><input type="submit" value="Добавить" class="sub-blue160" style="float:none;" /></td>';
	echo '</tr>';
echo '</tbody>';
echo '</tbody>';
echo '</table>';

echo '</form>';
?>