<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки статической рекламы</b></h3>';

if(count($_POST)>0) {

	$cena_slink = floatval(trim($_POST["cena_slink"]));
	$cena_slink_color = floatval(trim($_POST["cena_slink_color"]));
	$cena_slink_min = floatval(trim($_POST["cena_slink_min"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_slink' WHERE `item`='cena_slink'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_slink_color' WHERE `item`='cena_slink_color'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_slink_min' WHERE `item`='cena_slink_min'") or die($mysqli->error);

	echo '<span class="msg-ok">Изменения успешно сохранены.</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink' AND `howmany`='1'");
$cena_slink = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink_color' AND `howmany`='1'");
$cena_slink_color = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink_min' AND `howmany`='1'");
$cena_slink_min = $sql->fetch_object()->price;

echo '<form method="post" action="">';
echo '<table>';
	echo '<tr><th width="200" align="left">Стоимость 1 день:</th><td><input type="text" size="10" name="cena_slink" style="text-align:right;" value="'.$cena_slink.'"> руб.</td></tr>';
	echo '<tr><th width="200" align="left">Стоимость выделения:</th><td><input type="text" size="10" name="cena_slink_color" style="text-align:right;" value="'.$cena_slink_color.'"> руб./сутки</td></tr>';
	echo '<tr><th width="200" align="left">Минимальный заказ:</th><td><input type="text" size="10" name="cena_slink_min" style="text-align:right;" value="'.$cena_slink_min.'"> суток</td></tr>';
	echo '<tr><td width="200" align="left">&nbsp</td><td><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>