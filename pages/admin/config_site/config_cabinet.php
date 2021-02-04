<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка кабинета рекламодателя</b></h1>';

if(count($_POST)>0) {

	$cab_status = intval(abs(trim($_POST["status"])));
	$cab_start_sum = floatval(abs(trim($_POST["start_sum"])));
	$cab_shag_sum = floatval(abs(trim($_POST["shag_sum"])));
	$cab_start_proc = intval(abs(trim($_POST["start_proc"])));
	$cab_max_proc = intval(abs(trim($_POST["max_proc"])));
	$cab_shag_proc = floatval(abs(trim($_POST["shag_proc"])));

	if($cab_start_sum<=0) {
		echo '<span id="info-msg" class="msg-error">Начальная сумма должна быть больше нуля!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	}elseif($cab_start_proc<=0) {
		echo '<span id="info-msg" class="msg-error">Начальный процент должен быть больше нуля!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	}elseif($cab_shag_sum<=0) {
		echo '<span id="info-msg" class="msg-error">Шаг суммы должен быть больше нуля!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	}elseif($cab_shag_proc<=0) {
		echo '<span id="info-msg" class="msg-error">Шаг процента должен быть больше нуля!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	}elseif($cab_start_proc>$cab_max_proc) {
		echo '<span id="info-msg" class="msg-error">Максимльный процент скидки должен быть больше начального!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	}else{
		$save_post = 1;

		$mysqli->query("UPDATE `tb_cabinet` SET `status`='$cab_status', `start_sum`='$cab_start_sum', `shag_sum`='$cab_shag_sum', `start_proc`='$cab_start_proc', `max_proc`='$cab_max_proc', `shag_proc`='$cab_shag_proc' WHERE `id`='1'") or die($mysqli->error);
	}
}

$sql_cab = $mysqli->query("SELECT * FROM `tb_cabinet` WHERE `id`='1'");
if($sql_cab->num_rows>0) {
	$row_cab = $sql_cab->fetch_assoc();

	$cab_status = $row_cab["status"];
	$cab_start_sum = $row_cab["start_sum"];
	$cab_shag_sum = $row_cab["shag_sum"];
	$cab_start_proc = $row_cab["start_proc"];
	$cab_max_proc = $row_cab["max_proc"];
	$cab_shag_proc = $row_cab["shag_proc"];
}else{
	echo '<span class="msg-error">Ошибка чтения данных!</span>';
	exit();
}

$sql_cab = $mysqli->query("SELECT * FROM `tb_cabinet` WHERE `id`='1'");
if($sql_cab->num_rows>0) {
	$row_cab = $sql_cab->fetch_assoc();

	$cab_status = $row_cab["status"];
	$cab_start_sum = $row_cab["start_sum"];
	$cab_shag_sum = $row_cab["shag_sum"];
	$cab_start_proc = $row_cab["start_proc"];
	$cab_max_proc = $row_cab["max_proc"];
	$cab_shag_proc = $row_cab["shag_proc"];
}else{
	echo '<span class="msg-error">Ошибка чтения данных!</span>';
	exit();
}


echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:625px; margin:0px; padding:0px;">';
echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
	echo '<tr>';
		echo '<td align="left"><b>Статус активации скидки:</b></td>';
		echo '<td align="center">';
			echo '<select name="status" class="ok" style="width:124px; text-align:center;">';
				echo '<option value="0" '.("0" == $cab_status ? 'selected="selected"' : false).' style="color:#FF0000; font-weight: bold;">скидка отключена</option>';
				echo '<option value="1" '.("1" == $cab_status ? 'selected="selected"' : false).' style="color:#008B00; font-weight: bold;">скидка активна</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Начальная(потраченная) сумма для получения скидки</b>, (руб.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="start_sum" value="'.$cab_start_sum.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Начальный процент скидки</b>, (%)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="start_proc" value="'.$cab_start_proc.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Максимльный процент скидки</b>, (%)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="max_proc" value="'.$cab_max_proc.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Шаг суммы, для получения скидки</b>, (руб.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="shag_sum" value="'.$cab_shag_sum.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Шаг процента скидки</b>, (%)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="shag_proc" value="'.$cab_shag_proc.'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

if(count($_POST)>0 && isset($save_post)) {
	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1500);
		HideMsg("info-msg", 1510);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

?>