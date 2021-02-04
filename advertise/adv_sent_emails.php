<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_sent_emails' AND `howmany`='1'");
$cena_sent_emails = number_format($sql->fetch_object()->price, 2, ".", "");
?>

<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>

<script type="text/javascript" language="JavaScript">
var tm;

$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Тема сообщения</b> - максимум 250 символов.<br>Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей.<br>Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела.'
	});
	$("#hint2").simpletip({
		fixed: true, position: ["-610", "-25"], focus: false,
		content: '<b>Текст сообщения</b> - максимум 2000 символов.<br>Здесь вы можете подробно указать информацию о вашем ресурсе. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела. Уважайте читателей!'
	});
	$("#hint3").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Способ оплаты</b> - выберите наиболее подходящий для вас способ оплаты заказа.'
	});
	$("#hint4").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Стоимость размешения</b> составляет <?php echo $cena_sent_emails;?> руб.'
	});
})

function HideMsg(id, timer) {
        clearTimeout(tm);
	tm = setTimeout(function() {$("#"+id).slideToggle(700);}, timer);
	return false;
}

function ClearForm() {
	$("#subject").val("");
	$("#message").val("");
	$("#method_pay").val("1");
	return false;
}

function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}

function InsertTags(text1, text2, descId) {
	var textarea = document.getElementById(descId);
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

function SaveAds(id, type) {
	var subject = $.trim($("#subject").val());
	var message = $.trim($("#message").val());
	var method_pay = $.trim($("#method_pay").val());
	$("#info-msg-cab").html("").hide();

	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'add', 'type':type, 'subject':subject, 'message':message, 'method_pay':method_pay }, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
			var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("#info-msg-cab").show();
				$("#OrderForm").html(message);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				$("#InfoAds").slideToggle("slow");
				$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function PayAds(id, type) {
	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'pay', 'type':type, 'id':id}, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
			var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("html, body").animate({scrollTop:0}, 700);
				$("#OrderForm").html('<span class="msg-ok">'+message+'</span>');
				setTimeout(function() {
					location.href = "/cabinet_ads?ads=sent_emails";
				}, 2000);
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function DeleteAds(id, type) {
	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'del', 'type':type, 'id':id}, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
				var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
				$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
				//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("html, body").animate({scrollTop:0}, 700);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				$("#InfoAds").slideToggle("slow");
				ClearForm();
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function ChangeAds() {
	$("#info-msg-cab").hide();
	$("#loading").slideToggle();
	$("#BlockForm").slideToggle("slow");
	$("#OrderForm").slideToggle("slow");
	$("#InfoAds").slideToggle("slow");

	$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
	$("#loading").slideToggle();
	return false;
}

function GoToPay() {
	$("#loading").slideToggle();
	$("#PreView").slideToggle("slow");
	$("#ToPaySys").slideToggle("slow");

	$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
	$("#loading").slideToggle();
	return false;
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#Save").click();
	}
}

</script>

<?php

$sql_all_sent = $mysqli->query("SELECT `id` FROM `tb_users` WHERE `email_ok`='1' AND `email_sent`='1'");
$all_sent_users = number_format($sql_all_sent->num_rows, 0, ".", "`");

echo '<div id="ScrollID"></div>';

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:6px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Рассылка на e-mail - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Рассылка на e-mail пользователям <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; Ваша реклама будет отправлена на электронную почту пользователям нашего сайта, ';
		echo 'которые отметили в своем профиле, поставив в чекбоксе галочку, "Отправлять рекламные письма на E-mail" (на данный момент всего пользователей которые получат вашу рассылку: <b>'.($all_sent_users).'</b>).';
		echo '<br>';
	echo '</div>';
echo '</div>';

echo '<div id="BlockForm" style="display:block;">';
echo '<div id="newform" onkeypress="CtrlEnter(event);">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr><th align="center" colspan="3">Форма добавления рассылки</th></thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Тема сообщения</b></td>';
		echo '<td align="left"><input type="text" id="subject" maxlength="250" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint1" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2" style="padding:4px 5px 4px 5px;"><b>Текст сообщения &darr;</b></td>';
		echo '<td align="center" rowspan="2" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2" style="">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'message\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'message\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'message\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'message\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'message\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'message\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'message\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'message\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'message\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'message\'); return false;">IMG</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 2000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="message" class="ok" style="height:200px; width:99%;" onKeyup="descchange(\'1\', this, \'2000\');" onKeydown="descchange(\'1\', this, \'2000\');" onClick="descchange(\'1\', this, \'2000\');"></textarea>';
			echo '</div>';
		echo '</td>';
		//echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Способ оплаты</b></td>';
		echo '<td align="left">';
			echo '<select id="method_pay" class="ok">';
				require_once(DOC_ROOT."/method_pay/method_pay_form.php");
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint3" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="26"><b>Стоимость рассылки</b></td>';
		echo '<td align="left"><span style="color:#FF0000; font-size:14px;">'.$cena_sent_emails.' руб.</span> (без учета вашей накопительной скидки)</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '</table>';
echo '</div>';

echo '<br>';
echo '<div id="info-msg-cab" style="display:none;"></div>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'sent_emails\');" class="sub-blue160" style="float:none; width:160px;">Добавить рассылку</span></div>';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-pay" style="display:none;"></div>';

?>
<script language="JavaScript">ClearForm();</script>
<br>