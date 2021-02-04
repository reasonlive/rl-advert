<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки оплачиваемых посещений</b></h3>';

$ads = "pay_visits";
$security_key = "AsDiModI*N^I&uwK(*An#*hg@if%YST630nlkj7p0U?";
$token_string = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."adv-config".$security_key));

require(ROOT_DIR."/config.php");

function SqlConfig($item, $howmany=1, $decimals=false){
	global $mysqli;

	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='$item' AND `howmany`='$howmany'") or die($mysqli->error);
	$price = $sql->num_rows > 0 ? $sql->fetch_object()->price : die("Error: item['$item'] or howmany['$howmany'] not found in table `tb_config`");
	$sql->free();

	return ($decimals!==false && is_numeric($price)) ? round($price, $decimals) : $price;
}

$pvis_cena_hit = SqlConfig('pvis_cena_hit', 1, 4);
$pvis_cena_hideref = SqlConfig('pvis_cena_hideref', 1, 4);
$pvis_cena_color = SqlConfig('pvis_cena_color', 1, 4);
$pvis_cena_revisit[1] = SqlConfig('pvis_cena_revisit', 1, 4);
$pvis_cena_revisit[2] = SqlConfig('pvis_cena_revisit', 2, 4);
$pvis_cena_uniq_ip[1] = SqlConfig('pvis_cena_uniq_ip', 1, 4);
$pvis_cena_uniq_ip[2] = SqlConfig('pvis_cena_uniq_ip', 2, 4);
$pvis_cena_up = SqlConfig('pvis_cena_up', 1, 2);
$pvis_min_pay = SqlConfig('pvis_min_pay', 1, 2);
$pvis_max_pay = SqlConfig('pvis_max_pay', 1, 0);
$pvis_comis_sys = SqlConfig('pvis_comis_sys', 1, 0);
$pvis_comis_del = SqlConfig('pvis_comis_del', 1, 0);

$mysqli->close();

?>

<script>
function isValidIn(){
	var cena_hit = $.trim($("#cena_hit").val());
	var cena_hideref = $.trim($("#cena_hideref").val());
	var cena_color = $.trim($("#cena_color").val());
	var cena_revisit_1 = $.trim($("#cena_revisit_1").val());
	var cena_revisit_2 = $.trim($("#cena_revisit_2").val());
	var cena_uniq_ip_1 = $.trim($("#cena_uniq_ip_1").val());
	var cena_uniq_ip_2 = $.trim($("#cena_uniq_ip_2").val());
	var cena_up = $.trim($("#cena_up").val());
	var min_pay = $.trim($("#min_pay").val());
	var max_pay = $.trim($("#max_pay").val());
	var comis_sys = $.trim($("#comis_sys").val());
	var comis_del = $.trim($("#comis_del").val());

	cena_hit = cena_hit.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_hit = cena_hit[0] || ''; $("#cena_hit").val(cena_hit);

	cena_hideref = cena_hideref.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_hideref = cena_hideref[0] || ''; $("#cena_hideref").val(cena_hideref);

	cena_color = cena_color.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_color = cena_color[0] || ''; $("#cena_color").val(cena_color);

	cena_revisit_1 = cena_revisit_1.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_1 = cena_revisit_1[0] || ''; $("#cena_revisit_1").val(cena_revisit_1);

	cena_revisit_2 = cena_revisit_2.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_2 = cena_revisit_2[0] || ''; $("#cena_revisit_2").val(cena_revisit_2);

	cena_uniq_ip_1 = cena_uniq_ip_1.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_uniq_ip_1 = cena_uniq_ip_1[0] || ''; $("#cena_uniq_ip_1").val(cena_uniq_ip_1);

	cena_uniq_ip_2 = cena_uniq_ip_2.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,4})?/);
	cena_uniq_ip_2 = cena_uniq_ip_2[0] || ''; $("#cena_uniq_ip_2").val(cena_uniq_ip_2);

	cena_up = cena_up.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	cena_up = cena_up[0] || ''; $("#cena_up").val(cena_up);

	min_pay = min_pay.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	min_pay = min_pay[0] || ''; $("#min_pay").val(min_pay);

	max_pay = max_pay.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	max_pay = max_pay[0] || ''; $("#max_pay").val(max_pay);

	comis_sys = comis_sys.replace(/\,/g, ".").match(/(\d+)?/);
	comis_sys = comis_sys[0] || ''; $("#comis_sys").val(comis_sys);

	comis_del = comis_del.replace(/\,/g, ".").match(/(\d+)?/);
	comis_del = comis_del[0] || ''; $("#comis_del").val(comis_del);

	cena_hit_user = cena_hit/(1+comis_sys/100);

	plan = 1000;
	min_pay_ads = (cena_hit * plan);
	max_pay_ads = min_pay_ads;
	max_pay_ads += (cena_hideref * plan);
	max_pay_ads += (cena_color * plan);
	max_pay_ads += (cena_revisit_2 * plan);
	max_pay_ads += (cena_uniq_ip_2 * plan);

	$("#cena_hit_user").html('<b class="text-red">' + number_format(cena_hit_user, 4, ".", "") + '</b>');
	$("#min_pay_ads").html('<b class="text-green">' + number_format(min_pay_ads, 2, ".", "`") + '</b>');
	$("#max_pay_ads").html('<b class="text-green">' + number_format(max_pay_ads, 2, ".", "`") + '</b>');
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#subAdv").click();
	}
}
</script>

<?php
echo '<div class="scroll-to"></div>';

echo '<div id="newform" class="form-adv-list ws.r" onKeyPress="CtrlEnter(event);"><form id="form-adv-'.$ads.'" action="" method="POST" onSubmit="FuncAdv(0, \'adv-config\', \''.$ads.'\', $(this).attr(\'id\'), \''.$token_string.'\', \'modal\'); return false;">';

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:700px; margin:0px; padding:0px;">';
	echo '<thead><tr align="center"><th>Параметр</th><th width="120">Для пользователя</th><th width="120">Для рекламодателя</th></tr></thead>';

	echo '<tr>';
		echo '<td align="left"><b>Цена за 1 посещение</b>, (руб./посещение)</td>';
		echo '<td align="center" id="cena_hit_user"></td>';
		echo '<td align="center"><input type="text" id="cena_hit" name="cena_hit" value="'.p_floor($pvis_cena_hit, 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за скрытие HTTP_REFERER</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_hideref" name="cena_hideref" value="'.p_floor($pvis_cena_hideref, 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Цена за выделение цветом</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_color" name="cena_color" value="'.p_floor($pvis_cena_color, 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно для просмотра каждые 48 часов</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_revisit_1" name="cena_revisit_1" value="'.p_floor($pvis_cena_revisit[1], 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно для просмотра 1 раз в месяц</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_revisit_2" name="cena_revisit_2" value="'.p_floor($pvis_cena_revisit[2], 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за уникальный IP, 100% совпадение</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_uniq_ip_1" name="cena_uniq_ip_1" value="'.p_floor($pvis_cena_uniq_ip[1], 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за уникальный IP, по маске до 2 чисел</b>, (руб./посещение)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_uniq_ip_2" name="cena_uniq_ip_2" value="'.p_floor($pvis_cena_uniq_ip[2], 4).'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость поднятия в списке</b>, (руб.)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="cena_up" name="cena_up" value="'.number_format($pvis_cena_up, 2, ".", "").'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальная сумма пополнения</b>, (руб.)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="text" id="min_pay" name="min_pay" value="'.number_format($pvis_min_pay, 2, ".", "").'" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Максимальная сумма пополнения</b>, (руб.)</td>';
		echo '<td align="center"></td>';
		echo '<td align="center"><input type="number" id="max_pay" name="max_pay" value="'.p_floor($pvis_max_pay, 2).'" step="1" min="1" max="1000000000" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2"><b>Комиссия сайта</b>, (%)</td>';
		echo '<td align="center"><input type="number" id="comis_sys" name="comis_sys" value="'.$pvis_comis_sys.'" step="1" min="1" max="100" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2"><b>Комиссия за возврат средств при удалении</b>, (%)</td>';
		echo '<td align="center"><input type="number" id="comis_del" name="comis_del" value="'.$pvis_comis_del.'" step="1" min="0" max="100" class="ok12" style="text-align:center; padding:1px 5px;" onKeydowm="isValidIn();" onKeyup="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2" height="23"><b>Минимальная/Максимальная цена за 1000 посещений</b>, руб.</td>';
		echo '<td align="center" style="font-size:15px;"><span id="min_pay_ads"></span><span style="color:#808080; font-size:16px; padding:0 5px;">|</span><span id="max_pay_ads"></span></td>';
	echo '</tr>';

	echo '<tr align="center"><td colspan="3"><input id="subAdv" type="submit" value="Сохранить" class="sd_sub big green"></tr>';
echo '</table>';
echo '</form>';

?>

<script language="JavaScript">isValidIn();</script>