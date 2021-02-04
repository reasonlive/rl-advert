<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка кабинета рекламодателя</b></h1>';

if(count($_POST)>0) {

	$null_ref_cena_min = floatval(abs(trim($_POST["null_ref_cena_min"])));
	$null_ref_comis = round(floatval(abs(trim($_POST["null_ref_comis"]))),2);

	if($null_ref_cena_min<=0) {
		echo '<span id="info-msg" class="msg-error">Минимальная сумма за выкуп у реферера должна быть больше нуля!</span>';

	}elseif($null_ref_comis<=0) {
		echo '<span id="info-msg" class="msg-error">Комиссия системы должна быть больше нуля!</span>';

	}else{
		$mysqli->query("UPDATE `tb_config` SET `price`='$null_ref_cena_min' WHERE `item`='null_ref_cena_min' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$null_ref_comis' WHERE `item`='null_ref_comis' AND `howmany`='1'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}
       
	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1500);
		HideMsg("info-msg", 1500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='null_ref_cena_min' AND `howmany`='1'");
$null_ref_cena_min = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='null_ref_comis' AND `howmany`='1'");
$null_ref_comis = $sql->fetch_object()->price;

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:50%; margin:0px; padding:0px;">';
echo '<tr align="center"><th>Параметр</th><th>Значение</th></tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальная сумма за выкуп у реферера</b>, (руб.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="null_ref_cena_min" value="'.$null_ref_cena_min.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Комиссия системы</b>, (%)<b>:</b></td>';
		echo '<td align="center" style="width:125px;"><input type="text" class="ok12" name="null_ref_comis" value="'.$null_ref_comis.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>