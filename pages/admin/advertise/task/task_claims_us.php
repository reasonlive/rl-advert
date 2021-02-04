<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
require($_SERVER["DOCUMENT_ROOT"]."/merchant/func_mysql.php");
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр жалоб на исполнителей задания</b></h1>';

//$mysqli->query("UPDATE `tb_ads_task` SET `status`='3', `date`='".time()."' WHERE `status`>'0' AND `status`<'3' AND ( (`totals`<'1' )") or die(mysql_error());

echo '<div id="LoadModalClaimsSurf" style="display:none;"></div>';

?><script type="text/javascript" language="JavaScript">
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
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json',
			beforeSend: function() { $("#loading").slideToggle(); }, 

			error: function(request, status, errortext) {
				$("#loading").slideToggle();
				new_id = id; s_h = id + op;

				var error = new Array();
				error["rState"] = request.readyState!==false ? request.readyState : false;
				error["rText"]  = request.responseText!=false ? request.responseText : errortext;
				error["status"] = request.status!==false ? request.status : false;
				error["statusText"] = request.statusText!==false ? request.statusText : false;

				$("#load-info"+id).show(); 
				$("#mess-info"+id).html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			},

			success: function(data) { 
				$("#loading").slideToggle();

				if(op == "GoDel") {
					new_id = id; s_h = id;
				}else{
					new_id = id; s_h = id + op;
				}

				$("#load-info"+id).show();

				if (data.result == "OK") {
					if(data.message) { $("#mess-info"+id).html(data.message); }
					else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
				} else {
					if(data.message) { $("#mess-info"+id).html('<span class="msg-error">' + data.message + '</span>'); }
					else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
				}
			}
		});
	}
	return false;
}

function DelAdv(id, type, op) {
	if (confirm("Удалить рекламную площадку ID:"+id+" ?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#adv_dell"+id).remove();
					$("#hide"+id).remove();
					$("#load-info"+id).remove();
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function DelClaims(id, type, op) {
	if (confirm("Удалить жалобу?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#claims-"+id).remove();
					if(data.count_claims==0) {
						$("#adv_dell"+data.ident).remove();
						$("#LoadModalClaimsSurf").modalpopup('close');
					}else{
						$("#countclaims-"+data.ident).html(data.count_claims);
					}
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function DelAllClaims(id, type, op) {
	if (confirm("Удалить все жалобы?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#adv_dell"+id).remove();
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function ViewClaims(id, type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		success: function(data) {
			if (data.result=="OK" && data.message) {
				$("#LoadModalClaimsSurf").html(data.message).show();
				StartModal();
				WinHeight = Math.ceil($.trim($(window).height()));
				ModalHeight = Math.ceil($.trim($("#table-content").height()));
				if((ModalHeight+100) >= WinHeight) ModalHeight = WinHeight-100;
				$("#table-content").css("height", ModalHeight+"px");
			}else{
				alert(data.message);
			}
		}
	});
}

function StartModal() {
	$("#LoadModalClaimsSurf").modalpopup({
		closeOnEsc: true,
		closeOnOverlayClick: false,
		beforeClose: function(data, el) {
			$("#LoadModalClaimsSurf").hide();
			return true;
		}
	});
}

function PlayPause(id, type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
		success: function(data) { 
			$("#loading").slideToggle();
			if (data.result == "OK") { 
				$("#playpauseimg"+id).html(data.status);
				if(data.message) { alert(data.message); }
			} else { 
				if(data.message) { alert(data.message); }
				else { alert("Ошибка обработки данных!"); return false; }
			}
			return false;
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
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id, 'msg_lock':msg_lock }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				if (data.result == "OK") {
					$("#load-info"+id).hide();
					$("#mess-info"+id).html("");
					$("#playpauseimg"+id).html('<span class="adv-block" title="Рекламная площадка заблокирована" onClick="PlayPause('+id+', \'task\', \'PlayPause\', \'1\');"></span>');
					$("#lock-"+id).attr({class: "adv-unlock", title: "Просмотр информации о блокировки"});
					s_h = false; new_id = false;

					//if(data.message) { alert(data.message); }
				} else {
					if(data.message) { alert(data.message); }
					else { alert("Ошибка обработки данных!"); return false; }
				}
			}
		});
	}
}
</script><?php

require("navigator/navigator.php");
$perpage = 20;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_task_pay` WHERE `status`='bad' AND `user_id` IN (SELECT `ident` FROM `tb_ads_claims` WHERE `type`='task_us') ");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

if($count>$perpage) {echo "<br>"; universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op");}

echo '<table class="adv-cab" style="margin:1px auto;">';
echo '<tr align="center">';
	echo '<th>ID</th>';
	echo '<th>Информация</th>';
	echo '<th>Кол-во жалоб</th>';
	echo '<th>Действия</th>';
echo '</tr>';
$sql = $mysqli->query("SELECT * FROM `tb_ads_task_pay` WHERE `status`='bad' AND `user_id` IN (SELECT `ident` FROM `tb_ads_claims` WHERE `type`='task_us') ORDER BY `id` DESC LIMIT $start_pos,$perpage ");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_assoc()) {
		echo '<tr id="adv_dell'.$row["id"].'" align="center">';
		echo '<td style="border-left:solid 2px #FFFFFF;">'.$row["user_id"].'</td>';
		echo '<td align="ctnter">';
			echo 'Исполнитель: Задания №: <b>'.$row["ident"].'</b><br/>Логин: <b>'.$row["user_name"].'</b>';
		echo '</td>';
		echo '<td><span id="countclaims-'.$row["user_id"].'">'.$row["claims"].'</td>';
		echo '<td width="100">';
			echo '<span class="view-claims" title="Просмотр жалоб" onClick="ViewClaims(\''.$row["user_id"].'\', \'task_us\', \'ViewClaims\');"></span>';
		echo '</td>';
		echo '</tr>';
		echo '<tr id="hide'.$row["user_id"].'" style="display: none;"><td align="center" colspan="3"></td></tr>';
		echo '<tr id="load-info'.$row["user_id"].'" style="display: none;">';
			echo '<td align="center" colspan="6" class="ext-text">';
				echo '<div id="mess-info'.$row["user_id"].'"></div>';
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr align="center">';
		echo '<td colspan="6"><b>Жалоб на задания нет</b></td>';
	echo '</tr>';
}
echo '</table>';

if($count>$perpage) {universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $perpage, 10, '&page=', "?op=$op"); echo "<br>";}
?>