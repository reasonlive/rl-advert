<?php

function SFB_YANDEX($url, $typeCheck=false) {
	return false;
}
function my_num_format($number, $decimals, $dec_point=".", $thousands_sep="", $min_decimals=false) {
	$number = is_numeric($number) ? $number : 0;
	$min_decimals = $min_decimals===false ? $decimals : $min_decimals;
	$number = number_format($number, $decimals, ".", "");
	$number_arr = explode(".", $number);
	$fractions = isset($number_arr[1]) ? $number_arr[1] : false;
	$len = strlen($fractions)>$min_decimals ? strlen($fractions)-$min_decimals : 0;

	for($i=0; $i<$len; $i++) {
		$fractions = substr($fractions, -1)==0 ? substr($fractions, 0, -1) : $fractions;
	}

	return number_format($number, strlen($fractions), $dec_point, $thousands_sep);
}

function StringUrl($url) {
	$url_1 = @getHost($url);
	$url_2 = false;
	if($url_1!=false) {
		$url_arr = explode(".", $url_1);
		if(count($url_arr)>2) {
			array_shift($url_arr);
			$url_2 = (isset($url_arr) && is_array($url_arr)) ? implode(".", $url_arr) : false;
		}
	}
	return ($url_2!=false) ? "'$url_1', '$url_2'" : "'$url_1'";
}

function get_country($code) {
	$file = $_SERVER['DOCUMENT_ROOT']."/geoip/kodes_ru.txt";
	if(file_exists($file)) {
		$kodes = file($file);

		for ($i = 0; $i < count($kodes); $i++) {
			$explode = explode("|", $kodes[$i]);
			if(strtolower(trim($explode[0])) == strtolower(trim($code))) {
				return trim($explode[1]);
			}
		}
	}
}

function stats_users($USERNAME, $DAY, $TYPE) {
	global $mysqli;
	$sql_stat_us = $mysqli->query("SELECT `id` FROM `tb_users_stat` WHERE `username`='$USERNAME' AND `type`='$TYPE' ORDER BY `id` DESC LIMIT 1");
	if($sql_stat_us->num_rows>0) {
		$row_stat_us = $sql_stat_us->fetch_array();
		$id_stat_us = $row_stat_us["id"];

		$mysqli->query("UPDATE `tb_users_stat` SET `all_views`=`all_views`+'1', `month`=`month`+'1', `".$DAY."`=`".$DAY."`+'1' WHERE `id`='$id_stat_us'") or die($mysqli->error);
	}else{
		$mysqli->query("INSERT INTO `tb_users_stat` (`all_views`,`month`,`username`,`type`, `".$DAY."`) VALUES('1','1','$USERNAME','$TYPE','1')") or die($mysqli->error);
	}
}

function stats_users_reg($USERNAME) {
	global $mysqli;
	$type_arr = array('serf','auto_serf','mails','task','tests','you', 'autoyoutube_ser');
	$count_type = count($type_arr);

	$mysqli->query("DELETE FROM `tb_users_stat` WHERE `username`='$USERNAME'") or die($mysqli->error);

	for($i=0; $i<$count_type; $i++) {
		$mysqli->query("INSERT INTO `tb_users_stat` (`username`,`type`) VALUES('$USERNAME','".$type_arr[$i]."')") or die($mysqli->error);
	}
}

function stats_copilka($USERNAME, $DAY, $TYPE) {
	global $mysqli;
	$sql_stat_us = $mysqli->query("SELECT `id` FROM `tb_users_stat_copilka` WHERE `username`='$USERNAME' AND `type`='$TYPE' ORDER BY `id` DESC LIMIT 1");
	if($sql_stat_us->num_rows>0) {
		$row_stat_us = $sql_stat_us->fetch_array();
		$id_stat_us = $row_stat_us["id"];

		$mysqli->query("UPDATE `tb_users_stat_copilka` SET `all_views`=`all_views`+'1', `month`=`month`+'1', `".$DAY."`=`".$DAY."`+'1' WHERE `id`='$id_stat_us'") or die($mysqli->error);
	}else{
		$mysqli->query("INSERT INTO `tb_users_stat_copilka` (`all_views`,`month`,`username`,`type`, `".$DAY."`) VALUES('1','1','$USERNAME','$TYPE','1')") or die($mysqli->error);
	}
}

function stats_users_reg_copilka($USERNAME) {
	global $mysqli;
	$type_arr = array('serf_copilka');
	$count_type = count($type_arr);

	$mysqli->query("DELETE FROM `tb_users_stat_copilka` WHERE `username`='$USERNAME'") or die($mysqli->error);

	for($i=0; $i<$count_type; $i++) {
		$mysqli->query("INSERT INTO `tb_users_stat_copilka` (`username`,`type`) VALUES('$USERNAME','".$type_arr[$i]."')") or die($mysqli->error);
	}
}

function stats_users_pay($USERNAME, $DAY, $TYPE) {
	global $mysqli;
	$sql_stat_us = $mysqli->query("SELECT `id` FROM `tb_users_stat_pay` WHERE `username`='$USERNAME' AND `type`='$TYPE' ORDER BY `id` DESC LIMIT 1");
	if($sql_stat_us->num_rows>0) {
		$row_stat_us = $sql_stat_us->fetch_array();
		$id_stat_us = $row_stat_us["id"];

		$mysqli->query("UPDATE `tb_users_stat_pay` SET `all_views`=`all_views`+'1', `month`=`month`+'1', `".$DAY."`=`".$DAY."`+'1' WHERE `id`='$id_stat_us'") or die($mysqli->error);
	}else{
		$mysqli->query("INSERT INTO `tb_users_stat_pay` (`all_views`,`month`,`username`,`type`, `".$DAY."`) VALUES('1','1','$USERNAME','$TYPE','1')") or die($mysqli->error);
	}
}

function stats_users_reg_pay($USERNAME) {
	global $mysqli;
	$type_arr = array('serf_pay');
	$count_type = count($type_arr);

	$mysqli->query("DELETE FROM `tb_users_stat_pay` WHERE `username`='$USERNAME'") or die($mysqli->error);

	for($i=0; $i<$count_type; $i++) {
		$mysqli->query("INSERT INTO `tb_users_stat_pay` (`username`,`type`) VALUES('$USERNAME','".$type_arr[$i]."')") or die($mysqli->error);
	}
}

### NEW KONKURS ЛУЧШИЙ РЕФЕРЕР ###
function konkurs_clic_ref($REFERER, $money){
	global $mysqli;
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='clic_ref' AND `item`='status'");
	$konk_clic_ref_status = $sql->fetch_object()->price;

	if($konk_clic_ref_status==1) {
		$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='clic_ref' AND `item`='date_start'");
		$konk_clic_ref_date_start = $sql->fetch_object()->price;

		$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='clic_ref' AND `item`='date_end'");
		$konk_clic_ref_date_end = $sql->fetch_object()->price;

		if($konk_clic_ref_date_end>=time() && $konk_clic_ref_date_start<=time()) {
			$mysqli->query("UPDATE `tb_users` SET `konkurs_clic_ref`=`konkurs_clic_ref`+'$money' WHERE `username`='$REFERER' AND `username` NOT IN (SELECT `user_name` FROM `tb_konkurs_exp`) ");
		}
	}
}
### NEW KONKURS ЛУЧШИЙ РЕФЕРЕР ###

### NEW KONKURS ЛУЧШИЙ РЕФЕРАЛ ###
function konkurs_best_ref($USERNAME, $money_ref){
	global $mysqli;
	$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='best_ref' AND `item`='status'");
	$konk_best_ref_status = $sql->fetch_object()->price;

	if($konk_best_ref_status==1) {
		$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='best_ref' AND `item`='date_start'");
		$konk_best_ref_date_start = $sql->fetch_object()->price;

		$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='best_ref' AND `item`='date_end'");
		$konk_best_ref_date_end = $sql->fetch_object()->price;

		if($konk_best_ref_date_end>=time() && $konk_best_ref_date_start<=time()) {
			$mysqli->query("UPDATE `tb_users` SET `konkurs_best_ref`=`konkurs_best_ref`+'$money_ref' WHERE `username`='$USERNAME' AND `username` NOT IN (SELECT `user_name` FROM `tb_konkurs_exp`) ");
		}
	}
}
### NEW KONKURS ЛУЧШИЙ РЕФЕРАЛ ###

function is_url($url) {
	//return true;
	$timeout = 10;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
	//curl_setopt($curl, CURLOPT_INTERFACE, $_SERVER["HTTP_HOST"]);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$content = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if($status==404) {
		return '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
		curl_close($curl);
		return false;
	}elseif($status==200|$status==301|$status==302|$status==503|$status==403) {
		return "true";
	}elseif($status==0) {
		return '<span class="msg-error">Ссылка не существует! Код - '.$status.'</span>';
		curl_close($curl);
		return false;
	}else{
		return '<span class="msg-error">Ссылка не существует! Код - '.$status.'</span>';
		curl_close($curl);
		return false;
	}
	curl_close($curl);
}

function image_valid($type) {
	$file_types  = array(
		'image/pjpeg' => 'jpg',
		'image/jpeg' => 'jpg',
		'image/jpeg' => 'jpeg',
		'image/gif' => 'gif',
		'image/X-PNG' => 'png',
		'image/PNG' => 'png',
		'image/png' => 'png',
		'image/x-png' => 'png',
		'image/JPG' => 'jpg',
		'image/GIF' => 'gif',
		'image/bmp' => 'bmp',
		'image/bmp' => 'BMP'
	);
    
	if(!array_key_exists($type, $file_types)) {
		return "false";
	}else{
		return "true";
	}
}

function is_url_img($url, $cabinet=false) {
	//return true;
	$timeout = 10;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
	//curl_setopt($curl, CURLOPT_INTERFACE, $_SERVER["HTTP_HOST"]);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$content = curl_exec($curl);
	$status1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$status2 = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

	if($cabinet!=false) {
		$span1 = "";
		$span2 = "";
	}else{
		$span1 = '<span class="msg-error">';
		$span2 = '</span>';
	}

	if($status2==false) {
		return $span1.'URL баннера не существует!'.$span2;
		return false;
	}elseif(image_valid($status2) === "false"){
		return $span1.'Не верно указана ссылка на баннер!'.$span2;
		return false;
	}elseif($status1==200) {
		return "true";
	}elseif($status1==0) {
		return $span1.'URL баннера не существует!'.$span2;
		return false;
	}elseif($status1==302) {
		return $span1.'верно указана ссылка на баннер!'.$span2;
		return false;
	}else{
		return $span1.'URL баннера не существует!'.$span2;
		return false;
	}
}

function is_img_size($width, $height, $url, $cabinet=false) {
	$timeout = 10;
	//$headers = array("Range: bytes=0-32768");
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
	//curl_setopt($curl, CURLOPT_INTERFACE, $_SERVER["HTTP_HOST"]);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

	$data = curl_exec($curl);
	$data = imagecreatefromstring($data);
	$w = imagesx($data);
	$h = imagesy($data);
	curl_close($curl);

	if($cabinet!=false) {
		$span1 = "";
		$span2 = "";
	}else{
		$span1 = '<span class="msg-error">';
		$span2 = '</span>';
	}

	if($w==intval($width) && $h==intval($height)) {
		return "true";
	}else{
		if($cabinet!=false) {
			return ''.$span1.'Баннер не соответствует размерам! Размер баннера должен быть '.$width.'x'.$height.''.$span2.'';
			return false;
		}else{
			return '<span class="msg-error">Баннер не соответствует размерам! Размер баннера должен быть '.$width.'x'.$height.'</span>';
			return false;
		}
	}
}

function getHost($url) {
	if( $url!=false && $url!="http://" && $url!="https://" && ((substr($url, 0, 7)=="http://" | substr($url, 0, 8)=="https://")) ) {
		$host = str_replace("www.www.","www.", trim($url));
		$host = parse_url($host);
		$host = isset($host["host"]) ? $host["host"] : array_shift(explode('/', $host["path"], 2));

		if(in_array("www", explode(".", $host))) {
			$just_domain = explode("www.", $host);
			return $just_domain[1];
		}else{
			return $host;
		}
	}
}

function site_work($ost, $min_sec_b = false) {

	$years = floor( ($ost/31536000) );
	$month = floor( ($ost/2628000) );
	$month = floor( ($ost - $years*31536000)/2628000 );
	$days = floor( ($ost - ($years*31536000) - $month*2628000)/86400 );
	$hours = floor( ($ost - ($years*31536000) - ($month*2628000) - ($days * 86400)) / 3600);
	$minutes = floor( ($ost - ($years*31536000) - ($month*2628000) - ($days * 86400) - ($hours * 3600)) / 60 );
	$seconds = floor($ost - ($years*31536000) - ($month*2628000) - ($days * 86400) - ($hours * 3600) - ($minutes * 60));

	if($years>0) {
		if(($years>=10)&&($years<=20)) {
			$y="лет";
		}else{
			switch(substr($years, -1, 1)){
				case 1: $y="год"; break;
				case 2: case 3: case 4: $y="года"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $y="лет"; break;
			}
		}
	}else{
		$years=""; $y="";
	}

	if($month>0) {
		if(($month>=10)&&($month<=20)) {
			$mon="месяцев";
		}else{
			switch(substr($month, -1, 1)){
				case 1: $mon="месяц"; break;
				case 2: case 3: case 4: $mon="месяца"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $mon="месяцев"; break;
			}
		}
	}else{
		$month=""; $mon="";
	}

	if($days>0) {
		if(($days>=10)&&($days<=20)) {
			$d="дней";
		}else{
			switch(substr($days, -1, 1)){
				case 1: $d="день"; break;
				case 2: case 3: case 4: $d="дня"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $d="дней"; break;
			}
		}
	}else{
		$days=""; $d="";
	}


	if($hours>0) {
		if(($hours>=10)&&($hours<=20)) {
			$h="часов";
		}else{
			switch(substr($hours, -1, 1)) {
				case 1: $h="час"; break;
				case 2: case 3: case 4: $h="часа"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $h="часов"; break;
			}
		}
	}else{
		$hours=""; $h="";
	}


	if($minutes>0) {
		if(($minutes>=10)&&($minutes<=20)) {
			$m="минут";
		}else{
			switch(substr($minutes, -1, 1)) {
				case 1: $m="минута"; break;
				case 2: case 3: case 4: $m="минуты"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $m="минут"; break;
			}
		}
	}else{
		$minutes=""; $m="";
	}
	
	if($seconds>0) {
		if(($seconds>=10)&&($seconds<=20)) {
			$s="секунд";
		}else{
			switch(substr($seconds, -1, 1)) {
				case 1: $s="секунда"; break;
				case 2: case 3: case 4: $s="секунды"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $s="секунд"; break;
			}
		}
	}else{
		$seconds=""; $s="";
	}

	if($min_sec_b==false) {
		return "<b>$days</b> $d <b>$hours</b> $h <b>$minutes</b> $m <b>$seconds</b> $s";
	}elseif($min_sec_b==2) {
		return "<b style=\"color:green;\">$years</b> $y <b style=\"color:green;\">$month</b> $mon <b style=\"color:green;\">$days</b> $d";
	}elseif($min_sec_b==3) {
		return "$years $y $month $mon $days $d";
	}else{
		return "$days $d $hours $h";
	}
}


function date_ost($ost, $min_sec_b = false) {
	$days = floor($ost/86400);
	$hours = floor( ($ost - ($days * 86400)) / 3600);
	$minutes = floor( ($ost - ($days * 86400) - ($hours * 3600)) / 60 );
	$seconds = floor($ost - ($days * 86400) - ($hours * 3600) - ($minutes * 60));

	if($days>0) {
		if(($days>=10)&&($days<=20)) {
			$d="дней";
		}else{
			switch(substr($days, -1, 1)){
				case 1: $d="день"; break;
				case 2: case 3: case 4: $d="дня"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $d="дней"; break;
			}
		}
	}else{
		$days=""; $d="";
	}

	if($hours>0) {
		if(($hours>=10)&&($hours<=20)) {
			$h="часов";
		}else{
			switch(substr($hours, -1, 1)) {
				case 1: $h="час"; break;
				case 2: case 3: case 4: $h="часа"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $h="часов"; break;
			}
		}
	}else{
		$hours=""; $h="";
	}

	if($minutes>0) {
		if(($minutes>=10)&&($minutes<=20)) {
			$m="минут";
		}else{
			switch(substr($minutes, -1, 1)) {
				case 1: $m="минута"; break;
				case 2: case 3: case 4: $m="минуты"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $m="минут"; break;
			}
		}
	}else{
		$minutes=""; $m="";
	}
	
	if($seconds>0) {
		if(($seconds>=10)&&($seconds<=20)) {
			$s="секунд";
		}else{
			switch(substr($seconds, -1, 1)) {
				case 1: $s="секунда"; break;
				case 2: case 3: case 4: $s="секунды"; break;
				case 5: case 6: case 7: case 8: case 9: case 0: $s="секунд"; break;
			}
		}
	}else{
		$seconds=""; $s="";
	}

	if($min_sec_b==false) {
		return "<b>$days</b> $d <b>$hours</b> $h <b>$minutes</b> $m <b>$seconds</b> $s";
	}elseif($min_sec_b==1) {
		return "$days $d $hours $h $minutes $m $seconds $s";
	}elseif($min_sec_b==2) {
		//return "<b style=\"color:green;\">$years</b> $y <b style=\"color:green;\">$month</b> $mon <b style=\"color:green;\">$days</b> $d <b style=\"color:green;\">$hours</b> $h <b>$minutes</b> $m <b>$seconds</b> $s";
		  return "<b style=\"color:green;\">$years</b> $y <b style=\"color:green;\">$month</b> $mon <b style=\"color:green;\">$days</b> $d";
	}else{
		return "$days $d $hours $h";
	}
}


function unhtmlspecialchars($str){
	$trans = get_html_translation_table(HTML_SPECIALCHARS);
	$trans[' '] = '&nbsp;';
	$trans = array_flip($trans);
 
	return strtr (str_replace("<br>", "\r\n", $str), $trans);
} 

function p_ceil($val, $d){
	return ceil($val*pow(10,$d))/pow(10,$d);
}

function p_floor($val, $d){
	return floor($val*pow(10,$d))/pow(10,$d);
}

function limitatexto($texto, $limite){
	if(strlen($texto)>$limite) {$texto = substr($texto,0,$limite);}

	return $texto;
}

function ValidaMail($pMail){
	if(preg_match("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$", $pMail))
	{
		return true;
	}else{
		echo "Вы Должны Ввести Правильный Адрес E-mail";
		exit();
	}
}

function ValidaWMID($pMail){
	if(preg_match("[_A-Z0-9-]$", $pMail))
	{
		return true;
	}else{
		echo "Вы Должны Ввести Правильный R кошелек";
		exit();
	}
}


function minimo($contenido){
	if(strlen($contenido) < 3)
	{
		echo "Ваш логин должен быть по крайней мере из 3 символов.";
		include('footer.php');
		exit();
	}else{
		return $contenido;
	}
}


function minimopass($contenido){
	if(strlen($contenido) < 6)
	{
		echo "Ваш пароль должен быть по крайней мере из 6 символов.";
		include('footer.php');
		exit();
	}else{
		return $contenido;
	}
}



function limpiar($mensaje){
	$mensaje = htmlentities(stripslashes(trim($mensaje)));
	$mensaje = str_replace("'"," ",$mensaje);
	$mensaje = str_replace(";"," ",$mensaje);
	$mensaje = str_replace("$"," ",$mensaje);
	return $mensaje;
}


function shout($nombre_usuario){
	if (preg_match("^[a-zA-Z0-9\-_]{3,20}$", $nombre_usuario))
	{
		// echo "El campo $nombre_usuario es correcto<br>";
		return $nombre_usuario;
	}else{
		echo "The Field $nombre_usuario is not valid<br>";
		include('footer.php');
		exit();
	}
}


function uc($mensaje){
	if (preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", $mensaje)) {
		$mensaje = htmlentities(stripslashes((trim($mensaje))));
		$mensaje = str_replace("'"," ",$mensaje);
		$mensaje = str_replace(";"," ",$mensaje);
		$mensaje = str_replace("$"," ",$mensaje);
		return $mensaje;
	}else{
		echo "<br><font color=red><b>Ошибка ввода данных - $mensaje!</b></font><br>";
		exit();
	}
}

function uc_p($mensaje){
	if (preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", $mensaje)) {
		$mensaje = htmlentities(stripslashes(trim($mensaje)));
		$mensaje = str_replace("'"," ",$mensaje);
		$mensaje = str_replace(";"," ",$mensaje);
		$mensaje = str_replace("$"," ",$mensaje);
		return $mensaje;
	}else{
		echo "<br><font color=red><b>Ошибка ввода данных: $mensaje!</b></font><br>";
		exit();
	}
}




function caretos($texto,$ruta){
	$i="<img src=\"$ruta/";
	$i_="\" >";
	$texto=str_replace(":)",$i."icon_smile.gif".$i_,$texto);
	$texto=str_replace(":D",$i."icon_biggrin.gif".$i_,$texto);
	$texto=str_replace("^^",$i."icon_cheesygrin.gif".$i_,$texto);

	$texto=str_replace("xD",$i."icon_lol.gif".$i_,$texto);
	$texto=str_replace("XD",$i."icon_lol.gif".$i_,$texto);

	$texto=str_replace(":|",$i."icon_neutral.gif".$i_,$texto);
	$texto=str_replace(":(",$i."icon_sad.gif".$i_,$texto);
	$texto=str_replace(":&#039(",$i."icon_cry.gif".$i_,$texto);
	$texto=str_replace(":O",$i."icon_surprised.gif".$i_,$texto);
	$texto=str_replace("B)",$i."icon_cool.gif".$i_,$texto);
	$texto=str_replace("8|",$i."icon_rolleyes.gif".$i_,$texto);
	$texto=str_replace("O_O",$i."icon_eek.gif".$i_,$texto);
	$texto=str_replace(":P",$i."icon_razz.gif".$i_,$texto);
	$texto=str_replace(":?",$i."icon_confused.gif".$i_,$texto);
	$texto=str_replace("^:@",$i."icon_evil.gif".$i_,$texto);
	$texto=str_replace("^_-",$i."icon_frown.gif".$i_,$texto);
	$texto=str_replace("!(",$i."icon_mad.gif".$i_,$texto);
	$texto=str_replace("^)",$i."icon_twisted.gif".$i_,$texto);
	$texto=str_replace(";)",$i."icon_wink.gif".$i_,$texto);
	$texto=str_replace(":B",$i."drool.gif".$i_,$texto);
	return $texto;
}

function getRealIP() {
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$client_ip = (!empty($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : ((!empty($_ENV["REMOTE_ADDR"])) ? $_ENV["REMOTE_ADDR"] : "unknown" );
		        $entries = explode('[, ]', $_SERVER["HTTP_X_FORWARDED_FOR"]);
			reset($entries);
			while (list(, $entry) = reset($entries)) {
				$entry = trim($entry);
				if(preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $matches)) {
					$private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/');
					$found_ip = preg_replace($private_ip, $client_ip, $matches[1]);
					if($client_ip != $found_ip) {$client_ip = $found_ip; break;}
				}
			}
		}else{
			$client_ip = (!empty($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : ((!empty($_ENV["REMOTE_ADDR"])) ? $_ENV["REMOTE_ADDR"] : "unknown" );
		}
		return $client_ip;
	}

?>