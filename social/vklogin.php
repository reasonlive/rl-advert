<?php

session_start();
	
function __autoload($name){ include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");}
$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);
$date = date("Y.m.d", time());

//error_reporting(E_ALL); ini_set('display_errors', 'On');

if (isset($_GET['code'])) {
	
	@require_once('social_setup.php');
	//require_once ('soc_functions.php');
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
            'fields'       => 'id,first_name,last_name,screen_name,sex,bdate,phone,photo_big', 
            'access_token' => $token['access_token'],
			'v' => '5.71',
        );

        $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
		$vk_email = $token['email']; // получение email юзера
               
                 
                //print_r($userInfo);
                //print_r($vk_email);
               


              
		//if(!$vk_email){
			//exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка! Вы не ввели почту ВК! Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
		//}

        if (isset($userInfo['response'][0]['id'])) {
            $userInfo = $userInfo['response'][0];
            $result = true;
        }
   

    
	}else {
		exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка доступа к сервису ВК! Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
	}
	
	if ($result) {
		//стандартная авторизация из скрипта

		if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
		//require_once(DOC_ROOT."/config.php");
		//require_once(DOC_ROOT."/funciones.php");
	
		


		//$pass_hac = GetRandPassword(10);
		//$login = '(VK)_'.$userInfo['screen_name'];
		$user_id = $userInfo['id'];
		$login = $userInfo['screen_name'];
		$avatar = isset($userInfo['photo_big']) ? $userInfo['photo_big'] : '';
		$email =  isset($token['email']) ? $token['email'] : '';
                $phone = isset($userInfo['phone']) ? $userInfo['phone'] : "";
		
		$us_city = isset($userInfo['city']['title']) ? $userInfo['city']['title'] : '';
		$us_sex = isset($userInfo['sex']) ? $userInfo['sex'] : '';
		$us_bdate = isset($userInfo['bdate']) ? $userInfo['bdate'] : '';
		$us_photo = isset($userInfo['photo_big']) ? $userInfo['photo_big'] : '';
		
		$us_first_name = isset($userInfo['first_name']) ? $userInfo['first_name'] : '';
		$us_last_name = isset($userInfo['last_name']) ?  $userInfo['last_name'] : '';
		$us_screen_name = isset($userInfo['screen_name']) ?  $userInfo['screen_name'] : '';
		
		
		
		//registration
		if (isset($_SESSION['reg'])){
			if ($_SESSION['reg'] == 'reg') {
				//registration procedure - link account to the social networks
				unset($_SESSION['reg']);

				
				
				$db->query("SELECT * FROM `tb_users` WHERE `vk_id`=$user_id ") or die('db error');
				
				if ($db->NumRows() >= 1) { 
					//user exists - we can't register him 

                                         exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка! Пользователь с таким логином уже существует в системе! Мы не можем Вас зарегистрировать! Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
					
				} else {
				
					//new user - insert information

					
					$pass = $func->GetRandPassword(8);
					$hash = $func->md5Password($pass);
					$ip = $func->UserIP;



					$db->query("INSERT INTO `tb_users` (vk_id, first_name, last_name,
														screen_name, sex, bdate, photo_big, city_title,
														 username, email, password, joindate, user_status, ip) 
														 VALUES
										 				($user_id,'$us_first_name','$us_last_name',
										 				'$us_screen_name',$us_sex,'$us_bdate','$us_photo','$us_city',
										 				'$login', '$email', '$hash', '$date', 0, '$ip')" )
										 				 or die('database error');
					
					
						 
						exit("<div style='position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;'>Регистрация прошла успешно! Ваш пароль:  <span style='color:black;font-weight:bold'>$pass</span>    Вернитесь на <a href='/'>главную</a></div>");
					
					
				}
			}
		}

		
		
		//authorization
		$db->query("SELECT * FROM `tb_users` WHERE `vk_id`='$user_id' ");



		if ($db->NumRows() == 1) {

			$ip = $func->UserIP;
			
			$user_data = $db->FetchAssoc();

			$_SESSION["userID"] = $user_data['id'];
					$_SESSION["userLog"] = $user_data['username'];
					$_SESSION["userPas"] = $user_data['password'];
					
					if($user_data["user_status"]==1) {
						$_SESSION["userLog_a"] = $user_data["username"];
						$_SESSION["userPas_a"] = $user_data["password"];
					}
					

					$db->query("UPDATE `tb_users` SET online=1 , lastiplog='$ip' WHERE `vk_id`='$user_id' ");

					//header("Location: /account");

					echo '<head><meta http-equiv="refresh" content="0;URL=/account" /></head>';

				
			} else {
				exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка!Такого пользователя нет в систем!  Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
			}		
		} else {
				exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка!Такого пользователя нет в систем!  Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
		}
}else{
	sleep(1);
	exit();
}


?>