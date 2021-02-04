<?php
 session_start();

//Регистрация пользователя, вход, восстановление пароля, выход
spl_autoload_register(function($name){
  include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");
});




require_once($_SERVER['DOCUMENT_ROOT'] ."/recaptcha/config_recaptcha.php");




$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

$date = date('Y.m.d', time());
$ip = $func->UserIP;
$country_code = $func->CountryCode;


function register_user($name, $mail, $pass){

	$charset = 'utf-8'; // Кодировка письма
$to = $mail; // Получатель
$subject = "Регистрация на проекте ".$_SERVER["HTTP_HOST"]; // Тема письма
$text = $name .", Вас приветсвует платформа  ".$_SERVER["HTTP_HOST"]." \r\n"; 
$text .= "Ваш пароль для входа на сервис:". $pass; // Контент письма
$from = $_SERVER['SERVER_ADMIN']; // Отправитель
$fromName = $_SERVER['HTTP_HOST']; // Имя отправителя
// Вот что такое заголовки
$headers = "MIME-Version: 1.0\n";
$headers .= "From: =?$charset?B?".base64_encode($fromName)."?= <$from>\n";
$headers .= "Content-type: text/html; charset=$charset\n";
$headers .= "Content-Transfer-Encoding: base64\n";

$res = mail("=?$charset?B?".base64_encode($to)."?= <$to>", "=?$charset?B?".base64_encode($subject)."?=", chunk_split(base64_encode($text)), $headers, "-f$from");
return $res;
}

function recover_pass($time, $mail, $pass, $ip){

	$text = "Здравствуйте!\r\n
Вы запросили данные для авторизации на проекте ".$_SERVER["HTTP_HOST"]."\r\n
Запрос был осуществлен ".$time." с IP адреса: ".$ip."\r\n
Ваш пароль для входа на сервис:". $pass;

$charset = 'utf-8'; // Кодировка письма
$to = $mail; // Получатель
$subject = "Восстановление пароля на проекте ".$_SERVER["HTTP_HOST"]; // Тема письма

$from = $_SERVER['SERVER_ADMIN']; // Отправитель
$fromName = $_SERVER['HTTP_HOST']; // Имя отправителя
// Вот что такое заголовки
$headers = "MIME-Version: 1.0\n";
$headers .= "From: =?$charset?B?".base64_encode($fromName)."?= <$from>\n";
$headers .= "Content-type: text/html; charset=$charset\n";
$headers .= "Content-Transfer-Encoding: base64\n";

$res = mail("=?$charset?B?".base64_encode($to)."?= <$to>", "=?$charset?B?".base64_encode($subject)."?=", chunk_split(base64_encode($text)), $headers, "-f$from");
return $res;

	
}







///////////////REGISTRATION
if(isset($_POST['username']) and isset($_POST['email'])){

if(!$func->IsMail($_POST['email'])){
	echo 'fail';
	exit;
}

       /*if(isset($_POST['captcha'])){

         if(isset($_POST['g-recaptcha-response'])){
		$recaptcha = new recaptcha($secret, $_POST['g-recaptcha-response'], $func->UserIP);
		$resp = $recaptcha->verify();
		if($resp === 'missing_input_response'){
			echo "captcha_error";
			exit;
		}elseif(!$resp){
                          echo "captcha_error";
                          exit;                
                       }
          		
         }else{
                  echo "captcha_error";
                  exit;
         }

         }*/
	

	$name = (string)$_POST['username'];
	$mail = (string)$_POST['email'];

	


	$db->query("SELECT * FROM tb_users WHERE username='$name' OR email='$mail' ");
	if($db->NumRows() > 0){
		echo 'repeat_error';
		exit();
	}

	$pass = $func->GetRandPassword(8);
	$hash = $func->md5Password($pass);



	$db->query("INSERT INTO `tb_users` (username, email, password, joindate, user_status, ip, country_cod, lastiplog, lastlogdate) VALUES
										 ('$name', '$mail', '$hash', '$date', 0, '$ip', '$country_code', '$ip', '$date' )" ) or die('database error');


	//require_once("mail.php");
	$res = register_user($name,$mail,$pass);

	if($res){
		//$answer = "registered";
		echo 'registered';
		exit();
	}
	else{
		echo 'mail_error';
		exit();
	}

	
	
}else{
	//echo 'false';
}


//////////LOGIN
if(isset($_POST['email']) and isset($_POST['pass'])){

if(!$func->IsMail($_POST['email'])){
	echo "fail";
	exit;
}


          if(isset($_POST['captcha'])){

          	if(!preg_match('/^captcha$/', $_POST['captcha'])){
          		echo 'captcha_error';
          		exit;
          	}

         if(isset($_POST['g-recaptcha-response'])){
		$recaptcha = new recaptcha($secret, $_POST['g-recaptcha-response'], $func->UserIP);
		$resp = $recaptcha->verify();
		if($resp === 'missing_input_response'){
			echo "captcha_error";
			exit;
		}elseif(!$resp){
                          echo "captcha_error";
                          exit;                
                       }
          		
         }else{
                  echo "captcha_error";
                  exit;
         }

         }



	$mail = $_POST['email'];
	$pass = $_POST['pass'];




	$db->query("SELECT * FROM tb_users WHERE email = '$mail' AND online=0 ");

	if($db->NumRows() < 1){
		echo 'empty_or_online_error';
		exit();
	}else{
		$user_data = $db->FetchAssoc();

		$hash = $func->md5Password($pass);
		$user_hash = $user_data['password'];
		if($user_hash == $hash){
			$db->query("SELECT * FROM tb_users WHERE password = '$hash' AND email = '$mail' ");
			if($db->NumRows() > 0){

//УСТАНОВКА СЕССИИ И КУКИ ДЛЯ ЮЗЕРА
				
				//session_set_cookie_params(60, "/", $_SERVER['HTTP_HOST'], 0,1);
			   
			    //setcookie(session_name(),session_id(),time()+60);
				$user_data = $db->FetchAssoc();

					$_SESSION["userID"] = $user_data['id'];
					//$_SESSION["WMID"] = $user_data["wmid"];
					
					if($user_data["user_status"]==1) {
						$_SESSION["userLog_a"] = $user_data["username"];
						$_SESSION["userPas_a"] = $user_data["password"];
					}else{
						//setcookie("userLog", $user_data["id"], (time()+60*60*24*5), '/', $_SERVER['HTTP_HOST'], 0,1);
						$_SESSION["userLog"] = $user_data['username'];
						$_SESSION["userPas"] = $user_data['password'];
					}
					
					//setcookie("_id", $user_data["id"], (time()+60*60*24*5), '/');
					//setcookie("_pid", md5($user_data["id"]), (time()+60*60*24*5), '/');

					sleep(1);
				$db->query("UPDATE tb_users SET online=1, lastiplog='$ip', lastlogdate='$date' WHERE email='$mail' ");
				echo 'loggedin';
				exit();
			}else{
				echo 'login_error';
				exit();
			}
			
		}else{
			echo 'password_error';
			exit();
		}

	}

}


///////////RECOVERY PASSWORD
if(isset($_POST['recovery']) and isset($_POST['email'])){
	//send mail to the email

	if(!preg_match('/^recovery$/',$_POST['recovery']){
	echo 'fail';
	exit;
	}

	$mail = (string)$_POST['email'];

	$pass = $func->GetRandPassword(8);
	$hash = $func->md5Password($pass);

	$res = recover_pass($date, $mail, $pass, $ip);

	$db->query("UPDATE tb_users SET password = '$hash' WHERE email='$mail' ");

	if($res){
		echo 'recovery_success';
	}else{
		echo 'recovery_fail';
	}
}

/////////////LOGOUT 
if(isset($_POST['logout']) and isset($_POST['id'])){

if(!preg_match('/^logout$/',$_POST['logout']){
	echo 'fail';
	exit;
}

$id = (int)$_POST['id'];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


@session_destroy();
$db->query("UPDATE `tb_users` SET online=0 WHERE id=$id ");

echo 'logout';


}






?>