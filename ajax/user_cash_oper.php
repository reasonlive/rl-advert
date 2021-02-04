<?php 


spl_autoload_register(function($name){
  include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");
});

$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

$date = date('Y.m.d', time());
$ip = $func->UserIP;
$country_code = $func->CountryCode;

include_once($_SERVER['DOCUMENT_ROOT'] . "/pay/auto_pay_req/wmxml.inc.php");








 ?>