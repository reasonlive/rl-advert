<?php
session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=windows-1251");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
sleep(0);

$json_result = array();
$json_result["result"] = "";
$json_result["message"] = "";
$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	require(ROOT_DIR."/config.php");
	require_once(ROOT_DIR."/funciones.php");
	require_once(ROOT_DIR."/recaptcha/config_recaptcha.php");
	require_once(ROOT_DIR."/recaptcha/lib/autoload.php");
	$laip = getRealIP();

	function json_encode_cp1251($json_arr) {
		$json_arr = json_encode($json_arr);
		$arr_replace_cyr = array("\u0410", "\u0430", "\u0411", "\u0431", "\u0412", "\u0432", "\u0413", "\u0433", "\u0414", "\u0434", "\u0415", "\u0435", "\u0401", "\u0451", "\u0416", "\u0436", "\u0417", "\u0437", "\u0418", "\u0438", "\u0419", "\u0439", "\u041a", "\u043a", "\u041b", "\u043b", "\u041c", "\u043c", "\u041d", "\u043d", "\u041e", "\u043e", "\u041f", "\u043f", "\u0420", "\u0440", "\u0421", "\u0441", "\u0422", "\u0442", "\u0423", "\u0443", "\u0424", "\u0444", "\u0425", "\u0445", "\u0426", "\u0446", "\u0427", "\u0447", "\u0428", "\u0448", "\u0429", "\u0449", "\u042a", "\u044a", "\u042b", "\u044b", "\u042c", "\u044c", "\u042d", "\u044d", "\u042e", "\u044e", "\u042f", "\u044f");
		$arr_replace_utf = array("А", "а", "Б", "б", "В", "в", "Г", "г", "Д", "д", "Е", "е", "Ё", "ё", "Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н","О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь","ь","Э","э","Ю","ю","Я","я");
		$json_arr = str_replace($arr_replace_cyr, $arr_replace_utf, $json_arr);
		return $json_arr;
	}

	function myErrorHandler($errno, $errstr, $errfile, $errline, $js_result) {
		$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";
		$message_text = false;
		$errfile = str_replace($_SERVER["DOCUMENT_ROOT"], "", $errfile);

		switch ($errno) {
			case(1): $message_text = "Fatal error[$errno]: $errstr in line $errline in $errfile"; break;
			case(2): $message_text = "Warning[$errno]: $errstr in line $errline in $errfile"; break;
			case(8): $message_text = "Notice[$errno]: $errstr in line $errline in $errfile"; break;
			default: $message_text = "[$errno] $errstr in line $errline in $errfile"; break;
		}
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}
	$set_error_handler = set_error_handler('myErrorHandler', E_ALL);

	if(!isset($siteKey) | !isset($secret) | ( isset($siteKey) && isset($secret) && ($siteKey == false | $secret == false) ) ) {
		$message_text = "ERROR: Нет ключей!";
		$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
		exit($js_result);
	}else{
		if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
			$message_text = "Вы уже авторизовались!";
			$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
			exit($js_result);
		}else{
			$recaptcha = new \ReCaptcha\ReCaptcha($secret);
			$response = $recaptcha->verify(strip_tags(htmlspecialchars(trim($_POST["recaptcha"]))), $laip);

			if($response->isSuccess()) {
				$username = (isset($_POST["log_user"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", trim($_POST["log_user"]))) ? htmlentities(stripslashes(trim($_POST["log_user"]))) : false;
				$password = (isset($_POST["pas_user"]) && preg_match("|^[a-zA-Z0-9\-_-]{6,20}$|", trim($_POST["pas_user"]))) ? htmlentities(stripslashes(trim($_POST["pas_user"]))) : false;
				$enter_pas_oper = (isset($_POST["pas_oper"]) && preg_match("|^[0-9a-zA-Z]{7,9}$|", trim($_POST["pas_oper"]))) ? htmlentities(stripslashes(trim($_POST["pas_oper"]))) : false;

				if($username==false) {
					$message_text = "Логин должен быть от 3 до 20 символов, и содержать только латинские символы";
					$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
					exit($js_result);
				}elseif($password==false) {
					$message_text = "Пароль должен быть от 3 до 20 символов, и содержать только латинские символы";
					$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
					exit($js_result);
				}else{

					$sql = mysql_query("SELECT * FROM `tb_users` WHERE `username`='".mysql_real_escape_string($username)."' AND `password`='".mysql_real_escape_string($password)."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
					if(mysql_num_rows($sql)>0) {
						$row = mysql_fetch_assoc($sql);

						$user_id = $row["id"];
						$username = $row["username"];
						$my_referer_1 = $row["referer"];
						$my_referer_2 = $row["referer2"];
						$my_referer_3 = $row["referer3"];
						$ref_bonus_get_1 = $row["ref_bonus_get_1"];
						$ref_bonus_get_2 = $row["ref_bonus_get_2"];
						$statusref = $row["statusref"];
						$my_ban_date = $row["ban_date"];
						$my_joindate = $row["joindate2"];
						$my_lastlogdate = $row["lastlogdate2"];
						$my_status_ban = $row["ban_date"];
						$agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;

						if($my_status_ban > 0) {
							$message_text = "Ваш аккаунт заблокирован за нарушение правил проекта!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif(strtolower($username) != strtolower($row["username"])) {
							$message_text = "Логин введен не верно!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif(strtolower($password) != strtolower($row["password"])) {
							$message_text = "Пароль введен не верно!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif($row["block_wmid"]==1) {
							$message_text = "Необходимо авторизоваться через WebMomey Login";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif( $row["block_agent"]==1 && $row["lastiplog"]!=$laip && $enter_pas_oper==false ) {
							$message_text = "У Вас изменился IP-адрес, необходимо ввести пароль для операций!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "NeedPO", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif( $row["block_agent"]==1 && strtolower($row["agent"])!=strtolower($_SERVER["HTTP_USER_AGENT"]) && $enter_pas_oper==false ) {
							$message_text = "У Вас изменился браузер, необходимо ввести пароль для операций!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "NeedPO", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif( $row["block_agent"]==1 && ( $row["lastiplog"]!=$laip | strtolower($row["agent"])!=strtolower($_SERVER["HTTP_USER_AGENT"])) && $enter_pas_oper==false ) {
							$message_text = "Необходимо ввести пароль для операций!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "NeedPO", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}elseif( $row["block_agent"]==1 && ( $row["lastiplog"]!=$laip | strtolower($row["agent"])!=strtolower($_SERVER["HTTP_USER_AGENT"])) && strtolower($row["pass_oper"])!=strtolower($enter_pas_oper) ) {
							$message_text = "Пароль для операций введен не верно!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "NeedPO", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}else{
							$_SESSION["userID"] = $user_id;
							$_SESSION["userLog"] = $username;
							$_SESSION["userPas"] = md5($row["password"]);
							$_SESSION["WMID"] = $row["wmid"];
							$_SESSION["IP"] = $laip;
							if($row["user_status"]==1 | $row["user_status"]==2) {
								$_SESSION["userLog_a"] = $row["username"];
								$_SESSION["userPas_a"] = md5($row["password"]);
							}
							SETCOOKIE("_user", $row["username"], (time()+7776000), "/");
							SETCOOKIE("_pid", md5($row["id"]), (time()+7776000), "/");

							$sql_r1 = mysql_query("SELECT `id` FROM `tb_users` WHERE `referer`='$username'");
							$referals1 = mysql_num_rows($sql_r1);

							$sql_r2 = mysql_query("SELECT `id` FROM `tb_users` WHERE `referer2`='$username'");
							$referals2 = mysql_num_rows($sql_r2);

							$sql_r3 = mysql_query("SELECT `id` FROM `tb_users` WHERE `referer3`='$username'");
							$referals3 = mysql_num_rows($sql_r3);

							if( $row["lastlogdate2"] != 0 && $row["lastlogdate2"] < (time()-7*24*60*60) ) {
								$reit_add = mysql_result(mysql_query("SELECT `price` FROM `tb_config` WHERE `item`='reit_noactive' AND `howmany`='1'"),0,0);;
							}else{
								$reit_add = 0;
							}

							if( isset($row["wmid"]) && preg_match("|^[\d]{12}$|", trim($row["wmid"])) ) {
								include_once(ROOT_DIR."/auto_pay_req/wmxml.inc.php");
								$_RES_WM_11 = _WMXML11($row["wmid"]);

								if(isset($_RES_WM_11["wmids"]) && count($_RES_WM_11["wmids"])>0) {
									$_ALL_WMID_TAB = false;
									for($y=0; $y<count($_RES_WM_11["wmids"]); $y++) {
										$_ALL_WMID_TAB.= $_RES_WM_11["wmids"][$y]." ";
									}
									$_ALL_WMID_TAB = str_replace(" ","; ", trim($_ALL_WMID_TAB));
									$_ATTESTAT = isset($_RES_WM_11["att"]) ? $_RES_WM_11["att"] : $row["attestat"];
								}else{
									$_ALL_WMID_TAB = $row["wmid_all"];
									$_ATTESTAT = $row["attestat"];
								}
							}else{
								$_ALL_WMID_TAB = $row["wmid_all"];
								$_ATTESTAT = $row["attestat"];
							}

							include(ROOT_DIR."/geoip/geoipcity.inc");
							$gi = geoip_open(ROOT_DIR."/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
							$record = @geoip_record_by_addr($gi, $laip);
							@geoip_close($gi);
							$country_code = ( isset($record->country_code) && $record->country_code != false ) ? $record->country_code : false;
							$country_name = function_exists('get_country')!==false ? get_country($country_code) : false;

							$log_ip_aut = (isset($row["log_ip_aut"]) && trim($row["log_ip_aut"])!=false) ? explode(", ", $row["log_ip_aut"]) : array();
							if(end($log_ip_aut)!=$laip && $laip!=false) $log_ip_aut[] = $laip;
							$log_ip_aut = array_slice($log_ip_aut, -10);
							$log_ip_aut = implode(", ", $log_ip_aut);

							mysql_query("UPDATE `tb_users` SET 
								`attestat`='$_ATTESTAT', `wmid_all`='$_ALL_WMID_TAB', 
								`reiting`=`reiting`+'$reit_add', `country`='$country_name', `country_cod`='$country_code', 
								`referals`='$referals1', `referals2`='$referals2', `referals3`='$referals3', 
								`lastlogdate`='".DATE("d.m.Y")."', `lastlogdate2`='".time()."', 
								`lastiplog`='$laip', `log_ip_aut`='$log_ip_aut', 
								`kol_log`=`kol_log`+1, `agent`='$agent' 
							WHERE `username`='$username'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

							### АДМИН-КОНКУРС КОМПЛЕКСНЫЙ NEW ###
							if($my_referer_1!=false && $statusref==0 && $my_lastlogdate==0 && $my_ban_date==0) {
								$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='status'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
								$konk_complex_status = mysql_result($sql,0,0);

								if($konk_complex_status==1) {
									$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='date_start'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
									$konk_complex_date_start = mysql_result($sql,0,0);

									$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='date_end'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
									$konk_complex_date_end = mysql_result($sql,0,0);

									$sql = mysql_query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='complex' AND `item`='point_refs'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
									$konk_complex_point = mysql_result($sql,0,0);

									if($my_joindate>=$konk_complex_date_start && $konk_complex_date_end>=time() && $konk_complex_date_start<=time()) {
										mysql_query("UPDATE `tb_users` SET `konkurs_complex`=`konkurs_complex`+'$konk_complex_point' WHERE `username`='$my_referer_1' AND `username` NOT IN (SELECT `user_name` FROM `tb_konkurs_exp`)") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
									}
								}
							}
							### АДМИН-КОНКУРС КОМПЛЕКСНЫЙ NEW ###

							############ БАН МУЛЬТОВ ############
							$_USER_T_ID = strtolower(md5($row["id"]));
							$_COOKIE_ID = (isset($_COOKIE["_pid"]) && preg_match("|^[0-9a-fA-F]{32}$|", htmlspecialchars(trim($_COOKIE["_pid"])))) ? htmlspecialchars(strtolower(trim($_COOKIE["_pid"]))) : false;

							if($_COOKIE_ID != false) {
								$sql_ban = mysql_query("SELECT `username` FROM `tb_users` WHERE md5(`id`)='$_COOKIE_ID'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
								if(mysql_num_rows($sql_ban)>0) {
									$_COOKIE_NAME = mysql_result($sql_ban,0,0);

									if($_USER_T_ID != $_COOKIE_ID) {
										$sql_ch1 = mysql_query("SELECT `id` FROM `tb_black_users` WHERE `name`='$_COOKIE_NAME'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
										if(mysql_num_rows($sql_ch1)==0) {
											mysql_query("INSERT INTO `tb_black_users` (`name`,`why`,`ip`,`date`,`time`) 
											VALUES ('$_COOKIE_NAME','Мультиаккаунт ($_COOKIE_NAME, $username)','$laip','".DATE("d.m.Y H:i")."', '".time()."')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

											mysql_query("UPDATE `tb_users` SET `ban_date`='".time()."' WHERE `username`='$_COOKIE_NAME' AND `ban_date`='0'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
										}

										$sql_ch2 = mysql_query("SELECT `id` FROM `tb_black_users` WHERE `name`='$username'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
										if(mysql_num_rows($sql_ch2)==0) {
											mysql_query("INSERT INTO `tb_black_users` (`name`,`why`,`ip`,`date`,`time`) 
											VALUES ('$username','Мультиаккаунт ($_COOKIE_NAME, $username)','$laip','".DATE("d.m.Y H:i")."', '".time()."')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

											mysql_query("UPDATE `tb_users` SET `ban_date`='".time()."' WHERE `username`='$username' AND `ban_date`='0'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
										}
									}
								}
							}
							############ БАН МУЛЬТОВ ############

							### РЕФБОНУС ЗА РЕГИСТРАЦИЮ ###
							if($statusref==0 && $ref_bonus_get_1==0 && $row["lastlogdate2"]==0) {
								$sql_comis_sys_bon = mysql_query("SELECT `price` FROM `tb_config` WHERE `item`='comis_sys_bon' AND `howmany`='1'");
								$comis_sys_bon = mysql_num_rows($sql_comis_sys_bon)>0 ? mysql_result($sql_comis_sys_bon,0,0) : 0;

								$sql_r_b_stat_1 = mysql_query("SELECT * FROM `tb_refbonus_stat` WHERE `status`='-1' AND `username`='$username' AND `type`='1' ORDER BY `id` DESC LIMIT 1");
								if(mysql_num_rows($sql_r_b_stat_1)>0) {
									$row_r_b_stat_1 = mysql_fetch_assoc($sql_r_b_stat_1);

									$sql_referer_1 = mysql_query("SELECT `id`,`money_rb` FROM `tb_users` WHERE `username`='$my_referer_1'");
									if(mysql_num_rows($sql_referer_1)>0) {
										$row_referer_1 = mysql_fetch_assoc($sql_referer_1);
										$id_rb_ref_1 = $row_referer_1["id"];
										$money_ref_1 = $row_referer_1["money_rb"];

										$sql_r_b_1 = mysql_query("SELECT * FROM `tb_refbonus` WHERE `id`='".$row_r_b_stat_1["ident"]."' AND `status`='1' AND `username`='$my_referer_1' AND `type_bon`='1' ORDER BY `id` DESC LIMIT 1");
										if(mysql_num_rows($sql_r_b_1)>0) {
											$row_r_b_1 = mysql_fetch_assoc($sql_r_b_1);

											$money_ureferera_nado = ($row_r_b_stat_1["money"] * ($comis_sys_bon+100)/100);
											$money_ureferera_nado = round($money_ureferera_nado, 2);

											if($money_ref_1>=$money_ureferera_nado) {
												mysql_query("UPDATE `tb_refbonus` SET `count_bon`=`count_bon`+'1' WHERE `id`='".$row_r_b_1["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
												mysql_query("UPDATE `tb_refbonus_stat` SET `status`='1', `date`='".time()."' WHERE `id`='".$row_r_b_stat_1["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

												mysql_query("UPDATE `tb_users` SET `ref_bonus_get_1`='1', `money`=`money`+'".$row_r_b_stat_1["money"]."' WHERE `username`='$username'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
												mysql_query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_ureferera_nado' WHERE `username`='$my_referer_1'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

												mysql_query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
												VALUES('$username','$user_id','".DATE("d.m.Y H:i")."','".time()."','".$row_r_b_stat_1["money"]."','Реф-Бонус от реферера $my_referer_1 за регистрацию на проекте','Зачислено','rashod')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

												mysql_query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
												VALUES('$my_referer_1','$id_rb_ref_1','".DATE("d.m.Y H:i")."','".time()."','$money_ureferera_nado','Реф-Бонус рефералу $username за регистрацию на проекте','Списано','rashod')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

												if(trim($row_r_b_1["description"])!=false) {
													mysql_query("INSERT INTO `tb_mail_in`(`namein`,`nameout`,`subject`,`message`,`status`,`date`,`ip`) 
													VALUES('$username','Система','Реф-Бонус от реферера $my_referer_1 за регистрацию на проекте','".$row_r_b_1["description"]."','0','".time()."','0.0.0.0')");
												}
											}else{
												mysql_query("UPDATE `tb_refbonus_stat` SET `status`='0', `date`='".time()."' WHERE `id`='".$row_r_b_stat_1["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
											}
										}
									}
								}
							}
							### РЕФБОНУС ЗА РЕГИСТРАЦИЮ ###

							### РЕФБОНУС ЗА РЕГИСТРАЦИЮ РЕФЕРАЛА ###
							if($statusref==0 && $ref_bonus_get_2==0 && $row["lastlogdate2"]==0) {
								if(!isset($comis_sys_bon)) {
									$sql_comis_sys_bon = mysql_query("SELECT `price` FROM `tb_config` WHERE `item`='comis_sys_bon' AND `howmany`='1'");
									$comis_sys_bon = mysql_num_rows($sql_comis_sys_bon)>0 ? mysql_result($sql_comis_sys_bon,0,0) : 0;
								}

								$sql_r_b_stat_2 = mysql_query("SELECT * FROM `tb_refbonus_stat` WHERE `status`='-1' AND `username`='$my_referer_1' AND `type`='2' ORDER BY `id` DESC LIMIT 1");
								if(mysql_num_rows($sql_r_b_stat_2)>0) {
									$row_r_b_stat_2 = mysql_fetch_assoc($sql_r_b_stat_2);

									$sql_referer_1 = mysql_query("SELECT `id`,`ref_bonus_add` FROM `tb_users` WHERE `ref_bonus_add`='1' AND `username`='$my_referer_1'");
									if(mysql_num_rows($sql_referer_1)>0) {
										$row_referer_1 = mysql_fetch_assoc($sql_referer_1);
										$id_rb_ref_1 = $row_referer_1["ref_bonus_add"];

										$sql_referer_2 = mysql_query("SELECT `id`,`money_rb` FROM `tb_users` WHERE `username`='$my_referer_2'");
										if(mysql_num_rows($sql_referer_2)>0) {
											$row_referer_2 = mysql_fetch_assoc($sql_referer_2);
											$id_rb_ref_2 = $row_referer_2["id"];
											$money_ref_2 = $row_referer_2["money_rb"];

											$sql_r_b_2 = mysql_query("SELECT * FROM `tb_refbonus` WHERE `id`='".$row_r_b_stat_2["ident"]."' AND `status`='1' AND `username`='$my_referer_2' AND `type_bon`='2' ORDER BY `id` DESC LIMIT 1");
											if(mysql_num_rows($sql_r_b_2)>0) {
												$row_r_b_2 = mysql_fetch_assoc($sql_r_b_2);

												$money_ureferera_nado = ($row_r_b_stat_2["money"] * ($comis_sys_bon+100)/100);
												$money_ureferera_nado = round($money_ureferera_nado, 2);

												if($money_ref_2>=$money_ureferera_nado) {
													mysql_query("UPDATE `tb_refbonus` SET `count_bon`=`count_bon`+'1' WHERE `id`='".$row_r_b_2["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
													mysql_query("UPDATE `tb_refbonus_stat` SET `status`='1', `date`='".time()."' WHERE `id`='".$row_r_b_stat_2["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

													mysql_query("UPDATE `tb_users` SET `ref_bonus_get_2`='1' WHERE `username`='$username'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
													mysql_query("UPDATE `tb_users` SET `money`=`money`+'".$row_r_b_stat_2["money"]."' WHERE `username`='$my_referer_1'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
													mysql_query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_ureferera_nado' WHERE `username`='$my_referer_2'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

													mysql_query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
													VALUES('$my_referer_1','$id_rb_ref_1','".DATE("d.m.Y H:i")."','".time()."','".$row_r_b_stat_2["money"]."','Реф-Бонус от реферера $my_referer_2 за привлечение реферала (ID:$user_id)','Зачислено','rashod')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

													mysql_query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
													VALUES('$my_referer_2','$id_rb_ref_2','".DATE("d.m.Y H:i")."','".time()."','$money_ureferera_nado','Реф-Бонус рефералу $my_referer_1 за привлечение реферала (ID:$user_id)','Списано','rashod')") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());

													if(trim($row_r_b_2["description"])!=false) {
														mysql_query("INSERT INTO `tb_mail_in`(`namein`,`nameout`,`subject`,`message`,`status`,`date`,`ip`) 
														VALUES('$my_referer_1','Система','Реф-Бонус от реферера $my_referer_2 за привлечение реферала (ID:$user_id)','".$row_r_b_2["description"]."','0','".time()."','0.0.0.0')");
													}
												}else{
													mysql_query("UPDATE `tb_refbonus_stat` SET `status`='0', `date`='".time()."' WHERE `id`='".$row_r_b_stat_2["id"]."'") or die(($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", mysql_error()))) : mysql_error());
												}
											}
										}
									}
								}
							}
							### РЕФБОНУС ЗА РЕГИСТРАЦИЮ РЕФЕРАЛА ###

							//$message_text = "USER: ".$row["username"]."<br>PASS: ".$row["password"]."<br>COUNTRY: ".$country_code."<br>AGENT: ".$_SERVER["HTTP_USER_AGENT"];
							$message_text = "Авторизация прошла успешно!";
							$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "OK", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
							exit($js_result);
						}
					}else{
						$message_text = "Логин или пароль введен не верно!";
						$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
						exit($js_result);
					}
				}
			}else{
				$_ERR_CODE = false;
				foreach($response->getErrorCodes() as $code) {
					$_ERR_CODE.= $code;
				}

				$message_text = ( isset($_ERR_CODE) && htmlspecialchars(trim($_ERR_CODE))!=false ) ? "Необходимо подтвердить, что Вы не робот!" : "Обновите страницу!";
				$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
				exit($js_result);
			}
		}
	}
}else{
	$message_text = "ERROR: Не корректный запрос!";
	$js_result = ($ajax_json=="json") ? json_encode_cp1251(array("result" => "ERROR", "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
	exit($js_result);
}

?>