<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<div align="center" style="color:#0000FF; font-weight: bold; padding-bottom:10px; font-size:13pt;">Статусы пользователей на форуме</div>';

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


echo '<table style="width:300px">';
echo '<tr>';
	echo '<td class="center"><div align="center">';
			echo '<form method="GET" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="add_user">';
				echo '<input type="submit" value="Добавить пользователя" class="sub-blue160">';
			echo '</form>';
	echo '</div></td>';
	echo '<td class="center"><div align="center">';
			echo '<form method="GET" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="add_ban_user">';
				echo '<input type="submit" value="Забанить пользователя" class="sub-blue160">';
			echo '</form>';
	echo '</div></td>';
echo '</tr>';

echo '</table>';


$status_arr = array('Пользователь','Админ','Модератор','Консультант','Заблокированный&nbsp;пользователь');
$no_yes_arr = array('Нет','Да');

if(count($_GET)>0) {
	$id_user = (isset($_GET["id_user"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id_user"])) ) ? intval(limpiar(trim($_GET["id_user"]))) : false;
	$user_f = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
	$option = (isset($_GET["option"])) ? limpiar(trim($_GET["option"])) : false;

	if($option=="add_user") {
		$status_form="NO";

		if(count($_POST)>0) {
			$user_add = (isset($_POST["user_add"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_POST["user_add"]))) ? uc($_POST["user_add"]) : false;
			$forum_status = (isset($_POST["forum_status"]) && preg_match("|^[\d]$|", trim($_POST["forum_status"])) && (intval($_POST["forum_status"])>=1 | intval($_POST["forum_status"])<=3)) ? intval($_POST["forum_status"]) : false;

			if($user_add==false) {
				echo '<fieldset class="errorp">Ошибка! Необходимо ввести логин.</fieldset>';
			}elseif($forum_status==false){
				echo '<fieldset class="errorp">Ошибка! Не выбран статус.</fieldset>';
			}else{
				$sql_u = $mysqli->query("SELECT `id`,`username`,`forum_status` FROM `tb_users` WHERE `username`='$user_add'");
				if($sql_u->num_rows>0) {
					$row_u = $sql_u->fetch_array();
					$id_tab = $row_u["id"];
					$username = $row_u["username"];
					$forum_status_tab = $row_u["forum_status"];

					if($forum_status_tab==$forum_status) {
						echo '<fieldset class="errorp">Ошибка! У пользователя уже есть статус - '.$status_arr[$forum_status].'.</fieldset>';
					}else{
						$mysqli->query("UPDATE `tb_users` SET `forum_status`='$forum_status' WHERE `id`='$id_tab'") or die($mysqli->error);

						$status_form = "OK";
						echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
						echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
						exit();
					}
				}else{
					echo '<fieldset class="errorp">Ошибка! Пользователь не найден.</fieldset>';
				}
			}
		}

		if($status_form!="OK") {
			echo '<form method="POST" action="">';
			echo '<table style="width:500px;">';
				echo '<tr><th colspan="2" class="center">Добавление пользователя</th></tr>';
				echo '<tr>';
					echo '<td align="left"><b>Логин</b></td>';
					echo '<td align="left"><input type="text" name="user_add" value="" style="width:98%"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>Статус</b></td>';
					echo '<td align="left">';
						echo '<select name="forum_status" style="width:100%">';
							for($i=1; $i<=3; $i++) {
								echo '<option value="'.$i.'">'.$status_arr[$i].'</option>';
							}
						echo '</select>';
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="center" colspan="2"><center><input type="submit" value="Добавить" class="sub-green"></center></td>';
				echo '</tr>';
			echo '</table>';
			echo '</form>';
		}
	}

	if($option=="edit_user") {
		$status_form="NO";


		$sql_u = $mysqli->query("SELECT `id`,`username`,`forum_status`,`forum_b_u`,`forum_b_p`,`forum_b_e` FROM `tb_users` WHERE `id`='$id_user'");
		if($sql_u->num_rows>0) {
			$row_u = $sql_u->fetch_array();
			$id_tab = $row_u["id"];
			$username = $row_u["username"];
			$forum_status_tab = $row_u["forum_status"];

			if($forum_status_tab==4) {
				$display = 'style="display:block;"';
			}else{
				$display = 'style="display:none;"';
			}


			if(count($_POST)>0) {
				$forum_status = (isset($_POST["forum_status"]) && preg_match("|^[\d]$|", trim($_POST["forum_status"])) && (intval($_POST["forum_status"])>=0 | intval($_POST["forum_status"])<=3)) ? intval($_POST["forum_status"]) : false;

				if($forum_status==false){
					echo '<fieldset class="errorp">Ошибка! Не выбран статус.</fieldset>';
				}else{
					if($forum_status_tab==$forum_status) {
						echo '<fieldset class="errorp">Ошибка! У пользователя уже есть статус - '.$status_arr[$forum_status].'.</fieldset>';
					}else{
						$mysqli->query("UPDATE `tb_users` SET `forum_status`='$forum_status',`forum_b_u`='',`forum_b_p`='',`forum_b_e`='0' WHERE `id`='$id_tab'") or die($mysqli->error);

						$status_form = "OK";
						echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
						echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
						exit();
					}
				}
			}

			if($status_form!="OK") {
				echo '<form method="POST" action="">';
				echo '<table style="width:500px;">';
					echo '<tr><th colspan="2" class="center">Изменение статуса пользователя</th></tr>';
					echo '<tr>';
						echo '<td align="left"><b>Логин</b></td>';
						echo '<td align="left">'.$row_u["username"].'</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="left"><b>Статус</b></td>';
						echo '<td align="left">';
							echo '<select name="forum_status" style="width:100%">';
								for($i=1; $i<=3; $i++) {
									echo '<option value="'.$i.'" '.("$i" == "$forum_status_tab" ? 'selected="selected"' : false).'>'.$status_arr[$i].'</option>';
								}
							echo '</select>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td class="center" colspan="2"><center><input type="submit" value="Сохранить" class="sub-green"></center></td>';
					echo '</tr>';
				echo '</table>';
				echo '</form>';
			}
		}else{
			echo '<fieldset class="errorp">Ошибка! Пользователь не найден.</fieldset>';
		}
	}


	if($option=="add_ban_user") {
		$status_form="NO";

		$user_add = (isset($_POST["user_add"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_POST["user_add"]))) ? uc($_POST["user_add"]) : false;
		$forum_status = 4;
		$forum_b_p = (isset($_POST["forum_b_p"])) ? limitatexto(limpiarez($_POST["forum_b_p"]),250) : false;
		$forum_b_e = (isset($_POST["forum_b_e"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["forum_b_e"])) && intval(limpiar(trim($_POST["forum_b_e"])))>0) ? intval(limpiar(trim($_POST["forum_b_e"]))) : "30";

		if(count($_POST)>0) {
			if($user_add==false) {
				echo '<fieldset class="errorp">Ошибка! Необходимо ввести логин.</fieldset>';
			}elseif($forum_b_p==false){
				echo '<fieldset class="errorp">Ошибка! Не указана причина блокировки.</fieldset>';
			}elseif($forum_b_e==false){
				echo '<fieldset class="errorp">Ошибка! Не указан срок блокировки.</fieldset>';
			}else{
				$sql_u = $mysqli->query("SELECT `id`,`username`,`forum_status` FROM `tb_users` WHERE `username`='$user_add'");
				if($sql_u->num_rows>0) {
					$row_u = $sql_u->fetch_array();
					$id_tab = $row_u["id"];
					$username = $row_u["username"];
					$forum_status_tab = $row_u["forum_status"];

					if($forum_status_tab==$forum_status) {
						echo '<fieldset class="errorp">Ошибка! Пользователь '.$user_add.' уже заблокирован.</fieldset>';
					}else{
						$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ban' AND `howmany`='1'");
						$reit_ban = $sql->fetch_object()->price;

						$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`-'$reit_ban', `forum_status`='$forum_status',`forum_b_u`='$user_f',`forum_b_p`='$forum_b_p',`forum_b_e`='".(time()+$forum_b_e*24*60*60)."' WHERE `id`='$id_tab'") or die($mysqli->error);

						$status_form = "OK";
						echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
						echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
						exit();
					}
				}else{
					echo '<fieldset class="errorp">Ошибка! Пользователь не найден.</fieldset>';
				}
			}
		}

		if($status_form!="OK") {
			echo '<form method="POST" action="">';
			echo '<table style="width:500px;">';
				echo '<tr><th colspan="2" class="center">Блокировка пользователя</th></tr>';
				echo '<tr>';
					echo '<td align="left"><b>Логин</b></td>';
					echo '<td align="left"><input type="text" name="user_add" value="'.$user_add.'" style="width:98%"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>Причина блокировки</b></td>';
					echo '<td align="left"><input type="text" name="forum_b_p" value="'.$forum_b_p.'" style="width:98%" maxlength="250"></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td align="left"><b>Заблокировать на </b></td>';
					echo '<td align="left"><input type="text" name="forum_b_e" value="'.$forum_b_e.'" style="width:50px; text-align:right;"> дней</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="center" colspan="2"><center><input type="submit" value="Забанить" class="sub-green"></center></td>';
				echo '</tr>';
			echo '</table>';
			echo '</form>';
		}
	}

	if($option=="del_user") {
		$mysqli->query("UPDATE `tb_users` SET `forum_status`='0',`forum_b_u`='',`forum_b_p`='',`forum_b_e`='0' WHERE `id`='$id_user'") or die($mysqli->error);

		echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");</script> ';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}

echo '<table>';
echo '<tr>';
	echo '<th class="center" width="50">ID</th>';
	echo '<th class="center" width="100">Логин</th>';
	echo '<th class="center" width="150">Статус</th>';
	echo '<th class="center">Информация о блокировке</th>';
	echo '<th class="center" width="100"></th>';
	echo '<th class="center" width="100"></th>';
echo '</tr>';

$sql_u = $mysqli->query("SELECT `id`,`username`,`forum_mess`,`forum_status`,`forum_b_u`,`forum_b_p`,`forum_b_e` FROM `tb_users` WHERE `forum_status`>'0' ORDER BY `forum_status` ASC");
if($sql_u->num_rows>0) {
	while ($row_u = $sql_u->fetch_array()) {
		echo '<tr>';
			echo '<td class="center">'.$row_u["id"].'</td>';
			echo '<td class="center"><b>'.$row_u["username"].'</b></td>';
			echo '<td class="center">'.$status_arr[$row_u["forum_status"]].'</td>';

			if($row_u["forum_status"]==4) {
				echo '<td class="center">';
					if($row_u["forum_b_u"]!="") echo 'Заблокировал: '.$row_u["forum_b_u"];
					if($row_u["forum_b_p"]!="") echo '<br>Причина: '.$row_u["forum_b_p"];
					if($row_u["forum_b_e"]>0) echo '<br>Дата окончания блокировки: '.DATE("d.m.Yг. H:i",$row_u["forum_b_e"]);
				echo '</td>';
			}else{
				echo '<td class="center">-</td>';
			}

			echo '<td><div align="center"><form method="GET" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="edit_user">';
				echo '<input type="hidden" name="id_user" value="'.$row_u["id"].'">';
				echo '<input type="submit" value="Изменить" class="sub-green">';
			echo '</form></div></td>';

			echo '<td><div align="center"><form method="GET" action="" onsubmit=\'if(!confirm("Вы уверены что хотите снять статус с пользователя '.$row_u["username"].'!")) return false;\'">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="option" value="del_user">';
				echo '<input type="hidden" name="id_user" value="'.$row_u["id"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form></div></td>';
		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td colspan="6"><div align="center" style="color:#FF0000; font-weight: bold;">Пользователи не найдены</div></td>';
	echo '</tr>';
}

echo '</table>';

?>