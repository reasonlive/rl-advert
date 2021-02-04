<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require("navigator/navigator.php");
require(ROOT_DIR."/config.php");

$ads = "pay_visits";
$limit_entrys = 25;
$security_key = "AsDiModI*N^I&uwK(*An#*hg@if%YST630nlkj7p0U?";

$id = ( isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"])) ) ? intval(trim($_GET["id"])) : false;
$option = ( isset($_GET["option"])  && preg_match("|^[a-zA-Z0-9\-_-]{1,20}$|", trim($_GET["option"])) ) ? limpiar(trim($_GET["option"])) : false;
$page = (isset($_GET["page"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : 1;

if(isset($option, $id) && $option=="edit" && $id>0) {
	echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование оплачиваемого посещения ID:'.$id.'</b></h3>';

	if(!DEFINED("PAY_VISITS_EDIT")) DEFINE("PAY_VISITS_EDIT", true);
	include("pay_visits_edit.php");

	exit();
}else{
	echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование оплачиваемых посещений</b></h3>';
}
?>

<script>
function FuncLP(){
	if(!status_lp){
		var params = $("#load-pages");
		var datas = {};
		    datas['id'] = params.data("id") || 0;
		    datas['op'] = params.data("op") || ''; datas['type'] = params.data("type") || '';
		    datas['elid'] = params.data("elid") || ''; datas['page'] = params.data("page") || '';
		    datas['lastid'] = params.data("lastid") || 0; datas['token'] = params.data("token") || '';

		$.ajax({
			type:"POST", cache:false, url:"ajax/ajax_admin_advertise.php", dataType:'json', data:datas, 
			beforeSend: function () { status_lp = true; $("#load-pages").html('<div id="loading-pages"></div>'); }, 
			error: function () { status_lp = false; $("#load-pages").html('Ошибка ajax!'); }, 
			success: function (data) {
				status_lp = false;
				var result = data.result || data;
				var message = data.message || data;

				if (result == "OK") {
					$("#load-pages").html('Показать еще');
					if(message.rows) $("#"+datas['elid']).append(message.rows);
					params.data({"page":parseInt(message.page), "lastid":parseInt(message.lastid), "token":message.token});
					if(!message.lastid | message.lastid == 0) $("#load-pages").remove();
				}else{
					$("#load-pages").html('<span class="text-red">'+message+'</span>');
				}
			}
		});
	}
	return false;
}

function isMoney(id_input){
	var in_money = $.trim($(id_input).val());
	in_money = in_money.replace(/\,/g, ".");
	in_money = in_money.match(/(\d+(\.)?(\d){0,2})?/);
	in_money = in_money[0] || '';
	if($(id_input).attr("type")!="number") $(id_input).val(in_money);
}
</script>
<?php

function escape($value) {
	global $mysqli;
	return $mysqli->real_escape_string($value);
}

$mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);
$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='3',`date_end`='".time()."' WHERE `status`='1' AND `balance`<`price_adv`") or die($mysqli->error);
$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1' WHERE `status`='3' AND `balance`>=`price_adv`") or die($mysqli->error);

$method_arr = array(
	 "1" => array("id", "ID"), 
	 "2" => array("merch_tran_id", "№ счёта"), 
	 "2" => array("status", "статус: 1-[активные], 2-[на паузе], 3-[завершённые]"), 
	 "3" => array("wmid", "WMID"), 
	 "4" => array("username", "логину"), 
	 "5" => array("title", "заголовку ссылки"), 
	 "6" => array("description", "описанию ссылки"), 
	 "7" => array("url", "URL сайта"), 
	 "8" => array("ip", "IP адресу разместившего"), 
	 "9" => array("cnt_visits_all", "посещений всего"), 
	"10" => array("cnt_visits_now", "посещений сейчас"), 
	"11" => array("cnt_claims", "количеству жалоб"), 
	"12" => array("balance", "балансу площадки"), 
	"13" => array("money", "сумме пополнений")
);
$operator_arr = array(
	 "1" => array("=", 			"равно"), 
	 "2" => array(">", 			"больше"), 
	 "3" => array(">=", 			"больше либо равно"), 
	 "4" => array("<", 			"меньше"), 
	 "5" => array("<=", 			"меньши либо равно"), 
	 "6" => array("!=", 			"не равно"), 
	 "7" => array("LIKE '%...%'", 		"содержит любое из слов/символов"), 
	 "8" => array("NOT LIKE '%...%'", 	"не содержит любое из слов/символов"), 
	 "9" => array("=''", 			"равно пусто"), 
	"10" => array("!=''", 			"не равно пусто")
);

$_method = (isset($_GET["method"]) && is_string($_GET["method"]) && array_key_exists(trim($_GET["method"]), $method_arr)) ? trim($_GET["method"]) : false;
$_operator = (isset($_GET["operator"]) && is_string($_GET["operator"]) && array_key_exists(trim($_GET["operator"]), $operator_arr)) ? intval(trim($_GET["operator"])) : false;
$_method_tab = ($_method != false) && isset($method_arr[$_method][0]) ? $method_arr[$_method][0] : false;
$_operator_tab = ($_operator != false) && isset($operator_arr[$_operator][0]) ? $operator_arr[$_operator][0] : false;
$_search = (isset($_GET["search"]) && is_string($_GET["search"])) ? htmlspecialchars(trim($_GET["search"]), ENT_QUOTES, "CP1251", false) : false;
$_search = ($_method!==false && $_operator!==false) ? $_search : false;

$_oper_i_val = str_ireplace(
	array("LIKE '%...%'", "NOT LIKE '%...%'", "=''", "!=''", $_operator_tab), 
	array("LIKE '%".escape($_search)."%'", "NOT LIKE '%".escape($_search)."%'", "= ''", "!= ''", "$_operator_tab '".escape($_search)."'"), 
$_operator_tab);

$_WHERE_ADD = ($_method!==false && $_operator!==false) ? " AND `$_method_tab` $_oper_i_val" : false;
$_WHERE_ADD_GET = $_WHERE_ADD!==false ? "&method=$_method&operator=$_operator&search=$_search" : false;

$sql = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` FORCE INDEX (`status`) WHERE `status`>'0' $_WHERE_ADD") or die($mysqli->error);
$all_entrys = $sql->num_rows;
$sql->free();

$count_pages = ceil($all_entrys / $limit_entrys);
$page = ($page > $count_pages | $page < 0) ? $count_pages : $page;
$start_pos = ($page - 1) * $limit_entrys;
$start_pos = $start_pos >= 0 ? $start_pos : 0;

$sql = $mysqli->query("SELECT * FROM `tb_ads_pay_vis` FORCE INDEX (`status`) WHERE `status`>'0' $_WHERE_ADD ORDER BY `id` DESC LIMIT $start_pos, $limit_entrys") or die($mysqli->error);
$all_adv = $sql->num_rows;

echo '<form id="newform" method="get" action="'.$_SERVER["PHP_SELF"].'">';
echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
echo '<table class="tab-search">';
echo '<tbody>';
echo '<tr>';
	echo '<td align="left" width="100">';
		if($_WHERE_ADD != false) {
		 	echo 'Найдено площадок: <b>'.$all_entrys.'</b>';
			if($all_adv>0 && $all_adv!=$all_entrys) echo '<br>Показано записей на странице: <b>'.$all_adv.'</b> из <b>'.$all_entrys.'</b>';
		}else{
			echo 'Всего площадок: <b>'.$all_entrys.'</b>';
			if($all_adv>0 && $all_adv<$all_entrys && $all_adv!=$all_entrys) echo '<br>Показано записей на странице: <b>'.$all_adv.'</b> из <b>'.$all_entrys.'</b>';
		}
	echo '</td>';
	echo '<td align="right" width="60"><b>Поиск по:</b></td>';
	echo '<td align="center" width="140">';
		echo '<select name="method" style="text-align:left; padding-left:3px;">';
			foreach($method_arr as $key => $val) {
				echo '<option value="'.$key.'" '.($_method == "$key" ? 'selected="selected"' : false).'>'.$val[1].'</option>';
			}
		echo '</select>';
	echo '</td>';
	echo '<td align="center" width="140">';
		echo '<select name="operator">';
			foreach($operator_arr as $key => $val) {
				echo '<option value="'.$key.'" '.($_operator == "$key" ? 'selected="selected"' : false).'>'.$val[0].' ('.$val[1].')</option>';
			}
		echo '</select>';
	echo '</td>';
	echo '<td align="center"><input type="text" class="ok" name="search" value="'.$_search.'" style="min-width:200px;"></td>';
	echo '<td align="left" width="272">';
		echo '<input type="submit" value="Поиск" class="sd_sub big blue">';
		if($_WHERE_ADD != false) echo '<span class="sd_sub big red" onClick="location.replace(\'?op='.limpiar($_GET["op"]).'\');">Сбросить фильтры</span>';
	echo '</td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';
if(isset($page) && $page>1) echo '<input type="hidden" name="page" value="'.$page.'">';
echo '</form>';


if($all_entrys > $limit_entrys) universal_link_bar($all_entrys, $page, (isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : $_SERVER["PHP_SELF"]), $limit_entrys, 10, "&page=", "?op=$op".$_WHERE_ADD_GET);

echo '<table id="adv-tab" class="adv-cab" style="margin:2px auto;">';
echo '<tbody>';
if($all_adv > 0) {
	while ($row = $sql->fetch_assoc()) {
		$token_playpause = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-play-pause".$security_key));
		$token_conf_del = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-confirm-adv-del".$security_key));
		$token_edit_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-edit-adv".$security_key));
		$token_conf_erase = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-confirm-erase".$security_key));
		$token_info_up = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-info-up".$security_key));
		$token_info_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-info-adv".$security_key));
		$token_info_bal = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-info-bal".$security_key));
		$token_stat_adv = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-stat-adv".$security_key));
		$token_conf_clear_claims = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-confirm-clear-claims".$security_key));
		$token_clear_claims = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-clear-claims".$security_key));
		$token_view_claims = strtolower(md5($row["id"].strtolower($username).$_SERVER["HTTP_HOST"]."token-view-claims".$security_key));

		$cnt_totals = floor(bcdiv($row["balance"], $row["price_adv"]));
		$cnt_totals = number_format($cnt_totals, 0, ".", "`");

		if($row["status"]==1) {
			$sql_pos = $mysqli->query("SELECT COUNT(*) as `position` FROM `tb_ads_pay_vis` USE INDEX (`status_date_up`) WHERE `status`='1' AND `date_up`>(SELECT `date_up` FROM `tb_ads_pay_vis` WHERE `id`='".$row["id"]."')") or die($mysqli->error);
			$position = $sql_pos->num_rows > 0 ? ($sql_pos->fetch_object()->position + 1): 1;
			$sql_pos->free();
		}else{
			unset($position);
		}

		echo '<tr id="tr-adv-'.$row["id"].'" class="tr-adv">';
			echo '<td align="center" width="30">';
				echo '<div id="adv-status-'.$row["id"].'">';
					if($row["status"] == 0) {
						echo '<span onClick="FuncAdv('.$row["id"].', \'play-pause\', \''.$ads.'\', false, \''.$token_playpause.'\');" class="adv-play" title="Запустить показ рекламной площадки"></span>';
					}elseif($row["status"] == 1) {
						echo '<span onClick="FuncAdv('.$row["id"].', \'play-pause\', \''.$ads.'\', false, \''.$token_playpause.'\');" class="adv-pause" title="Приостановить показ рекламной площадки"></span>';
					}elseif($row["status"] == 2) {
						echo '<span onClick="FuncAdv('.$row["id"].', \'play-pause\', \''.$ads.'\', false, \''.$token_playpause.'\');" class="adv-play" title="Запустить показ рекламной площадки"></span>';
					}elseif($row["status"] == 3) {
						echo '<span onClick="FuncAdv('.$row["id"].', \'play-pause\', \''.$ads.'\', false, \''.$token_playpause.'\');" class="adv-play" title="Запустить показ рекламной площадки"></span>';
					}
				echo '</div>';
			echo '</td>';

			echo '<td align="left">';
				echo '<div id="adv-sumin-'.$row["id"].'" title="Сумма пополнений" style="float:right; display:block; font-weight:bold; color:#828282; cursor:help; margin-bottom:5px;">{'.number_format($row["money"], 2, ".", "`").'}</div>';
				echo '<div><a class="adv" href="'.$row["url"].'" target="_blank">'.$row["title"].'<br><span class="desc-text">'.$row["description"].'</span></a></div>';

				echo '<div class="info-text">';
					echo 'ID:<span style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["id"].'</span>';
					echo 'Счёт:<span style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["merch_tran_id"].'</span>';
					echo 'Оплата:<span class="text-green" style="padding:0px 8px 0 3px; font-weight:bold;">'.my_num_format($row["price_adv"], 4, ".", "", 2).' руб.</span>';
					echo 'Посещений:<span id="adv-stat-'.$row["id"].'" style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["cnt_visits_now"].'</span>';
					echo 'Осталось:<span id="adv-totals-'.$row["id"].'" style="padding:0px 8px 0 3px; font-weight:bold;">'.$cnt_totals.'</span>';
					if($row["cnt_claims"]>0) echo '<span id="adv-c-claims-'.$row["id"].'">Жалобы:<span class="text-red" style="padding:0px 8px 0 3px; font-weight:bold;">'.$row["cnt_claims"].'</span></span>';
				echo '</div>';

				echo '<div style="display:inline-block;" class="info-text">';
					echo 'Способ оплаты: <b style="padding:0px 8px 0 3px;">'.$system_pay[$row["method_pay"]].'</b>';
					echo 'Рекламодатель: '.($row["wmid"]!=false ? ($row["username"]!=false ? 'WMID:<b style="padding:0px 8px 0 3px;">'.$row["wmid"].'</b>Логин:<b style="padding:0px 8px 0 3px;">'.$row["username"].'</b>' : 'WMID:<b style="padding:0px 8px 0 3px;">'.$row["wmid"].'</b>') : ($row["username"]!=false ? 'Логин:<b style="padding:0px 8px 0 3px;">'.$row["username"].'</b>' : '<span style="color:#CCC;">не опеределен</span>'));
				echo '</div>';

				echo '<div style="display:inline-block; float:right;">';
					echo '<span onClick="FuncAdv('.$row["id"].', \'confirm-adv-del\', \''.$ads.'\', false, \''.$token_conf_del.'\', \'modal\');" class="adv-dell" title="Удалить площадку"></span>';
					echo '<span onClick="location.href=\'?op='.limpiar($_GET["op"]).$_WHERE_ADD_GET.'&page='.$page.'&option=edit&id='.$row["id"].'\';" class="adv-edit" title="Редактировать площадку"></span>';
					echo '<span onClick="FuncAdv('.$row["id"].', \'stat-adv\', \''.$ads.'\', false, \''.$token_stat_adv.'\', \'modal\', \'История просмотров ссылки ID:'.$row["id"].'\', 550);" class="adv-statistics" title="Статистика просмотров"></span>';
					if(isset($position) && $position>0 && $position<100) {
						echo '<span id="adv-up-'.$row["id"].'" onClick="FuncAdv('.$row["id"].', \'info-up\', \''.$ads.'\', false, \''.$token_info_up.'\');" class="adv-down" title="Позиция ссылки в общем списке выдачи: '.$position.'">'.$position.'</span>';
					}else{
						echo '<span id="adv-up-'.$row["id"].'" onClick="FuncAdv('.$row["id"].', \'info-up\', \''.$ads.'\', false, \''.$token_info_up.'\');" class="adv-up" title="Поднять в списке">&uarr;</span>';
					}
					echo '<span onClick="FuncAdv('.$row["id"].', \'info-adv\', \''.$ads.'\', false, \''.$token_info_adv.'\');"  class="adv-info" title="Посмотреть подробное описание"></span>';
					if($row["cnt_visits_now"]>0) echo '<span id="adv-erase-'.$row["id"].'" onClick="FuncAdv('.$row["id"].', \'confirm-erase\', \''.$ads.'\', false, \''.$token_conf_erase.'\', \'modal\');" class="adv-erase" title="Сброс статистики"></span>';
					if($row["cnt_claims"] > 0) {
						echo '<span id="adv-d-claims-'.$row["id"].'" class="clear-claims" title="Удалить все жалобы" onClick="FuncAdv('.$row["id"].', \'confirm-clear-claims\', \''.$ads.'\', false, \''.$token_conf_clear_claims.'\', \'modal\');"></span>';
						echo '<span id="adv-v-claims-'.$row["id"].'" class="view-claims" title="Просмотр жалоб" onClick="FuncAdv('.$row["id"].', \'view-claims\', \''.$ads.'\', false, \''.$token_view_claims.'\', \'modal\', \'Жалобы на рекламную площадку ID:'.$row["id"].'\', 700);"></span>';
					}
				echo '</div>';
			echo '</td>';

			echo '<td align="center" width="60" nowrap="nowrap">';
				echo '<span id="adv-bal-'.$row["id"].'" onClick="FuncAdv('.$row["id"].', \'info-bal\', \''.$ads.'\', false, \''.$token_info_bal.'\');" class="add-money'.(($row["balance"]==0) ? "-no" : false).'" title="Пополнить рекламный бюджет">'.($row["balance"]==0 ? "Пополнить" : my_num_format($row["balance"], 4, ".", "`", 2)).'</span>';
			echo '</td>';
		echo '</tr>';

		echo '<tr id="tr-hide-'.$row["id"].'" style="display:none;"><td align="center" colspan="3"></td></tr>';

		echo '<tr id="tr-info-'.$row["id"].'" class="tr-info" style="display:none;">';
			echo '<td align="center" colspan="3" id="text-info-'.$row["id"].'" class="ext-text"></td>';
		echo '</tr>';
	}
}else{
	if($_WHERE_ADD != false) echo '<tr><td align="center" colspan="3" style="padding:3px 2px 2px;"><span id="adv-warning" class="msg-w" style="margin:0 auto;">Реклама не найдена, попробуйте изменить условия поиска</span></td></tr>';
	else  echo '<tr><td align="center" colspan="3" style="padding:3px 2px 2px;"><span id="adv-warning" class="msg-w" style="margin:0 auto;">Реклама не найдена</span></td></tr>';
}
echo '</tbody>';
echo '</table>';

if($all_entrys > $limit_entrys) universal_link_bar($all_entrys, $page, (isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : $_SERVER["PHP_SELF"]), $limit_entrys, 10, "&page=", "?op=$op".$_WHERE_ADD_GET);

$sql->free();
$mysqli->close();

?>