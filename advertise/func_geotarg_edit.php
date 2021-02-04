<?php
$geo_codname_arr = array(
	1  => array('RU', 'Россия'),
	2  => array('UA', 'Украина'),
	3  => array('BY', 'Белоруссия'),
	4  => array('MD', 'Молдавия'),
	5  => array('KZ', 'Казахстан'),
	6  => array('AM', 'Армения'),
	7  => array('UZ', 'Узбекистан'),
	8  => array('LV', 'Латвия'),
	9  => array('DE', 'Германия'),
	10 => array('GE', 'Грузия'),
	11 => array('LT', 'Литва'),
	12 => array('FR', 'Франция'),
	13 => array('AZ', 'Азербайджан'),
	14 => array('US', 'США'),
	15 => array('VN', 'Вьетнам'),
	16 => array('PT', 'Португалия'),
	17 => array('GB', 'Англия'),
	18 => array('BE', 'Бельгия'),
	19 => array('ES', 'Испания'),
	20 => array('CN', 'Китай'),
	21 => array('TJ', 'Таджикистан'),
	22 => array('EE', 'Эстония'),
	23 => array('IT', 'Италия'),
	24 => array('KG', 'Киргизия'),
	25 => array('IL', 'Израиль'),
	26 => array('CA', 'Канада'),
	27 => array('TM', 'Туркменистан'),
	28 => array('BG', 'Болгария'),
	29 => array('IR', 'Иран'),
	30 => array('GR', 'Греция'),
	31 => array('TR', 'Турция'),
	32 => array('PL', 'Польша'),
	33 => array('FI', 'Финляндия'),
	34 => array('EG', 'Египет'),
	35 => array('SE', 'Швеция'),
	36 => array('RO', 'Румыния'),
);

//echo '<table width="98%">';
for ($i = 1; $i <= count($geo_codname_arr); $i++) {
	if($i==1 | $i==5 | $i==9 | $i==13 | $i==17 | $i==21 | $i==25 | $i==29 | $i==33) echo '<tr>';
		echo '<td width="25%">';
			echo '<input type="checkbox" id="country[]" name="country[]" value="'.$geo_codname_arr[$i]["0"].'" '.(strpos(strtoupper($row["geo_targ"]), strtoupper($geo_codname_arr[$i]["0"])) !== false ? 'checked="checked"' : false).' />';
			echo '<img src="//'.$_SERVER["HTTP_HOST"].'/img/flags/'.strtolower($geo_codname_arr[$i]["0"]).'.gif" alt="" align="absmiddle" style="margin:0; padding:0;" />&nbsp;'.$geo_codname_arr[$i]["1"].'';
		echo '</td>';
	if($i==4 | $i==8 | $i==12 | $i==16 | $i==20 | $i==24 | $i==28 | $i==32 | $i==36) echo '</tr>';
}
//echo '</table>';


?>