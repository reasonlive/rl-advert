var tmID, TmMod;
var ReOnTimer = 30000;
var domen = location.hostname || document.domain;
var socket = io.connect('wss://'+domen+':5000', {secure: true, reconnect: true, transports:['websocket']});
socket.on('reconnect_attempt', function(){socket.io.opts.transports = ['polling','websocket'];});

function setCookie(name, value, path, domain, secure, expires) {
	document.cookie= name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toGMTString() : "") + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
}

function getCookie(name) {
	var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"))
	return matches ? decodeURIComponent(matches[1]) : undefined 
}

function OnlineUs(ReOnTimer){
	var page_user = window.location.href ? window.location.href : document.location.href;
	var page_title = $("div").is("#block-title-page") ? $("#block-title-page").text() : document.title;
	$.ajax({
		type: "POST", url: "ajax/ajax_online.php", data: {'page_user':page_user, 'page_title':page_title}, 
		success: function(data) {
			if($("div").is("#onlineload") && $("#onlineload").text() != data) $("#onlineload").fadeOut(50, function() {$("#onlineload").html(data).fadeIn(150);});
			if($("span").is("#stat_online") && $("#stat_online").text() != data) $("#stat_online").fadeOut(50, function() {$("#stat_online").html(data).fadeIn(150);});
		}
	});
}

$(document).ready(function(){
	OnlineUs();

	socket.on("Online", function(data) {
		if(!$("div").is("#wsOnline")) $("body").append($('<div id="wsOnline" style="display:none;">').html(data.cnt_online));
		else $("#wsOnline").html(data.cnt_online);
	});
	
});

$(window).focus(function(){
	ReOnTimer = 30000;
	if(tmID) clearInterval(tmID)
	tmID = setInterval("OnlineUs();", ReOnTimer);
}).blur(function(){
	ReOnTimer = 60000;
	if(tmID) clearInterval(tmID)
	tmID = setInterval("OnlineUs();", ReOnTimer);
});
tmID = setInterval("OnlineUs();", ReOnTimer);

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

	window.onresize = function(){
		WinHeight = Math.ceil($.trim($(window).height()));
		ModalContentHeight = WinHeight - 100;
		$(".box-modal-content").css({"max-height": ModalContentHeight+"px", "overflow-y": "auto"});
	}

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
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '',
	toFixedFix = function(n, prec) {
		var k = Math.pow(10, prec);
		return '' + (Math.round(n * k) / k).toFixed(prec);
	};
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return minus + s.join(dec);
}

function InsertTags(text1, text2, descId) {
	var textarea = $(this).attr(descId);
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;
		if (temp_length == 0) {
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		} else {
			textarea.focus(caretPos);
		}
	} else if (typeof(textarea.selectionStart) != "undefined") {
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;
		textarea.value = begin + text1 + selection + text2 + end;
		if (textarea.setSelectionRange) {
			if (selection.length == 0) {
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			} else {
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			}
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	} else {
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

function SetSmile(smileskod) {
	InsertTags(smileskod,'');
}

function DescChange(id, elem, count_s) {
	if (elem.value.length > count_s) { elem.value = elem.value.substr(0,count_s); }
	$("#count"+id).html("Осталось символов: " +(count_s-elem.value.length));
}

function isJson(str) {
	try {JSON.parse(str);} catch (e) {return false;}
	return true;
}


if(window.addEventListener){
	window.addEventListener("message", PostMess);
}else{
	window.attachEvent("onmessage", PostMess);
}

function PostMess(event){
	var data = isJson(event.data) ? JSON.parse(event.data) : event;
	if(data.id && data.op && data.mess && data.op == "pv_mess") {
		$("#td-work-"+data.id).html('<div style="text-align:center;" class="text-green">'+data.mess+'</div>');
		setTimeout(function(){
			$("#tr-adv-"+data.id).fadeOut(300, function () {$(this).remove();});
			$("#tr-info-"+data.id).remove();
			$("#tr-hide-"+data.id).remove();
		}, 30000);
	}
}
