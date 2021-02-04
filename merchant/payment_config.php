<?PHP
define('HTTP_HOST', $_SERVER["HTTP_HOST"]);

#BTC
define('BTC_GUID', '');
define('BTC_PASS', '');

#WebMoney
define('WM_WMID', '');
define('WM_WMR', '');
define('WM_FILE_KEY', WM_WMID.'.kwm');
define('WM_FILE_PASS', '');
define('WM_SECRET_KEY', '');

#QiWi
define('QW_LOGIN', '');
define('QW_PASSWORD', '');

#YandexMoney
define ('YM_CLIENT_PURSE', '');
define ('YM_CLIENT_ID', '');
define ('YM_REDIRECT_URL', 'https://'.$_SERVER["HTTP_HOST"].'/merchant/yandexmoneytoken/get_token.php');
define ('YM_CLIENT_SECRET', '');
define ('YM_SECRET_KEY', '');
define ('YM_TOKEN', YM_CLIENT_PURSE.'.');

#LiqPay
define('LP_MERCH_ID', '');//ID РјРµСЂС‡Р°РЅС‚Р° (РїСЂРёРµРј РїР»Р°С‚РµР¶РµР№ РЅР° РєР°СЂС‚Сѓ/СЃС‡РµС‚). РћРЅ Р¶Рµ public_key
define('LP_PRIVAT_KEY', '');//РџРѕРґРїРёСЃСЊ РјРµСЂС‡Р°РЅС‚Р°
define('LP_RES_URL', 'http://'.$_SERVER["HTTP_HOST"].'/payok.php');//СЃС‚СЂР°РЅРёС†Р° РЅР° РєРѕС‚РѕСЂСѓСЋ РІРµСЂРЅРµС‚СЃСЏ РєР»РёРµРЅС‚
define('LP_SER_URL', 'http://'.$_SERVER["HTTP_HOST"].'/merchant/liqpay/lp_payresult.php');//СЃС‚СЂР°РЅРёС†Р° РЅР° РєРѕС‚РѕСЂСѓСЋ РїСЂРёРґРµС‚ РѕС‚РІРµС‚ РѕС‚ СЃРµСЂРІРµСЂР°
define('LP_CURR', 'RUR'); // РІР°Р»СЋС‚Р°
define('LP_SENDBOX', '0'); // 1 = С‚РµСЃС‚РѕРІС‹Р№, 0 = СЂРѕР±РѕС‡РёР№
define('LP_TYPE', 'buy'); //buy - РїРѕРєСѓРїРєР°, donate - РїРѕР¶РµСЂС‚РІРѕРІР°РЅРёРµ, subscribe - РїРѕРґРїРёСЃРєР°
define('LP_LANG', 'ru'); //СЏР·С‹Рє

#PerfectMoney
define('PM_MEMBER_ID', '');
define('PM_PASSWORD', '');
define('PM_PHRASE_HASH', '');
define('PM_MEMBER_USD', '');
define('PM_MEMBER_EUR', '');
define('PM_PAYMENT_UNITS', 'USD');
define('PM_TIMESTAMPGMT', time());

#Payeer
define('PY_ACC_NUMBER', '');
define('PY_API_ID', '');
define('PY_API_KEY', '');
define('PY_M_SHOP', '');
define('PY_M_CURR', '');
define('PY_M_KEY', '');

#RoboKassa
define('RK_LOGIN', '');
define('RK_PASS1', '');
define('RK_PASS2', '');
define('RK_CURR', 'PCR');
define('RK_CULTURE', 'ru');

#InterKassa
define('IK_SHOP_ID', '');
define('IK_SECRET_KEY', '');
define('IK_CURR', 'RUB');
define('IK_ENC', 'cp-1251');
define('IK_LOC', 'ru');

#ZPayment
define('ZP_ID_SHOP', '16413');//ID РјР°РіР°Р·РёРЅР° РІ Z-Payment
define('ZP_SECRET_KEY', '');//Merhant Key РєР»СЋС‡ РјР°РіР°Р·РёРЅР°

#WalletOne
define('WO_LOGIN', '');
define('WO_SECRET_KEY', '');
define('WO_PTENABLED', 'WalletOneRUR');
define('WO_CURRENCY_ID', '643'); // 643 вЂ” Р РѕСЃС–Р№СЃСЊРєС– СЂСѓР±Р»С–  980 вЂ” РЈРєСЂР°С—РЅСЃСЊРєС– РіСЂРёРІРЅС–
?>