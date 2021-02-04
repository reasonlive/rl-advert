<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка процента от пополнения рекламного счета</b></h3>';

if(count($_POST)>0) {
	$bon_pay_summa = isset($_POST["bon_pay_summa"]) ? number_format(floatval($_POST["bon_pay_summa"]), 2, ".", "") : 0;
	$bon_pay_reiting = isset($_POST["bon_pay_reiting"]) ? number_format(floatval($_POST["bon_pay_reiting"]), 2, ".", "") : 0;
	$bon_pay_money = isset($_POST["bon_pay_money"]) ? number_format(floatval($_POST["bon_pay_money"]), 2, ".", "") : 0;
	$bon_pay_status = isset($_POST["bon_pay_status"]) ? $_POST["bon_pay_status"] : 0;
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_pay_summa' WHERE `item`='bon_pay_summa'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_pay_reiting' WHERE `item`='bon_pay_reiting'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_pay_money' WHERE `item`='bon_pay_money'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_pay_status' WHERE `item`='bon_pay_status'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1000);
		HideMsg("info-msg", 1050);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_status'");
$bon_pay_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_summa'");
$bon_pay_summa = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_money'");
$bon_pay_money = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_reiting'");
$bon_pay_reiting = $sql->fetch_object()->price;

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:500px;">';
echo '<thead><tr align="center"><th>Параметр</th><th width="120">Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Включить/выключить бонус при пополнении</b>, <br>(1-включено; 0-выключено)</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_pay_status" value="'.$bon_pay_status.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Давать приз если пополнение больше(равно) чем</b>, руб</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_pay_summa" value="'.number_format($bon_pay_summa, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Бонус рейтинга</b>, ед.</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_pay_reiting" value="'.number_format($bon_pay_reiting, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Бонус денег</b>, руб</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_pay_money" value="'.number_format($bon_pay_money, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить" class="sub-green"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>