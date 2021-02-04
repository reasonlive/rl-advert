<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0; width:700px;"><b>Настройки партнерской программы для статической рекламы</b></h1>';

if(count($_POST)>0 && isset($_POST["type"]) && limpiar(intval($_POST["type"]))==1) {

	$partner_max_percent = intval(trim($_POST["partner_max_percent"]));
	$partner_count_day = intval(trim($_POST["partner_count_day"]));
	$partner_count_per = intval(trim($_POST["partner_count_per"]));
	$partner_active = intval(trim($_POST["partner_active"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$partner_max_percent' WHERE `item`='partner_max_percent'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `howmany`='$partner_count_day', `price`='$partner_count_per' WHERE `item`='partner_count_day'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$partner_active' WHERE `item`='partner_active'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok" style="width:660px;">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='partner_max_percent' AND `howmany`='1'");
$partner_max_percent = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='partner_active' AND `howmany`='1'");
$partner_active = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price`,`howmany` FROM `tb_config` WHERE `item`='partner_count_day'");
if($sql->num_rows>0) {
	$row_pd = $sql->fetch_array();
	$partner_count_day = $row_pd["howmany"];
	$partner_count_per = $row_pd["price"];
}else{
	$partner_count_day = 1;
	$partner_count_per = 1;
}

echo '<form method="post" action="" id="newform">';
echo '<input type="hidden" name="type" value="1">';
echo '<table class="tables" style="width:700px;">';
echo '<thead><tr align="center"><th>Параметр</th><th width="125">Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr><td><b>Максимальный процент</b>, %</td><td><input type="text" class="ok12" name="partner_max_percent" style="text-align:center;" value="'.$partner_max_percent.'"></td></tr>';
	echo '<tr><td><b>Соотношение дней заказа к проценту</b>, дней = %</th><td><input type="text" class="ok12" name="partner_count_day" style="text-align:center; width:50px;" value="'.$partner_count_day.'"> = <input type="text" class="ok12" name="partner_count_per" style="text-align:center; width:50px;" value="'.$partner_count_per.'"></td></tr>';
	echo '<tr><td><b>Стоимость активации</b>, баллов рейтинга</td><td><input type="text" class="ok12" name="partner_active" style="text-align:center;" value="'.$partner_active.'"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form><br><br>';


echo '<h3 class="sp" style="margin-top:0; padding-top:0; width:700px;"><b>Настройки партнерской программы для пакетов рекламы</b></h1>';

if(count($_POST)>0 && isset($_POST["type"]) && limpiar(intval($_POST["type"]))==2) {

	$partner_max_percent_pack = intval(trim($_POST["partner_max_percent_pack"]));
	$partner_count_day_pack = 1;
	$partner_count_per_pack = intval(trim($_POST["partner_count_per_pack"]));
	$partner_active_pack = intval(trim($_POST["partner_active_pack"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$partner_max_percent_pack' WHERE `item`='partner_max_percent_pack'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `howmany`='$partner_count_day_pack', `price`='$partner_count_per_pack' WHERE `item`='partner_count_day_pack'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$partner_active_pack' WHERE `item`='partner_active_pack'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok" style="width:660px;">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='partner_max_percent_pack' AND `howmany`='1'");
$partner_max_percent_pack = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='partner_active_pack' AND `howmany`='1'");
$partner_active_pack = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price`,`howmany` FROM `tb_config` WHERE `item`='partner_count_day_pack'");
if($sql->num_rows>0) {
	$row_pd = $sql->fetch_array();
	$partner_count_day_pack = 1;
	$partner_count_per_pack = $row_pd["price"];
}else{
	$partner_count_day_pack = 1;
	$partner_count_per_pack = 1;
}

echo '<form method="post" action="" id="newform">';
echo '<input type="hidden" name="type" value="2">';
echo '<table class="tables" style="width:700px;">';
echo '<thead><tr align="center"><th>Параметр</th><th width="125">Значение</th></tr></thead>';
echo '<tbody>';
	echo '<tr><td><b>Максимальный процент</b>, %</td><td><input type="text" class="ok12" name="partner_max_percent_pack" style="text-align:center;" value="'.$partner_max_percent_pack.'"></td></tr>';
	echo '<tr><td><b>Процент за размещение пакета рекламы</b>, %</th><td><input type="text" class="ok12" name="partner_count_per_pack" style="text-align:center;" value="'.$partner_count_per_pack.'"></td></tr>';
	echo '<tr><td><b>Стоимость активации</b>, баллов рейтинга</td><td><input type="text" class="ok12" name="partner_active_pack" style="text-align:center;" value="'.$partner_active_pack.'"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';

echo '</form>';

?>