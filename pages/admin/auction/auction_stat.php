<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Статистика аукциона:</b></h3>';

$sql = $mysqli->query("SELECT `id` FROM `tb_auction` WHERE `status`='1' AND `timer_end`>'".time()."'");
$act_auc = $sql->num_rows;

$sql = $mysqli->query("SELECT `id` FROM `tb_auction` WHERE `status`='0'");
$all_auc = $sql->num_rows;

$sql = $mysqli->query("SELECT `id`,`kolstv`,`stavka` FROM `tb_auction` WHERE `status`='0' ORDER BY `kolstv` DESC");
if($sql->num_rows > 0) {
	$row = $sql->fetch_array();
	$id_a = $row["0"];
	$kolstv_a = $row["1"];
	$sum_max = $kolstv_a * $row["2"];
}else{
	$id_a = false;
	$kolstv_a = false;
	$sum_max = false;
}


$sql_all_s = $mysqli->query("SELECT sum(`summa`) FROM `tb_auction` WHERE `status`='0'");
//$sum_stav = $sql->fetch_object()->price;
$all_count1 = $sql_all_s->fetch_array();
 $sum_stav = $all_count1['0'];

$sql_all = $mysqli->query("SELECT sum(`proc`) FROM `tb_auction` WHERE `status`='0'");
$all_count1 = $sql_all->fetch_array();
 $doxod_sys = $all_count1['0'];
//$doxod_sys = $sql->fetch_object()->price;

echo '<table>';
echo '<tr><th width="230" align="left">Активных лотов:</th><td><b style="color:#000; font-size:115%;">'.number_format($act_auc,0,".","`").'</b></td></tr>';
echo '<tr><th width="230" align="left">Завершенных лотов:</th><td><b style="color:#000; font-size:115%;">'.number_format($all_auc,0,".","`").'</b></td></tr>';
if($id_a==true) {
	echo '<tr><th width="230" align="left">Максимальное кол-во ставок:</th><td><b style="color:#000; font-size:115%;">'.$kolstv_a.'</b> в аукционе #<b>'.$id_a.'</b> на сумму <b>'.number_format($sum_max,2,".","`").'</b> руб.</td></tr>';
}else{
	echo '<tr><th width="230" align="left">Максимальное кол-во ставок:</th><td>Аукционы еще не проводились</td></tr>';
}
echo '<tr><th width="230" align="left">Заработали пользователи:</th><td><b style="color:#000; font-size:115%;">'.number_format($sum_stav,2,".","`").' руб.</b></td></tr>';
echo '<tr><th width="230" align="left">Доход системы:</th><td><b style="color:#000; font-size:115%;">'.number_format($doxod_sys,2,".","`").' руб.</b></td></tr>';

echo '</table>';

?>