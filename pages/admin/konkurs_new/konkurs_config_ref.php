<script type="text/javascript" src="js/jquery_min.js" ></script>
<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-ui-personalized-1.5.3.packed.js"></script>
<script type="text/javascript" src="js/ui.datepicker-ru.js"></script>
<script type="text/javascript">
	function gebi(id){
		return document.getElementById(id)
	}

/*<![CDATA[*/

	var $d = jQuery.noConflict();

	$d(document).ready(function() {

		$d.datepicker.setDefaults($d.datepicker.regional['ru']);

		$d("#startDate1,#endDate1").datepicker({
		    beforeShow: customRange,
		    yearRange: "<?php echo (DATE("Y")-1).":".(DATE("Y")+1);?>",
		    showOn: "both",
		    buttonImageOnly: true
		});

		$d("#startDate2,#endDate2").datepicker({
		    beforeShow: customRange,
		    yearRange: "<?php echo (DATE("Y")-1).":".(DATE("Y")+1);?>",
		    showOn: "both",
		    buttonImageOnly: true
		});

		$d("#startDate3,#endDate3").datepicker({
		    beforeShow: customRange,
		    yearRange: "<?php echo (DATE("Y")-1).":".(DATE("Y")+1);?>",
		    showOn: "both",
		    buttonImageOnly: true
		});

		$d("#startDate4,#endDate4").datepicker({
		    beforeShow: customRange,
		    yearRange: "<?php echo (DATE("Y")-1).":".(DATE("Y")+1);?>",
		    showOn: "both",
		    buttonImageOnly: true
		});
	});

	function customRange(input) {
		return {

		}
	}

/* ]]> */
</script>

<?php
echo '<h3 class="sp" style="margin:0; padding:0;"><b>КОНКУРС АКТИВНЫХ РЕФЕРАЛОВ</b></h3>';

$free_users = $mysqli->query("SELECT `id` FROM `tb_users` WHERE `referer`='' AND `user_status`='0' AND `not_get_ref`='0' AND `ban_date`='0'")->num_rows;
echo "На данный момент в системе свободных рефералов которых можно выдать в качестве приза: <b>".($free_users)."</b>";

$konk_ref_type_prize_arr = array("","Деньги","Баллы","Рефералы","% от потраченной суммы");

### КОНКУРС АКТИВНЫХ РЕФЕРАЛОВ ###
if( isset($_POST["type"]) && limpiar($_POST["type"])=="ref" ) {

	$konk_ref_date_start = (isset($_POST["konk_ref_date_start"])) ? abs(intval(strtotime(limpiar($_POST["konk_ref_date_start"])))) : "0";
	$konk_ref_date_end = (isset($_POST["konk_ref_date_end"])) ? abs(intval(strtotime(limpiar($_POST["konk_ref_date_end"])))) : "0";
	$konk_ref_min = (isset($_POST["konk_ref_min"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min"]))), 2)) : "1";
	$konk_ref_min_click = (isset($_POST["konk_ref_min_click"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min_click"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min_click"]))), 2)) : "1";
	$konk_ref_min_day_act = (isset($_POST["konk_ref_min_day_act"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min_day_act"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_min_day_act"]))), 2)) : "1";
	$konk_ref_autostart = (isset($_POST["konk_ref_autostart"]) && abs(intval(limpiar($_POST["konk_ref_autostart"])))==1) ? "1" : "0";

	$error = 0; $konk_ref_type_prize_error = 0; $konk_ref_all_count_prize = 0;
	for($y=1; $y<=3; $y++) {
		$konk_ref_count_prize[$y] = 0;

		$konk_ref_type_prize[$y] = (isset($_POST["konk_ref_type_prize"][$y]) && abs(intval(limpiar($_POST["konk_ref_type_prize"][$y])))==1) ? "1" : "0";

		for($i=1; $i<=10; $i++) {
			$konk_ref_prizes[$y][$i] = (isset($_POST["konk_ref_prizes"][$y][$i]) && $konk_ref_type_prize[$y]!=0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ref_prizes"][$y][$i]))), 2)) : "0";

			if(($y==2 | $y==3) && $konk_ref_type_prize[$y]==1) $konk_ref_prizes[$y][$i] = intval($konk_ref_prizes[$y][$i]);

			if($konk_ref_prizes[$y][$i]>0) {
				$konk_ref_count_prize[$y]++;
			}
		}

		if(array_sum($konk_ref_prizes[$y])==0) $konk_ref_type_prize[$y] = 0;

		if($konk_ref_type_prize[$y]==0) $konk_ref_type_prize_error++;
	}

	for($i=1; $i<=10; $i++) {
		if(($konk_ref_prizes[1][$i] + $konk_ref_prizes[2][$i] + $konk_ref_prizes[3][$i])!=0) $konk_ref_all_count_prize++;
	}

	$konk_summ_get_free_users = array_sum($konk_ref_prizes[3]);

	for($y=1; $y<=3; $y++) {
		for($i=1; $i<=10; $i++) {
			if($error>0) break;

			if($konk_ref_type_prize_error==3) {
				$error++;
				echo '<span class="msg-error">Необходимо выбрать хотябы один тип приза!</span>';
				break;
			}elseif($i<=3 && $konk_ref_prizes[1][$i]==0 && $konk_ref_prizes[2][$i]==0 && $konk_ref_prizes[3][$i]==0) {
				for($z=1; $z<=3; $z++) {
					if($konk_ref_type_prize[$z]==1 && $konk_ref_prizes[$z][$i]==0) {
						$error++;
						echo '<span class="msg-error">Необходимо указать приз('.$konk_ref_type_prize_arr[$z].') за '.$i.' место!</span>';
						break;
					}
				}
			}elseif($i>1 && $i<10 && ($konk_ref_prizes[1][$i]==0 && $konk_ref_prizes[2][$i]==0 && $konk_ref_prizes[3][$i]==0) && ($konk_ref_prizes[1][$i+1]>0 | $konk_ref_prizes[2][$i+1]>0 | $konk_ref_prizes[3][$i+1]>0)) {
				$error++;
				echo '<span class="msg-error">Необходимо указать приз за '.$i.' место!</span>';
				break;
			}elseif($i>1 && $konk_ref_prizes[$y][$i]>$konk_ref_prizes[$y][$i-1] && $konk_ref_prizes[$y][$i-1]!=0) {
				$error++;
				echo '<span class="msg-error">Приз('.$konk_ref_type_prize_arr[$y].') за '.$i.' место не может быть больше чем за '.($i-1).' место</span>';
				break;
			}
		}
	}

	$konk_ref_type_prize = implode("; ", $konk_ref_type_prize);
	$konk_ref_prizes_1 = implode("; ", $konk_ref_prizes[1]);
	$konk_ref_prizes_2 = implode("; ", $konk_ref_prizes[2]);
	$konk_ref_prizes_3 = implode("; ", $konk_ref_prizes[3]);
	$konk_ref_count_prize = implode("; ", $konk_ref_count_prize);

	$minus_free_users = explode("; ", $konk_ref_count_prize);
	$minus_free_users = $minus_free_users[2];

	if($konk_ref_date_end<$konk_ref_date_start) {
		$konk_ref_date_end_new = $konk_ref_date_start;
		$konk_ref_date_start_new = $konk_ref_date_end;
		$konk_ref_date_start = $konk_ref_date_start_new;
		$konk_ref_date_end = $konk_ref_date_end_new;
	}

/*
	if($error==0 && $konk_ref_date_start < (strtotime(date("d.m.Y"))) ) {
		echo '<span class="msg-error">Дата начала не может быть меньше '.DATE("d.m.Y").'г.</span>';
	}elseif($error==0 && $konk_ref_date_end < (strtotime(date("d.m.Y"))) ) {
		echo '<span class="msg-error">Дата окончания не может быть меньше '.DATE("d.m.Y").'г.</span>';
	}elseif($error==0 && $konk_ref_date_end == (strtotime(date("d.m.Y"))) ) {
		echo '<span class="msg-error">Дата окончания не может быть равна '.DATE("d.m.Y").'г.</span>';
	}elseif($error==0 && $konk_ref_date_end<$konk_ref_date_start) {
		echo '<span class="msg-error">Дата окончания не может быть меньше даты начала.</span>';
*/
	if($error==0 && $konk_ref_date_end==$konk_ref_date_start) {
		echo '<span class="msg-error">Дата окончания не может быть равна дате начала.</span>';
	}elseif(($konk_summ_get_free_users+$minus_free_users)>$free_users) {
		echo '<span class="msg-error">Призовой фонд по рефералам = '.$konk_summ_get_free_users.' реф. На данный момент в системе нет столько свободных рефералов которых можно выдать в качестве приза</span>';
	}elseif($error==0) {

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_date_start' WHERE `type`='ref' AND `item`='date_start'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_date_end' WHERE `type`='ref' AND `item`='date_end'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_min' WHERE `type`='ref' AND `item`='min_ref'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_min_click' WHERE `type`='ref' AND `item`='min_click'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_min_day_act' WHERE `type`='ref' AND `item`='min_day_act'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_autostart' WHERE `type`='ref' AND `item`='autostart'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ref_all_count_prize' WHERE `type`='ref' AND `item`='all_count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ref_count_prize' WHERE `type`='ref' AND `item`='count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ref_type_prize' WHERE `type`='ref' AND `item`='type_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ref_prizes_1' WHERE `type`='ref' AND `item`='prizes' AND `howmany`='1'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ref_prizes_2' WHERE `type`='ref' AND `item`='prizes' AND `howmany`='2'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ref_prizes_3' WHERE `type`='ref' AND `item`='prizes' AND `howmany`='3'");

		if($konk_ref_date_end>=time()) {
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='1' WHERE `type`='ref' AND `item`='status'");
		}else{
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='0' WHERE `type`='ref' AND `item`='status'");
		}

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1500); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}
$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='status'");
$konk_ref_status = $sql->fetch_object()->price;

if(count($_POST)>0) {
	$konk_ref_type_prize = explode("; ", $konk_ref_type_prize);

	$konk_ref_prizes[1] = explode("; ", $konk_ref_prizes_1);
	$konk_ref_prizes[2] = explode("; ", $konk_ref_prizes_2);
	$konk_ref_prizes[3] = explode("; ", $konk_ref_prizes_3);

	$konk_ref_count_prize = explode("; ", $konk_ref_count_prize);
}else{
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='date_start'");
	$konk_ref_date_start = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='date_end'");
	$konk_ref_date_end = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='count_prize'");
	$konk_ref_count_prize = explode("; ", $sql->fetch_object()->price_array);

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='min_ref'");
	$konk_ref_min = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='min_click'");
	$konk_ref_min_click = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='min_day_act'");
	$konk_ref_min_day_act = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='autostart'");
	$konk_ref_autostart = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='type_prize'");
	$konk_ref_type_prize = explode("; ", $sql->fetch_object()->price_array);

	for($y=1; $y<=3; $y++) {
		$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='prizes' AND `howmany`='$y'");
		$konk_ref_prizes[$y] = explode("; ", $sql->fetch_object()->price_array);
	}
}

echo '<form method="POST" action="" id="newform" autocomplete="off">';
	echo '<input type="hidden" name="type" value="ref">';
	echo '<table style="width:auto;">';
		echo '<tr>';
			echo '<th width="300">Параметр</th>';
			echo '<th>Значение</th>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Статус конкурса</b></td>';
			echo '<td align="left">'.($konk_ref_status==1 ? '<b style="color:green;">Активен</b>' : '<b style="color:red;">Не активен</b>').'</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Дата начала</b></td>';
			echo '<td align="left"><input type="text" name="konk_ref_date_start" id="startDate1" value="'.DATE("d.m.Y", $konk_ref_date_start).'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Дата окончания</b></td>';
			echo '<td align="left"><input type="text" name="konk_ref_date_end" id="endDate1" value="'.DATE("d.m.Y", $konk_ref_date_end).'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Минимальное кол-во рефералов для участия</b></td>';
			echo '<td align="left"><input type="text" name="konk_ref_min" value="'.$konk_ref_min.'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Минимальное кол-во кликов которое должен сделать реферал</b></td>';
			echo '<td align="left"><input type="text" name="konk_ref_min_click" value="'.$konk_ref_min_click.'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>На момент окончания конкурса последний вход реферала не позднее...,</b> (дней)</td>';
			echo '<td align="left"><input type="text" name="konk_ref_min_day_act" value="'.$konk_ref_min_day_act.'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Автозапуск</b></td>';
			echo '<td align="left">';
				echo '<input type="checkbox" name="konk_ref_autostart" value="1" '.($konk_ref_autostart == 1 ? 'checked="checked"' : false).'>&nbsp;-&nbsp;';
				echo '<b>Автозапуск конкурса после его окончания</b>, конкурс запустится автоматически с тем же промежутком времени';
			echo '</td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td align="left"><b>Тип приза</b></td>';
			echo '<td align="center">';
				echo '<table style="margin:0; padding:0;"><tr align="center">';
					for($y=1; $y<=3; $y++) {
						echo '<td align="center" valign="top" width="25%">';
							echo '<input type="checkbox" name="konk_ref_type_prize['.$y.']" value="1" '.($konk_ref_type_prize[$y-1] == 1 ? 'checked="checked"' : false).'><br>';
							echo "<b>$konk_ref_type_prize_arr[$y]</b>";
						echo '</td>';
					}
				echo '</tr></table>';
			echo '</td>';
		echo '</tr>';


		for($i=1; $i<=10; $i++) {
			echo '<tr>';
				echo '<td><b>Приз за '.$i.' место</b></td>';
				echo '<td align="center" style="margin:0; padding:0;">';
					echo '<table style="margin:0; padding:0;"><tr align="center">';
						for($y=1; $y<=3; $y++) {
							echo '<td align="center" width="25%">';
								echo '<input type="text" name="konk_ref_prizes['.$y.']['.$i.']" value="'.$konk_ref_prizes[$y][$i-1].'" class="ok" style="width:70%; text-align:center;" />';
							echo '</td>';
						}
					echo '</tr></table>';
				echo '</td>';
			echo '</tr>';
		}

		echo '<tr><td align="center" colspan="2"><input type="submit" class="sub-blue160" value="Cохранить изменения"></td></tr>';
	echo '</table>';
echo '</form>';
### КОНКУРС АКТИВНЫХ РЕФЕРАЛОВ ###

?>