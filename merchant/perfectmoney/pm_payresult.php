<?php
header("Content-type: text/html; charset=windows-1251");
require($_SERVER["DOCUMENT_ROOT"]."/config.php");
require($_SERVER["DOCUMENT_ROOT"]."/merchant/func_mysql.php");
@require($_SERVER["DOCUMENT_ROOT"]."/curs/curs.php");
require($_SERVER["DOCUMENT_ROOT"]."/merchant/perfectmoney/pm_config.php");
define('PATH_TO_LOG', dirname(__FILE__).'/');

$PAYMENT_ID = (isset($_REQUEST["PAYMENT_ID"])) ? htmlspecialchars(trim($_REQUEST["PAYMENT_ID"])) : false;
$PAYEE_ACCOUNT = (isset($_REQUEST["PAYEE_ACCOUNT"])) ? htmlspecialchars(trim($_REQUEST["PAYEE_ACCOUNT"])) : false;
$merch_amount_usd = (isset($_REQUEST["PAYMENT_AMOUNT"])) ? htmlspecialchars(trim($_REQUEST["PAYMENT_AMOUNT"])) : false;
$PAYMENT_UNITS = (isset($_REQUEST["PAYMENT_UNITS"])) ? htmlspecialchars(trim($_REQUEST["PAYMENT_UNITS"])) : false;
$PAYMENT_BATCH_NUM = (isset($_REQUEST["PAYMENT_BATCH_NUM"])) ? htmlspecialchars(trim($_REQUEST["PAYMENT_BATCH_NUM"])) : false;
$PAYER_ACCOUNT = (isset($_REQUEST["PAYER_ACCOUNT"])) ? htmlspecialchars(trim($_REQUEST["PAYER_ACCOUNT"])) : false;
$TIMESTAMPGMT = (isset($_REQUEST["TIMESTAMPGMT"])) ? htmlspecialchars(trim($_REQUEST["TIMESTAMPGMT"])) : false;
$merch_tran_id = (isset($_REQUEST["ORDER_NUM"])) ? intval(htmlspecialchars(trim($_REQUEST["ORDER_NUM"]))) : false;
$V2_HASH = (isset($_REQUEST["V2_HASH"])) ? strtoupper(htmlspecialchars(trim($_REQUEST["V2_HASH"]))) : false;
$STRING = $PAYMENT_ID.':'.$PAYEE_ACCOUNT.':'.$merch_amount_usd.':'.$PAYMENT_UNITS.':'.$PAYMENT_BATCH_NUM.':'.$PAYER_ACCOUNT.':'.ALTERNATE_PHRASE_HASH.':'.$TIMESTAMPGMT;
$HASH = strtoupper(md5($STRING));
$shp_item = ( isset($_REQUEST["CUST_NUM"]) && preg_match("|^[\d]{1,11}$|", htmlspecialchars(trim($_REQUEST["CUST_NUM"]))) ) ? intval(htmlspecialchars(trim($_REQUEST["CUST_NUM"]))) : false;

$post=false;
if(count($_POST)>0) {
	foreach($_POST as $key => $val) $post.="$key - $val\n";
	$mysqli->query("INSERT INTO `tb_test` (`text`) VALUES('$post\n$HASH\n$V2_HASH\nSHP-$shp_item\nID-$merch_tran_id\n\n')");
}

$TABLE_ARR = array(
	 1  =>	"tb_add_pay", 
	 2  =>	"tb_ads_dlink", 
	 3  =>	"tb_ads_psevdo", 
	 4  =>	"tb_ads_bs", 
	 5  =>	"tb_ads_auto", 
	 6  =>	"tb_ads_slink", 
	 7  =>	"tb_ads_kontext", 
	 8  =>	"tb_ads_banner", 
	 9  =>	"tb_ads_txt", 
	10  =>	"tb_ads_frm", 
	11  =>	"tb_ads_mails", 
	12  =>	"tb_ads_rc", 
	13  =>	"tb_ads_downloads", 
	14  =>	"tb_ads_questions", 
	15  =>	"tb_ads_emails", 
	16  =>	"tb_ads_packet",
	17  =>	"tb_ads_kat",
					18  =>	"tb_ads_link",
	20  =>	"tb_ads_beg_stroka",
	21  =>	"tb_ads_tests",
	22  =>	"tb_ads_catalog",
	23  =>	"tb_ads_youtube", 
	24  =>	"tb_ads_autoyoutube",
	30  =>	"tb_ads_articles",
	31  =>	"tb_ads_pay_row",
	40  =>	"tb_invest_money_in"
);

$STAT_PAY_ARR = array(
	 1  =>	"money_in", 
	 2  =>	"dlink", 
	 3  =>	"psevdo", 
	 4  =>	"bserf", 
	 5  =>	"autoserf", 
	 6  =>	"statlink", 
	 7  =>	"kontext", 
	 8  =>	"banners", 
	 9  =>	"txtob", 
	10  =>	"frmlink", 
	11  =>	"mails", 
	12  =>	"rekcep", 
	13  =>	"files", 
	14  =>	"quest", 
	15  =>	"sent_emails", 
	16  =>	"packet",
	17  =>	"statkat",
					18  =>	"link",
	20  =>	"bstroka",
	21  =>	"tests",
	22  =>	"catalog",
	23  =>	"youtube", 
	24 =>	"autoserfyou",
	30  =>  "articles",
	31  =>  "pay_row",
	40  =>	"money_invest"
);

$PARTNER_TYPE_ARR = array(
	 3  =>	"p_psd", 
	 6  =>	"p_sl", 
	 8  =>	"p_b", 
	 9  =>	"p_txt", 
	10  =>	"p_frm", 
	16  =>	"p_packet_"
);

if($HASH==$V2_HASH) {
	if($PAYEE_ACCOUNT==PM_MEMBER_USD && $PAYMENT_UNITS==PAYMENT_UNITS) {

		if($shp_item!=false && array_key_exists($shp_item, $TABLE_ARR)) {

			$sql = $mysqli->query("SELECT * FROM `".$TABLE_ARR[$shp_item]."` WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' LIMIT 1");
			if($sql->num_rows) {
				$row = $sql->fetch_array();
				$username = $row["username"];
				$merch_amount = $row["money"];

				if($shp_item==3 | $shp_item==6 | $shp_item==8 | $shp_item==9 | $shp_item==10) {
					$DATE_END = ", `date_end`=`plan`*'".(24*60*60)."'+'".time()."'";
				}else{
					$DATE_END = false;
				}

				if(floatval($merch_amount_usd)!=floatval(round(($merch_amount/$CURS_USD),2))) {
					$f = fopen(PATH_TO_LOG."pm_bad.log", "ab+");
					fwrite($f, date("d.m.Y H:i")."; REASON: ERROR SUMMA; POST: ".serialize($_POST)."; STRING: $STRING; HASH: $HASH\n");
					fclose($f);
					exit("ERROR");

				}elseif($shp_item==1) {
					if(isset($username) && $username != false) {
						$b=$mysqli->query("Select price from tb_config WHERE item='bon_popoln'")->fetch_array();
                                                $merch_amount1=$merch_amount+$merch_amount*$b[0]*0.01;
												
												////////////////////
				$sql_bon_pay_status = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_status'");
				$bon_pay_status = $sql_bon_pay_status->fetch_object()->price;
				
				$sql_bon_pay_summa = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_summa'");
				$bon_pay_summa = $sql_bon_pay_summa->fetch_object()->price;
				
				$sql_bon_pay_money = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_money'");
				$bon_pay_money = $sql_bon_pay_money->fetch_object()->price;
				
				$sql_bon_pay_reiting = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_pay_reiting'");
				$bon_pay_reiting = $sql_bon_pay_reiting->fetch_object()->price;
			/////////////////////	
				if($bon_pay_status==1){
					if($merch_amount1>=$bon_pay_summa){
						$mysqli->query("UPDATE `tb_users` SET `money_rb`=`money_rb`+'".$bon_pay_money."', `reiting`=`reiting`+'".$bon_pay_reiting."' WHERE `username`='$username'") or die($mysqli->error);
					}
				}

						$mysqli->query("UPDATE `tb_users` SET `money_rb`=`money_rb`+'$merch_amount1', `money_in`=`money_in`+'$merch_amount' WHERE `username`='$username'") or die($mysqli->error);
						$mysqli->query("INSERT INTO `tb_history` (`user`, `amount`, `date`, `method`, `status`, `tipo`) 
						VALUES('$username', '$merch_amount', '".DATE("d.m.Yг. H:i")."', 'Пополнение рекламного баланса через PerfectMoney','Зачислено', 'popoln')") or die($mysqli->error);
						$mysqli->query("UPDATE `tb_add_pay` SET `status`='1' WHERE `merch_tran_id`='$merch_tran_id' AND`username`='$username'") or die($mysqli->error);
						stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);

						ProcRefRB($username, $merch_amount);
						/////////////////////////////
					$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_popoln' AND `howmany`='1'") or die($mysqli->error);
                    $bon_popoln =  $sql->fetch_object()->price;	

                    $pay_to_user = $merch_amount * ($bon_popoln/100);
	                $pay_to_user = round($pay_to_user,2);
	
					$sql_user = $mysqli->query("SELECT * FROM `tb_users` WHERE `username`='$username'");
	                $row_user = $sql_user->fetch_array();	
	                $email = $row_user["email"];	
	           require_once($_SERVER['DOCUMENT_ROOT'].'/class/email.conf.php');
			require_once($_SERVER['DOCUMENT_ROOT'].'/class/smtp.class.php');	
	    $var = array('{MONEY}','{BONUS}','{BONUSKEH}');
	    $zamena = array($merch_amount,$bon_popoln,$pay_to_user);
	    $msgtext = str_replace($var, $zamena, $email_temp['add_din']);		
		$mail_out = new mailPHP();	
	    $mail_error = $mail_out->send($email,$username,iconv("CP1251", "UTF-8", 'Пополнение рекламного баланса на SUPREME-GARDEN.RU'), iconv("CP1251", "UTF-8", $msgtext));
		/////////////////////////////////////////////////////
					}else{
						exit("ERROR");
					}

				}elseif($shp_item==40) {
					if($username!=false) {
						
						////////////////////
				$sql_bon_inv_status = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_status'");
				$bon_inv_status = $sql_bon_inv_status->fetch_object()->price;
				
				$sql_bon_inv_summa = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_summa'");
				$bon_inv_summa = $sql_bon_inv_summa->fetch_object()->price;
				
				$sql_bon_inv_money = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_money'");
				$bon_inv_money = $sql_bon_inv_money->fetch_object()->price;
				
				$sql_bon_inv_reiting = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='bon_inv_reiting'");
				$bon_inv_reiting = $sql_bon_inv_reiting->fetch_object()->price;
			/////////////////////	
				if($bon_inv_status==1){
					if($merch_amount>=$bon_inv_summa){
						$mysqli->query("UPDATE `tb_users` SET `money_inv`=`money_inv`+'".$bon_inv_money."', `reiting`=`reiting`+'".$bon_inv_reiting."' WHERE `username`='$username'") or die($mysqli->error);
					}
				}
				
						$mysqli->query("UPDATE `tb_users` SET `money_inv`=`money_inv`+'$merch_amount', `money_in`=`money_in`+'$merch_amount' WHERE `username`='$username'") or die($mysqli->error);
						$mysqli->query("INSERT INTO `tb_history` (`user`, `amount`, `date`, `method`, `status`, `tipo`) 
						VALUES('$username', '$merch_amount', '".DATE("d.m.Yг. H:i")."', 'Пополнение баланса инвестора через PerfectMoney','Зачислено', 'popoln')") or die($mysqli->error);

						$mysqli->query("UPDATE `tb_invest_money_in` SET `status`='1' WHERE `merch_tran_id`='$merch_tran_id' AND `username`='$username'") or die($mysqli->error);

						stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);
					}else{
						exit("ERROR");
					}

				}elseif($shp_item==16) {
					$mysqli->query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."' WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
					$mysqli->query("UPDATE `tb_users` SET `money_rek`=`money_rek`+'$merch_amount' WHERE `username`='$username'") or die($mysqli->error);

					stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);
					invest_stat($merch_amount, 1);
					ActionRef(number_format($merch_amount,2,".",""), $username);

                                        $merch_user_wmid = false;
				        konkurs_ads_new($merch_user_wmid, $username, $merch_amount);

					PartnerSet($username, $PARTNER_TYPE_ARR[$shp_item], $row["start_cena"], $row["packet"]);

					require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/add_adv_packet.php");

					require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/func_cache.php");
					cache_stat_links();
					cache_frm_links();
					cache_txt_links();
					cache_banners();

				}else{
					if($shp_item==21) {
						$mysqli->query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."',`money`='$merch_amount',`balance`='$merch_amount'  WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
					}elseif($shp_item==30) {
						$mysqli->query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='2', `date`='".time()."' WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
					}else{
						$mysqli->query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."' $DATE_END  WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
					}
					$mysqli->query("UPDATE `tb_users` SET `money_rek`=`money_rek`+'$merch_amount' WHERE `username`='$username'") or die($mysqli->error);

					stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);

					$merch_user_wmid = false;
					if($shp_item!=21) konkurs_ads_new($merch_user_wmid, $username, $merch_amount);
					if($shp_item==2) ads_date();
						if($shp_item==2) {
				$sql_rek = $mysqli->query("SELECT * FROM `".$TABLE_ARR[$shp_item]."` WHERE `merch_tran_id`='$merch_tran_id' LIMIT 1")->fetch_array();
				$geo_targ = $sql_rek["geo_targ"];
			$new_users = $sql_rek["new_users"];
			$no_ref = $sql_rek["no_ref"];
			$to_ref = $sql_rek["to_ref"];
			$sex_adv = $sql_rek["sex_adv"];
			$revisit = $sql_rek["revisit"];
			$unic_ip = $sql_rek["unic_ip"];
			if($geo_targ == false && $new_users == 0 && $no_ref == 0 && $to_ref == 0 && $sex_adv == 0 && $revisit== 0 && $unic_ip == 0) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_konkurs_conf` WHERE `type`='serf' AND `item`='timer_ot'");
				$konk_serf_timer = $sql->fetch_object()->price;
				
				if($sql_rek['timer']>=$konk_serf_timer){
				konkurs_serf_new($merch_user_wmid, $username, $merch_amount);
				}
				}
					}

				if($shp_item==23) ads_date();
				if($geo_targ == false && $new_users == 0 && $no_ref == 0 && $to_ref == 0 && $sex_adv == 0 && $revisit== 0 && $unic_ip == 0) {
				if($shp_item==23) konkurs_serf_new($merch_user_wmid, $username, $merch_amount);
				}

				if($shp_item==2 | $shp_item==11 | $shp_item==21 | $shp_item==23) {
						if($shp_item!=21) invest_stat($merch_amount, 1);
					}elseif($shp_item==5) {
						invest_stat($merch_amount, 2);
					}else{
						invest_stat($merch_amount, 4);
					}
					if($shp_item!=21) ActionRef(number_format($merch_amount,2,".",""), $username);

					if($shp_item==3 | $shp_item==6 | $shp_item==8 | $shp_item==9 | $shp_item==10) {
						if($shp_item==8) {
							PartnerSet($username, $PARTNER_TYPE_ARR[$shp_item], $row["start_cena"], $row["plan"], $row["type"]);
						}else{
							PartnerSet($username, $PARTNER_TYPE_ARR[$shp_item], $row["start_cena"], $row["plan"]);
						}
					}

					if($shp_item==6 | $shp_item==7 | $shp_item==8 | $shp_item==9 | $shp_item==10 | $shp_item==12 | $shp_item==17 | $shp_item==18 | $shp_item==20 | $shp_item==22 | $shp_item==31) {
						require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/func_cache.php");

						cache_stat_links();
						cache_kontext();
						cache_frm_links();
						cache_txt_links();
						cache_rek_cep();
						cache_banners();
						cache_beg_stroka();
						cache_catalog();
						cache_pay_row();
						cache_stat_kat();
				                cache_link();
					}
				}
			}else{
				exit("ERROR");
			}
		}else{
			exit("ERROR");
		}

	}else{
		$f = fopen(PATH_TO_LOG."pm_bad.log", "ab+");
		fwrite($f, date("d.m.Y H:i")."; REASON: fake data; POST: ".serialize($_POST)."; STRING: $STRING; HASH: $HASH\n");
		fclose($f);
	}
}else{
	$f=fopen(PATH_TO_LOG."pm_bad.log", "ab+");
	fwrite($f, date("d.m.Y H:i")."; REASON: bad hash; POST: ".serialize($_POST)."; STRING: $STRING; HASH: $HASH\n");
	fclose($f);
}
?>