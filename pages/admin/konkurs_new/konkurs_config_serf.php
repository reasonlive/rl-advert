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
echo '<h3 class="sp" style="margin:0; padding:0;"><b>КОНКУРС ПО РАЗМЕЩЕНИЮ ССЫЛОК В СЕРФИНГЕ</b></h3>';

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

$konk_serf_type_prize_arr = array("", 
	"<b>Деньги</b><br>[осн. счет]", 
	"<b>Деньги</b><br>[рекл. счет]", 
	"<b>Баллы</b><br>[рейтинг]"
);

### КОНКУРС ПО РАЗМЕЩЕНИЮ ССЫЛОК В СЕРФИНГЕ ###
if( isset($_POST["type"]) && limpiar($_POST["type"])=="serf" ) {

	$konk_serf_title = (isset($_POST["konk_serf_title"])) ? limitatexto(limpiarez($_POST["konk_serf_title"]),300) : false;
	$konk_serf_date_start = (isset($_POST["konk_serf_date_start"])) ? abs(intval(strtotime(limpiar($_POST["konk_serf_date_start"])))) : "0";
	$konk_serf_date_end = (isset($_POST["konk_serf_date_end"])) ? abs(intval(strtotime(limpiar($_POST["konk_serf_date_end"])))) : "0";
	$konk_serf_min = (isset($_POST["konk_serf_min"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_serf_min"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_serf_min"]))), 2)) : "1";
	$konk_serf_autostart = (isset($_POST["konk_serf_autostart"]) && abs(intval(limpiar($_POST["konk_serf_autostart"])))==1) ? "1" : "0";
	$konk_serf_timer = (isset($_POST["konk_serf_timer"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_serf_timer"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_serf_timer"]))), 2)) : "1";

	$error = 0; $konk_serf_type_prize_error = 0; $konk_serf_all_count_prize = 0;
	for($y=1; $y<=3; $y++) {
		$konk_serf_count_prize[$y] = 0;

		$konk_serf_type_prize[$y] = (isset($_POST["konk_serf_type_prize"][$y]) && abs(intval(limpiar($_POST["konk_serf_type_prize"][$y])))==1) ? "1" : "0";

		for($i=1; $i<=10; $i++) {
			$konk_serf_prizes[$y][$i] = (isset($_POST["konk_serf_prizes"][$y][$i]) && $konk_serf_type_prize[$y]!=0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_serf_prizes"][$y][$i]))), 2)) : "0";

			if(($y==2 | $y==3) && $konk_serf_type_prize[$y]==1) $konk_serf_prizes[$y][$i] = intval($konk_serf_prizes[$y][$i]);

			if($konk_serf_prizes[$y][$i]>0) {
				$konk_serf_count_prize[$y]++;
			}
		}

		if(array_sum($konk_serf_prizes[$y])==0) $konk_serf_type_prize[$y] = 0;

		if($konk_serf_type_prize[$y]==0) $konk_serf_type_prize_error++;
	}

	for($i=1; $i<=10; $i++) {
		if(($konk_serf_prizes[1][$i] + $konk_serf_prizes[2][$i] + $konk_serf_prizes[3][$i])!=0) $konk_serf_all_count_prize++;
	}

	for($y=1; $y<=3; $y++) {
		for($i=1; $i<=10; $i++) {
			if($error>0) break;

			if($konk_serf_type_prize_error==3) {
				$error++;
				echo '<span class="msg-error">Необходимо выбрать хотябы один тип приза!</span>';
				break;
			}elseif($i<=1 && $konk_serf_prizes[1][$i]==0 && $konk_serf_prizes[2][$i]==0 && $konk_serf_prizes[3][$i]==0) {
				for($z=1; $z<=3; $z++) {
					if($konk_serf_type_prize[$z]==1 && $konk_serf_prizes[$z][$i]==0) {
						$error++;
						echo '<span class="msg-error">Необходимо указать приз<br>'.str_ireplace("<br>"," ", $konk_serf_type_prize_arr[$z]).' за '.$i.' место!</span>';
						break;
					}
				}
			}elseif($i>1 && $i<10 && ($konk_serf_prizes[1][$i]==0 && $konk_serf_prizes[2][$i]==0 && $konk_serf_prizes[3][$i]==0) && ($konk_serf_prizes[1][$i+1]>0 | $konk_serf_prizes[2][$i+1]>0 | $konk_serf_prizes[3][$i+1]>0)) {
				$error++;
				echo '<span class="msg-error">Необходимо указать приз за '.$i.' место!</span>';
				break;
			}elseif($i>1 && $konk_serf_prizes[$y][$i]>$konk_serf_prizes[$y][$i-1] && $konk_serf_prizes[$y][$i-1]!=0) {
				$error++;
				echo '<span class="msg-error">Приз: '.str_ireplace("<br>"," ", $konk_serf_type_prize_arr[$y]).' - за '.$i.' место не может быть больше чем за '.($i-1).' место</span>';
				break;
			}
		}
	}

	$konk_serf_type_prize = implode("; ", $konk_serf_type_prize);
	$konk_serf_prizes_1 = implode("; ", $konk_serf_prizes[1]);
	$konk_serf_prizes_2 = implode("; ", $konk_serf_prizes[2]);
	$konk_serf_prizes_3 = implode("; ", $konk_serf_prizes[3]);
	$konk_serf_count_prize = implode("; ", $konk_serf_count_prize);

	if($konk_serf_date_end<$konk_serf_date_start) {
		$konk_serf_date_end_new = $konk_serf_date_start;
		$konk_serf_date_start_new = $konk_serf_date_end;
		$konk_serf_date_start = $konk_serf_date_start;
		$konk_serf_date_end = $konk_serf_date_end_new;
	}

	if($error==0 && $konk_serf_date_end==$konk_serf_date_start) {
		$error++;
		echo '<span id="info-msg" class="msg-error">Дата окончания не может быть равна дате начала.</span>';
	}elseif($error==0) {

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_title' WHERE `type`='serf' AND `item`='title'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_date_start' WHERE `type`='serf' AND `item`='date_start'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_date_end' WHERE `type`='serf' AND `item`='date_end'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_min' WHERE `type`='serf' AND `item`='min_do'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_autostart' WHERE `type`='serf' AND `item`='autostart'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_all_count_prize' WHERE `type`='serf' AND `item`='all_count_prize'");
        $mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_serf_timer' WHERE `type`='serf' AND `item`='timer_ot'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_count_prize' WHERE `type`='serf' AND `item`='count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_type_prize' WHERE `type`='serf' AND `item`='type_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_prizes_1' WHERE `type`='serf' AND `item`='prizes' AND `howmany`='1'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_prizes_2' WHERE `type`='serf' AND `item`='prizes' AND `howmany`='2'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_serf_prizes_3' WHERE `type`='serf' AND `item`='prizes' AND `howmany`='3'");

		if($konk_serf_date_end>=time()) {
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='1' WHERE `type`='serf' AND `item`='status'");
		}else{
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='0' WHERE `type`='serf' AND `item`='status'");
		}

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, '.(isset($error) && $error>0 ? "4000" : "2000").');
		HideMsg("info-msg", '.(isset($error) && $error>0 ? "4000" : "2000").');
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="3;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='status'");
$konk_serf_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='title'");
$konk_serf_title = $sql->fetch_object()->price_array;

if(count($_POST)>0) {
	$konk_serf_type_prize = explode("; ", $konk_serf_type_prize);

	$konk_serf_prizes[1] = explode("; ", $konk_serf_prizes_1);
	$konk_serf_prizes[2] = explode("; ", $konk_serf_prizes_2);
	$konk_serf_prizes[3] = explode("; ", $konk_serf_prizes_3);

	$konk_serf_count_prize = explode("; ", $konk_serf_count_prize);
}else{
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='date_start'");
	$konk_serf_date_start = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='date_end'");
	$konk_serf_date_end = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='count_prize'");
	$konk_serf_count_prize = explode("; ", $sql->fetch_object()->price_array);

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='min_do'");
	$konk_serf_min = $sql->fetch_object()->price;
	
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='timer_ot'");
	$konk_serf_timer = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='autostart'");
	$konk_serf_autostart = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='type_prize'");
	$konk_serf_type_prize = explode("; ", $sql->fetch_object()->price_array);

	for($y=1; $y<=3; $y++) {
		$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='prizes' AND `howmany`='$y'");
		$konk_serf_prizes[$y] = explode("; ", $sql->fetch_object()->price_array);
	}
}

echo '<form method="POST" action="" id="newform" autocomplete="off">';
echo '<input type="hidden" name="type" value="serf">';
echo '<table class="tables" style="margin:0; margin-top:4px; padding:0;">';
echo '<thead><tr align="center">';
	echo '<th width="220px">Параметр</th>';
	echo '<th>Значение</th>';
echo '</tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td><b>Статус конкурса</b></td>';
		echo '<td align="left">'.($konk_serf_status==1 ? '<b style="color:green;">Активен</b>' : '<b style="color:red;">Не активен</b>').'</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Заголовок к конкурсу</b></td>';
		echo '<td align="left"><input type="text" name="konk_serf_title" value="'.$konk_serf_title.'" class="ok" maxlength="300"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Период проведения конкурса</b><br>[дата начала - дата окончания]</td>';
		echo '<td align="left"><input type="text" name="konk_serf_date_start" id="startDate1" value="'.DATE("d.m.Y", $konk_serf_date_start).'" class="ok12" style="text-align:center;"> - <input type="text" name="konk_serf_date_end" id="endDate1" value="'.DATE("d.m.Y", $konk_serf_date_end).'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Минимальное потраченная сумма</b><br>[для участия в конкурсе]</td>';
		echo '<td align="left"><input type="text" name="konk_serf_min" value="'.$konk_serf_min.'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
			echo '<td><b>Минимальный таймер</b><br>[для участия в конкурсе]</td>';
			echo '<td align="left"><input type="text" name="konk_serf_timer" value="'.$konk_serf_timer.'" class="ok12" style="text-align:center;"></td>';
		echo '</tr>';
	echo '<tr>';
		echo '<td><b>Автозапуск конкурса</b><br>[после его окончания]</td>';
		echo '<td align="left">';
			echo '<div style="float:left;"><input type="checkbox" name="konk_serf_autostart" value="1" '.($konk_serf_autostart == 1 ? 'checked="checked"' : false).' style="height:16px; width:16px; margin:0px;"></div>';
			echo '<div style="float:left; padding-left:6px; padding-top:1px;">- [конкурс запустится автоматически с тем же промежутком времени]</div>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Тип приза</b></td>';
		echo '<td align="left" style="margin:0; padding:0;">';
			echo '<table style="margin:0; padding:0; border:none; width:100%;"><tr align="center">';
				for($y=1; $y<=3; $y++) {
					echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:20%;">';
						echo '<input type="checkbox" name="konk_serf_type_prize['.$y.']" value="1" '.($konk_serf_type_prize[$y-1] == 1 ? 'checked="checked"' : false).' style="height:15px; width:15px; margin:0px;"><br>';
						echo $konk_serf_type_prize_arr[$y];
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
					for($y=1; $y<=3; $y++) {
						echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:20%;">';
							echo '<input type="text" name="konk_serf_prizes['.$y.']['.$i.']" value="'.$konk_serf_prizes[$y][$i-1].'" class="ok12" style="text-align:center;" />';
						echo '</td>';
					}
				echo '</tr></table>';
			echo '</td>';
		echo '</tr>';
	}
	echo '<tr><td align="center" colspan="2"><input type="submit" class="sub-blue160" value="Cохранить изменения"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';
### КОНКУРС ПО РАЗМЕЩЕНИЮ ССЫЛОК В СЕРФИНГЕ ###

?>