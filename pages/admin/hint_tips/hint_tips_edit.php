<?php
if(!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

echo '<h3 class="sp" id="h_edit" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование подсказок</b></h3>';
echo '<div id="LoadForm"></div>';

?><script type="text/javascript">
var BlockLoadPages = false;
var AutoLoadPages = true;

function DelHint(id) {
	if (confirm("Удалить подсказку ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "hint_tips/hint_tips_ajax.php", 
			data: {'op':'DelHint', 'id': id }, 
			dataType: 'json',
			error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) { 
				$("#loading").slideToggle();
				var result = data.result ? data.result : data;
				var message = data.message ? data.message : data;
				if (result == "OK") {
					$("#IdDel"+id).remove();
					return false;
				} else {
					alert(message);
					return false;
				}
			}
		});
	}
}

function EditHint(id) {
	$.ajax({
		type: "POST", url: "hint_tips/hint_tips_ajax.php", 
		data: {'op':'EditHint', 'id': id }, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			if (result == "OK") {
				$("#TableHint").hide();
				$("#h_edit").html('<b>Редактирование подсказки ID:'+id+'</b>');
				$("#LoadForm").html(message).show();
				descchange(1, description, 500);
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function SaveHint(id) {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	$("#info-msg-hint").html("").hide();

	$.ajax({
		type: "POST", url: "hint_tips/hint_tips_ajax.php", 
		data: {'op':'SaveHint', 'id': id, 'title':title, 'description':description}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if (result == "OK") {
				$("#FormHint").remove();
				//$("#info-msg-hint").html('<span class="msg-ok">Подсказка успешно отредактирована.</span>').slideToggle("fast");
				//setTimeout(function() {
					$("#TableHint").show();
					$("#h_edit").html('<b>Просмотр и редактирование подсказок</b>');
					$("#LoadForm").html('').hide();
					$("#Edit"+id).html(message);
				//}, 1500);

				$("html, body").animate({scrollTop: $("#IdDel"+id).offset().top-1}, 700);

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


echo '<div id="TableHint">';
	echo '<table id="table-news" class="tables" style="margin:1px auto;">';
	echo '<tbody>';
	$sql = $mysqli->query("SELECT * FROM `tb_hint_tips` ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while ($row = $sql->fetch_assoc()) {
			$description = new bbcode($row["description"]);
			$description = $description->get_html();

			echo '<tr align="center" id="IdDel'.$row["id"].'">';
				echo '<td align="center" style="padding:3px; width:25px;"><b>'.$row["id"].'</b></td>';
				echo '<td align="left" style="padding:2px 0px;" id="Edit'.$row["id"].'">';
					echo '<div style="padding:1px 5px; display:block; margin-top:3px; margin-bottom:3px; color:#008B8B; font-size:13px; font-weight:bold;">'.$row["title"].'</div>';
					echo '<div style="padding:1px 5px; display:block; font-size:12px;">'.$description.'</div>';
					echo '<div style="margin-top:0px;">';
						echo '<span class="adv-dell" onClick="DelHint(\''.$row["id"].'\');" title="Удалить подсказку"></span>';
						echo '<span class="adv-edit" onClick="EditHint(\''.$row["id"].'\');" title="Редактировать подсказку"></span>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr align="center"><td colspan="2"><b>Подсказки не найдены!</b></td></tr>';
	}
	echo '</tbody>';
	echo '</table>';
echo '</div>';

?>