<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

	if($row["plan"] > 0 && $row["totals"] > 0) {
		$mysqli->query("UPDATE `tb_ads_task` SET `status`='pay', `ip`='$ip' WHERE `id`='$rid' AND `username`='$username'") or die($mysqli->error);

		echo '<fieldset class="okp">Задание запущено!</fieldset>';

		echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view");</script>';
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view">';

		
		exit();
	}else{
		echo '<fieldset class="errorp">Ошибка! Необходимо пополнить баланс задания!</fieldset>';
	}
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
	
	exit();
}
?>