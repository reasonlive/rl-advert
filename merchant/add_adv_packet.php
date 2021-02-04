<?php
if(isset($merch_tran_id) && $merch_tran_id>0) { }else{ exit("ERROR"); }

$sql_id = $mysqli->query("SELECT * FROM `tb_ads_packet` WHERE `status`='1' AND `merch_tran_id`='$merch_tran_id'");
if($sql_id->num_rows>0) {
	$row = $sql_id->fetch_assoc();

	$username = $row["username"];
	$wmid_user = $row["wmid"];
	$packet = $row["packet"];
	$ip = $row["ip"];
	$method_pay = "-1";
	$money_pay = $row["money"];

	$sql_p = $mysqli->query("SELECT * FROM `tb_config_packet` WHERE `packet`='$packet'");
	if($sql_p->num_rows>0) {
		$row_p = $sql_p->fetch_assoc();
		$ds_plan = $row_p["ds_plan"];
		$ds_timer = $row_p["ds_timer"];
		$slink_plan = $row_p["slink_plan"];
		$sban468_plan = $row_p["sban468_plan"];
		$sban100_plan = $row_p["sban100_plan"];
		$sban200_plan = $row_p["sban200_plan"];
		$psdlink_plan = $row_p["psdlink_plan"];

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

		$mysqli->query("DELETE FROM `tb_ads_packet` WHERE `status`='1' AND `merch_tran_id`='$merch_tran_id'");
	}else{
		exit();
	}
}

?>