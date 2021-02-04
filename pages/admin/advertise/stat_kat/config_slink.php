<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки статической рекламы</b></h3>';

if(count($_POST)>0) {

	$cena_kat = floatval(trim($_POST["cena_kat"]));
	$cena_kat_color = floatval(trim($_POST["cena_kat_color"]));
	$cena_kat_min = floatval(trim($_POST["cena_kat_min"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_kat' WHERE `item`='cena_kat'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_kat_color' WHERE `item`='cena_kat_color'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_kat_min' WHERE `item`='cena_kat_min'") or die($mysqli->error);

	echo '<span class="msg-ok">Изменения успешно сохранены.</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_kat' AND `howmany`='1'");
$cena_kat = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_kat_color' AND `howmany`='1'");
$cena_kat_color = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_kat_min' AND `howmany`='1'");
$cena_kat_min = $sql->fetch_object()->price;

echo '<form method="post" action="">';
echo '<table>';
	echo '<tr><th width="200" align="left">Стоимость 1 день:</th><td><input type="text" size="10" name="cena_kat" style="text-align:right;" value="'.$cena_kat.'"> руб.</td></tr>';
	echo '<tr><th width="200" align="left">Стоимость выделения:</th><td><input type="text" size="10" name="cena_kat_color" style="text-align:right;" value="'.$cena_kat_color.'"> руб./сутки</td></tr>';
	echo '<tr><th width="200" align="left">Минимальный заказ:</th><td><input type="text" size="10" name="cena_kat_min" style="text-align:right;" value="'.$cena_kat_min.'"> суток</td></tr>';
	echo '<tr><td width="200" align="left">&nbsp</td><td><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>