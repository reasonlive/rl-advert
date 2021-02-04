<?php
header("Content-type: text/html; charset=utf-8");
require($_SERVER['DOCUMENT_ROOT'].'/all_zapros.php');
require_once($_SERVER['DOCUMENT_ROOT']."/merchant/payment_config.php");

$merch_amount = (isset($_REQUEST["amount"])) ? $_REQUEST["amount"] : false;
$merch_tran_id = (isset($_REQUEST["label"])) ? trim($_REQUEST["label"]) : false;
$hash = (isset($_REQUEST["sha1_hash"])) ? strtoupper($_REQUEST["sha1_hash"]) : false;
$site_hash = @strtoupper(sha1($_REQUEST["notification_type"].'&'.$_REQUEST["operation_id"].'&'.$_REQUEST["amount"].'&'.$_REQUEST["currency"].'&'.$_REQUEST["datetime"].'&'.$_REQUEST["sender"].'&'.$_REQUEST["codepro"].'&'.YM_SECRET_KEY.'&'.$_REQUEST["label"]));

$nano_order = ( isset($merch_tran_id) && preg_match("|^[\d]{1,11}$|", strip_tags(trim($merch_tran_id))) ) ? intval(strip_tags(trim($merch_tran_id))) : false;



if($hash==$site_hash) {
$allsumme = mysql_result(mysql_query("SELECT SUM(summ) FROM `nano_korzina` WHERE `id_user`='$nano_order'"),0);
if($merch_amount >= $allsumme){
$merch = "2";
require_once($_SERVER['DOCUMENT_ROOT']."/merchant/success-merch.php");
}else{
exit("ERROR");
}
}else{
exit("ERROR");
}

?>