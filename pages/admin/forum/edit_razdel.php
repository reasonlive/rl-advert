<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<div align="center" style="color:#0000FF; font-weight: bold; padding-bottom:10px; font-size:14pt;">Разделы форума</div>';

function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("'","",$mensaje);
	$mensaje = str_replace(";","",$mensaje);
	$mensaje = str_replace("$","",$mensaje);
	$mensaje = str_replace("<","",$mensaje);
	$mensaje = str_replace(">","",$mensaje);
	$mensaje = str_replace("`","",$mensaje);
	$mensaje = str_replace("&amp amp ","&",$mensaje);
	return $mensaje;
}

if(count($_GET)>0) {
	$id_r = (isset($_GET["id_r"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id_r"]))) ? intval(limpiar(trim($_GET["id_r"]))) : false;
	$id_pr = (isset($_GET["id_pr"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id_pr"]))) ? intval(limpiar(trim($_GET["id_pr"]))) : false;
	$user_f = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
	$option = (isset($_GET["option"])) ? limpiar(trim($_GET["option"])) : false;

	if($option=="add_r") {

		$status_add = "NO";

		if(count($_POST)>0) {
			$title_r = (isset($_POST["title_r"])) ? limitatexto(limpiarez($_POST["title_r"]),100) : false;

			if($title_r!=false) {
				$mysqli->query("INSERT INTO `tb_forum_r` (`username`,`razdel`,`date`) VALUES('$user_f','$title_r','".time()."')") or die($mysqli->error);

				$mysqli->query("ALTER TABLE `tb_forum_r` ORDER BY `id`") or die($mysqli->error);
				$mysqli->query("ALTER TABLE `tb_forum_pr` ORDER BY `id`") or die($mysqli->error);
				$mysqli->query("ALTER TABLE `tb_forum_t` ORDER BY `id`") or die($mysqli->error);
				$mysqli->query("ALTER TABLE `tb_forum_p` ORDER BY `id`") or die($mysqli->error);
				$mysqli->query("OPTIMIZE TABLE `tb_forum_r`") or die($mysqli->error);
				$mysqli->query("OPTIMIZE TABLE `tb_forum_pr`") or die($mysqli->error);
				$mysqli->query("OPTIMIZE TABLE `tb_forum_t`") or die($mysqli->error);
				$mysqli->query("OPTIMIZE TABLE `tb_forum_p`") or die($mysqli->error);

				$status_add = "OK";

				echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
				exit();
			}else{
				echo '<fieldset class="errorp">Ошибка! Не указано название раздела!</fieldset>';
			}
		}

		if($status_add!="OK") {
			echo '<form method="POST" action="">';
			echo '<table>';
				echo '<tr><th colspan="2" class="center">Создание раздела</th></tr>';
				echo '<tr><td>Дата создания</td><td>'.DATE("d.m.Yг. в H:i", time()).'</td></tr>';
				echo '<tr><td>Название раздела</td><td><input type="text" name="title_r" value="" maxlength="100" size="100"></td></tr>';
				echo '<tr><td></td><td><input type="submit" value="Добавить" class="sub-green"></td></tr>';
			echo '</table>';
			echo '</form>';
		}
	}


	if($option=="edit_r" && $id_r!=false) {

		$sql_r = $mysqli->query("SELECT * FROM `tb_forum_r` WHERE `id`='$id_r'");
		if($sql_r->num_rows>0) {
			$row_r = $sql_r->fetch_array();

			$status_edit = "NO";

			if(count($_POST)>0) {
				$title_r = (isset($_POST["title_r"])) ? limitatexto(limpiarez($_POST["title_r"]),100) : false;

				if($title_r!=false) {
					$mysqli->query("UPDATE `tb_forum_r` SET `razdel`='$title_r' WHERE `id`='$id_r'") or die($mysqli->error);

					$mysqli->query("ALTER TABLE `tb_forum_r` ORDER BY `id`") or die($mysqli->error);
					$mysqli->query("ALTER TABLE `tb_forum_pr` ORDER BY `id`") or die($mysqli->error);
					$mysqli->query("ALTER TABLE `tb_forum_t` ORDER BY `id`") or die($mysqli->error);
					$mysqli->query("ALTER TABLE `tb_forum_p` ORDER BY `id`") or die($mysqli->error);
					$mysqli->query("OPTIMIZE TABLE `tb_forum_r`") or die($mysqli->error);
					$mysqli->query("OPTIMIZE TABLE `tb_forum_pr`") or die($mysqli->error);
					$mysqli->query("OPTIMIZE TABLE `tb_forum_t`") or die($mysqli->error);
					$mysqli->query("OPTIMIZE TABLE `tb_forum_p`") or die($mysqli->error);

					$status_edit = "OK";

					echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
					echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
					exit();
				}else{
					echo '<fieldset class="errorp">Ошибка! Не указано название раздела!</fieldset>';
				}
			}

			if($status_edit!="OK") {
				echo '<form method="POST" action="">';
				echo '<table>';
					echo '<tr><th colspan="2" class="center">Редактирование раздела</th></tr>';
					echo '<tr><td>#</td><td>'.$row_r["id"].'</td></tr>';
					echo '<tr><td>Дата создания</td><td>'.DATE("d.m.Yг. в H:i",$row_r["date"]).'</td></tr>';
					echo '<tr><td>Название раздела</td><td><input type="text" name="title_r" value="'.$row_r["razdel"].'" maxlength="100" size="100"></td></tr>';
					echo '<tr><td></td><td><input type="submit" value="Сохранить" class="sub-green"></td></tr>';
				echo '</table>';
				echo '</form>';
			}
		}else{
			echo '<fieldset class="errorp">Ошибка! Раздел не найден!</fieldset>';
		}
	}

	if($option=="del_r" && $id_r!=false) {

		$sql_r = $mysqli->query("SELECT `id` FROM `tb_forum_r` WHERE `id`='$id_r'");
		if($sql_r->num_rows>0) {

			$mysqli->query("DELETE FROM `tb_forum_r` WHERE `id`='$id_r'") or die($mysqli->error);
			$mysqli->query("DELETE FROM `tb_forum_pr` WHERE `ident_r`='$id_r'") or die($mysqli->error);
			$mysqli->query("DELETE FROM `tb_forum_t` WHERE `ident_r`='$id_r'") or die($mysqli->error);
			$mysqli->query("DELETE FROM `tb_forum_p` WHERE `ident_r`='$id_r'") or die($mysqli->error);

			echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
			echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
			exit();
		}else{
			echo '<fieldset class="errorp">Ошибка! Раздел не найден!</fieldset>';
		}
	}
}

echo '<table>';
echo '<tr>';
	echo '<th class="center">#</th>';
	echo '<th class="center">Название раздела</th>';
	echo '<th class="center" width="100px">Создан</th>';
	echo '<th class="center" width="130px">Дата создания</th>';
	echo '<th class="center" width="100px"></th>';
	echo '<th class="center" width="100px"></th>';
echo '</tr>';

$sql_r = $mysqli->query("SELECT * FROM `tb_forum_r` ORDER BY `id` ASC");
if($sql_r->num_rows>0) {
	while ($row_r=$sql_r->fetch_array()) {
		echo '<tr>';
			echo '<td class="center">'.$row_r["id"].'</td>';
			echo '<td>'.$row_r["razdel"].'</td>';
			echo '<td class="center">'.$row_r["username"].'</td>';
			echo '<td class="center">'.DATE("d.m.Yг. в H:i",$row_r["date"]).'</td>';

			echo '<td><div align="center"><form method="GET" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="edit_r">';
				echo '<input type="hidden" name="id_r" value="'.$row_r["id"].'">';
				echo '<input type="submit" value="Изменить" class="sub-green">';
			echo '</form></div></td>';

			echo '<td><div align="center"><form method="GET" action="" onsubmit=\'if(!confirm("Вы уверены что хотите удалить раздел?\n\rПри удалении раздела будут также удалены его подразделы, темы и посты!")) return false;\'">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="del_r">';
				echo '<input type="hidden" name="id_r" value="'.$row_r["id"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form></div></td>';

		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td colspan="6"><div align="center" style="color:#FF0000; font-weight: bold;">Разделы не найдены</div></td>';
	echo '</tr>';
}

echo '<tr><th class="center" colspan="6">';
	echo '<div align="center">';
		echo '<form method="GET" action="">';
			echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
			echo '<input type="hidden" name="option" value="add_r">';
			echo '<input type="submit" value="Добавить раздел" class="sub-blue160">';
		echo '</form>';
	echo '</div>';
echo '</th></tr>';

echo '</table>';

?>