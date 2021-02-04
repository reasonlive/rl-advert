<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
require_once("navigator/navigator.php");

echo '<h3 class="sp" id="h_edit" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование новостей</b></h3>';
echo '<div id="LoadForm"></div>';

$perpage = 15;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_invest_news`");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;


?><script type="text/javascript">
function ClearForm() {
	$("#title").val("");
	$("#description").val("");
	return false;
}

function EditNews(id, op) {
	$.ajax({
		type: "POST", url: "invest/invest_news/invest_news_ajax.php", 
		data: {'op':op, 'id': id }, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();

			if (data == "ERRORNOID") {
				$("#IdDel"+id).remove();
				alert("Новость с ID:" + id + " не найдена в базе!");

			} else if (data != "") {
				$("#TableNews").hide();
				$("#h_edit").html('<b>Редактирование новости ID:'+id+'</b>');
				$("#LoadForm").html(data);

			} else {
				alert("Ошибка! Не удалось обработать запрос.");
			}
		}
	});
}

function DelNews(id, op) {
	if (confirm("Вы уверены что хотите удалить новость ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "invest/invest_news/invest_news_ajax.php", 
			data: {'op':op, 'id': id }, 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) { 
				$("#loading").slideToggle();

				if (data == "OK") {
					$("#IdDel"+id).remove();
					return false;

				} else if (data == "ERRORNOID") {
					$("#IdDel"+id).remove();
					alert("Новость с ID:" + id + " не найдено в базе!");
					return false;

				} else if (data == "ERROR" | data == "") {
					alert("Ошибка! Не удалось обработать запрос!");
					return false;

				} else {
					alert(data);
					return false;
				}
			}
		});
	}
}

function SaveNews(id, op) {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());

	if (title == "") {
		$("#info-msg-news").html('<span class="msg-error">Вы не указали заголовок новости.</span>').slideToggle("fast");
		$("#title").focus().attr("class", "err");
		HideMsg("info-msg-news", 3000);
		return false;
	} else if (description == "") {
		$("#info-msg-news").html('<span class="msg-error">Вы не указали текст новости.</span>').slideToggle("fast");
		$("#description").focus().attr("class", "err");
		HideMsg("info-msg-news", 3000);
		return false;
	} else {
		$.ajax({
			type: "POST", url: "invest/invest_news/invest_news_ajax.php", 
			data: {'op':op, 'id':id, 'title':title, 'description':description}, 
			error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) { 
				$("#loading").slideToggle();

				if (data == "OK") {
					$("#info-msg-news").html('<span class="msg-ok">Новость успешно отредактирована.</span>').slideToggle("fast");
					HideMsg("info-msg-news", 1400);
					setTimeout(function() {$("#LoadForm").hide(); document.location.href = ""}, 1500);
					return false;

				} else {
					$("#info-msg-news").html('<span class="msg-error">'+data+'</span>').slideToggle("fast");
					HideMsg("info-msg-news", 3000);
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

</script><?php


echo '<div id="TableNews">';
	if($count>$perpage) { universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op"); }

	echo '<table class="tables" style="margin:1px auto;">';
	echo '<tbody>';
	$sql = $mysqli->query("SELECT * FROM `tb_invest_news` ORDER BY `id` DESC LIMIT $start_pos, $perpage");
	if($sql->num_rows>0) {
		while ($row = $sql->fetch_assoc()) {
			$description = new bbcode($row["description"]);
			$description = $description->get_html();

			echo '<tr align="center" id="IdDel'.$row["id"].'">';
				echo '<td align="left" style="padding:2px 0px;">';
					echo '<div style="color:#9E003F; background-color:#FFEC82; padding:3px 7px;">'.DATE("d.m.Yг. H:i", $row["time"]).'</div>';
					echo '<div style=" padding:2px 5px; display:block; margin-top:3px; margin-bottom:3px; color:#008B8B; font-size:14px;">'.$row["title"].'</div>';
					echo '<div style=" padding:2px 5px; display:block; font-size:12px;">'.$description.'</div>';
					echo '<div style="margin-top:5px;">';
						echo '<span class="adv-edit" onClick="EditNews('.$row["id"].', \'ShowForm\');" title="Редактировать новость"></span>';
						echo '<span class="adv-dell" onClick="DelNews('.$row["id"].', \'DelNews\');" title="Удалить новость"></span>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr align="center"><td colspan="6"><b>Новости не найдены!</b></td></tr>';
	}
	echo '</tbody>';
	echo '</table>';
	if($count>$perpage) { universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op"); }
echo '</div><br>';

?>