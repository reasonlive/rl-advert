<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы статической ссылки</b></h3>';

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
$system_pay[10] = "Рекламный счет";

if(isset($_POST["id"])) {

	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if($option=="add")	{
		$mysqli->query("UPDATE `tb_ads_kat` SET `status`='1' WHERE `id`='$id'") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_stat_kat();

		echo '<span class="msg-ok">Реклама добавлена.</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}

	if($option=="dell") {
		$mysqli->query("DELETE FROM `tb_ads_kat` WHERE `id`='$id'") or die($mysqli->error);

		require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
		cache_stat_kat();

		echo '<span class="msg-error">Заказ удален.</span>';
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
	echo '<th>Дата заказа</th>';
	echo '<th>Тариф</th>';
	echo '<th>URL</th>';
	echo '<th>Описание</th>';
	echo '<th>Цвет</th>';
	echo '<th>IP</th>';
	echo '<th>Цена</th>';
	echo '<th></th>';
	echo '<th></th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_kat` WHERE `status`='0' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
		echo '<td>'.$row["id"].'</td>';
		echo '<td>'.$row["merch_tran_id"].'</td>';
		echo '<td>'.$row["wmid"].'</td>';
		echo '<td>'.$row["username"].'</td>';
		echo '<td>'.$system_pay[$row["method_pay"]].'</td>';

		echo '<td>'.DATE("d.m.Y H:i",$row["date"]).'</td>';
		echo '<td>'.$row["plan"].'</td>';

		if(strlen($row["url"])>40) {
			echo '<td><a href="'.$row["url"].'" target="_blank">'.limitatexto($row["url"],40).' ...</a></td>';
		}else{
			echo '<td><a href="'.$row["url"].'" target="_blank">'.$row["url"].'</a></td>';
		}

		if(strlen($row["description"])>40) {
			echo '<td>'.limitatexto($row["description"],40).' ....</td>';
		}else{
			echo '<td>'.$row["description"].'</td>';
		}

		if($row["color"]=="1") {
			echo '<td align="center">Да</td>';
		}else{
			echo '<td>Нет</td>';
		}

		echo '<td>'.$row["ip"].'</td>';
		echo '<td>'.$row["money"].' руб.</td>';
		echo '<td>';
			echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&option=add">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Добавить" class="sub-green">';
			echo '</form>';
		echo '</td>';
		echo '<td>';
			echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&option=dell">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form>';
		echo '</td>';
		echo '</tr>';
	}
}
echo '</table>';
?>