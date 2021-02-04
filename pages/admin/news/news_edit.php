<?php
if(!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

echo '<h3 class="sp" id="h_edit" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование новостей</b></h3>';
echo '<div id="LoadForm"></div>';

$perpage = 5;
$count = $mysqli->query("SELECT `id` FROM `tb_news`")->num_rows;
$pages_count = ceil($count / $perpage);
$start_pos = 0;

?><script type="text/javascript">
var BlockLoadPages = false;
var AutoLoadPages = true;

$(document).ready(function(){
	$(window).on("scroll", function() {
		if($("div").is("#load-pages")){
			if( AutoLoadPages && ($(window).height() + $(window).scrollTop()) >= ($("#load-pages").offset().top + $("#load-pages").height()) ) {
				LoadPages();
			}
		}
	});
});

function LoadPages(){
	if(!BlockLoadPages){
		var param = $("#load-pages");
		BlockLoadPages = true;
		$.ajax({
			type: 'POST', url: param.data("link"), 
			data: { 'op':param.data("load"), 'hash':param.data("hash"), 'page':param.data("page"), 'num':param.data("num") }, 
			dataType: 'json', 
			beforeSend: function () { $("#load-pages").html('<div id="loading-pages"></div>'); }, 
			error: function () { BlockLoadPages = false; $("#load-pages").html('Ошибка'); }, 
			success: function (Res) {
				if (Res.result == "OK") {
					$("#load-pages").html('Показать еще');
					$("#"+param.data("id")).append(Res.ajax_code);
					param.data("page", Res.page);
					if(Res.page >= param.data("close")) { $("#load-pages").remove(); }
				}else{
					$("#load-pages").html('Ошибка');
					//$("#load-pages").html(Res.message);
				}
				BlockLoadPages = false;
				return false;
			}
		});
	}
}

function DelNews(id) {
	if (confirm("Удалить новость ID: "+id+" ?")) {
		$.ajax({
			type: "POST", url: "news/news_ajax.php", 
			data: {'op':'DelNews', 'id': id }, 
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

function LockComNews(id, lock) {
	$.ajax({
		type: "POST", url: "news/news_ajax.php", 
		data: {'op':'LockComNews', 'id': id , 'lock': lock}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			if (result == "OK") {
				if(message == 1) {
					$("#lock-"+id).attr({class: "adv-lock", title: "Закрыть комментирование", onClick: "LockComNews("+id+", "+message+")"});
				}else{
					$("#lock-"+id).attr({class: "adv-unlock", title: "Открыть комментирование", onClick: "LockComNews("+id+", "+message+")"});
				}
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function EditNews(id) {
	$.ajax({
		type: "POST", url: "news/news_ajax.php", 
		data: {'op':'EditNews', 'id': id }, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			if (result == "OK") {
				$("#TableNews").hide();
				$("#h_edit").html('<b>Редактирование новости ID:'+id+'</b>');
				$("#LoadForm").html(message).show();
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function SaveNews(id) {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	var link_forum = $.trim($("#link_forum").val());
	var status_comments = $("#status_comments").prop("checked") == true ? 1 : 0;
	var re_not = $("#re_not").prop("checked") == true ? 1 : 0;
	$("#info-msg-news").html("").hide();

	$.ajax({
		type: "POST", url: "news/news_ajax.php", 
		data: {'op':'SaveNews', 'id': id, 'title':title, 'description':description, 'link_forum':link_forum, 'status_comments':status_comments, 're_not':re_not}, 
		dataType: 'json',
		error: function() {$("#loading").slideToggle(); alert("Ошибка обработки данных ajax!"); return false;}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) { 
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if (result == "OK") {
				$("#FormNews").remove();
				//$("#info-msg-news").html('<span class="msg-ok">Новость успешно отредактирована.</span>').slideToggle("fast");
				//setTimeout(function() {
					$("#TableNews").show();
					$("#h_edit").html('<b>Просмотр и редактирование новостей</b>');
					$("#LoadForm").html('').hide();
					$("#Edit"+id).html(message);
				//}, 1500);

				$("html, body").animate({scrollTop: $("#IdDel"+id).offset().top-1}, 700);

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


echo '<div id="TableNews">';
	echo '<table id="table-news" class="tables" style="margin:1px auto;">';
	echo '<tbody>';
	$sql = $mysqli->query("SELECT * FROM `tb_news` ORDER BY `id` DESC LIMIT $start_pos, $perpage");
	if($sql->num_rows>0) {
		while ($row = $sql->fetch_assoc()) {
			$description = new bbcode($row["description"]);
			$description = $description->get_html();

			echo '<tr align="center" id="IdDel'.$row["id"].'">';
				echo '<td align="left" style="padding:2px 0px;" id="Edit'.$row["id"].'">';
					echo '<div style="color:#9E003F; background-color:#FFEC82; padding:3px 7px;">'.DATE("d.m.Yг. H:i", $row["time"]).'</div>';
					echo '<div style=" padding:2px 5px; display:block; margin-top:3px; margin-bottom:3px; color:#008B8B; font-size:14px;">'.$row["title"].'</div>';
					echo '<div style=" padding:2px 5px; display:block; font-size:12px;">'.$description.'</div>';
					echo '<div style="margin-top:5px;">';
						echo '<span class="adv-dell" onClick="DelNews('.$row["id"].');" title="Удалить новость"></span>';
						echo '<span class="adv-edit" onClick="EditNews('.$row["id"].');" title="Редактировать новость"></span>';
						echo '<span id="lock-'.$row["id"].'" class="adv-'.($row["status_comments"]=="1" ? "lock" : "unlock").'" title="'.($row["status_comments"]=="1" ? "Закрыть комментирование" : "Открыть комментирование").'" onClick="LockComNews('.$row["id"].', '.$row["status_comments"].');" style="margin-right:3px;"></span>';
					echo '</div>';
				echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr align="center"><td colspan="6"><b>Новости не найдены!</b></td></tr>';
	}
	echo '</tbody>';
	echo '</table>';

	if($count>$perpage) echo '<div id="load-pages" data-load="LoadNews" data-id="table-news" data-idb="" data-page="1" data-close="'.$pages_count.'" data-num="'.$perpage.'" data-status="0" data-hash="'.md5("48915022".$pages_count.$perpage).'" data-link="news/news_ajax.php" onclick="LoadPages();">Показать ещё</div>';
echo '</div>';

?>