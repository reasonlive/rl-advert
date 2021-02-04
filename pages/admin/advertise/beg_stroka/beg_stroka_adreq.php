<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы Бегущая строка</b></h3>';

$mysqli->query("UPDATE `tb_ads_beg_stroka` SET `status`='3' WHERE `status`>'0' AND `date_end`<'".time()."'") or die($mysqli->error);

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

if(isset($_GET["id"])) {

	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if($option=="add") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_beg_stroka` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_beg_stroka` SET `status`='1', `date`='".time()."', `date_end`=`plan`*'".(24*60*60)."'+'".time()."' WHERE `id`='$id'") or die($mysqli->error);

			require_once($_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
			cache_beg_stroka();

			echo '<span id="info-msg" class="msg-ok">Реклама добавлена.</span>';
		}
	}

	if($option=="dell") {
		$sql = $mysqli->query("SELECT * FROM `tb_ads_beg_stroka` WHERE `id`='$id' AND `status`='0'");
		if($sql->num_rows>0) {
			$mysqli->query("DELETE FROM `tb_ads_beg_stroka` WHERE `id`='$id'") or die($mysqli->error);

			require_once($_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
			cache_beg_stroka();

			echo '<span id="info-msg" class="msg-error">Заказ удален.</span>';
		}

	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

echo '<table>';
echo '<tr align="center">';
	echo '<th>ID Счет&nbsp;№</th>';
	echo '<th>WMID Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Информация</th>';
	echo '<th>Дата заказа</th>';
	echo '<th>Кол-во суток</th>';
	echo '<th>Цена</th>';
	echo '<th colspan="2"></th>';
echo '</tr>';


$sql = $mysqli->query("SELECT * FROM `tb_ads_beg_stroka` WHERE `status`='0' ORDER BY `id` ASC");
if($sql->num_rows($sql)>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'<br>'.$row["merch_tran_id"].'</td>';
			echo '<td>'.($row["wmid"]!=false ? $row["wmid"]."<br>".$row["username"] : $row["username"]).'</td>';
			echo '<td>'.$system_pay[$row["method_pay"]].'</td>';
			echo '<td align="left">';
				echo 'Описание: <b '.($row["color"]=="1" ? 'style="color:#FF0000;"' : false).'>'.(strlen($row["description"])>50 ? limitatexto($row["description"],50)."...." : $row["description"]).'</b><br>';
				echo 'URL: '.'<a href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.(strlen($row["url"])>60 ? limitatexto($row["url"],60)."...." : $row["url"]).'</a><br>';
				echo 'IP: <b>'.$row["ip"].'</b>';
			echo '</td>';
			echo '<td>'.DATE("d.m.Y H:i",$row["date"]).'</td>';
			echo '<td>'.$row["plan"].'</td>';
			echo '<td>'.number_format($row["money"], 2, ".", " ").' руб.</td>';

			echo '<td width="80">';
				echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
					echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="hidden" name="option" value="add">';
					echo '<input type="submit" value="Добавить" class="sub-green">';
				echo '</form>';
			echo '</td>';
			echo '<td width="80">';
				echo '<form method="GET" action="'.$_SERVER["PHP_SELF"].'">';
					echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="hidden" name="option" value="dell">';
					echo '<input type="submit" value="Удалить" class="sub-red">';
				echo '</form>';
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr align="center">';
		echo '<td colspan="10"><b>Не оплаченных заказов рекламы нет!</b></td>';
	echo '</tr>';
}
echo '</table>';

?>