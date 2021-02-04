<?php
/*
created by ivancoff 14/04/2019
skype: ivancofer
e-mail: ivancoff@rambler.ru
description:unlink account from the social networks
*/

	session_start();
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		sleep(3);
		exit();
	}	


	if (isset($_GET['social'])) {
		@require_once('social_setup.php');
		require_once ('soc_functions.php');
	
		if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
		require_once(DOC_ROOT."/config.php");

		require_once(DOC_ROOT."/funciones.php");
		$username = uc($_SESSION["userLog"]);
		$sql_user = mysql_query("SELECT * FROM `tb_users` WHERE `username`='$username'");
		$row_user = mysql_fetch_array($sql_user);
		$user_id = $row_user["id"];
		//$username = $row_user["username"];
		//$password = $row_user["password"];
		
		if ($_GET['social'] == 'vk') {
			$sql_vk = mysql_query("DELETE FROM `tb_users_vk` WHERE `user_id` = '$user_id'") or die(mysql_error());
			send_and_exit('msg-ok','Данные сохранены!','profile.php');
		}
		
		if ($_GET['social'] == 'mr') {
			$sql_vk = mysql_query("DELETE FROM `tb_users_mr` WHERE `user_id` = '$user_id'") or die(mysql_error());
			send_and_exit('msg-ok','Данные сохранены!','profile.php');
		}
		
		if ($_GET['social'] == 'ok') {
			$sql_vk = mysql_query("DELETE FROM `tb_users_ok` WHERE `user_id` = '$user_id'") or die(mysql_error());
			send_and_exit('msg-ok','Данные сохранены!','profile.php');
		}
		
		if ($_GET['social'] == 'gm') {
			$sql = mysql_query("DELETE FROM `tb_users_social` WHERE `user_id` = '$user_id' AND `social_type`='".$SocialTypeGMail."'") or die(mysql_error());
			send_and_exit('msg-ok','Данные сохранены!','profile.php');
		}	
	}
?>