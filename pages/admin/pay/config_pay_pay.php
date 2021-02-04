<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки платежей</b></h3>';

if(count($_POST)>0) {
	$site_pay_wm = intval(trim($_POST["site_pay_wm"]));

	$site_pay_lp = intval(trim($_POST["site_pay_robo"]));

	$site_pay_payeer = intval(trim($_POST["site_pay_payeer"]));

	$site_pay_qw = intval(trim($_POST["site_pay_qw"]));

	$site_pay_ym = intval(trim($_POST["site_pay_ym"]));
	
	$site_pay_pm = intval(trim($_POST["site_pay_pm"]));
	
	$site_pay_wo = intval(trim($_POST["site_pay_wo"]));
	
	$site_pay_mega = intval(trim($_POST["site_pay_mega"]));
	
	$site_pay_free = intval(trim($_POST["site_pay_free"]));
	
	$site_pay_advcash = intval(trim($_POST["site_pay_advcash"]));
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_wm' WHERE `item`='site_pay_wm' AND `howmany`='1'") or die($mysqli->error);

	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_lp' WHERE `item`='site_pay_robo' AND `howmany`='1'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_payeer' WHERE `item`='site_pay_payeer' AND `howmany`='1'") or die($mysqli->error);

	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_qw' WHERE `item`='site_pay_qw' AND `howmany`='1'") or die($mysqli->error);

	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_ym' WHERE `item`='site_pay_ym' AND `howmany`='1'") or die($mysqli->error);	

	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_pm' WHERE `item`='site_pay_pm' AND `howmany`='1'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_wo' WHERE `item`='site_pay_wo' AND `howmany`='1'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_mega' WHERE `item`='site_pay_mega' AND `howmany`='1'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_free' WHERE `item`='site_pay_free' AND `howmany`='1'") or die($mysqli->error);
	
	$mysqli->query("UPDATE `tb_config` SET `price`='$site_pay_advcash' WHERE `item`='site_pay_advcash' AND `howmany`='1'") or die($mysqli->error);

	echo '<span class="msg-ok">Изменения успешно сохранены.</span>';
	echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 2000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_wm' AND `howmany`='1'");
	$site_pay_wm = $sql->fetch_object()->price;

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_robo' AND `howmany`='1'");
	$site_pay_robo = $sql->fetch_object()->price;

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_payeer' AND `howmany`='1'");
	$site_pay_payeer = $sql->fetch_object()->price;

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_qw' AND `howmany`='1'");
	$site_pay_qw = $sql->fetch_object()->price;

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_ym' AND `howmany`='1'");
	$site_pay_ym = $sql->fetch_object()->price;

	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_pm' AND `howmany`='1'");
	$site_pay_pm = $sql->fetch_object()->price;
	
	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_wo' AND `howmany`='1'");
	$site_pay_wo = $sql->fetch_object()->price;
	
	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_mega' AND `howmany`='1'");
	$site_pay_mega = $sql->fetch_object()->price;
	
	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_free' AND `howmany`='1'");
	$site_pay_free = $sql->fetch_object()->price;
	
	$sql=$mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='site_pay_advcash' AND `howmany`='1'");
	$site_pay_advcash = $sql->fetch_object()->price;

?>

<form method="post" action="" id="newform">
<table width="100%">

<tr><td width="50%">
	<br><b>Настройки платежей на WebMoney</b><br>
	<table>
	<tr><th width="200" align="left">Выплаты на WebMoney:</th> <td>
		<select style="width:125px;" name="site_pay_wm">
			
			<option value="1" <?php if($site_pay_wm=="1") echo "selected";?>>Включены</option>
			<option value="2" <?php if($site_pay_wm=="2") echo "selected";?>>Отключен</option>
		</select></td></tr>
		</table>

<br><b>Настройки платежей на Freekacca</b><br>
	<table>
	<tr><th width="200" align="left">Выплаты на Freekacca:</th> <td><select style="width:125px;" name="site_pay_free"><option value="0" <?php if($site_pay_free=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_free=="1") echo "selected";?>>Включены</option></select></td></tr>
</table>
</td><td>
    <tr><td width="50%">
	<br><b>Настройки Платежей на Robokassa</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на Robokassa:</th> <td><select style="width:125px;" name="site_pay_robo"><option value="0" <?php if($site_pay_robo=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_robo=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td><td>
<br><b>Настройки Выплат на advcash</b><br>
	<table>
	<tr><th width="200" align="left">Выплаты на advcash:</th> <td><select style="width:125px;" name="site_pay_advcash"><option value="0" <?php if($site_pay_advcash=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_advcash=="1") echo "selected";?>>Включены</option></select></td></tr>
</table>
</td><td>
<br><b>Настройки Платежей на Megakassa</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на Megakassa:</th> <td><select style="width:125px;" name="site_pay_mega"><option value="0" <?php if($site_pay_mega=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_mega=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td></tr>

<tr><td width="50%">
	<br><b>Настройки Платежей на Payeer</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на Payeer:</th> <td><select style="width:125px;" name="site_pay_payeer"><option value="0" <?php if($site_pay_payeer=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_payeer=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td><td>
	<br><b>Настройки Платежей на QIWI Кошелек</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на QIWI Кошелек:</th> <td><select style="width:125px;" name="site_pay_qw"><option value="0" <?php if($site_pay_qw=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_qw=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td></tr>
<tr><td width="50%">
	<br><b>Настройки Платежей на Walet one</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на Walet one:</th> <td><select style="width:125px;" name="site_pay_wo"><option value="0" <?php if($site_pay_wo=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_wo=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
	</td></tr>
<tr><td width="50%">
	<br><b>Настройки Платежей на Яндекс.Деньги</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на Яндекс.Деньги:</th> <td><select style="width:125px;" name="site_pay_ym"><option value="0" <?php if($site_pay_ym=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_ym=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td><td>
	<br><b>Настройки Платежей на PerfectMoney</b><br>
	<table>
	<tr><th width="200" align="left">Платежейы на PerfectMoney:</th> <td><select style="width:125px;" name="site_pay_pm"><option value="0" <?php if($site_pay_pm=="0") echo "selected";?>>Отключены</option><option value="1" <?php if($site_pay_pm=="1") echo "selected";?>>Включены</option></select></td></tr>
	</table>
</td></tr>
</table>
<table width="100%">
	<tr align="center"><td align="center"><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>
</table>

</form>