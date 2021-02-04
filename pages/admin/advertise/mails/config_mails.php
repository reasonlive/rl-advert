<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки рекламных писем</b></h3>';

if(count($_POST)>0) {
	$cena_mails_1 = round(floatval(trim($_POST["cena_mails_1"])), 5);
	$cena_mails_2 = round(floatval(trim($_POST["cena_mails_2"])), 5);
	$cena_mails_3 = round(floatval(trim($_POST["cena_mails_3"])), 5);
	$cena_mails_color = round(floatval(trim($_POST["cena_mails_color"])), 5);
	$cena_mails_active = round(floatval(trim($_POST["cena_mails_active"])), 5);
	$cena_mails_gotosite = round(floatval(trim($_POST["cena_mails_gotosite"])), 5);
	$min_mails = intval(trim($_POST["min_mails"]));
	$nacenka_mails = intval(trim($_POST["nacenka_mails"]));

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_1' WHERE `item`='cena_mails' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_2' WHERE `item`='cena_mails' AND `howmany`='2'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_3' WHERE `item`='cena_mails' AND `howmany`='3'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_color' WHERE `item`='cena_mails_color' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_active' WHERE `item`='cena_mails_active' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_mails_gotosite' WHERE `item`='cena_mails_gotosite' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$min_mails' WHERE `item`='min_mails' AND `howmany`='1'") or die($mysqli->error);
	$mysqli->query("UPDATE `tb_config` SET `price`='$nacenka_mails' WHERE `item`='nacenka_mails' AND `howmany`='1'") or die($mysqli->error);

	echo '<span class="msg-ok">Изменения успешно сохранены!</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='1'");
$cena_mails_1 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='2'");
$cena_mails_2 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='3'");
$cena_mails_3 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_color' AND `howmany`='1'");
$cena_mails_color = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_active' AND `howmany`='1'");
$cena_mails_active = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_gotosite' AND `howmany`='1'");
$cena_mails_gotosite = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='nacenka_mails' AND `howmany`='1'");
$nacenka_mails = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='min_mails' AND `howmany`='1'");
$min_mails = $sql->fetch_object()->price;

?>

<script type="text/javascript" language="JavaScript"> 
	function gebi(id){
		return document.getElementById(id)
	}


	function obsch(){
		var cena_mails_1 = gebi('cena_mails_1').value;
		var cena_mails_2 = gebi('cena_mails_2').value;
		var cena_mails_3 = gebi('cena_mails_3').value;
		var cena_mails_color = gebi('cena_mails_color').value;
		var cena_mails_active = gebi('cena_mails_active').value;
		var cena_mails_gotosite = gebi('cena_mails_gotosite').value;
		var nacenka_mails = gebi('nacenka_mails').value;

		var price_1 = (cena_mails_1*(1+(nacenka_mails/100)));
		var price_2 = (cena_mails_2*(1+(nacenka_mails/100)));
		var price_3 = (cena_mails_3*(1+(nacenka_mails/100)));
		var price_4 = (cena_mails_color*(1+(nacenka_mails/100)));
		var price_5 = (cena_mails_active*(1+(nacenka_mails/100)));
		var price_6 = (cena_mails_gotosite*(1+(nacenka_mails/100)));

		gebi('price_1').innerHTML = Math.round(price_1.toFixed(12)*10000000)/10000000 + " руб.";
		gebi('price_2').innerHTML = Math.round(price_2.toFixed(12)*10000000)/10000000 + " руб.";
		gebi('price_3').innerHTML = Math.round(price_3.toFixed(12)*10000000)/10000000 + " руб.";
		gebi('price_4').innerHTML = Math.round(price_4.toFixed(12)*10000000)/10000000 + " руб.";
		gebi('price_5').innerHTML = Math.round(price_5.toFixed(12)*10000000)/10000000 + " руб.";
		gebi('price_6').innerHTML = Math.round(price_6.toFixed(12)*10000000)/10000000 + " руб.";

	}
</script>


<form method="post" action="" style="width:800px;" id="newform">
<table>
	<tr><th>Параметры</th><th>Значение</th><th>Для рекламодателя с учетом наценки системы</th></tr>
	<tr>
		<td align="left">Стоимость письма VIP(60сек.):</td>
		<td><input type="text" class="ok12" name="cena_mails_1" value="<?=$cena_mails_1;?>" style="text-align:right;" id="cena_mails_1" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_1" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Стоимость письма STANDART(40сек.):</td>
		<td><input type="text" class="ok12" name="cena_mails_2" value="<?=$cena_mails_2;?>" style="text-align:right;" id="cena_mails_2" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_2" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Стоимость письма LITE(20сек.):</td>
		<td><input type="text" class="ok12" name="cena_mails_3" value="<?=$cena_mails_3;?>" style="text-align:right;" id="cena_mails_3" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_3" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Стоимость выделения цветом за 1 письмо:</td>
		<td><input type="text" class="ok12" name="cena_mails_color" value="<?=$cena_mails_color;?>" style="text-align:right;" id="cena_mails_color" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_4" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Стоимость активного окна за 1 письмо:</td>
		<td><input type="text" class="ok12" name="cena_mails_active" value="<?=$cena_mails_active;?>" style="text-align:right;" id="cena_mails_active" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_5" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Стоимость последующего перехода за 1 письмо:</td>
		<td><input type="text" class="ok12" name="cena_mails_gotosite" value="<?=$cena_mails_gotosite;?>" style="text-align:right;" id="cena_mails_gotosite" onChange="obsch();" onKeyUp="obsch();">&nbsp;руб.</td>
		<td align="right"><span id="price_6" style="color:#228B22; font-weight: bold;"></span></td>
	</tr>
	<tr>
		<td align="left">Минимальное кол-во для заказа:</td>
		<td><input type="text" class="ok12" name="min_mails" value="<?=$min_mails;?>" style="text-align:right;">&nbsp;шт.</td>
		<td>&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td align="left">Комиссия системы (наценка):</td>
		<td><input type="text" class="ok12" name="nacenka_mails" value="<?=$nacenka_mails;?>" style="text-align:right;" id="nacenka_mails" onChange="obsch();" onKeyUp="obsch();">&nbsp;%.</td>
		<td>&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3"><div align="center"><input type="submit" value="сохранить изменения" class="sub-blue160"></div></td>
	</tr>
</table>
</form>

<script language="JavaScript"> obsch(); </script>