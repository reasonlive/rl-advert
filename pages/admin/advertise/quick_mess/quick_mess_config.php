<?php
if(!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;">Настройка <b>"Быстрые сообщения"</b></h3>';

if(count($_POST)>0) {
	$quick_mess_cena = isset($_POST["quick_mess_cena"]) ? number_format(abs(str_replace(",", ".", trim($_POST["quick_mess_cena"]))), 2, ".", "") : false;
	$quick_mess_cena_url = isset($_POST["quick_mess_cena_url"]) ? number_format(abs(str_replace(",", ".", trim($_POST["quick_mess_cena_url"]))), 2, ".", "") : false;
	$quick_mess_cena_color = isset($_POST["quick_mess_cena_color"]) ? number_format(abs(str_replace(",", ".", trim($_POST["quick_mess_cena_color"]))), 2, ".", "") : false;
	$count_quick_mess_max = isset($_POST["count_quick_mess_max"]) ? number_format(abs(str_replace(",", ".", trim($_POST["count_quick_mess_max"]))), 0, ".", "") : false;

	$mysqli->query("UPDATE `tb_config` SET `price`='$quick_mess_cena' WHERE `item`='quick_mess_сena' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$quick_mess_cena_url' WHERE `item`='quick_mess_url' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$quick_mess_cena_color' WHERE `item`='quick_mess_color' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$count_quick_mess_max' WHERE `item`='count_quick_mess_max' AND `howmany`='1'") or die($mysqli->error);

	$save = true;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='quick_mess_сena' AND `howmany`='1'");
$quick_mess_cena = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='quick_mess_url' AND `howmany`='1'");
$quick_mess_cena_url = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='quick_mess_color' AND `howmany`='1'");
$quick_mess_cena_color = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='count_quick_mess_max' AND `howmany`='1'");
$count_quick_mess_max = number_format($sql->fetch_object()->price, 0, ".", "");

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:500px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость размещения сообщения</b>, [руб.]</td>';
		echo '<td align="center"><input type="text" class="ok12" name="quick_mess_cena" value="'.$quick_mess_cena.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость добавления URL-ссылки</b>, [руб.]</td>';
		echo '<td align="center"><input type="text" class="ok12" name="quick_mess_cena_url" value="'.$quick_mess_cena_url.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость выделения цветом</b>, [руб.]</td>';
		echo '<td align="center"><input type="text" class="ok12" name="quick_mess_cena_color" value="'.$quick_mess_cena_color.'" style="text-align:center;"></td>';
	echo '</tr>';
		echo '<tr>';
		echo '<td align="left"><b>Количество отображения сообщений в блоке</td>';
		echo '<td align="center"><input type="text" class="ok12" name="count_quick_mess_max" value="'.$count_quick_mess_max.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';


if(count($_POST)>0 && isset($save)) {
	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 50);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}
?>