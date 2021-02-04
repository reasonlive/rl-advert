<?php
if(!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);

require(ROOT_DIR."/config.php");

function SqlConfig($item, $howmany=1, $decimals=false){
	global $mysqli;

	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='$item' AND `howmany`='$howmany'") or die($mysqli->error);
	$price = $sql->num_rows > 0 ? $sql->fetch_object()->price : die("Error: item['$item'] or howmany['$howmany'] not found in table `tb_config`");
	$sql->free();

	return ($decimals!==false && is_numeric($price)) ? round($price, $decimals) : $price;
}

$security_key = "AdsIiN^&uw(*Fn#*hglk0U@if%YST630n?";
$user_id = isset($user_id) ? $user_id : false;
$token_string = strtolower(md5($user_id.strtolower($username).$_SERVER["HTTP_HOST"]."adv-add".$security_key));

$pvis_cena_hit = SqlConfig('pvis_cena_hit', 1, 4);
$pvis_cena_hideref = SqlConfig('pvis_cena_hideref', 1, 4);
$pvis_cena_color = SqlConfig('pvis_cena_color', 1, 4);
$pvis_cena_revisit[1] = SqlConfig('pvis_cena_revisit', 1, 4);
$pvis_cena_revisit[2] = SqlConfig('pvis_cena_revisit', 2, 4);
$pvis_cena_uniq_ip[1] = SqlConfig('pvis_cena_uniq_ip', 1, 4);
$pvis_cena_uniq_ip[2] = SqlConfig('pvis_cena_uniq_ip', 2, 4);
$pvis_min_pay = SqlConfig('pvis_min_pay', 1, 2);
$pvis_max_pay = SqlConfig('pvis_max_pay', 1, 2);
$pvis_comis_sys = SqlConfig('pvis_comis_sys', 1, 0);

$price_adv = $pvis_cena_hit + $hide_httpref * $pvis_cena_hideref + $color * $pvis_cena_color + $pvis_cena_revisit[$revisit] + $pvis_cena_uniq_ip[$uniq_ip];
        //$price_adv = number_format($price_adv, 4, ".", "");

$reit_user_arr = array();
$sql_s = $mysqli->query("SELECT `id`,`rang`,`r_ot`,`cnt_users` FROM `tb_config_rang` WHERE `id`>'1' ORDER BY `id` ASC") or die($mysqli->error);
if($sql_s->num_rows > 0) {
	$reit_user_arr[0] = "Все пользователи проекта";
	while ($row_s = $sql_s->fetch_assoc()) {
		$reit_user_arr[$row_s["id"]] = "С рейтингом ".number_format($row_s["r_ot"], 0, ".", " ")." и более баллов (".$row_s["rang"]." ~ ".number_format($row_s["cnt_users"], 0, ".", " ")." чел.)";
	}
	$sql_s->free();
}else{
	$reit_user_arr[0] = "Все пользователи проекта";
	$sql_s->free();
}

$mysqli->close();
?>

<script src="js/jquery.simpletip-1.3.1.pack.js"></script>
<script>
var status_form = false;

$(document).ready(function(){
	var hint_ids, hint_txt = [];
	hint_txt[1] = '<b>Заголовок ссылки</b> - максимум <b>60</b> символов.<br>Заголовок должен быть кратким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После точки или запятой правильно ставить знак пробела.';
	hint_txt[2] = '<b>Описание ссылки</b> - максимум <b>80</b> символов.<br>Размещается под заголовком. Описание должно быть кратким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После точки или запятой правильно ставить знак пробела.';
	hint_txt[3] = '<b>URL-адрес сайта</b> должен начинаться с http:// или https:// и содержать не более 300 символов. Не используйте HTML-теги и Java-скрипты.';
	hint_txt[4] = '<b>Скрыть HTTP_REFERER</b> - Вы можете скрыть адрес веб-сайта с которого пришел посетитель. Некоторые браузеры, ранних версий, могут не поддерживать данную опцию.';
	hint_txt[5] = '<b>Выделить цветом</b> - Ваша ссылка будет <b class="text-red">выделена красным цветом</b>, что сделает её заметнее по сравнению с другими ссылками.';
	hint_txt[6] = '<b>Доступно для просмотра</b> - выберите как пользователи будут просматривать вашу рекламу. Стандартная настройка "Каждые 24 часа" означает, что один и тот же пользователь может просматривать вашу рекламу каждые 24 часа, подобный принцип у дополнительной настройки "Каждые 48 часов" и "1 раз в месяц".';
	hint_txt[7] = '<b>Уникальный IP</b> - Вы можете ограничить показ вашей рекламы при полном совпадении у пользователей IP или маски подсети. При включении данной опции вашу рекламу с одного IP или одной маски подсети смогут просмотреть только один раз.';
	hint_txt[8] = '<b>По дате регистрации</b> - Вы можете установить ограничение просмотра ссылки по дате регистрации пользователя на нашем проекте.';
	hint_txt[9] = '<b>По рейтингу пользователя</b> - Вы можете установить ограничение просмотра ссылки по рейтингу пользователя на нашем проекте.';
	hint_txt[10] = '<b>По наличию реферера</b> - Вы можете ограничить просмотры ссылки только пользователями не имеющими реферера на нашем проекте.';
	hint_txt[11] = '<b>По половому признаку</b> - Вы можете ограничить показ своей рекламы по половому признаку пользователей, разрешив показ своей рекламы только пользователям мужского либо женского пола.';
	hint_txt[12] = '<b>Показывать только рефералам</b> - Вы можете ограничить показ своей рекламы, разрешив показывать ее только своим рефералам.';
	hint_txt[13] = '<b>Контент <img src="/img/18+.png" alt="18+" width="16" height="16" align="absmiddle" style="margin:0 0 3px; padding:0;"></b> &mdash; если на вашем сайте присутствуют материалы для взрослых, обязательно установите галочку, в противном случае ваша реклама может быть удалена из показа, а деньги, потраченные на ее размещение, возвращены не будут. Это не касается порнографии, так как сайты, содержащие порнографию вообще запрещены для рекламирования на нашем сервисе. Также рекомендуем ознакомиться с <b>пунктом правил 3.5.3</b>';
	hint_txt[14] = '<b>Геотаргетинг</b> - это возможность ограничить посещения вашего сайта по территориальному признаку. Ссылка будет доступна для просмотра только пользователям из тех стран, которые вы отметите. По умолчанию переходы по ссылке разрешены пользователям из любых стран.';
	hint_txt[15] = '<b>Способ оплаты</b> - выберите наиболее подходящий Вам способ оплаты заказа.';
	hint_txt[16] = '<b>Сумма пополнения</b> - укажите сумму, которую вы хотите внести в бюджет рекламной площадки.';
	hint_txt[17] = '<b>Цена одного посещения</b> - стоимость за одно посещение сайта.';
	hint_txt[18] = '<b>Количество посещений</b> - количество показов сайта, которое получит рекламная площадка';
	hint_ids = Object.keys(hint_txt);

	for (var i = 0; i < hint_ids.length; i++) {
		$("#hint-"+hint_ids[i]).simpletip({fixed: true, position: ["-609", "24"], focus: false, content: hint_txt[hint_ids[i]]});
	}
})

function SHBlock(id) {
	if($("#adv-title-"+id).attr("class") == "adv-title-open") {
		$("#adv-title-"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title-"+id).attr("class", "adv-title-open")
	}
	$("#adv-block-"+id).slideToggle("slow");
}

function SetChecked(type){
	var nodes = document.getElementsByTagName("input");
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == "country[]") {
			if(type == "paste") nodes[i].checked = true;
			else  nodes[i].checked = false;
		}
	}
}

function PlanChange(){
	var rprice = <?=$pvis_cena_hit;?>;

	var hide_httpref = $.trim($("#hide_httpref").val());
	var color = $.trim($("#color").val());
	var revisit = $.trim($("#revisit").val());
	var uniq_ip = $.trim($("#uniq_ip").val());
	var money_add = $.trim($("#money_add").val());

	money_add = money_add.replace(/\,/g, ".");
	money_add = money_add.match(/(\d+(\.)?(\d){0,2})?/);
	money_add = money_add[0] || '';
	if($("#money_add").attr("type")!="number") $("#money_add").val(money_add);

	money_add = number_format_js(money_add, 2, ".", "");

	if(hide_httpref == 1) rprice += <?=$pvis_cena_hideref;?>;
	if(color == 1) rprice += <?=$pvis_cena_color;?>;

	if(revisit == 1) rprice += <?=$pvis_cena_revisit[1];?>;
	else if (revisit == 2) rprice += <?=$pvis_cena_revisit[2];?>;

	if(uniq_ip == 1) rprice += <?=$pvis_cena_uniq_ip[1];?>;
	else if(uniq_ip == 2) rprice += <?=$pvis_cena_uniq_ip[2];?>;

	count_pvis = parseFloat((money_add*10000)/(rprice*10000));

	$("#price_one").html('<span class="text-green"><b>'+number_format_js(rprice, 4, ".", " ")+'</b> руб.</span>');
	$("#count_pvis").html('<span class="text-blue"><b>'+number_format_js(Math.floor(count_pvis), 0, ".", " ")+'</b></span>');
}

function FuncAdv(id, op, type, form_id, token, modal, title_win, width_win) {
	var datas = {}; datas['id'] = id || 0; datas['op'] = op || ''; datas['type'] = type || ''; datas['token'] = token || '';
	if(form_id) {
		var data_form = $("#"+form_id).serializeArray();
		$.each(data_form, function(i, field){
			if(field.name=="country[]") datas[field.name] = $('input[id="country[]"]:checked').map(function(){return $(this).val();}).get();
			else datas[field.name] = field.value;
		});
	}

	if(!status_form){$.ajax({
		type:"POST", cache:false, url:"/advertise/ajax/ajax_advertise_new.php", dataType:'json', data:datas, 
		error: function(request, status, errortext) {
			status_form = false; $("#loading").hide();
			ModalStart("Ошибка Ajax!", StatusMsg("ERROR", errortext+"<br>"+(request.status!=404 ? request.responseText : 'url ajax not found')), 500, true, false, false);
		}, 
		beforeSend: function() { status_form = true; $("input, textarea, select").blur(); $("#loading").show(); }, 
		success: function(data) {
			status_form = false; $("#loading").hide();
			var result = data.result || data;
			var message = data.message || data;
			width_win = width_win || "550";

			if (result == "OK") {
				title_win = title_win || "Информация";

				if(op == "adv-add") {
					$("#InfoAds").hide();
					$(".form-adv-order").html(message);
					$(".form-adv-list").slideToggle("slow");
					$(".form-adv-order").slideToggle("slow");
					$("html, body").animate({scrollTop: $(".scroll-to").offset().top-10}, 700);

				} else if(op == "adv-del") {
					ClearForm();
					$("#InfoAds").show();
					$(".form-adv-list").slideToggle("slow");
					$(".form-adv-order").html("").slideToggle("slow");
					$("html, body").animate({scrollTop: $("#InfoAds").offset().top-10}, 700);

				} else if(op == "adv-pay") {
					ClearForm();
					if(modal) {
						ModalStart(title_win, message, width_win, true, false, 15);
						$("#InfoAds").show();
						$(".form-adv-list").show();
						$(".form-adv-order").html("").hide();
						$("html, body").animate({scrollTop: $("#InfoAds").offset().top-10}, 700);
					} else {
						$(".form-adv-order").html(message).show();
						$("html, body").animate({scrollTop: $(".form-adv-order").offset().top-200}, 700);
						setTimeout(function() {window.location.href = "/cabinet_ads?ads=<?=$ads;?>";}, 10000);
					}
				}

			} else { 
				title_win = title_win || "Ошибка";

				if($("div").is(".box-modal") && message) {
					$(".box-modal-title").html(title_win);
					$(".box-modal-content").html(StatusMsg(result, message));
					setTimeout(function(){$.modalpopup("close");}, 5000);
				} else if(message) {
					if(modal) ModalStart(title_win, StatusMsg(result, message), width_win, true, false, 5);
				}
			}
		}
	});}
	return false;
}

function ChangeAds() {
	$("#loading").show();
	$(".form-adv-list").slideToggle("slow");
	$(".form-adv-order").html("").slideToggle("slow");
	$("#InfoAds").show();
	$("html, body").animate({scrollTop: $(".scroll-to").offset().top-10}, 700);
	$("#loading").hide();
	return false;
}

function ClearForm() {
	$("#title").val("");
	$("#description").val("");
	$("#url").val("");
	$("#hide_httpref").val("0"); 
	$("#color").val("0");
	$("#revisit").val("0");
	$("#uniq_ip").val("0");
	$("#date_reg_user").val("0");
	$("#no_ref").val("0");
	$("#sex_user").val("0");
	$("#to_ref").val("0");
	$("#content").prop("checked", false);
	$("#method_pay").val("1");
	$("#money_add").val("100.00");
	SetChecked();
	PlanChange();
	return false;
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#subAdv").click();
	}
}
</script>

<?php
echo '<div class="scroll-to"></div>';

echo '<div id="InfoAds" style="display:block; align:justify; padding-bottom:10px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="SHBlock(\'info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:0px;">Оплачиваемые посещения - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:6px 7px; text-align:justify; background:#F7F7F7; line-height:18px; border:1px solid #E8E8E8; border-top: 1px solid #FFF;">';
		echo 'Доступная, эффективная и недорогая реклама на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; прекрасная возможность привлечения целевой аудитории на ваш интернет-ресурс. ';
		echo 'Тысячи потенциальных потребителей смогут в полной мере ознакомиться с вашей продукцией или услугами. ';
		echo 'Очень актуально для сайтов не поддерживающих просмотр во фрейме. ';
		echo 'Кроме того, вы можете максимально точно сформировать поток именно тех посетителей, для которых ваш сайт будет наиболее интересен.';
	echo '</div>';
echo '</div>';

echo '<div id="newform" class="form-adv-list ws.r" onKeyPress="CtrlEnter(event);"><form id="form-adv-'.$ads.'" action="" method="POST" onSubmit="FuncAdv(0, \'adv-add\', \''.$ads.'\', $(this).attr(\'id\'), \''.$token_string.'\', \'modal\'); return false;">';
	echo '<table class="tables">';
	echo '<thead><tr><th>Параметр</th><th colspan="2">Значение</th></tr></thead>';
	echo '<tbody>';
		echo '<tr>';
			echo '<td width="240"><b>Заголовок ссылки</b></td>';
			echo '<td><input type="text" id="title" name="title" maxlength="60" value="" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-1" class="hint-quest"></span></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Краткое описание ссылки</b></td>';
			echo '<td><input type="text" id="description" name="description" maxlength="80" value="" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-2" class="hint-quest"></span></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>URL сайта</b> (включая http:// или https://)</td>';
			echo '<td align="center"><input type="url" id="url" name="url" maxlength="300" value="" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-3" class="hint-quest"></span></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';

	echo '<div id="adv-title-dopset" class="adv-title-open" onClick="SHBlock(\'dopset\');">Настройки</div>';
	echo '<div id="adv-block-dopset" style="display:block;">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td width="240"><b>Скрыть HTTP_REFERER</b></td>';
				echo '<td>';
					echo '<select id="hide_httpref" name="hide_httpref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Нет</option>';
						echo '<option value="1">Да '.($pvis_cena_hideref>0 ? "(+$pvis_cena_hideref руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-4" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Выделить цветом</td>';
				echo '<td>';
					echo '<select id="color" name="color" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Нет</option>';
						echo '<option value="1">Да '.($pvis_cena_color>0 ? "(+$pvis_cena_color руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-5" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Доступно для просмотра</b></td>';
				echo '<td>';
					echo '<select id="revisit" name="revisit" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Каждые 24 часа</option>';
						echo '<option value="1">Каждые 48 часов '.($pvis_cena_revisit[1]>0 ? "(+$pvis_cena_revisit[1] руб./посещение)" : false).'</option>';
						echo '<option value="2">1 раз в месяц '.($pvis_cena_revisit[2]>0 ? "(+$pvis_cena_revisit[2] руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-6" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Уникальный IP</td>';
				echo '<td>';
					echo '<select id="uniq_ip" name="uniq_ip" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Нет</option>';
						echo '<option value="1">Да (100% совпадение) '.($pvis_cena_uniq_ip[1]>0 ? "&mdash; (+$pvis_cena_uniq_ip[1] руб./посещение)" : false).'</option>';
						echo '<option value="2">Усиленный по маске до 2 чисел (255.255.X.X) '.($pvis_cena_uniq_ip[2]>0 ? "&mdash; (+$pvis_cena_uniq_ip[2] руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-7" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По дате регистрации</td>';
				echo '<td>';
					echo '<select id="date_reg_user" name="date_reg_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Все пользователи проекта</option>';
						echo '<option value="3">3 дня с момента регистрации</option>';
						echo '<option value="7">7 дней с момента регистрации</option>';
						echo '<option value="30">1 месяц с момента регистрации</option>';
						echo '<option value="90">3 месяца с момента регистрации</option>';
						echo '<option value="180">6 месяцев с момента регистрации</option>';
						echo '<option value="365">1 год с момента регистрации</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-8" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По рейтингу пользователя</td>';
				echo '<td>';
					echo '<select id="reit_user" name="reit_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						foreach($reit_user_arr as $key => $val) {
							echo '<option value="'.$key.'">'.$val.'</option>';
						}
					echo '</select>';
				echo '</td>';
				echo '<td align="center" width="16"><span id="hint-9" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По наличию реферера</td>';
				echo '<td>';
					echo '<select id="no_ref" name="no_ref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Все пользователи проекта</option>';
						echo '<option value="1">Пользователям без реферера на '.$_SERVER["HTTP_HOST"].'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-10" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По половому признаку</td>';
				echo '<td>';
					echo '<select id="sex_user" name="sex_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Все пользователи проекта</option>';
						echo '<option value="1">Только мужчины</option>';
						echo '<option value="2">Только женщины</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-11" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Показывать только рефералам</td>';
				echo '<td>';
					echo '<select id="to_ref" name="to_ref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0">Все пользователи проекта</option>';
						echo '<option value="1">Рефералам 1-го уровня</option>';
						echo '<option value="2">Рефералам всех уровней</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-12" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Контент 18+</td>';
				echo '<td><input type="checkbox" id="content" name="content" value="1"> - на моем сайте присутствуют материалы для взрослых</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-13" class="hint-quest"></span></td>';
			echo '</tr>';
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<div id="adv-title-geotarg" class="adv-title-close" onClick="SHBlock(\'geotarg\');">Геотаргетинг</div>';
	echo '<div id="adv-block-geotarg" style="display:none;">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td colspan="2" align="center"><a onclick="SetChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
				echo '<td colspan="2" align="center"><a onclick="SetChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
				echo '<td rowspan="10" style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-14" class="hint-quest"></span></td>';
			echo '</tr>';
			include(ROOT_DIR."/advertise/func_geotarg.php");
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<div id="adv-title-infopay" class="adv-title-open" onclick="SHBlock(\'infopay\');">Информация и способ оплаты</div>';
	echo '<div id="adv-block-infopay" style="display:block;">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td width="240"><b>Способ оплаты</b></td>';
				echo '<td>';
					echo '<select id="method_pay" name="method_pay">';
						require_once(ROOT_DIR."/method_pay/method_pay_form.php");
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-15" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="left"><b>Сумма пополнения</b></td>';
				echo '<td align="left"><input type="text" id="money_add" name="money_add" maxlength="10" value="100.00" step="any" min="'.$pvis_min_pay.'" max="'.$pvis_max_pay.'" class="ok12" required="required" autocomplete="off" style="text-align:center;" onKeydowm="PlanChange();" onKeyup="PlanChange();">&nbsp;&nbsp;(минимум - '.number_format($pvis_min_pay, 2, ".", " ").' руб.)</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-16" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td style="height:22px;">Цена одного посещения</td>';
				echo '<td id="price_one"></td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-17" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td style="height:22px;">Количество посещений</td>';
				echo '<td id="count_pvis"></td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-18" class="hint-quest"></span></td>';
			echo '</tr>';
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<table class="tables" style="margin:0 auto;">';
	echo '<tbody>';
		echo '<tr>';
			echo '<td align="center"><input id="subAdv" type="submit" value="Оформить заказ" class="sd_sub big green"></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';

echo '</form></div>';
echo '<div class="form-adv-order ws.r" style="display:none;"></div>';

?>

<script language="JavaScript">ClearForm();</script>