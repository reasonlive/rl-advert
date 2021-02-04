<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0; width:600px"><b>Настройки заданий</b></h3>';

if(count($_POST)>0) {
	$limit_min_task = intval(trim($_POST["limit_min_task"]));
	$cena_task = round(floatval(trim($_POST["cena_task"])), 2);
	$ref_proc_rek = round(floatval(trim($_POST["ref_proc_rek"])), 2);
	$nacenka_task = round(floatval(trim($_POST["nacenka_task"])), 2);
	$task_vip = round(floatval(trim($_POST["task_vip"])), 2);
	$task_up = round(floatval(trim($_POST["task_up"])), 2);

	$mysqli->query("UPDATE `tb_config` SET `price`='$limit_min_task' WHERE `item`='limit_min_task' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_task' WHERE `item`='cena_task' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$ref_proc_rek' WHERE `item`='ref_proc_rek' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$nacenka_task' WHERE `item`='nacenka_task' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$task_vip' WHERE `item`='task_vip' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$task_up' WHERE `item`='task_up' AND `howmany`='1'") or die($mysqli->error);

	echo '<span class="msg-ok" style="width:560px;">Изменения успешно сохранены!</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='limit_min_task' AND `howmany`='1'");
$limit_min_task = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_task' AND `howmany`='1'");
$cena_task = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_proc_rek' AND `howmany`='1'");
$ref_proc_rek = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='nacenka_task' AND `howmany`='1'");
$nacenka_task = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='task_up' AND `howmany`='1'");
$task_up = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='task_vip' AND `howmany`='1'");
$task_vip = $sql->fetch_object()->price;


echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
echo '<table style="width:600px;">';
	echo '<thead><tr><th>Параметр</th><th width="120">Значение</th></tr></thead>';
	echo '<tr>';
		echo '<td><b>Минимальное кол-во заданий для заказа</b>, шт.</td>';
		echo '<td><input type="text" name="limit_min_task" value="'.$limit_min_task.'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Минимальная цена задания:</b>, руб.</td>';
		echo '<td><input type="text" name="cena_task" value="'.number_format($cena_task,2,"."," ").'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Реф процент рефереру рекламодателя</b>, %</td>';
		echo '<td><input type="text" name="ref_proc_rek" value="'.$ref_proc_rek.'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Комиссия системы (наценка)</b>, %.</td>';
		echo '<td><input type="text" name="nacenka_task" value="'.$nacenka_task.'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Цена поднятия задания</b>, руб.</td>';
		echo '<td><input type="text" name="task_up" value="'.number_format($task_up,2,"."," ").'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Цена за VIP-блок</b>, руб.</td>';
		echo '<td><input type="text" name="task_vip" value="'.number_format($task_vip,2,"."," ").'" class="ok12" style="text-align:right;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>