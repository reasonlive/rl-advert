<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование текстового объявления</b></h3>';

$mysqli->query("UPDATE `tb_ads_txt` SET `status`='3' WHERE `status`>'0' AND `date_end`<'".time()."'") or die($mysqli->error);

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
$perpage = 25;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_txt` WHERE `status`>'0' $WHERE_ADD");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
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
$system_pay[10] = "Рекл. счет";

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

if(isset($_GET["option"])) {
	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if($option=="edit") {

		if(count($_POST)>0) {
			$wmid = (isset($_POST["wmid"]) && preg_match("|^[\d]{12}$|", trim($_POST["wmid"]))) ? limpiarez(trim($_POST["wmid"])) : false;
			$username = (isset($_POST["username"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_POST["username"]))) ? uc($_POST["username"]) : false;
			$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),255) : false;
			$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
			$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"]))) ? intval(limpiarez(trim($_POST["plan"]))) : false;
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
				$save = "ok";

				$mysqli->query("UPDATE `tb_ads_txt` SET `wmid`='$wmid', `username`='$username', `date_end`=`date`+'".($plan*24*60*60)."', `plan`='$plan', `url`='$url', `description`='$description' WHERE `id`='$id'") or die($mysqli->error);

				require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
				cache_txt_links();

				echo '<span class="msg-ok">Изменения успешно сохранены.</span>';
				echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'")\', 2000); </script>';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'"></noscript>';
			}
		}

		if(!isset($save)) {
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
						arrayElem[i+3].style.background = "#FFDBDB";
						arrayElem[i+3].focus();
						return false;
					}else{
						arrayElem[i+3].style.background = "#FFFFFF";
					}
					if ((document.forms["formzakaz"].description.value == '')) {
						alert('Вы не указали Описание ссылки');
						arrayElem[i+4].style.background = "#FFDBDB";
						arrayElem[i+4].focus();
						return false;
					}else{
						arrayElem[i+4].style.background = "#FFFFFF";
					}
					if ((document.forms["formzakaz"].plan.value == '')) {
						alert('Вы не указали количество дней');
						arrayElem[i+5].style.background = "#FFDBDB";
						arrayElem[i+5].focus();
						return false;
					}else{
						arrayElem[i+5].style.background = "#FFFFFF";
					}
					if ((document.forms["formzakaz"].plan.value < 1 )) {
						alert('Минимальное количество дней 1');
						arrayElem[i+5].style.background = "#FFDBDB";
						arrayElem[i+5].focus();
						return false;
					}else{
						arrayElem[i+5].style.background = "#FFFFFF";
					}
				}
				document.forms["formzakaz"].submit();
				return true;
			}
			</script><?php

			$sql = $mysqli->query("SELECT * FROM `tb_ads_txt` WHERE `id`='$id' ORDER BY `id` ASC");
			if($sql->num_rows>0) {
				$row = $sql->fetch_array();

				echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
				echo '<table class="tables">';
				echo '<thead><tr>';
					echo '<th class="top">Параметр</a>';
					echo '<th class="top">Значение</a>';
				echo '</thead></tr>';
				echo '<tbody>';
				echo '<tr>';
					echo '<td width="160"><b>№</b></td>';
					echo '<td><input type="hidden" name="id" value="'.$row["id"].'">'.$row["id"].'</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Счет №</b></td>';
					echo '<td>'.$row["merch_tran_id"].'</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td width="160"><b>WMID:</b></td>';
					echo '<td><input type="text" name="wmid" maxlength="160" value="'.$row["wmid"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td width="160"><b>Логин:</b></td>';
					echo '<td><input type="text" name="username" maxlength="160" value="'.$row["username"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td width="160"><b>URL сайта (ссылка):</b></td>';
					echo '<td><input type="text" name="url" maxlength="160" value="'.$row["url"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Описание ссылки:</b></td>';
					echo '<td>';
						echo '<textarea name="description" id="desc" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';" onChange="descchange();" onKeyUp="descchange();">'.$row["description"].'</textarea>';
						echo '<div align="right" id="count" style="color:#696969;"></div>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Количество дней:</b></td>';
					echo '<td><input type="text" name="plan" id="plan" maxlength="7" value="'.$row["plan"].'" class="ok12" style="text-align:right;" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td colspan="2"><div align="center"><input type="submit" value="Сохранить" class="sub-blue" /></div></td>';
				echo '</tr>';
				echo '</tbody>';
				echo '</table>';
				echo '</form>';
			}else{
				echo '<span class="msg-error">Реклама не найдена.</span>';
			}
		}
		exit();
	}

	if($option=="dell") {
		$mysqli->query("DELETE FROM `tb_ads_txt` WHERE `id`='$id'") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_txt_links();

		echo '<span class="msg-error">Реклама удалена.</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}

$sql_ads = $mysqli->query("SELECT * FROM `tb_ads_txt` WHERE `status`>'0' $WHERE_ADD ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die($mysqli->error);
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
echo '<tr>';
	echo '<th>Статус</th>';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Даты</th>';
	echo '<th>Информация</th>';
	echo '<th>Цена</th>';
	echo '<th></th>';
echo '</tr></thead>';
echo '<tbody>';

if($sql_ads->num_rows>0) {
	while ($row = $sql_ads->fetch_assoc()) {
		echo '<tr align="center">';
		echo '<td align="center" width="30" class="noborder1">';
			echo '<div id="playpauseimg'.$row["id"].'">';
				if($row["status"]=="0") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}elseif($row["status"]=="1" && $row["date_end"]>time()) {
					echo '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="alert_nopause();"></span>';
				}elseif($row["status"]=="1" && $row["date_end"]<time()) {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}elseif($row["status"]=="2") {

				}elseif($row["status"]=="3") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}else{

				}
			echo '</div>';
		echo '</td>';

		echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';
		echo '<td>'.$row["wmid"].'<br>'.$row["username"].'</td>';
		echo '<td>'.$system_pay[$row["method_pay"]].'</td>';

		echo '<td>';
			echo DATE("d.m.Y H:i",$row["date"]).'<br>';
			echo DATE("d.m.Y H:i",$row["date_end"]);
		echo '</td>';

		echo '<td align="left">';
			echo 'Объявление: <b>'.(strlen($row["description"])>50 ? limitatexto($row["description"],50)."...." : $row["description"]).'</b><br>';
			echo 'URL: '.'<a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>60 ? limitatexto($row["url"],60)."...." : $row["url"]).'</a><br>';
			echo 'IP: <b>'.$row["ip"].'</b>';
		echo '</td>';

		echo '<td>'.$row["money"].'&nbsp;руб.</td>';
		echo '<td>';
			echo '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
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
	echo '<tr align="center">';
		echo '<td colspan="8"><b>Реклама не найдена!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}

?>