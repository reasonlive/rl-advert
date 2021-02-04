<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<div align="center" style="color:#0000FF; font-weight: bold; padding-bottom:10px; font-size:13pt;">Статусы форума</div>';

$status_arr = array('Пользователь','Админ','Модератор','Консультант','Заблокированный&nbsp;пользователь');
$no_yes_arr = array('Нет','Да');

if(count($_GET)>0) {
	$id_s = (isset($_GET["id_s"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id_s"])) && (intval(limpiar(trim($_GET["id_s"])))>=0 | intval(limpiar(trim($_GET["id_s"])))<=4) ) ? intval(limpiar(trim($_GET["id_s"]))) : false;
	$user_f = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
	$option = (isset($_GET["option"])) ? limpiar(trim($_GET["option"])) : false;

	if($option=="edit_s") {

		$sql_s = $mysqli->query("SELECT * FROM `tb_forum_s` WHERE `id_status`='$id_s'");
		if($sql_s->num_rows>0) {
			$row_s = $sql_s->fetch_array();

			$status_form = "NO";

			if(count($_POST)>0) {
				$moder_post = (isset($_POST["moder_post"]) && preg_match("|^[\d]$|", trim($_POST["moder_post"])) && intval(limpiar(trim($_POST["moder_post"])))==1) ? "1" : "0";
				$ban_users = (isset($_POST["ban_users"]) && preg_match("|^[\d]$|", trim($_POST["ban_users"])) && intval(limpiar(trim($_POST["ban_users"])))==1) ? "1" : "0";
				$unban_users = (isset($_POST["unban_users"]) && preg_match("|^[\d]$|", trim($_POST["unban_users"])) && intval(limpiar(trim($_POST["unban_users"])))==1) ? "1" : "0";
				$add_theme = (isset($_POST["add_theme"]) && preg_match("|^[\d]$|", trim($_POST["add_theme"])) && intval(limpiar(trim($_POST["add_theme"])))==1) ? "1" : "0";
				$del_theme = (isset($_POST["del_theme"]) && preg_match("|^[\d]$|", trim($_POST["del_theme"])) && intval(limpiar(trim($_POST["del_theme"])))==1) ? "1" : "0";
				$close_theme = (isset($_POST["close_theme"]) && preg_match("|^[\d]$|", trim($_POST["close_theme"])) && intval(limpiar(trim($_POST["close_theme"])))==1) ? "1" : "0";
				$open_theme = (isset($_POST["open_theme"]) && preg_match("|^[\d]$|", trim($_POST["open_theme"])) && intval(limpiar(trim($_POST["open_theme"])))==1) ? "1" : "0";
				$re_theme = (isset($_POST["re_theme"]) && preg_match("|^[\d]$|", trim($_POST["re_theme"])) && intval(limpiar(trim($_POST["re_theme"])))==1) ? "1" : "0";
				$add_post = (isset($_POST["add_post"]) && preg_match("|^[\d]$|", trim($_POST["add_post"])) && intval(limpiar(trim($_POST["add_post"])))==1) ? "1" : "0";
				$del_post = (isset($_POST["del_post"]) && preg_match("|^[\d]$|", trim($_POST["del_post"])) && intval(limpiar(trim($_POST["del_post"])))==1) ? "1" : "0";
				$edit_post = (isset($_POST["edit_post"]) && preg_match("|^[\d]$|", trim($_POST["edit_post"])) && intval(limpiar(trim($_POST["edit_post"])))==1) ? "1" : "0";

				$mysqli->query("UPDATE `tb_forum_s` SET 
					`moder_post`='$moder_post',
					`ban_users`='$ban_users', 
					`unban_users`='$unban_users', 
					`add_theme`='$add_theme', 
					`del_theme`='$del_theme', 
					`close_theme`='$close_theme', 
					`open_theme`='$open_theme', 
					`re_theme`='$re_theme', 
					`add_post`='$add_post', 
					`del_post`='$del_post', 
					`edit_post`='$edit_post' 
				WHERE `id_status`='$id_s'") or die($mysqli->error);

				$status_form = "OK";
				echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
				exit();
			}

			if($status_form!="OK") {
				echo '<form method="POST" action="">';
				echo '<input type="hidden" name="id_s" value="'.$id_s.'">';
				echo '<table style="width:300px;">';
					echo '<tr><th colspan="2" class="center">Редактирование статуса: '.$status_arr[$id_s].'</th></tr>';
					echo '<tr>';
						echo '<td align="left">Модерация</td>';
						echo '<td class="center"><input type="checkbox" name="moder_post" value="1" '.("1" == $row_s["moder_post"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Блокировка пользователей</td>';
						echo '<td class="center"><input type="checkbox" name="ban_users" value="1" '.("1" == $row_s["ban_users"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Разблокировка пользователей</td>';
						echo '<td class="center"><input type="checkbox" name="unban_users" value="1" '.("1" == $row_s["unban_users"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr><td align="left">Создание темы</td>';
						echo '<td class="center"><input type="checkbox" name="add_theme" value="1" '.("1" == $row_s["add_theme"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Удаление темы</td>';
						echo '<td class="center"><input type="checkbox" name="del_theme" value="1" '.("1" == $row_s["del_theme"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Закрытие темы</td>';
						echo '<td class="center"><input type="checkbox" name="close_theme" value="1" '.("1" == $row_s["close_theme"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Открытие темы</td>';
						echo '<td class="center"><input type="checkbox" name="open_theme" value="1" '.("1" == $row_s["open_theme"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Перенос темы</td>';
						echo '<td class="center"><input type="checkbox" name="re_theme" value="1" '.("1" == $row_s["re_theme"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Добавление постов</td>';
						echo '<td class="center"><input type="checkbox" name="add_post" value="1" '.("1" == $row_s["add_post"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Удаление постов</td>';
						echo '<td class="center"><input type="checkbox" name="del_post" value="1" '.("1" == $row_s["del_post"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left">Изменение постов</td>';
						echo '<td class="center"><input type="checkbox" name="edit_post" value="1" '.("1" == $row_s["edit_post"] ? 'checked="checked"' : false).'></td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="center" colspan="2"><center><input type="submit" value="Сохранить" class="sub-green"></center></td>';
					echo '</tr>';
				echo '</table>';
				echo '</form>';
			}
		}else{
			echo '<fieldset class="errorp">Ошибка! Статус не найден</fieldset>';
		}
	}
}

echo '<table>';
echo '<tr>';
	echo '<th class="center" rowspan="2">Статус</th>';
	echo '<th class="center" colspan="11">Права</th>';
	echo '<th class="center" rowspan="2"></th>';
echo '</tr>';
echo '<tr>';
	echo '<th class="center">Модерация</th>';
	echo '<th class="center">Блокировка пользователей</th>';
	echo '<th class="center">Разблокировка пользователей</th>';
	echo '<th class="center">Создание темы</th>';
	echo '<th class="center">Удаление темы</th>';
	echo '<th class="center">Закрытие темы</th>';
	echo '<th class="center">Открытие темы</th>';
	echo '<th class="center">Перенос темы</th>';
	echo '<th class="center">Добавление постов</th>';
	echo '<th class="center">Удаление постов</th>';
	echo '<th class="center">Изменение постов</th>';
echo '</tr>';

$sql_s = $mysqli->query("SELECT * FROM `tb_forum_s` ORDER BY `id` ASC");
if($sql_s->num_rows>0) {
	while ($row_s=$sql_s->fetch_array()) {
		echo '<tr>';
			echo '<td class="center">'.$status_arr[$row_s["id_status"]].'</td>';

			echo '<td class="center">'.$no_yes_arr[$row_s["moder_post"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["ban_users"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["unban_users"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["add_theme"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["del_theme"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["close_theme"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["open_theme"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["re_theme"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["add_post"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["del_post"]].'</td>';
			echo '<td class="center">'.$no_yes_arr[$row_s["edit_post"]].'</td>';

			echo '<td><div align="center"><form method="GET" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="edit_s">';
				echo '<input type="hidden" name="id_s" value="'.$row_s["id_status"].'">';
				echo '<input type="submit" value="Изменить" class="sub-green">';
			echo '</form></div></td>';

		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td colspan="12"><div align="center" style="color:#FF0000; font-weight: bold;">Статусы не найдены</div></td>';
	echo '</tr>';
}
echo '</table>';



?>