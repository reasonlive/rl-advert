<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список онлайн посетителей</b></h3>';

$mysqli->query("DELETE FROM `tb_online` WHERE `date`<'".(time()-180)."'") or die($mysqli->error);

$sql_user = $mysqli->query("SELECT * FROM `tb_online` WHERE `username`!='' ORDER BY `date` DESC");
$sql_guest = $mysqli->query("SELECT * FROM `tb_online` WHERE `username`='' ORDER BY `date` DESC");

$count_online = number_format(($sql_user->num_rows + $sql_guest->num_rows), 0, ".", "`");

echo '<div style="font-weight:bold; margin-top:10px;">Всего on-line: '.$count_online.'</div>';

echo '<table class="tables" style="margin:2px auto;">';
echo '<thead><tr align="center">';
	echo '<th width="200">Логин юзера</th>';
	echo '<th width="150">Время активности</th>';
	echo '<th width="150">IP</th>';
	echo '<th>Страница</th>';
echo '</tr></thead>';

echo '<tbody>';
if($sql_user->num_rows>0) {
	while($row = $sql_user->fetch_assoc()) {
		echo '<tr align="center">';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["username"]!=false ? "<b>".$row["username"]."</b>" : "-").'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["username"]!=false ? "<b>".DATE("H:i:s", $row["date"]-5*60)."</b>" : DATE("H:i:s", $row["date"]-5*60)).'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["username"]!=false ? "<b>".$row["ip"]."</b>" : $row["ip"]).'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["pagetitle"]!=false ? "<b>".$row["pagetitle"]."</b>" : "-").'</td>';
		echo '</tr>';
	}
}
if($sql_guest->num_rows>0) {
	while($row = $sql_guest->fetch_assoc()) {
		echo '<tr align="center">';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["username"]!=false ? "<b>".$row["username"]."</b>" : "-").'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.DATE("H:i:s", $row["date"]-5*60).'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["username"]!=false ? "<b>".$row["ip"]."</b>" : $row["ip"]).'</td>';
			echo '<td align="center" style="padding:0px 5px; margin:0px 5px;">'.($row["pagetitle"]!=false ? $row["pagetitle"] : "-").'</td>';
		echo '</tr>';
	}
}
echo '</tbody>';

echo '</table>';

?>