<?php
require_once($_SERVER['DOCUMENT_ROOT']."/merchant/payment_config.php");
require_once(dirname(__FILE__) . '/lib/YandexMoney.php');

$code = $_GET['code'];
if (!isset($code)) { //Посылаем человека на страницу подтверждения получения токена приложением
    $scope = 
        "account-info " .
        "payment-p2p " .
        "payment-shop";

    $authUri = YandexMoney::authorizeUri(YM_CLIENT_ID, YM_REDIRECT_URL, $scope);
    header('Location: ' . $authUri);
    exit();
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Получение токена Yandex.Money</title>
</head>
<body>
<div>
    <h3>Получение токена Yandex.Money</h3>

    <?php

        $ym = new YandexMoney(YM_CLIENT_ID, './ym.log'); //Создание экземпляра класса YandexMoney для Работы с API
        $receiveTokenResp = $ym->receiveOAuthToken($code, YM_REDIRECT_URL, YM_CLIENT_SECRET);

        print "<p>";
        if ($receiveTokenResp->isSuccess()) {
            $token = $receiveTokenResp->getAccessToken();
            print "Received token: " . $token; // Вывод: Токена
        } else {
            print "Error: " . $receiveTokenResp->getError();
            die();
        }
        print "</p>";

        $resp = $ym->accountInfo($token);

        print "<p>";
            echo 'Identified: '; if($resp->getIdentified()){echo 'Yes';}else{echo 'No';}; echo '</br>'; // Вывод: Идентификации
            echo 'Account: '.$resp->getAccount().'</br>'; // Вывод: Номера счета
            echo 'Balance(RUB): '.$resp->getBalance(); // Вывод: Баланса
        print "</p>";
    ?>
</div>
</body>
</html>