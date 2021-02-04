<?php
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);

require(ROOT_DIR."/config.php");
$sql = $mysqli->query("SELECT `sitewmid` FROM `tb_site` WHERE `id`='1'");
$site_wmid = $sql->fetch_object()->sitewmid;

$global_wmid = $site_wmid;
$global_pas = "пароль";
$global_kwm = is_file(ROOT_DIR."/auto_pay_req/keys/$global_wmid.kwm") ? file_get_contents(ROOT_DIR."/auto_pay_req/keys/".$global_wmid.".kwm") : false;
$Path_Certs = is_file(ROOT_DIR."/auto_pay_req/cert/WMunited.cer") ? ROOT_DIR."/auto_pay_req/cert/WMunited.cer" : false;

if($global_wmid == false) {
	echo '<span class="msg-error">Error: WMID for site not set.</span>';
	include(ROOT_DIR."/footer.php");
	exit();

}elseif($global_pas == false) {
	echo '<span class="msg-error">Error: password for key not fountd.</span>';
	include(ROOT_DIR."/footer.php");
	exit();

}elseif($global_kwm == false) {
	echo '<span class="msg-error">Error: kwm file not fountd.</span>';
	include(ROOT_DIR."/footer.php");
	exit();

}elseif($Path_Certs == false) {
	echo '<span class="msg-error">Error: WMunited.cer file not fountd.</span>';
	include(ROOT_DIR."/footer.php");
	exit();
}

include_once(ROOT_DIR."/auto_pay_req/wmsigner.php");

// URL интерфейсов
$XML_addr[1]="https://w3s.webmoney.ru/asp/XMLInvoice.asp";
$XML_addr[2]="https://w3s.webmoney.ru/asp/XMLTrans.asp";
$XML_addr[3]="https://w3s.webmoney.ru/asp/XMLOperations.asp";
$XML_addr[4]="https://w3s.webmoney.ru/asp/XMLOutInvoices.asp";
$XML_addr[5]="https://w3s.webmoney.ru/asp/XMLFinishProtect.asp";
$XML_addr[6]="https://w3s.webmoney.ru/asp/XMLSendMsg.asp";
$XML_addr[7]="https://w3s.webmoney.ru/asp/XMLClassicAuth.asp";
$XML_addr[8]="https://w3s.webmoney.ru/asp/XMLFindWMPurseNew.asp";
$XML_addr[9]="https://w3s.webmoney.ru/asp/XMLPurses.asp";
$XML_addr[10]="https://w3s.webmoney.ru/asp/XMLInInvoices.asp";
$XML_addr[11]="https://passport.webmoney.ru/asp/XMLGetWMPassport.asp";
$XML_addr[13]="https://w3s.webmoney.ru/asp/XMLRejectProtect.asp";
$XML_addr[14]="https://w3s.webmoney.ru/asp/XMLTransMoneyback.asp";
$XML_addr[151]="https://w3s.webmoney.ru/asp/XMLTrustList.asp";
$XML_addr[152]="https://w3s.webmoney.ru/asp/XMLTrustList2.asp";
$XML_addr[153]="https://w3s.webmoney.ru/asp/XMLTrustSave2.asp";
$XML_addr[16]="https://w3s.webmoney.ru/asp/XMLCreatePurse.asp";
$XML_addr[171]="https://arbitrage.webmoney.ru/xml/X17_CreateContract.aspx";
$XML_addr[172]="https://arbitrage.webmoney.ru/xml/X17_GetContractInfo.aspx";
$XML_addr[18]="https://merchant.webmoney.ru/conf/xml/XMLTransGet.asp";
$XML_addr[19]="https://passport.webmoney.ru/XML/XMLCheckUser.aspx";

function _GetReqn(){
    $time=microtime();
    $int=substr($time,11);
    $flo=substr($time,2,5);
    return $int.$flo;
};

function _GetAnswer($address, $xml){
	global $Path_Certs;
	$ch = curl_init($address);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	curl_setopt($ch, CURLOPT_CAINFO, $Path_Certs);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
	$result=curl_exec($ch);
	return $result;
}


// ИНТЕРФЕЙС X1. ВЫПИСКА СЧЕТА.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'date'=>дата и время, 'wminvid'=>уникальный номер счета]
function _WMXML1 ($orderid,$wmid,$purse,$amount,$desc,$address,$period,$expiration) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$desc=trim($desc); $address=trim($address); $amount=floatval($amount);
	$data = ($orderid.$wmid.$purse.$amount.$desc.$address.$period.$expiration.$reqn);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$address=htmlspecialchars($address, ENT_QUOTES);
	$desc=htmlspecialchars($desc, ENT_QUOTES);
	$address=iconv("CP1251", "UTF-8", $address);
	$desc=iconv("CP1251", "UTF-8", $desc);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<invoice>
			<orderid>$orderid</orderid>
			<customerwmid>$wmid</customerwmid>
			<storepurse>$purse</storepurse>
			<amount>$amount</amount>
			<desc>$desc</desc>
			<address>$address</address>
			<period>$period</period>
			<expiration>$expiration</expiration>
		</invoice>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[1], $xml);
	//echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['date']=strval($xmlres->invoice->datecrt);
	$result['wminvid']=strval($xmlres->invoice->attributes()->id);
	return $result;
}


// ИНТЕРФЕЙС X2. ОТПРАВКА ПЕРЕВОДА.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'date'=>дата и время]
function _WMXML2 ($tranid,$purse,$rpurse,$amount,$period,$pcode,$desc,$wminvid,$onlyauth) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$desc=trim($desc); $pcode=trim($pcode); $amount=floatval($amount);
	$data = ($reqn.$tranid.$purse.$rpurse.$amount.$period.$pcode.$desc.$wminvid);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$pcode=htmlspecialchars($pcode, ENT_QUOTES);
	$desc=htmlspecialchars($desc, ENT_QUOTES);
	$pcode=iconv("CP1251", "UTF-8", $pcode);
	$desc=iconv("CP1251", "UTF-8", $desc);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<trans>
			<tranid>$tranid</tranid>
			<pursesrc>$purse</pursesrc>
			<pursedest>$rpurse</pursedest>
			<amount>$amount</amount>
			<period>$period</period>
			<pcode>$pcode</pcode>
			<desc>$desc</desc>
			<wminvid>$wminvid</wminvid>
			<onlyauth>$onlyauth</onlyauth>
		</trans>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[2], $xml);
	//echo $resxml;
	//echo "<br>reqn: $reqn<br>";
	//echo "Подпись: $rsign<br>";
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['reqn']=strval($xmlres->reqn);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['date']=strval($xmlres->operation->datecrt);
	return $result;
}


// ИНТЕРФЕЙС X3. ПОЛУЧЕНИЕ ИСТОРИИ ОПЕРАЦИЙ.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'cnt'=>количество операций в выборке, 'operations'=>массив с операциями]
function _WMXML3 ($purse,$wmtranid,$tranid,$wminvid,$orderid,$datestart,$datefinish) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($purse.$reqn);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<getoperations>
			<purse>$purse</purse>
			<wmtranid>$wmtranid</wmtranid>
			<tranid>$tranid</tranid>
			<wminvid>$wminvid</wminvid>
			<orderid>$orderid</orderid>
			<datestart>$datestart</datestart>
			<datefinish>$datefinish</datefinish>
		</getoperations>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[3], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['cnt']=strval($xmlres->operations->attributes()->cnt);
	if($result['cnt']>0) {
		// В элементе $result['operations'] формируем элементы, каждый из которых является
		// массивом с параметрами операции
		foreach ($xmlres->operations->operation as $operation) {
			// определяем тип операции (входящая, исходящая)
			// и кошелек корреспондента
			$pursesrc=strval($operation->pursesrc);
			$pursedest=strval($operation->pursedest);
			if($pursesrc==$purse) {
				$type="out"; $corrpurse=$pursedest;
			} elseif($pursedest==$purse) {
				$type="in"; $corrpurse=$pursesrc;
			}
			$result['operations'][strval($operation->attributes()->id)] = Array
				(
				'tranid'=>strval($operation->tranid),
			 	'wminvid'=>strval($operation->wminvid),
				'orderid'=>strval($operation->orderid),
				'type'=>$type,
				'corrpurse'=>$corrpurse,
				'corrwmid'=>strval($operation->corrwm),
				'amount'=>floatval($operation->amount),
				'comiss'=>floatval($operation->comiss),
				'rest'=>floatval($operation->rest),
				'protection'=>strval($operation->opertype),
				'desc'=>iconv("UTF-8", "CP1251", strval($operation->desc)),
				'datecrt'=>strval($operation->datecrt)
				);
		}
	}
	return $result;
}


// ИНТЕРФЕЙС X4. ПРОВЕРКА ВЫПИСАННЫХ СЧЕТОВ.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'cnt'=>количество счетов вошедших в выборку, 'invoices'=>массив со счетами]
function _WMXML4 ($purse,$wminvid,$orderid,$datestart,$datefinish) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($purse.$reqn);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<getoutinvoices>
			<purse>$purse</purse>
			<wminvid>$wminvid</wminvid>
			<orderid>$orderid</orderid>
			<datestart>$datestart</datestart>
			<datefinish>$datefinish</datefinish>
		</getoutinvoices>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[4], $xml);
	//echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['cnt']=strval($xmlres->outinvoices->attributes()->cnt);
	if($result['cnt']>0) {
		// В элементе $result['invoices'] формируем массив [номер счета в WM] = состояние оплаты
		foreach ($xmlres->outinvoices->outinvoice as $invoice) {
			$wminvid=strval($invoice->attributes()->id);
			$state=strval($invoice->state);
			$result['invoices'][$wminvid]=$state;
		}
	}
	return $result;
}


// ИНТЕРФЕЙС X6. ОТПРАВКА СООБЩЕНИЯ.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'date'=>дата и время]
function _WMXML6 ($wmid,$msg,$subj) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn = _GetReqn();
	$msg = trim($msg); $subj = trim($subj);
	$msg=str_replace ("\r", "", $msg);
	$data = ($wmid.$reqn.$msg.$subj);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$msg = htmlspecialchars($msg, ENT_QUOTES, "CP1251");
	$subj = htmlspecialchars($subj, ENT_QUOTES, "CP1251");
	$msg = iconv("CP1251", "UTF-8", $msg);
	$subj = iconv("CP1251", "UTF-8", $subj);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<message>
			<receiverwmid>$wmid</receiverwmid>
			<msgsubj>$subj</msgsubj>
			<msgtext>$msg</msgtext>
		</message>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[6], $xml);
	//echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['date']=strval($xmlres->message->datecrt);
	return $result;
}


// ИНТЕРФЕЙС X7. ПРОВЕРКА ПОДПИСИ
// На входе: WMID, чью подпись нужно проверить; исходная строка; подпись исходной строки
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'res'=>результат проверки (yes\no)]
function _WMXML7 ($wmid,$string,$sign) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$data = ($global_wmid.$wmid.$string.$sign);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
		<wmid>$global_wmid</wmid> 
		<sign>$rsign</sign>
		<testsign>
			<wmid>$wmid</wmid>
			<plan><![CDATA[$string]]></plan>
			<sign>$sign</sign>
		</testsign>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[7], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
	} else {
		$result['retval']=strval($xmlres->retval);
		$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
		$result['res']=strval($xmlres->testsign->res);
	}
	return $result;
}

// ИНТЕРФЕЙС X8. ОПРЕДЕЛЕНИЕ ПРИНАДЛЕЖНОСТИ КОШЕЛЬКА.
// На выходе: массив ['wmid'=>wmid, 'purse'=>кошелек, 'avaliable'=>запрет на входящие переводы, 'newattst'=>аттестат, 'merchant_active_mode'=>включенность кошелька в WM Merchant, 'merchant_allow_cashier'=>включенность опции *прием из терминалов* в WM Merchant, 'messpermiit'=>запрет на входящие сообщения от НЕ корреспондентов, 'invpermit'=>запрет на входящие счета от НЕ коррексондентов, 'paypermit'=>запрет на входящие переводы от НЕ корреспондентов, 'retval'=>код выполнения, 'retdesc'=>описание результата]
function _WMXML8 ($wmid,$purse) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($wmid.$purse);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<testwmpurse>
			<wmid>$wmid</wmid>
			<purse>$purse</purse>
		</testwmpurse>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[8], $xml);
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['wmid']=strval($xmlres->testwmpurse->wmid);
	$result['purse']=strval($xmlres->testwmpurse->purse);
	$result['avaliable']=strval($xmlres->testwmpurse->wmid->attributes()->available);
	$result['newattst']=strval($xmlres->testwmpurse->wmid->attributes()->newattst);
	$result['merchant_active_mode']=strval($xmlres->testwmpurse->purse->attributes()->merchant_active_mode);
	$result['merchant_allow_cashier']=strval($xmlres->testwmpurse->purse->attributes()->merchant_allow_cashier);
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$themselfcorrstate=decbin(strval($xmlres->testwmpurse->wmid->attributes()->themselfcorrstate));
	if(strlen($themselfcorrstate)<2) $messpermit=0; else $messpermit=substr($themselfcorrstate, -2, 1);
	if(strlen($themselfcorrstate)<3) $invpermit=0; else $invpermit=substr($themselfcorrstate, -3, 1);
	if(strlen($themselfcorrstate)<4) $paypermit=0; else $paypermit=substr($themselfcorrstate, -4, 1);
	$result['messpermit']=$messpermit;
	$result['invpermit']=$invpermit;
	$result['paypermit']=$paypermit;
	return $result;
}

// ИНТЕРФЕЙС X9. ПОЛУЧЕНИЕ БАЛАНСОВ
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'purses'=>массив балансов]
function _WMXML9 () {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($global_wmid.$reqn);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<getpurses>
			<wmid>$global_wmid</wmid>
		</getpurses>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[9], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	if($result['retval']==0 && $result['retval']!==false) {
		// Формируем массив [номер кошелька] = баланс
		foreach ($xmlres->purses->purse as $purse) {
			$pursename=strval($purse->pursename);
			$amount=floatval($purse->amount);
			$result['purses'][$pursename]=$amount;
		}
	}
	return $result;
}


// ИНТЕРФЕЙС X11. ПОЛУЧЕНИЕ ИНФОРМАЦИИ ИЗ АТТЕСТАТА.
// На выходе: массив ['att'=>код аттестата, 'recalled'=>флаг отзыва аттестата, 'retval'=>код выполнения, 'retdesc'=>описание результата, 'wmids'=>список прикрепленных к аттестату WMID]
function _WMXML11 ($wmid) {
	global $XML_addr;
	$xml="
	<request>
		<wmid></wmid>
		<passportwmid>$wmid</passportwmid>				
		<sign></sign>
		<params>
			<dict>1</dict>
			<info>1</info>
			<mode>0</mode>
		</params>
	</request>";
	$resxml=_GetAnswer($XML_addr[11], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
	  $result['att']=0;
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	if(!$xmlres->certinfo->attestat->row) {
		$result['att']=0; 
		$result['retval']=1001;
		//$result['retdesc']="Информация об аттестате не получена. Возможно, неверно указан WMID.";
		$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
		return $result;
	}
	$result['att']=strval($xmlres->certinfo->attestat->row->attributes()->tid);
	$result['recalled']=strval($xmlres->certinfo->attestat->row->attributes()->recalled);
	$result['retval']=strval($xmlres->attributes()->retval);
	foreach ($xmlres->certinfo->wmids->row as $row) {
		$wmids[]=strval($row->attributes()->wmid);
	}
	$result['wmids']=$wmids;
	return $result;
}


// ИНТЕРФЕЙС X14. БЕСКОМИССИОННЫЙ ВОЗВРАТ.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'date'=>дата и время, 'wmtranid_ret'=>номер транзакции возврата]
function _WMXML14 ($wmtranid,$amount) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$amount=floatval($amount);
	$data = ($reqn.$wmtranid.$amount);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$xml="
	<w3s.request>
	    <reqn>$reqn</reqn>
	    <wmid>$global_wmid</wmid>
	    <sign>$rsign</sign>
	        <trans>
	            <inwmtranid>$wmtranid</inwmtranid>
	            <amount>$amount</amount>
	        </trans>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[14], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['wmtranid_ret']=strval($xmlres->operation->attributes()->id);
	$result['date']=strval($xmlres->operation->datecrt);
	return $result;
}


// ИНТЕРФЕЙС X16. СОЗДАНИЕ КОШЕЛЬКА.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'purse'=>номер кошелька]
function _WMXML16 ($type,$desc) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($global_wmid.$type.$reqn);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$desc=trim($desc);
	$desc=htmlspecialchars($desc, ENT_QUOTES);
	$desc=iconv("CP1251", "UTF-8", $desc);
	$xml="
	<w3s.request>
		<reqn>$reqn</reqn>
		<wmid>$global_wmid</wmid>
		<sign>$rsign</sign>
		<createpurse>
			<wmid>$global_wmid</wmid>
			<pursetype>$type</pursetype>
			<desc>$desc</desc>
		</createpurse>
	</w3s.request>";
	$resxml=_GetAnswer($XML_addr[16], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['purse']=strval($xmlres->purse->pursename);
	return $result;
}


// ИНТЕРФЕЙС X19. ПРОВЕРКА СООТВЕТСТВИЯ ПЕРСОНАЛЬНЫХ ДАННЫХ ВЛАДЕЛЬЦА WM-ИДЕНТИФИКАТОРА.
// На выходе: массив ['retval'=>код выполнения, 'retdesc'=>описание результата, 'iname'=>имя, 'oname'=>отчество, 'retid'=>уникальный идентификатор ответа]
function _WMXML19 ($type, $purse, $amount, $wmid, $passport, $fname, $iname, $bank_name, $bank_account, $card_number, $emoney_name, $emoney_id, $direction) {
	global $global_wmid, $global_pas, $global_kwm, $XML_addr;
	$reqn=_GetReqn();
	$data = ($reqn.$type.$wmid);
	$rsign = _GetSign($data, $global_wmid, $global_pas, $global_kwm);
	$fname=iconv("CP1251", "UTF-8", $fname);
	$iname=iconv("CP1251", "UTF-8", $iname);
	$bank_name=iconv("CP1251", "UTF-8", $bank_name);
	$emoney_name=iconv("CP1251", "UTF-8", $emoney_name);
	$xml="
	<passport.request>
		<reqn>$reqn</reqn>
		<signerwmid>$global_wmid</signerwmid>
		<sign>$rsign</sign>
		<operation>
			<type>$type</type>
			<direction>$direction</direction>
			<pursetype>$purse</pursetype>
			<amount>$amount</amount>
		</operation>
		<userinfo>
			<wmid>$wmid</wmid>
			<pnomer>$passport</pnomer>
			<fname>$fname</fname>
			<iname>$iname</iname>
			<bank_name>$bank_name</bank_name>
			<bank_account>$bank_account</bank_account>
			<card_number>$card_number</card_number>
			<emoney_name>$emoney_name</emoney_name>
			<emoney_id>$emoney_id</emoney_id>
		</userinfo>
	</passport.request>";
	$resxml=_GetAnswer($XML_addr[19], $xml);
	// echo $resxml;
	$xmlres = simplexml_load_string($resxml);
	if(!$xmlres) {
		$result['retval']=1000;
		$result['retdesc']="Не получен XML-ответ";
		return $result;
	}
	$result['retval']=strval($xmlres->retval);
	$result['retdesc']=iconv("UTF-8", "CP1251", strval($xmlres->retdesc));
	$result['iname']=iconv("UTF-8", "CP1251", strval($xmlres->userinfo->iname));
	$result['oname']=iconv("UTF-8", "CP1251", strval($xmlres->userinfo->oname));
	$result['retid']=strval($xmlres->retid);
	return $result;
}
?>