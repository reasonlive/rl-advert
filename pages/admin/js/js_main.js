var TmID, TmMod;
var status_form, status_lp, last_info = false;
var domen = location.hostname || document.domain;

var socket = io.connect('wss://'+domen+':5000', {secure: true, reconnect: true, transports:['websocket']});
socket.on('reconnect_attempt', function(){socket.io.opts.transports = ['polling','websocket'];});

$(document).ready(function(){
	socket.on("Online", function(data) {
		if($("div").is("#wsOnline")) $("#wsOnline").html(data.cnt_online);
		else $("body").append($('<div id="wsOnline" style="display:none;">').html(data.cnt_online));
	});
});

function ModalStart(title, content, width, closeOnEsc, closeOnOverlayClick, timerAutoClose) {
	var modal_win = '<div class="box-modal" style="'+(width ? "width:"+width+"px;" : "")+'">'+
		'<div class="box-modal-title">'+(title ? title : "")+'</div><div class="box-modal-close modalpopup-close"></div>'+
		'<div class="box-modal-content" style="margin:0 auto; padding:8px 12px; text-align:justify;">'+(content ? content : "")+'</div>'+
	'</div>';
	$("#LoadModal").html(modal_win).show().modalpopup({
		closeOnEsc: closeOnEsc, 
		closeOnOverlayClick: closeOnOverlayClick, 
		beforeClose: function(data, el) {$("#LoadModal").html('').hide(); return true;}
	});

	var WinHeight = Math.ceil($.trim($(window).height()));
	var ModalHeight = Math.ceil($.trim($("#LoadModal").height()));

	if((ModalHeight+50) > WinHeight) {
		ModalContentHeight = WinHeight - 100;
		$(".box-modal-content").css({"max-height": ModalContentHeight+"px", "overflow-y": "auto"});

	}

	$(window).resize(function() {
		WinHeight = Math.ceil($.trim($(window).height()));
		ModalContentHeight = WinHeight - 100;
		$(".box-modal-content").css({"max-height": ModalContentHeight+"px", "overflow-y": "auto"});
	});

	if(TmMod) clearTimeout(TmMod);
	if(timerAutoClose) TmMod = setTimeout(function(){if($("div").is(".box-modal")) $.modalpopup("close");}, timerAutoClose*1000);

	return false;
}

function StatusMsg(status, message){
	if(status == "OK") {
		message = '<span class="msg-ok" style="margin:3px auto; padding:10px;">'+message+'</span>';
	} else if(status == "ERROR") {
		message = '<span class="msg-error" style="margin:3px auto; padding:10px;">'+message+'</span>';
	} else {
		message = '<span class="msg-w" style="margin:3px auto; padding:10px;">'+message+'</span>';
	}
	return message;
}

function number_format_js(number, decimals, dec_point, thousands_sep) {
	var minus = "";
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	if(number < 0) { minus = "-"; number = number*-1; }
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '', toFixedFix = function(n, prec) { var k = Math.pow(10, prec); return '' + (Math.round(n * k) / k).toFixed(prec); };
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || ''; s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return minus + s.join(dec);
}

function DescChange(id, elem, count_s) {
	if (elem.value.length > count_s) { elem.value = elem.value.substr(0,count_s); }
	$("#count"+id).html("Осталось символов: " +(count_s-elem.value.length));
}

function SetChecked(name_in, type){
	if(type && type=="paste") $("input[name='"+name_in+"']").prop("checked", true);
	else $("input[name='"+name_in+"']").prop("checked", false);
}

function SHBlock(id) {
	if($("#adv-title-"+id).attr("class") == "adv-title-open") {
		$("#adv-title-"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title-"+id).attr("class", "adv-title-open")
	}
	$("#adv-block-"+id).slideToggle("slow");
}

function ClearForm() {
	if($("form")[0]) $("form")[0].reset();
	if(typeof PlanChange === 'function') PlanChange();
}

function FuncAdv(id, op, type, form_id, token, modal, title_win, width_win) {
	if(!status_form){
		if(TmMod) clearTimeout(TmMod);
		var datas = {}; datas['id'] = id || 0; datas['op'] = op || ''; datas['type'] = type || ''; datas['token'] = token || '';
		if(form_id) {
			var data_form = $("#"+form_id).serializeArray();
			$.each(data_form, function(i, field){
				if(field.name=="country[]") datas[field.name] = $('input[id="country[]"]:checked').map(function(){return $(this).val();}).get();
				else datas[field.name] = field.value;
			});
		}

		if(op == "info-adv" | op == "info-up" | op == "info-bal") {
			if((last_info == op+"-"+id | $("#tr-info-"+id).css("display") == "none")) {
				$(".tr-info").hide();
				if(op+"-"+id == last_info) { last_info = false; return false; }
			}
		}

		$.ajax({
			type:"POST", cache:false, url:"ajax/ajax_admin_advertise.php", dataType:'json', data:datas, 
			error: function(request, status, errortext) {
				status_form = false; $("#loading").hide();
				ModalStart(request.status==404 ? errortext : "Ошибка Ajax!", StatusMsg("ERROR", request.status!=404 ? request.responseText+" "+errortext : "URL ajax not found.."), 500, true, false, false);
			}, 
			beforeSend: function() { status_form = true; $("input, textarea, select").blur(); $("#loading").show(); }, 
			success: function(data) {
				status_form = false; $("#loading").hide();
				var result = data.result || data;
				var message = data.message || data;
				width_win = width_win || 500;

				if (result == "OK") {
					title_win = title_win || "Информация";

					if(op == "play-pause") {
						$("#adv-status-"+id).html(message);

					} else if(op == "del-claims") {
						if(message.id_adv && message.id_claims && message.cnt_claims) {
							$("#tr-claims-"+message.id_claims).remove();
							if(message.cnt_claims<=0) {
								$("#adv-c-claims-"+message.id_adv+", #adv-v-claims-"+message.id_adv+", #adv-d-claims-"+message.id_adv+"").remove();
							}else{
								$("#adv-c-claims-"+message.id_adv+" span").html(message.cnt_claims);
							}
							if($(".tr-claims").length < 1) {
								$("#tab-claims-"+message.id_adv).append('<tr><td align="center" colspan="5" style="padding:4px;"><b>Жалобы не найдены</b></td></tr>');
								TmMod = setTimeout(function(){$.modalpopup("close");}, 3000);
							}
						}

					} else if(op == "adv-add" | op == "adv-cancel") {
						FuncOrder(op, message);

					} else if(op == "info-adv" | op == "info-up" | op == "info-bal") {
						last_info = op+"-"+id;
						$("#tr-info-"+id).show();
						$("#text-info-"+id).html(message);
						if(($(window).height() + $(window).scrollTop()) < ($("#text-info-"+id).offset().top + $("#text-info-"+id).height())) {
							$("html, body").animate({scrollTop: $("#tr-info-"+id).offset().top-$("#tr-adv-"+id).height()}, 700);
						}

					} else {
						if(op == "adv-start") FuncOrder(op, message);

						if($("div").is(".box-modal") && message && modal) {
							$(".box-modal-title").html(title_win);
							$(".box-modal-content").html(message);
						} else if(message && modal) {
							ModalStart(title_win, message, width_win, true, false);
						}
					}
				} else { 
					title_win = title_win || "Ошибка";
					width_win = width_win>=500 ? 500 : width_win;

					if(op == "info-adv" | op == "info-up" | op == "info-bal") {
						last_info = false;
					}

					if($("div").is(".box-modal") && message) {
						$(".box-modal").css("width", width_win);
						$(".box-modal-title").html(title_win);
						$(".box-modal-content").html(StatusMsg(result, message));
						TmMod = setTimeout(function(){$.modalpopup("close");}, 10000);
					} else if(message) {
						ModalStart(title_win, StatusMsg(result, message), width_win, true, false, 10);
					}
				}
			}
		});
	}
	return false;
}

function FuncOrder(op, message) {
	if(op == "adv-add") {
		$(".form-adv-order").html(message);
		$(".form-adv-list").slideToggle("slow");
		$(".form-adv-order").slideToggle("slow");
		if($("div, span").is(".scroll-to")) $("html, body").animate({scrollTop: $(".scroll-to").offset().top-50}, 700);

	} else if(op == "adv-cancel") {
		ClearForm();
		$(".form-adv-list").slideToggle("slow");
		$(".form-adv-order").html("").slideToggle("slow");
		if($("div, span").is(".scroll-to")) $("html, body").animate({scrollTop: $(".scroll-to").offset().top-50}, 700);

	} else if(op == "adv-start") {
		ClearForm();
		$(".form-adv-list").show();
		$(".form-adv-order").html("").hide();
		if($("div, span").is(".scroll-to")) $("html, body").animate({scrollTop: $(".scroll-to").offset().top-50}, 700);
	}
	return false;
}

function ChangeAds() {
	$("#loading").show();
	$(".form-adv-list").slideToggle("slow");
	$(".form-adv-order").html("").slideToggle("slow");
	if($("div, span").is(".scroll-to")) $("html, body").animate({scrollTop: $(".scroll-to").offset().top-50}, 700);
	$("#loading").hide();
	return false;
}

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars ? vars : false;
}
