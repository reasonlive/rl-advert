<?php
if(!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;">Добавить <b>"Быстрые сообщения"</b></h3>';
?>

<script type="text/javascript" language="JavaScript">
var tm;

$(document).ready(function(){
	ClearForm();
})

function HideMsg(id, timer) {
        clearTimeout(tm);
	tm = setTimeout(function() {$("#"+id).slideToggle(700);}, timer);
	return false;
}

function ClearForm() {
	$("#url").val("");
	$("#description").val("");
	$("#color").val("0");
	return false;
}

function SaveAds(id, type) {
	$("#info-msg-pay").html("").hide();

	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':'Add', 'type':type, 'url':$.trim($("#url").val()), 'description':$.trim($("#description").val()), 'color':$.trim($("#color").val()) }, 
		dataType: 'json',
		error: function(request, status, errortext) {
			$("#loading").slideToggle();
			var error = new Array(); error["rState"] = request.readyState!==false ? request.readyState : false; error["rText"]  = request.responseText!=false ? request.responseText : errortext; error["status"] = request.status!==false ? request.status : false; error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#info-msg-pay").show().html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			//console.log(request, status, errortext);
		},
		beforeSend: function() { $("#loading").slideToggle(); }, 
		success: function(data) {
			$("#loading").slideToggle();
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;

			if(result == "OK") {
				$("#BlockForm").slideToggle("slow");
				$("#info-msg-pay").html('<span class="msg-ok">'+message+'</span>').slideToggle("slow");
				setTimeout(function() {
					ClearForm();
					$("#info-msg-pay").html('<span class="msg-ok">'+message+'</span>').slideToggle("slow");
					$("#BlockForm").slideToggle("slow");
				}, 3000);
				return false;
			} else {
				$("#info-msg-pay").html('<span class="msg-error">'+message+'</span>').show();
				HideMsg("info-msg-pay", 5000);
				return false;
			}
		}
	});
}

</script>

<?php
### START BlockForm ###
echo '<div id="BlockForm" style="display:block; padding-bottom:8px;">';
echo '<div id="newform">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr><th align="center" colspan="3">Форма добавления сообщения</th></thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Сообщение</b></td>';
		echo '<td align="left"><input type="text" id="description" maxlength="45" value="" class="ok"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">URL (не обязательно)</td>';
		echo '<td align="left"><input type="text" id="url" maxlength="300" value="" class="ok"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left">Выделить цветом</td>';
		echo '<td align="left">';
			echo '<select id="color">';
				echo '<option value="0">Нет</option>';
				echo '<option value="1">Да</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	echo '</table>';
echo '</div>';
echo '<br>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'quick_mess\');" class="sub-blue160" style="">Добавить сообщение</span></div>';

echo '</div>';
### END BlockForm ###

echo '<div id="info-msg-pay" style="display:none;"></div>';

?>