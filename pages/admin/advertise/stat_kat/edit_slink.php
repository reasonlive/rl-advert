<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование индексируемой ссылки</b></h3>';

$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
$system_pay[1] = "WebMoney";
$system_pay[2] = "RoboKassa";
$system_pay[3] = "LiqPay";
$system_pay[4] = "Interkassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[9] = "AdvCash (Advanced cash)";
$system_pay[10] = "Рекламный счет";
$system_pay[11] = "MEGAKASSA.RU";
$system_pay[12] = "Free-Kassa";

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
			$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),80) : false;
			$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
			$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"]))) ? intval(limpiarez(trim($_POST["plan"]))) : false;
			$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
			$laip = getRealIP();
			$black_url = @getHost($url);

			$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
			if($sql_bl->num_rows>0 && $black_url!=false) {
				$row = $sql_bl->fetch_array();
				echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).'!<br>Причина: '.$row["cause"].'</span>';
			}elseif($url==false | $url=="http://" | $url=="https://") {
				echo '<span class="msg-error">Не указана ссылка на сайт!</span>';
			}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
				echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
			}elseif(is_url($url)!="true") {
				echo is_url($url);
			}elseif($description==false) {
				echo '<span class="msg-error">Не заполнено поле Описание ссылки.</span><br>';
			}elseif($plan<1) {
				echo '<span class="msg-error">Минимальное количество дней - 1.</span><br>';
			}else{
				$save = "ok";

				$mysqli->query("UPDATE `tb_ads_kat` SET `wmid`='$wmid', `username`='$username', `date_end`=`date`+'".($plan*24*60*60)."', `plan`='$plan', `url`='$url', `description`='$description', `color`='$color' WHERE `id`='$id'") or die($mysqli->error);

				require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
				cache_stat_kat();

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
					if ((document.forms["formzakaz"].plan.value == '') | (document.forms["formzakaz"].plan.value < 1 )) {
						alert('Минимальное количество дней');
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

			$sql = $mysqli->query("SELECT * FROM `tb_ads_kat` WHERE `id`='$id' ORDER BY `id` ASC");
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
					echo '<td><input type="text" name="description" maxlength="60" value="'.$row["description"].'" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Количество дней:</b></td>';
					echo '<td><input type="text" name="plan" id="plan" maxlength="7" value="'.$row["plan"].'" class="ok12" style="text-align:right;" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td><b>Выделить цветом:</b></td>';
					echo '<td>';
						echo '<select name="color" id="color">';
						echo '<option value="0" '.("".$row["color"]."" == "0" ? 'selected="selected"' : false).'>Нет</option>';
						echo '<option value="1" '.("".$row["color"]."" == "1" ? 'selected="selected"' : false).'>Да</option>';
						echo '</select>';
					echo '</td>';
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
	}

	if($option=="dell") {
		$mysqli->query("DELETE FROM `tb_ads_kat` WHERE `id`='$id'") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_stat_kat();

		echo '<span class="msg-error">Реклама удалена.</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}


echo '<table>';
echo '<tr>';
	echo '<th>ID</th>';
	echo '<th>Счет №</th>';
	echo '<th>WMID</th>';
	echo '<th>Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Даты</th>';
	echo '<th>Тариф</th>';
	echo '<th>URL</th>';
	echo '<th>Описание</th>';
	echo '<th>Цвет</th>';
	echo '<th>IP</th>';
	echo '<th>Цена</th>';
	echo '<th></th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_kat` WHERE `status`='1' AND `date_end`>'".time()."' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
		echo '<td>'.$row["id"].'</td>';
		echo '<td>'.$row["merch_tran_id"].'</td>';
		echo '<td>'.$row["wmid"].'</td>';
		echo '<td>'.$row["username"].'</td>';
		echo '<td align="left">'.$system_pay[$row["method_pay"]].'</td>';

		echo '<td>от: '.DATE("d.m.Y H:i",$row["date"]).'<br>до: '.DATE("d.m.Y H:i",$row["date_end"]).'</td>';
		echo '<td>'.$row["plan"].'</td>';

		if(strlen($row["url"])>40) {
			echo '<td align="left"><a href="'.$row["url"].'" target="_blank">'.limitatexto($row["url"],40).' ...</a></td>';
		}else{
			echo '<td align="left"><a href="'.$row["url"].'" target="_blank">'.$row["url"].'</a></td>';
		}

		if(strlen($row["description"])>40) {
			echo '<td align="left">'.limitatexto($row["description"],40).' ....</td>';
		}else{
			echo '<td align="left">'.$row["description"].'</td>';
		}

		if($row["color"]=="1") {
			echo '<td align="center">Да</td>';
		}else{
			echo '<td>Нет</td>';
		}

		echo '<td>'.$row["ip"].'</td>';
		echo '<td>'.$row["money"].' руб.</td>';
		echo '<td>';
			echo '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
				echo '<input type="hidden" name="op" value="'.limpiarez($_GET["op"]).'">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="hidden" name="option" value="edit">';
				echo '<input type="submit" value="Изменить" class="sub-green">';
			echo '</form>';
			echo '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';
				echo '<input type="hidden" name="op" value="'.limpiarez($_GET["op"]).'">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="hidden" name="option" value="dell">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form>';
		echo '</td>';
		echo '</tr>';
	}
}
echo '</table>';
?>