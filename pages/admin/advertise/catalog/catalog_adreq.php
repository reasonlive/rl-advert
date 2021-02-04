<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
$mysqli->query("UPDATE `tb_ads_catalog` SET `status`='3' WHERE `status`>'0' AND `date_end`<'".time()."'") or die($mysqli->error);

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы ссылок в каталоге</b></h3>';

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

if(count($_POST)>0) {
	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;
	$option = (isset($_POST["option"])) ? limpiar($_POST["option"]) : false;

	if($option=="add") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_catalog` SET `status`='1', `date`='".time()."', `date_end`=`plan`*'".(24*60*60)."'+'".time()."' WHERE `id`='$id'") or die($mysqli->error);

			require_once(ROOT_DIR."/merchant/func_cache.php");
			cache_catalog();

			echo '<span id="info-msg" class="msg-ok">Реклама добавлена.</span>';
		}
	}

	if($option=="dell") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("DELETE FROM `tb_ads_catalog` WHERE `id`='$id'") or die($mysqli->error);

			require_once(ROOT_DIR."/merchant/func_cache.php");
			cache_catalog();

			echo '<span id="info-msg" class="msg-error">Реклама удалена.</span>';
		}

	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1400);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


echo '<table class="tables" style="margin:2px auto; padding:0px;">';
echo '<thead><tr align="center">';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Информация</th>';
	echo '<th>Даты</th>';
	echo '<th>Кол-во суток</th>';
	echo '<th>Цена</th>';
	echo '<th colspan="2"></th>';
echo '</tr><thead>';
echo '<tbody>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `status`='0' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_assoc()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';
			echo '<td>'.($row["wmid"]!=false ? $row["wmid"]."<br>".$row["username"] : $row["username"]).'</td>';
			echo '<td>'.$system_pay[$row["method_pay"]].'</td>';
			echo '<td align="left">';
				echo 'Заголовок: <b '.($row["color"]=="1" ? 'style="color:#FF0000;"' : false).'>'.(strlen($row["title"])>50 ? limitatexto($row["title"],50)."...." : $row["title"]).'</b><br>';
				echo 'URL: '.'<a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>60 ? "<b>".limitatexto($row["url"],60)."</b>...." : "<b>".$row["url"]."</b>").'</a><br>';
				echo 'IP: <b>'.$row["ip"].'</b>';
			echo '</td>';
			echo '<td>от '.DATE("d.m.Y H:i", $row["date"]).'<br>до '.DATE("d.m.Y H:i", $row["date_end"]).'</td>';
			echo '<td>'.$row["plan"].'</td>';
			echo '<td>'.number_format($row["money"], 2, ".", " ").' руб.</td>';

			echo '<td width="80">';
				echo '<form method="POST" action="">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="hidden" name="option" value="add">';
					echo '<input type="submit" value="Добавить" class="sub-green">';
				echo '</form>';
			echo '</td>';
			echo '<td width="80">';
				echo '<form method="POST" action="">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="hidden" name="option" value="dell">';
					echo '<input type="submit" value="Удалить" class="sub-red">';
				echo '</form>';
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr align="center">';
		echo '<td colspan="9"><b>Не оплаченных заказов рекламы нет!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';

?>