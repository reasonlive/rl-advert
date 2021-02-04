<?php
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка "Доски почета"</b></h1>';

if(count($_POST)>0) {
	$cena_board_add = isset($_POST["cena_board_add"]) ? intval(abs(str_replace(",", ".", trim($_POST["cena_board_add"])))) : false;
	$cena_board_comm = isset($_POST["cena_board_comm"]) ? number_format(abs(str_replace(",", ".", trim($_POST["cena_board_comm"]))), 2, ".", "") : false;
	$cena_board_reit = isset($_POST["cena_board_reit"]) ? number_format(abs(str_replace(",", ".", trim($_POST["cena_board_reit"]))), 2, ".", "") : false;
	$cena_board_comis = isset($_POST["cena_board_comis"]) ? number_format(abs(str_replace(",", ".", trim($_POST["cena_board_comis"]))), 2, ".", "") : false;

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_board_add' WHERE `item`='cena_board_add' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_board_comm' WHERE `item`='cena_board_comm' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_board_reit' WHERE `item`='cena_board_reit' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_board_comis' WHERE `item`='cena_board_comis' AND `howmany`='1'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1200);
		HideMsg("info-msg", 1210);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_board_add'");
$cena_board_add = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_board_comm'");
$cena_board_comm = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_board_reit'");
$cena_board_reit = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_board_comis'");
$cena_board_comis = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left"><td><b>Минимальная ставка</b>, ( руб. )</td><td><input type="text" class="ok12" name="cena_board_add" value="'.$cena_board_add.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Cтоимость добавления комментария</b>, ( руб. )</td><td><input type="text" class="ok12" name="cena_board_comm" value="'.$cena_board_comm.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление рейтинга за размещение</b>, ( баллов )</td><td><input type="text" class="ok12" name="cena_board_reit" value="'.$cena_board_reit.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление процента за участие в конкурсе</b>, ( % )</td><td><input type="text" class="ok12" name="cena_board_comis" value="'.$cena_board_comis.'" style="text-align:center;"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>