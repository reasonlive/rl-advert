<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require(DOC_ROOT."/advertise/func_load_banners.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование статических баннеров</b></h3>';

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

$mysqli->query("UPDATE `tb_ads_banner` SET `status`='3' WHERE `status`>'0' AND `date_end`<'".time()."'") or die($mysqli->error);

$type_banner_arr = array(
	"468x60" 	=> "(все страницы, в шапке сайта)", 
	"468x60_frm" 	=> "(во фрейме просмотра рекламы)", 
	"200x300" 	=> "(все страницы)", 
	"100x100" 	=> "(все страницы)",
	"728x90" 	=> "(главная страница)"
);

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
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_banner` WHERE `status`>'0' $WHERE_ADD");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

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
			$wmid = (isset($_POST["wmid"]) && preg_match("|^[0-9\+]{5,30}$|", trim($_POST["wmid"]))) ? limpiarez(trim($_POST["wmid"])) : false;
			$username = (isset($_POST["username"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_POST["username"]))) ? uc($_POST["username"]) : false;
			$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]),300) : false;
			$urlbanner = (isset($_POST["urlbanner"])) ? limitatexto(limpiarez($_POST["urlbanner"]),300) : false;
			$type_banner = (isset($_POST["type_banner"])) ? limpiarez($_POST["type_banner"]) : false;
			$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) > 0) ? intval(limpiarez(trim($_POST["plan"]))) : false;
			$laip = getRealIP();
			$black_url = getHost($url);
			$black_url_ban = getHost($url);

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

				$save = "ok";
				$mysqli->query("DELETE FROM `tb_ads_banner` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_banner` SET `wmid`='$wmid',`username`='$username',`type`='$type_banner',`plan`='$plan',`date`='".time()."',`date_end`=`date`+'".($plan*24*60*60)."',`url`='$url',`urlbanner`='$urlbanner',`urlbanner_load`='$urlbanner_load' WHERE `id`='$id'") or die($mysqli->error);

				require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/func_cache.php");
				cache_banners();

				echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены.</span>';

				echo '<script type="text/javascript">
					setTimeout(function() {
						window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).(isset($_GET["page"]) ? "&page=".intval($_GET["page"]) : false).'");
					}, 1550);
					HideMsg("info-msg", 1500);
				</script>';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).(isset($_GET["page"]) ? "&page=".intval($_GET["page"]) : false).'"></noscript>';
			}
		}

		if(!isset($save)) {
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

			$sql = $mysqli->query("SELECT * FROM `tb_ads_banner` WHERE `id`='$id' AND `status`>'0' ORDER BY `id` ASC");
			if($sql->num_rows>0) {
				$row = $sql->fetch_array();

				$wh = explode("x", str_replace("_frame", "", $row["type"]));
				$w = $wh["0"];
				$h = $wh["1"];

				echo '<form method="POST" action="" onSubmit="return SbmFormB(); return false;" id="newform">';
				echo '<table class="tables">';
				echo '<thead><tr>';
					echo '<th class="top">Параметр</a>';
					echo '<th class="top">Значение</a>';
					echo '<th class="top" width="234">Баннер</a>';
				echo '</thead></tr>';
				echo '<tbody>';
				echo '<tr>';
					echo '<td width="100"><b>№</b></td>';
					echo '<td><input type="hidden" name="id" value="'.$row["id"].'">'.$row["id"].'</td>';
					echo '<td rowspan="9" align="center"><a href="'.$row["url"].'" target="_blank"><img src="//'.$_SERVER["HTTP_HOST"].''.$row["urlbanner_load"].'" width="'.(($w!=100 && $h!=100) ? $w/2 : $w).'" height="'.(($w!=100 && $h!=100) ? $h/2 : $h).'" border="0" alt="" title="" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Счет №</b></td>';
					echo '<td>'.$row["merch_tran_id"].'</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td width="200"><b>WMID:</b></td>';
					echo '<td><input type="text" name="wmid" maxlength="160" value="'.$row["wmid"].'" class="ok12" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Логин:</b></td>';
					echo '<td><input type="text" name="username" maxlength="160" value="'.$row["username"].'" class="ok12" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>Баннер</b></td>';
					echo '<td align="left">';
						echo '<select id="type_banner" name="type_banner" onChange="PlanChange();" onClick="PlanChange();">';
							foreach ($type_banner_arr as $key => $val) {
								$size_banner_arr = explode("_", $key);
								$size_banner = $size_banner_arr[0];
								echo '<option value="'.$key.'" '.($row["type"] == $key ? 'selected="selected"' : false).'>'.$size_banner.' '.$val.'</option>';
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
					echo '<td align="left"><input type="text" id="url" name="url" maxlength="300" value="'.$row["url"].'" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>URL баннера</b> (включая http://)</td>';
					echo '<td align="left"><input type="text" id="urlbanner" name="urlbanner" maxlength="300" value="'.$row["urlbanner"].'" class="ok" onKeyDown="$(this).attr(\'class\', \'ok\');"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left">Количество дней</td>';
					echo '<td align="left"><input type="text" id="plan" name="plan" maxlength="7" value="'.$row["plan"].'" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeyUp="PlanChange();" onKeyDown="$(this).attr(\'class\', \'ok12\');">&nbsp;&nbsp;&nbsp;(минимум 1)</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td colspan="2"><div align="center"><input type="submit" value="Сохранить изменения" class="sub-blue160" /></div></td>';
				echo '</tr>';
				echo '</tbody>';
				echo '</table>';
				echo '</form>';
			}else{
				echo '<span id="info-msg" class="msg-error">Реклама не найдена.</span>';
			}

			exit();
		}
	}

	if($option=="dell") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_banner` WHERE `id`='$id' AND `status`>'0'");
		if($sql->num_rows>0) {
			$mysqli->query("DELETE FROM `tb_ads_banner` WHERE `id`='$id'") or die($mysqli->error);

			require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/func_cache.php");
			cache_banners();

			echo '<span id="info-msg" class="msg-error">Реклама успешно удалена!</span>';
		}

		echo '<script type="text/javascript">
			setTimeout(function() {
				window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).(isset($_GET["page"]) ? "&page=".intval($_GET["page"]) : false).'");
			}, 1550);
			HideMsg("info-msg", 1500);
		</script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).(isset($_GET["page"]) ? "&page=".intval($_GET["page"]) : false).'"></noscript>';
	}
}


$sql_ads = $mysqli->query("SELECT * FROM `tb_ads_banner` WHERE `status`>'0' $WHERE_ADD ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die($mysqli->error);
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
	echo '<th></th>';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Баннер</th>';
	echo '<th>Информация</th>';
	echo '<th>Цена</th>';
	echo '<th></th>';
echo '</tr></thead>';
echo '<tbody>';

if($sql_ads->num_rows>0) {
	while ($row = $sql_ads->fetch_assoc()) {
		$size_banner_arr = explode("_", $row["type"]);
		$size_banner = $size_banner_arr[0];

		$wh = explode("x", $size_banner);
		$w = $wh["0"];
		$h = $wh["1"];

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
					}
				echo '</div>';
			echo '</td>';
			echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';
			echo '<td>'.($row["wmid"]!=false ? $row["wmid"]."<br>".$row["username"] : $row["username"]).'</td>';
			echo '<td>'.$system_pay[$row["method_pay"]].'</td>';
			echo '<td>';
				echo '<a href="'.$row["url"].'"><img src="//'.$_SERVER["HTTP_HOST"].''.$row["urlbanner_load"].'" width="'.(($w!=100 && $h!=100) ? $w/2 : $w).'" height="'.(($w!=100 && $h!=100) ? $h/2 : $h).'" border="0" alt="" title="" /></a>';
				echo '<br><br>Баннер: <b>'.$size_banner.'</b> '.(array_key_exists($row["type"], $type_banner_arr) ? $type_banner_arr[$row["type"]] : false);
			echo '</td>';
			echo '<td align="left">';
				echo 'URL сайта: <a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>40 ? "<b>".limitatexto($row["url"], 40)."</b>...." : "<b>".$row["url"]."</b>").'</a><br>';
				echo 'URL баннера: <a href="'.$row["urlbanner"].'" target="_blank" title="'.$row["urlbanner"].'">'.(strlen($row["urlbanner"])>40 ? "<b>".limitatexto($row["urlbanner"], 40)."</b>...." : "<b>".$row["urlbanner"]."</b>").'</a><br>';
				echo 'Дата заказа: <b>'.DATE("d.m.Y H:i", $row["date"]).'</b><br>';
				echo 'Дата окончания: <b>'.DATE("d.m.Y H:i", $row["date_end"]).'</b><br>';
				echo 'Заказано, дней: <b>'.$row["plan"].'</b><br>';
				echo 'IP: <b>'.$row["ip"].'</b>';
			echo '</td>';
			echo '<td>'.number_format($row["money"], 2, ".", " ").' руб.</td>';

			echo '<td width="95">';
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
	echo '<tr align="center">';
		echo '<td colspan="10"><b>Реклама не найдена!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}

?>