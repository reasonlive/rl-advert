<?
require($_SERVER['DOCUMENT_ROOT'].'/merchant/payment_config.php');
require($_SERVER['DOCUMENT_ROOT'].'/all_zapros.php');
define('PATH_TO_LOG', $_SERVER['DOCUMENT_ROOT'].'/merchant/yandexmoney/');

$hash = (isset($_REQUEST["sha1_hash"])) ? strtoupper($_REQUEST["sha1_hash"]) : false;
$site_hash = @strtoupper(sha1($_REQUEST["notification_type"].'&'.$_REQUEST["operation_id"].'&'.$_REQUEST["amount"].'&'.$_REQUEST["currency"].'&'.$_REQUEST["datetime"].'&'.$_REQUEST["sender"].'&'.$_REQUEST["codepro"].'&'.YM_SECRET_KEY.'&'.$_REQUEST["label"]));

if ($site_hash != $hash) {
exit('Верификация не пройдена. SHA1_HASH не совпадает.');
}

$nano_order = (isset($_REQUEST['label']) && preg_match("|^[\d]{1,11}$|", trim($_REQUEST['label']))) ? intval(trim($_REQUEST['label'])) : false;
$amount = (isset($_REQUEST['amount'])) ? floatval($_REQUEST['amount']) : NULL;

$allsumme = mysql_result(mysql_query("SELECT SUM(summ) FROM `nano_korzina` WHERE `id_user`='$nano_order'"),0);
$allsumme = number_format($allsumme, 2, '.', "");
$merch = "2";
require($_SERVER['DOCUMENT_ROOT'].'/merchant/success-merch.php');
?>