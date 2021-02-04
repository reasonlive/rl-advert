<?php
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);

function CheckWalletIdAC($walletId) {
	require(ROOT_DIR."/merchant/advcash/advcash_config.php");
	require_once(ROOT_DIR."/api/advcash/advcash_class.php");

	$merchantWebService = new MerchantWebService();
	$arg0 = new authDTO();
	$arg0->apiName = $ac_api_name;
	$arg0->accountEmail = $ac_account_email;
	$arg0->authenticationToken = $merchantWebService->getAuthenticationToken($ac_api_skey);

	$arg1 = new validateAccountRequestDTO();
	$arg1->walletId = $walletId;
	$arg1->firstName = '';
	$arg1->lastName = '';

	$validateAccount = new validateAccount();
	$validateAccount->arg0 = $arg0;
	$validateAccount->arg1 = $arg1;

	try {
		$validateAccountsResponse = $merchantWebService->validateAccount($validateAccount);
		$ac_purse_check = isset($validateAccountsResponse->return->walletId) ? $validateAccountsResponse->return->walletId : false;
	} catch (Exception $e) {
		$ac_purse_check = $e->getMessage();
	}
	return $ac_purse_check;
}


function SendMoneyAC($money_pay, $walletId, $desc) {
	require(ROOT_DIR."/merchant/advcash/advcash_config.php");
	require_once(ROOT_DIR."/api/advcash/advcash_class.php");

	$merchantWebService = new MerchantWebService();
	$arg0 = new authDTO();
	$arg0->apiName = $ac_api_name;
	$arg0->accountEmail = $ac_account_email;
	$arg0->authenticationToken = $merchantWebService->getAuthenticationToken($ac_api_skey);

	$arg1 = new sendMoneyRequest();
	$arg1->amount = $money_pay;
	$arg1->currency = $ac_api_curr;
	$arg1->walletId = $walletId;
	$arg1->note = iconv("CP1251", "UTF-8", $desc);
	$arg1->savePaymentTemplate = false;

	$validationSendMoney = new validationSendMoney();
	$validationSendMoney->arg0 = $arg0;
	$validationSendMoney->arg1 = $arg1;

	$sendMoney = new sendMoney();
	$sendMoney->arg0 = $arg0;
	$sendMoney->arg1 = $arg1;

	try {
		$merchantWebService->validationSendMoney($validationSendMoney);
		$sendMoneyResponse = $merchantWebService->sendMoney($sendMoney);
		$ac_tran_id = isset($sendMoneyResponse->return) ? trim($sendMoneyResponse->return) : false;
		$SendMoneyStatus = (isset($ac_tran_id) && preg_match("|^[0-9a-fA-F]{32}$|", strtolower(str_ireplace("-", "", $ac_tran_id)))) ? strtolower(str_ireplace("-", "", $ac_tran_id)) : false;
	} catch (Exception $e) {
		$SendMoneyStatus = $e->getMessage();
	}
	return preg_match("|^[0-9a-fA-F]{32}$|", $SendMoneyStatus) ? "SUCCESS" : $SendMoneyStatus;
}

?>