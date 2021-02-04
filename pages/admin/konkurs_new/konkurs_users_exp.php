<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Исключить пользователя из системных конкурсов</b></h3>';
if(count($_POST)>0 && isset($_POST["option"]) && limpiar($_POST["option"])=="add_exp") {
	$user_name = (isset($_POST["user_name"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_POST["user_name"]))) ? uc($_POST["user_name"]) : false;

	if($user_name==false) {
		echo '<span id="info-msg" class="msg-error">Необходимо указать логин пользователя!</span>';
	}else{
		$sql_id = $mysqli->query("SELECT `username` FROM `tb_users` WHERE `username`='$user_name'");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_assoc();
			$user_name = $row["username"];

			$sql_exp = $mysqli->query("SELECT `id` FROM `tb_konkurs_exp` WHERE `user_name`='$user_name'");
			if($sql_exp->num_rows==0) {
				$mysqli->query("INSERT INTO `tb_konkurs_exp` (`user_name`,`time`) 
				VALUES('$user_name','".time()."')") or die($mysqli->error);

				//echo '<span id="info-msg" class="msg-ok">Пользователь '.$user_name.' успешно исключен из конкурсов!</span>';
			}else{
				echo '<span id="info-msg" class="msg-error">Пользователь '.$user_name.' уже есть в списке исключенных!</span>';
			}
		}else{
			echo '<span id="info-msg" class="msg-error">Пользователя с логином '.$user_name.' нет на проекте!</span>';
		}
	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 50);
		HideMsg("info-msg", 3000);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';

}

echo '<form action="" method="POST" id="newform">';
echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<input type="hidden" name="option" value="add_exp">';
	echo '<tr><th width="200">Параметр:</th><th>Значение</th></tr>';
	echo '<tr><td><b>Логин пользователя:</b></td><td><input type="text" name="user_name" class="ok" value="" /></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" class="sub-red" value="Исключить"></td></tr>';
echo '</table>';
echo '</form><br><br>';



echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список пользователей исключенных из системных конкурсов</b></h3>';

if(count($_POST)>0 && isset($_POST["option"]) && limpiar($_POST["option"])=="del_exp") {
	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;

	$sql_del = $mysqli->query("SELECT `id` FROM `tb_konkurs_exp` WHERE `id`='$id'") or die($mysqli->error);
	if($sql_del->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_konkurs_exp` WHERE `id`='$id'") or die($mysqli->error);
	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 50);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$perpage = 30;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_konkurs_exp`");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

if($count>$perpage) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op");
echo '<table class="tables" style="margin:1px auto;">';
echo '<tralign="center">';
	echo '<th>#</th>';
	echo '<th>Логин</th>';
	echo '<th>Дата добавления</th>';
	echo '<th>Действие</th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_konkurs_exp` ORDER BY `id` DESC LIMIT $start_pos,$perpage");
$all_users = $sql->num_rows;
if($all_users>0) {
	while ($row = $sql->fetch_assoc()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'</td>';
			echo '<td><b>'.$row["user_name"].'</b></td>';
			echo '<td><b>'.DATE("d.m.Y H:i", $row["time"]).'</b></td>';

			echo '<td width="190" nowrap="nowrap"><div align="center">';
				echo '<form method="POST" action="" onClick=\'if(!confirm("Вы уверены что хотите включить пользователя '.$row["user_name"].' снова в конкурсы?")) return false;\'>';
					echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
					echo '<input type="hidden" name="page" value="'.$page.'">';
					echo '<input type="hidden" name="option" value="del_exp">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="submit" value="Включить" class="sub-green">';
				echo '</form></div>';
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td colspan="4" align="center" style="padding:0;"><b>Исключенных пользователей нет</b></td>';
	echo '</tr>';
}
echo '</table>';
if($count>$perpage) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op");

?>