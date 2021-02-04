<?php
if(!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

function img_get_save($url, $get_file_name = false) {
	$timeout = 10;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
	//curl_setopt($curl, CURLOPT_INTERFACE, $_SERVER["HTTP_HOST"]);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$content = curl_exec($curl);

	$type = trim(curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
	$host_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
	$file_size = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
	$file_name = basename($host_url);
	$host_name = parse_url(trim($host_url));
	$host_name = str_replace("www.","", $host_name["host"]);
	$dir_name = $host_name;
	curl_close($curl);

	$files_type  = array(
		'image/PJPEG' 	=> 'jpeg', 	'image/pjpeg' 	=> 'jpeg', 
		'image/JPEG' 	=> 'jpeg', 	'image/jpeg' 	=> 'jpeg', 
		'image/JPG' 	=> 'jpg', 	'image/jpg' 	=> 'jpg',
		'image/GIF' 	=> 'gif', 	'image/gif' 	=> 'gif', 
		'image/X-PNG' 	=> 'png', 	'image/x-png' 	=> 'png', 
		'image/PNG'	=> 'png', 	'image/png' 	=> 'png', 
		'image/BMP' 	=> 'bmp', 	'image/bmp' 	=> 'bmp'
	);

	if($type == false) {
		return '<span class="msg-error">Не удалось загрузить баннер '.$host_url.'!</span>';
		return false;
	}elseif(!array_key_exists($type, $files_type)) {
		return '<span class="msg-error">Не поддерживаемый формат баннера!</span>';
		return false;
	}else{
		$type = $files_type[$type];
	}

	$file_name = md5($host_url).".$type";
	$upload_dir = $_SERVER["DOCUMENT_ROOT"]."/adv_banners/";
	$upload_file = $upload_dir.basename($file_name);

	if($get_file_name!=false) {
		return "/adv_banners/$file_name";
		return false;
	}

	if(!file_exists($upload_dir)) {
		if (!mkdir($upload_dir, 0777)) {
			return '<span class="msg-error">Внутрення ошибка сервера!</span>';
			return false;
		}
	}elseif($file_size>1048576) {
		return '<span class="msg-error">Размер баннера('.$file_size.') не должен превышать 1 Mb!</span>';
		return false;
	}else{
		file_put_contents($upload_file, $content);
		return "true";
	}

	return false;
}

function img_get_save_cab($url, $get_file_name = false) {
	$timeout = 10;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
	//curl_setopt($curl, CURLOPT_INTERFACE, $_SERVER["HTTP_HOST"]);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$content = curl_exec($curl);

	$type = trim(curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
	$host_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
	$file_size = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
	$file_name = basename($host_url);
	$host_name = parse_url(trim($host_url));
	$host_name = str_replace("www.","", $host_name["host"]);
	$dir_name = $host_name;
	curl_close($curl);

	$files_type  = array(
		'image/PJPEG' 	=> 'jpeg', 	'image/pjpeg' 	=> 'jpeg', 
		'image/JPEG' 	=> 'jpeg', 	'image/jpeg' 	=> 'jpeg', 
		'image/JPG' 	=> 'jpg', 	'image/jpg' 	=> 'jpg',
		'image/GIF' 	=> 'gif', 	'image/gif' 	=> 'gif', 
		'image/X-PNG' 	=> 'png', 	'image/x-png' 	=> 'png', 
		'image/PNG'	=> 'png', 	'image/png' 	=> 'png', 
		'image/BMP' 	=> 'bmp', 	'image/bmp' 	=> 'bmp'
	);

	if($type == false) {
		return "Не удалось загрузить баннер $host_url";
		return false;
	}elseif(!array_key_exists($type, $files_type)) {
		return "Не поддерживаемый формат баннера!";
		return false;
	}else{
		$type = $files_type[$type];
	}

	$file_name = md5($host_url).".$type";
	$upload_dir = $_SERVER["DOCUMENT_ROOT"]."/adv_banners/";
	$upload_file = $upload_dir.basename($file_name);

	if($get_file_name!=false) {
		return "/adv_banners/$file_name";
		return false;
	}

	if(!file_exists($upload_dir)) {
		if (!mkdir($upload_dir, 0777)) {
			return "Внутрення ошибка сервера";
			return false;
		}
	}elseif($file_size>1048576) {
		return "Размер баннера($file_size) не должен превышать 1 Mb!";
		return false;
	}else{
		file_put_contents($upload_file, $content);
		return "true";
	}

	return false;
}
?>