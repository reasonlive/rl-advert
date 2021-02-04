<?php


if($mysqli->connect_errno) {
	if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
		echo "Нет соединения с базой данных!<br>".str_ireplace(array($config->BaseDB, $config->HostDB, $config->UserDB, "(using password: YES)", "(using password: NO)"), array("DataBase","Host","User","",""), $mysqli->connect_error);
	}else{
		echo '<!DOCTYPE html>';
		echo '<html lang="ru" style="width:100%; height:100%;">';
			echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>'.strtoupper(HTTP_HOST).' | Ошибка соединения!</title></head>';
			echo '<body style="width:100%; background:none; margin:0; padding:0; font: 20px/24px Tahoma, Arial, sans-serif;">';
				echo '<div style="width:50%; margin:100px auto; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color:#EE6363; display:block; padding:15px 10px;">';
					echo "Нет соединения с базой данных!<br>".str_ireplace(array($config->BaseDB, $config->HostDB, $config->UserDB, "(using password: YES)", "(using password: NO)"), array("DataBase","Host","User","",""), $mysqli->connect_error);
				echo '</div>'; 
			echo '</body>';
		echo '</html>';
	}
	exit();
}


if(!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require("funciones.php");

$op = (isset($_GET["op"])) ? $func->limpiar(trim($_GET["op"])) : "site_config";

if(isset($_HTTPS) && $_HTTPS == true) {
	$_SESSION["redirect_url"] = "https://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?op=$op";
}else{
	$_SESSION["redirect_url"] = "http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?op=$op";
}

if(isset($_SESSION["userLog_a"]) && isset($_SESSION["userPas_a"])) {

	$username = (isset($_SESSION["userLog_a"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog_a"]))) ? htmlspecialchars(stripslashes(trim($_SESSION["userLog_a"]))) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_users` WHERE `username`='$username'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$wmid_user = $row["wmid"];

		if(strtolower($username)!=strtolower($row["username"])) {
			echo '<div style="position:fixed; top:0; left:0; height:100%; width:100%; background: url(/style/img/bg-header2.jpg);">';
				echo '<span class="msg-error" style="margin:150px auto; width:70%; padding:20px;">Логин не верный!</span>';
			echo '</div>';
			exit();

		/*}elseif(htmlspecialchars($_SESSION["userPas_a"])!=$row["password"]) {
			echo '<div style="position:fixed; top:0; left:0; height:100%; width:100%; background: url(/style/img/bg-header2.jpg);">';
				echo '<span class="msg-error" style="margin:150px auto; width:70%; padding:20px;">Пароль не верный!</span>';
			echo '</div>';
			exit();*/

		}elseif($row["user_status"]!="1") {
			echo '<div style="position:fixed; top:0; left:0; height:100%; width:100%; background: url(/style/img/bg-header2.jpg);">';
				echo '<span class="msg-error" style="margin:150px auto; width:70%; padding:20px;">Вы не являетесь администратором. Доступ закрыт!</span>';
			echo '</div>';
			exit();
		}
	}else{
		echo '<div style="position:fixed; top:0; left:0; height:100%; width:100%; background: url(/style/img/bg-header2.jpg);">';
			echo '<span class="msg-error" style="margin:150px auto; width:70%; padding:20px;">Error: Access denied!</span>';
		echo '</div>';
		exit();
	}
}else{
	echo '<div style="position:fixed; top:0; left:0; height:100%; width:100%; background: url(/style/img/bg-header2.jpg);">';
		echo '<span class="msg-error" style="margin:150px auto; width:70%; padding:20px;">';
			echo 'Для доступа в админ панель необходимо авторизоваться через WebMoney Login';
			echo '<br><br>';
			echo '<div align="center"><form method="GET" action="https://login.wmtransfer.com/GateKeeper.aspx" id="newform">';
				echo '<input type="hidden" name="RID" value="'.$config->URL_ID_WM_LOGIN.'">';
				echo '<input type="hidden" name="lang" value="ru-RU">';
				echo '<input type="hidden" name="op" value="adminka">';
				echo '<input type="submit" class="sub-blue160" style="float:none;" value="WebMoney Login">';
			echo '</form></div>';
		echo '</span>';
	echo '</div>';

	echo '</body>';
	echo '</html>';

	exit();
}
?>