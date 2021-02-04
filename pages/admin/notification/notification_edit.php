<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require(ROOT_DIR."/merchant/func_cache.php");
require("navigator/navigator.php");

echo '<h3 class="sp" id="h_edit" style="margin-top:0; padding-top:0;"><b>Редактирование уведомлений</b></h3>';

echo '<div id="LoadForm"></div>';
echo '<div id="LoadModalNot" style="display:none;"></div>';

$perpage = 25;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_notification`");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;


?><script type="text/javascript">
function PreviewNot(id) {
	$.ajax({
		type: "POST", url: "notification/ajax/ajax_modalpopup.php?=rnd"+Math.random(), 
		data: { 'op' : 'LoadNot', 'id':id }, 
		success: function(data) {if (data) $('#LoadModalNot').html(data);}
	});
}
</script><?php

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	gebi("title").value = "";
	gebi("url").value = "";
	gebi("url_img").value = "";
	gebi("description").value = "";
}

function EditNot(id, op) {
	$.ajax({
		type: "POST", url: "notification/notification_ajax.php", data: { 'op' : op, 'id' : id }, 
		beforeSend: function() { $('#loading').show(); }, 
		success: function(data) { 
			$('#loading').hide();

			if (data == "ERRORNOID") {
				$('#IdDell'+id).hide();
				alert("Уведомление ID:" + id + " не найдено в базе!");
			} else if (data != "") {
				gebi("h_edit").innerHTML = "<b>Редактирование уведомления ID:" + id + "</b>";
				gebi("navigator1").style.display = 'none';
				gebi("navigator2").style.display = 'none';
				gebi("TableNot").style.display = 'none';
				$('#LoadForm').html(data);
			} else {
				alert("Ошибка! Не удалось обработать запрос.");
			}
		}
	});
}

function DelNot(id, op) {
	if (confirm("Вы уверены что хотите удалить уведомление ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "notification/notification_ajax.php", data: { 'op' : op, 'id' : id }, 
			beforeSend: function() { $('#loading').show(); }, 
			success: function(data) { 
				$('#loading').hide();
				if (data == "OK") {
					$('#IdDell'+id).hide();
				} else if (data == "ERRORNOID") {
					$('#IdDell'+id).hide();
					alert("Уведомление ID:" + id + " не найдено в базе!");
				} else if (data == "ERROR" | data == "") {
					alert("Ошибка! Не удалось обработать запрос!");
				} else {
					alert(data);
				}
			}
		});
	}
}

function PlayPauseNot(id, op) {
	$.ajax({
		type: "POST", url: "notification/notification_ajax.php", data: { 'op' : op, 'id' : id }, 
		beforeSend: function() { $('#loading').show(); }, 
		success: function(data) { 
			$('#loading').hide();

			if (data == "ERRORNOID") {
				alert("Уведомление ID:" + id + " не найдено в базе!");
			} else if (data == "ERROR" | data == "") {
				alert("Ошибка! Не удалось обработать запрос!");
			} else {
				$('#StatusImg'+id).html(data); 
			}

			return false;
		}
	});
}

function SaveNot(id, op) {
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
				'id':id, 
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
					gebi("info-msg-admin").innerHTML = '<span class="msg-ok">Уведомление успешно отредактировано!</span>';
					setTimeout(
						function() {
							$('#info-msg-admin').fadeOut('slow'),
							gebi("LoadForm").style.display = 'none',
							document.location.href = ""
						}, 
						1000
					);
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


echo '<div id="navigator1">';
	if($count>$perpage) { echo '<br>'; universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op"); }
echo '</div>';

echo '<table id="TableNot" class="tables" style="margin:1px auto;">';
echo '<tr align="center">';
	echo '<th width="30">Статус</th>';
	echo '<th width="40">ID</th>';
	echo '<th width="200">Заголовок</th>';
	echo '<th>URL</th>';
	echo '<th colspan="3" width="285">Действия</th>';
echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_notification` ORDER BY `id` DESC LIMIT $start_pos, $perpage");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center" id="IdDell'.$row["id"].'">';

			echo '<td align="center" class="noborder1">';
				echo '<div id="StatusImg'.$row["id"].'">';
					if($row["status"]=="1") {
						echo '<span class="adv-pause" title="Остановить" onClick="PlayPauseNot('.$row["id"].', \'PlayPause\');"></span>';
					}elseif($row["status"]=="0") {
						echo '<span class="adv-play" title="Запустить" onClick="PlayPauseNot('.$row["id"].', \'PlayPause\');"></span>';
					}
				echo '</div>';
			echo '</td>';

			echo '<td align="center">'.$row["id"].'</td>';
			echo '<td align="center">'.$row["title"].'</td>';

			echo '<td align="center"><table align="center" style="margin:0 auto; width:100%; padding:0; margin:0; border:none;">';
				echo '<tr align="left"><td style="border:none; background:none; padding:0;">URL сайта:&nbsp;<a href="'.$row["url"].'" target="_blank"><b>'.(strlen($row["url"])>80 ? limitatexto($row["url"],60)." ...." : $row["url"]).'</b></a></td></tr>';
				echo '<tr align="left"><td style="border:none; background:none; padding:0;">URL баннера:&nbsp;<a href="'.$row["url_img"].'" target="_blank"><b>'.(strlen($row["url_img"])>80 ? limitatexto($row["url_img"],60)." ...." : $row["url_img"]).'</b></a></td></tr>';
			echo '</table></td>';

			echo '<td align="center" width="95">';
				echo '<span onClick="PreviewNot('.$row["id"].');" class="sub-blue" style="float:none;">Просмотр</span>';
			echo '</td>';
			echo '<td align="center" width="95">';
				echo '<span onClick="EditNot('.$row["id"].', \'ShowForm\');" class="sub-green" style="float:none;">Изменить</span>';
			echo '</td>';
			echo '<td align="center" width="95">';
				echo '<span onClick="DelNot('.$row["id"].', \'Del\');" class="sub-red" style="float:none;">Удалить</span>';
			echo '</td>';

		echo '</tr>';
	}
}else{
	echo '<tr>';
		echo '<td align="center" colspan="6"><b>Уведомления не найдены!</b></td>';
	echo '</tr>';
}
echo '</table>';

echo '<div id="navigator2">';
	if($count>$perpage) { universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op"); }
echo '</div><br>';

?>