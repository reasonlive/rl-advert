<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0; font-weight:bold;">Статистика биржи рефералов</h3>';

require("navigator/navigator.php");
$perpage = 30;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_refview`");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op");}

echo '<table class="tables" style="margin:2px auto;">';
echo '<tr>';
	echo '<th>#</th>';
	echo '<th>Дата / Время продажи</th>';
	echo '<th>Реферал</th>';
	echo '<th>Покупатель</th>';
	echo '<th>Продавец</th>';
	echo '<th>Стоимость</th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_refview` ORDER BY `id` DESC LIMIT $start_pos,$perpage");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'</td>';
			echo '<td>'.$row["date"].'</td>';
			echo '<td>'.$row["ref"].'</td>';
			echo '<td>'.$row["namep"].'</td>';
			echo '<td>'.$row["name"].'</td>';
			echo '<td>'.number_format($row["cena"], 2, ".", " ").'&nbsp;руб.</td>';
		echo '</tr>';
	}
}
echo '</table>';

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op");}
?>