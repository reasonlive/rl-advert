<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование статей в каталоге</b></h3>';

require("navigator/navigator.php");
$perpage = 20;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='1'");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

$system_pay[-1] = "Пакет";
$system_pay[0] = "Админка";
$system_pay[1] = "WebMoney";
$system_pay[2] = "RoboKassa";
$system_pay[3] = "Wallet One";
$system_pay[4] = "InterKassa";
$system_pay[5] = "Payeer";
$system_pay[6] = "Qiwi";
$system_pay[7] = "PerfectMoney";
$system_pay[8] = "YandexMoney";
$system_pay[9] = "MegaKassa";
$system_pay[20] = "FreeKassa";
$system_pay[10] = "Рекл. счет";

?>
<script type="text/javascript" language="JavaScript">
var s_h = false;
var new_id = false;

function LoadInfo(id, type, op) {
	if(s_h==(id + op)) {
		s_h = false;
		$("#load-info"+id).hide();
		$("#mess-info"+id).html("");
	} else {
		if(s_h && new_id) {
			$("#load-info"+new_id).hide();
			$("#mess-info"+new_id).html("");
		}
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json',
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() {
				$("#loading").slideToggle();
				new_id = id; s_h = id + op;

				$("html, body").animate({scrollTop: $("#adv_dell"+id).offset().top-3}, 700);

				$("#load-info"+id).show(); 
				$("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>');
				return false;
			}, 
			success: function(data) { 
				$("#loading").slideToggle();
				var result = data.result ? data.result : data;
				var message = data.message ? data.message : data;

				new_id = id; s_h = id + op;

				$("html, body").animate({scrollTop: $("#adv_dell"+id).offset().top-3}, 700);

				$("#load-info"+id).show();

				if(result == "OK") {
					$("#mess-info"+id).html(message);
					if(op == "LoadForm") {descchange(1, desc_min, 1000); descchange(2, desc_big, 5000);}
					return false;
				} else {
					$("#mess-info"+id).html('<span class="msg-error">'+message+'</span>');
					return false;
				}
			}
		});
	}
	return false;
}

function SaveAds(id, type, op) {
	var title = $.trim($("#title").val());
	var url = $.trim($("#url").val());
	var desc_min = $.trim($("#desc_min").val());
	var desc_big = $.trim($("#desc_big").val());
	$("#info-msg-cab").html("").hide();

	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':op, 'type':type, 'id': id, 'title':title, 'url':url, 'desc_min':desc_min, 'desc_big':desc_big }, 
		dataType: 'json',
		error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("#load-info"+id).hide();
				$("#mess-info"+id).html("");
				$("#art-title"+id).html(message);

				s_h = false; new_id = false;
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function Lock(id, type, op){
	var msg_lock = $.trim($("#msg_lock").val());

	if (!msg_lock | msg_lock == false) {
		$("#msg_lock").focus().attr("class", "err");
		alert("Укажите причину блокировки!");
	} else {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id, 'msg_lock':msg_lock }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				var result = data.result ? data.result : data;
				var message = data.message ? data.message : data;

				if(result == "OK") {
					$("#adv_dell"+id).remove();
					$("#load-info"+id).remove();
					$("#hide"+id).remove();
					StatMenu(type, 'StatMenu');

					s_h = false; new_id = false;

					AddRow("newform");
					return false;
				} else {
					alert(message);
					return false;
				}
			}
		});
	}
}

function AdsReq(id, type, op) {
	if (op == "Delete" && !confirm("Вы уверены что хотите удалить рекламную площадку ID: "+id+" ?")) {
		return false;
	} else if (op == "Start" && !confirm("Опубликовать статью с ID: "+id+" ?")) {
		return false;
	} else {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				var result = data.result ? data.result : data;
				var message = data.message ? data.message : data;

				if (result == "OK") {
					$("#adv_dell"+id).remove();
					$("#load-info"+id).remove();
					$("#hide"+id).remove();
					StatMenu(type, 'StatMenu');

					s_h = false; new_id = false;

					AddRow("newform");
					return false;
				} else {
					alert(message);
					return false;
				}
			}
		});
	}
}

function StatMenu(type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type }, 
		dataType: 'json', 
		success: function(data) { 
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			var obj_mess = jQuery.parseJSON(message);

			if (result == "OK") {
				if(obj_mess.count_moder) {
					if(obj_mess.count_moder == "0") {$("#li_art_moder").html("");}else{$("#li_art_moder").html(obj_mess.count_moder);}
				}
				if(obj_mess.count_req)   $("#art_req").html(obj_mess.count_req);
				if(obj_mess.count_moder) $("#art_moder").html(obj_mess.count_moder);
				if(obj_mess.count_ban)   $("#art_ban").html(obj_mess.count_ban);
				if(obj_mess.count_edit)  $("#art_edit").html(obj_mess.count_edit);
				return false;
			}
		}
	});
}

function AddRow(id){
	var table = document.getElementById(id);
	if(table.rows.length < 1) {
		var newrow = table.insertRow();
		newrow.setAttribute("align", "center");
		var newcell = newrow.insertCell();
		newcell.innerHTML = "<b>Статьи не найдены!</b>";
	}
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#Save").click();
	}
}
</script>
<?php

if($count>$perpage) universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op");

echo '<table class="adv-cab" id="newform">';
echo '<tbody>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `status`='1' ORDER BY `id` DESC LIMIT $start_pos, $perpage");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_assoc()) {

		echo '<tr id="adv_dell'.$row["id"].'">';
			echo '<td align="center" width="35">';
				echo '<div id="playpauseimg'.$row["id"].'">';
					echo '<span class="adv-pause" title="Статья активна"></span>';
				echo '</div>';
			echo '</td>';

			echo '<td align="left">';
				echo '<img width="16" height="16" border="0" alt="" title="" style="margin:0; padding:0; padding-bottom:2px; padding-right:5px;" src="//www.google.com/s2/favicons?domain='.@gethost($row["url"]).'" align="absmiddle" />';
				echo '<a id="art-title'.$row["id"].'" class="adv" href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.$row["title"].'</a><br>';

				echo '<span class="info-text">';
					echo 'ID:&nbsp;<b>'.$row["id"].'</b>, Счет:&nbsp;<b>'.$row["merch_tran_id"].'</b>;&nbsp;&nbsp;';
					echo 'Способ оплаты: <b>'.$system_pay[$row["method_pay"]].'</b>;&nbsp;&nbsp;';
					echo 'Рекламодатель: '.($row["wmid"]!=false ? ($row["username"]!=false ? "WMID:<b>".$row["wmid"]."</b>, Логин:<b>".$row["username"]."</b>" : "WMID:<b>".$row["wmid"]."</b>") : ($row["username"]!=false ? "Логин:<b>".$row["username"]."</b>" : "<span style=\"color:#CCC;\">не опеределен</span>"));
				echo '</span>';

				echo '<span class="adv-dell" title="Удалить статью" onClick="AdsReq('.$row["id"].', \'articles\', \'Delete\');"></span>';
				echo '<span class="adv-edit" title="Редактировать статью" onClick="LoadInfo('.$row["id"].', \'articles\', \'LoadForm\');"></span>';
				echo '<span id="lock-'.$row["id"].'" class="adv-'.($row["status"]=="4" ? "unlock" : "lock").'" title="'.($row["status"]=="4" ? "Просмотр информации о блокировки" : "Заблокировать статью").'" onClick="LoadInfo('.$row["id"].', \'articles\', \'GoLock\');"></span>';
				echo '<span class="adv-info" title="Предварительный просмотр статьи" onClick="LoadInfo('.$row["id"].', \'articles\', \'GetInfo\');"></span>';
			echo '</td>';

			echo '<td align="center" width="80" nowrap="nowrap">';
				echo '<a class="add-money-yes" title="Стоимость заказа: '.number_format($row["money"], 2, ".", "`").' руб.">';
					echo number_format($row["money"], 2, ".", "`").' руб.';
				echo '</a>';
			echo '</td>';
		echo '</tr>';

		echo '<tr id="hide'.$row["id"].'" style="display: none;"><td align="center" colspan="3"></td></tr>';

		echo '<tr id="load-info'.$row["id"].'" style="display: none;">';
			echo '<td align="center" colspan="3" class="ext-text">';
				echo '<div id="mess-info'.$row["id"].'"></div>';
			echo '</td>';
		echo '</tr>';

	}
}else{
	echo '<tr align="center">';
		echo '<td colspan="3"><b>Статьи не найдены!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
if($count>$perpage) universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op");

?>