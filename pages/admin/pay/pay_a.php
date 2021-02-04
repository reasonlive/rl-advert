<?php
//if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Автовыплаты</b></h3>';

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis' AND `howmany`='1'");
$pay_comis1 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis' AND `howmany`='2'");
$pay_comis2 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis_lp' AND `howmany`='1'");
$pay_comis1_lp = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='pay_comis_lp' AND `howmany`='2'");
$pay_comis2_lp = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='pm'");
$pay_comis_pm = $sql->fetch_object()->price;
		
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='qw'");
$pay_comis_qw = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='ym'");
$pay_comis_ym = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='pe'");
$pay_comis_pe = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='ac'");
$pay_comis_ac = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='me'");
$pay_comis_me = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='vs'");
$pay_comis_vs = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='ms'");
$pay_comis_ms = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='be'");
$pay_comis_be = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='mt'");
$pay_comis_mt = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='mg'");
$pay_comis_mg = $sql->fetch_object()->price;
			
$sql = $mysqli->query("SELECT `price` FROM `tb_config_pay` WHERE `item`='pay_comis' AND `eps`='tl'");
$pay_comis_tl = $sql->fetch_object()->price;
			



if(isset($_GET["option"]) && isset($_POST["id"]) && $_GET["option"]=="auto_pay") {
	$id = (isset($_POST["id"])) ? intval($_POST["id"]) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `id`='$id' AND `status_pay`='0' AND (`method`='WebMoney' OR `method`='Payeer' OR `method`='YandexMoney' OR `method`='Qiwi' OR `method`='AdvCash' OR `method`='MAESTRO' OR `method`='VISA' OR `method`='MasterCard' OR `method`='Beeline' OR `method`='MTS' OR `method`='Megaphone' OR `method`='TELE2') AND `status`='' AND `tipo`='0'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_array();
		$username = $row["user"];
		$money_pay = abs($row["amount"]);
		$method = $row["method"];

		$sql = $mysqli->query("SELECT `tranid` FROM `tb_statistics` WHERE `id`='1'");
		$tranid = $sql->fetch_object()->tranid;

		if($method=="WebMoney") {
			require_once("".$_SERVER['DOCUMENT_ROOT']."/auto_pay_req/wmxml.inc.php");

			$site_w = $mysqli->query("SELECT `sitewmr` FROM `tb_site` WHERE `id`='1'");
			$site_wmr = $site_w->fetch_object()->sitewmr;
			$wmr_users = $row["wmr"];
			$period = "0";
			$pcode = "";
			$desc = "Выплата с $url #$tranid пользователю $username. Благодарим Вас за работу!";
			$wminvid = "0";
			$onlyauth = "1";

			if(abs($row["amount"])>1.25) {
				$summa_topay = p_floor( ((abs($row["amount"]) * (100 - $pay_comis1)/100) - $pay_comis2), 2);
			}else{
				$summa_topay = (abs($row["amount"]) - 0.01 - $pay_comis2);
			}

			$r = _WMXML2($tranid,$site_wmr,$wmr_users,$summa_topay,$period,$pcode,$desc,$wminvid,$onlyauth);
			$kod_result = $r["retval"];
			//echo "Расшифровка - ".$r["retdesc"]."<br>";

			if($kod_result==0) {
				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='WebMoney'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='WebMoney'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('WebMoney','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			}else{
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки '.$kod_result.'.</b><br>';
			}

		}elseif($method=="Payeer") {

			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			//$currency = "RUR";
			$liqpay_users = $row["wmr"];

			if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_pm/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);
			
			$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);

							$AUT_CHECK = $PE_API->isAuth();

							if (isset($AUT_CHECK) && $AUT_CHECK=="1") {
								$PE_TRANSFER = $PE_API->transfer(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','to' => ''.$liqpay_users.'','comment' => 'Payment from '.$url.' #'.$tranid.' to account '.$username.'','anonim' => 'N'));
								$PE_RES = isset($PE_TRANSFER["historyId"]) ? $PE_TRANSFER["historyId"] : false;
								if($code_result = (isset($PE_RES) && intval($PE_RES)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='Payeer'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='Payeer'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('Payeer','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}
			
		}elseif($method=="Qiwi") {

			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			//$currency = "RUR";
			$liqpay_users = '+'.$row["wmr"];
			//$qw_purse='+'.$row_user["qw_purse"];

			if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_qw/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);
			
			$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);
							if ($PE_API->isAuth()){
								$PE_INITOUTPUT = $PE_API->initOutput(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','ps' => '26808','param_ACCOUNT_NUMBER' => ''.$liqpay_users.'','comment' => 'Payment from '.$url.' #'.$tranid.' to account '.$username.''));
								$PE_RES = isset($PE_INITOUTPUT["historyId"]) ? $PE_INITOUTPUT["historyId"] : false;
								$historyId = $PE_API->output();
								if($code_result = (isset($PE_INITOUTPUT) && intval($PE_INITOUTPUT)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='Qiwi'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='Qiwi'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('Qiwi','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}
							
	    }elseif($method=="Beeline") {
			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			
							$liqpay_users = '+'.$row["wmr"];
							
                         if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_be/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);							
                            
							$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);
							if ($PE_API->isAuth()){
								$PE_INITOUTPUT = $PE_API->initOutput(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','ps' => '24898938','param_ACCOUNT_NUMBER' => ''.$liqpay_users.''));
								$PE_RES = isset($PE_INITOUTPUT["historyId"]) ? $PE_INITOUTPUT["historyId"] : false;
								$historyId = $PE_API->output();
								if($code_result = (isset($PE_INITOUTPUT) && intval($PE_INITOUTPUT)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='Beeline'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='Beeline'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('Beeline','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}
							
						}elseif($method=="MTS") {
			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			
							$liqpay_users = '+'.$row["wmr"];
							
                         if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_mt/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);
			 
							$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);
							if ($PE_API->isAuth()){
								$PE_INITOUTPUT = $PE_API->initOutput(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','ps' => '24899291','param_ACCOUNT_NUMBER' => ''.$liqpay_users.''));
								$PE_RES = isset($PE_INITOUTPUT["historyId"]) ? $PE_INITOUTPUT["historyId"] : false;
								$historyId = $PE_API->output();
								if($code_result = (isset($PE_INITOUTPUT) && intval($PE_INITOUTPUT)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='MTS'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='MTS'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('MTS','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}	
							
						}elseif($method=="Megaphone") {
			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			
							$liqpay_users = '+'.$row["wmr"];
							
                         if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_mg/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);
			 
							$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);
							if ($PE_API->isAuth()){
								$PE_INITOUTPUT = $PE_API->initOutput(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','ps' => '24899391','param_ACCOUNT_NUMBER' => ''.$liqpay_users.''));
								$PE_RES = isset($PE_INITOUTPUT["historyId"]) ? $PE_INITOUTPUT["historyId"] : false;
								$historyId = $PE_API->output();
								if($code_result = (isset($PE_INITOUTPUT) && intval($PE_INITOUTPUT)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='Megaphone'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='Megaphone'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('Megaphone','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}
							
						}elseif($method=="TELE2") {
			require(ROOT_DIR."/merchant/payeer/cpayeer.php");
            require(ROOT_DIR."/merchant/payeer/payeer_config.php");
			
							$liqpay_users = '+'.$row["wmr"];
							
                         if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_tl/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);
			 
							$PE_API = new CPayeer($accountNumber, $apiId, $apiKey);
							if ($PE_API->isAuth()){
								$PE_INITOUTPUT = $PE_API->initOutput(array('curIn' => 'RUB','sumOut' => ''.floatval($summa_topay).'','curOut' => 'RUB','ps' => '95877310','param_ACCOUNT_NUMBER' => ''.$liqpay_users.''));
								$PE_RES = isset($PE_INITOUTPUT["historyId"]) ? $PE_INITOUTPUT["historyId"] : false;
								$historyId = $PE_API->output();
								if($code_result = (isset($PE_INITOUTPUT) && intval($PE_INITOUTPUT)>0)){

				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='TELE2'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='TELE2'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('TELE2','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			
			}elseif($code_result = "ERROR: ID PAY NOT FOUND"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							}
		}elseif($method=="AdvCash") {
							require(ROOT_DIR."/api/advcash/advcash_func.php");
							
							$liqpay_users = $row["wmr"];
							
                         if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_ac/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);

							$desc = "Выплата с $url #$tranid (Логин:$username). Благодарим Вас за работу!";
							$AC_API = SendMoneyAC(floatval($summa_topay), $liqpay_users, $desc);
							$AC_RES = (isset($AC_API) && strtoupper($AC_API)=="SUCCESS") ? "OK" : $AC_API;

							if($AC_RES == "OK") {
								$code_result = (isset($AC_API) && strtoupper($AC_API)=="SUCCESS") ? "SUCCESS" : $AC_API;

								$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+1 WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='AdvCash'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='AdvCash'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('AdvCash','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на '.$method.' #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
							}elseif($code_result ="$AC_RES"){
				//$code_result = "ERROR: ID PAY NOT FOUND";
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
							

		}elseif($method=="YandexMoney") {
			$ymoney_users = $row["wmr"];
			
			if(abs($row["amount"])>1.25) {
				$sum_percent = round(($row["amount"] * $pay_comis_ym/100), 2);
				if($sum_percent < 0.01) $sum_percent = 0.01;
			}else{
				$sum_percent = 0;
			}
			
			 $summa_topay = p_floor(($row["amount"] - $sum_percent), 2);

			require(ROOT_DIR."/merchant/yandexmoney/ym_config.php");
            require(ROOT_DIR."/merchant/yandexmoney/ym_outresult.php");
			$comment = iconv("Windows-1251", "UTF-8", "Выплата с $url #$tranid пользователю $username. Благодарим Вас за работу!");
							$YM_API = new ymAPI(CLIENT_ID, REDIRECT_URL);
							$YM_SEND = $YM_API->requestPaymentP2P(TOKEN_YM, $ymoney_users, $summa_topay, $comment, $comment);
							$YM_RES = isset($YM_SEND["request_id"]) ? $YM_API->processPayment(TOKEN_YM, $YM_SEND["request_id"]) : array("status" => "ERROR_YM_1");
							if($code_result = (isset($YM_RES["status"]) && strtolower($YM_RES["status"]) == "success")){
				
				$mysqli->query("UPDATE `tb_statistics` SET `tranid`=`tranid`+'1' WHERE `id`='1'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
				
				$sql_stat_pay = $mysqli->query("SELECT * FROM `tb_pay_stat` WHERE `type`='YandexMoney'");
	               if($sql_stat_pay->num_rows>0) {
		               $mysqli->query("UPDATE `tb_pay_stat` SET `".strtolower(DATE("D"))."`=`".strtolower(DATE("D"))."`+'$money_pay' WHERE `type`='YandexMoney'") or die($mysqli->error);
				    }else{
					   $mysqli->query("INSERT INTO `tb_pay_stat` (`type`,`".strtolower(DATE("D"))."`) VALUES('YandexMoney','$money_pay')") or die($mysqli->error);
				 }

				echo '<br><b style="color:#2E8B57">Автовыплата на Яндекс.Деньги #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
			}elseif($code_result = (isset($YM_RES["status"]) ? $YM_RES["status"] : "ERROR_YM_2")){
				echo '<br><b style="color:#FF0000">Ошибка выплаты! Код ошибки: '.$code_result.'</b><br>';
			}
		}
	}else{
		echo '<br><b style="color:#FF0000">Ошибка! Выплаты с таким id #'.$id.' нет, либо выплата уже была сделана!</b><br>';
	}
}

if(isset($_GET["option"]) && isset($_POST["id"]) && $_GET["option"]=="pay_r") {
	$id = (isset($_POST["id"])) ? intval($_POST["id"]) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `id`='$id' AND `status_pay`='0' AND (`method`='WebMoney' OR `method`='Payeer' OR `method`='YandexMoney' OR `method`='Qiwi' OR `method`='AdvCash' OR `method`='MAESTRO' OR `method`='VISA' OR `method`='MasterCard' OR `method`='Beeline' OR `method`='MTS' OR `method`='Megaphone') AND `status`='' AND `tipo`='0'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_array();
		$username = $row["user"];
		$money_pay = abs($row["amount"]);

		$mysqli->query("UPDATE `tb_history` SET `status_pay`='1', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_users` SET `money_out`=`money_out`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_statistics` SET `viplat`=`viplat`+'1', `sumpay`=`sumpay`+'$money_pay' WHERE `id`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_history_pay` SET `status_pay`='0', `time`='".time()."' WHERE `user`='$username'") or die($mysqli->error);

//		echo '<br><b style="color:#2E8B57">Выплата #'.$tranid.' успешно выполнена пользователю '.$username.'</b><br>';
	}else{
		echo '<br><b style="color:#FF0000">Ошибка! Выплаты с таким id #'.$id.' нет, либо выплата уже была сделана (или отменена)!</b><br>';
	}
}

if(isset($_GET["option"]) && isset($_POST["id"]) && $_GET["option"]=="dell_pay") {
	$id = (isset($_POST["id"])) ? intval($_POST["id"]) : false;

	$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `id`='$id' AND `status_pay`='0' AND (`method`='WebMoney' OR `method`='Payeer' OR `method`='YandexMoney' OR `method`='Qiwi' OR `method`='AdvCash' OR `method`='MAESTRO' OR `method`='VISA' OR `method`='MasterCard' OR `method`='Beeline' OR `method`='MTS' OR `method`='Megaphone' OR `method`='TELE2') AND `status`='' AND `tipo`='0'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_array();
		$username = $row["user"];
		$money_pay = abs($row["amount"]);

		$mysqli->query("UPDATE `tb_users` SET `money`=`money`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_history` SET `status_pay`='2', `date`='".DATE("d.m.Y H:i",time())."',`time`='".time()."' WHERE `id`='$id' AND `status_pay`='0'") or die($mysqli->error);
		
		//$sql_history_pay = $mysqli->query("SELECT * FROM `tb_history_pay` WHERE `user`='$username'");
	               //if(mysql_num_rows($sql_history_pay)>0) {
		$mysqli->query("UPDATE `tb_history_pay` SET `status_pay`='0' ,`time`='0' WHERE `user`='$username'") or die($mysqli->error);
				    //}else{
					   //$mysqli->query("INSERT INTO `tb_history_pay` (`user`,`status_pay`,`time`) VALUES('$username','0','0')") or die($mysqli->error);
				 //}

		echo '<br><b style="color:#2E8B57">Выпплата пользователю '.$username.' отменена, деньги возвращены на баланс аккаунта!</b><br>';
	}else{
		echo '<br><b style="color:#FF0000">Ошибка! Выплаты с таким id #'.$id.' нет, либо выплата уже была сделана (или отменена)!</b><br>';
	}
}

$sql = $mysqli->query("SELECT * FROM `tb_history` WHERE `status_pay`='0' AND (`method`='WebMoney' OR `method`='Payeer' OR `method`='YandexMoney' OR `method`='Qiwi' OR `method`='AdvCash' OR `method`='MAESTRO' OR `method`='VISA' OR `method`='MasterCard' OR `method`='Beeline' OR `method`='MTS' OR `method`='Megaphone' OR `method`='TELE2') AND `status`='' AND `tipo`='0' ORDER BY `id` ASC");
$kol = $sql->num_rows;
if($kol>0) {
	$sql_s = $mysqli->query("SELECT sum(amount) FROM `tb_history` WHERE `status_pay`='0' AND (`method`='WebMoney' OR `method`='Payeer' OR `method`='YandexMoney' OR `method`='Qiwi' OR `method`='AdvCash' OR `method`='MAESTRO' OR `method`='VISA' OR `method`='MasterCard' OR `method`='Beeline' OR `method`='MTS' OR `method`='Megaphone' OR `method`='TELE2') AND `status`='' AND `tipo`='0'");
	$all_count1 = $sql_s->fetch_array();
 $sumpay_wait = $all_count1['0'];

	echo '<br><b>Всего:</b> <b style="color:#FF0000;">'.$kol.'</b> на сумму <b style="color:#FF0000;">'.p_floor($sumpay_wait, 2).'</b> руб.';
}

echo '<table width="100%" border="1" style="border-collapse: collapse;">';
echo '<tr bgcolor="#CCC">';
echo '<th>Номер счета дата</th>';
echo '<th>Объем оплаты</th>';
echo '<th>Примечание</th>';
echo '<th>№</th>';
echo '<th>Оплачено</th>';
echo '<th>Автовыплата</th>';
echo '<th>Отмена</th>';
echo '</tr>';

$sum_m = 0;
if($kol>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';

		echo '<td><span style="color:blue;"><b>'.$row["method"].'</b></span> : '.$row["wmr"].';<br/>'.$row["date"].';</td>';

		if(abs($row["amount"])>1.25 && $row["method"]!="YandexMoney") {
			echo '<td>'.p_floor( ((abs($row["amount"]) * (100 - $pay_comis1)/100) - $pay_comis2), 2).';</td>';
		}elseif($row["method"]=="YandexMoney") {
			echo '<td>'.abs($row["amount"]).';</td>';
		}else{
			echo '<td>'.(abs($row["amount"]) - 0.01 - $pay_comis2).';</td>';
		}

		echo '<td>Выплата с '.$url.' пользователю - '.$row["user"].'. Благодарим Вас за работу!;</td>';
		echo '<td>'.$row["tran_id"].'</td>';
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'?op='.intval($_GET["op"]).'&amp;option=pay_r"><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="sub-green" value="Оплачено"></form></td>';
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'?op='.intval($_GET["op"]).'&amp;option=auto_pay"><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="sub-blue" value="Выплатить"></form></td>';
		echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'?op='.intval($_GET["op"]).'&amp;option=dell_pay"><input type="hidden" name="id" value="'.$row["id"].'"><input type="submit" class="sub-red" value="Отменить заказ"></form></td>';
		echo '</tr>';
	}
	echo '</table>';
}else{
	echo '<tr><td colspan="7"><div align="center" style="color:#FF0000; font-weight:bold;">Выплат в ожидании нет.</div></td></tr>';
	echo '</table>';
}
?>
