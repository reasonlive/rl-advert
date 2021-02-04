<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

$sql = $mysqli->query("SELECT `id` FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

	$rid = $row["id"];

	$mysqli->query("UPDATE `tb_ads_task` SET `date_up`='".time()."', `ip`='$ip' WHERE `id`='$rid' AND `username`='$username'") or die($mysqli->error);
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
	exit();
}
?>