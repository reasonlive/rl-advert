<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

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

echo '<b>Оплачиваемые задания:</b><br /><br />';

echo '
	<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=add_task"><img src="../../img/add.png" border="0" alt="" align="middle" title="Создать новое задание" /></a>
	<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=add_task" title="Создать новое задание">Создать новое задание</a><br><br>
';

$date_s = strtotime(DATE("d.m.Y"));
$date_v = strtotime(DATE("d.m.Y",(time()-24*60*60)));

echo '<table align="center" border="0" width="100%" cellspacing="2" cellpadding="2" style="border-collapse: collapse; border: 1px solid #000;">';
	echo '<tr bgcolor="#7EC0EE">';
	echo '<th align="center" style="border: 1px solid #000000;" width="80">Статус</th>';
	echo '<th align="center" colspan="2" style="border: 1px solid #000;">Название</th>';
	//echo '<th align="center" style="border: 1px solid #000000;" width="80">Статус</th>';
	echo '<th align="center" style="border: 1px solid #000000;" width="80">Последняя активность</th>';
	echo '<th align="center" style="border: 1px solid #000000;" width="90">Задания</th>';
	echo '<th align="center" style="border: 1px solid #000000;" width="80">Баланс</th>';
	echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `username`='$username' ORDER BY `id` ASC");
if($sql->num_rows>0) {

	$sql_n = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='nacenka_task' AND `howmany`='1'");
	$nacenka_task = $sql_n->fetch_object()->price;

	while ($row = $sql->fetch_array()) {
		echo '<tr id="adv_dell'.$row["id"].'" >';
		echo '<td align="center" width="30" class="noborder1" style="border-right:solid 1px #DDDDDD;">';
			echo '<div id="playpauseimg'.$row["id"].'">';
				if($row["status"]=="wait") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}elseif($row["status"]=="pay") {
					echo '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="pause") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="wait") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="lock") {
					echo '<span class="adv-block" title="Рекламная площадка заблокирована" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\', \'1\');"></span>';
				}
			echo '</div>';
		echo '</td>';
		//echo '<tr>';
		echo '<td align="center" width="45">'.$row["id"].'</td>';
		echo '<td align="left" style="padding:5px;">';
			echo '<table width="100%" border="0">';
			echo '<tr>';
				echo '<td align="left" width="100%">
					<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=task_stat&amp;rid='.$row["id"].'">'.$row["zdname"].'</a><br>
					Рейтинг:&nbsp;<b>'.round($row["reiting"],2).'</b>&nbsp;|&nbsp;Всего проголосовало:&nbsp;<b>'.$row["all_coments"].'</b><br><br>
					[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=edit_task&amp;rid='.$row["id"].'">редактировать</a>]&nbsp;
					[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=addmoney_task&amp;rid='.$row["id"].'">пополнить&nbsp;баланс</a>]&nbsp;';

					if($row["status"]=="wait")
						echo '[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=del_task&amp;rid='.$row["id"].'" onClick=\'if(!confirm("Вы точно хотите удалить задание?")) return false;\'>удалить</a>]';
					elseif($row["status"]=="pause" && $row["date_act"]>(time()-7*24*60*60))
						echo '[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=del_task&amp;rid='.$row["id"].'" onClick=\'alert("Чтобы удалить задание, его необходимо остановить и подождать 7 дней. Если на задание не будет жалоб, то задание можно будет удалить и баланс задания будет переведен на баланс аккаунта."); return false;\'>удалить</a>]';
					elseif($row["status"]=="pay" | $row["date_act"]>(time()-7*24*60*60))
						echo '[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=del_task&amp;rid='.$row["id"].'" onClick=\'alert("Чтобы удалить задание, его необходимо остановить и подождать 7 дней. Если на задание не будет жалоб, то задание можно будет удалить и баланс задания будет переведен на баланс аккаунта."); return false;\'>удалить</a>]';
					else{
						echo '[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=del_task&amp;rid='.$row["id"].'" onClick=\'if(!confirm("Вы точно хотите удалить задание?")) return false;\'>удалить</a>]';
					}
				echo '</td>';

				if($row["zdre"]==3)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 3 часа" /></td>';
				elseif($row["zdre"]==6)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 6 часов" /></td>';
				elseif($row["zdre"]==12)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 12 часов" /></td>';
				elseif($row["zdre"]==24)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 24 часа (1 сутки)" /></td>';
				elseif($row["zdre"]==48)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 48 часов (2-е суток)" /></td>';
				elseif($row["zdre"]==72)
					echo '<td><img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые 72 часа (3-е суток)" /></td>';
				else{
					echo '';
				}

				echo '<td>';
					if( $row["date_up"] < (time() - 1*60*60) ) {
						echo '<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=up_task&amp;rid='.$row["id"].'"><img src="../../img/up_task.png" border="0" alt="Задание было поднято '.DATE("d.m.Yг. в H:i:s", $row["date_up"]).'" align="middle" title="Задание было поднято '.DATE("d.m.Yг. в H:i:s", $row["date_up"]).'" /></a>';
					}else{
						echo '<img src="../../img/up_task.png" border="0" alt="Задание было поднято '.DATE("d.m.Yг. в H:i:s", $row["date_up"]).'" align="middle" title="Задание было поднято '.DATE("d.m.Yг. в H:i:s", $row["date_up"]).'" />';
					}
				echo '</td>';

			echo '</tr>';
			echo '</table>';
		echo '</td>';

		/*if($row["status"]=="end")
			echo '<td align="center"><span style="color: #FF0000;">Не активно</span><br>[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=active_task&amp;rid='.$row["id"].'">Запустить</a>]</td>';
		elseif($row["status"]=="pause" | $row["status"]=="wait")
			echo '<td align="center"><span style="color: #FF0000;">Не активно</span><br>[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=active_task&amp;rid='.$row["id"].'">Запустить</a>]</td>';
		elseif($row["status"]=="pay")
			echo '<td align="center"><span style="color: #006400;">Активно</span><br>[<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=pause_task&amp;rid='.$row["id"].'">Пауза</a>]</td>';
		else {echo '<td></td>';}*/

		echo '<td align="center">'.DATE("d.m.Y",$row["date_act"]).'<br>'.DATE("H:i",$row["date_act"]).'</td>';
		echo '<td align="center"><a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=task_get&amp;rid='.$row["id"].'">Выполнено:&nbsp;'.$row["goods"].'</a><br>Осталось:&nbsp;'.$row["totals"].'<br><a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page=task_mod&amp;rid='.$row["id"].'">Заявок:&nbsp;'.$row["wait"].'</td>';
		echo '<td align="center">'.number_format(( ($row["totals"] * $row["zdprice"]) * (100 + $nacenka_task) / 100 ),2,".","'").' руб.</td>';
		echo '</tr>';
	}
}else{
	echo '<tr><td align="center" colspan="6">Созданных заданий нет!</td></tr>';
}
echo '</table>';

echo '<br><br><b style="color:#FF0000;">*</b> - Поднятие задания возможно 1 раз в час. Стоимость одного поднятия 0.25р.<br>';

?>