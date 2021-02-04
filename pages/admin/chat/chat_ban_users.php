<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Заблокированные пользователи ЧАТа</b></h3>';

$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
$token_ban_users_list = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-ban-users-list".$security_key));
$token_ban_user_add_form = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-ban-user-add-form".$security_key));
?>

<script>
var status_form = false;
var token_chat = "<?=$token_ban_users_list;?>";

$(document).ready(function(){
	FuncChat(false, "ban-users-list", false, token_chat);
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

				if(op=="ban-users-list") {
					$("#chat-ban-users tbody").html(message);
					return false;
				}

				if(result=="ERROR" && (op=="ban-user-add") && $("div").is("#info-msg-chat")) {
					$("#info-msg-chat").html(StatusMsg(result, message)).show();
					HideMsg("info-msg-chat", 3000);
					return false;
				}

				if(result=="ERROR" && (op=="ban-user-add-form" | op=="ban-user-add")) message = StatusMsg(result, message);

				if($("div").is(".box-modal") && message) {
					$(".box-modal-title").html(title_win);
					$(".box-modal-content").html(message);
					if(width_win) $(".box-modal").css("width", width_win);
					$(".box-modal-content").css("padding", 0);
				} else if(message) {
					ModalStart(title_win, message, width_win, true, false);
					if(width_win) $(".box-modal").css("width", width_win);
					$(".box-modal-content").css("padding", 0);
				}
			}
		});
	}
	return false;
}
</script>

<?php
echo '<div style="text-align:left; margin:5px auto 10px;"><span class="sd_sub red" style="margin-left:0;" onClick="FuncChat(false, \'ban-user-add-form\', false, \''.$token_ban_user_add_form.'\', true, \'Заблокировать пользователя\', 500);">Заблокировать пользователя</span></div>';

echo '<table id="chat-ban-users" class="tables" style="margin:2px auto;">';
echo '<thead>';
echo '<tr>';
	echo '<th width="100">Логин</th>';
	echo '<th width="100">Заблокировал</th>';
	echo '<th>Причина</th>';
	echo '<th width="100">Период</th>';
	echo '<th width="250">Дата блокировки/окончания</th>';
	echo '<th width="30"></th>';
echo '</tr>';
echo '</thead>';
echo '<tbody></tbody>';
echo '</table>';

?>