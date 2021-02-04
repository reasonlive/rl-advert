<?php
header("Content-type: text/html; charset=windows-1251");
require($_SERVER["DOCUMENT_ROOT"]."/config.php");
require($_SERVER["DOCUMENT_ROOT"]."/merchant/func_mysql.php");
require($_SERVER["DOCUMENT_ROOT"]."/merchant/interkassa/ik_config.php");
define('PATH_TO_LOG', dirname(__FILE__).'/');

$ik_co_id = (isset($_REQUEST["ik_co_id"])) ? htmlspecialchars($_REQUEST["ik_co_id"]) : false;
$ik_inv_id = (isset($_REQUEST["ik_inv_id"])) ? htmlspecialchars($_REQUEST["ik_inv_id"]) : false;
$ik_inv_st = (isset($_REQUEST["ik_inv_st"])) ? htmlspecialchars($_REQUEST["ik_inv_st"]) : false;
$ik_trn_id = (isset($_REQUEST["ik_trn_id"])) ? htmlspecialchars($_REQUEST["ik_trn_id"]) : false;
$merch_tran_id = (isset($_REQUEST["ik_pm_no"])) ? intval(htmlspecialchars($_REQUEST["ik_pm_no"])) : false;
$ik_desc = (isset($_REQUEST["ik_desc"])) ? htmlspecialchars($_REQUEST["ik_desc"], NULL, "CP1251") : false;
$merch_amount = (isset($_REQUEST["ik_am"])) ? htmlspecialchars($_REQUEST["ik_am"]) : false;
$ik_cur = (isset($_REQUEST["ik_cur"])) ? floatval(htmlspecialchars($_REQUEST["ik_cur"])) : false;
$shp_item = ( isset($_REQUEST["ik_x_shp_item"]) && preg_match("|^[\d]{1,11}$|", htmlspecialchars(trim($_REQUEST["ik_x_shp_item"]))) ) ? intval(htmlspecialchars(trim($_REQUEST["ik_x_shp_item"]))) : false;
$sign_hash_post = (isset($_REQUEST["ik_sign"])) ? htmlspecialchars($_REQUEST["ik_sign"]) : false;

$dataSet = $_REQUEST;
$key_arr = array_key_exists('ik_sign', $dataSet);
if($key_arr != false) {unset($dataSet['ik_sign']);}
ksort($dataSet, SORT_STRING);
array_push($dataSet, $secret_key_ik);
$signString = implode(':', $dataSet);
$sign = base64_encode(md5($signString, true));

$post=false;
if(count($_POST)>0) {
	foreach($_POST as $key => $val) $post.="$key - $val\n";
//	mysql_query("INSERT INTO `tb_test` (`text`) VALUES('$post\n$signString\n')");
}

#запись в файл информации о проведенной операции
$f=@fopen(PATH_TO_LOG."ik_order.txt","a+") or die("error");
@fputs($f,"Summa: $merch_amount; Type: $shp_item; Tran_id: $merch_tran_id; Tran_id_ik: $ik_inv_id; Method: ".$_REQUEST["ik_pw_via"]."; Status: $ik_inv_st; Date: ".DATE("d.m.Y H:i")."; SIGN1: $sign_hash_post; SIGN2:$sign\n");
@fclose($f);

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
	20  =>	"tb_ads_beg_stroka",
	21  =>	"tb_ads_tests",
	22  =>	"tb_ads_catalog",
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
	20  =>	"bstroka",
	21  =>	"tests",
	22  =>	"catalog",
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

if($sign_hash_post!=$sign) {
	exit("ERROR");

}elseif($ik_inv_st=="success") {
	if($shp_item!=false && array_key_exists($shp_item, $TABLE_ARR)) {

		$sql = mysql_query("SELECT * FROM `".$TABLE_ARR[$shp_item]."` WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' LIMIT 1");
		if(mysql_num_rows($sql)) {
			$row = mysql_fetch_array($sql);
			$username = $row["username"];

			if($shp_item==3 | $shp_item==6 | $shp_item==8 | $shp_item==9 | $shp_item==10) {
				$DATE_END = ", `date_end`=`plan`*'".(24*60*60)."'+'".time()."'";
			}else{
				$DATE_END = false;
			}

			if(floatval($row["money"])!=floatval($merch_amount)) {
				exit("ERROR");

			}elseif($shp_item==1) {
				if(isset($username) && $username != false) {
					$sql_proc_add = mysql_query("SELECT `price` FROM `tb_config` WHERE `item`='poc_money_add'");
					$poc_money_add = (mysql_num_rows($sql_proc_add)>0 && mysql_result($sql_proc_add,0,0)>0 ) ? mysql_result($sql_proc_add,0,0) : 0;

					mysql_query("UPDATE `tb_users` SET `money_rb`=`money_rb`+'".number_format(($merch_amount * ($poc_money_add+100)/100), 2, ".", "")."', `money_in`=`money_in`+'$merch_amount' WHERE `username`='$username'") or die(mysql_error());
					mysql_query("UPDATE `tb_add_pay` SET `status`='1', `date`='".time()."' WHERE `merch_tran_id`='$merch_tran_id' AND `username`='$username'") or die(mysql_error());

					mysql_query("INSERT INTO `tb_history` (`user`, `amount`, `date`, `method`, `status`, `tipo`) 
					VALUES('$username', '$merch_amount', '".DATE("d.m.Yг. H:i")."', 'Пополнение рекламного баланса через InterKassa','Зачислено', 'popoln')") or die(mysql_error());

					stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);
					ProcRefRB($username, $merch_amount);

				}else{
					exit("ERROR");
				}

			}elseif($shp_item==40) {
				if($username!=false) {
					mysql_query("UPDATE `tb_users` SET `money_inv`=`money_inv`+'$merch_amount', `money_in`=`money_in`+'$merch_amount' WHERE `username`='$username'") or die(mysql_error());
					mysql_query("INSERT INTO `tb_history` (`user`, `amount`, `date`, `method`, `status`, `tipo`) 
					VALUES('$username', '$merch_amount', '".DATE("d.m.Yг. H:i")."', 'Пополнение баланса инвестора через InterKassa','Зачислено', 'popoln')") or die(mysql_error());
					mysql_query("UPDATE `tb_invest_money_in` SET `status`='1' WHERE `merch_tran_id`='$merch_tran_id' AND `username`='$username'") or die(mysql_error());

					stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);
				}else{
					exit("ERROR");
				}

			}elseif($shp_item==16) {
				mysql_query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."' WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
				mysql_query("UPDATE `tb_users` SET `money_rek`=`money_rek`+'$merch_amount' WHERE `username`='$username'") or die(mysql_error());

				stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);
				invest_stat($merch_amount, 1);
				ActionRef(number_format($merch_amount,2,".",""), $username);

				$merch_user_wmid = false;
				konkurs_ads_new($merch_user_wmid, $username, $merch_amount);

				ads_date();

				PartnerSet($username, $PARTNER_TYPE_ARR[$shp_item], $row["start_cena"], $row["packet"]);

				require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/add_adv_packet.php");

				require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/func_cache.php");
				cache_stat_links();
				cache_frm_links();
				cache_txt_links();
				cache_banners();
			}else{
				if($shp_item==21) {
					mysql_query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."',`money`='$merch_amount',`balance`='$merch_amount'  WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
				}elseif($shp_item==30) {
					mysql_query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='2', `date`='".time()."' WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
				}else{
					mysql_query("UPDATE `".$TABLE_ARR[$shp_item]."` SET `status`='1', `date`='".time()."' $DATE_END  WHERE `status`='0' AND `merch_tran_id`='$merch_tran_id' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
				}
				mysql_query("UPDATE `tb_users` SET `money_rek`=`money_rek`+'$merch_amount' WHERE `username`='$username'") or die(mysql_error());

				stat_pay($STAT_PAY_ARR[$shp_item], $merch_amount);

				$merch_user_wmid = false;
				if($shp_item!=21) konkurs_ads_new($merch_user_wmid, $username, $merch_amount);
				if($shp_item==2) ads_date();
				if($shp_item==2) konkurs_serf_new($merch_user_wmid, $username, $merch_amount);

				if($shp_item==2 | $shp_item==11 | $shp_item==21) {
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

				if($shp_item==6 | $shp_item==7 | $shp_item==8 | $shp_item==9 | $shp_item==10 | $shp_item==12 | $shp_item==20 | $shp_item==22 | $shp_item==31) {
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
				}
			}
		}else{
			exit("ERROR");
		}
	}else{
		exit("ERROR");
	}
}else{
	exit("ERROR");
}
?>