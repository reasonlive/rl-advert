<?php
session_start();

require($_SERVER["DOCUMENT_ROOT"]."/merchant/yandexmoney/ym_config.php");
require($_SERVER["DOCUMENT_ROOT"]."/merchant/yandexmoney/ym_outresult.php");

$ym = new YandexMoney(CLIENT_ID, REDIRECT_URL);
$result_auth = $ym->getOAuthToken(@$_GET["code"]);
echo "$result_auth";
?>
