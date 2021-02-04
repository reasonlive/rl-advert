<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Статистика сайта</b></h3>';

 $sql = $mysqli->query("SELECT count(id) FROM `tb_users`");
//$users_all = $sql->fetch_object()->price;
$all_count1 = $sql->fetch_array();
$users_all = $all_count1['0'];

$sql = $mysqli->query("SELECT count(id) FROM `tb_users` WHERE `lastlogdate2`>='".strtotime(DATE("d.m.Y", time()-1*24*60*60))."'");
$all_count1 = $sql->fetch_array();
 $active_users = $all_count1['0'];
//$active_users = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT count(id) FROM `tb_users` WHERE `joindate`='".DATE("d.m.Y",(time()-24*60*60))."'");
$all_count1 = $sql->fetch_array();
 $users_v = $all_count1['0'];
//$users_v = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT count(id) FROM `tb_users` WHERE `joindate`='".DATE("d.m.Y")."'");
$all_count1 = $sql->fetch_array();
 $users_s = $all_count1['0'];
//$users_s = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT sum(money) FROM `tb_users` WHERE `money`>'0'");
$all_count1 = $sql->fetch_array();
 $money_all = $all_count1['0'];
//$money_all = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT sum(money_rb) FROM `tb_users` WHERE `money_rb`>'0'");
$all_count1 = $sql->fetch_array();
 $money_rb_all = $all_count1['0'];
//$money_rb_all = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT sum(amount) FROM `tb_history` WHERE `date` LIKE '".date("d.m.Y",(time()-24*60*60))."%'AND tipo='0'");
$all_count1 = $sql->fetch_array();
 $money_v = $all_count1['0'];
//$money_v = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT sum(amount) FROM `tb_history` WHERE `date` LIKE '".date("d.m.Y")."%'AND tipo='0'");
$all_count1 = $sql->fetch_array();
 $money_s = $all_count1['0'];
//$money_s = $sql->fetch_object()->price;

$sql_t = $mysqli->query("SELECT `id`,`zdprice`,`totals`,`wait` FROM `tb_ads_task` ORDER BY `id` ASC");
if($sql_t->num_rows) {
	$balance_task = 0;
	while ($row_t = $sql_t->fetch_row()) {
		$balance_task = $balance_task + ($row_t["1"] * ($row_t["2"] + $row_t["3"]));

		//echo $row_t["0"]." | ".$balance_task." + ".$row_t["1"]." * ".$row_t["2"]." + ".$row_t["1"]." * ".$row_t["3"]."<br>";
	}
}else{
	$balance_task = 0;
}

$sql_kon_0 = $mysqli->query("SELECT `p1`,`p2`,`p3`,`p4`,`p5`,`p6`,`p7`,`p8`,`p9`,`p10`,`p11`,`p12`,`p13`,`p14`,`p15`,`p16`,`p17`,`p18`,`p19`,`p20` FROM `tb_refkonkurs` WHERE `status`='0' ORDER BY `id` ASC");
if($sql_kon_0->num_rows) {
	$balance_kon_0 = 0;
	while ($row_kon_0 = $sql_kon_0->fetch_row()) {
		$balance_kon_0 = $balance_kon_0 + $row_kon_0["0"] + $row_kon_0["1"] + $row_kon_0["2"] + $row_kon_0["3"] + $row_kon_0["4"] + $row_kon_0["5"] + $row_kon_0["6"] + $row_kon_0["7"] + $row_kon_0["8"] + $row_kon_0["9"] + $row_kon_0["10"] + $row_kon_0["11"] + $row_kon_0["12"] + $row_kon_0["13"] + $row_kon_0["14"] + $row_kon_0["15"] + $row_kon_0["16"] + $row_kon_0["17"] + $row_kon_0["18"] + $row_kon_0["19"];
	}
}else{
	$balance_kon_0 = 0;
}

$sql_kon_1 = $mysqli->query("SELECT `p1`,`p2`,`p3`,`p4`,`p5`,`p6`,`p7`,`p8`,`p9`,`p10`,`p11`,`p12`,`p13`,`p14`,`p15`,`p16`,`p17`,`p18`,`p19`,`p20` FROM `tb_refkonkurs` WHERE `status`='1' ORDER BY `id` ASC");
if($sql_kon_1->num_rows) {
	$balance_kon_1 = 0;
	while ($row_kon_1 = $sql_kon_1->fetch_row()) {
		$balance_kon_1 = $balance_kon_1 + $row_kon_1["0"] + $row_kon_1["1"] + $row_kon_1["2"] + $row_kon_1["3"] + $row_kon_1["4"] + $row_kon_1["5"] + $row_kon_1["6"] + $row_kon_1["7"] + $row_kon_1["8"] + $row_kon_1["9"] + $row_kon_1["10"] + $row_kon_1["11"] + $row_kon_1["12"] + $row_kon_1["13"] + $row_kon_1["14"] + $row_kon_1["15"] + $row_kon_1["16"] + $row_kon_1["17"] + $row_kon_1["18"] + $row_kon_1["19"];
	}
}else{
	$balance_kon_1 = 0;
}

echo '<table class="tables" style="width:800px;">';
echo '<thead><tr><th>Параметр</th><th width="100">Значение</th><th width="25">ед.</th></tr></thead>';
echo '<tr>';
	echo '<td><b>Всего пользователей:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.number_format($users_all,0,".","`").'</b></td>';
	echo '<td align="right">чел.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Активные пользователей за 24 часа:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.number_format($active_users,0,".","`").'</b></td>';
	echo '<td align="right">чел.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Зарегистрировано вчера:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.$users_v.'</b></td>';
	echo '<td align="right">чел.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Зарегистрировано сегодня:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.$users_s.'</b></td>';
	echo '<td align="right">чел.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Выплачено вчера:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.number_format($money_v,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Выплачено сегодня:</b></td>';
	echo '<td align="right"><b style="color:#000; font-size:125%;">'.number_format($money_s,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Всего денег на основном счете:</b></td>';
	echo '<td align="right"><b style="color:#FF0000; font-size:125%;">'.number_format($money_all,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Всего денег на рекламном счете:</b></td>';
	echo '<td align="right"><b style="color:#FF0000; font-size:125%;">'.number_format($money_rb_all,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Всего денег на балансе заданий:</b></td>';
	echo '<td align="right"><b style="color:#FF0000; font-size:125%;">'.number_format($balance_task,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Всего денег на балансе не активных конкурсов:</b></td>';
	echo '<td align="right"><b style="color:#FF0000; font-size:125%;">'.number_format($balance_kon_0,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '<tr>';
	echo '<td><b>Всего денег на балансе активных конкурсов:</b></td>';
	echo '<td align="right"><b style="color:#FF0000; font-size:125%;">'.number_format($balance_kon_1,2,".","`").'</b></td>';
	echo '<td align="right">руб.</td>';
echo'</tr>';
echo '</table>';

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список пользователей по странам</b></h3>';

$sql = $mysqli->query("SELECT `country_cod`, count(`country_cod`) FROM `tb_users` GROUP BY `country_cod` ORDER BY 2 DESC");
echo '<table class="tables" style="width:800px;">';
echo '<tr align="center">';
	echo '<th align="right">№</th>';
	echo '<th>Флаг</th>';
	echo '<th align="left">Страна</th>';
	echo '<th width="20" nowrap="nowrap">чел.</th>';
	echo '<th align="right" width="50" nowrap="nowrap">%</th>';
echo '</tr>';
if ($sql->num_rows > 0) {
	$i = 0;
	while ($row = $sql->fetch_row()) {
		$i++;
		$percent = $row["1"] * 100 / $users_all;
		if ($row["0"] == "") { $flag = ""; } else { $flag = $row["0"]; }
		echo '<tr>';
			echo '<td align="right" width="20" nowrap="nowrap"><b>'.$i.'</b></td>';
			echo '<td align="center" width="20" nowrap="nowrap"><img src="//'.$_SERVER['HTTP_HOST'].'/img/flags/'.strtolower($row["0"]).'.gif" width="'.( (strtolower($row["0"])=="a1" | strtolower($row["0"])=="a2") ? "18" : "16").'" border="0" alt="" valign="baseline" title="'.get_country($row["0"]).'" style="margin:0; padding:0;" /></td>';
			echo '<td align="left"><b>'.get_country($row["0"]).'</b></td>';
			echo '<td align="right"><b style="color:green;">'.$row["1"].'</b></td>';
			echo '<td align="right"><b style="color:blue;">'.round(number_format($percent,2,".","`"),2).'</b> <b>%</b></td>';
		echo '</tr>';
	}
}
echo '</table>';

?>