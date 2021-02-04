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
echo '<h3 class="sp" style="margin:0; padding:0;"><b>КОНКУРС РЕКЛАМОДАТЕЛЕЙ №2</b></h3>';

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

$konk_ads_big_type_prize_arr = array("", 
	"<b>Деньги</b><br>[осн. счет]", 
	"<b>Деньги</b><br>[рекл. счет]", 
	"<b>% от потраченной суммы</b><br>[осн. счет]", 
	"<b>% от потраченной суммы</b><br>[рекл. счет]", 
	"<b>Баллы</b><br>[рейтинг]"
);

### КОНКУРС РЕКЛАМОДАТЕЛЕЙ ###
if( isset($_POST["type"]) && limpiar($_POST["type"])=="ads_big" ) {

	$konk_ads_big_title = (isset($_POST["konk_ads_big_title"])) ? limitatexto(limpiarez($_POST["konk_ads_big_title"]),300) : false;
	$konk_ads_big_date_start = (isset($_POST["konk_ads_big_date_start"])) ? abs(intval(strtotime(limpiar($_POST["konk_ads_big_date_start"])))) : "0";
	$konk_ads_big_date_end = (isset($_POST["konk_ads_big_date_end"])) ? abs(intval(strtotime(limpiar($_POST["konk_ads_big_date_end"])))) : "0";
	$konk_ads_big_min = (isset($_POST["konk_ads_big_min"]) && abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ads_big_min"]))), 2))>0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ads_big_min"]))), 2)) : "1";
	$konk_ads_big_autostart = (isset($_POST["konk_ads_big_autostart"]) && abs(intval(limpiar($_POST["konk_ads_big_autostart"])))==1) ? "1" : "0";

	$error = 0; $konk_ads_big_type_prize_error = 0; $konk_ads_big_all_count_prize = 0;
	for($y=1; $y<=5; $y++) {
		$konk_ads_big_count_prize[$y] = 0;

		$konk_ads_big_type_prize[$y] = (isset($_POST["konk_ads_big_type_prize"][$y]) && abs(intval(limpiar($_POST["konk_ads_big_type_prize"][$y])))==1) ? "1" : "0";

		for($i=1; $i<=5; $i++) {
			$konk_ads_big_prizes[$y][$i] = (isset($_POST["konk_ads_big_prizes"][$y][$i]) && $konk_ads_big_type_prize[$y]!=0) ? abs(round( floatval(str_replace(",",".", limpiar($_POST["konk_ads_big_prizes"][$y][$i]))), 2)) : "0";

			if(($y==3 | $y==4 | $y==5) && $konk_ads_big_type_prize[$y]==1) $konk_ads_big_prizes[$y][$i] = intval($konk_ads_big_prizes[$y][$i]);

			if($konk_ads_big_prizes[$y][$i]>0) {
				$konk_ads_big_count_prize[$y]++;
			}
		}

		if(array_sum($konk_ads_big_prizes[$y])==0) $konk_ads_big_type_prize[$y] = 0;

		if($konk_ads_big_type_prize[$y]==0) $konk_ads_big_type_prize_error++;
	}

	for($i=1; $i<=5; $i++) {
		if(($konk_ads_big_prizes[1][$i] + $konk_ads_big_prizes[2][$i] + $konk_ads_big_prizes[3][$i] + $konk_ads_big_prizes[4][$i] + $konk_ads_big_prizes[5][$i])!=0) $konk_ads_big_all_count_prize++;
	}

	for($y=1; $y<=5; $y++) {
		for($i=1; $i<=5; $i++) {
			if($error>0) break;

			if($konk_ads_big_type_prize_error==5) {
				$error++;
				echo '<span class="msg-error">Необходимо выбрать хотябы один тип приза!</span>';
				break;
			}elseif($i<=1 && $konk_ads_big_prizes[1][$i]==0 && $konk_ads_big_prizes[2][$i]==0 && $konk_ads_big_prizes[3][$i]==0 && $konk_ads_big_prizes[4][$i]==0 && $konk_ads_big_prizes[5][$i]==0) {
				for($z=1; $z<=5; $z++) {
					if($konk_ads_big_type_prize[$z]==1 && $konk_ads_big_prizes[$z][$i]==0) {
						$error++;
						echo '<span class="msg-error">Необходимо указать приз<br>'.str_ireplace("<br>"," ", $konk_ads_big_type_prize_arr[$z]).' за '.$i.' место!</span>';
						break;
					}
				}
			}elseif($i>1 && $i<5 && ($konk_ads_big_prizes[1][$i]==0 && $konk_ads_big_prizes[2][$i]==0 && $konk_ads_big_prizes[3][$i]==0 && $konk_ads_big_prizes[4][$i]==0 && $konk_ads_big_prizes[5][$i]==0) && ($konk_ads_big_prizes[1][$i+1]>0 | $konk_ads_big_prizes[2][$i+1]>0 | $konk_ads_big_prizes[3][$i+1]>0 | $konk_ads_big_prizes[4][$i+1]>0 | $konk_ads_big_prizes[5][$i+1]>0)) {
				$error++;
				echo '<span class="msg-error">Необходимо указать приз за '.$i.' место!</span>';
				break;
			}elseif($i>1 && $konk_ads_big_prizes[$y][$i]>$konk_ads_big_prizes[$y][$i-1] && $konk_ads_big_prizes[$y][$i-1]!=0) {
				$error++;
				echo '<span class="msg-error">Приз: '.str_ireplace("<br>"," ", $konk_ads_big_type_prize_arr[$y]).' - за '.$i.' место не может быть больше чем за '.($i-1).' место</span>';
				break;
			}
		}
	}

	$konk_ads_big_type_prize = implode("; ", $konk_ads_big_type_prize);
	$konk_ads_big_prizes_1 = implode("; ", $konk_ads_big_prizes[1]);
	$konk_ads_big_prizes_2 = implode("; ", $konk_ads_big_prizes[2]);
	$konk_ads_big_prizes_3 = implode("; ", $konk_ads_big_prizes[3]);
	$konk_ads_big_prizes_4 = implode("; ", $konk_ads_big_prizes[4]);
	$konk_ads_big_prizes_5 = implode("; ", $konk_ads_big_prizes[5]);
	$konk_ads_big_count_prize = implode("; ", $konk_ads_big_count_prize);

	if($konk_ads_big_date_end<$konk_ads_big_date_start) {
		$konk_ads_big_date_end_new = $konk_ads_big_date_start;
		$konk_ads_big_date_start_new = $konk_ads_big_date_end;
		$konk_ads_big_date_start = $konk_ads_big_date_start;
		$konk_ads_big_date_end = $konk_ads_big_date_end_new;
	}

	if($error==0 && $konk_ads_big_date_end==$konk_ads_big_date_start) {
		$error++;
		echo '<span id="info-msg" class="msg-error">Дата окончания не может быть равна дате начала.</span>';
	}elseif($error==0) {

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_title' WHERE `type`='ads_big' AND `item`='title'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ads_big_date_start' WHERE `type`='ads_big' AND `item`='date_start'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ads_big_date_end' WHERE `type`='ads_big' AND `item`='date_end'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ads_big_min' WHERE `type`='ads_big' AND `item`='min_do'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ads_big_autostart' WHERE `type`='ads_big' AND `item`='autostart'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='$konk_ads_big_all_count_prize' WHERE `type`='ads_big' AND `item`='all_count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_count_prize' WHERE `type`='ads_big' AND `item`='count_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_type_prize' WHERE `type`='ads_big' AND `item`='type_prize'");

		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_prizes_1' WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='1'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_prizes_2' WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='2'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_prizes_3' WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='3'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_prizes_4' WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='4'");
		$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_ads_big_prizes_5' WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='5'");

		if($konk_ads_big_date_end>=time()) {
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='1' WHERE `type`='ads_big' AND `item`='status'");
		}else{
			$mysqli->query("UPDATE `tb_konkurs_conf` SET `price`='0' WHERE `type`='ads_big' AND `item`='status'");
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

$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='status'");
$konk_ads_big_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='title'");
$konk_ads_big_title = $sql->fetch_object()->price_array;

if(count($_POST)>0) {
	$konk_ads_big_type_prize = explode("; ", $konk_ads_big_type_prize);

	$konk_ads_big_prizes[1] = explode("; ", $konk_ads_big_prizes_1);
	$konk_ads_big_prizes[2] = explode("; ", $konk_ads_big_prizes_2);
	$konk_ads_big_prizes[3] = explode("; ", $konk_ads_big_prizes_3);
	$konk_ads_big_prizes[4] = explode("; ", $konk_ads_big_prizes_4);
	$konk_ads_big_prizes[5] = explode("; ", $konk_ads_big_prizes_5);

	$konk_ads_big_count_prize = explode("; ", $konk_ads_big_count_prize);
}else{
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='date_start'");
	$konk_ads_big_date_start = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='date_end'");
	$konk_ads_big_date_end = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='count_prize'");
	$konk_ads_big_count_prize = explode("; ", $sql->fetch_object()->price_array);

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='min_do'");
	$konk_ads_big_min = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='autostart'");
	$konk_ads_big_autostart = $sql->fetch_object()->price;

	$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='type_prize'");
	$konk_ads_big_type_prize = explode("; ", $sql->fetch_object()->price_array);

	for($y=1; $y<=5; $y++) {
		$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='prizes' AND `howmany`='$y'");
		$konk_ads_big_prizes[$y] = explode("; ", $sql->fetch_object()->price_array);
	}
}

echo '<form method="POST" action="" id="newform" autocomplete="off">';
echo '<input type="hidden" name="type" value="ads_big">';
echo '<table class="tables" style="margin:0; margin-top:4px; padding:0;">';
echo '<thead><tr align="center">';
	echo '<th width="220px">Параметр</th>';
	echo '<th>Значение</th>';
echo '</tr></thead>';
echo '<tbody>';
	echo '<tr>';
		echo '<td><b>Статус конкурса</b></td>';
		echo '<td align="left">'.($konk_ads_big_status==1 ? '<b style="color:green;">Активен</b>' : '<b style="color:red;">Не активен</b>').'</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Заголовок к конкурсу</b></td>';
		echo '<td align="left"><input type="text" name="konk_ads_big_title" value="'.$konk_ads_big_title.'" class="ok" maxlength="300"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Период проведения конкурса</b><br>[дата начала - дата окончания]</td>';
		echo '<td align="left"><input type="text" name="konk_ads_big_date_start" id="startDate1" value="'.DATE("d.m.Y", $konk_ads_big_date_start).'" class="ok12" style="text-align:center;"> - <input type="text" name="konk_ads_big_date_end" id="endDate1" value="'.DATE("d.m.Y", $konk_ads_big_date_end).'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Минимальное потраченная сумма</b><br>[для участия в конкурсе]</td>';
		echo '<td align="left"><input type="text" name="konk_ads_big_min" value="'.$konk_ads_big_min.'" class="ok12" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Автозапуск конкурса</b><br>[после его окончания]</td>';
		echo '<td align="left">';
			echo '<div style="float:left;"><input type="checkbox" name="konk_ads_big_autostart" value="1" '.($konk_ads_big_autostart == 1 ? 'checked="checked"' : false).' style="height:16px; width:16px; margin:0px;"></div>';
			echo '<div style="float:left; padding-left:6px; padding-top:1px;">- [конкурс запустится автоматически с тем же промежутком времени]</div>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Тип приза</b></td>';
		echo '<td align="left" style="margin:0; padding:0;">';
			echo '<table style="margin:0; padding:0; border:none; width:100%;"><tr align="center">';
				for($y=1; $y<=5; $y++) {
					echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:20%;">';
						echo '<input type="checkbox" name="konk_ads_big_type_prize['.$y.']" value="1" '.($konk_ads_big_type_prize[$y-1] == 1 ? 'checked="checked"' : false).' style="height:15px; width:15px; margin:0px;"><br>';
						echo $konk_ads_big_type_prize_arr[$y];
					echo '</td>';
				}
			echo '</tr></table>';
		echo '</td>';
	echo '</tr>';
	for($i=1; $i<=5; $i++) {
		echo '<tr>';
			echo '<td><b>Приз за '.$i.' место</b></td>';
			echo '<td align="left" style="margin:0; padding:0;">';
				echo '<table style="margin:0; padding:0; border:none; width:100%;"><tr align="center">';
					for($y=1; $y<=5; $y++) {
						echo '<td align="center" valign="top" style="margin:0; padding:5px; border:none; width:20%;">';
							echo '<input type="text" name="konk_ads_big_prizes['.$y.']['.$i.']" value="'.$konk_ads_big_prizes[$y][$i-1].'" class="ok12" style="text-align:center;" />';
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
### КОНКУРС РЕКЛАМОДАТЕЛЕЙ ###

?>