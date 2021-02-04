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
echo '<h3 class="sp" style="margin:0; padding:0;"><b>КОМПЛЕКСНЫЙ КОНКУРС</b></h3>';

function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
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

$free_users = $mysqli->query("SELECT `id` FROM `tb_users` WHERE `referer`='' AND `user_status`='0' AND `not_get_ref`='0' AND `ban_date`='0'")->num_rows;
echo "На данный момент в системе свободных рефералов которых можно выдать в качестве приза: <b>".($free_users)."</b>";

$konk_complex_type_prize_arr = array("", 
	"<b>Деньги</b><br>[ осн. счет ]", 
	"<b>Деньги</b><br>[ рекл. счет ]", 
	"<b>Баллы</b><br>[ рейтинг ]",
	"<b>Рефералы</b><br>"
);

$points_arr = array(
	"serf"    => "<b>Серфинг</b>", 
	"youtub"    => "<b>Серфинг <span style=\"color: #3F3F3F;\">You</span><span style=\"border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;\">Tube</span></b>", 
	"aserf"    => "<b>Автосерфинг</b>", 
	"mail"    => "<b>Письма</b>", 
	"task"    => "<b>Задания</b><br>[&nbsp;одноразовые&nbsp;]", 
	"task_re" => "<b>Задания</b><br>[&nbsp;многоразовые&nbsp;]", 
	"test"    => "<b>Тесты</b>", 
	"refs"    => "<b>Привлечение реферала</b>"
);
$points_key_arr = array_keys($points_arr);
$points_val_arr = array_values($points_arr);

### КОМПЛЕКСНЫЙ КОНКУРС ###
if( isset($_POST["type"]) && limpiar($_POST["type"])=="complex" ) {

	$konk_complex_title = (isset($_POST["konk_complex_title"])) ? limitatexto(limpiarez($_POST["konk_complex_title"]),300) : false;
	$konk_complex_date_start = (isset($_POST["konk_complex_date_start"])) ? abs(intval(strtotime(limpiar($_POST["konk_complex_date_start"])))) : "0";
	$konk_complex_date_end = (isset($_POST["konk_complex_date_end"])) ? abs(intval(strtotime(limpiar($_POST["konk_complex_date_end"])))) : "0";
	$konk_complex_min = (isset($_POST["konk_complex_min"]) && abs(round( intval(str_replace(",",".", limpiar($_POST["konk_complex_min"]))), 2))>0) ? abs(round( intval(str_replace(",",".", limpiar($_POST["konk_complex_min"]))), 2)) : "1";
	$konk_complex_autostart = (isset($_POST["konk_complex_autostart"]) && abs(intval(limpiar($_POST["konk_complex_autostart"])))==1) ? "1" : "0";

	foreach($points_key_arr as $key) {
		$konk_complex_point[$key] = isset($_POST["konk_complex_point"][$key]) ? abs(round(intval(str_replace(",",".", limpiar($_POST["konk_complex_point"][$key]))), 0)) : "0";
	}

	$error = 0; $konk_complex_type_prize_error = 0; $konk_complex_all_count_prize = 0;
	for($y=1; $y<=4; $y++) {
		$konk_complex_count_prize[$y] = 0;

		$konk_complex_type_prize[$y] = (isset($_POST["konk_complex_type_prize"][$y]) && abs(intval(limpiar($_POST["konk_complex_type_prize"][$y])))==1) ? "1" : "0";

		for($i=1; $i<=10; $i++) {
			$konk_complex_prizes[$y][$i] = (isset($_POST["konk_complex_prizes"][$y][$i]) && $konk_complex_type_prize[$y]!=0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_complex_prizes"][$y][$i]))), 2)) : "0";

			if(($y==3 | $y==4) && $konk_complex_type_prize[$y]==1) $konk_complex_prizes[$y][$i] = intval($konk_complex_prizes[$y][$i]);

			if($konk_complex_prizes[$y][$i]>0) {
				$konk_complex_count_prize[$y]++;
			}
		}

		if(array_sum($konk_complex_prizes[$y])==0) $konk_complex_type_prize[$y] = 0;

		if($konk_complex_type_prize[$y]==0) $konk_complex_type_prize_error++;
	}

	for($i=1; $i<=10; $i++) {
		if(($konk_complex_prizes[1][$i] + $konk_complex_prizes[2][$i] + $konk_complex_prizes[3][$i] + $konk_complex_prizes[4][$i])!=0) $konk_complex_all_count_prize++;
	}

	$konk_summ_get_free_users = array_sum($konk_complex_prizes[4]);

	for($y=1; $y<=4; $y++) {
		for($i=1; $i<=10; $i++) {
			if($error>0) break;

			if($konk_complex_type_prize_error==4) {
				$error++;
				echo '<span id="info-msg"  class="msg-error">Необходимо выбрать хотябы один тип приза!</span>';
				break;
			}elseif($i<=3 && $konk_complex_prizes[1][$i]==0 && $konk_complex_prizes[2][$i]==0 && $konk_complex_prizes[3][$i]==0 && $konk_complex_prizes[4][$i]==0) {
				for($z=1; $z<=4; $z++) {
					if($konk_complex_type_prize[$z]==1 && $konk_complex_prizes[$z][$i]==0) {
						$error++;
						echo '<span id="info-msg" class="msg-error">Необходимо указать приз('.str_replace("<br>", " ", $konk_complex_type_prize_arr[$z]).') за '.$i.' место!</span>';
						break;
					}
				}
			}elseif($i>1 && $i<10 && ($konk_complex_prizes[1][$i]==0 && $konk_complex_prizes[2][$i]==0 && $konk_complex_prizes[3][$i]==0 && $konk_complex_prizes[4][$i]==0) && ($konk_complex_prizes[1][$i+1]>0 | $konk_complex_prizes[2][$i+1]>0 | $konk_complex_prizes[3][$i+1]>0 | $konk_complex_prizes[4][$i+1]>0)) {
				$error++;
				echo '<span id="info-msg"  class="msg-error">Необходимо указать приз за '.$i.' место!</span>';
				break;
			}elseif($i>1 && $konk_complex_prizes[$y][$i]>$konk_complex_prizes[$y][$i-1] && $konk_complex_prizes[$y][$i-1]!=0) {
				$error++;
				echo '<span id="info-msg" class="msg-error">Приз('.str_replace("<br>", " ", $konk_complex_type_prize_arr[$y]).') за '.$i.' место не может быть больше чем за '.($i-1).' место</span>';
				break;
			}
		}
	}

	$konk_complex_type_prize = implode("; ", $konk_complex_type_prize);
	$konk_complex_prizes_1 = implode("; ", $konk_complex_prizes[1]);
	$konk_complex_prizes_2 = implode("; ", $konk_complex_prizes[2]);
	$konk_complex_prizes_3 = implode("; ", $konk_complex_prizes[3]);
	$konk_complex_prizes_4 = implode("; ", $konk_complex_prizes[4]);
	$konk_complex_count_prize = implode("; ", $konk_complex_count_prize);

	$minus_free_users = explode("; ", $konk_complex_count_prize);
	$minus_free_users = $minus_free_users[3];

	if($konk_complex_date_end<$konk_complex_date_start) {
		$konk_complex_date_end_new = $konk_complex_date_start;
		$konk_complex_date_start_new = $konk_complex_date_end;
		$konk_complex_date_start = $konk_complex_date_start;
		$konk_complex_date_end = $konk_complex_date_end_new;
	}

	if($error==0 && $konk_complex_date_end==$konk_complex_date_start) {
		echo '<span id="info-msg" class="msg-error">Дата окончания не может быть равна дате начала.</span>';
		$error++;

	}elseif(($konk_summ_get_free_users+$minus_free_users)>$free_users) {
		echo '<span id="info-msg" class="msg-error">Призовой фонд по рефералам = '.$konk_summ_get_free_users.' реф. На данный момент в системе нет столько свободных рефералов которых можно выдать в качестве приза</span>';
		$error++;

	}elseif($error==0) {

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_title' WHERE `type`='complex' AND `item`='title'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_date_start' WHERE `type`='complex' AND `item`='date_start'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_date_end' WHERE `type`='complex' AND `item`='date_end'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_min' WHERE `type`='complex' AND `item`='min_do'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_autostart' WHERE `type`='complex' AND `item`='autostart'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_all_count_prize' WHERE `type`='complex' AND `item`='all_count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_count_prize' WHERE `type`='complex' AND `item`='count_prize'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_type_prize' WHERE `type`='complex' AND `item`='type_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_prizes_1' WHERE `type`='complex' AND `item`='prizes' AND `howmany`='1'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_prizes_2' WHERE `type`='complex' AND `item`='prizes' AND `howmany`='2'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_prizes_3' WHERE `type`='complex' AND `item`='prizes' AND `howmany`='3'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_complex_prizes_4' WHERE `type`='complex' AND `item`='prizes' AND `howmany`='4'");

		foreach($points_key_arr as $key) {
			if(isset($konk_complex_point[$key])) $mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_complex_point[$key]' WHERE `type`='complex' AND `item`='point_".$key."'") or die($mysqli->error);
		}

		if($konk_complex_date_end>=time()) {
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='1' WHERE `type`='complex' AND `item`='status'");
		}else{
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='0' WHERE `type`='complex' AND `item`='status'");
		}

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, '.( (isset($error) && $error>0) ? "5000" : "2000").');
		HideMsg("info-msg", '.( (isset($error) && $error>0) ? "5000" : "2000").');
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="3;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='status'");
$konk_complex_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='title'");
$konk_complex_title = $sql->fetch_object()->price_array;

foreach($points_key_arr as $key) {
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='point_".$key."'") or die($mysqli->error);
	$konk_complex_point[$key] = $sql->fetch_object()->price;
}

if(count($_POST)>0) {
	$konk_complex_type_prize = explode("; ", $konk_complex_type_prize);

	$konk_complex_prizes[1] = explode("; ", $konk_complex_prizes_1);
	$konk_complex_prizes[2] = explode("; ", $konk_complex_prizes_2);
	$konk_complex_prizes[3] = explode("; ", $konk_complex_prizes_3);
	$konk_complex_prizes[4] = explode("; ", $konk_complex_prizes_4);

	$konk_complex_count_prize = explode("; ", $konk_complex_count_prize);
}else{
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='date_start'");
	$konk_complex_date_start = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='date_end'");
	$konk_complex_date_end = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='count_prize'");
	$konk_complex_count_prize = explode("; ", $sql->fetch_object()->price_array);

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='min_do'");
	$konk_complex_min = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='autostart'");
	$konk_complex_autostart = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='type_prize'");
	$konk_complex_type_prize = explode("; ", $sql->fetch_object()->price_array);

	for($y=1; $y<=4; $y++) {
		$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='prizes' AND `howmany`='$y'");
		$konk_complex_prizes[$y] = explode("; ", $sql->fetch_object()->price_array);
	}
}

echo '<form method="POST" action="" id="newform" autocomplete="off">';
echo '<input type="hidden" name="type" value="complex">';
echo '<table class="tables" style="margin:0; margin-top:4px; padding:0; width:900px;">';
echo '<thead><tr align="center">';
	echo '<th width="220px">Параметр</th>';
	echo '<th>Значение</th>';
echo '</tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td><b>Статус конкурса</b></td>';
		echo '<td align="left">'.($konk_complex_status==1 ? '<b style="color:green;">Активен</b>' : '<b style="color:red;">Не активен</b>').'</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Заголовок к конкурсу</b></td>';
		echo '<td align="left"><input type="text" name="konk_complex_title" value="'.$konk_complex_title.'" class="ok" maxlength="300"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Период проведения конкурса</b></td>';
		echo '<td align="left"><input type="text" name="konk_complex_date_start" id="startDate1" value="'.DATE("d.m.Y", $konk_complex_date_start).'" class="ok12" style="text-align:center;"> - <input type="text" name="konk_complex_date_end" id="endDate1" value="'.DATE("d.m.Y", $konk_complex_date_end).'" class="ok12" style="text-align:center;"><span style="padding-left:10px;">[ дата начала - окончания ]</span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Минимальное кол-во баллов</b></td>';
		echo '<td align="left"><input type="text" name="konk_complex_min" value="'.$konk_complex_min.'" class="ok12" style="text-align:center;"><span style="padding-left:10px;">[ для участия в конкурсе ]</span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Автозапуск конкурса</b></td>';
		echo '<td align="left">';
			echo '<div style="float:left;"><input type="checkbox" name="konk_complex_autostart" value="1" '.($konk_complex_autostart == 1 ? 'checked="checked"' : false).' style="height:16px; width:16px; margin:0px;"></div>';
			echo '<div style="float:left; padding-left:6px; padding-top:1px;"> - [ конкурс запустится автоматически с тем же промежутком времени, после его окончания ]</div>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Начисление баллов</b></td>';
		echo '<td align="left" style="margin:0; padding:5px 5px 5px 2px;">';
			echo '<table style="margin:0; padding:0; border:none; width:100%;">';
			echo '<tr align="center">';
				for($p=0; $p<count($points_arr); $p++) {
					echo '<td align="center" valign="top" style="margin:0; padding:5px 5px 0px 5px; border:none; width:'.round(100/count($points_arr),2).'%;">';
						echo $points_val_arr[$p];
					echo '</td>';
				}
			echo '</tr>';
			echo '<tr align="center">';
				for($p=0; $p<count($points_arr); $p++) {
					echo '<td align="center" valign="top" style="margin:0; padding:0px 5px 5px 5px; border:none; width:'.round(100/count($points_arr),2).'%;">';
						echo '<input type="text" name="konk_complex_point['.$points_key_arr[$p].']" value="'.$konk_complex_point[$points_key_arr[$p]].'" class="ok" style="text-align:center; width:95%;">';
					echo '</td>';
				}
			echo '</tr>';
			echo '</table>';
		echo '</td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td align="left"><b>Тип приза</b></td>';
		echo '<td align="left" style="margin:0; padding:0;">';
			echo '<table style="margin:0; padding:0; border:none; width:100%;"><tr align="center">';
				for($y=1; $y<=4; $y++) {
					echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:25%;">';
						echo '<input type="checkbox" name="konk_complex_type_prize['.$y.']" value="1" '.($konk_complex_type_prize[$y-1] == 1 ? 'checked="checked"' : false).' style="height:15px; width:15px; margin:0px;"><br>';
						echo $konk_complex_type_prize_arr[$y];
					echo '</td>';
				}
			echo '</tr></table>';
		echo '</td>';
	echo '</tr>';
	for($i=1; $i<=10; $i++) {
		echo '<tr>';
			echo '<td><b>Приз за '.$i.' место</b></td>';
			echo '<td align="left" style="margin:0; padding:0;">';
				echo '<table style="margin:0; padding:0; border:none; width:100%;"><tr align="center">';
					for($y=1; $y<=4; $y++) {
						echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:25%;">';
							echo '<input type="text" name="konk_complex_prizes['.$y.']['.$i.']" value="'.$konk_complex_prizes[$y][$i-1].'" class="ok12" style="text-align:center;" />';
						echo '</td>';
					}
				echo '</tr></table>';
			echo '</td>';
		echo '</tr>';
	}

	echo '<tr><td align="center" colspan="3"><input type="submit" class="sub-blue160" value="Cохранить изменения"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

### КОМПЛЕКСНЫЙ КОНКУРС ###
?>