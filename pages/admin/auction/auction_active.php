<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список Активных аукционов</b></h3>';

$domen = ucfirst($_SERVER['HTTP_HOST']);
$domen = str_replace("wm","WM", $domen);
$domen = str_replace("ru","RU", $domen);

$attestat[100]='<font color="#000"><img src="../img/att/att_100.ico" alt="" align="absmiddle" border="0" /> Аттестат Псевдонима</font>';
$attestat[110]='<font color="green"><img src="../img/att/att_110.ico" alt="" align="absmiddle" border="0" /> Формальный Аттестат</font>';
$attestat[120]='<font color="green"><img src="../img/att/att_120.ico" alt="" align="absmiddle" border="0" /> Начальный Аттестат</font>';
$attestat[130]='<font color="green"><img src="../img/att/att_130.ico" alt="" align="absmiddle" border="0" /> Персональный Аттестат</font>';
$attestat[135]='<font color="green"><img src="../img/att/att_135.ico" alt="" align="absmiddle" border="0" /> Аттестат Продавца</font>';
$attestat[136]='<font color="green"><img src="../img/att/att_136.ico" alt="" align="absmiddle" border="0" /> Аттестат Capitaller</font>';
$attestat[140]='<font color="green"><img src="../img/att/att_140.ico" alt="" align="absmiddle" border="0" /> Аттестат Разработчика</font>';
$attestat[150]='<font color="green"><img src="../img/att/att_150.ico" alt="" align="absmiddle" border="0" /> Аттестат Регистратора</font>';
$attestat[170]='<font color="green"><img src="../img/att/att_170.ico" alt="" align="absmiddle" border="0" /> Аттестат Гаранта</font>';
$attestat[190]='<font color="green"><img src="../img/att/att_190.ico" alt="" align="absmiddle" border="0" /> Аттестат Сервиса</font>';
$attestat[300]='<font color="green"><img src="../img/att/att_300.ico" alt="" align="absmiddle" border="0" /> Аттестат Оператора</font>';
$attestat[0]='<font color="red">Неопредлен</font>';
$attestat[1]='<font color="red">Неопредлен</font>';
$attestat[-1]='<font color="red">Неопредлен</font>';

// Аукцион
$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_time' AND `howmany`='1'");
$auc_time = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_time_end_add' AND `howmany`='1'");
$auc_time_end_add = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_time_add' AND `howmany`='1'");
$auc_time_add = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_comis' AND `howmany`='1'");
$auc_comis = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_limit_click_user' AND `howmany`='1'");
$auc_limit_click_user = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_limit_activ_last_user' AND `howmany`='1'");
$auc_limit_activ_last_user = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_max' AND `howmany`='1'");
$auc_max = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='auc_limit_activ_all_user' AND `howmany`='1'");
$auc_limit_activ_all_user = $sql->fetch_object()->price;
// END Аукцион


$sql_all = $mysqli->query("SELECT `username` FROM `tb_online` WHERE `username`!='' AND `page` LIKE '%auction.php%'") or die($mysqli->error);
$online_game = $sql_all->num_rows;

echo '<table align="center" border="0" width="100%" cellspacing="1" cellpadding="2">';
echo '<tr><td colspan="5">Пользователи на аукционе: (<b>'.$online_game.'</b>):&nbsp;';
	while ($row_o = $sql_all->fetch_array()) {
		echo '<span style="color:blue;">'.$row_o["username"].',</span> ';
	}
echo '</td></tr>';
echo "</table>";

$sql = $mysqli->query("SELECT * FROM `tb_auction` WHERE `status`='1' AND `timer_end`>='".time()."' ORDER BY `timer_end` ASC LIMIT $auc_max");
if($sql->num_rows>0) {

	echo '<div align="center"><form action="'.$_SERVER["PHP_SELF"].'" method="GET"><input type="hidden" name="op" value="'.limpiar($_GET["op"]).'"><input type="submit" class="submit" value="Обновить список аукционов"></form></div><br>';
	echo '<script type="text/javascript" src="../scripts/auction.js"></script>';

	while ($row = $sql->fetch_array()) {

		$sql_u = $mysqli->query("SELECT * FROM `tb_users` WHERE `username`='".$row["referal"]."'");
		$row_u = $sql_u->fetch_array();

		echo '<div align="center" style="padding: 0px 0px 20px 0px; margin: 0px 10px 20px 10px; border-bottom: 2px dotted #669966;">';
		echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="line-height : 1.5em; border-collapse: collapse; border: 1px solid #1E90FF;">';
		echo '<tr bgcolor="#1E90FF" align="center"><th align="center" colspan="3">Аукцион #'.$row["id"].' (продавец: '.$row["username"].')</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="60%" style="padding:5px;">';
				echo '<table border="0" cellspacing="1" cellpadding="0">';
				echo '<tr><td align="left" style="padding-right:20px;"><u>Информация о реферале:</u></td><td></td></tr>';
				echo '<tr><td align="left">ID:</td><td align="left">#'.$row_u["id"].'</td></tr>';
				echo '<tr><td align="left">Логин:</td><td align="left">'.$row_u["username"].'</td></tr>';
				echo '<tr><td align="left">Дата регистрации:</td><td align="left">'.DATE("d.m.Yг. H:i", $row_u["joindate2"]).'</td></tr>';
				echo '<tr><td align="left">Дата последнего входа:</td><td align="left">'.DATE("d.m.Yг. H:i", $row_u["lastlogdate2"]).'</td></tr>';
				echo '<tr><td align="left">Аттестат в системе WMT:</td><td align="left">'.$attestat[$row_u["attestat"]].'</td></tr>';
				echo '<tr><td align="left">Рефералы:</td><td align="left">'.$row_u["referals"].' - '.$row_u["referals2"].' - '.$row_u["referals3"].'</td></tr>';
				echo '<tr><td align="left">Клики:</td><td align="left">'.number_format($row_u["visits"],0,".","'").'</td></tr>';
				echo '<tr><td align="left">Автосерфинг:</td><td align="left">'.number_format($row_u["visits_a"],0,".","'").'</td></tr>';
				echo '<tr><td align="left">Задания:</td><td align="left">'.number_format($row_u["task_good"],0,".","'").'</td></tr>';
				echo '<tr><td align="left">Реклама:</td><td align="left">'.number_format($row_u["money_rek"],2,".","'").' руб.</td></tr>';
				if($row["inforef"]!="") {echo '<tr><td align="left">Доп. информация от продавца:</td><td></td></tr><tr><td align="left" colspan="2">'.$row["inforef"].'</td></tr>';}
				echo '<tr><td align="left" style="padding-top:10px;"><u>Информация  об аукционе:</u></td><td></td></tr>';
				echo '<tr><td align="left">Размер ставки:</td><td align="left"><b style="color: green;">'.number_format($row["stavka"],2,".","'").' руб.</b></td></tr>';
				if($row["kolstv"]>0) {
					echo '<tr><td align="left">Количество ставок:</td><td align="left"><b>'.number_format($row["kolstv"],0,".","'").'</b></td></tr>';
					echo '<tr><td align="left">Лидер аукциона:</td><td align="left"><b>'.$row["lider"].'</b></td></tr>';
				}else{
					echo '<tr><td colspan="2" align="left">Ставок еще небыло!</td></tr>';
				}
				echo '</table>';
			echo '</td>';
			echo '<td align="center" width="40%" style="padding:5px;">';
				echo '<table border="0" cellspacing="1" cellpadding="0">';
				echo '<tr><td><img src="../avatar/'.$row_u["avatar"].'" width="80" height="80" border="0" align="middle" alt="" /><br><br></td></tr>';
				echo '<tr><td><b>Окончание аукциона через:</b></td></tr>';
				echo '<tr><td><span style="font-size:150%; font-weight:bold; color:blue;" class="end_time">'.DATE("i:s", ($row["timer_end"]-time())).'</SPAN>&nbsp;мин.</td></tr>';
				echo '<tr><td><div id="form"><form method="POST" action=""><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="submit" value="Сделать ставку"></form></div></td></tr>';
				echo '</table>';
			echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
	}
	echo '<script type="text/javascript">timer_init();</script>';
}else{
	echo '<div align="center" style="color:#FF0000; font-weight:bold;">В даный момент активных аукционов нет.</div><br><br>';
	echo '<div align="center"><form action="'.$_SERVER["PHP_SELF"].'" method="GET"><input type="hidden" name="op" value="'.limpiar($_GET["op"]).'"><input type="submit" class="submit" value="Обновить список аукционов"></form></div><br><br>';
}
?>