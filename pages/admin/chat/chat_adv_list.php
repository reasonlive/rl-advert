<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование рекламы в ЧАТе</b></h3>';

$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
$token_promo_list = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-promo-list".$security_key));
$token_promo_add = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-promo-add-form".$security_key));

$system_pay[0] = "Админка";
$system_pay[1] = "Рекламный счёт";
$system_pay[2] = "Внутренний счёт";
?>

<script>
var status_form = false;
var token_promo = "<?=$token_promo_list;?>";

$(document).ready(function(){
	FuncChat(false, "promo-list", false, token_promo);
});

function FuncChat(id, op, form_id, token, modal, title_win, width_win) {
	if(!status_form) {
		var datas = {}; datas["id"] = id || 0; datas["op"] = op || ''; datas["token"] = token || '';
		if(form_id) { var data_form = $("#"+form_id).serializeArray(); $.each(data_form, function(i, field) { datas[field.name] = field.value; }); }

		$.ajax({
			type:"POST", cache:false, url:"ajax/ajax_chat.php", dataType:'json', data:datas, 
			error: function(request, status, errortext) {
				status_form = false;
				ModalStart("Ошибка Ajax!", StatusMsg("ERROR", errortext+"<br>"+(request.status!=404 ? request.responseText : 'url ajax not found')), 500, true, false, false);
			}, 
			beforeSend: function() { status_form = true; $("input, textarea, select").blur(); }, 
			success: function(data) {
				status_form = false;
				var result = data.result || data;
				var message = data.message || data;
				width_win = width_win || 500;

				if(result=="OK") {
					title_win = title_win || "Информация";
				} else {
					title_win = title_win || "Ошибка";
					width_win = width_win>=500 ? 500 : width_win;
				}

				if(op=="promo-list") {
					$("#adv-chat").html(message);
					return false;
				}

				if(result=="ERROR" && (op=="promo-save" | op=="promo-add") && $("div").is("#info-msg-chat")) {
					$("#info-msg-chat").html(StatusMsg(result, message)).show();
					HideMsg("info-msg-chat", 3000);
					return false;
				}

				if($("div").is(".box-modal") && message) {
					$(".box-modal-title").html(title_win);
					$(".box-modal-content").html(message);
					if(width_win) $(".box-modal").css("width", width_win);
					if(op=="promo-form" | op=="promo-add-form") $(".box-modal-content").css("padding", 0);
				} else if(message) {
					ModalStart(title_win, message, width_win, true, false);
					if(width_win) $(".box-modal").css("width", width_win);
					if(op=="promo-form" | op=="promo-add-form") $(".box-modal-content").css("padding", 0);
				}
			}
		});
	}
	return false;
}

function CtrlEnter(event) {
	e = event || window.event;
	if((e.ctrlKey) && (e.keyCode == 10 | e.keyCode == 13)) {
		$("#SubMit").click();
	}
	return false;
}
</script>

<?php
echo '<table id="adv-chat" class="adv-cab" style="margin:2px auto;">';
echo '<tbody></tbody>';
echo '</table>';
echo '<div style="text-align:center; margin-top:15px;"><span class="sd_sub big green" onClick="FuncChat(false, \'promo-add-form\', false, \''.$token_promo_add.'\', true, \'Размещение рекламы в ЧАТе\', 550);">Добавить рекламу</span></div>';
?>