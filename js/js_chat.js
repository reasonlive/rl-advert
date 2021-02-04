$(document).ready(function(){
	socket.on("Chat Update", function(data) {
		if(data.chat_op && data.chat_op == "mess-load") {
			status_form = false;
			FuncChat(chat_last_id, "mess-load", false, chat_token);

		} else if(data.chat_op && data.chat_op == "mess-load-new") {
			status_form = false;
			FuncChat(chat_last_id, "mess-load-new", false, chat_token);

		} else if(data.chat_op && data.chat_op == "mess-del") {
			$("#chat-mess-"+data.id_mess).html('<div align="center">сообщение удалено</div>').fadeOut(1000, function() {$(this).remove();});
			return false;

		} else if(data.chat_op && data.chat_op == "promo-load") {
			status_form = false;
			FuncPromoLoad();
		}
	});

	$("#form_chat").keypress(function(event) {
		e = event || window.event;
		if (e.keyCode == 13) {
			$("#chat_submit").click();
			return false;
		}
	});

	$(".smile_sub").on("click", function() {
		$(".box-colors").hide();
		$(".box-smiles").toggle();
		return false;
	});

	$(document).on("click", function(e) {
		if(!$(e.target).closest(".box-panel").length) {
			$(".box-smiles").hide();
			$(".box-colors").hide();
		}
		e.stopPropagation();
	});

	$(".smile_sub img").on("click", function() {
		InsertTags($(this).data("smile")+" ", "", "chat_mess");
		return false;
	});

	$(".colors_sub").on("click", function() {
		$(".box-smiles").hide();
		$(".box-colors").toggle();
		return false;
	});

	$(".colors_sub span").on("click", function() {
		chat_color_login = $(this).data("logcolor");
		$(".box-colors").toggle();
		FuncChat(false, "form-color-login", false, chat_token, true, "Цвет логина в чате", 500);
		return false;
	});

	$("#status_scroll").on("click", function() {
		if(status_scroll){
			status_scroll = false;
			$(this).attr({"class":"scroll_off", "title":"Включить автоматический скроллинг"});
		}else{
			status_scroll = true; ScrollChat(true);
			$(this).attr({"class":"scroll_on", "title":"Отключить автоматический скроллинг"});
	  	}
		return false;
	});

	$("#status_sound").on("click", function() {
		if(status_sound){
			status_sound = false; setCookie("status_sound", 0, "/", location.hostname || document.domain);
			$(this).attr({"class":"sound_off", "title":"Включить звук сообщений"});
		}else{
			status_sound = true; setCookie("status_sound", 1, "/", location.hostname || document.domain);
			$(this).attr({"class":"sound_on", "title":"Отключить звук сообщений"});
	  	}
		return false;
	});

	FuncChat(chat_last_id, "mess-load", false, chat_token);
	FuncOnlineChat(); FuncPromoLoad();
});

function HideMsg(id, timer) {
	clearTimeout(tm);
	tm = setTimeout(function() {$("#"+id).slideToggle(700);}, timer);
	return false;
}

function LenghtChat(elem) {
	if (elem.value.length > maxlength_mess) elem.value = elem.value.substr(0,maxlength_mess);
	$(".chat-lenght").html(maxlength_mess-elem.value.length);
}

function ChatStatus(status, text_info, time_info) {
	if(status == "ban") {
		chat_last_id = 0;
		$("#chat_mess").val("");
		$("#form_chat").hide();
		$(".chat-status").html("Ваш аккаунт заблокирован для общения в чате!").show();
		$(".box-chat").html('<div class="chat-ban-block"><div class="chat-ban-info"><div class="ban-title">БАН</div><div class="ban-text">'+text_info+'</div><div class="ban-time">'+time_info+'</div></div></div>');
	} else if(status == "reit") {
		$("#chat_mess").val("");
		$("#form_chat").hide();
		$(".chat-status").html("Для общения в чате необходим рейтинг "+text_info+" баллов и выше!").show();
	}else{
		$("#form_chat").show();
		$(".chat-ban-block").remove();
		$(".chat-status").html("").hide();
	}
}

function UserToChat(type, user) {
	if(type == "chat-user-to") {
		$("#chat_mess").focus();
		$("#chat_user_to").val(user);
		$(".chat-user-to").html('<input class="chat_form" type="checkbox" id="to_privat" name="to_privat" value="1" title="Приватное сообщение, увидит только адресат" onClick="($(this).prop(\'checked\')==true ? $(\'#chat_privat\').val(1) : $(\'#chat_privat\').val(0)); $(\'#chat_mess\').focus();" />Приват | сообщение для <b>'+user+'</b> <span onClick="UserToChat();">&times;</span>');
	}else{
		$("#chat_mess").focus();
		$("#chat_privat").val(0);
		$("#chat_user_to").val("");
		$(".chat-user-to").html("");
	}
}

function ScrollChat(status_animate) {
	if(status_animate) {
		if($("div").is(".box-chat") && status_scroll) $(".box-chat").animate({scrollTop: $(".box-chat").offset().top + $(".box-chat")[0].scrollHeight}, 1000);
	}else{
		if($("div").is(".box-chat") && status_scroll) $(".box-chat").scrollTop($(".box-chat")[0].scrollHeight);
	}
	return false;
}

function SoundsChat(sound) {
	if(sound == 1 && status_sound) $("#chat_sound_all")[0].play();
	if(sound == 2 && status_sound) $("#chat_sound_user")[0].play();
	return false;
}

function FuncChat(id, op, form_id, token, modal, title_win, width_win) {
	var status_chat_rules = (getCookie("status_chat_rules") && getCookie("status_chat_rules")==1) ? true : false;

	if(!status_form) {
		var datas = {}; datas["id"] = id || 0; datas["op"] = op || ''; datas["token"] = token || ''; datas["color"] = chat_color_login || '';
		if(form_id) { var data_form = $("#"+form_id).serializeArray(); $.each(data_form, function(i, field) { datas[field.name] = field.value; }); }
		$.ajax({
			type:"POST", cache:false, url:"/ajax/ajax_chat.php", dataType:'json', data:datas, 
			error: function(request, status, errortext) {
				status_form = false;
				ModalStart("Ошибка Ajax!", StatusMsg("ERROR", errortext+"<br>"+(request.status!=404 ? request.responseText : 'url ajax not found')), 500, true, false, false);
			}, 
			beforeSend: function() {
				status_form = true;
				if(op=="mess-add") {$("input, textarea, select").blur();}
				if(!status_chat_rules) {FunRulesChat();}
			}, 
			success: function(data) {
				status_form = false;
				var result = data.result || data;
				var message = data.message || data;

				if(result=="ERROR-LOGIN") { location.href="/login"; return false;}

				if(message.chat_status && message.chat_status=="ban") {
					ChatStatus(message.chat_status, message.chat_ban_info, message.chat_ban_end);
					return false;
				} else if(message.chat_status && message.chat_status=="reit") {
					ChatStatus(message.chat_status, message.chat_ban_info, message.chat_ban_end);
					if(op!="mess-load" && op!="mess-load-new") {
						if($("div").is(".box-modal")) $.modalpopup("close");
						return false;
					}
				}else{
					ChatStatus();
				}

				if(op=="mess-add") {
					$("#chat_mess").val("").focus();
					UserToChat(); $(".chat-lenght").html(maxlength_mess);

				} else if((op=="mess-load" | op=="mess-load-new")) {
					if(result=="OK" && message.chat_messages) {
						chat_last_id = message.last_id;
						if(op=="mess-load") $(".box-chat").html(message.chat_messages);
						else $(".box-chat").append(message.chat_messages);
						ScrollChat(false);
					}

				} else if(op=="mess-del") {
					$(".box-chat").focus();
					return false;

				} else {
					if(op=="color-login-pay" | op=="chat-promotion-pay" | op=="promo-save" | op=="ban-user") {
						if(result=="OK") {
							if(width_win) $(".box-modal").css("width", width_win);
							$(".box-modal-title").html("Информация");
							$(".box-modal-content").css("padding", "5px").html(StatusMsg(result, message));
							setTimeout(function(){$.modalpopup("close");}, 3000);
						} else {
							$("#info-msg-chat").html(result!="OK" ? StatusMsg(result, message) : message).show();
							HideMsg("info-msg-chat", 3000);
						}
					} else {
						if($("div").is(".box-modal") && message) {
							if(width_win) $(".box-modal").css("width", width_win);
							$(".box-modal-title").html(title_win);
							$(".box-modal-content").html(result!="OK" ? StatusMsg(result, message) : message);
						} else if(message) {
							ModalStart(title_win, (result!="OK" ? StatusMsg(result, message) : message), width_win, true, false);
						}
						$(".box-modal-content").css("padding", "0");
					}
				}
			}
		});
	}
	return false;
}

function FuncOnlineChat() {
	$.ajax({type:"POST", cache:false, url:"/ajax/ajax_chat_online.php", dataType:'script', data:{'user':chat_user_name}, success:function(data){}});
	setTimeout(FuncOnlineChat, 10000);
	return false;
}

function FuncPromoLoad() {
	$.ajax({type:"POST", cache:false, url:"/ajax/ajax_chat_promo.php", dataType:'script', data:{'op':'promo-load'}, success:function(){}});
	return false;
}

function FunRulesChat() {
	$.ajax({
		type:"POST", cache:false, url:"/ajax/ajax_chat_rules.php", dataType:'html', data:{'op':'rules'}, 
		success:function(data){ ModalStart("Правила общения в ЧАТе на проекте <b>"+(location.hostname || document.domain).toUpperCase()+"</b>", data, 650, true, false, false)}
	});
	return false;
}

function getRandomInt(min, max) {
	return Math.floor(Math.random() * (max - min)) + min;
}

function RotatePromo() {
	if(promo_arr && typeof promo_arr === 'object' && promo_arr.length > 0) {
		var i = promo_start==1 ? 0 : getRandomInt(0, promo_arr.length);
		var promo_link = '<img src="//www.google.com/s2/favicons?domain='+promo_arr[i].promo_domen+'" alt="" align="absmiddle" style="margin:0 5px 3px 0; padding:0;" />';
		    promo_link+= '<a href="'+promo_arr[i].promo_url+'" class="'+promo_arr[i].promo_class+'" target="_blank">'+promo_arr[i].promo_desc+'</a>';
		$(".promotion-panel span").data("id_promo", promo_arr[i].promo_id);
		promo_start = false;
		$(".chat-promo").html(promo_link);
		clearTimeout(tm_promo); tm_promo = setTimeout(RotatePromo, 10000);
	}
}
