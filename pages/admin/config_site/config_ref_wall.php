<?php
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка Реф-Стены</b></h1>';

if(count($_POST)>0) {
	$ref_wall_cena = isset($_POST["ref_wall_cena"]) ? number_format(abs(str_replace(",", ".", trim($_POST["ref_wall_cena"]))), 2, ".", "") : 30;
	$ref_wall_cnt_all = isset($_POST["ref_wall_cnt_all"]) ? number_format(abs(str_replace(",", ".", trim($_POST["ref_wall_cnt_all"]))), 0, ".", "") : 15;
	$ref_wall_cnt_reg = isset($_POST["ref_wall_cnt_reg"]) ? number_format(abs(str_replace(",", ".", trim($_POST["ref_wall_cnt_reg"]))), 0, ".", "") : 10;

	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_wall_cena' WHERE `item`='ref_wall_cena' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_wall_cnt_all' WHERE `item`='ref_wall_cnt_all' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_wall_cnt_reg' WHERE `item`='ref_wall_cnt_reg' AND `howmany`='1'") or die($mysqli->error);

	$save_ok = true;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_wall_cena' AND `howmany`='1'") or die($mysqli->error);
if($sql->num_rows>0) {
	$ref_wall_cena = number_format($sql->fetch_object()->price, 2, ".", "");
}else{
	$ref_wall_cena = number_format(30, 2, ".", "");
	$mysqli->query("INSERT INTO `tb_config` (`item`, `howmany`, `price`) 
	VALUES('ref_wall_cena', '1', '$ref_wall_cena')") or die($mysqli->error);
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_wall_cnt_all' AND `howmany`='1'") or die($mysqli->error);
if($sql->num_rows>0) {
	$ref_wall_cnt_all = number_format($sql->fetch_object()->price, 0, ".", "");
}else{
	$ref_wall_cnt_all = number_format(15, 0, ".", "");
	$mysqli->query("INSERT INTO `tb_config` (`item`, `howmany`, `price`) 
	VALUES('ref_wall_cnt_all', '1', '$ref_wall_cnt_all')") or die($mysqli->error);
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_wall_cnt_reg' AND `howmany`='1'") or die($mysqli->error);
if($sql->num_rows>0) {
	$ref_wall_cnt_reg = number_format($sql->fetch_object()->price, 0, ".", "");
}else{
	$ref_wall_cnt_reg = number_format(10, 0, ".", "");
	$mysqli->query("INSERT INTO `tb_config` (`item`, `howmany`, `price`) 
	VALUES('ref_wall_cnt_reg', '1', '$ref_wall_cnt_reg')") or die($mysqli->error);
}

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left">';
		echo '<td align="left"><b>Стоимость размещения на Реф-Стене</b>, [ руб. ]</td>';
		echo '<td align="center"><input type="text" class="ok12" name="ref_wall_cena" value="'.$ref_wall_cena.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="left">';
		echo '<td align="left"><b>Количество мест на Реф-Стене</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="ref_wall_cnt_all" value="'.$ref_wall_cnt_all.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="left">';
		echo '<td align="left"><b>Количество мест на странице регистрации</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="ref_wall_cnt_reg" value="'.$ref_wall_cnt_reg.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center">';
		echo '<td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

if(count($_POST)>0 && isset($save_ok)) {
	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 10);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

?>