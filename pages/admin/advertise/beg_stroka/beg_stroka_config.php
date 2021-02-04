<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки Бегущей строки</b></h3>';

if(count($_POST)>0) {

	$beg_stroka_cena = floatval(trim($_POST["beg_stroka_cena"]));
	$beg_stroka_cena_color = floatval(trim($_POST["beg_stroka_cena_color"]));
	$beg_stroka_min = intval(trim($_POST["beg_stroka_min"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$beg_stroka_cena' WHERE `item`='beg_stroka_cena'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$beg_stroka_cena_color' WHERE `item`='beg_stroka_cena_color'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$beg_stroka_min' WHERE `item`='beg_stroka_min'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='beg_stroka_cena' AND `howmany`='1'");
$beg_stroka_cena = number_format($sql->fetch_object()->price,2,".","");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='beg_stroka_cena_color' AND `howmany`='1'");
$beg_stroka_cena_color = number_format($sql->fetch_object()->price,2,".","");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='beg_stroka_min' AND `howmany`='1'");
$beg_stroka_min = number_format($sql->fetch_object()->price,0,".","");

echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
echo '<table>';
	echo '<tr><th width="200" align="left">Стоимость размещения:</th><td><input type="text" class="ok12" name="beg_stroka_cena" style="text-align:right;" value="'.$beg_stroka_cena.'"> руб./сутки</td></tr>';
	echo '<tr><th width="200" align="left">Стоимость выделения:</th><td><input type="text" class="ok12" name="beg_stroka_cena_color" style="text-align:right;" value="'.$beg_stroka_cena_color.'"> руб./сутки</td></tr>';
	echo '<tr><th width="200" align="left">Минимальный заказ:</th><td><input type="text" class="ok12" name="beg_stroka_min" style="text-align:right;" value="'.$beg_stroka_min.'"> дней</td></tr>';
	echo '<tr><td width="200" align="left">&nbsp</td><td><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>