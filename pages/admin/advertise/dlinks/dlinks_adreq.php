<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы динамических ссылок</b></h1>';
require($_SERVER['DOCUMENT_ROOT']."/merchant/func_mysql.php");

$system_pay[-2] = "Test Drive";
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

$type_serf[1] = "Динамический";
$type_serf[2] = "Баннерный";
$type_serf[3] = "Динамический-VIP";
$type_serf[4] = "Баннерный-VIP";
$type_serf[-1] = "Тест драйв";

if(count($_GET)>0) {
	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if($option=="add") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_dlink` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_dlink` SET `status`='1' WHERE `id`='$id'") or die($mysqli->error);

			echo '<span id="info-msg" class="msg-ok">Реклама успешно добавлена!</span>';
		}
	}

	if($option=="delete") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_dlink` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("DELETE FROM `tb_ads_dlink` WHERE `id`='$id'") or die($mysqli->error);

			echo '<span id="info-msg" class="msg-error">Реклама успешно удалена!</span>';
		}
	}


	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1550);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

echo '<table class="tables" style="margin:1px auto;">';
echo '<thead><tr align="center">';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Информация</th>';
	echo '<th>Статистика</th>';
	echo '<th>Цена</th>';
	echo '<th>IP</th>';
	echo '<th>Действия</th>';
echo '</tr></thead>';
echo '<tbody>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_dlink` WHERE `status`='0' AND `type_serf`!='10' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';

		echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';
		echo '<td>'.$row["wmid"].'<br>'.$row["username"].'</td>';
		echo '<td>'.$system_pay[$row["method_pay"]].'</td>';

		echo '<td align="left">';
			echo '<b>Тип серфинга:</b> '.$type_serf[$row["type_serf"]].'<br>';
			if($row["type_serf"]==2 | $row["type_serf"]==4) {
				echo '<b>URL сайта:</b> <a href="'.$row["url"].'" target="_blank">'.$row["url"].'</a><br>';
				echo '<b>URL баннера:</b> <a href="'.$row["description"].'" target="_blank">'.$row["description"].'</a><br>';
			}else{
				echo '<b>Заголовок:</b> '.$row["title"].'<br>';
				echo '<b>Описание:</b> '.$row["description"].'<br>';
				echo '<b>URL сайта:</b> <a href="'.$row["url"].'" target="_blank">'.$row["url"].'</a><br>';
			}
		echo '</td>';

		echo '<td align="left">';
			echo '<b>Дата заказа:</b> '.DATE("d.m.Y H:i", $row["date"]).'<br>';
			if($row["nolimit"]>0) {
				echo '<b>Заказано:</b> до '.DATE("d.m.Y H:i",$row["nolimit"]).'<br>';
				echo '<b>Таймер:</b> '.$row["timer"].' сек.<br>';
			}else{
				echo '<b>Заказано:</b> '.$row["plan"].' просмотров<br>';
				echo '<b>Таймер:</b> '.$row["timer"].' сек.<br>';
			}
		echo '</td>';

		echo '<td>'.$row["money"].' руб.</td>';
		echo '<td>'.$row["ip"].'</td>';
		echo '<td width="95">';
			echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'" onClick=\'if(!confirm("Добавить рекламу с ID: '.$row["id"].'")) return false;\'>';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="add">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Добавить" class="sub-green">';
			echo '</form>';
			echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'" onClick=\'if(!confirm("Удалить рекламу с ID: '.$row["id"].'")) return false;\'>';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="delete">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form>';
		echo '</td>';

		echo '</tr>';
	}
}else{
	echo '<tr align="center"><td colspan="8"><b>Неоплаченных заказов нет</b></td></tr>';
}
echo '</tbody>';
echo '</table>';
?>