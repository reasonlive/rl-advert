<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_pay_row' AND `howmany`='1'");
$cena_pay_row = number_format($sql->fetch_object()->price, 2, ".", "");
?>

<script type="text/javascript" src="js/jquery.simpletip-1.3.1.pack.js"></script>

<script type="text/javascript" language="JavaScript">
var tm;

$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Описание ссылки</b> - максимум 60 символов.<br>Коротко укажите информацию о вашем ресурсе. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей.<br>Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После запятой правильно ставить знак пробела.'
	});
	$("#hint2").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>URL-адрес сайта</b> - должен начинаться с http:// или https:// и содержать не более 300 символов.<br>Не используйте HTML-теги и Java-скрипты. За попытки взлома системы, наказание - удаление аккаунта.'
	});
	$("#hint3").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Способ оплаты</b> - выберите наиболее подходящий для вас способ оплаты заказа.'
	});
	$("#hint4").simpletip({
		fixed: true, position: ["-610", "23"], focus: false,
		content: '<b>Стоимость размешения</b> составляет <?php echo $cena_pay_row;?> руб. Ссылка будет размещена на не ограниченный срок, но до тех пор пока ее не сменит другой рекламодатель.'
	});
})

function HideMsg(id, timer) {
        clearTimeout(tm);
	tm = setTimeout(function() {$("#"+id).slideToggle(700);}, timer);
	return false;
}

function ClearForm() {
	$("#url").val("");
	$("#description").val("");
	$("#method_pay").val("1");
	$("#tosaccept").prop("checked", false);
	return false;
}

function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}

function SaveAds(id, type) {
	var tosaccept = $("#tosaccept").prop("checked") == true ? 1 : 0;
	var url = $.trim($("#url").val());
	var description = $.trim($("#description").val());
	var method_pay = $.trim($("#method_pay").val());
	$("#info-msg-cab").html("").hide();

	if(tosaccept!=1) {
		alert("Если вы прочитали правила, с ними необходимо согласиться!");
		return false;
	}

	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'add', 'type':type, 'url':url, 'description':description, 'method_pay':method_pay }, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
			var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("#info-msg-cab").show();
				$("#OrderForm").html(message);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				$("#InfoAds").slideToggle("slow");
				$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function PayAds(id, type) {
	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'pay', 'type':type, 'id':id}, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
			var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("html, body").animate({scrollTop:0}, 700);
				$("#OrderForm").html('<span class="msg-ok">'+message+'</span>');
				setTimeout(function() {
					location.href = "/cabinet_ads?ads=pay_row";
				}, 2000);
				return false;
			} else {
				alert(message);
				return false;
			}
		}
	});
}

function DeleteAds(id, type) {
	$.ajax({
		type: "POST", url: "/advertise/ajax/ajax_advertise.php?rnd="+Math.random(), 
		data: {'op':'del', 'type':type, 'id':id}, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
				var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
				$("#info-msg-cab").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
				//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("html, body").animate({scrollTop:0}, 700);
				$("#BlockForm").slideToggle("slow");
				$("#OrderForm").slideToggle("slow");
				$("#InfoAds").slideToggle("slow");
				ClearForm();
			} else {
				alert(message);
				return false;
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

	$("html, body").animate({scrollTop: $("#ScrollID").offset().top-10}, 700);
	$("#loading").slideToggle();
	return false;
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#Save").click();
	}
}

</script>

<?php
echo '<div id="ScrollID"></div>';

echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:10px;">';
	echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Платная строка - что это?</span>';
	echo '<div id="adv-block-info" style="display:block; padding:5px 7px 3px 7px; text-align:justify; background-color:#F0F8FF;">';
		echo 'Платная строка на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> даёт отличную возможность повысить показатели тИЦ и PR Вашего ресурса, а так же даст больше информации для пользователей и поисковых систем! ';
                echo 'Ваша ссылка будет отображаться вверху на всех страницах, пока ее не сменит другой пользователь! Ссылка размещается автоматически и выделяется красным цветом! '; 
		echo 'Данный сервис очень эффективен тем, что ссылка может быть размещена на несколько часов, а возможно и на несколько дней! ';
		echo 'Сервис будет полезен тем рекламодателям, которые уделяют много внимания в продвижение своего ресурса, и много времени проводят на нашем сайте. ';
		echo 'Не забывайте также, что можно размещать ссылки не только на свой сайт, но и свои реферальные ссылки. Ограничений на количество размещаемых ссылок нет!';
	echo '</div>';
echo '</div>';

echo '<div id="BlockForm" style="display:block;">';
echo '<div id="newform" onkeypress="CtrlEnter(event);">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr><th align="center" colspan="3">Форма добавления рекламы</th></thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Описание ссылки</b></td>';
		echo '<td align="left"><input type="text" id="description" maxlength="60" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint1" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="url" maxlength="300" value="" class="ok"></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint2" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Способ оплаты</b></td>';
		echo '<td align="left">';
			echo '<select id="method_pay" class="ok">';
				require_once(DOC_ROOT."/method_pay/method_pay_form.php");
			echo '</select>';
		echo '</td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint3" class="hint-quest"></span></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="26"><b>Стоимость размешения</b></td>';
		echo '<td align="left"><span style="color:#FF0000; font-size:14px;">'.$cena_pay_row.' руб.</span></td>';
		echo '<td align="center" width="16" style="background: #EDEDED;"><span id="hint4" class="hint-quest"></span></td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td align="center" colspan="3" height="24"><input type="checkbox" id="tosaccept" value="1" /> С <a href="/tos.php" target="_blank" title="Откроются в новом окне"><b>Правилами</b></a> размещения рекламы полностью согласен(на)</td>';
	echo '</tr>';

	echo '</table>';
echo '</div>';

echo '<br>';
echo '<div id="info-msg-cab" style="display:none;"></div>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'pay_row\');" class="sub-blue160" style="float:none; width:160px;">Оформить заказ</span></div>';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-pay" style="display:none;"></div>';

?>
<script language="JavaScript">ClearForm();</script>