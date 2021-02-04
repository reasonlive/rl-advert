<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки псевдо-динамической рекламы</b></h3>';

if(count($_POST)>0) {

	$cena_psevdo = floatval(trim($_POST["cena_psevdo"]));
	$cena_color_psevdo = floatval(trim($_POST["cena_color_psevdo"]));
	$min_psevdo = floatval(trim($_POST["min_psevdo"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_psevdo' WHERE `item`='cena_psevdo'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_color_psevdo' WHERE `item`='cena_color_psevdo'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$min_psevdo' WHERE `item`='min_psevdo'") or die($mysqli->error);

	echo '<span class="msg-ok">Изменения успешно сохранены!</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_psevdo' AND `howmany`='1'");
$cena_psevdo = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_color_psevdo' AND `howmany`='1'");
$cena_color_psevdo = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='min_psevdo' AND `howmany`='1'");
$min_psevdo = $sql->fetch_object()->price;


echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
echo '<table>';
	echo '<tr><th width="200" align="left">Стоимость за 1 сутки:</th><td><input type="text" size="10" name="cena_psevdo" style="text-align:right;" value="'.$cena_psevdo.'"> руб.</td></tr>';
	echo '<tr><th width="200" align="left">Стоимость выделения:</th><td><input type="text" size="10" name="cena_color_psevdo" style="text-align:right;" value="'.$cena_color_psevdo.'"> руб./сутки</td></tr>';
	echo '<tr><th width="200" align="left">Минимальный заказ:</th><td><input type="text" size="10" name="min_psevdo" style="text-align:right;" value="'.$min_psevdo.'"> суток</td></tr>';
	echo '<tr><td width="200" align="left">&nbsp</td><td><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>