<?php
	
/*
created by ivancoff 23/01/2020
skype: ivancofer
e-mail: ivancoff@rambler.ru
*/

//error_reporting(E_ALL); ini_set('display_errors', 'On');

if (isset($_GET['code'])) {
	
	@require_once('social_setup.php');
	require_once ('soc_functions.php');
	$result = false;

	$params = array(
		'client_id' => $vk_client_id,
		'client_secret' => $vk_client_secret,
		'code' => $_GET['code'],
		'redirect_uri' => $vk_redirect_uri,
	);
	
    $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);

    if (isset($token['access_token'])) {
	
        $params = array(
            'uids'         => $token['user_id'],
            'fields'       => 'id,first_name,last_name,screen_name,sex,bdate,photo_big', //,city
            'access_token' => $token['access_token'],
			'v' => '5.71',
        );

        $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
		$vk_email = $token['email']; // получение email юзера

        if (isset($userInfo['response'][0]['id'])) {
            $userInfo = $userInfo['response'][0];
			//print_r($userInfo);
			foreach($userInfo as &$value) {
				//$value = iconv("UTF-8", "CP1251", $value);
			}
			
            $result = true;
        }
    }
	else {
		send_and_exit('msg-error','Ошибка доступа к сервису VK!','');
	}
	
	if ($result) {
		//стандартная авторизация из скрипта

		if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
		require_once(DOC_ROOT."/config.php");
		require_once(DOC_ROOT."/funciones.php");
	
		session_start();

		if (isset($_SESSION['reg'])){
			if ($_SESSION['reg'] == 'reg') {
				//registration procedure - link account to the social networks
				unset($_SESSION['reg']);

				// if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
				// require DOC_ROOT."/system/db.php";
				// include DOC_ROOT."/system/config/config.php";
				// include DOC_ROOT."/system/config/view.php";
				// include DOC_ROOT."/system/config/def.php";
				// include DOC_ROOT."/system/func.php";
				
				//$vk_rows = $db->selects("tb_users_vk","vk_id = ?",[1=>$userInfo['id']],"user_id");
				$vk_rows = mysql_query("SELECT `user_id` FROM `tb_users_vk` WHERE `vk_id`='".mysql_real_escape_string($userInfo['id'])."'") or die(mysql_error());
				
				//if (!empty($vk_rows['user_id'])){
				if (mysql_num_rows($vk_rows) >= 1) { 
					//user exists - we can't register him
					send_and_exit('msg-error','Пользователь с таким логином уже существует в системе! Мы не можем Вас зарегистрировать','');
				} else {
				
					//new user - insert information
					
					//$ip_us = getRealIP();
					//$date = date('d.m.Y');

					//$pass_hac = GetRandPassword(10);
					//$login = '(VK)_'.$userInfo['screen_name'];
					
					$login = $userInfo['screen_name'];
					$avatar = isset($userInfo['photo_big']) ? $userInfo['photo_big'] : '';
					$email =  isset($token['email']) ? $token['email'] : '';
					
					$us_city = isset($userInfo['city']['title']) ? $userInfo['city']['title'] : '';
					$us_sex = isset($userInfo['sex']) ? $userInfo['sex'] : '';
					$us_bdate = isset($userInfo['bdate']) ? $userInfo['bdate'] : '';
					$us_photo = isset($userInfo['photo_big']) ? $userInfo['photo_big'] : '';
					
					$us_first_name = isset($userInfo['first_name']) ? iconv("UTF-8", "CP1251", $userInfo['first_name']) : '';
					$us_last_name = isset($userInfo['last_name']) ? iconv("UTF-8", "CP1251", $userInfo['last_name']) : '';
					$us_screen_name = isset($userInfo['screen_name']) ? iconv("UTF-8", "CP1251", $userInfo['screen_name']) : '';
					
					//if (isset($token['email'])) {
					//	$email = $token['email'];
					//}

					if(!DEFINED("SOCIAL_REGISTER")) DEFINE("SOCIAL_REGISTER", true);
					include(DOC_ROOT.'/ajax/u_register.php');
					
					//$reffor = (int)$_COOKIE['ref'];
					//$urL_P = clear($_SESSION['url_p']);
					// Занос данных в систему
					//if ($reffor > 0) {
					//	$db->update("users","all_ref = all_ref + 1","id = ?",[1=>$reffor]);
					//}
					// $db->inserts("users","`login`, `email`, `pass`, `ref`, `date_reg`, `permission`, `ip`, `urL_P`,`date_auth`,`avatar`","?,?,?,?,?,?,?,?,?,?",[1=>$login, 2=>$email, 3=>$pass_hac, 4=>$reffor, 5=>$date, 6=>1,7=>$ip_us, 8=>$urL_P,9=>time(),10=>$avatar]);	
					
					//find this new user id
					//$us_rows = $db->selects("users","login = ?",[1=>$login],"id");
					$us_rows_id = isset($id_user) ? $id_user : 0;
					
					if ($us_rows_id > 0) {
						//$db->inserts("tb_users_vk","`user_id`, `vk_id`, `first_name`, `last_name`, `screen_name`, `sex`, `bdate`, `photo_big`, `city_title`","?,?,?,?,?,?,?,?,?",[1=>$us_rows['id'], 2=>$userInfo['id'], 3=>$userInfo['first_name'], 4=>$userInfo['last_name'], 5=>$userInfo['screen_name'], 6=>$userInfo['sex'], 7=>$userInfo['bdate'],8=>$userInfo['photo_big'], 9=>$userInfo['city']['title']]);
						
						mysql_query("INSERT INTO `tb_users_vk` (`user_id`, `vk_id`, `first_name`, `last_name`, `screen_name`, `sex`, `bdate`, `photo_big`, `city_title`) VALUES ('$us_rows_id','".$userInfo['id']."','".$us_first_name."','".$us_last_name."','".$us_screen_name."','$us_sex','$us_bdate','$us_photo','$us_city')") or die(mysql_error());
						 
						send_and_exit('msg-ok','Регистрация прошла успешно!. Посетите свой аккаунт в течении 72-х часов иначе он будет удалён из системы.','login3');
					} else {
						send_and_exit('msg-error','Ошибка обработки данных 1','');
					}
					send_and_exit('msg-error','Ошибка обработки данных 2','');
				}
			}
		}

		$sql_vk = mysql_query("SELECT `user_id` FROM `tb_users_vk` WHERE `vk_id`='".mysql_real_escape_string($userInfo['id'])."'") or die(mysql_error());

		if (mysql_num_rows($sql_vk) == 1) {
			$row_db = mysql_fetch_assoc($sql_vk);
			$user_id = $row_db['user_id'];
			
			$sql = mysql_query("SELECT * FROM `tb_users` WHERE `id`='".$user_id."'") or die(mysql_error());
			if (mysql_num_rows($sql) > 0) {
				$row = mysql_fetch_assoc($sql);
				
				if(!DEFINED("VK_LOGIN")) DEFINE("VK_LOGIN", true);
				include(DOC_ROOT.'/ajax/u_login.php');
				
			} else {
				send_and_exit('msg-error','Такого пользователя нет в системе!','login3');
			}		
		} else {
				send_and_exit('msg-error','Такого пользователя нет в системе!','login3');
		}
	}	
} else {
	sleep(3);
}
?>