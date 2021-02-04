<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require("navigator/navigator.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список заблокированных пользователей</b></h3>';

$metode="name";
$search=false;
if(isset($_POST["search"])) {
	$metode = "name";
	$search = trim($_POST["search"]);

	if($search!="" && $metode!="") {
		$WHERE = "WHERE $metode LIKE '%$search%'";
		$WHERE_to_get = "&metode=$metode&search=$search";
	}else{
		$WHERE = false;
		$WHERE_to_get = false;
	}
}else{
	$WHERE = false;
	$WHERE_to_get = false;
}
if(isset($_GET["search"])) {
	$metode = "name";
	$search = trim($_GET["search"]);

	if($search!="" && $metode!="") {
		$WHERE = "WHERE $metode LIKE '%$search%'";
		$WHERE_to_get = "&metode=$metode&search=$search";
	}else{
		$WHERE = false;
		$WHERE_to_get = false;
	}
}


$perpage = 30;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_black_users` $WHERE");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;

if(isset($_GET["option"]) && limpiar($_GET["option"])=="dell") {
	$sql_с = $mysqli->query("SELECT `name` FROM `tb_black_users` WHERE `id`='$id'");
	if($sql_с->num_rows>0) {
		$row_c = $sql_с->fetch_array();
		$username_ban = $row_c["name"];

		$mysqli->query("DELETE FROM `tb_black_users` WHERE `id`='$id'") or die($mysqli->error);

		$mysqli->query("UPDATE `tb_users` SET `ban_date`='0' WHERE `username`='$username_ban'") or die($mysqli->error);

		echo '<br><span class="msg-ok">Блокировка с пользователя <b>'.$username_ban.'</b> снята.</span><br>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'")\', 2000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'"></noscript>';
	}else{
	}
}

$sql = $mysqli->query("SELECT * FROM `tb_black_users` $WHERE  ORDER BY `id` DESC LIMIT $start_pos,$perpage");
$all_users = $sql->num_rows;

echo '<table style="margin:0; padding:0; margin-bottom:20px;"><tr>';
echo '<td valign="top">';
	if($WHERE=="") {
		echo 'Пользователей заблокировано всего: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_users.'</b> из <b>'.$count.'</b>';
	}else{
	 	echo 'Найдено пользователей: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_users.'</b> из <b>'.$count.'</b>';
	}
echo '</td>';
echo '<td valign="top">';
	echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
	echo '<table style="margin:0; padding:0;">';
	echo '<tr align="center">';
	echo '<td nowrap="nowrap" width="100"><b>Поиск пользователя по логину:</b></th>';
	echo '<td nowrap="nowrap" width="150"><input type="text" class="ok" name="search" value="'.$search.'"></td>';
	echo '<td nowrap="nowrap" width="100"><input type="submit" value="Поиск" class="sub-green"></td>';
	echo '</form>';

	echo '<td align="center">';
		echo '<form method="get" action="">';
			echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
			echo '<input type="submit" value="Очистить поиск" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
echo '</td>';
echo '</tr>';
echo '</table>';

if($count>$perpage) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op$WHERE_to_get");

echo '<table class="tables" style="margin:1px auto;">';
echo '<thead><tr align="center">';
	echo '<th style="text-align:center;">#</th>';
	echo '<th style="text-align:center;">Логин</th>';
	echo '<th style="text-align:center;">Причина блокировки</th>';
	echo '<th style="text-align:center;">Дата блокировки</th>';
	echo '<th style="text-align:center;">IP</th>';
	echo '<th style="text-align:center;">Заблокировал</th>';
	echo '<th style="text-align:center;">Действие</th>';
echo '</tr>';

if($all_users>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'</td>';
			echo '<td><b>'.$row["name"].'</b></td>';
			echo '<td><b>'.$row["why"].'</b></td>';
			echo '<td><b>'.DATE("d.m.Y H:i", $row["time"]).'</b></td>';
			echo '<td align="center"><b>'.($row["ip"]!=false ? $row["ip"] : "-").'</b></td>';
			echo '<td align="center">'.($row["who_block"]!=false ? "<b>".$row["who_block"]."</b>" : '<span style="color:#9C9C9C;">Система</span>').'</td>';

			echo '<td width="190" nowrap="nowrap"><div align="center">';
				echo '<form method="get" action="" onClick=\'if(!confirm("Вы уверены что хотите снять блокировку с пользователя '.$row["name"].'?")) return false;\'>';
					echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
					echo '<input type="hidden" name="page" value="'.$page.'">';
					echo '<input type="hidden" name="option" value="dell">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="submit" value="Снять" class="sub-red">';
				echo '</form></div>';
			echo '</td>';
		echo '</tr>';
	}

	if($count>$perpage) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op$WHERE_to_get");
}else{
	echo '<tr>';
		echo '<td colspan="7" align="center" style="padding:0;"><b>Заблокированных пользователей нет</b></td>';
	echo '</tr>';
}
echo '</table>';

?>