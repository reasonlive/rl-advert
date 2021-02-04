<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/merchant/func_cache.php");

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Форма добавления новости</b></h3>';

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	$("#title").val("");
	$("#description").val("");
	$("#link_forum").val("");
	$("#status_comments").prop("checked", true);
	$("#re_not").prop("checked", true);
	return false;
}

function AddNews() {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	var link_forum = $.trim($("#link_forum").val());
	var status_comments = $("#status_comments").prop("checked") == true ? 1 : 0;
	var re_not = $("#re_not").prop("checked") == true ? 1 : 0;
	$("#info-msg-news").html("").hide();

	$.ajax({
		type: "POST", url: "news/news_ajax.php", 
		data: {'op':'AddNews', 'title':title, 'description':description, 'link_forum':link_forum, 'status_comments':status_comments, 're_not':re_not}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if (result == "OK") {
				$("#info-msg-news").html('<span class="msg-ok">'+message+'</span>').slideToggle("fast");
				ClearForm();
				HideMsg("info-msg-news", 2000);
				return false;
			} else {
				$("#info-msg-news").html('<span class="msg-error">'+message+'</span>').slideToggle("fast");
				HideMsg("info-msg-news", 3000);
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
		echo '<td align="left"><b>Заголовок новости</b></td>';
		echo '<td align="left"><input type="text" id="title" value="" maxlength="60" class="ok" autocomplete="off" placeholder="Заголовок новости" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2"><b>Текст новости &darr;</b></td>';
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
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:250px; width:99.2%;" placeholder="Текст новости" onKeyDown="$(this).attr(\'class\', \'ok\');"></textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Ссылка на форум</b>, [ для обсуждения ]</td>';
		echo '<td align="left"><input type="text" id="link_forum" value="" maxlength="300" class="ok" autocomplete="off" placeholder="Не обязательно, пример: http://'.$_SERVER["HTTP_HOST"].'/forum.php" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Разрешить комментирование новости</b></td>';
		echo '<td align="left"><input type="checkbox" id="status_comments" value="1" checked="checked" style="height:20px; width:20px; margin:0px;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Уведомить о добавлении новости</b></td>';
		echo '<td align="left"><input type="checkbox" id="re_not" value="1" checked="checked" style="height:20px; width:20px; margin:0px;"></td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div align="center"><span onClick="AddNews();" class="sub-blue160" style="float:none; width:160px;">Добавить</span></div>';
echo '<div id="info-msg-news" style="display:none;"></div>';

?>