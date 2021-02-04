<?php
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка игры "Бонус Шанс"</b></h1>';

if(count($_POST)>0) {
	
	$shans_bonus_cena = isset($_POST["shans_bonus_cena"]) ? number_format(abs(str_replace(",", ".", trim($_POST["shans_bonus_cena"]))), 2, ".", "") : false;
	$shans_bonus_reit = isset($_POST["shans_bonus_reit"]) ? number_format(abs(str_replace(",", ".", trim($_POST["shans_bonus_reit"]))), 2, ".", "") : false;
	$shans_bonus_sum = isset($_POST["shans_bonus_sum"]) ? number_format(abs(str_replace(",", ".", trim($_POST["shans_bonus_sum"]))), 2, ".", "") : false;
	$shans_bonus_reit_igr = isset($_POST["shans_bonus_reit_igr"]) ? number_format(abs(str_replace(",", ".", trim($_POST["shans_bonus_reit_igr"]))), 2, ".", "") : false;

	
	$mysqli->query("UPDATE `tb_config` SET `price`='$shans_bonus_cena' WHERE `item`='shans_bonus_cena' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$shans_bonus_reit' WHERE `item`='shans_bonus_reit' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$shans_bonus_sum' WHERE `item`='shans_bonus_sum' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$shans_bonus_reit_igr' WHERE `item`='shans_bonus_reit_igr' AND `howmany`='1'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1200);
		HideMsg("info-msg", 1210);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='shans_bonus_cena'");
$shans_bonus_cena = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='shans_bonus_reit'");
$shans_bonus_reit = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='shans_bonus_sum'");
$shans_bonus_sum = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='shans_bonus_reit_igr'");
$shans_bonus_reit_igr = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left"><td><b>Cтоимость билета</b>, ( руб. )</td><td><input type="text" class="ok12" name="shans_bonus_cena" value="'.$shans_bonus_cena.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление рейтинга за покупку билета</b>, ( баллов )</td><td><input type="text" class="ok12" name="shans_bonus_reit" value="'.$shans_bonus_reit.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление рейтинга за выигрышь</b>, ( баллов )</td><td><input type="text" class="ok12" name="shans_bonus_reit_igr" value="'.$shans_bonus_reit_igr.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление процента за выигрышь</b>, ( % )</td><td><input type="text" class="ok12" name="shans_bonus_sum" value="'.$shans_bonus_sum.'" style="text-align:center;"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>