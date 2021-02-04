<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
//require(DOC_ROOT."/advertise/func_load_banners.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование YouTube серфинга</b></h3>';

$mysqli->query("UPDATE `tb_ads_youtube` SET `status`='3', `date`='".time()."' WHERE `status`>'0' AND `status`<'3' AND ( (`totals`<'1' AND `nolimit`='0') OR ( `nolimit`>'0' AND `nolimit`<='".time()."') )") or die($mysqli->error);

$metode = false;
$search = false;
$operator = 0;
$WHERE_ADD = false;
$WHERE_ADD_to_get = false;

if(isset($_POST["search"]) && isset($_POST["metode"])) {
	$metode = isset($_POST["metode"]) ? $mysqli->real_escape_string(trim($_POST["metode"])) : false;
	$search = isset($_POST["search"]) ? $mysqli->real_escape_string(trim($_POST["search"])) : false;
	$operator = isset($_POST["operator"]) ? intval($mysqli->real_escape_string(trim($_POST["operator"]))) : 0;

	if($metode != "" && $search != false) {
		if($operator == "0") {
			$WHERE_ADD = " AND `$metode`='$search'";
		}else{
			$WHERE_ADD = " AND `$metode` LIKE '%$search%'";
		}
		$WHERE_ADD_to_get = "&metode=$metode&operator=$operator&search=$search";
	}
}
if(isset($_GET["search"]) && isset($_GET["metode"])) {
	$metode = isset($_GET["metode"]) ? $mysqli->real_escape_string(trim($_GET["metode"])) : false;
	$search = isset($_GET["search"]) ? $mysqli->real_escape_string(trim($_GET["search"])) : false;
	$operator = isset($_GET["operator"]) ? intval($mysqli->real_escape_string(trim($_GET["operator"]))) : false;

	if($metode != "" && $search != false) {
		if($operator == "0") {
			$WHERE_ADD = " AND `$metode`='$search'";
		}else{
			$WHERE_ADD = " AND `$metode` LIKE '%$search%'";
		}
		$WHERE_ADD_to_get = "&metode=$metode&operator=$operator&search=$search";
	}
}

require("navigator/navigator.php");
$perpage = 20;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_youtube` WHERE `status`>'0' AND `type_serf`!='10' $WHERE_ADD");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

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

$system_pay[-2] = "Test Drive";
$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
$system_pay[1] = "WebMoney";
$system_pay[2] = "Robokassa";
$system_pay[3] = "LiqPay";
$system_pay[4] = "Interkassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[11] = "MegaKassa";
$system_pay[10] = "Рекл. счет";

$type_serf[1] = "youtube";
$type_serf[2] = "Баннерный";
$type_serf[3] = "youtube-VIP";
$type_serf[4] = "Баннерный-VIP";
$type_serf[-1] = "Тест драйв";


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

if(isset($_POST["id"])) {
	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;
	$username = (isset($_POST["username"])) ? ($_POST["username"]) : false;
	$wmid = (isset($_POST["wmid"])) ? (limpiar($_POST["wmid"])) : false;

	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]),60) : false;
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]),300) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$timer = ( isset($_POST["timer"]) && preg_match("|^[\d]{1,3}$|", trim($_POST["timer"])) && intval(limpiarez(trim($_POST["timer"]))) >= $youtube_timer_ot  && intval(limpiarez(trim($_POST["timer"]))) <= $youtube_timer_do ) ? intval(limpiarez(trim($_POST["timer"]))) : false;
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
	$revisit = (isset($_POST["revisit"]) && (intval($_POST["revisit"])==0 | intval($_POST["revisit"])==1 | intval($_POST["revisit"])==2)) ? intval(trim($_POST["revisit"])) : "0";
	$type_serf = (isset($_POST["type_serf"]) && (intval($_POST["type_serf"])==-1 | intval($_POST["type_serf"])==1 | intval($_POST["type_serf"])==2 | intval($_POST["type_serf"])==3 | intval($_POST["type_serf"])==4 )) ? intval(trim($_POST["type_serf"])) : "1";

	$p = explode("youtu.be/", $_POST["url"]);  
	$p_time = explode("?", $p[1]);  
	$img_youtube='https://img.youtube.com/vi/'.$p_time[0].'/1.jpg';
	
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
	$nolimit = (isset($_POST["nolimit"])) ? intval(trim($_POST["nolimit"])) : "0";
	$limit_d = ( isset($_POST["limit_d"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["limit_d"])) ) ? intval(limpiarez(trim($_POST["limit_d"]))) : 0;
	$limit_h = ( isset($_POST["limit_h"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["limit_h"])) ) ? intval(limpiarez(trim($_POST["limit_h"]))) : 0;
	$laip = getRealIP();

	$new_users = (isset($_POST["new_users"]) && (intval($_POST["new_users"])==0 | intval($_POST["new_users"])==1)) ? intval($_POST["new_users"]) : "0";
	$unic_ip = (isset($_POST["unic_ip"]) && (intval($_POST["unic_ip"])==0 | intval($_POST["unic_ip"])==1 | intval($_POST["unic_ip"])==2)) ? intval($_POST["unic_ip"]) : "0";
	$no_ref = (isset($_POST["no_ref"]) && (intval($_POST["no_ref"])==0 | intval($_POST["no_ref"])==1)) ? intval($_POST["no_ref"]) : "0";
	$sex_adv = (isset($_POST["sex_adv"]) && (intval($_POST["sex_adv"])==0 | intval($_POST["sex_adv"])==1 | intval($_POST["sex_adv"])==2)) ? intval($_POST["sex_adv"]) : "0";
	$to_ref = (isset($_POST["to_ref"]) && (intval($_POST["to_ref"])==0 | intval($_POST["to_ref"])==1 | intval($_POST["to_ref"])==2)) ? intval($_POST["to_ref"]) : "0";

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
	}elseif(($type_serf==2 | $type_serf==4) && ($description==false | $description=="http://" | $description=="https://")) {
		echo '<span class="msg-error">Не указана ссылка на баннер!</span>';
	}elseif(($type_serf==2 | $type_serf==4) && ((substr($description, 0, 7) != "http://" && substr($description, 0, 8) != "https://"))) {
		echo '<span class="msg-error">Не верно указана ссылка на баннер!</span>';
	}elseif(($type_serf==2 | $type_serf==4) && is_url($url)!="true") {
		echo is_url($url);
	}elseif(($type_serf==2 | $type_serf==4) && is_url_img($description)!="true") {
		echo is_url_img($description);
	}elseif(($type_serf==2 | $type_serf==4) && is_img_size("468", "60", $description)!="true") {
		echo is_img_size("468", "60", $description);

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

		$sql_totals = $mysqli->query("SELECT `plan`,`totals` FROM `tb_ads_youtube` WHERE `id`='$id' AND `type_serf`!='10'");
		if($sql_totals->num_rows>0) {
			$row_totals = $sql_totals->fetch_array();
			$plan_table = $row_totals["plan"];
			$totals_table = $row_totals["totals"];

			if($plan==$plan_table) {
				$new_plan = $plan;
				$new_totals = $totals_table;
			}elseif($plan>$plan_table) {
				$new_plan = $plan;
				$new_totals = $totals_table + ($plan - $plan_table);
			}elseif($plan<$plan_table) {
				echo '<span class="msg-error">Количество переходов нельзя уменьшить.</span><br>';
				exit();
			}else{
				$new_plan = $plan;
				$new_totals = $totals_table;
			}

			$mysqli->query("UPDATE `tb_ads_youtube` SET `img_youtube`='$img_youtube', `type_serf`='$type_serf',`wmid`='$wmid',`username`='$username',`geo_targ`='$country',`content`='$content',`active`='$active',`revisit`='$revisit',`color`='$color',`timer`='$timer',`limit_d`='$limit_d',`limit_h`='$limit_h',`limit_d_now`='$limit_d',`limit_h_now`='$limit_h',`new_users`='$new_users',`unic_ip`='$unic_ip',`no_ref`='$no_ref',`sex_adv`='$sex_adv',`to_ref`='$to_ref',`url`='$url',`title`='$title',`description`='$description',`urlbanner_load`='$urlbanner_load',`plan`='$new_plan',`totals`='$new_totals' WHERE `id`='$id'") or die($mysqli->error);
		}

		echo '<span id="info-msg" class="msg-ok">Реклама успешно сохранена!</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'")\', 2000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'"></noscript>';
		exit();
	}
}

if(isset($_GET["id"])) {
	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if ($option=="edit"){
		$sql = $mysqli->query("SELECT * FROM `tb_ads_youtube` WHERE `id`='$id' AND `status`>'0' AND `type_serf`!='10'");
		if($sql->num_rows>0) {
			$row = $sql->fetch_array();

			echo '<form method="post" action="" id="newform">';
			echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
			echo '<thead><tr>';
				echo '<th class="top">Параметр</a>';
				echo '<th class="top">Значение</a>';
			echo '</thead></tr>';
			echo '<tbody>';
			echo '<tr>';
				echo '<td width="240"><b>№</b></td>';
				echo '<td><input type="hidden" name="id" value="'.$row["id"].'">'.$row["id"].'</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td width="240"><b>Тип серфинга</b></td>';
				if($row["type_serf"]==1) {
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'">youtube серфинг</td>';
				}elseif($row["type_serf"]==2) {
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'">Баннерный серфинг</td>';
				}elseif($row["type_serf"]==3) {
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'">youtube серфинг - VIP</td>';
				}elseif($row["type_serf"]==4) {
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'">Баннерный серфинг - VIP</td>';
				}elseif($row["type_serf"]==-1) {
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'">Тест драйв</td>';
				}else{
					echo '<td><input type="hidden" name="type_serf" value="'.$row["type_serf"].'"></td>';
				}
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Счет №</b></td>';
				echo '<td>'.$row["merch_tran_id"].'</td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>WMID</b></td>';
				echo '<td><input type="text" name="wmid" value="'.$row["wmid"].'" class="ok12"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Логин</b></td>';
				echo '<td><input type="text" name="username" value="'.$row["username"].'" class="ok12"></td>';
			echo '</tr>';

			if($row["nolimit"]>0) {
				echo '<tr>';
					echo '<td align="left"><b>Количество просмотров</b></td>';
					echo '<td align="left">';
						echo '<input type="hidden" name="plan" value="'.$row["plan"].'">';
						echo 'Безлимитная, до '.DATE("d.m.Y H:i",$row["nolimit"]);
					echo '</td>';
				echo '</tr>';
			}else{
				echo '<tr>';
					echo '<td align="left"><b>Количество просмотров</b></td>';
					echo '<td align="left">';
						echo '<input type="text" name="plan" value="'.$row["plan"].'" class="ok12" style="text-align:right;">';
					echo '</td>';
				echo '</tr>';
			}
			echo '<tr>';
				echo '<td><b>Время просмотра</b></td>';
				echo '<td><input type="text" name="timer" value="'.$row["timer"].'" class="ok12" style="text-align:right;"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>URL сайта</b></td>';
				echo '<td><input type="text" name="url" value="'.$row["url"].'" class="ok"></td>';
			echo '</tr>';

			if($row["type_serf"]==2 | $row["type_serf"]==4) {
				echo '<tr>';
					echo '<td><b>URL баннера</b></td>';
					echo '<td><input type="text" name="url_banner" maxlength="300" value="'.$row["description"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
			}else{
				echo '<tr>';
					echo '<td><b>Заголовок ссылки</b></td>';
					echo '<td><input type="text" name="title" maxlength="60" value="'.$row["title"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Краткое описание ссылки</b></td>';
					echo '<td><input type="text" name="description" maxlength="80" value="'.$row["description"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';

			echo '<span id="adv-title1" class="adv-title-close" onclick="ShowHideBlock(1);">Дополнительные настройки</span>';
			echo '<div id="adv-block1" style="display:none; margin:0; padding:0;">';

				echo '<table class="tables" style="margin:0 auto;">';

				if($row["type_serf"]!=2 | $row["type_serf"]!=4) {
					echo '<tr>';
						echo '<td width="240">Выделение цветом</td>';
						echo '<td>';
							echo '<select name="color" class="ok">';
								echo '<option value="0" '.("".$row["color"]."" == 0 ? 'selected="selected"' : false).'>НЕТ</option>';
								echo '<option value="1" '.("".$row["color"]."" == 1 ? 'selected="selected"' : false).'>ДА</option>';
							echo '</select>';
						echo '</td>';
					echo '</tr>';
				}

				echo '<tr>';
					echo '<td width="240">Активное окно</td>';
					echo '<td>';
						echo '<select name="active" class="ok">';
							echo '<option value="0" '.("".$row["active"]."" == 0 ? 'selected="selected"' : false).'>НЕТ</option>';
							echo '<option value="1" '.("".$row["active"]."" == 1 ? 'selected="selected"' : false).'>ДА</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Доступно для просмотра:</b></td>';
					echo '<td>';
						echo '<select name="revisit">';
							echo '<option value="0" '.("".$row["revisit"]."" == 0 ? 'selected="selected"' : false).'>Каждые 24 часа</option>';
							echo '<option value="1" '.("".$row["revisit"]."" == 1 ? 'selected="selected"' : false).'>Каждые 48 часов</option>';
							echo '<option value="2" '.("".$row["revisit"]."" == 2 ? 'selected="selected"' : false).'>1 раз</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">Уникальный IP</td>';
					echo '<td>';
						echo '<select name="unic_ip" id="unic_ip" onChange="obsch();" onClick="obsch();">';
							echo '<option value="0" '.("".$row["unic_ip"]."" == 0 ? 'selected="selected"' : false).'>Нет</option>';
							echo '<option value="1" '.("".$row["unic_ip"]."" == 1 ? 'selected="selected"' : false).'>Да (100% совпадение)</option>';
							echo '<option value="2" '.("".$row["unic_ip"]."" == 2 ? 'selected="selected"' : false).'>Усиленный по маске до 2 чисел (255.255.X.X)</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">Показывать только новичкам</td>';
					echo '<td>';
						echo '<select name="new_users" id="new_users" onChange="obsch();" onClick="obsch();">';
							echo '<option value="0" '.("".$row["new_users"]."" == 0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
							echo '<option value="1" '.("".$row["new_users"]."" == 1 ? 'selected="selected"' : false).'>Да (До 7 дней с момента регистрации)</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">Показывать</td>';
					echo '<td>';
						echo '<select name="no_ref" id="no_ref" onChange="obsch();" onClick="obsch();">';
							echo '<option value="0" '.("".$row["no_ref"]."" == 0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
							echo '<option value="1" '.("".$row["no_ref"]."" == 1 ? 'selected="selected"' : false).'>Пользователям без реферера на '.$_SERVER["HTTP_HOST"].'</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">По половому признаку</td>';
					echo '<td>';
						echo '<select name="sex_adv" id="sex_adv" onChange="obsch();" onClick="obsch();">';
							echo '<option value="0" '.("".$row["sex_adv"]."" == 0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
							echo '<option value="1" '.("".$row["sex_adv"]."" == 1 ? 'selected="selected"' : false).'>Только мужчины</option>';
							echo '<option value="2" '.("".$row["sex_adv"]."" == 2 ? 'selected="selected"' : false).'>Только женщины</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">Показывать только рефералам</td>';
					echo '<td>';
						echo '<select name="to_ref" id="to_ref" onChange="obsch();" onClick="obsch();">';
							echo '<option value="0" '.("".$row["to_ref"]."" == 0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
							echo '<option value="1" '.("".$row["to_ref"]."" == 1 ? 'selected="selected"' : false).'>Рефералам 1-го уровня</option>';
							echo '<option value="2" '.("".$row["to_ref"]."" == 2 ? 'selected="selected"' : false).'>Рефералам всех уровней</option>';
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>Контент 18+</td>';
					echo '<td><input type="checkbox" name="content" value="1" '.("".$row["content"]."" == 1 ? 'checked="checked"' : false).'> - на сайте присутствуют материалы для взрослых</td>';
				echo '</tr>';
				echo '</table>';

				echo '<table class="tables" style="margin:0 auto;">';
				echo '<tr>';
					echo '<td width="240">Ограничение показов в сутки</td>';
					if($row["limit_d"]>=$row["plan"]) {$row["limit_d"]=0;}
					echo '<td><input type="text" name="limit_d" value="'.$row["limit_d"].'" class="ok12" style="text-align:right;"></td>';
					echo '<td rowspan="2">(0 - без ограничений)</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>Ограничение показов в час</td>';
					if($row["limit_h"]>=$row["plan"]) {$row["limit_h"]=0;}
					echo '<td><input type="text" name="limit_h" value="'.$row["limit_h"].'" class="ok12" style="text-align:right;"></td>';
				echo '</tr>';
				echo '</table>';
			echo '</div>';

			echo '<span id="adv-title2" class="adv-title-close" onclick="ShowHideBlock(2);">Гео-таргетинг</span>';
			echo '<div id="adv-block2" class="tables" style="display:none; margin:0 auto; padding:0;">';
				echo '<table class="tables" style="margin:0 auto;">';
				echo '<tr>';
					echo '<td colspan="2" align="center"><a onclick="setChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
					echo '<td colspan="2" align="center"><a onclick="setChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
				echo '</tr>';
				include($_SERVER["DOCUMENT_ROOT"]."/advertise/func_geotarg_edit.php");
				echo '</table>';
			echo '</div>';

			echo '<table class="tables" style="margin:0 auto;">';
			echo '<tr>';
				echo '<td colspan="2" align="center"><input type="submit" value="Сохранить" class="sub-blue160" style="float:none;" /></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
		}else{
			echo '<span id="info-msg" class="msg-ok">Реклама с #'.$id.' не найдена!</span>';
		}
		exit();

	}elseif($option=="dell"){
		$sql = $mysqli->query("SELECT * FROM `tb_ads_youtube` WHERE `id`='$id'");
		if($sql->num_rows>0) {
			$mysqli->query("DELETE FROM `tb_ads_youtube` WHERE `id`='$id'") or die($mysqli->error);
			echo '<span id="info-msg" class="msg-error">Реклама успешно удалена!</span>';
		}

		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'")\', 2000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'"></noscript>';
	}
}

$sql_ads = $mysqli->query("SELECT * FROM `tb_ads_youtube` WHERE `status`>'0' AND `type_serf`!='10' $WHERE_ADD ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die($mysqli->error);
$all_ads = $sql_ads->num_rows;

echo '<table class="adv-cab" style="margin:0; padding:0; margin-bottom:1px;"><tr>';
echo '<td align="left" width="230" valign="middle" style="border-right:solid 1px #DDDDDD;">';
	if($WHERE_ADD=="") {
		echo 'Всего: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_ads.'</b> из <b>'.$count.'</b>';
	}else{
	 	echo 'Найдено: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_ads.'</b> из <b>'.$count.'</b>';
	}
echo '</td>';
echo '<td align="center" valign="middle" style="border-left:solid 2px #FFFFFF;">';
	echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
	echo '<table class="adv-cab" style="width:auto; margin:0; padding:0;">';
	echo '<tr align="center">';
		echo '<td nowrap="nowrap" width="60" style="margin:0; padding:2px; border:none;"><b>Поиск по:</b></td>';
		echo '<td nowrap="nowrap" width="100" align="center" style="margin:0; padding:2px; border:none;">';
			echo '<select name="metode" style="text-align:left; padding-left:3px;">';
				echo '<option value="id" '.("id" == $metode ? 'selected="selected"' : false).'>ID</option>';
				echo '<option value="merch_tran_id" '.("merch_tran_id" == $metode ? 'selected="selected"' : false).'>№ счета</option>';
				echo '<option value="status" '.("status" == $metode ? 'selected="selected"' : false).'>Статус</option>';
				echo '<option value="wmid" '.("wmid" == $metode ? 'selected="selected"' : false).'>WMID</option>';
				echo '<option value="username" '.("username" == $metode ? 'selected="selected"' : false).'>Логин</option>';
				echo '<option value="title" '.("title" == $metode ? 'selected="selected"' : false).'>Заголовок</option>';
				echo '<option value="description" '.("description" == $metode ? 'selected="selected"' : false).'>Описание</option>';
				echo '<option value="url" '.("url" == $metode ? 'selected="selected"' : false).'>URL</option>';
			echo '</select>';
		echo '</td>';

		echo '<td nowrap="nowrap" width="100" align="center" style="margin:0; padding:2px; border:none;">';
			echo '<select name="operator" style="text-align:center;">';
				echo '<option value="0" '.($operator == "0" ? 'selected="selected"' : false).' style="text-align:center;">=</option>';
				echo '<option value="1" '.($operator == "1" ? 'selected="selected"' : false).' style="text-align:center;">содержит</option>';
			echo '</select>';
		echo '</td>';

		echo '<td nowrap="nowrap" width="135" align="center" style="margin:0; padding:2px; border:none;"><input type="text" class="ok" style="height:18px; text-align:center;" name="search" value="'.$search.'"></td>';
		echo '<td nowrap="nowrap" width="85"  align="center" style="margin:0; padding:3px 0px 2px 6px; border:none;"><input type="submit" value="Поиск" class="sub-green" style="float:none;"></td>';
	echo '</tr>';

	echo '</table>';
	echo '</form>';
echo '</td>';

echo '<td align="center" width="160">';
	echo '<form method="get" action="">';
		echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
		echo '<input type="submit" value="Очистить поиск" class="sub-blue160" style="float:none;">';
	echo '</form>';
echo '</td>';

echo '</tr>';
echo '</table>';
echo '<div align="center" style="margin-bottom:20px;">Для поиска по <b>статусу</b> указать: <b>1</b> [активные], <b>2</b> [на паузе], <b>3</b> [завершили показ]</div>';

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}

echo '<table class="tables" style="margin:1px auto;">';
echo '<thead><tr align="center">';
	echo '<th>Статус</th>';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Информация</th>';
	echo '<th>Статистика</th>';
	echo '<th>Цена</th>';
	echo '<th>Действия</th>';
echo '</tr></thead>';
echo '<tbody>';

if($sql_ads->num_rows>0) {
	while ($row = $sql_ads->fetch_assoc()) {
		echo '<tr align="center">';
		echo '<td align="center" width="30" class="noborder1">';
			echo '<div id="playpauseimg'.$row["id"].'">';
				if($row["status"]=="1") {
					echo '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="play_pause('.$row["id"].', \'youtube\');"></span>';
				}elseif($row["status"]=="2") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="play_pause('.$row["id"].', \'youtube\');"></span>';
				}elseif($row["status"]=="3") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}
			echo '</div>';
		echo '</td>';

		echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';

		echo '<td>'.$row["wmid"].'<br>'.$row["username"].'</td>';
		echo '<td>'.$system_pay[$row["method_pay"]].'</td>';

		echo '<td align="left">';
			echo 'Тип серфинга: <b>'.$type_serf[$row["type_serf"]].'</b><br>';
			if($row["type_serf"]==2 | $row["type_serf"]==4) {
				echo 'URL&nbsp;сайта:&nbsp;<a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>40 ? "<b>".limitatexto($row["url"], 40)."</b>...." : "<b>".$row["url"]."</b>").'</a><br>';
				echo 'URL&nbsp;баннера:&nbsp;<a href="'.$row["description"].'" target="_blank" title="'.$row["description"].'">'.(strlen($row["url"])>40 ? "<b>".limitatexto($row["description"], 40)."</b>...." : "<b>".$row["description"]."</b>").'</a><br>';
			}else{
				echo 'Заголовок:&nbsp;'.(strlen($row["title"])>40 ? "<b>".limitatexto($row["title"],40)."</b>...." : "<b>".$row["title"]."</b>").'<br>';
				echo 'Описание:&nbsp;'.(strlen($row["description"])>40 ? "<b>".limitatexto($row["description"],40)."</b>...." : "<b>".$row["description"]."</b>").'<br>';
				echo 'URL&nbsp;сайта:&nbsp;<a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>40 ? "<b>".limitatexto($row["url"], 40)."</b>...." : "<b>".$row["url"]."</b>").'</a><br>';
			}
			echo 'Дата&nbsp;изменения:&nbsp;'.DATE("d.m.Y H:i", $row["date"]).'</b><br>';
			echo 'IP:&nbsp;'.$row["ip"];
		echo '</td>';

		echo '<td align="left">';
			if($row["nolimit"]>0) {
				echo 'Заказано:&nbsp;до&nbsp;<b>'.DATE("d.m.Y H:i", $row["nolimit"]).'</b><br>';
				echo 'Таймер:&nbsp;<b>'.$row["timer"].'</b>&nbsp;сек.<br>';
				echo 'Просмотров:&nbsp;<b>'.number_format($row["members"], 0, ".", "'").'</b><br>';
				echo 'Осталось:&nbsp;<b>'.($row["nolimit"]>time() ? date_ost(($row["nolimit"]-time()), 1) : '0 дней, <span style="color:#CCCCCC">показ завершен</span>').'</b>';
			}else{
				echo 'Заказано:&nbsp;<b>'.number_format($row["plan"], 0, ".", "'").'</b><br>';
				echo 'Таймер:&nbsp;<b>'.$row["timer"].'</b>&nbsp;сек.<br>';
				echo 'Просмотров:&nbsp;<b>'.number_format($row["members"], 0, ".", "'").'</b><br>';
				echo 'Осталось:&nbsp;<b>'.number_format($row["totals"], 0, ".", "'").'</b>';
			}
		echo '</td>';

		echo '<td>'.$row["money"].'</td>';

		echo '<td>';
			echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
				echo '<input type="hidden" name="op" value="'.limpiarez($_GET["op"]).'">';
				echo '<input type="hidden" name="page" value="'.$page.'">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="hidden" name="option" value="edit">';
				echo '<input type="submit" value="Изменить" class="sub-green">';
			echo '</form>';
			echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'" onClick=\'if(!confirm("Подтвердите удаление рекламы\nID рекламы: '.$row["id"].'")) return false;\'>';
				echo '<input type="hidden" name="op" value="'.limpiarez($_GET["op"]).'">';
				echo '<input type="hidden" name="page" value="'.$page.'">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="hidden" name="option" value="dell">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form>';
		echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr align="center"><td colspan="8"><b>Реклама не найдена.</b></td></tr>';
}
echo '</tbody>';
echo '</table>';

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}

?>