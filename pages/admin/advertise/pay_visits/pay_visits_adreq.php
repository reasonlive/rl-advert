<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Неоплаченные заказы посещений</b></h3>';

$ads = "pay_visits";
$security_key = "AsDiModI*N^I&uwK(*An#*hg@if%YST630nlkj7p0U?";

require(ROOT_DIR."/config.php");

$mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);
$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='3',`date_end`='".time()."' WHERE `status`='1' AND `balance`<`price_adv`") or die($mysqli->error);
$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1' WHERE `status`='3' AND `balance`>=`price_adv`") or die($mysqli->error);


echo '<table id="adv-tab" class="adv-cab" style="margin:0 auto;">';
echo '<tbody>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_pay_vis` USE INDEX (`status_id`) WHERE `status`='0' ORDER BY `id` DESC") or die($mysqli->error);
if($sql->num_rows > 0) {
	while ($row = $sql->fetch_assoc()) {
		$token_start_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-confirm-start-req".$security_key));
		$token_conf_adv_del = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-confirm-del-req".$security_key));
		//$token_edit_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-edit-adv".$security_key));
		$token_info_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-info-adv".$security_key));

		$cnt_totals = floor(bcdiv($row["money"], $row["price_adv"]));
		$cnt_totals = number_format($cnt_totals, 0, ".", "`");

		echo '<tr id="tr-adv-'.$row["id"].'" class="tr-adv">';
			echo '<td align="center" width="30">';
				echo '<div id="adv-status-'.$row["id"].'">';
					echo '<span onClick="FuncAdv('.$row["id"].', \'confirm-start-req\', \''.$ads.'\', false, \''.$token_start_adv.'\', \'modal\');" class="adv-play" title="Запустить показ рекламной площадки"></span>';
				echo '</div>';
			echo '</td>';

			echo '<td align="left">';
				echo '<div><a class="adv" href="'.$row["url"].'" target="_blank">'.$row["title"].'<br><span class="desc-text">'.$row["description"].'</span></a></div>';

				echo '<div class="info-text">';
					echo 'ID:<span style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["id"].'</span>';
					echo 'Счёт:<span style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["merch_tran_id"].'</span>';
					echo 'Цена за посещение:<span class="text-green" style="padding:0px 8px 0 3px; font-weight:bold;">'.my_num_format($row["price_adv"], 4, ".", "", 2).' руб.</span>';
					echo 'Количество посещений:<span id="adv-totals-'.$row["id"].'" style="padding:0px 8px 0 3px; font-weight:bold;">'.$cnt_totals.'</span>';
				echo '</div>';

				echo '<div style="display:inline-block;" class="info-text">';
					echo 'Способ оплаты: <b style="padding:0px 8px 0 3px;">'.$system_pay[$row["method_pay"]].'</b>';
					echo 'Рекламодатель: '.($row["wmid"]!=false ? ($row["username"]!=false ? 'WMID:<b style="padding:0px 8px 0 3px;">'.$row["wmid"].'</b>Логин:<b style="padding:0px 8px 0 3px;">'.$row["username"].'</b>' : 'WMID:<b style="padding:0px 8px 0 3px;">'.$row["wmid"].'</b>') : ($row["username"]!=false ? 'Логин:<b style="padding:0px 8px 0 3px;">'.$row["username"].'</b>' : '<span style="color:#CCC;">не опеределен</span>'));
				echo '</div>';

				echo '<div style="display:inline-block; float:right;">';
					echo '<span onClick="FuncAdv('.$row["id"].', \'confirm-del-req\', \''.$ads.'\', false, \''.$token_conf_adv_del.'\', \'modal\');" class="adv-dell" title="Удалить площадку"></span>';
					//echo '<span onClick="location.href = \'?ads='.$ads.'&op=edit&id='.$row["id"].'\';" class="adv-edit" title="Редактировать площадку"></span>';
					echo '<span onClick="FuncAdv('.$row["id"].', \'info-adv\', \''.$ads.'\', false, \''.$token_info_adv.'\');"  class="adv-info" title="Посмотреть подробное описание"></span>';
				echo '</div>';
			echo '</td>';

			echo '<td align="center" width="60" nowrap="nowrap">';
				echo '<span id="adv-bal-'.$row["id"].'" class="add-money-req" title="Ожидаемая сумма пополнения" style="cursor:help;">'.my_num_format($row["money"], 2, ".", "`").'</span>';
			echo '</td>';
		echo '</tr>';

		echo '<tr id="tr-hide-'.$row["id"].'" style="display:none;"><td align="center" colspan="3"></td></tr>';

		echo '<tr id="tr-info-'.$row["id"].'" class="tr-info" style="display:none;">';
			echo '<td align="center" colspan="3" id="text-info-'.$row["id"].'" class="ext-text"></td>';
		echo '</tr>';
	}
}else{
	echo '<tr><td align="center" colspan="3" style="padding:3px 2px 2px;"><span id="adv-warning" class="msg-w" style="margin:0 auto;">Не оплаченных заказов нет</span></td></tr>';
}
echo '</tbody>';
echo '</table>';

$sql->free();

$mysqli->close();

?>