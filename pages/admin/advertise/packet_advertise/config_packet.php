<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки пакетов рекламы</b></h3>';

if(count($_POST)>0) {
	$y=0;
	for($i=1; $i<=5; $i++) {
		$id[$i] = intval(trim($_POST["id_$i"]));

		$ds_plan[$i] = intval(trim($_POST["ds_plan_$i"]));
		$ds_timer[$i] = intval(trim($_POST["ds_timer_$i"]));
		$slink_plan[$i] = intval(trim($_POST["slink_plan_$i"]));
		$sban468_plan[$i] = intval(trim($_POST["sban468_plan_$i"]));
		$sban100_plan[$i] = intval(trim($_POST["sban100_plan_$i"]));
		$sban200_plan[$i] = intval(trim($_POST["sban200_plan_$i"]));
		$txtob_plan[$i] = intval(trim($_POST["txtob_plan_$i"]));
		$psdlink_plan[$i] = intval(trim($_POST["psdlink_plan_$i"]));
		$frm_plan[$i] = intval(trim($_POST["frm_plan_$i"]));
		$price[$i] = floatval(trim($_POST["price_$i"]));

		$mysqli->query("UPDATE `tb_config_packet` SET `ds_plan`='$ds_plan[$i]', `ds_timer`='$ds_timer[$i]', `slink_plan`='$slink_plan[$i]', `sban468_plan`='$sban468_plan[$i]', `sban100_plan`='$sban100_plan[$i]', `sban200_plan`='$sban200_plan[$i]', `txtob_plan`='$txtob_plan[$i]', `psdlink_plan`='$psdlink_plan[$i]', `frm_plan`='$frm_plan[$i]', `price`='$price[$i]' WHERE `id`='$id[$i]'") or die($mysqli->error);
		$y++;
	}

	if($y==5) {
		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}else{
		echo '<span id="info-msg" class="msg-error">Ошибка сохранения!</span>';
	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}


$tabla= $mysqli->query("SELECT * FROM `tb_config_packet` ORDER BY `packet` ASC");
if($tabla->num_rows>0) {
	echo '<form action="" method="post" id="newform">';
	echo '<table class="tables" style="">';
	echo '<tr>';
	echo '<th rowspan="2"><div align="center">№</div></th>';
	echo '<th colspan="2"><div align="center">Динамические ссылки</div></th>';
	echo '<th rowspan="2"><div align="center">Псевдо-динамические ссылки,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Статические ссылки,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Ссылки во фрейме,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Текстовые объявления,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Баннер 100x100,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Баннер 100x100,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Баннер 200x300,<br>кол-во дней</div></th>';
	echo '<th rowspan="2"><div align="center">Цена пакета,<br>руб.</div></th>';
	echo '</tr>';
	echo '<tr>';
	echo '<th><div align="center">Просмотров</div></th>';
	echo '<th><div align="center">Таймер</div></th>';
	echo '</tr>';

	while ($row_p = $tabla->fetch_array()) {
		echo '<tr align="center">';
		echo '<td><input type="hidden" name="id_'.$row_p["id"].'" value="'.$row_p["packet"].'"><b style="color:#000">'.$row_p["packet"].'</b></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="ds_plan_'.$row_p["id"].'" value="'.$row_p["ds_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="ds_timer_'.$row_p["id"].'" value="'.$row_p["ds_timer"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="psdlink_plan_'.$row_p["id"].'" value="'.$row_p["psdlink_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="slink_plan_'.$row_p["id"].'" value="'.$row_p["slink_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="frm_plan_'.$row_p["id"].'" value="'.$row_p["frm_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="txtob_plan_'.$row_p["id"].'" value="'.$row_p["txtob_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="sban468_plan_'.$row_p["id"].'" value="'.$row_p["sban468_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="sban100_plan_'.$row_p["id"].'" value="'.$row_p["sban100_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="sban200_plan_'.$row_p["id"].'" value="'.$row_p["sban200_plan"].'"></td>';
		echo '<td><input type="text" style="text-align:center; width:60px" class="ok12" name="price_'.$row_p["id"].'" value="'.$row_p["price"].'"></td>';
		echo '</tr>';
	}
	echo '<tr><td align="center" colspan="11"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';

	echo '</table>';
	echo '</form>';

}
?>