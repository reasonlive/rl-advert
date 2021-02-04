<?php 

//CHECKING IF USER HAVE ALREADY REGISTERED OR NOT
// Проверка на уникальность регистрации


function __autoload($name){ include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");}
//$func = new func;
$config = new config;
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

$date = date('Y.m.d', time());



if(isset($_POST['email']) or isset($_POST['username'])){
	$mail  = $_POST['email'];
	$name  = $_POST['username'];

	$db->query("SELECT * FROM `tb_users` WHERE email='$mail' OR username='$name' ");
	if($db->NumRows() > 0)echo json_encode(['false']);
	else echo json_encode(['true']);



}



?>