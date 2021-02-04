<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

	$id = $row["id"];
	$wait = $row["wait"];
	$totals = $row["totals"];
	$zdprice = $row["zdprice"];

	if($row["status"]=="wait" && $row["wait"]<=0 ) {

		$mysqli->query("DELETE FROM `tb_ads_task` WHERE `id`='$id' AND `status`='wait' AND `username`='$username'") or die($mysqli->error);
		echo '<fieldset class="okp">Задание #'.$id.' успешно удалено!</fieldset>';

	}elseif( $row["status"]=="pause" && $row["date_act"] > (time()-7*24*60*60) ) {

		echo '<fieldset class="errorp">Ошибка! Чтобы удалить задание, его необходимо остановить и подождать 7 дней. Если на задание не будет жалоб, то задание можно будет удалить.</fieldset>';

	}elseif( $row["status"]=="pay" | $row["date_act"] > (time()-7*24*60*60) ) {

		echo '<fieldset class="errorp">Ошибка! Чтобы удалить задание, его необходимо остановить и подождать 7 дней. Если на задание не будет жалоб, то задание можно будет удалить.</fieldset>';

	}elseif( $row["wait"] > 0 ) {

		echo '<fieldset class="errorp">Ошибка! Чтобы удалить задание необходимо проверить поданные заявки на проверку выполнения задания!</fieldset>';

	}elseif( $row["status"]=="pause" | $row["date_act"] < (time()-7*24*60*60) ) {

		$mysqli->query("DELETE FROM `tb_ads_task` WHERE `id`='$id' AND `status`='pause' AND `username`='$username'") or die($mysqli->error);
		echo '<fieldset class="okp">Задание #'.$id.' успешно удалено!</fieldset>';

	}elseif( $row["totals"] <= 0 ) {

		$mysqli->query("DELETE FROM `tb_ads_task` WHERE `id`='$id' AND `status`='pause' AND `username`='$username'") or die($mysqli->error);
		echo '<fieldset class="okp">Задание #'.$id.' успешно удалено!</fieldset>';

	}elseif( $row["status"]=="pause" && $row["totals"] > 0 ) {

		$mysqli->query("DELETE FROM `tb_ads_task` WHERE `id`='$id' AND `status`='pause' AND `username`='$username'") or die($mysqli->error);
		echo '<fieldset class="okp">Задание #'.$id.' успешно удалено!</fieldset>';

	}else{


	}


	echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view");</script>';
	echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view">';
	exit();
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
	exit();
}
?>