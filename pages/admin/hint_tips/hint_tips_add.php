<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/merchant/func_cache.php");

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Форма добавления подсказок</b></h3>';

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	$("#title").val("");
	$("#description").val("");
	return false;
}

function AddHint() {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	$("#info-msg-hint").html("").hide();

	$.ajax({
		type: "POST", url: "hint_tips/hint_tips_ajax.php", 
		data: {'op':'AddHint', 'title':title, 'description':description}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if (result == "OK") {
				$("#info-msg-hint").html('<span class="msg-ok">'+message+'</span>').slideToggle("fast");
				ClearForm();
				HideMsg("info-msg-hint", 2000);
				return false;
			} else {
				$("#info-msg-hint").html('<span class="msg-error">'+message+'</span>').slideToggle("fast");
				HideMsg("info-msg-hint", 3000);
				return false;
			}
		}
	});
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
		echo '<td align="left"><b>Заголовок подсказки</b></td>';
		echo '<td align="left"><input type="text" id="title" value="" maxlength="30" class="ok" autocomplete="off" placeholder="Заголовок подсказки" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2"><b>Текст подсказки &darr;</b></td>';
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
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:7px;">Осталось символов: 500</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:120px; width:99.2%;" placeholder="Текст подсказки" onKeyup="descchange(\'1\', this, \'500\');" onKeydown="descchange(\'1\', this, \'500\'); $(this).attr(\'class\', \'ok\');" onClick="descchange(\'1\', this, \'500\');"></textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div align="center"><span onClick="AddHint();" class="sub-blue160" style="float:none; width:160px;">Добавить подсказку</span></div>';
echo '<div id="info-msg-hint" style="display:none;"></div>';

?>