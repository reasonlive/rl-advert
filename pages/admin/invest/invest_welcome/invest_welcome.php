<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/merchant/func_cache.php");

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Приветственное сообщение новому инвестору на внутреннюю почту</b></h3>';

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	return false;
}

function AddWelcome() {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	$("#info-msg-welcome").html('').hide();

	if (title == "") {
		$("#info-msg-welcome").html('<span class="msg-error">Вы не указали тему сообщения.</span>').slideToggle("fast");
		$("#title").focus().attr("class", "err");
		HideMsg("info-msg-welcome", 3000);
		return false;
	} else if (description == "") {
		$("#info-msg-welcome").html('<span class="msg-error">Вы не указали текст сообщения.</span>').slideToggle("fast");
		$("#description").focus().attr("class", "err");
		HideMsg("info-msg-welcome", 3000);
		return false;
	} else {
		$.ajax({
			type: "POST", url: "invest/invest_welcome/invest_welcome_ajax.php", 
			data: {'op':'AddWelcome', 'title':title, 'description':description}, 
			error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) { 
				$("#loading").slideToggle();

				if (data == "OK") {
					$("#info-msg-welcome").html('<span class="msg-ok">Приветствие успешно сохранено.</span>').slideToggle("fast");
					ClearForm();
					setTimeout(function() {
						HideMsg("info-msg-welcome", 0);
						$("#AddForm").hide();
						LoadWelcome();
					}, 1500);
					return false;
				} else if (data == "") {
					$("#info-msg-welcome").html('<span class="msg-error">Ошибка обработки данных ajax!</span>').slideToggle("fast");
					return false;
				} else {
					$("#info-msg-welcome").html('<span class="msg-error">'+data+'</span>').slideToggle("fast");
					return false;
				}
			}
		});
	}
}

function LoadWelcome() {
	$.ajax({
		type: "POST", url: "invest/invest_welcome/invest_welcome_ajax.php", 
		data: {'op':'LoadWelcome'}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var title = data.title ? data.title : data;
			var description = data.description ? data.description : data;
			var message = data.message ? data.message : data;

			if(result=="OK") {
				$("#info-msg-welcome").html('').hide();
				$("#LoadWelcome").html(message).show();
				$("#title").val(title);
				$("#description").val(description);
				$("#AddForm").hide();
				return false;
			} else {
				if($("#LoadWelcome").css("display") != "none") $("#info-msg-welcome").html('<span class="msg-error">'+message+'</span>').slideToggle("fast");
				return false;
			}
		}
	});
}

function EditWelcome() {
	$("#info-msg-welcome").html('').hide();
	$("#AddForm").show();
	$("#LoadWelcome").hide();
	return false;
}

function DelWelcome() {
	$.ajax({
		type: "POST", url: "invest/invest_welcome/invest_welcome_ajax.php", 
		data: {'op':'DelWelcome'}, 
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var title = $("#title").val("");
			var description = $("#description").val("");

			if (data == "OK") {
				$("#info-msg-welcome").html('').hide();
				$("#AddForm").show();
				$("#LoadWelcome").html('').hide();
				return false;
			} else if (data == "") {
				$("#info-msg-welcome").html('<span class="msg-error">Ошибка обработки данных ajax!</span>').slideToggle("fast");
				return false;
			} else {
				$("#info-msg-welcome").html('<span class="msg-error">'+data+'</span>').slideToggle("fast");
				return false;
			}
		}
	});
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

$(document).ready(function(){
	if($("#LoadWelcome").css("display") == "none") LoadWelcome();
})
</script><?php


$sql = $mysqli->query("SELECT  * FROM `tb_invest_welcome`") or die($mysqli->error);
if($sql->num_rows>0) {
	$row = $sql->fetch_assoc();
	$title = (isset($row["title_text"])) ? limitatexto(trim($row["title_text"]), 100) : false;
	$description = (isset($row["desc_text"])) ? trim($row["desc_text"]) : false;

}else{
	$title = false;
	$description = false;

}

echo '<div id="AddForm" '.(($description==false && $title==false) ? 'style="display:block;"' : 'style="display:none;"').'>';
echo '<table class="tables" id="newform">';
echo '<thead><tr align="center">';
	echo '<th class="top" width="180">Параметр</a>';
	echo '<th class="top">Значение</a>';
echo '</thead></tr>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Тема сообщения</b></td>';
		echo '<td align="left"><input type="text" id="title" value="'.$title.'" maxlength="100" class="ok" autocomplete="off" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="3"><b>Текст сообщения &darr;</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:200px; width:99.2%;" onKeyDown="$(this).attr(\'class\', \'ok\');">'.$description.'</textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '<div align="center"><span onClick="AddWelcome();" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';
echo '</div></div>';

echo '<div id="LoadWelcome" style="display:none;"></div>';

echo '<div id="info-msg-welcome" style="display:none;"></div>';

?>