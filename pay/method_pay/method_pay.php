<?php
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);

if($method_pay==1) {
	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" name="pay" method="post" action="https://merchant.webmoney.ru/lmi/payment.asp">';
			echo '<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="'.round($money_add,2).'">';
			echo '<input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="'.base64_encode(iconv("CP1251", "UTF-8", $inv_desc)).'">';
			echo '<input type="hidden" name="LMI_PAYMENT_NO" value="'.$merch_tran_id.'">';
			echo '<input type="hidden" name="LMI_PAYEE_PURSE" value="'.$site_wmr.'">';
			echo '<input type="hidden" name="shp_item" value="'.$shp_item.'">';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay==2) {
	require_once(ROOT_DIR."/merchant/robokassa/rk_config.php");
	$crc  = md5("$mrh_login:".floatval($money_add).":$merch_tran_id:$mrh_pass1:Shp_item=$shp_item");

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" action="https://merchant.roboxchange.com/Index.aspx" method="POST">';
			echo '<input type="hidden" name="MrchLogin" value="'.$mrh_login.'">';
			echo '<input type="hidden" name="OutSum" value="'.floatval($money_add).'">';
			echo '<input type="hidden" name="InvId" value="'.$merch_tran_id.'">';
			echo '<input type="hidden" name="Desc" value="'.$inv_desc.'">';
			echo '<input type="hidden" name="SignatureValue" value="'.$crc.'">';
			echo '<input type="hidden" name="Shp_item" value="'.$shp_item.'">';
			echo '<input type="hidden" name="IncCurrLabel" value="'.$in_curr.'">';
			echo '<input type="hidden" name="Culture" value="'.$culture.'">';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay==3) {
	require_once(ROOT_DIR."/merchant/walletone/w1_config.php");

	if(!isset($money_add_w1)) {
		echo '<span class="msg-error">Не определена сумма к оплате!</span>';
	}else{
		$F_W1_VAL = "";
		$F_W1 = array();
		$F_W1["WMI_MERCHANT_ID"]    = $WMI_MERCHANT_ID;
		$F_W1["WMI_PAYMENT_AMOUNT"] = number_format($money_add_w1, 2, ".", "");
		$F_W1["WMI_AUTO_LOCATION"]  = $WMI_AUTO_LOCATION;
		$F_W1["WMI_CURRENCY_ID"]    = $WMI_CURRENCY_ID;
		$F_W1["WMI_PAYMENT_NO"]     = $merch_tran_id;
		$F_W1["WMI_DESCRIPTION"]    = "BASE64:".base64_encode(iconv("CP1251", "UTF-8", $inv_desc));
		$F_W1["WMI_SUCCESS_URL"]    = $WMI_SUCCESS_URL;
		$F_W1["WMI_FAIL_URL"]       = $WMI_FAIL_URL;
		$F_W1["WMI_CULTURE_ID"]     = $WMI_CULTURE_ID;
		$F_W1["WMI_EXPIRED_DATE"]   = gmdate("Y-m-dTH:i:s", (time()+24*60*60));
		$F_W1["SHP_ITEM"]           = $shp_item;
		foreach($F_W1 as $key => $val) {if(is_array($val)) {usort($val, "strcasecmp"); $F_W1[$key] = $val;}}
		uksort($F_W1, "strcasecmp");
		foreach($F_W1 as $val) {if(is_array($val)) {foreach($val as $v) {$F_W1_VAL.=iconv("utf-8", "windows-1251", $v);}}else{$F_W1_VAL.=iconv("utf-8", "windows-1251", $val);}}
		$SIGNATURE = base64_encode(pack("H*", md5($F_W1_VAL . $WMI_SECRET_KEY)));
		$F_W1["WMI_SIGNATURE"] = $SIGNATURE;

		echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
			echo '<form id="newform" method="POST" action="https://wl.walletone.com/checkout/checkout/Index">';
			foreach($F_W1 as $key => $val) {
				if(is_array($val)) {
					foreach($val as $value) {echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";}
				}else{
					echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />'."\n";
				}
	 		}
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">'."\n";
			echo '</form>';
		echo '</td></tr></table>';
	}


}elseif($method_pay == 4){
	require_once(ROOT_DIR."/merchant/interkassa/ik_config.php");
	$ik_sign = "$money_add:$ik_shop_id:$ik_cur:$inv_desc_en:$ik_enc:$ik_loc:$merch_tran_id:$shp_item:$secret_key_ik";
	$ik_sign = md5($ik_sign, true);
	$ik_sign = base64_encode($ik_sign);

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" name="payment" method="post" action="https://sci.interkassa.com/" enctype="utf-8">';
			echo '<input type="hidden" name="ik_am" value="'.$money_add.'" />';
			echo '<input type="hidden" name="ik_co_id" value="'.$ik_shop_id.'" />';
			echo '<input type="hidden" name="ik_cur" value="'.$ik_cur.'" />';
			echo '<input type="hidden" name="ik_desc" value="'.$inv_desc_en.'" />';
			echo '<input type="hidden" name="ik_enc" value="'.$ik_enc.'" />';
			//echo '<input type="hidden" name="ik_exp" value="'.$ik_exp.'" />';
			echo '<input type="hidden" name="ik_loc" value="'.$ik_loc.'" />';
			echo '<input type="hidden" name="ik_pm_no" value="'.$merch_tran_id.'" />';
			echo '<input type="hidden" name="ik_x_shp_item" value="'.$shp_item.'" />';
			echo '<input type="hidden" name="ik_sign" value="'.$ik_sign.'" />';
			echo '<input type="submit" name="process" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay == 5){
	require_once(ROOT_DIR."/merchant/payeer/payeer_config.php");
	$m_desc = base64_encode($inv_desc_utf8);
	$arHash = array($m_shop,$shp_item.":".$merch_tran_id,$money_add,$m_curr,$m_desc,$m_key);
	$sign = strtoupper(hash('sha256', implode(":", $arHash)));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" method="POST" action="https://payeer.com/api/merchant/m.php">';
			echo '<input type="hidden" name="m_shop" value="'.$m_shop.'">';
			echo '<input type="hidden" name="m_orderid" value="'.$shp_item.':'.$merch_tran_id.'">';
			echo '<input type="hidden" name="m_amount" value="'.$money_add.'">';
			echo '<input type="hidden" name="m_curr" value="'.$m_curr.'">';
			echo '<input type="hidden" name="m_desc" value="'.$m_desc.'">';
			echo '<input type="hidden" name="m_sign" value="'.$sign.'">';
			echo '<input type="submit" name="m_process" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay == 6) {
	require_once(ROOT_DIR."/merchant/qiwi/qw_config.php");

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" action="https://w.qiwi.com/order/external/create.action" accept-charset="windows-1251" method="post">';
			echo '<input type="hidden" name="from" value="'.HTTP_LOGIN_QW.'" />';
			echo '<input type="hidden" name="to" value="" />';
			echo '<input type="hidden" name="summ" value="'.$money_add.'" />';
			echo '<input type="hidden" name="currency" value="RUB" />';
			echo '<input type="hidden" name="iframe" value="false" />';
			echo '<input type="hidden" name="com" value="'.$inv_desc_en.'" />';
			echo '<input type="hidden" name="txn_id" value="'.$shp_item.':'.$merch_tran_id.'" />';
			echo '<input type="hidden" name="lifetime" value="24" />';
			echo '<input type="hidden" name="check_agt" value="true" />';
			echo '<input type="hidden" name="target" value="" />';
			echo '<input type="hidden" name="successUrl" value="https://'.$_SERVER["HTTP_HOST"].'/payok.php" />';
			echo '<input type="hidden" name="failUrl" value="https://'.$_SERVER["HTTP_HOST"].'/payfail.php" />';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay == 7){	
	require_once(ROOT_DIR."/merchant/perfectmoney/pm_config.php");
	if(!isset($money_add_usd)) {
		@require_once(ROOT_DIR."/curs/curs.php");
		$money_add_usd = number_format(round(($money_add/$CURS_USD),2),2,".","");
	}
	$V2_HASH = strtoupper(md5(HTTP_HOST.':'.PM_MEMBER_USD.':'.$money_add_usd.':'.PAYMENT_UNITS.':1::'.ALTERNATE_PHRASE_HASH.':'.TIMESTAMPGMT));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" action="https://perfectmoney.is/api/step1.asp" method="POST">';
			echo '<input type="hidden" name="PAYEE_ACCOUNT" value="'.PM_MEMBER_USD.'" />';
			echo '<input type="hidden" name="PAYEE_NAME" value="'.HTTP_HOST.'" />';
			echo '<input type="hidden" name="PAYMENT_ID" value="'.HTTP_HOST.'" />';
			echo '<input type="hidden" name="PAYMENT_AMOUNT" value="'.$money_add_usd.'" />';
			echo '<input type="hidden" name="PAYMENT_UNITS" value="'.PAYMENT_UNITS.'" />';
			echo '<input type="hidden" name="STATUS_URL" value="https://'.HTTP_HOST.'/merchant/perfectmoney/pm_payresult.php" />';
			echo '<input type="hidden" name="PAYMENT_URL" value="https://'.HTTP_HOST.'/payok.php" />';
			echo '<input type="hidden" name="NOPAYMENT_URL" value="https://'.HTTP_HOST.'/payfail.php" />';
			echo '<input type="hidden" name="SUGGESTED_MEMO" value="'.$inv_desc_en.'" />';
			echo '<input type="hidden" name="PAYMENT_BATCH_NUM" value="1" />';
			echo '<input type="hidden" name="BAGGAGE_FIELDS" value="ORDER_NUM CUST_NUM" />';
			echo '<input type="hidden" name="ORDER_NUM" value="'.$merch_tran_id.'" />';
			echo '<input type="hidden" name="CUST_NUM" value="'.$shp_item.'" />';
			echo '<input type="hidden" name="V2_HASH" value="'.$V2_HASH.'" />';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay == 8){
	require_once(ROOT_DIR."/merchant/yandexmoney/ym_config.php");

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" name="payform" method="post" action="https://money.yandex.ru/quickpay/confirm.xml">';
			echo '<input type="hidden" name="receiver" value="'.CLIENT_PURSE.'" />';
			echo '<input type="hidden" name="formcomment" value="'.$inv_desc_utf8.'" />';
			echo '<input type="hidden" name="short-dest" value="'.$inv_desc_utf8.'" />';
			echo '<input type="hidden" name="writable-targets" value="false" />';
			echo '<input type="hidden" name="comment-needed" value="false" />';
			echo '<input type="hidden" name="label" value="'.$shp_item.':'.$merch_tran_id.'" />';
			echo '<input type="hidden" name="quickpay-form" value="shop" />';
			echo '<input type="hidden" name="targets" value="'.$inv_desc.'" />';
			echo '<input type="hidden" name="sum" value="'.$money_add_ym.'" data-type="number" />';
			echo '<input type="hidden" name="fio" value="0" />';
			echo '<input type="hidden" name="mail" value="0" />';
			echo '<input type="hidden" name="phone" value="0" />';
			echo '<input type="hidden" name="address" value="0" />';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay==9) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/megakassa/rk_config.php");
	$client_email = "";
	$order_id = "$shp_item:$merch_tran_id";
	$desc = $inv_desc_utf8;
	$crc = md5($secret_key.md5(join(':', array($shop_id, floatval($money_add), $currency, $desc, $order_id, $method_id, $client_email, $debug, $secret_key))));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" method="POST" action="https://megakassa.ru/merchant/">';
			echo '<input type="hidden" name="shop_id" 	value="'.$shop_id.'">';
			echo '<input type="hidden" name="amount" 	value="'.floatval($money_add).'">';
			echo '<input type="hidden" name="currency" 	value="'.$currency.'">';
			echo '<input type="hidden" name="description" 	value="'.$desc.'">';
			echo '<input type="hidden" name="order_id" 	value="'.$order_id.'">';
			echo '<input type="hidden" name="method_id" 	value="'.$method_id.'">';
			echo '<input type="hidden" name="client_email" 	value="'.$client_email.'">';
			echo '<input type="hidden" name="debug" 	value="'.$debug.'">';
			echo '<input type="hidden" name="language" 	value="'.$culture.'">';
			echo '<input type="hidden" name="signature" 	value="'.$crc.'">';
			echo '<input type="submit" class="sub-blue160" 	value="Оплатить" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay==10) {
	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" action="" method="post">';
			echo '<input type="hidden" name="id_pay" value="'.$id_zakaz.'">';
			echo '<input type="hidden" name="method_pay" value="'.$method_pay.'">';
			echo '<input type="submit" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';

/*}elseif($method_pay == 22){
	require_once(ROOT_DIR."/merchant/payeer/payeer_config.php");
	$m_desc = base64_encode($inv_desc_utf8);
	$arHash = array($m_shop,$shp_item.":".$merch_tran_id,$money_add,$m_curr,$m_desc,$m_key);
	$sign = strtoupper(hash('sha256', implode(":", $arHash)));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" method="POST" action="https://payeer.com/api/merchant/">';
			echo '<input type="hidden" name="m_shop" value="'.$m_shop.'">';
			echo '<input type="hidden" name="m_orderid" value="'.$shp_item.':'.$merch_tran_id.'">';
			echo '<input type="hidden" name="m_amount" value="'.$money_add.'">';
			echo '<input type="hidden" name="form[ps]" value="49398264">';
            echo '<input type="hidden" name="form[curr[49398264]]" value="RUB">';
			echo '<input type="hidden" name="m_curr" value="'.$m_curr.'">';
			echo '<input type="hidden" name="m_desc" value="'.$m_desc.'">';
			echo '<input type="hidden" name="m_sign" value="'.$sign.'">';
			echo '<input type="submit" name="m_process" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';
	*/
}elseif($method_pay==20) {
	require_once(ROOT_DIR."/merchant/freekassa/fk_config.php");
	$fk_order_amount = number_format($money_add, 2, ".", "");
	$fk_order_id = "$shp_item:$merch_tran_id";
	$fk_signature = md5(join(':', array($fk_shop_id, $fk_order_amount, $fk_secretkey_1, $fk_order_id)));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" method="GET" accept-charset="windows-1251" action="http://www.free-kassa.ru/merchant/cash.php">';
			echo '<input type="hidden" name="m" 	value="'.$fk_shop_id.'">';
			echo '<input type="hidden" name="oa" 	value="'.$fk_order_amount.'">';
			echo '<input type="hidden" name="o" 	value="'.$fk_order_id.'">';
			echo '<input type="hidden" name="s" 	value="'.$fk_signature.'">';
			echo '<input type="hidden" name="lang" 	value="'.$fk_lang.'">';
			echo '<input type="submit" name="pay" 	value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';


}elseif($method_pay==21) {
	require_once($_SERVER["DOCUMENT_ROOT"]."/merchant/advcash/advcash_config.php");
	$ac_comments = $inv_desc_utf8;
	$ac_order_id = "$shp_item-$merch_tran_id";
	$ac_amount = "$money_add";
	$hash = array($ac_account_email,$ac_sci_name,$ac_amount,$ac_currency,$secret,$ac_order_id);
	$ac_sign = strtoupper(hash('sha256', implode(":", $hash)));

	echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" method="POST" action="https://wallet.advcash.com/sci/">';
			echo '<input type="hidden" name="ac_account_email" value="'.$ac_account_email.'" />';
			echo '<input type="hidden" name="ac_sci_name" value="'.$ac_sci_name.'" />';
			echo '<input type="hidden" name="ac_amount" value="'.$ac_amount.'">';
			echo '<input type="hidden" name="ac_currency" value="'.$ac_currency.'">';
			echo '<input type="hidden" name="ac_order_id" value="'.$ac_order_id.'">';
			echo '<input type="hidden" name="ac_sign" value="'.$ac_sign.'">';			
			echo '<input type="hidden" name="ac_comments" value="'.$ac_comments.'">';			
			echo '<input type="submit" name="pay" value="Оплатить" class="sub-blue160" style="float:none;">';
		echo '</form>';
	echo '</td></tr></table>';

}elseif ($method_pay==22) {

	//echo '<table class="tables"><tr><td align="center" colspan="2" '.($shp_item==21 ? 'style="border:none; padding:0; margin:0;"' : false).'>';
		echo '<form id="newform" action="" method="post">';
			echo '<input type="hidden" name="id_pay" value="'.$id_zakaz.'">';
			echo '<input type="hidden" name="method_pay" value="'.$method_pay.'">';
			echo '<center><input type="submit" value="Оплатить '.floatval($money_add).' руб. С основного счета" class="btn blue"></center>';
			//echo '<div id="info-msg-cash" style="display: block; margin: 0px 0px 10px; text-align: center;"><input type="submit" value="Оплатить" class="cash-order-btn" style="float:none;"></div>';
		echo '</form>';
	//echo '</td></tr></table>';

}else{
	echo '<span class="msg-error">Не выбран способ оплаты!</span>';
}

?>