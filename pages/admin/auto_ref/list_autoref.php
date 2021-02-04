<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список пользователей активировавших функцию автореферал</b></h3>';

if(count($_POST)>0) {
	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;
	$username = (isset($_POST["username"]) && preg_match("|^[a-zA-Z0-9\-_]{3,25}$|", trim($_POST["username"]))) ? uc($_POST["username"]) : false;

	if($id!=false && $username!=false) {
		$mysqli->query("DELETE FROM `tb_autoref` WHERE `id`='$id' AND `username`='$username'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_users` SET `autoref`='0', `autorefend`='0', `last_autoref`='0' WHERE `username`='$username'") or die($mysqli->error);

		echo '<span class="msg-ok">Функция деактивирована.</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}

}



echo '<table>';
echo '<thead>';
	echo '<tr>';
		echo '<th class="center">#</th>';
		echo '<th class="center">Логин</th>';
		echo '<th class="center">План, недель</th>';
		echo '<th class="center">Дата окончания</th>';
		echo '<th class="center">Сумма</th>';
		echo '<th class="center">IP</th>';
		echo '<th class="center" width="100px"></th>';
	echo '</tr>';
echo '</thead>';


$sql = $mysqli->query("SELECT * FROM `tb_autoref` WHERE  `time`>='".time()."' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr>';
			echo '<td class="center">'.$row["id"].'</td>';
			echo '<td class="center">'.$row["username"].'</td>';
			echo '<td class="center">'.$row["plan"].'</td>';
			echo '<td class="center">'.DATE("d.m.Y H:i",$row["time"]).'</td>';
			echo '<td class="center">'.$row["money"].'</td>';
			echo '<td class="center">'.$row["ip"].'</td>';

			echo '<td><div align="center"><form method="POST" action="" onsubmit=\'if(!confirm("Вы уверены, что хотите деактивировать функцию пользователю '.$row["username"].'?")) return false;\'">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="hidden" name="username" value="'.$row["username"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form></div></td>';
		echo '</tr>';
	}
}else{
	echo '<tr><td colspan="7"><div align="center" style="color:#FF0000; font-weight: bold;">Пользователей активировавших функцию нет!</div></td></tr>';
}

?>