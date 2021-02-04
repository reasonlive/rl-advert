<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Блокировка пользователей</b></h3>';

function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje), NULL, "cp1251");
	$mensaje = str_replace("?","&#063;",$mensaje);
	$mensaje = str_replace(">","&#062;",$mensaje);
	$mensaje = str_replace("<","&#060;",$mensaje);
	$mensaje = str_replace("'","&#039;",$mensaje);
	$mensaje = str_replace("`","&#096;",$mensaje);
	$mensaje = str_replace("$","&#036;",$mensaje);
	$mensaje = str_replace('"',"&#034;",$mensaje);
	$mensaje = str_replace("  "," ",$mensaje);
	$mensaje = str_replace("&amp amp ","&",$mensaje);
	$mensaje = str_replace("&&","&",$mensaje);
	$mensaje = str_replace("http://http://","http://",$mensaje);
	$mensaje = str_replace("https://https://","https://",$mensaje);
	$mensaje = str_replace("&#063;","?",$mensaje);
	return $mensaje;
}

if(count($_POST)>0) {
	$user_name_ban = (isset($_POST["username"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_POST["username"]))) ? uc($_POST["username"]) : false;
	$prichina = (isset($_POST["prichina"])) ? limitatexto(limpiarez($_POST["prichina"]),200) : false;

	if($user_name_ban==false) {
		echo '<span id="info-msg" class="msg-error">Необходимо указать логин пользователя!</span>';
	}elseif($prichina==false) {
		echo '<span id="info-msg" class="msg-error">Необходимо указать причину!</span>';
	}else{
		$sql_id = $mysqli->query("SELECT `username`,`wmid` FROM `tb_users` WHERE `username`='$user_name_ban'");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$user_name_ban = $row["username"];
			$user_wmid_ban = $row["wmid"];

			$sql_с = $mysqli->query("SELECT `id` FROM `tb_black_users` WHERE `name`='$user_name_ban'");
			if($sql_с->num_rows>0) {
				echo '<span id="info-msg" class="msg-error">Ошибка! Пользователь с логином '.$user_name_ban.' уже заблокирован</span>';
			}else{
				$mysqli->query("INSERT INTO `tb_black_users` (`name`,`why`,`ip`,`date`,`time`,`who_block`) 
				VALUES('$user_name_ban','$prichina','','".DATE("d.m.Y H:i")."','".time()."','$username')") or die($mysqli->error);

				$mysqli->query("UPDATE `tb_users` SET `ban_date`='".time()."' WHERE `username`='$user_name_ban' AND `ban_date`='0'") or die($mysqli->error);

				$sql_ch2 = $mysqli->query("SELECT `id` FROM `tb_black_wmid` WHERE `wmid`='$user_wmid_ban'") or die($mysqli->error);
				if($sql_ch2->num_rows<1) {
					$mysqli->query("INSERT INTO `tb_black_wmid` (`wmid`,`reason`,`date`,`ip`) 
					VALUES ('$user_wmid_ban','$prichina','".time()."','')") or die($mysqli->error);
				}

				echo '<span id="info-msg" class="msg-ok">Пользователь '.$user_name_ban.' заблокирован!</span>';
			}
		}else{
			echo '<span id="info-msg" class="msg-error">Пользователя с логином '.$user_name_ban.' нет в системе</span>';
		}
	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1000);
		HideMsg("info-msg", 1100);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

echo '<form action="" method="post" id="newform">';
echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<tr><th width="200">Параметр:</th><th>Значение</th></tr>';
	echo '<tr><td><b>Логин пользователя:</b></td><td><input type="text" name="username" class="ok" value=""></td></tr>';
	echo '<tr><td><b>Причина блокировки:</b></td><td><input type="text" name="prichina" class="ok" value=""> </td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" class="sub-blue160" value="Заблокировать"></td></tr>';
echo '</form>';
echo '</table>';

?>