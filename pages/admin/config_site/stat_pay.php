<?php
require(ROOT_DIR."/pay/auto_pay_req/wmxml.inc.php");
require(ROOT_DIR."/merchant/payeer/cpayeer.php");
require(ROOT_DIR."/merchant/payeer/payeer_config.php");
require(ROOT_DIR."/merchant/yandexmoney/ym_config.php");
require(ROOT_DIR."/merchant/yandexmoney/ym_outresult.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Финансовая статистика</b></h3>';

function getDatesByWeek($_week_number, $_year = null) {
        $year = $_year ? $_year : date('Y');
        $week_number = sprintf('%02d', $_week_number);
        $date_base = strtotime($year . 'W' . $week_number . '1 00:00:00');
        $date_limit = strtotime($year . 'W' . $week_number . '7 23:59:59');
        return array($date_base, $date_limit);
}

$type_ads_arr = array(
	'dlink' => 'Серфинг', 
	'youtube' => 'Серфинг <span style="color: #3F3F3F;">You</span><span style="border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;">Tube</span>', 
	'bonus_pay' => 'Бонус шанс', 
	'autoserf' => 'Авто-серфинг', 
	'autoserfyou' => 'Авто-серфинг <span style="color: #3F3F3F;">You</span><span style="border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;">Tube</span>', 
	'mails' => 'Письма', 
	'kontext' => 'Контекстная реклама', 
	'statlink' => 'Статические ссылки', 
	'banners' => 'Баннеры', 
	'txtob' => 'Текстовые объявления', 
	'frmlink' => 'Ссылки во фрейме', 
	'psevdo' => 'Псевдо ссылки', 
	'rekcep' => 'Рекламная цепочка', 
	'task_pay' => 'Задания', 
	'tests' => 'Оплачиваемые тесты', 
	'statkat' => 'Каталог ссылок', 
	'articles' => 'Каталог статей', 
	'bstroka' => 'Бегущая строка', 
	'pay_row' => 'Платная строка',
	'pay_mes' => 'Быстрые сообщения',
	'sent_emails' => 'Рассылка на е-mail', 
	'ref_birj' => 'Биржа рефералов',
	'auc' => 'Аукцион',
	'board' => 'Доска почета',
	'gifts_pay' => 'Подарки',
	 'mail_user' => 'Рассылка пользователям',	
	'ref_wall' => 'Реф-Стена',
	'packet' => 'Пакеты рекламы',
	'task_up' => 'Задания - поднятие в списке',
                    'task_aup' => 'Задания - авто-поднятие в списке', 					
					'task_vip' => 'Задания - поднятие в VIP блок',
	'money_in' => 'Пополнение рекл. баланса', 
	'money_invest' => 'Пополнение баланса инвестора', 
	//'money_out' => 'Вывод средств'
);
$type_ads_key_arr = array_keys($type_ads_arr);
$type_ads_key_arr = implode("','", $type_ads_key_arr);

$time_week_start = getDatesByWeek(DATE("W"));

$day_week_arr_en = array("sun","mon","tue","wed","thu","fri","sat");
$day_week_arr_ru = array("Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота");
$day_month_arr_ru = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");

$date_start = strtotime(DATE("d.m.Y", (time()-6*24*60*60)));
$date_end = strtotime(DATE("d.m.Y"));
$period = (24*60*60);

echo '<table class="tables" style="margin-top:1px;">';
echo '<thead><tr>';
	echo '<th></th>';
	echo '<th style="text-transform:uppercase;">Всего</th>';
	echo '<th>За год<br>'.DATE("Y").'</th>';
	echo '<th>За месяц<br>'.$day_month_arr_ru[DATE("n", time())].''.DATE(" Y").'</th>';
	echo '<th>За неделю</th>';
	for($i=$date_start; $i<=$date_end; $i=$i+$period) {
		if(DATE("w", $i)==DATE("w")) echo '<th style="width:8%; background: green; text-transform:uppercase;">Сегодня, '.DATE("d.m.Y", $i).'</th>';
		else echo '<th style="width:8%; text-transform:uppercase;">'.$day_week_arr_ru[strtolower(DATE("w", $i))].'<br>'.DATE("d.m.Y", $i).'</th>';
	}
	echo '</tr>';
echo '</thead>';

$ITOGO_ADS["mon"] = 0;
$ITOGO_ADS["tue"] = 0;
$ITOGO_ADS["wed"] = 0;
$ITOGO_ADS["thu"] = 0;
$ITOGO_ADS["fri"] = 0;
$ITOGO_ADS["sat"] = 0;
$ITOGO_ADS["sun"] = 0;
$ITOGO_ADS["all"] = 0;
$ITOGO_ADS["year"] = 0;
$ITOGO_ADS["month"] = 0;
$ITOGO_ADS["week"] = 0;

$sql_stat = $mysqli->query("SELECT * FROM `tb_ads_stat` WHERE `type` IN ('$type_ads_key_arr') ORDER BY `id` ASC");
if($sql_stat->num_rows>0) {

	while ($row_stat = $sql_stat->fetch_array()) {

		$ITOGO_ADS["all"] += $row_stat["sum_all"];
		$ITOGO_ADS["year"] += $row_stat["sum_year"];
		$ITOGO_ADS["month"] += $row_stat["sum_month"];
		$ITOGO_ADS["week"] += $row_stat["sum_week"];

		echo '<tr>';
			echo '<td align="left" nowrap="nowrap" style="background-color:#FFFACD; height:22px;"><b style="color: #27408B; font-size:12px /*text-transform:uppercase;*/">'.$type_ads_arr[$row_stat["type"]].'</b></td>';
			echo '<td align="right" style="background-color:#D1EEEE; height:22px;"><b style="color:black; padding-right:5px">'.($row_stat["sum_all"]==0 ? "-" : number_format($row_stat["sum_all"],2,".","`")).'</b></td>';
			echo '<td align="right" style="background-color:#D1EEEE; height:22px;"><b style="color:black; padding-right:5px">'.($row_stat["sum_year"]==0 ? "-" : number_format($row_stat["sum_year"],2,".","`")).'</b></td>';
			echo '<td align="right" style="background-color:#D1EEEE; height:22px;"><b style="color:black; padding-right:5px">'.($row_stat["sum_month"]==0 ? "-" : number_format($row_stat["sum_month"],2,".","`")).'</b></td>';
			echo '<td align="right" style="background-color:#D1EEEE; height:22px;"><b style="color:black; padding-right:5px">'.($row_stat["sum_week"]==0 ? "-" : number_format($row_stat["sum_week"],2,".","`")).'</b></td>';

			for($i=$date_start; $i<=$date_end; $i=$i+$period) {
				if(DATE("w", $i)==DATE("w")) echo '<td align="right" style="background-color:#E8E8E8; height:22px;"><b style="color:green; padding-right:5px">'.($row_stat[$day_week_arr_en[strtolower(DATE("w", $i))]]==0 ? "-" : number_format($row_stat[$day_week_arr_en[strtolower(DATE("w", $i))]],2,".","`")).'</b></td>';
				else echo '<td align="right" style="height:22px;"><b style="padding-right:5px">'.($row_stat[$day_week_arr_en[strtolower(DATE("w", $i))]]==0 ? "-" : number_format($row_stat[$day_week_arr_en[strtolower(DATE("w", $i))]],2,".","`")).'</b></td>';

				$ITOGO_ADS[$day_week_arr_en[strtolower(DATE("w", $i))]] += $row_stat[$day_week_arr_en[strtolower(DATE("w", $i))]];
			}
		echo '</tr>';

		if($row_stat["type"]=="packet") {
			echo '<tr>';
				echo '<td align="left" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:12px">РЕКЛАМЫ НА СУММУ:</b>&nbsp;&nbsp;</td>';
				echo '<td align="right" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:14px">'.($ITOGO_ADS["all"]==0 ? "-" : number_format($ITOGO_ADS["all"],2,".","`")).'</b></td>';
				echo '<td align="right" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:14px">'.($ITOGO_ADS["year"]==0 ? "-" : number_format($ITOGO_ADS["year"],2,".","`")).'</b></td>';
				echo '<td align="right" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:14px">'.($ITOGO_ADS["month"]==0 ? "-" : number_format($ITOGO_ADS["month"],2,".","`")).'</b></td>';
				echo '<td align="right" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:14px">'.($ITOGO_ADS["week"]==0 ? "-" : number_format($ITOGO_ADS["week"],2,".","`")).'</b></td>';

				for($i=$date_start; $i<=$date_end; $i=$i+$period) {
					echo '<td align="right" style="background-color:#CDCDB4; height:22px;"><b style="color: #A52A2A; font-size:14px">'.number_format($ITOGO_ADS[$day_week_arr_en[strtolower(DATE("w", $i))]],2,".","`").'</b></td>';
				}
			echo '</tr>';
		}
	}

}
echo '</table>';

$balance_wmr_purse = 0;
$balance_payeer_purse = 0;
$balance_ym_purse = 0;

$WM_X9 = _WMXML9();
$wmr_purses = isset($WM_X9["purses"]) ? $WM_X9["purses"] : array();

foreach($wmr_purses as $key => $val) {
	if(preg_match("|^[R]{1}[\d]{12}$|", trim($key))) $balance_wmr_purse+=$val;
}

$payeer = new CPayeer($accountNumber, $apiId, $apiKey);
$balance_payeer_arr = $payeer->isAuth() ? $payeer->getBalance() : false;
$balance_payeer_purse = isset($balance_payeer_arr["balance"]["RUB"]["DOSTUPNO"]) ? $balance_payeer_arr["balance"]["RUB"]["DOSTUPNO"] : 0;

$YM_API = new ymAPI(CLIENT_ID, REDIRECT_URL);
$YM_INFO = $YM_API->accountInfo(TOKEN_YM);
$balance_ym_purse = isset($YM_INFO["balance"]) ? $YM_INFO["balance"] : 0;
//$balance_ym_purse = isset($YM_INFO["account_status"]) ? $YM_INFO["account_status"] : 0; проверка кошелька параметрв (anonymous - анонимный счет, named - именной счет, identified - идентифицированный счет)

echo '<h3 class="sp" style="margin-top:20px; padding-top:0;"><b>Баланс кошельков проекта</b></h3>';
echo '<table class="tables" style="margin:0 auto;">';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Баланс WMR кошельков</b></td>';
		echo '<td align="right" width="100px"><span class="text-green"><b style="font-size:14px;">'.number_format($balance_wmr_purse, 2, ".", " ").'</b> руб.</span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Баланс Payeer кошелька</b></td>';
		echo '<td align="right"><span class="text-green"><b style="font-size:14px;">'.number_format($balance_payeer_purse, 2, ".", " ").'</b> руб.</span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Баланс Яндекс кошелька</b></td>';
		echo '<td align="right"><span class="text-green"><b style="font-size:14px;">'.number_format($balance_ym_purse, 2, ".", " ").'</b> руб.</span></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';

?>