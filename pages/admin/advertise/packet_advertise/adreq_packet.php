<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы пакетов рекламы</b></h3>';

$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
$system_pay[1] = "WebMoney";
$system_pay[2] = "RoboKassa";
$system_pay[3] = "Wallet One";
$system_pay[4] = "InterKassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[9] = "MegaKassa";
$system_pay[20] = "FreeKassa";
$system_pay[10] = "Рекл. счет";

if(isset($_POST["id"])) {

	$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id"]))) ? intval(limpiar(trim($_POST["id"]))) : false;
	$option = (isset($_GET["option"])) ? limpiar($_GET["option"]) : false;

	if($option=="add") {
		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_packet` WHERE `id`='$id'");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();

			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];
			$wmid_user = $row["wmid"];
			$username = $row["username"];
			$packet = $row["packet"];
			$ip = $row["ip"];
			$method_pay = "-1";

			$sql_p = $mysqli->query("SELECT * FROM `tb_config_packet` WHERE `packet`='$packet'");
			if($sql_p->num_rows>0) {
				$row_p = $sql_p->fetch_array();
				$ds_plan = $row_p["ds_plan"];
				$ds_timer = $row_p["ds_timer"];
				$slink_plan = $row_p["slink_plan"];
				$sban468_plan = $row_p["sban468_plan"];
				$sban100_plan = $row_p["sban100_plan"];
				$sban200_plan = $row_p["sban200_plan"];
				$psdlink_plan = $row_p["psdlink_plan"];
			}else{
				echo '<span class="msg-error">Ошибка! Такого пакета рекламы нет!</span>';
				include('footer.php');
				exit();
			}


			$ds_url = $row["ds_url"];
			$ds_title = $row["ds_title"];
			$ds_text = $row["ds_text"];

			$slink_url = $row["slink_url"];
			$slink_text = $row["slink_text"];

			$sban468_url = $row["sban468_url"];
			$sban468_urlban = $row["sban468_urlban"];
			$sban100_url = $row["sban100_url"];
			$sban100_urlban = $row["sban100_urlban"];
			$sban200_url = $row["sban200_url"];
			$sban200_urlban = $row["sban200_urlban"];

			$psdlink_url = $row["psdlink_url"];
			$psdlink_text = $row["psdlink_text"];

			$mysqli->query("INSERT INTO `tb_ads_dlink` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`type_serf`,`date`,`wmid`,`username`,`geo_targ`,`content`,`active`,`revisit`,`color`,`timer`,`nolimit`,`limit_d`,`limit_h`,`limit_d_now`,`limit_h_now`,`url`,`title`,`description`,`plan`,`totals`,`ip`,`money`) 
			VALUES('1','','$merch_tran_id','$method_pay','1','".time()."','$wmid_user','$username','','0','0','0','0','$ds_timer','0','0','0','0','0','$ds_url','$ds_title','$ds_text','$ds_plan','$ds_plan','$ip','$money_pay')") or die($mysqli->error);

			$mysqli->query("INSERT INTO `tb_ads_slink` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `plan`, `date`, `date_end`, `url`, `description`,`ip`) 
			VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '$slink_plan', '".time()."', '".(time()+$slink_plan*24*60*60)."', '$slink_url', '$slink_text', '$ip')") or die($mysqli->error);

			$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`,`ip`) 
			VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '468x60', '$sban468_plan', '".time()."', '".(time()+$sban468_plan*24*60*60)."', '$sban468_url', '$sban468_urlban', '$ip')") or die($mysqli->error);

			$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`,`ip`) 
			VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '100x100', '$sban100_plan', '".time()."', '".(time()+$sban100_plan*24*60*60)."', '$sban100_url', '$sban100_urlban', '$ip')") or die($mysqli->error);

			$mysqli->query("INSERT INTO `tb_ads_banner` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`, `type`, `plan`, `date`, `date_end`, `url`, `urlbanner`,`ip`) 
			VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username', '200x300', '$sban200_plan', '".time()."', '".(time()+$sban200_plan*24*60*60)."', '$sban200_url', '$sban200_urlban', '$ip')") or die($mysqli->error);

			$mysqli->query("INSERT INTO `tb_ads_psevdo` (`status`, `merch_tran_id`, `method_pay`, `wmid`, `username`,`plan`, `date`, `date_end`, `url`, `description`,`ip`) 
			VALUES('1', '$merch_tran_id', '$method_pay', '$wmid_user', '$username','$psdlink_plan', '".time()."', '".(time()+$psdlink_plan*24*60*60)."', '$psdlink_url', '$psdlink_text', '$ip')") or die($mysqli->error);

			$mysqli->query("DELETE FROM `tb_ads_packet` WHERE `id`='$id'");

			require($_SERVER["DOCUMENT_ROOT"]."/merchant/func_mysql.php");
			stat_pay('packet', $money_pay);
			if($wmid_user!=false) {
				$wmr_user = false;
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
			}

			require_once("".$_SERVER['DOCUMENT_ROOT']."/merchant/func_cache.php");
			cache_stat_links();
			cache_kontext();
			cache_frm_links();
			cache_txt_links();
			cache_rek_cep();
			cache_banners();

			echo '<span class="msg-ok">Реклама добавлена.</span>';

		}else{
			echo '<span class="msg-error">Ошибка! Реклама не найдена.</span>';
		}

		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}

	if($option=="dell") {
		$mysqli->query("DELETE FROM `tb_ads_packet` WHERE `id`='$id'") or die($mysqli->error);

		echo '<span class="msg-error">Заказ удален.</span>';
		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}

echo '<table>';
echo '<tr>';
	echo '<th>ID</th>';
	echo '<th>Счет №</th>';
	echo '<th>WMID</th>';
	echo '<th>Логин</th>';
	echo '<th>Способ оплаты</th>';
	echo '<th>Дата заказа</th>';
	echo '<th>Номер пакета</th>';
	echo '<th>Цена</th>';
	echo '<th>IP</th>';
	echo '<th></th>';
	echo '<th></th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_packet` WHERE `status`='0' ORDER BY `id` ASC");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
		echo '<td>'.$row["id"].'</td>';
		echo '<td>'.$row["merch_tran_id"].'</td>';
		echo '<td>'.$row["wmid"].'</td>';
		echo '<td>'.$row["username"].'</td>';
		echo '<td>'.$system_pay[$row["method_pay"]].'</td>';
		echo '<td>'.DATE("d.m.Y H:i",$row["date"]).'</td>';
		echo '<td>'.$row["packet"].'</td>';
		echo '<td>'.$row["money"].' руб.</td>';
		echo '<td>'.$row["ip"].'</td>';

		echo '<td>';
			echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&option=add">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Добавить" class="sub-green">';
			echo '</form>';
		echo '</td>';
		echo '<td>';
			echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&option=dell">';
				echo '<input type="hidden" name="id" value="'.$row["id"].'">';
				echo '<input type="submit" value="Удалить" class="sub-red">';
			echo '</form>';
		echo '</td>';
		echo '</tr>';
	}
}
echo '</table>';
?>