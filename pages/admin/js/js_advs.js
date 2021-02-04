function gebi(id){
	return document.getElementById(id)
}
/*
function hidemsg() {
	$("#info-msg").slideToggle("slow");
	if(tm) clearTimeout(tm);
}
*/
function hidemsg(ID) {
	if(ID) { $("#"+ID).slideToggle("slow"); }else{ 	$("#info-msg").slideToggle("slow"); }
	if(tm) clearTimeout(tm);
}

function HideMsg(id, timer) {
        if(timer) clearTimeout(timer);
	if(id) { timer = setTimeout(function() {$("#"+id).slideToggle(1000);}, timer); }
}

function alert_nostart() {
	alert("Для запуска, необходимо пополнить рекламный бюджет");
	return false;
}

function alert_nopause() {
	alert("Приостановка этой рекламной площадки не предусмотрена");
	return false;
}

function alert_nolimit() {
	alert("На сегодня показ приостановлен, так как вы установлено ограничение показов в сутки (или в час). Просмотр запуститься автоматически");
	return false;
}

function alert_bezlimit() {
	alert("Приостановка этой рекламной площадки не предусмотрена (заказан безлимит)");
	return false;
}

function play_pause(id, type) {
	$.ajax({
		type: "POST", url: "ajax_adm/ajax_adm_adv.php?rnd="+Math.random(), data: { 'op' : 'play_pause', 'type' : type, 'id' : id }, 
		beforeSend: function() { $('#loading').show(); }, 
		success: function(data) { 
			$('#loading').hide();

			if (data == "ERRORNOID") {
				alert("У Вас нет рекламной площадки с ID - " + id);
			} else if (data == "BEZLIMIT") {
				alert_bezlimit();
			} else if (data == "NOLIMIT") {
				alert_nolimit();
			} else if (data == "ERROR" | data == "") {
				alert("Ошибка! Не удалось обработать запрос!");
			} else if (data == "0") {
				alert_nostart();
			} else {
				$('#playpauseimg'+id).html(data); 
			}

			return false;
		}
	});
}

function number_format(number, decimals, dec_point, thousands_sep) {
	var minus = "";
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	if(number < 0){
		minus = "-";
		number = number*-1;
	}
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
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

function str_replace(search, replace, subject) {
	return subject.split(search).join(replace);
}

function InsertTags(text1, text2, descId) {
	var textarea = gebi(descId);
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

function descchange(id, elem, count_s) {
	if (elem.value.length > count_s) { elem.value = elem.value.substr(0,count_s); }
	$("#count"+id).html("Осталось символов: " +(count_s-elem.value.length));
}
