<?PHP
class func{
public $UserIP = "Undefined"; # IP пользователя
public $UserCode = "Undefined"; # Код от IP
public $TableID = -1; # ID таблицы
public $UserAgent = "Undefined";
public $CountryCode = '';
private $key = "donny666";

public function __construct(){
	$this->UserIP = $this->GetUserIp();
	//$this->UserCode = $this->IpCode();
	$this->UserAgent = $this->UserAgent();
	$this->CountryCode = $this->GetCountryCode($this->UserIP);
}
public function __destruct(){
}


public function IpToInt($ip){ 
	$ip = ip2long($ip); 
	($ip < 0) ? $ip+=4294967296 : true; 
	return $ip; 
}

public function IntToIP($int){ 
	return long2ip($int);  
}

public function GetUserIp(){
	if(getenv('REMOTE_ADDR'))
{$user_ip = getenv('REMOTE_ADDR');}
elseif(getenv('HTTP_FORWARDED_FOR'))
{$user_ip = getenv('HTTP_FORWARDED_FOR');}
elseif(getenv('HTTP_X_FORWARDED_FOR'))
{$user_ip = getenv('HTTP_X_FORWARDED_FOR');}
elseif(getenv('HTTP_X_COMING_FROM'))
{$user_ip = getenv('HTTP_X_COMING_FROM');}
elseif(getenv('HTTP_VIA'))
{$user_ip = getenv('HTTP_VIA');}
elseif(getenv('HTTP_XROXY_CONNECTION'))
{$user_ip = getenv('HTTP_XROXY_CONNECTION');}
elseif(getenv('HTTP_CLIENT_IP'))
{$user_ip = getenv('HTTP_CLIENT_IP');}
else{$user_ip='unknown';}

if(15 < strlen($user_ip))
{
   $ar = split(', ', $user_ip);
   for($i=sizeof($ar)-1; $i > 0; $i--)
   {
      if($ar[$i]!='' and !preg_match('/[a-zA-Zà-ÿÀ-ß]/', $ar[$i]))
      {
        $user_ip = $ar[$i];
        break;
      }
      if($i==sizeof($ar)-1)
      {
    	 $user_ip = 'unknown';
      }
   }
}
if(preg_match('/[a-zA-Zà-ÿÀ-ß]/', $user_ip))
{$user_ip = 'unknown';}

return htmlspecialchars(stripslashes($user_ip));
}

public function IsLogin($login){
	return (is_array($login)) ? false : (preg_match("/^[a-zA-Z0-9]{4,10}$/", $login)) ? $login : false;
}

public function IsPassword($password){
	return (is_array($password)) ? false : (preg_match("/^[a-zA-Z0-9]{4,20}$/", $password)) ? $password : false;
}

public function IsWM($data, $type = 0){
	$FirstChar = array( 1 => "R",
		2 => "Z",
		3 => "E",
		4 => "U");
	if(strlen($data) < 12 && strlen($data) > 12 && $type < 0 && $type > count($FirstChar)) return false;
	if($type == 0) return (is_array($data)) ? false : ( ereg("^[0-9]{12}$", $data) ? $data : false );
	if( substr(strtoupper($data),0,1) != $FirstChar[$type] or !ereg("^[0-9]{12}", substr($data,1)) ) return false;
	return $data;
}

public function IsMail($mail){
	if(is_array($mail) && empty($mail) && strlen($mail) > 255 && strpos($mail,'@') > 64) return false;
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $mail)) ? false : strtolower($mail);
}

/*public function IpCode(){
	$arr_mask = explode(".",$this->GetUserIp());
	return $arr_mask[0].".".$arr_mask[1].".".$arr_mask[2].".0";
}*/

public function GetTime($tis = 0, $unix = true, $template = "d.m.Y H:i:s"){
	if($tis == 0){
		return ($unix) ? time() : date($template,time());
	}else return date($template,$unix);
}

public function UserAgent(){
	return $this->TextClean($_SERVER['HTTP_USER_AGENT']);
}


public function TextClean($text){
	$array_find = array("`", "<", ">", "^", '"', "~", "\\");
	$array_replace = array("&#96;", "&lt;", "&gt;", "&circ;", "&quot;", "&tilde;", "");
	return str_replace($array_find, $array_replace, $text);
}

public function GetBrowser(){
	$agent = $this->UserAgent();
	if(preg_match('/OPR|Opera/', $agent)){
		return 'Opera';
	}elseif(preg_match('/Firefox/', $agent)){
		return 'Firefox';
	}elseif(preg_match('/NET|ASU/', $agent)){
		return 'IE';
	}elseif(preg_match('/Edge/', $agent)){
		return 'Edge';
	}elseif(preg_match('/Safari/', $agent) and !preg_match('/Chrome/', $agent)){
		return 'Safari';
	}else{
		return 'Chrome';
	}
	return 'Unknown';

}

public function ShowError($errorArray = array(), $title = "Исправьте следующие ошибки"){
	if(count($errorArray) > 0){
		$string_a = "<div class='Error'><div class='ErrorTitle'>".$title."</div><ul>";
		foreach($errorArray as $number => $value){
			$string_a .= "<li>".($number+1)." - ".$value."</li>";
		}
		$string_a .= "</ul></div><BR />";
		return $string_a;
	}else return "Неизвестная ошибка :(";
}

public function ComissionWm($sum, $com_payee, $com_payysys){
	$a = ceil(ceil($sum * $com_payee * 100) / 10000*100) / 100;
	$b = ceil(ceil($sum * str_replace("%","",$com_payysys) * 100) / 10000*100) / 100;
	return $a+$b;
}

public function md5Password($pass){
	$pass = strtolower($pass);
	return md5($this->key."-".$pass);
}


public function ControlCode($time = 0){
	return ($time > 0) ? date("Ymd", $time) : date("Ymd");
}

public function SumCalc($per_h, $sum_tree, $last_sbor){
	if($last_sbor > 0){
		if($sum_tree > 0 AND $per_h > 0){
			$last_sbor = ($last_sbor < time()) ? (time() - $last_sbor) : 0;
			$per_sec = $per_h / 3600;
			return round( ($per_sec * $sum_tree) * $last_sbor);
		}else return 0;
	}else return 0;
}

public function SellItems($all_items, $for_one_coin){
	if($all_items <= 0 OR $for_one_coin <= 0) return 0;
	return sprintf("%.2f", ($all_items / $for_one_coin));
}


// FUNCTIONS FROM THE OLD PROJECT SERFNETS.RU 
////////////////////////////////////////////////


public function GetDomen($url) {
	$host = str_replace("www.www.","www.", trim($url));
	$host = @parse_url($host);
	$host = trim($host['host'] ? $host['host'] : array_shift(explode('/', $host['path'], 2)));

	if(in_array("www", explode(".", $host))) {
		$just_domain = explode("www.", $host);
		return $just_domain[1];
	}else{
		return $host;
	}
}

public function SFB_YANDEX($url, $typeCheck=false) {
	require_once("phar://".$_SERVER["DOCUMENT_ROOT"]."/api/yandex/yandex-php-library_0.4.1.phar.bz2/vendor/autoload.php");
	$safeBrowsing = new Yandex\SafeBrowsing\SafeBrowsingClient("dd2069b2-8c2f-42b5-9e73-e64a1e5a35c9");

	if($typeCheck == "searchUrl") {
		$sfb_y = $safeBrowsing->searchUrl($url);
	}elseif($typeCheck == "lookup") {
		$sfb_y = $safeBrowsing->lookup($url);
	}elseif($typeCheck == "checkAdult") {
		$sfb_y = $safeBrowsing->checkAdult($url);
	}else{
		$sfb_y = $safeBrowsing->searchUrl($url);
	}
/*
	if($sfb_y != false) {
		return "По данным Яндекса сайт ".@getHost($url)." находится в списке опасных сайтов!";
	}else{
		return false;
	}*/
}


public function GetCountryCode($ip){
		
		require_once($_SERVER['DOCUMENT_ROOT'] . "/geoip/geoipcity.inc");
		$gi = @geoip_open( $_SERVER['DOCUMENT_ROOT']. "/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
		$record = @geoip_record_by_addr($gi, $ip);
		@geoip_close($gi);
		$country_code = ( isset($record->country_code) && $record->country_code != false ) ? $record->country_code : false;

		return $country_code;


}


public function GetCountry($code) {
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

//stats_users
//stats_users_reg
//stats_copilka
//stats_users_reg_copilka


public function is_url($url) {
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


public function image_valid($type) {
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



public function is_url_img($url, $cabinet=false) {
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



public function is_img_size($width, $height, $url, $cabinet=false) {
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


public function getHost($url) {
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


public function site_work($ost, $min_sec_b = false) {

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


public function date_ost($ost, $min_sec_b = false) {
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


public function unhtmlspecialchars($str){
	$trans = get_html_translation_table(HTML_SPECIALCHARS);
	$trans[' '] = '&nbsp;';
	$trans = array_flip($trans);
 
	return strtr (str_replace("<br>", "\r\n", $str), $trans);
} 

public function p_ceil($val, $d){
	return ceil($val*pow(10,$d))/pow(10,$d);
}

public function p_floor($val, $d){
	return floor($val*pow(10,$d))/pow(10,$d);
}

public function limitatexto($texto, $limite){
	if(strlen($texto)>$limite) {$texto = substr($texto,0,$limite);}

	return $texto;
}

public function ValidaMail($pMail){
	if(preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/", $pMail))
	{
		return true;
	}else{
		echo "Вы Должны Ввести Правильный Адрес E-mail";
		exit();
	}
}

public function ValidaWMID($pMail){
	if(ereg("[_A-Z0-9-]$", $pMail))
	{
		return true;
	}else{
		echo "Вы Должны Ввести Правильный R кошелек";
		exit();
	}
}





public function limpiar($mensaje){
	$mensaje = htmlentities(stripslashes(trim($mensaje)));
	$mensaje = str_replace("'"," ",$mensaje);
	$mensaje = str_replace(";"," ",$mensaje);
	$mensaje = str_replace("$"," ",$mensaje);
	return $mensaje;
}


public function shout($nombre_usuario){
	if (ereg("^[a-zA-Z0-9\-_]{3,20}$", $nombre_usuario))
	{
		// echo "El campo $nombre_usuario es correcto<br>";
		return $nombre_usuario;
	}else{
		echo "The Field $nombre_usuario is not valid<br>";
		include('footer.php');
		exit();
	}
}


public function uc($mensaje){
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

public function uc_p($mensaje){
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




public function caretos($texto,$ruta){
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




public function GetRandPassword($fLength) {
	// Символы, которые будут использоваться в пароле.
	$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
	// Количество символов в пароле.
	$max = $fLength;
	// Определяем количество символов в $chars
	$size=strlen($chars)-1;
	// Определяем пустую переменную, в которую и будем записывать символы.
	$password=null;
	// Создаём пароль.
	while($max--)
		$password.=$chars[rand(0,$size)];
	// Выводим созданный пароль.
	return $password;
}















}
?>