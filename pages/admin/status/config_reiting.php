<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки начисления баллов рейтинга</b></h1>';

if(count($_POST)>0) {
	$reit_ds = isset($_POST["reit_ds"]) ? floatval(trim($_POST["reit_ds"])) : "0";
	$reit_you = isset($_POST["reit_you"]) ? floatval(trim($_POST["reit_you"])) : "0";
	$reit_mails = isset($_POST["reit_mails"]) ? floatval(trim($_POST["reit_mails"])) : "0";
	$reit_as = isset($_POST["reit_as"]) ? floatval(trim($_POST["reit_as"])) : "0";
	$reit_task = isset($_POST["reit_task"]) ? floatval(trim($_POST["reit_task"])) : "0";
	$reit_ref = isset($_POST["reit_ref"]) ? floatval(trim($_POST["reit_ref"])) : "0";
	$reit_rek = isset($_POST["reit_rek"]) ? floatval(trim($_POST["reit_rek"])) : "0";
	
	$reit_rek_bal = isset($_POST["reit_rek_bal"]) ? floatval(trim($_POST["reit_rek_bal"])) : "0";
	
	$reit_ref_rek = isset($_POST["reit_ref_rek"]) ? floatval(trim($_POST["reit_ref_rek"])) : "0";
	$reit_active = isset($_POST["reit_active"]) ? floatval(trim($_POST["reit_active"])) : "0";
	$reit_noactive = isset($_POST["reit_noactive"]) ? floatval(trim($_POST["reit_noactive"])) : "0";
	$reit_ban = isset($_POST["reit_ban"]) ? floatval(trim($_POST["reit_ban"])) : "0";
	$tests_reiting = isset($_POST["tests_reiting"]) ? floatval(trim($_POST["tests_reiting"])) : "0";
    $reit_vip = isset($_POST["reit_vip"]) ? floatval(trim($_POST["reit_vip"])) : "0";
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_ds' WHERE `item`='reit_ds' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_you' WHERE `item`='reit_you' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_mails' WHERE `item`='reit_mails' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_as' WHERE `item`='reit_as' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_task' WHERE `item`='reit_task' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_ref' WHERE `item`='reit_ref' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_rek' WHERE `item`='reit_rek'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_rek_bal' WHERE `item`='reit_rek_bal'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_ref_rek' WHERE `item`='reit_ref_rek'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_active' WHERE `item`='reit_active' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_noactive' WHERE `item`='reit_noactive' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_ban' WHERE `item`='reit_ban' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$tests_reiting' WHERE `item`='tests_reiting' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$reit_vip' WHERE `item`='reit_vip'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
        
	echo '<script type="text/javascript"> HideMsg("info-msg", 1500); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ds' AND `howmany`='1'");
$reit_ds = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_you' AND `howmany`='1'");
$reit_you = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_mails' AND `howmany`='1'");
$reit_mails = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_as' AND `howmany`='1'");
$reit_as = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_task' AND `howmany`='1'");
$reit_task = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref' AND `howmany`='1'");
$reit_ref = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
$reit_rek = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek_bal'");
$reit_rek_bal = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
$reit_ref_rek = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_noactive' AND `howmany`='1'");
$reit_noactive = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_active' AND `howmany`='1'");
$reit_active = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ban' AND `howmany`='1'");
$reit_ban = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_vip'");
$reit_vip = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_reiting' AND `howmany`='1'");
$tests_reiting = $sql->fetch_object()->price;

echo '<form method="post" action="" id="newform">';
echo '<table>';
echo '<tr valign="top"><td width="49.5%" style="margin:0; padding:0; background:none; border:none;">';
	echo '<table class="tables" style="margin:0; padding:0;">';
		echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
		echo '<tr align="left"><td><b>за просмотр динамической ссылки</b>:</td><td align="center"><input type="text" class="ok12" name="reit_ds" value="'.$reit_ds.'" style="text-align:right;"></td></tr>';
			echo '<tr align="left"><td><b>за просмотр YouTube ролика</b>:</td><td align="center"><input type="text" class="ok12" name="reit_you" value="'.$reit_you.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за прочтение рекламного письма</b>:</td><td align="center"><input type="text" class="ok12" name="reit_mails" value="'.$reit_mails.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за просмотр ссылки в автосерфинге</b>:</td><td align="center"><input type="text" class="ok12" name="reit_as" value="'.$reit_as.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за выполнение теста</b>:</td><td align="center"><input type="text" class="ok12" name="tests_reiting" value="'.$tests_reiting.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за выполнение задания рекламодателя</b>:</td><td align="center"><input type="text" class="ok12" name="reit_task" value="'.$reit_task.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за привлечение реферала</b>:</td><td align="center"><input type="text" class="ok12" name="reit_ref" value="'.$reit_ref.'" style="text-align:right;"></td></tr>';
	echo '</table>';
echo '</td>';
echo '<td width="1%" style="margin:0; padding:0; background:none; border:none;"></td>';
echo '<td width="49.5%" style="margin:0; padding:0; background:none; border:none;">';
	echo '<table class="tables" style="margin:0; padding:0;">';
		echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
		echo '<tr align="left"><td><b>за каждые полные 10 руб затраченные на оплату<br>рекламы с внутреннего счета</b>:</td><td align="center"><input type="text" class="ok12" name="reit_rek" value="'.$reit_rek.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за каждые полные 10 руб затраченные рефералом<br>на оплату рекламы с внутреннего счета</b>:</td><td align="center"><input type="text" class="ok12" name="reit_ref_rek" value="'.$reit_ref_rek.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за ежедневное посещение аккаунта</b>:</td><td align="center"><input type="text" class="ok12" name="reit_active" value="'.$reit_active.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за отсутствие активности в течении недели</b>:</td><td align="center"><input type="text" class="ok12" name="reit_noactive" value="'.$reit_noactive.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>бан на форуме или в чате</b>:</td><td align="center"><input type="text" class="ok12" name="reit_ban" value="'.$reit_ban.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за заказ вип серфинга и вип баннеров</b>:</td><td align="center"><input type="text" class="ok12" name="reit_vip" value="'.$reit_vip.'" style="text-align:right;"></td></tr>';
		echo '<tr align="left"><td><b>за каждые полные 10 руб конвертированые с основного счета</b>:</td><td align="center"><input type="text" class="ok12" name="reit_rek_bal" value="'.$reit_rek_bal.'" style="text-align:right;"></td></tr>';
	echo '</table>';
echo '</td></tr>';
echo '<tr align="center"><td colspan="3" style="background:none;"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>