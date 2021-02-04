<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки биржи рефералов</b></h3>';


if(count($_POST)>0) {
	$ref_birj_comis_proc = isset($_POST["ref_birj_comis_proc"]) ? number_format(abs(str_replace(",", ".", trim($_POST["ref_birj_comis_proc"]))), 0, ".", "") : false;
	$ref_birj_comis_min = isset($_POST["ref_birj_comis_min"]) ? number_format(abs(str_replace(",", ".", trim($_POST["ref_birj_comis_min"]))), 2, ".", "") : false;

	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_birj_comis_proc' WHERE `item`='ref_birj_comis_proc'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_birj_comis_min' WHERE `item`='ref_birj_comis_min'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 50);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_birj_comis_proc'");
$ref_birj_comis_proc = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_birj_comis_min'");
$ref_birj_comis_min = number_format($sql->fetch_object()->price, 2, ".", "");


echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="margin-top:1px; width:auto;">';
	echo '<thead><tr><th width="270px">Параметр</th><th>Значение</th></tr></thead>';
	echo '<tr>';
		echo '<td><b>Комиссия системы</b>, %</td>';
		echo '<td align="center"><input type="text" name="ref_birj_comis_proc" value="'.$ref_birj_comis_proc.'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Минимальная комиссия системы</b>, руб.</td>';
		echo '<td align="center"><input type="text" name="ref_birj_comis_min" value="'.$ref_birj_comis_min.'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr><td align="center" colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></tr>';
echo '</table>';
echo '</form><br><br>';

?>