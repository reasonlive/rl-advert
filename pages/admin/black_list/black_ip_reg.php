<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;">Внести IP адрес или маску в черный список, запрещенных для регистрации.</h3>';

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
	if(isset($_POST["option"]) && limpiarez($_POST["option"])=="add_ban") {
		$ip_block = isset($_POST["ip_block"]) ? htmlspecialchars(trim($_POST["ip_block"])) : false;
		$cause = (isset($_POST["cause"])) ? limitatexto(limpiarez($_POST["cause"]),200) : false;

		$pattern_1 = "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/";
		$pattern_2 = "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.$/";
		$pattern_3 = "/^\d{1,3}\.\d{1,3}\.$/";
		preg_match($pattern_1, $ip_block, $matches_1);
		preg_match($pattern_2, $ip_block, $matches_2);
		preg_match($pattern_3, $ip_block, $matches_3);
		$matches_1 = isset($matches_1[0]) ? $matches_1[0] : false;
		$matches_2 = isset($matches_2[0]) ? $matches_2[0] : false;
		$matches_3 = isset($matches_3[0]) ? $matches_3[0] : false;

		if($ip_block==false) {
			echo '<span id="info-msg" class="msg-error">Необходимо указать IP адрес или маску!</span>';
		}elseif($matches_1==false && $matches_2==false && $matches_3==false) {
			echo '<span id="info-msg" class="msg-error">IP адрес или маска указаны не верно!</span>';
		}elseif($cause==false) {
			echo '<span id="info-msg" class="msg-error">Необходимо указать причину!</span>';
		}else{
			if($matches_1!=false) 		{ $ip_block = $matches_1; }
			elseif($matches_2!=false) 	{ $ip_block = $matches_2; }
			elseif($matches_3!=false) 	{ $ip_block = $matches_3; }

			$sql_с = $mysqli->query("SELECT `id` FROM `tb_black_ip` WHERE `ip_block`='$ip_block'");
			if($sql_с->num_rows>0) {
				echo '<span id="info-msg" class="msg-error">IP адрес или маска '.$ip_block.' уже есть в черном списке!</span>';
			}else{
				$mysqli->query("INSERT INTO `tb_black_ip` (`ip_block`,`who_block`,`cause`,`time`) 
				VALUES('$ip_block','$username','$cause','".time()."')") or die($mysqli->error);

				echo '<span id="info-msg" class="msg-ok">IP адрес или маска '.$ip_block.' успешно внесены в черный список!</span>';
				unset($ip_block, $cause);
			}
		}

		echo '<script type="text/javascript">
			setTimeout(function() {
				window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
			}, 100);
			HideMsg("info-msg", 2000);
		</script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';

	}
}

echo '<form action="" method="POST" id="newform">';
echo '<input type="hidden" name="option" value="add_ban" />';
echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<tr><th width="200">Параметр:</th><th>Значение</th></tr>';
	echo '<tr><td><b>IP адрес или маска:</b></td><td><input type="text" name="ip_block" class="ok" value="'.(isset($ip_block) ? $ip_block : false).'" placeholder="Пример: 178.213.151.12 или 178.213.151. или 178.151." autocomplete="off" /></td></tr>';
	echo '<tr><td><b>Причина блокировки:</b></td><td><input type="text" name="cause" class="ok" value="'.(isset($cause) ? $cause : false).'" /> </td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" class="sub-blue160" value="Заблокировать"></td></tr>';
echo '</form>';
echo '</table>';


echo '<h3 class="sp" style="margin-top:0; padding-top:40px;">Список IP адресов (или масок IP адресов), запрещенных для регистрации.</h3>';

if(count($_POST)>0) {
	if(isset($_POST["option"]) && limpiarez($_POST["option"])=="del_ban") {
		$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiarez(trim($_POST["id"]))) : false;

		$sql_с = $mysqli->query("SELECT `ip_block` FROM `tb_black_ip` WHERE `id`='$id'");
		if($sql_с->num_rows>0) {
			$row_c = $sql_с->fetch_assoc();
			$ip_block = $row_c["ip_block"];

			$mysqli->query("DELETE FROM `tb_black_ip` WHERE `id`='$id'") or die($mysqli->error);

			echo '<span id="info-msg" class="msg-ok">IP адрес или маска <b>'.$ip_block.'</b> успешно удалены из черного списка.</span>';
			unset($ip_block, $cause);
		}

		echo '<script type="text/javascript">
			setTimeout(function() {
				window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
			}, 100);
			HideMsg("info-msg", 1500);
		</script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';

	}
}

require("navigator/navigator.php");
$PER_PAGE = 30;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_black_ip`");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $PER_PAGE);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$START_POS = ($page - 1) * $PER_PAGE;
if($START_POS<0) $START_POS = 0;

$sql = $mysqli->query("SELECT * FROM `tb_black_ip` ORDER BY `id` DESC LIMIT $START_POS, $PER_PAGE");
$all_users = $sql->num_rows;

if($count>$PER_PAGE) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $PER_PAGE, 10, '&page=', "?op=$op");
echo '<table class="tables" style="margin:1px auto;">';
echo '<thead><tr>';
	echo '<th align="center">#</th>';
	echo '<th align="center">IP адрес или маска</th>';
	echo '<th align="center">Причина блокировки</th>';
	echo '<th align="center">Дата блокировки</th>';
	echo '<th align="center">Заблокировал</th>';
	echo '<th align="center">Действие</th>';
echo '</tr>';

echo '<tbody>';
if($all_users>0) {
	while ($row = $sql->fetch_assoc()) {
		echo '<tr align="center">';
			echo '<td>'.$row["id"].'</td>';
			echo '<td><b>'.$row["ip_block"].'</b></td>';
			echo '<td><b>'.$row["cause"].'</b></td>';
			echo '<td><b>'.DATE("d.m.Y H:i", $row["time"]).'</b></td>';
			echo '<td align="center">'.($row["who_block"]!=false ? "<b>".$row["who_block"]."</b>" : '<span style="color:#9C9C9C;">Система</span>').'</td>';

			echo '<td width="190" nowrap="nowrap"><div align="center">';
				echo '<form id="newform" action="" method="POST" onClick=\'if(!confirm("Удалить IP адрес '.$row["ip_block"].' из черного списка?")) return false;\'>';
					echo '<input type="hidden" name="option" value="del_ban">';
					echo '<input type="hidden" name="id" value="'.$row["id"].'">';
					echo '<input type="submit" value="Снять" class="sub-red">';
				echo '</form></div>';
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td colspan="6" align="center"><b>Список пуст!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';

if($count>$PER_PAGE) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $PER_PAGE, 10, '&page=', "?op=$op");

?>