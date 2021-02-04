<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/merchant/func_cache.php");

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Форма добавления уведомлений</b></h3>';

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	gebi("title").value = "";
	gebi("url").value = "";
	gebi("url_img").value = "";
	gebi("description").value = "";
}

function SaveNot(op) {
	var title = $.trim($('#title').val());
	var url = $.trim($('#url').val());
	var url_img = $.trim($('#url_img').val());
	var description = $.trim($('#description').val());

	if (title == "") {
		gebi("info-msg-admin").style.display = "";
		gebi("info-msg-admin").innerHTML = '<span class="msg-error">Вы не указали заголовок уведомления!</span>';
		setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 3000); clearTimeout();
		return false;
	} else if ((url == '') | (url == 'http://') | (url == 'https://')) {
		gebi("info-msg-admin").style.display = "";
		gebi("info-msg-admin").innerHTML = '<span class="msg-error">Вы не указали URL-адрес сайта!</span>';
		setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 3000); clearTimeout();
		return false;
	} else if ((url_img == '') | (url_img == 'http://') | (url_img == 'https://')) {
		gebi("info-msg-admin").style.display = "";
		gebi("info-msg-admin").innerHTML = '<span class="msg-error">Вы не указали URL-адрес баннера!</span>';
		setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 3000); clearTimeout();
		return false;
	} else if (description == "") {
		gebi("info-msg-admin").style.display = "";
		gebi("info-msg-admin").innerHTML = '<span class="msg-error">Вы не указали описание уведомления!</span>';
		setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 3000); clearTimeout();
		return false;
	} else {
		$.ajax({
			type: "POST", url: "notification/notification_ajax.php", 
			data: {
				'op':op, 
				'title':title, 
				'url':url, 
				'url_img':url_img, 
				'description':description
			}, 
			beforeSend: function() { $('#loading').show(); }, 
			success: function(data) { 
				$('#loading').hide(); 
				if (data == "OK") {
					gebi("info-msg-admin").style.display = "";
					gebi("info-msg-admin").innerHTML = '<span class="msg-ok">Уведомление успешно создано!</span>';
					setTimeout(function() {$('#info-msg-admin').fadeOut('slow')/*,document.location.href = "index.php?op=notification_edit"*/}, 2000);
					ClearForm(); gebi("count1").innerHTML = 'Осталось символов: 2000'; clearTimeout();
					return false;
				} else if (data == "") {
					gebi("info-msg-admin").innerHTML = '<span class="msg-error">Ошибка! Не удалось обработать запрос.</span>';
					gebi("info-msg-admin").style.display = "";
					setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 5000); clearTimeout();
					return false;
				} else {
					gebi("info-msg-admin").innerHTML = '<span class="msg-error">' + data + '</span>';
					gebi("info-msg-admin").style.display = "";
					setTimeout(function() {$('#info-msg-admin').fadeOut('slow')}, 5000); clearTimeout();
					return false;
				}
			}
		});
	}
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

function DescChange(id, elem, count_s) {
	if (elem.value.length > count_s) { elem.value = elem.value.substr(0,count_s); }
	$("#count"+id).html("Осталось символов: " +(count_s-elem.value.length));
}

</script><?php


echo '<div id="newform">';
echo '<table class="tables">';
echo '<thead><tr align="center">';
	echo '<th class="top" width="220">Параметр</a>';
	echo '<th class="top">Значение</a>';
echo '</thead></tr>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Заголовок уведомления</b></td>';
		echo '<td align="left"><input type="text" id="title" name="title" value="" maxlength="60" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b></td>';
		echo '<td align="left"><input type="text" id="url" name="url" value="" maxlength="300" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL баннера</b></td>';
		echo '<td align="left"><input type="text" id="url_img" name="url_img" value="" maxlength="300" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="3"><b>Описание уведомления &darr;</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'description\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'description\'); return false;">IMG</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 2000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:150px; width:99.2%;" onKeydown="this.style.background=\'#FFFFFF\';" onkeyup="DescChange(\'1\', this, \'2000\');"></textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div align="center"><span onClick="SaveNot(\'Add\');" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';
echo '<div id="info-msg-admin"></div>';


?>