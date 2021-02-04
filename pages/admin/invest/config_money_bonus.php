<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка процента от пополнения баланса инвестора</b></h3>';

if(count($_POST)>0) {
	$bon_inv_summa = isset($_POST["bon_inv_summa"]) ? number_format(floatval($_POST["bon_inv_summa"]), 2, ".", "") : 0;
	$bon_inv_reiting = isset($_POST["bon_inv_reiting"]) ? number_format(floatval($_POST["bon_inv_reiting"]), 2, ".", "") : 0;
	$bon_inv_money = isset($_POST["bon_inv_money"]) ? number_format(floatval($_POST["bon_inv_money"]), 2, ".", "") : 0;
	$bon_inv_status = isset($_POST["bon_inv_status"]) ? $_POST["bon_inv_status"] : 0;
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_inv_summa' WHERE `item`='bon_inv_summa'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_inv_reiting' WHERE `item`='bon_inv_reiting'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_inv_money' WHERE `item`='bon_inv_money'") or die($mysqli->error);
		
		$mysqli->query("UPDATE `tb_config` SET `price`='$bon_inv_status' WHERE `item`='bon_inv_status'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1000);
		HideMsg("info-msg", 1050);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_status'");
$bon_inv_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_summa'");
$bon_inv_summa = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_money'");
$bon_inv_money = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_reiting'");
$bon_inv_reiting = $sql->fetch_object()->price;

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:500px;">';
echo '<thead><tr align="center"><th>Параметр</th><th width="120">Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Включить/выключить бонус при пополнении</b>, <br>(1-включено; 0-выключено)</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_inv_status" value="'.$bon_inv_status.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Давать приз если пополнение больше(равно) чем</b>, руб</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_inv_summa" value="'.number_format($bon_inv_summa, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Бонус рейтинга</b>, ед.</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_inv_reiting" value="'.number_format($bon_inv_reiting, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Бонус денег</b>, руб</td>';
		echo '<td align="center"><input type="text" class="ok12" name="bon_inv_money" value="'.number_format($bon_inv_money, 2, ".", "").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить" class="sub-green"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>