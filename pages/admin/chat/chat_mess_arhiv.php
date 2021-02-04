<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Архивные сообщения ЧАТа</b> <span class="text-grey" style="font-size:12px;">[cообщения старше 3-х дней удаляются автоматически]</span></h3>';

$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
$token_chat_mess = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-mess-arhiv-list".$security_key));
?>

<script>
var status_form = false;
var token_chat = "<?=$token_chat_mess;?>";

$(document).ready(function(){
	FuncChat(false, "mess-arhiv-list", false, token_chat);
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

				if(op=="mess-arhiv-list") {
					$(".box-chat").html(result!="OK" ? StatusMsg(result, message) : message);
					return false;
				}

				if(op=="mess-del") {
					if(result=="OK") {
						$("#chat-mess-"+id).html('<div align="center">сообщение удалено</div>').fadeOut(1000, function() {$(this).remove();});
					}
				}


			}
		});
	}
	return false;
}
</script>

<?php
echo '<div class="box-chat"></div>';
?>