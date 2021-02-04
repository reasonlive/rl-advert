<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_hit' AND `howmany`='1'");
$tests_cena_hit = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_comis_del' AND `howmany`='1'");
$tests_comis_del = number_format($sql->fetch_object()->price, 0, ".", "");

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
?>

<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>

<script type="text/javascript" language="JavaScript">

$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Заголовок теста</b> - максимум 55 символов.<br>Заголовок должен быть коротким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей.<br>Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела.'
	});
	$("#hint2").simpletip({
		fixed: true, position: ["-622", "-45"], focus: false,
		content: '<b>Инструкция для тестирования</b> - максимум 1000 символов.<br>Напишите, что необходимо предпринять прежде чем проходить тестирование, например просмотреть ваш сайт или прочитать статью. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела. Уважайте читателей!'
	});
	$("#hint3").simpletip({
		fixed: true, position: ["-622", "-30"], focus: false,
		content: '<b>Тест предусматривает три вопроса с тремя вариантами ответа каждый</b>.<br>Первый вариант всегда должен быть правильным. За дополнительную плату можно использовать ещё два дополнительных вопроса. Максимальное количество символов в вопросе - 300, в ответе - 30.'
	});
	$("#hint4").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Технология тестирования</b> - существует <b>пять</b> способов предоставления тестирования пользователям:<br><b>Доступно всем каждые 24 часа</b> - это значит, что один пользователь сможет пройти ваш тест один раз в сутки.<br><b>Доступно всем каждые 3 дня</b> - это значит, что один пользователь сможет пройти ваш тест один раз в 3 дня.<br><b>Доступно всем каждую неделю</b> - это значит, что один пользователь сможет пройти ваш тест один раз в неделю.<br><b>Доступно всем каждые 2 недели</b> - это значит, что один пользователь сможет пройти ваш тест один раз в 2 недели.<br><b>Доступно всем каждый месяц</b> - это значит, что один пользователь сможет пройти ваш тест один раз в месяц.'
	});
	$("#hint5").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Выделить тест</b> - Ваш тест будет в верхней части списка и <b style="color:red;">выделен красным цветом</b> за дополнительную плату.'
	});
	$("#hint6").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Уникальный IP</b> - Вы можете ограничить выполнение вашего теста при полном совпадении IP или по маске до 2 чисел (255.255.X.X)'
	});
	$("#hint7").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>По дате регистрации</b> - Вы можете ограничить показ вашей рекламы, например, разрешив показывать ее только новичкам, которые зарегистрировались не позднее 7-ми дней назад.'
	});
	$("#hint8").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>По половому признаку</b> - Вы можете ограничить показ своей рекламы по половому признаку пользователей, разрешив показ своей рекламы только пользователям мужского либо женского пола.'
	});
	$("#hint9").simpletip({
		fixed: true, position: ["-622", "-45"], focus: false,
		content: '<b>Геотаргетинг по странам</b> - выполнение тестов будет доступно пользователям из указанных стран, так же вы можете исключить одну или несколько стран'
	});
	$("#hint10").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Способ оплаты</b> - выберите наиболее подходящий для вас способ оплаты заказа.'
	});
	$("#hint11").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Сумма пополнения</b> - укажите сумму, которую вы хотите внести в бюджет рекламной площадки.'
	});
	$("#hint12").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Вознаграждение</b> - оплата пользователю за успешное прохождение теста.'
	});
	$("#hint13").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Цена одного теста</b> - стоимость за одно выполнение теста.'
	});
	$("#hint14").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>Количество выполнений</b> - количество успешных прохождений теста, которое получит рекламная площадка.'
	});
	$("#hinturl").simpletip({
		fixed: true, position: ["-622", "23"], focus: false,
		content: '<b>URL-адрес сайта</b> - должен начинаться с http:// или https:// и содержать не более 300 символов.<br>Не используйте HTML-теги и Java-скрипты. За попытки взлома системы, наказание - удаление аккаунта.'
	});
})

function gebi(id){
	return document.getElementById(id)
}

function ClearForm() {
	gebi("title").value = "";
	gebi("description").value = "";
	gebi("url").value = "";
	gebi("quest1").value = "";
	gebi("quest2").value = "";
	gebi("quest3").value = "";
	gebi("quest4").value = "";
	gebi("quest5").value = "";
	gebi("answ11").value = "";
	gebi("answ12").value = "";
	gebi("answ13").value = "";
	gebi("answ21").value = "";
	gebi("answ22").value = "";
	gebi("answ23").value = "";
	gebi("answ31").value = "";
	gebi("answ32").value = "";
	gebi("answ33").value = "";
	gebi("answ41").value = "";
	gebi("answ42").value = "";
	gebi("answ43").value = "";
	gebi("answ51").value = "";
	gebi("answ52").value = "";
	gebi("answ53").value = "";
	gebi("block_quest4").style.display = 'none';
	gebi("block_quest5").style.display = 'none';
	gebi("money_add").value = 100;
	gebi("revisit").value = 0;
	gebi("color").value = 0;
	gebi("unic_ip_user").value = 0;
	gebi("date_reg_user").value = 0;
	gebi("sex_user").value = 0;
	gebi("method_pay").value = 1;
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
			gebi("quest4").value = '';
			gebi("answ41").value = '';
			gebi("answ42").value = '';
			gebi("answ43").value = '';
			if (gebi("block_quest5").style.display == '') gebi("block_add_quest").style.display = 'none';
			gebi("block_quest4").style.display = '';
			PlanChange();
		});
	} else if (gebi("block_quest5").style.display == 'none') {
		$("#block_quest5").fadeIn("fast", function(){
			gebi("quest5").value = '';
			gebi("answ51").value = '';
			gebi("answ52").value = '';
			gebi("answ53").value = '';
			if (gebi("block_quest4").style.display == '') gebi("block_add_quest").style.display = 'none';
			gebi("block_quest5").style.display = '';
			PlanChange();
		});
	}
}

function del_quest() {
	if (gebi("block_quest5").style.display == '') {
		$("#block_quest5").fadeOut("fast", function(){
			gebi("quest5").value = '';
			gebi("answ51").value = '';
			gebi("answ52").value = '';
			gebi("answ53").value = '';
			gebi("block_quest5").style.display = 'none';
			gebi("block_add_quest").style.display = '';
			PlanChange();
		});
	} else if (gebi("block_quest4").style.display == '') {
		$("#block_quest4").fadeOut("fast", function(){
			gebi("quest4").value = '';
			gebi("answ41").value = '';
			gebi("answ42").value = '';
			gebi("answ43").value = '';
			gebi("block_quest4").style.display = 'none';
			gebi("block_add_quest").style.display = '';
			PlanChange();
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

function descchange(id, elem, count_s) {
	if (elem.value.length > count_s) { elem.value = elem.value.substr(0,count_s); }
	$("#count"+id).html("Осталось символов: " +(count_s-elem.value.length));
}

function number_format(number, decimals, dec_point, thousands_sep) {
	var minus = "";
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	if(number < 0){
		minus = "-";
		number = number*-1;
	}
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function(n, prec) {
		var k = Math.pow(10, prec);
		return '' + (Math.round(n * k) / k).toFixed(prec);
	};
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return minus + s.join(dec);
}

function str_replace(search, replace, subject) {
	return subject.split(search).join(replace);
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

	if (gebi("block_quest4").style.display == '') {
		uprice += <?=number_format(($tests_cena_quest/(1 + $tests_nacenka/100)), 5, ".", "");?>;
		rprice += <?=$tests_cena_quest;?>;
	}
	if (gebi("block_quest5").style.display == '') {
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

	var quest1 = $.trim($("#quest1").val());
	var answ11 = $.trim($("#answ11").val());
	var answ12 = $.trim($("#answ12").val());
	var answ13 = $.trim($("#answ13").val());

	var quest2 = $.trim($("#quest2").val());
	var answ21 = $.trim($("#answ21").val());
	var answ22 = $.trim($("#answ22").val());
	var answ23 = $.trim($("#answ23").val());

	var quest3 = $.trim($("#quest3").val());
	var answ31 = $.trim($("#answ31").val());
	var answ32 = $.trim($("#answ32").val());
	var answ33 = $.trim($("#answ33").val());

	var quest4 = $.trim($("#quest4").val());
	var answ41 = $.trim($("#answ41").val());
	var answ42 = $.trim($("#answ42").val());
	var answ43 = $.trim($("#answ43").val());

	var quest5 = $.trim($("#quest5").val());
	var answ51 = $.trim($("#answ51").val());
	var answ52 = $.trim($("#answ52").val());
	var answ53 = $.trim($("#answ53").val());

	if (title == "") {
		gebi("info-msg-cab").style.display = "block";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали заголовок теста!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (description == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не описали инструкцию к выполнению теста!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if ((url == '') | (url == 'http://') | (url == 'https://')) {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали URL-адрес сайта!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (quest1 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали первый вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (answ11 == "" | answ12 == "" | answ13 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали варианты ответа на первый вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (quest2 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали второй вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (answ21 == "" | answ22 == "" | answ23 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали варианты ответа на второй вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (quest3 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали третий вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (answ31 == "" | answ32 == "" | answ33 == "") {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Вы не указали варианты ответа на третий вопрос!</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else if (money_add < <?=$tests_min_pay;?>) {
		gebi("info-msg-cab").style.display = "";
		gebi("info-msg-cab").innerHTML = '<span class="msg-error">Минимальная сумма пополнения - <?=$tests_min_pay;?> руб.</span>';
		setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 3000); clearTimeout();
		return false;
	} else {
		$.ajax({
			type: "POST", url: "/advertise/ajax/ajax_adv_add.php?rnd="+Math.random(), 
			data: {
				'op':'add', 
				'type':type, 
				'title':title, 
				'description':description, 
				'url':url, 
				'revisit':revisit, 
				'color':color, 
				'unic_ip_user':unic_ip_user, 
				'date_reg_user':date_reg_user, 
				'sex_user':sex_user, 
				'country[]':country, 
				'money_add':money_add, 
				'method_pay':method_pay, 
				'quest1':quest1, 
				'answ11':answ11, 
				'answ12':answ12, 
				'answ13':answ13, 
				'quest2':quest2, 
				'answ21':answ21, 
				'answ22':answ22, 
				'answ23':answ23, 
				'quest3':quest3, 
				'answ31':answ31, 
				'answ32':answ32, 
				'answ33':answ33, 
				'quest4':quest4, 
				'answ41':answ41, 
				'answ42':answ42, 
				'answ43':answ43, 
				'quest5':quest5, 
				'answ51':answ51, 
				'answ52':answ52, 
				'answ53':answ53

			}, 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			success: function(data) {
				$("#loading").slideToggle();
				gebi("info-msg-cab").innerHTML = "";
				var status_ok = data.substr(0,2);
				var status_er = data.substr(0,5);

				if(status_ok == "OK") var data = data.substr(2);
				if(status_er == "ERROR") var data = data.substr(5);

				if (status_ok == "OK") {
					$("#info-msg-cab").show();
					$("#OrderForm").html(data);
					$("#BlockForm").slideToggle("slow");
					$("#OrderForm").slideToggle("slow");
					$("#InfoAds").slideToggle("slow");

					//window.history.pushState(null, null, "/advertise.php?ads=<?=$ads;?>");
					$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
					return false;
				} else if (status_er == "ERROR") {
					gebi("info-msg-cab").style.display = "";
					gebi("info-msg-cab").innerHTML = '<span class="msg-error">' + data + '</span>';
					setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 5000); clearTimeout();
					return false;
				} else {
					gebi("info-msg-cab").style.display = "";
					gebi("info-msg-cab").innerHTML = '<span class="msg-error">' + data + '</span>';
					setTimeout(function() {$("#info-msg-cab").fadeOut("slow")}, 5000); clearTimeout();
					return false;
				}
			}
		});
	}
}

function DeleteAds(id) {
	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_adv_add.php?rnd="+Math.random(), 
		data: {'op':'del', 'type':'tests', 'id':id}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			if (data == "OK") {
				$("html, body").animate({scrollTop:0}, 700);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				$("#InfoAds").slideToggle("slow");
				ClearForm();
			}else{
				alert("Ошибка! Возможно тест уже был удален.");
			}
		}
	});
}

function ChangeAds() {
	$("#info-msg-cab").hide();
	$("#loading").slideToggle();
	$("#BlockForm").slideToggle("slow");
	$("#OrderForm").slideToggle("slow");
	$("#InfoAds").slideToggle("slow");
	//window.history.pushState(null, null, "/advertise.php?ads=<?=$ads;?>");
	$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
	$("#loading").slideToggle();
	return false;
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		gebi("Save").click();
	}
}

</script>

<?php
echo '<div id="ScrollID"></div>';

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:6px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Оплачиваемые тесты - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Тесты на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; это уникальный сервис, позволяющий пригласить человека на ваш интернет-ресурс, исследовать его или произвести какие-либо действия, после чего ответить на 3-5 контрольных вопросов. ';
		echo 'Это особенно полезно, если нужно, чтобы человек что-либо прочёл, прошёлся по разным страницам сайта или пришёл со страниц Google или Яндекс по требуемым ключевым словам.';
		echo '<br>';
	echo '</div>';

	echo '<span id="adv-title-rules" class="adv-title-close_1" onclick="ShowHideBlock(\'-rules\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Правила</span>';
	echo '<div id="adv-block-rules" style="display:block; padding:5px 7px 10px 7px; text-align:center; background-color:#FFFFFF;">';
	echo '<div style="margin:7px auto; margin-bottom:50px; background:#F0F8FF; padding:8px; box-shadow:0 1px 2px rgba(0, 0, 0, 0.4); text-align:center; color:#E32636;">';
	echo 'Тест должен быть сформулирован четко, не допускайте двойного трактования в описании.<br>';
                        echo 'Ответ на вопрос должен находиться не далее 3-й (третьей) страницы после перехода по ссылке на сайт.<br>';
                        echo '<b>Если вы размещаете тест на прохождение капчи, ее должно быть не больше 10 на весь тест! <br>Тесты, где капчи будет больше 10 - будут удаляться!</b><br>';
                        echo 'В тестах требующих клики по рекламе, разрешено не более 3-х переходов по рекламе.<br>';
			echo 'Запрещено составлять тесты с требованием регистрации либо авторизации.<br>';
			echo 'Для регистраций и авторизаций используйте <b>задания</b>.<br><br>';
			echo '<b>Тесты будут удалены без возврата средств в случае:</b><br>';
			echo '1. Требование пройти регистрацию и авторизацию.<br>';
			echo '2. Тесты с использованием сервиса MoneyCaptcha, adf.ly.<br>';
			echo '3. Скачивание файлов.<br>';
			echo '<b>Перед удалением, тест будет проверен Администратором или Модератором проекта.</b><br>';
	echo '</div>';
echo '</div>';
echo '</div>';

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
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 1000</span>';
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
		echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; padding:6px 10px; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Вопрос №'.$i.'</td></tr>';
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
		echo '<table class="tables" id="block_quest'.$i.'" style="display: none; margin:0;">';
		echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; padding:6px 10px; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Дополнительный вопрос</td></tr>';
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
	}

	echo '<table class="tables" id="block_add_quest" style="margin:0;">';
	echo '<tr><td align="center" style="padding: 8px 0;">';
		echo '<span class="sub-click" onClick="add_quest();">Добавить ещё вопрос '.($tests_cena_quest>0 ? "(+ ".p_floor($tests_cena_quest, 4)." руб.)" : false).'</span>';
	echo '</td></tr>';
	echo '</table>';

	echo '<span id="adv-title1" class="adv-title-close" onclick="ShowHideBlock(1);">Дополнительные настройки</span>';
	echo '<div id="adv-block1" style="display:none;">';
	echo '<table class="tables">';
	echo '<tr>';
		echo '<td align="left" width="220">Технология тестирования</td>';
		echo '<td align="left">';
			echo '<select id="revisit" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0" selected="selected">Доступно всем каждые 24 часа</option>';
				echo '<option value="1">Доступно всем каждые 3 дня '.($tests_cena_revisit[1]>0 ? "(+ ".p_floor($tests_cena_revisit[1], 4)." руб.)" : false).'</option>';
				echo '<option value="2">Доступно всем каждую неделю '.($tests_cena_revisit[2]>0 ? "(+ ".p_floor($tests_cena_revisit[2], 4)." руб.)" : false).'</option>';
				echo '<option value="3">Доступно всем каждые 2 недели '.($tests_cena_revisit[3]>0 ? "(+ ".p_floor($tests_cena_revisit[3], 4)." руб.)" : false).'</option>';
				echo '<option value="4">Доступно всем каждый месяц '.($tests_cena_revisit[4]>0 ? "(+ ".p_floor($tests_cena_revisit[4], 4)." руб.)" : false).'</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Выделить тест</td>';
		echo '<td align="left">';
			echo '<select id="color" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">Да '.($tests_cena_color>0 ? "(+ ".p_floor($tests_cena_color, 4)." руб.)" : false).'</option>';
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint5" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Уникальный IP</td>';
		echo '<td align="left">';
			echo '<select id="unic_ip_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">Да, 100% совпадение '.($tests_cena_unic_ip[1]>0 ? "(+ ".p_floor($tests_cena_unic_ip[1], 4)." руб.)" : false).'</option>';
				echo '<option value="2">Усиленный по маске до 2 '.($tests_cena_unic_ip[2]>0 ? "(+ ".p_floor($tests_cena_unic_ip[2], 4)." руб.)" : false).'</option>';
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
	echo '<table class="tables">';
	echo '<tr>';
		echo '<td colspan="2" align="center" style="border-right:none;"><a onclick="SetChecked(\'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
		echo '<td colspan="2" align="center" style="border-left:none;"><a onclick="SetChecked();" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
		echo '<td align="center" width="16" rowspan="10" style="background: #EDEDED;"><span id="hint9" class="hint-quest"></span></td>';
	echo '</tr>';
	include(DOC_ROOT."/advertise/func_geotarg.php");
	echo '</table>';
	echo '</div>';

	echo '<table class="tables">';
	echo '<tr><td align="center" colspan="3" style="background: #DCE7EA; padding:6px 10px; color: #00649E; font:13px Tahoma, Arial, sans-serif; font-weight:bold; text-shadow:1px 1px 1px #FFF;">Заказ и способ оплаты</td></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Способ оплаты</b></td>';
		echo '<td align="left">';
			echo '<select id="method_pay" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
				require_once("".DOC_ROOT."/method_pay/method_pay_form.php");
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint10" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Сумма пополнения</b></td>';
		echo '<td align="left"><input type="text" id="money_add" maxlength="7" value="100" class="ok12" style="text-align:center;" onChange="PlanChange();" onKeydowm="PlanChange();" onKeyup="PlanChange();">&nbsp;&nbsp;(минимум - '.$tests_min_pay.' руб.)</td>';
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
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'tests\');" class="sub-blue160" style="float:none; width:160px;">Оформить заказ</span></div>';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-pay" style="display:none;"></div>';

?>
<script language="JavaScript">ClearForm();</script>