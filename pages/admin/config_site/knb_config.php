<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка Игры "КНБ"</b></h1>';

if(count($_POST)>0) {
	$game_knb_min = isset($_POST["game_knb_min"]) ? number_format(abs(str_replace(",", ".", trim($_POST["game_knb_min"]))), 2, ".", "") : false;
	$game_knb_max = isset($_POST["game_knb_max"]) ? number_format(abs(str_replace(",", ".", trim($_POST["game_knb_max"]))), 2, ".", "") : false;
	$game_knb_comis = isset($_POST["game_knb_comis"]) ? intval(abs(str_replace(",", ".", trim($_POST["game_knb_comis"])))) : false;
	$game_knb_victory = isset($_POST["game_knb_victory"]) ? intval(abs(str_replace(",", ".", trim($_POST["game_knb_victory"])))) : false;
	$game_knb_reit = isset($_POST["game_knb_reit"]) ? intval(abs(str_replace(",", ".", trim($_POST["game_knb_reit"])))) : false;

	$mysqli->query("UPDATE `tb_config` SET `price`='$game_knb_min' WHERE `item`='game_knb_min' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$game_knb_max' WHERE `item`='game_knb_max' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$game_knb_comis' WHERE `item`='game_knb_comis' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$game_knb_victory' WHERE `item`='game_knb_victory' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$game_knb_reit' WHERE `item`='game_knb_reit' AND `howmany`='1'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1200);
		HideMsg("info-msg", 1210);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='game_knb_min'");
$game_knb_min = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='game_knb_max'");
$game_knb_max = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='game_knb_comis'");
$game_knb_comis = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='game_knb_victory'");
$game_knb_victory = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='game_knb_reit'");
$game_knb_reit = number_format($sql->fetch_object()->price, 0, ".", "");

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left"><td><b>Минимальная ставка</b>, ( руб. )</td><td><input type="text" class="ok12" name="game_knb_min" value="'.$game_knb_min.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Максимальная ставка</b>, ( руб. )</td><td><input type="text" class="ok12" name="game_knb_max" value="'.$game_knb_max.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Коммисия за игру</b>, ( % )</td><td><input type="text" class="ok12" name="game_knb_comis" value="'.$game_knb_comis.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление рейтинга за победу</b>, ( баллов )</td><td><input type="text" class="ok12" name="game_knb_victory" value="'.$game_knb_victory.'" style="text-align:center;"></td></tr>';
	echo '<tr align="left"><td><b>Начисление рейтинга за участие</b>, ( баллов )</td><td><input type="text" class="ok12" name="game_knb_reit" value="'.$game_knb_reit.'" style="text-align:center;"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>