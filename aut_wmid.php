<?php
session_start();

spl_autoload_register(function($name){
  include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");
});

$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

$date = date("Y.m.d", time());
$time = time();

function _GetAnswer_($xml) {
	$ch2 = curl_init("https://login.wmtransfer.com/ws/authorize.xiface");
	curl_setopt($ch2, CURLOPT_HEADER, 0);
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch2, CURLOPT_POST,1);
	curl_setopt($ch2, CURLOPT_POSTFIELDS, $xml);
	curl_setopt($ch2, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT'] ."/pay/auto_pay_req/cert/WMunited.cer");
	curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($ch2);

	if(curl_errno($ch2)) echo "Curl Error number = ".curl_errno($ch2).", Error desc = ".curl_error($ch2)."<br>";
	curl_close($ch2); 
	return $result; 
}


$sql = $db->query("SELECT `sitewmid` FROM `tb_site` WHERE `id`='1'");
$site_wmid = $db->NumRows()>0 ? $db->FetchRow() : false;

if(!isset($config->URL_ID_WM_LOGIN)) exit("ERROR! URL ID NOT FOUND.");


if(!isset($_POST["WmLogin_WMID"])) {
	echo '<script type="text/javascript">location.replace("/");</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL=/"></noscript>';
	exit();
}else{
	
	$id = (isset($_POST["id"]) && is_string($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(trim($_POST["id"])) : false;
	$token_post = (isset($_POST["token"]) && is_string($_POST["token"]) && preg_match("|^[A-F0-9]{32}$|i", trim($_POST["token"]))) ? strtolower(($_POST["token"])) : false;
	$security_key = "2Lyq6R9tvCj@OT{4c5B#FbQKJ~kX?d3Gx?am$lhIVR62T"; 
	
	$laip = $func->UserIP;
		$agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;
		
		$token_post =strtolower(md5($_SERVER["HTTP_HOST"].strtolower(session_id())."user-auth".$security_key));
		
	$testticket=preg_match('/^[a-zA-Z0-9\$\!\/]{32,48}$/i', $_POST['WmLogin_Ticket']);

	if($_POST['WmLogin_UrlID']==$config->URL_ID_WM_LOGIN && $testticket==1) {
		$xml="<request>
			<siteHolder>".$site_wmid."</siteHolder>
			<user>".htmlspecialchars($_POST["WmLogin_WMID"])."</user>
			<ticket>".htmlspecialchars($_POST["WmLogin_Ticket"])."</ticket>
			<urlId>".$config->URL_ID_WM_LOGIN."</urlId>
			<authType>".htmlspecialchars($_POST["WmLogin_AuthType"])."</authType>
			<userAddress>".htmlspecialchars($_POST["WmLogin_UserAddress"])."</userAddress>
		</request>";

		$resxml = _GetAnswer_($xml);
		$xmlres = simplexml_load_string($resxml);

		if(!isset($xmlres) && trim($xmlres)==false) {
			exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Не получен XML-ответ!</div>');
		}
		$result = strval($xmlres->attributes()->retval);

		if($result==0) {
			exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка! Вернитесь на <a href="/">главную</a> и попробуйте еще раз!</div>');
		}else{
			$wmid = ( isset($_POST["WmLogin_WMID"]) && preg_match("|^[\d]{12}$|", trim($_POST["WmLogin_WMID"])) ) ? htmlspecialchars(trim($_POST["WmLogin_WMID"])) : false;
			
			$op = isset($_GET["op"]) ? $_GET["op"] : false;

			if($op!=false && $op=="login") {


				$db->query("SELECT * FROM `tb_users` WHERE wmid='$wmid' ");
				if($db->NumRows()<1){
					exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка! Вы не зарегистрировали ваш webmoney в системе! Зарегистрируйте ваш webmoney! <br> На <a href="/">главную</a> </div>');
				}else{


					$row = $db->FetchAssoc();


					$_SESSION["userID"] = $row['id'];
					//$_SESSION["WMID"] = $row["wmid"];
					//$_SESSION["IP"] = $laip;
					if($user_data["user_status"]==1) {
						$_SESSION["userLog_a"] = $row["username"];
						$_SESSION["userPas_a"] = $row["password"];
					}else{
						$_SESSION["userLog"] = $row['username'];
						$_SESSION["userPas"] = $row['password'];
					}
					
					
					
					$db->query("UPDATE tb_users SET online=1, lastiplog='$laip', lastlogdate='$date' WHERE wmid='$wmid' ");
                                        sleep(1);
                                        echo '<script type="text/javascript">location.replace("/account");</script>';
					echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL=/account"></noscript>';
                                        exit;

				}

								

			}elseif($op!=false && ($op=="adminka" | $op=="moderator")) {

				$sql = $db->query("SELECT * FROM `tb_users` WHERE `wmid`='$wmid' AND (`user_status`='1' OR `user_status`='2')") or die('db error');
				if($db->NumRows()>0) {
					$row = $db->FetchAssoc();

					$_SESSION["userID"] = $row["id"];
					$_SESSION["userLog_a"] = $row["username"];
					$_SESSION["userPas_a"] = md5($row["password"]);
					$_SESSION["WMID"] = $row["wmid"];
					$_SESSION["IP"] = $laip;

					$agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;
					$redirect_url = (isset($_SESSION["redirect_url"])) ? $_SESSION["redirect_url"] : "/index.php";


					$country_code = $func->CountryCode;

					$db->query("UPDATE `tb_users` SET 
						`country_cod`='$country_code', 
						`lastlogdate`='$date', `lastlogdate2`='$time', 
						`lastiplog`='$laip', `agent`='$agent', online=1 
					WHERE `wmid`='$wmid' AND `user_status`=1 ") or die('db error');

					echo '<script type="text/javascript">location.replace("'.$redirect_url.'");</script>';
					echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$redirect_url.'"></noscript>';
					exit();
				}else{
					exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Доступ закрыт! Вы не являетесь '.($op=="moderator" ? "модератором": "администратором").'!</div>');
				}


			}

		}	
	}
}				

					### РЕФБОНУС ЗА РЕГИСТРАЦИЮ ###
					/*if($statusref==0 && $ref_bonus_get_1==0 && $row["lastlogdate2"]==0) {
						$sql_comis_sys_bon = $db->query("SELECT `price` FROM `tb_config` WHERE `item`='comis_sys_bon' AND `howmany`='1'");
						$comis_sys_bon = $db->NumRows()comis_sys_bon)>0 ? $db->result($sql_comis_sys_bon,0,0) : 0;

						$sql_r_b_stat_1 = $db->query("SELECT * FROM `tb_refbonus_stat` WHERE `status`='-1' AND `username`='$username' AND `type`='1' ORDER BY `id` DESC LIMIT 1");
						if($db->NumRows()r_b_stat_1)>0) {
							$row_r_b_stat_1 = $db->FetchAssoc()r_b_stat_1);

							$sql_referer_1 = $db->query("SELECT `id`,`money_rb` FROM `tb_users` WHERE `username`='$my_referer_1'");
							if($db->NumRows()referer_1)>0) {
								$row_referer_1 = $db->FetchAssoc()referer_1);
								$id_rb_ref_1 = $row_referer_1["id"];
								$money_ref_1 = $row_referer_1["money_rb"];

								$sql_r_b_1 = $db->query("SELECT * FROM `tb_refbonus` WHERE `id`='".$row_r_b_stat_1["ident"]."' AND `status`='1' AND `username`='$my_referer_1' AND `type_bon`='1' ORDER BY `id` DESC LIMIT 1");
								if($db->NumRows()r_b_1)>0) {
									$row_r_b_1 = $db->FetchAssoc()r_b_1);

									$money_ureferera_nado = ($row_r_b_stat_1["money"] * ($comis_sys_bon+100)/100);
									$money_ureferera_nado = round($money_ureferera_nado, 2);

									if($money_ref_1>=$money_ureferera_nado) {
										$db->query("UPDATE `tb_refbonus` SET `count_bon`=`count_bon`+'1' WHERE `id`='".$row_r_b_1["id"]."'") or die($db->error());
										$db->query("UPDATE `tb_refbonus_stat` SET `status`='1', `date`='".time()."' WHERE `id`='".$row_r_b_stat_1["id"]."'") or die($db->error());

										$db->query("UPDATE `tb_users` SET `ref_bonus_get_1`='1', `money`=`money`+'".$row_r_b_stat_1["money"]."' WHERE `username`='$username'") or die($db->error());
										$db->query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_ureferera_nado' WHERE `username`='$my_referer_1'") or die($db->error());

										$db->query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
										VALUES('$username','$user_id','".DATE("d.m.Y H:i")."','".time()."','".$row_r_b_stat_1["money"]."','Реф-Бонус от реферера $my_referer_1 за регистрацию на проекте','Зачислено','rashod')") or die($db->error());

										$db->query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
										VALUES('$my_referer_1','$id_rb_ref_1','".DATE("d.m.Y H:i")."','".time()."','$money_ureferera_nado','Реф-Бонус рефералу $username за регистрацию на проекте','Списано','rashod')") or die($db->error());

										if(trim($row_r_b_1["description"])!=false) {
											$db->query("INSERT INTO `tb_mail_in`(`namein`,`nameout`,`subject`,`message`,`status`,`date`,`ip`) 
											VALUES('$username','Система','Реф-Бонус от реферера $my_referer_1 за регистрацию на проекте','".$row_r_b_1["description"]."','0','".time()."','0.0.0.0')");
										}
									}else{
										$db->query("UPDATE `tb_refbonus_stat` SET `status`='0', `date`='".time()."' WHERE `id`='".$row_r_b_stat_1["id"]."'") or die($db->error());
									}
								}
							}
						}
					}*/
					### РЕФБОНУС ЗА РЕГИСТРАЦИЮ ###

					### РЕФБОНУС ЗА РЕГИСТРАЦИЮ РЕФЕРАЛА ###
					/*if($statusref==0 && $ref_bonus_get_2==0 && $row["lastlogdate2"]==0) {
						if(!isset($comis_sys_bon)) {
							$sql_comis_sys_bon = $db->query("SELECT `price` FROM `tb_config` WHERE `item`='comis_sys_bon' AND `howmany`='1'");
							$comis_sys_bon = $db->NumRows()comis_sys_bon)>0 ? $db->result($sql_comis_sys_bon,0,0) : 0;
						}

						$sql_r_b_stat_2 = $db->query("SELECT * FROM `tb_refbonus_stat` WHERE `status`='-1' AND `username`='$my_referer_1' AND `type`='2' ORDER BY `id` DESC LIMIT 1");
						if($db->NumRows()r_b_stat_2)>0) {
							$row_r_b_stat_2 = $db->FetchAssoc()r_b_stat_2);

							$sql_referer_1 = $db->query("SELECT `id`,`ref_bonus_add` FROM `tb_users` WHERE `ref_bonus_add`='1' AND `username`='$my_referer_1'");
							if($db->NumRows()referer_1)>0) {
								$row_referer_1 = $db->FetchAssoc()referer_1);
								$id_rb_ref_1 = $row_referer_1["ref_bonus_add"];

								$sql_referer_2 = $db->query("SELECT `id`,`money_rb` FROM `tb_users` WHERE `username`='$my_referer_2'");
								if($db->NumRows()referer_2)>0) {
									$row_referer_2 = $db->FetchAssoc()referer_2);
									$id_rb_ref_2 = $row_referer_2["id"];
									$money_ref_2 = $row_referer_2["money_rb"];

									$sql_r_b_2 = $db->query("SELECT * FROM `tb_refbonus` WHERE `id`='".$row_r_b_stat_2["ident"]."' AND `status`='1' AND `username`='$my_referer_2' AND `type_bon`='2' ORDER BY `id` DESC LIMIT 1");
									if($db->NumRows()r_b_2)>0) {
										$row_r_b_2 = $db->FetchAssoc()r_b_2);

										$money_ureferera_nado = ($row_r_b_stat_2["money"] * ($comis_sys_bon+100)/100);
										$money_ureferera_nado = round($money_ureferera_nado, 2);

										if($money_ref_2>=$money_ureferera_nado) {
											$db->query("UPDATE `tb_refbonus` SET `count_bon`=`count_bon`+'1' WHERE `id`='".$row_r_b_2["id"]."'") or die($db->error());
											$db->query("UPDATE `tb_refbonus_stat` SET `status`='1', `date`='".time()."' WHERE `id`='".$row_r_b_stat_2["id"]."'") or die($db->error());

											$db->query("UPDATE `tb_users` SET `ref_bonus_get_2`='1' WHERE `username`='$username'") or die($db->error());
											$db->query("UPDATE `tb_users` SET `money`=`money`+'".$row_r_b_stat_2["money"]."' WHERE `username`='$my_referer_1'") or die($db->error());
											$db->query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_ureferera_nado' WHERE `username`='$my_referer_2'") or die($db->error());

											$db->query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
											VALUES('$my_referer_1','$id_rb_ref_1','".DATE("d.m.Y H:i")."','".time()."','".$row_r_b_stat_2["money"]."','Реф-Бонус от реферера $my_referer_2 за привлечение реферала (ID:$user_id)','Зачислено','rashod')") or die($db->error());

											$db->query("INSERT INTO `tb_history` (`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) 
											VALUES('$my_referer_2','$id_rb_ref_2','".DATE("d.m.Y H:i")."','".time()."','$money_ureferera_nado','Реф-Бонус рефералу $my_referer_1 за привлечение реферала (ID:$user_id)','Списано','rashod')") or die($db->error());

											if(trim($row_r_b_2["description"])!=false) {
												$db->query("INSERT INTO `tb_mail_in`(`namein`,`nameout`,`subject`,`message`,`status`,`date`,`ip`) 
												VALUES('$my_referer_1','Система','Реф-Бонус от реферера $my_referer_2 за привлечение реферала (ID:$user_id)','".$row_r_b_2["description"]."','0','".time()."','0.0.0.0')");
											}
										}else{
											$db->query("UPDATE `tb_refbonus_stat` SET `status`='0', `date`='".time()."' WHERE `id`='".$row_r_b_stat_2["id"]."'") or die($db->error());
										}
									}
								}
							}
						}
					}*/
					### РЕФБОНУС ЗА РЕГИСТРАЦИЮ РЕФЕРАЛА ###

					/*echo '<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #2E8B57; display:block; padding:10px 20px;">Добро пожаловать на проект!<br>Сейчас Вы будете перемещены в Ваш аккаунт!</div>';

					echo ' <script type="text/javascript"> setTimeout(\'location.replace("/members.php")\', 250); </script> ';
					echo ' <noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL=/members.php"></noscript>';
				}else{
					exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">WMID#'.$wmid.' не зарегистрирован на проекте, либо Вы не указали его в своем профиле! Попробуйте войти в аккаунт, используя логин и пароль!</div>');
				}
				
			}
			
			}else{
				echo '<script type="text/javascript">location.replace("/");</script>';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL=/"></noscript>';
				exit();
			}
		}
	}else{
		exit('<div style="position: fixed; left: 20%; top: 50%; right: 20%; font-size:18px; color:#FFF; text-align:center; text-shadow:1px 1px 1px #000; background-color: #EE6363; display:block; padding:10px 20px;">Ошибка при получении тикета!</div>');
	}
}*/


?>