<?php
session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
if(!DEFINED("ADVERTISE")) DEFINE("CABINET", true);
sleep(0);

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	require(ROOT_DIR."/config.php");
	require(ROOT_DIR."/funciones.php");
	require(ROOT_DIR."/cabinet/cab_func.php");
	require(ROOT_DIR."/merchant/func_mysql.php");
	require_once(ROOT_DIR."/method_pay/method_pay_sys.php");

	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(limpiarez($_POST["id"])))) ? intval(limpiarez($_POST["id"])) : false;
	$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", limpiarez($_POST["op"])) ) ? limpiarez($_POST["op"]) : false;
	$type_ads = ( isset($_POST["type"]) && preg_match("|^[a-zA-Z0-9\-_]{1,20}$|", limpiarez($_POST["type"])) ) ? limpiarez($_POST["type"]) : false;
	$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
	$cab_skidka = 0; $cab_text = false;
	$id_user = false; $wmid_user = false; $wmr_user = false; $money_user_rb = false; $money_user_rek = false;
	$laip = getRealIP();

	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
		$sql_user = $mysqli->query("SELECT `id`,`username`,`wmid`,`wm_purse`,`money_rb`,`money_rek`,`referer` FROM `tb_users` WHERE `username`='$username'");
		if($sql_user->num_rows>0) {
			$row_user = $sql_user->fetch_array();
			$id_user = $row_user["id"];
			$username = $row_user["username"];
			$wmid_user = $row_user["wmid"];
			$wmr_user = $row_user["wm_purse"];
			$money_user_rb = $row_user["money_rb"];
			$money_user_rek = $row_user["money_rek"];
			$my_referer_1 = $row_user["referer"];

			$sql_cab = $mysqli->query("SELECT * FROM `tb_cabinet` WHERE `id`='1'");
			if($sql_cab->num_rows>0) {
				$row_cab = $sql_cab->fetch_assoc();
				if($row_cab["status"]==1 && $money_user_rek>=$row_cab["start_sum"]) {
					$cab_skidka = $row_cab["start_proc"] + (floor(($money_user_rek - $row_cab["start_sum"])/$row_cab["shag_sum"]) * $row_cab["shag_proc"]);
					if($cab_skidka>$row_cab["max_proc"]) $cab_skidka = $row_cab["max_proc"];
					$cab_text = '<tr><td align="left">Ваша скидка рекламодателя:</td><td align="left">'.$cab_skidka.' %</td></tr>';
				}
			}
		}else{
			$username = false;
		}
	}else{
		$username = false;
	}

	if($type_ads!=false && $type_ads=="tests") {
		if(!DEFINED("TESTS_AJAX")) DEFINE("TESTS_AJAX", true);
		include("ajax_adv/ajax_adv_tests.php");
		exit();
	}else{
		exit("Ошибка! Не удалось обработать запрос!");
	}

}else{
	exit("Ошибка! Не удалось обработать запрос!");
}

?>