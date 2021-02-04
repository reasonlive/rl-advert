<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление оплачиваемых тестов</b></h3>';

$mysqli->query("UPDATE `tb_ads_tests` SET `status`='3' WHERE `status`>'0' AND `balance`<`cena_advs`") or die($mysqli->error);
$mysqli->query("UPDATE `tb_ads_tests` SET `status`='1' WHERE `status`='3' AND `balance`>=`cena_advs`") or die($mysqli->error);

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_hit' AND `howmany`='1'");
$tests_cena_hit = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_nacenka' AND `howmany`='1'");
$tests_nacenka = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_min_pay' AND `howmany`='1'");
$tests_min_pay = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_quest' AND `howmany`='1'");
$tests_cena_quest = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_color' AND `howmany`='1'");
$tests_cena_color = number_format($sql->fetch_object()->price, 4, ".", "");

for($i=1; $i<=4; $i++) {
	$tests_cena_revisit[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_revisit' AND `howmany`='$i'");
	$tests_cena_revisit[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

for($i=1; $i<=2; $i++) {
	$tests_cena_unic_ip[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_unic_ip' AND `howmany`='$i'");
	$tests_cena_unic_ip[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

?><script type="text/javascript" language="JavaScript">
function ClearForm() {
	$("#title").val("");
	$("#description").val("");
	$("#url").val("");
	$("#money_add").val("100");
	$("#revisit").val("0");
	$("#color").val("0");
	$("#unic_ip_user").val("0");
	$("#date_reg_user").val("0");
	$("#sex_user").val("0");

	$("#quest1").val(""); $("#quest2").val(""); $("#quest3").val(""); $("#quest5").val(""); $("#quest5").val("");
	$("#answ11").val(""); $("#answ12").val(""); $("#answ13").val("");
	$("#answ21").val(""); $("#answ22").val(""); $("#answ23").val("");
	$("#answ31").val(""); $("#answ32").val(""); $("#answ33").val("");
	$("#answ41").val(""); $("#answ42").val(""); $("#answ43").val("");
	$("#answ51").val(""); $("#answ52").val(""); $("#answ53").val("");

	$("#block_quest4").attr("style", "display:none; margin:0;");
	$("#block_quest5").attr("style", "display:none; margin:0;");

	SetChecked();
	PlanChange();
}

function ShowHideBlock(id) {
	if (gebi("adv-title"+id).className == 'adv-title-open') {
		gebi("adv-title"+id).className = 'adv-title-close';
	} else {
		gebi("adv-title"+id).className = 'adv-title-open';
	}
	$("#adv-block"+id).slideToggle("fast");
}

function SetChecked(type){
	var nodes = document.getElementsByTagName("input");
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == "country[]") {
			if(type == "paste") nodes[i].checked = true;
			else  nodes[i].checked = false;
		}
	}
}

function add_quest() {
	if (gebi("block_quest4").style.display == 'none') {
		$("#block_quest4").fadeIn("fast", function(){
			$("#quest4").val(""); $("#answ41").val(""); $("#answ42").val(""); $("#answ43").val("");
			if(gebi("block_quest5").style.display == '' | gebi("block_quest5").style.display == 'block') $("#block_add_quest").hide();
			$("#block_quest4").show();
			PlanChange();
		});
	} else if (gebi("block_quest5").style.display == 'none') {
		$("#block_quest5").fadeIn("fast", function(){
			$("#quest5").val(""); $("#answ51").val(""); $("#answ52").val(""); $("#answ53").val("");
			if(gebi("block_quest4").style.display == '' | gebi("block_quest4").style.display == 'block') $("#block_add_quest").hide();
			$("#block_quest5").show();
			PlanChange();
		});
	}
}

function del_quest() {
	if (gebi("block_quest5").style.display == '' | gebi("block_quest5").style.display == 'block') {
		$("#block_quest5").fadeOut("fast", function(){
			$("#quest5").val(""); $("#answ51").val(""); $("#answ52").val(""); $("#answ53").val("");
			$("#block_quest5").hide();
			$("#block_add_quest").show();
			PlanChange();
		});
	} else if (gebi("block_quest4").style.display == '' | gebi("block_quest4").style.display == 'block') {
		$("#block_quest4").fadeOut("fast", function(){
			$("#quest4").val(""); $("#answ41").val(""); $("#answ42").val(""); $("#answ43").val("");
			$("#block_quest4").hide();
			$("#block_add_quest").show();
			PlanChange();
		});
	}
}

function PlanChange(){
	var revisit = $.trim($("#revisit").val());
	var color = $.trim($("#color").val());
	var unic_ip_user = $.trim($("#unic_ip_user").val());
	var money_add = $.trim($("#money_add").val());

	money_add = str_replace(",", ".", money_add);
	money_add = money_add.match(/(\d+(\.)?(\d){0,2})?/);
	money_add = money_add[0] ? money_add[0] : '';

	$("#money_add").val(money_add);
	money_add = number_format(money_add, 2, ".", "");

	var uprice = <?=number_format(($tests_cena_hit/(1 + $tests_nacenka/100)), 4, ".", "");?>;
	var rprice = <?=$tests_cena_hit;?>;

	if (gebi("block_quest4").style.display == '' | gebi("block_quest4").style.display == 'block') {
		uprice += <?=number_format(($tests_cena_quest/(1 + $tests_nacenka/100)), 5, ".", "");?>;
		rprice += <?=$tests_cena_quest;?>;
	}
	if (gebi("block_quest5").style.display == '' | gebi("block_quest5").style.display == 'block') {
		uprice += <?=number_format(($tests_cena_quest/(1 + $tests_nacenka/100)), 5, ".", "");?>;
		rprice += <?=$tests_cena_quest;?>;
	}

	if (color == 1) rprice += <?=$tests_cena_color;?>;

	if (revisit == 1) {
		rprice += <?=$tests_cena_revisit[1];?>;
	} else if (revisit == 2) {
		rprice += <?=$tests_cena_revisit[2];?>;
	} else if (revisit == 3) {
		rprice += <?=$tests_cena_revisit[3];?>;
	} else if (revisit == 4) {
		rprice += <?=$tests_cena_revisit[4];?>;
	}
	if (unic_ip_user == 1) {
		rprice += <?=$tests_cena_unic_ip[1];?>;
	} else if (unic_ip_user == 2) {
		rprice += <?=$tests_cena_unic_ip[2];?>;
	}

	count_test = parseFloat((money_add*10000)/(rprice*10000));

	$("#price_user").html('<span style="color:#228B22;">' + number_format(uprice, 4, ".", " ") + ' руб.</span>');
	$("#price_one").html('<span style="color:#0000FF;">' + number_format(rprice, 4, ".", " ") + ' руб.</span>');
	$("#count_test").html('<span style="color:#FF0000;">' + Math.floor(count_test) + '</span>');
}

function SaveAds(id, type) {
	var title = $.trim($("#title").val());
	var description = $.trim($("#description").val());
	var url = $.trim($("#url").val());
	var revisit = $.trim($("#revisit").val());
	var color = $.trim($("#color").val());
	var unic_ip_user = $.trim($("#unic_ip_user").val());
	var date_reg_user = $.trim($("#date_reg_user").val());
	var sex_user = $.trim($("#sex_user").val());
	var country = $('input[id="country[]"]:checked').map(function(){return $(this).val();}).get();
	var method_pay = $.trim($("#method_pay").val());
	var money_add = $.trim($("#money_add").val());

	money_add = str_replace(",", ".", money_add);
	money_add = money_add.match(/[+]?(\d+(\.)?(\d){0,2})?/);
	money_add = money_add[0] ? money_add[0] : '';
	money_add = number_format(money_add, 2, ".", "");

	var quest1 = $.trim($("#quest1").val()); var answ11 = $.trim($("#answ11").val()); var answ12 = $.trim($("#answ12").val()); var answ13 = $.trim($("#answ13").val());
	var quest2 = $.trim($("#quest2").val()); var answ21 = $.trim($("#answ21").val()); var answ22 = $.trim($("#answ22").val()); var answ23 = $.trim($("#answ23").val());
	var quest3 = $.trim($("#quest3").val()); var answ31 = $.trim($("#answ31").val()); var answ32 = $.trim($("#answ32").val()); var answ33 = $.trim($("#answ33").val());
	var quest4 = $.trim($("#quest4").val()); var answ41 = $.trim($("#answ41").val()); var answ42 = $.trim($("#answ42").val()); var answ43 = $.trim($("#answ43").val());
	var quest5 = $.trim($("#quest5").val()); var answ51 = $.trim($("#answ51").val()); var answ52 = $.trim($("#answ52").val()); var answ53 = $.trim($("#answ53").val());

	if (title == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали заголовок теста!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (description == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не описали инструкцию к выполнению теста!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if ((url == '') | (url == 'http://') | (url == 'https://')) {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали URL-адрес сайта!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (quest1 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали первый вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (answ11 == "" | answ12 == "" | answ13 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали варианты ответа на первый вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (quest2 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали второй вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (answ21 == "" | answ22 == "" | answ23 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали варианты ответа на второй вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (quest3 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали третий вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (answ31 == "" | answ32 == "" | answ33 == "") {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Вы не указали варианты ответа на третий вопрос!</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else if (money_add < <?=$tests_min_pay;?>) {
		$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
		$("#info-msg-cab").show().html('<span class="msg-error">Минимальная сумма пополнения - <?=$tests_min_pay;?> руб.</span>');
		setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 3000);
		return false;
	} else {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: {
				'op':'Add', 
				'type':type, 
				'title':title, 'description':description, 'url':url, 
				'quest1':quest1, 'answ11':answ11, 'answ12':answ12, 'answ13':answ13, 
				'quest2':quest2, 'answ21':answ21, 'answ22':answ22, 'answ23':answ23, 
				'quest3':quest3, 'answ31':answ31, 'answ32':answ32, 'answ33':answ33, 
				'quest4':quest4, 'answ41':answ41, 'answ42':answ42, 'answ43':answ43, 
				'quest5':quest5, 'answ51':answ51, 'answ52':answ52, 'answ53':answ53, 
				'revisit':revisit, 'color':color, 'unic_ip_user':unic_ip_user, 'date_reg_user':date_reg_user, 'sex_user':sex_user, 
				'country[]':country, 'money_add':money_add
			}, 
			dataType: 'json',
			error: function() {
				$("#loading").slideToggle();
				$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
				$("#info-msg-cab").show().html('<span class="msg-error">Ошибка обработки данных ajax/json!</span>');
				setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 10000);
				return false;
			}, 

			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) {
				$("#loading").slideToggle();
				$("#info-msg-cab").hide().html("");
//alert(data);
				if (data.result == "OK") {
					$("#OrderForm").html(data.message);
					$("#BlockForm").slideToggle("slow");
					$("#OrderForm").slideToggle("slow");
					$("html, body").animate({scrollTop: $("html, body").offset().top-10}, 700);
					return false;
				} else {
					if(data.message) {
						$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
						$("#info-msg-cab").show().html('<span class="msg-error">' + data.message + '</span>');
						setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 5000);
						return false;
					} else if(data) {
						$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
						$("#info-msg-cab").show().html('<span class="msg-error">' + data + '</span>');
						setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 5000);
						return false;
					} else {
						$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
						$("#info-msg-cab").show().html('<span class="msg-error">Ошибка обработки данных!</span>');
						setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 5000);
						return false;
					}
				}
			}
		});
	}
}

function PayAds(id) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: {'op':'Start', 'type':'tests', 'id':id}, 
		dataType: 'json',
		error: function() {
			$("#loading").slideToggle();
			$("html, body").animate({scrollTop: $("#info-msg-pay").offset().top+10000}, 700);
			$("#info-msg-pay").show().html('<span class="msg-error">Ошибка обработки данных ajax/json!</span>');
			setTimeout(function() {var tm; hidemsg("info-msg-pay");}, 10000);
			return false;
		}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 

		success: function(data) {
			$("#loading").slideToggle();

			if (data.result == "OK") {
				$("html, body").animate({scrollTop: $("html, body").offset().top-10}, 700);
				$("#OrderForm").hide().html('<span class="msg-ok">' + data.message + '</span>');
				$("#OrderForm").slideToggle("slow");
				setTimeout(function() {location.replace("");}, 5000);
			}else{
				if(data.message) {
					$("html, body").animate({scrollTop: $("#info-msg-pay").offset().top+10000}, 700);
					$("#info-msg-pay").show().html('<span class="msg-error">' + data.message + '</span>');
					setTimeout(function() {var tm; hidemsg("info-msg-pay");}, 5000);
					return false;
				} else if(data) {
					$("html, body").animate({scrollTop: $("#info-msg-pay").offset().top+10000}, 700);
					$("#info-msg-pay").show().html('<span class="msg-error">' + data + '</span>');
					setTimeout(function() {var tm; hidemsg("info-msg-pay");}, 5000);
					return false;
				} else {
					$("html, body").animate({scrollTop: $("#info-msg-pay").offset().top+10000}, 700);
					$("#info-msg-pay").show().html('<span class="msg-error">Ошибка обработки данных!</span>');
					setTimeout(function() {var tm; hidemsg("info-msg-pay");}, 5000);
					return false;
				}
			}
		}
	});

}

function DeleteAds(id) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: {'op':'Delete', 'type':'tests', 'id':id}, 
		dataType: 'json',
		error: function() {
			$("#loading").slideToggle();
			$("html, body").animate({scrollTop: $("#Save").offset().top+10000}, 700);
			$("#info-msg-cab").show().html('<span class="msg-error">Ошибка обработки данных ajax/json!</span>');
			setTimeout(function() {var tm; hidemsg("info-msg-cab");}, 10000);
			return false;
		}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 

		success: function(data) {
			$("#loading").slideToggle();

			if (data.result == "OK") {
				$("html, body").animate({scrollTop: $("html, body").offset().top-10}, 700);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				ClearForm();
			}else{
				alert("Ошибка! Возможно тест уже был удален.");
			}
		}
	});
}

function ChangeAds() {
	$("#info-msg-cab").hide().html("");
	$("#loading").slideToggle();
	$("#BlockForm").slideToggle("slow");
	$("#OrderForm").slideToggle("slow");
	$("html, body").animate({scrollTop: $("html, body").offset().top-10}, 700);
	$("#loading").slideToggle();
	return false;
}

function CtrlEnter(event) {
	var event = event || window.event;
	if( ( (event.ctrlKey) && ((event.keyCode == 0xA) || (event.keyCode == 0xD)) ) ) {
		gebi("Save").click();
	}
}
</script><?php

echo '<div id="BlockForm" style="display:block;">';
echo '<div id="newform" onkeypress="CtrlEnter(event);">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr>';
		echo '<th align="center" class="top">Параметр</th>';
		echo '<th align="center" class="top" colspan="2">Значение</th>';
	echo '</thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Заголовок теста</b></td>';
		echo '<td align="left"><input type="text" id="title" maxlength="60" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint1" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="3"><b>Инструкции для тестирования &darr;</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'description\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:7px;">Осталось символов: 1000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:120px; width:99%;" onKeyup="descchange(\'1\', this, \'1000\');" onKeydown="descchange(\'1\', this, \'1000\');" onClick="descchange(\'1\', this, \'1000\');"></textarea>';
			echo '</div>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="url" maxlength="300" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hinturl" class="hint-quest"></span></td>';
	echo '</tr>';

	for($i=1; $i<=3; $i++){
		echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Вопрос №'.$i.'</td></tr>';
		echo '<tr align="left">';
			echo '<td align="left" width="220"><b>Содержание вопроса</b></td>';
			echo '<td align="left"><input type="text" id="quest'.$i.'" maxlength="300" value="" class="ok"></td>';
			if($i==1) {
				echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"><span id="hint3" class="hint-quest"></span></td>';
			}else{
				echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"></td>';
			}
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #009125;">(правильный)</span></td>';
			echo '<td align="left"><input type="text" id="answ'.$i.'1" maxlength="30" value="" class="ok" style="color: #009125;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
			echo '<td align="left"><input type="text" id="answ'.$i.'2" maxlength="30" value="" class="ok" style="color: #FF0000;"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
			echo '<td align="left"><input type="text" id="answ'.$i.'3" maxlength="30" value="" class="ok" style="color: #FF0000;"></td>';
		echo '</tr>';
	}
	echo '</table>';

	for($i=4; $i<=5; $i++){
		echo '<div id="block_quest'.$i.'" style="display:none;">';
			echo '<table class="tables" style="margin:0;">';
			echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Дополнительный вопрос</td></tr>';
			echo '<tr>';
				echo '<td align="left" width="220"><b>Содержание вопроса</b></td>';
				echo '<td align="left"><input type="text" id="quest'.$i.'" maxlength="300" value="" class="ok"></td>';
				echo '<td align="center" width="16" rowspan="4" style="background: #EDEDED;"><img src="/img/error2.gif" onClick="del_quest();" style="float: none; width:14px; cursor:pointer; margin:0; padding:0" title="Удалить вопрос"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="left">Вариант ответа <span style="color: #009125;">(правильный)</span></td>';
				echo '<td align="left"><input type="text" id="answ'.$i.'1" maxlength="30" value="" class="ok" style="color: #009125;"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
				echo '<td align="left"><input type="text" id="answ'.$i.'2" maxlength="30" value="" class="ok" style="color: #FF0000;"></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
				echo '<td align="left"><input type="text" id="answ'.$i.'3" maxlength="30" value="" class="ok" style="color: #FF0000;"></td>';
			echo '</tr>';
			echo '</table>';
		echo '</div>';
	}

	echo '<div id="block_add_quest" style="display:block;">';
		echo '<table class="tables" style="margin:0; padding:0;">';
		echo '<tr><td align="center" style="padding: 3px 0;">';
			echo '<span class="sub-click" onClick="add_quest();" style="padding-left:40px; padding-right:40px;">Добавить ещё вопрос</span>';
		echo '</td></tr>';
		echo '</table>';
	echo '</div>';

	echo '<span id="adv-title1" class="adv-title-close" onclick="ShowHideBlock(1);">Дополнительные настройки</span>';
	echo '<div id="adv-block1" style="display:none;">';
	echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<tr>';
		echo '<td align="left" width="220">Технология тестирования</td>';
		echo '<td align="left">';
			echo '<select id="revisit" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0" selected="selected">Доступно всем каждые 24 часа</option>';
				echo '<option value="1">Доступно всем каждые 3 дня</option>';
				echo '<option value="2">Доступно всем каждую неделю</option>';
				echo '<option value="3">Доступно всем каждые 2 недели</option>';
				echo '<option value="4">Доступно всем каждый месяц</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Выделить тест</td>';
		echo '<td align="left">';
			echo '<select id="color" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">Да</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint5" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Уникальный IP</td>';
		echo '<td align="left">';
			echo '<select id="unic_ip_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">Да, 100% совпадение</option>';
				echo '<option value="2">Усиленный по маске до 2 чисел (255.255.X.X)</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint6" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">По дате регистрации</td>';
		echo '<td align="left">';
			echo '<select id="date_reg_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Все пользователи проекта</option>';
				echo '<option value="1">До 7 дней с момента регистрации</option>';
				echo '<option value="2">От 7 дней с момента регистрации</option>';
				echo '<option value="3">От 30 дней с момента регистрации</option>';
				echo '<option value="4">От 90 дней с момента регистрации</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint7" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">По половому признаку</td>';
		echo '<td align="left">';
			echo '<select id="sex_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Все пользователи проекта</option>';
				echo '<option value="1">Только мужчины</option>';
				echo '<option value="2">Только женщины</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint8" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';

	echo '<span id="adv-title2" class="adv-title-close" onclick="ShowHideBlock(2);">Настройки геотаргетинга</span>';
	echo '<div id="adv-block2" style="display:none;">';
	echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<tr>';
		echo '<td colspan="2" align="center" style="border-right:none;"><a onclick="SetChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
		echo '<td colspan="2" align="center" style="border-left:none;"><a onclick="SetChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
	echo '</tr>';
	include(DOC_ROOT."/advertise/func_geotarg.php");
	echo '</table>';
	echo '</div>';

	echo '<table class="tables" style="margin:0; padding:0;">';
	echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Пополнение</td></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Сумма пополнения</b></td>';
		echo '<td align="left"><input type="text" id="money_add" maxlength="11" value="100" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeydowm="PlanChange();" onKeyup="PlanChange();">&nbsp;&nbsp;(минимум - '.$tests_min_pay.' руб.)</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint11" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="23px"><b>Вознаграждение</b></td>';
		echo '<td align="left" id="price_user"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint12" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="23px"><b>Цена одного теста</b></td>';
		echo '<td align="left" id="price_one"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint13" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="23px"><b>Количество выполнений</b></td>';
		echo '<td align="left" id="count_test"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint14" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '</table>';
echo '</div>';

echo '<br>';
echo '<div id="info-msg-cab" style="display:none;"></div>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'tests\');" class="sub-blue160" style="float:none; width:160px;">Создать тест</span></div>';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-pay" style="display:none;"></div>';

?>
<script language="JavaScript">ClearForm();</script>