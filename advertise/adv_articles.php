<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_articles' AND `howmany`='1'");
$cena_articles = number_format($sql->fetch_object()->price, 2, ".", "");
?>

<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>

<script type="text/javascript" language="JavaScript">
var tm;

$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Заголовок статьи</b> - максимум 100 символов.<br>Заголовок должен быть коротким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей.<br>Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела.'
	});
	$("#hint2").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>URL-адрес сайта</b> - должен начинаться с http:// или https:// и содержать не более 300 символов.<br>Не используйте HTML-теги и Java-скрипты. За попытки взлома системы, наказание - удаление аккаунта.'
	});
	$("#hint3").simpletip({
		fixed: true, position: ["-610", "-34"], focus: false,
		content: '<b>Краткое описание статьи</b> - максимум 1000 символов.<br>Коротко укажите информацию о вашем ресурсе. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела. Уважайте читателей!'
	});
	$("#hint4").simpletip({
		fixed: true, position: ["-610", "-84"], focus: false,
		content: '<b>Описание статьи</b> - максимум 5000 символов.<br>Здесь вы можете подробно указать информацию о вашем ресурсе. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела. Уважайте читателей!'
	});
	$("#hint5").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Способ оплаты</b> - выберите наиболее подходящий для вас способ оплаты заказа.'
	});
	$("#hint6").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Стоимость размешения</b> составляет <?php echo $cena_articles;?> руб. Статья будет размещена на не ограниченный срок.'
	});
})

function HideMsg(id, timer) {
        clearTimeout(tm);
	tm = setTimeout(function() {$("#"+id).slideToggle(700);}, timer);
	return false;
}

function ClearForm() {
	$("#title").val("");
	$("#url").val("");
	$("#desc_min").val("");
	$("#desc_big").val("");
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
	var title = $.trim($("#title").val());
	var url = $.trim($("#url").val());
	var desc_min = $.trim($("#desc_min").val());
	var desc_big = $.trim($("#desc_big").val());
	var method_pay = $.trim($("#method_pay").val());
	$("#info-msg-cab").html("").hide();

	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'add', 'type':type, 'title':title, 'url':url, 'desc_min':desc_min, 'desc_big':desc_big, 'method_pay':method_pay }, 
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
					location.href = "/cabinet_ads?ads=articles";
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
echo '<div id="ScrollID"></div>';

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:10px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Каталог статей - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 3px 7px; text-align:justify; background-color:#F0F8FF;">';
		echo 'Каталог статей на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> даёт отличную возможность повысить показатели ИКС и PR Вашего ресурса, а так же даст больше информации для пользователей и поисковых систем! ';
		echo 'После оплаты статья будет проходить модерацию и если не будет нарушений правил размещения, Ваша статья будет размещена на неограниченный срок!';

		echo '<div style="margin:7px auto; text-align:center; color:#E32636;">';
			echo 'Перед размещением Вы сможете увидеть как будет выглядеть Ваша статья.';
		echo '</div>';
	echo '</div>';

	echo '<a href="/articles" class="art-book-title" target="_blank">Каталог статей<span>Перейти в каталог статей</span></a>';

echo '</div>';

echo '<div id="BlockForm" style="display:block;">';
echo '<div id="newform" onkeypress="CtrlEnter(event);">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr><th align="center" colspan="3">Форма добавления статьи</th></thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Заголовок статьи</b></td>';
		echo '<td align="left"><input type="text" id="title" maxlength="100" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint1" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="url" maxlength="300" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" colspan="3"><b>Краткое описание статьи</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_min\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_min\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_min\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_min\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_min\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_min\'); return false;">IMG</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 1000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="desc_min" class="ok" style="height:120px; width:99%;" onKeyup="descchange(\'1\', this, \'1000\');" onKeydown="descchange(\'1\', this, \'1000\');" onClick="descchange(\'1\', this, \'1000\');"></textarea>';
			echo '</div>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint3" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" colspan="3"><b>Описание статьи</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_big\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_big\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_big\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_big\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_big\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_big\'); return false;">IMG</span>';
			echo '<span id="count2" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 5000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="desc_big" class="ok" style="height:200px; width:99%;" onKeyup="descchange(\'2\', this, \'5000\');" onKeydown="descchange(\'2\', this, \'5000\');" onClick="descchange(\'2\', this, \'5000\');"></textarea>';
			echo '</div>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Способ оплаты</b></td>';
		echo '<td align="left">';
			echo '<select id="method_pay" class="ok">';
				require_once(DOC_ROOT."/method_pay/method_pay_form.php");
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint5" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="26"><b>Стоимость размешения</b></td>';
		echo '<td align="left"><span style="color:#FF0000; font-size:14px;">'.$cena_articles.' руб.</span></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint6" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '</table>';
echo '</div>';

echo '<br>';
echo '<div id="info-msg-cab" style="display:none;"></div>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'articles\');" class="sub-blue160" style="float:none; width:160px;">Добавить статью</span></div><br />';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-pay" style="display:none;"></div>';

?>
<script language="JavaScript">ClearForm();</script>