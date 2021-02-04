<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Просмотр и редактирование ссылки в платной строке</b></h3>';

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

require("navigator/navigator.php");
$perpage = 20;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_ads_pay_row` WHERE `status`='1'");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

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
	var url = $.trim($("#url").val());
	var description = $.trim($("#description").val());
	$("#info-msg-cab").html("").hide();

	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':op, 'type':type, 'id': id, 'url':url, 'description':description}, 
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
				$("#pr-title"+id).html(message);

				s_h = false; new_id = false;
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function AdsReq(id, type, op) {
	if (op == "Delete" && !confirm("Вы уверены что хотите удалить рекламную площадку ID: "+id+" ?")) {
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

function AddRow(id){
	var table = document.getElementById(id);
	if(table.rows.length < 1) {
		var newrow = table.insertRow();
		newrow.setAttribute("align", "center");
		var newcell = newrow.insertCell();
		newcell.innerHTML = "<b>Реклама не найдена!</b>";

		location.href = "";
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

$sql = $mysqli->query("SELECT * FROM `tb_ads_pay_row` WHERE `status`='1' ORDER BY `id` DESC LIMIT $start_pos, $perpage");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_assoc()) {

		echo '<tr id="adv_dell'.$row["id"].'">';
			echo '<td align="left" style="border-right:solid 1px #DDDDDD; ">';
				echo '<img width="16" height="16" border="0" alt="" title="" style="margin:0; padding:0; padding-bottom:2px; padding-right:5px;" src="http://www.google.com/s2/favicons?domain='.@gethost($row["url"]).'" align="absmiddle" />';
				echo '<a id="pr-title'.$row["id"].'" class="adv" href="'.$row["url"].'" target="_blank" title="'.$row["url"].'">'.$row["description"].'</a><br>';

				echo '<span class="info-text">';
					echo 'ID:&nbsp;<b>'.$row["id"].'</b>, Счет:&nbsp;<b>'.$row["merch_tran_id"].'</b>;&nbsp;&nbsp;Переходов:&nbsp;<b id="a_stat'.$row["id"].'">'.$row["views"].'</b><br>';
					echo 'Способ оплаты: <b>'.$system_pay[$row["method_pay"]].'</b>;&nbsp;&nbsp;';
					echo 'Рекламодатель: '.($row["wmid"]!=false ? ($row["username"]!=false ? "WMID:<b>".$row["wmid"]."</b>, Логин:<b>".$row["username"]."</b>" : "WMID:<b>".$row["wmid"]."</b>") : ($row["username"]!=false ? "Логин:<b>".$row["username"]."</b>" : "<span style=\"color:#CCC;\">не опеределен</span>"));
				echo '</span>';

				echo '<span class="adv-dell" title="Удалить рекламную площадку" onClick="AdsReq('.$row["id"].', \'pay_row\', \'Delete\');"></span>';
				echo '<span class="adv-edit" title="Редактировать рекламную площадку" onClick="LoadInfo('.$row["id"].', \'pay_row\', \'LoadForm\');"></span>';
			echo '</td>';

			echo '<td align="center" width="80" nowrap="nowrap" style="border-left:solid 2px #FFFFFF;">';
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
		echo '<td colspan="2"><b>Реклама не найдена!</b></td>';
	echo '</tr>';
}
echo '</tbody>';
echo '</table>';
if($count>$perpage) universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op");

?>