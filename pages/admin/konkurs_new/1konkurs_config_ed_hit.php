<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки ежедневного конкурса посетителей</b></h3>';

?><script type="text/javascript">
function setChecked(type){
	var nodes = document.getElementsByTagName("input");
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == "country[]") {
			if(type == "paste") nodes[i].checked = true;
			else  nodes[i].checked = false;
		}
	}
}
</script><?php

if(count($_POST)>0) {
	$konk_hit_ed_status = (isset($_POST["konk_hit_ed_status"])) ? intval(trim($_POST["konk_hit_ed_status"])) : false;

	$konk_hit_ed_count_prizes = 0; $error = 0;
	for($i=1; $i<=10; $i++) {
		$konk_hit_ed_prizes[$i] = (isset($_POST["konk_hit_ed_prizes$i"])) ? round( floatval(trim($_POST["konk_hit_ed_prizes$i"])), 3) : false;

		if($konk_hit_ed_prizes[$i]<0) {
			$error++;
			echo '<span class="msg-error" id="info-msg">Приз не может быть меньше 0</span>'; break;
		}elseif($konk_hit_ed_prizes[$i]==0 && $i<=3) {
			$error++;
			echo '<span class="msg-error" id="info-msg">Приз за '.$i.' место не может быть равен 0</span>'; break;
		}elseif($i>1 && $konk_hit_ed_prizes[$i]>$konk_hit_ed_prizes[$i-1]) {
			$error++;
			echo '<span class="msg-error" id="info-msg">Приз за '.$i.' место не может быть больше чем за '.($i-1).'</span>'; break;
		}

		if($konk_hit_ed_prizes[$i]>0) {
			$konk_hit_ed_count_prizes++;
		}
	}

	if($error==0) {
		$konk_hit_ed_prizes = implode("; ", $konk_hit_ed_prizes);

		mysql_query("UPDATE `tb_konkurs_conf` SET `price`='$konk_hit_ed_status' WHERE `type`='hit' AND `item`='status'") or die(mysql_error());
		mysql_query("UPDATE `tb_konkurs_conf` SET `price`='$konk_hit_ed_count_prizes' WHERE `type`='hit' AND `item`='all_count_prize'") or die(mysql_error());

		for($i=1; $i<=10; $i++) {
			mysql_query("UPDATE `tb_konkurs_conf` SET `price_array`='$konk_hit_ed_prizes' WHERE `type`='hit' AND `item`='prizes'") or die(mysql_error());
		}


		$country = (isset($_POST["country"]) && count($_POST["country"])>0) ? (array_map('mysql_real_escape_string', $_POST["country"])) : false;
		$geo_cod_arr = array(
			1  => 'RU', 2  => 'UA', 3  => 'BY', 4  => 'MD', 5  => 'KZ', 6  => 'AM', 7  => 'UZ', 8  => 'LV', 
			9  => 'DE', 10 => 'GE',	11 => 'LT', 12 => 'FR', 13 => 'AZ', 14 => 'US', 15 => 'VN', 16 => 'PT', 
			17 => 'GB', 18 => 'BE', 19 => 'ES', 20 => 'CN', 21 => 'TJ', 22 => 'EE', 23 => 'IT', 24 => 'KG', 
			25 => 'IL', 26 => 'CA', 27 => 'TM', 28 => 'BG', 29 => 'IR', 30 => 'GR', 31 => 'TR', 32 => 'PL', 
			33 => 'FI', 34 => 'EG', 35 => 'SE', 36 => 'RO',
		);
		if(is_array($country)) {
			foreach($country as $key => $val) {
				if(array_search($val, $geo_cod_arr)) {
					$id_country = array_search($val, $geo_cod_arr);
					$country_arr[] = $val;
				}
			}
		}
		$country = isset($country_arr) ? trim(strtoupper(implode('; ', $country_arr))) : false;

		mysql_query("UPDATE `tb_konkurs_conf` SET `price_array`='$country' WHERE `type`='hit' AND `item`='country'") or die(mysql_error());

		echo '<span class="msg-ok" id="info-msg">Изменения успешно сохранены!</span>';
	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 2000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='hit' AND `item`='status'");
if(mysql_num_rows($sql)>0) {
	$konk_hit_ed_status = mysql_result($sql,0,0);
}else{
	mysql_query("INSERT INTO `tb_konkurs_conf` (`type`,`item`,`howmany`,`price`,`price_array`) 
	VALUES('hit','status','1','0','')") or die(mysql_error());

	$konk_hit_ed_status = 0;
}

$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='hit' AND `item`='all_count_prize'");
if(mysql_num_rows($sql)>0) {
	$konk_hit_ed_count_prizes = mysql_result($sql,0,0);
}else{
	mysql_query("INSERT INTO `tb_konkurs_conf` (`type`,`item`,`howmany`,`price`,`price_array`) 
	VALUES('hit','all_count_prize','1','0','')") or die(mysql_error());

	$konk_hit_ed_count_prizes = 0;
}

$sql = mysql_query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='hit' AND `item`='prizes'");
if(mysql_num_rows($sql)>0) {
	$konk_hit_ed_prizes = mysql_result($sql,0,0);
	$konk_hit_ed_prizes = explode("; ", $konk_hit_ed_prizes);
}else{
	$konk_hit_ed_prizes = "0; 0; 0; 0; 0; 0; 0; 0; 0; 0";

	mysql_query("INSERT INTO `tb_konkurs_conf` (`type`,`item`,`howmany`,`price`,`price_array`) 
	VALUES('hit','prizes','1','0','$konk_hit_ed_prizes')") or die(mysql_error());

	$konk_hit_ed_prizes = explode("; ", $konk_hit_ed_prizes);
}


$sql = mysql_query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='hit' AND `item`='country'");
if(mysql_num_rows($sql)>0) {
	$konk_hit_ed_country = mysql_result($sql,0,0);
}else{
	mysql_query("INSERT INTO `tb_konkurs_conf` (`type`,`item`,`howmany`,`price`,`price_array`) 
	VALUES('hit','country','1','0','')") or die(mysql_error());

	$konk_hit_ed_country = false;
}

echo '<form method="post" action="" id="newform">';
echo '<table style="margin:0px; padding:0;">';
	echo '<thead><tr><th>Параметр</th><th width="120">Значение</th><th>Таргетинг по странам</th></tr></thead>';
	echo '<tr>';
		echo '<td><b>Статус конкурса</b><div style="display:block; float:right;">(включение/отключение)</div></td>';
			echo '<td>';
				echo '<select name="konk_hit_ed_status" class="ok" style="text-align:center;">';
					echo '<option value="0" '.("0" == $konk_hit_ed_status ? 'selected="selected"' : false).'>Не активен</option>';
					echo '<option value="1" '.("1" == $konk_hit_ed_status ? 'selected="selected"' : false).'>Активен</option>';
				echo '</select>';
			echo '</td>';
			echo '<td rowspan="11" valign="top">';
				echo '<table style="margin:0px; padding:0;">';
				echo '<tr>';
					echo '<td colspan="2" align="center" height="26px"><a onclick="setChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
					echo '<td colspan="2" align="center" height="26px"><a onclick="setChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
				echo '</tr>';
				include(DOC_ROOT."/advertise/func_geotarg_edit_konk.php");
				echo '</table>';
			echo '</td>';

	echo '</tr>';

	for($i=1; $i<=10; $i++) {
		echo '<tr>';
			echo '<td><b>Приз за '.$i.' место</b><div style="display:block; float:right;">(за каждого посетителя)</div></td>';
			echo '<td><input type="text" name="konk_hit_ed_prizes'.$i.'" value="'.number_format($konk_hit_ed_prizes[$i-1],3,".","").'" class="ok12" style="text-align:right;"></td>';
		echo '</tr>';
	}

	echo '<tr align="center"><td colspan="3"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>