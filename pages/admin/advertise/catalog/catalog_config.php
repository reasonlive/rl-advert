<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки каталога ссылок</b></h3>';

if(count($_POST)>0) {

	$catalog_cena = floatval(trim($_POST["catalog_cena"]));
	$catalog_cena_color = floatval(trim($_POST["catalog_cena_color"]));
	$catalog_min = floatval(trim($_POST["catalog_min"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$catalog_cena' WHERE `item`='catalog_cena'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$catalog_cena_color' WHERE `item`='catalog_cena_color'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$catalog_min' WHERE `item`='catalog_min'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены.</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1500);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_cena' AND `howmany`='1'");
$catalog_cena = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_cena_color' AND `howmany`='1'");
$catalog_cena_color = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='catalog_min' AND `howmany`='1'");
$catalog_min = number_format($sql->fetch_object()->price, 0, ".", "");


echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:500px; margin:2px 0px; padding:0px;">';
echo '<thead><tr align="center"><th>Параметр</th><th width="130px">Значение</th><th width="70px"></th></tr></thead>';
echo '<tbody>';
echo '<tr>';
	echo '<td align="left"><b>Стоимость размещения</b></td>';
	echo '<td align="center"><input type="text" name="catalog_cena" value="'.$catalog_cena.'" class="ok12" style="text-align:center;"></td>';
	echo '<td align="left">руб./сутки</td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left"><b>Стоимость выделения</b></td>';
	echo '<td align="center"><input type="text" name="catalog_cena_color" value="'.$catalog_cena_color.'" class="ok12" style="text-align:center;"></td>';
	echo '<td align="left">руб./сутки</td>';
echo '</tr>';
echo '<tr>';
	echo '<td align="left"><b>Минимальный заказ</b></td>';
	echo '<td align="center"><input type="text" name="catalog_min" value="'.$catalog_min.'" class="ok12" style="text-align:center;"></td>';
	echo '<td align="left">суток</td>';
echo '</tr>';
echo '<tr><td align="center" colspan="3"><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>