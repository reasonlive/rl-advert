<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Заголовки к конкурсам</b></h3>';

function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("?","&#063;",$mensaje);
	$mensaje = str_replace(">","&#062;",$mensaje);
	$mensaje = str_replace("<","&#060;",$mensaje);
	$mensaje = str_replace("'","&#039;",$mensaje);
	$mensaje = str_replace("`","&#096;",$mensaje);
	$mensaje = str_replace("$","&#036;",$mensaje);
	$mensaje = str_replace('"',"&#034;",$mensaje);
	$mensaje = str_replace("  "," ",$mensaje);
	$mensaje = str_replace("&amp amp ","&",$mensaje);
	$mensaje = str_replace("&&","&",$mensaje);
	$mensaje = str_replace("http://http://","http://",$mensaje);
	$mensaje = str_replace("https://https://","https://",$mensaje);
	$mensaje = str_replace("&#063;","?",$mensaje);
	return $mensaje;
}

if(count($_POST)>0) {
	$konkurs_title_ads = (isset($_POST["konkurs_title_ads"])) ? limitatexto(limpiarez($_POST["konkurs_title_ads"]),300) : false;
	$konkurs_title_ads_big = (isset($_POST["konkurs_title_ads_big"])) ? limitatexto(limpiarez($_POST["konkurs_title_ads_big"]),300) : false;
	$konkurs_title_click = (isset($_POST["konkurs_title_click"])) ? limitatexto(limpiarez($_POST["konkurs_title_click"]),300) : false;
	$konkurs_title_test = (isset($_POST["konkurs_title_test"])) ? limitatexto(limpiarez($_POST["konkurs_title_test"]),300) : false;
	$konkurs_title_youtub = (isset($_POST["konkurs_title_youtub"])) ? limitatexto(limpiarez($_POST["konkurs_title_youtub"]),300) : false;
	$konkurs_title_task = (isset($_POST["konkurs_title_task"])) ? limitatexto(limpiarez($_POST["konkurs_title_task"]),300) : false;
	$konkurs_title_ref = (isset($_POST["konkurs_title_ref"])) ? limitatexto(limpiarez($_POST["konkurs_title_ref"]),300) : false;

	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_ads' WHERE `type`='ads' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_ads_big' WHERE `type`='ads_big' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_click' WHERE `type`='click' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_test' WHERE `type`='test' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_youtub' WHERE `type`='youtub' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_task' WHERE `type`='task' AND `item`='title'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_konkurs_conf` SET `price_array`='$konkurs_title_ref' WHERE `type`='ref' AND `item`='title'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 1500); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads' AND `item`='title'");
$konkurs_title_ads = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ads_big' AND `item`='title'");
$konkurs_title_ads_big = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='click' AND `item`='title'");
$konkurs_title_click = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='test' AND `item`='title'");
$konkurs_title_test = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='youtub' AND `item`='title'");
$konkurs_title_youtub = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='task' AND `item`='title'");
$konkurs_title_task = $sql->fetch_object()->price_array;

$sql = $mysqli->query("SELECT `price_array` FROM `tb_konkurs_conf` WHERE `type`='ref' AND `item`='title'");
$konkurs_title_ref = $sql->fetch_object()->price_array;

echo '<form method="POST" action="" id="newform" autocomplete="off">';
echo '<table>';
echo '<tr>';
	echo '<th width="250px">Параметр</th>';
	echo '<th>Значение</th>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу рекламодателей</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_ads" value="'.$konkurs_title_ads.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу рекламодателей №2</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_ads" value="'.$konkurs_title_ads_big.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу кликеров</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_click" value="'.$konkurs_title_click.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу тестов</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_test" value="'.$konkurs_title_test.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу YouTube</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_youtub" value="'.$konkurs_title_youtub.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу заданий</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_task" value="'.$konkurs_title_task.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td><b>Заголовок к конкурсу рефералов</b></td>';
	echo '<td align="left"><input type="text" name="konkurs_title_ref" value="'.$konkurs_title_ref.'" class="ok" maxlength="300"></td>';
echo '</tr>';
echo '<tr>';
	echo '<td colspan="2" align="center"><input type="submit" value="Сохранить" class="sub-blue160" style="float:none;" /></td>';
echo '</tr>';

echo '</table>';
echo '</form>';

?>