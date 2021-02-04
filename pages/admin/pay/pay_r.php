<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Ручные выплаты</b></h3>';

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis' AND `howmany`='1'");
$pay_comis1 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis' AND `howmany`='2'");
$pay_comis2 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis_qw' AND `howmany`='1'");
$pay_comis_qw = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis_sberbank' AND `howmany`='1'");
$pay_comis_sberbank = $sql->fetch_object()->price;


if(isset($_GET["option"]) && isset($_POST["id"]) && $_GET["option"]=="pay") {
	$id = (isset($_POST["id"])) ? intval($_POST["id"]) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `id`='$id' AND `status_pay`='0' AND (`method`='WebMoney' OR `method`='Qiwi' OR `method`='SberBank') AND `status`='' AND `tipo`='0'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$username = $row["user"];
		$money_pay = abs($row["amount"]);

		$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Выплата #'.$row["tran_id"].' пользователю '.$username.' успешно произведена!</span>';
	}else{
		echo '<span id="info-msg" class="msg-error">Выплаты #'.$row["tran_id"].' нет, либо выплата уже была сделана (или отменена)!</span>';
	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="3;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

if(isset($_GET["option"]) && isset($_POST["id"]) && $_GET["option"]=="dell_pay") {
	$id = (isset($_POST["id"])) ? intval($_POST["id"]) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `id`='$id' AND `status_pay`='0' AND (`method`='WebMoney' OR `method`='Qiwi' OR `method`='SberBank') AND `status`='' AND `tipo`='0'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();
		$username = $row["user"];
		$money_pay = abs($row["amount"]);

		$mysqli->query("UPDATE `tb_users` SET `money`=`money`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_history` SET `status_pay`='2', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Выпплата пользователю '.$username.' отменена, деньги возвращены на баланс аккаунта!</span>';
	}else{
		echo '<span id="info-msg" class="msg-error">Выплаты #'.$row["tran_id"].' нет, либо выплата уже была сделана (или отменена)!</span>';
	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 3000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="3;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `status_pay`='0' AND (`method`='WebMoney' OR `method`='Qiwi' OR `method`='SberBank') AND `status`='' AND `tipo`='0' ORDER BY `id` ASC");
$kol = $sql->num_rows;
if($kol>0) {
	$sql_s = $mysqli->query("SELECT sum(`amount`) FROM `tb_history` WHERE `status_pay`='0' AND (`method`='WebMoney' OR `method`='Qiwi' OR `method`='SberBank') AND `status`='' AND `tipo`='0'");
	$all_count1 = $sql_s->fetch_array();
 $sumpay_wait = $all_count1['0'];
	//$sumpay_wait = mysql_result($sql_s,0,0);

	echo '<br><b>Всего:</b> <b style="color:#FF0000;">'.$kol.'</b> на сумму <b style="color:#FF0000;">'.p_floor($sumpay_wait, 2).'</b> руб.';
}

echo '<table>';
echo '<tr align="center">';
	echo '<th>ID</th>';
	echo '<th>Логин</th>';
	echo '<th>Кошелек</th>';
	echo '<th>Дата заказа</th>';
	echo '<th>Объем оплаты</th>';
	echo '<th>Примечание</th>';
	echo '<th></th>';
	echo '<th></th>';
	echo '<th></th>';
echo '</tr>';

$sum_m = 0;
if($kol>0) {
	while ($row = $sql->fetch_assoc()) {
		echo '<tr align="center">';
		echo '<td>'.$row["tran_id"].'</td>';
		echo '<td>'.$row["user"].'</td>';
		echo '<td>'.$row["method"].': '.$row["wmr"].'</td>';
		echo '<td>'.DATE("d.m.Y H:i", $row["time"]).'</td>';

		if(strtolower($row["method"])==strtolower("WebMomey")) {
			if(abs($row["amount"])>1.25) {
				$summa_topay = p_floor( ((abs($row["amount"]) * (100 - $pay_comis1)/100) - $pay_comis2), 2);
			}else{
				$summa_topay = (abs($row["amount"]) - 0.01 - $pay_comis2);
			}

		}elseif(strtolower($row["method"])==strtolower("Qiwi")) {
			$summa_topay = p_floor( ((abs($row["amount"]) * (100 - $pay_comis_qw)/100)), 2);

		}elseif(strtolower($row["method"])==strtolower("PayPal")) {
			$summa_topay = p_floor( ((abs($row["amount"]) * (100 - $pay_comis_paypal)/100)), 2);

		}elseif(strtolower($row["method"])==strtolower("SberBank")) {
			$summa_topay = p_floor( ((abs($row["amount"]) * (100 - $pay_comis_sberbank)/100)), 2);

		}else{
			$summa_topay = '<span style="color:#FF0000;">Ошибка!</span>';
		}

		$desc = "Выплата с ".$url." логин - ".$row["user"].". Благодарим за работу!";

		echo '<td>'.$summa_topay.'</td>';
		echo '<td>'.$desc.'</td>';

		if(strtolower($row["method"])==strtolower("WebMomey")) {
			echo '<td width="80"><a href="wmk:payto?Purse='.$row["wmr"].'&Amount='.$summa_topay.'&Desc='.$desc.'&BringToFront=Y" class="sub-blue" style="padding:0;">Выплатить</a></td>';
		}else{
			echo '<td>&nbsp;&nbsp;</td>';
		}

		echo '<td width="80"><form method="post" action="'.$_SERVER['PHP_SELF'].'?op='.intval($_GET["op"]).'&option=pay"><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="sub-green" value="Оплачено"></form></td>';
		echo '<td width="80"><form method="post" action="'.$_SERVER['PHP_SELF'].'?op='.intval($_GET["op"]).'&option=dell_pay"><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="sub-red" value="Отменить"></form></td>';
		echo '</tr>';
	}
	echo '</table>';
}else{
	echo '<tr><td colspan="10"><div align="center" style="color:#FF0000; font-weight:bold;">Выплат в ожидании нет.</div></td></tr>';
	echo '</table>';
}
?>
