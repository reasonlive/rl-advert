<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавить статью в каталог</b></h3>';
?>

<script type="text/javascript" language="JavaScript">
$(document).ready(function(){
	ClearForm();
})

function ClearForm() {
	$("#title").val("");
	$("#url").val("");
	$("#desc_min").val("");
	$("#desc_big").val("");
	$("#method_pay").val("1");
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
	var title = $.trim($("#title").val());
	var url = $.trim($("#url").val());
	var desc_min = $.trim($("#desc_min").val());
	var desc_big = $.trim($("#desc_big").val());
	var method_pay = $.trim($("#method_pay").val());
	$("#info-msg-cab").html("").hide();

	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':'Add', 'type':type, 'title':title, 'url':url, 'desc_min':desc_min, 'desc_big':desc_big, 'method_pay':method_pay }, 
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

function AddAdv(id, type) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':'Start', 'type':type, 'id':id}, 
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
				StatMenu(type, 'StatMenu');
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
		type: "POST", url: "ajax_admin/ajax_admin_advertise.php?rnd="+Math.random(), 
		data: {'op':'Delete', 'type':type, 'id':id}, 
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

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#Save").click();
	}
}
</script>

<?php
echo '<div id="ScrollID"></div>';

echo '<div id="BlockForm" style="display:block;">';
echo '<div id="newform" onkeypress="CtrlEnter(event);">';
	echo '<table class="tables" style="border:none; margin:0; padding:0; width:100%;">';
	echo '<thead><tr><th align="center" colspan="2">Форма добавления статьи</th></thead></tr>';
	echo '<tr>';
		echo '<td align="left" width="220"><b>Заголовок статьи</b></td>';
		echo '<td align="left"><input type="text" id="title" maxlength="100" value="" class="ok"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b> (включая http://)</td>';
		echo '<td align="left"><input type="text" id="url" maxlength="300" value="" class="ok"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" colspan="3"><b>Краткое описание статьи</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_min\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_min\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_min\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_min\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_min\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_min\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_min\'); return false;">IMG</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:8px;">Осталось символов: 1000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="desc_min" class="ok" style="height:120px; width:99%;" onKeyup="descchange(\'1\', this, \'1000\');" onKeydown="descchange(\'1\', this, \'1000\');" onClick="descchange(\'1\', this, \'1000\');"></textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center" colspan="2"><b>Описание статьи</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_big\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_big\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_big\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_big\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_big\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_big\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_big\'); return false;">IMG</span>';
			echo '<span id="count2" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:8px;">Осталось символов: 5000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="desc_big" class="ok" style="height:200px; width:99%;" onKeyup="descchange(\'2\', this, \'5000\');" onKeydown="descchange(\'2\', this, \'5000\');" onClick="descchange(\'2\', this, \'5000\');"></textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
	echo '</table>';
echo '</div>';

echo '<br>';
echo '<div id="info-msg-cab" style="display:none;"></div>';
echo '<div align="center"><span id="Save" onClick="SaveAds(0, \'articles\');" class="sub-blue160" style="float:none; width:160px;">Добавить статью</span></div>';

echo '</div>'; ### END BlockForm ###

echo '<div id="OrderForm" style="display:none;"></div>';
echo '<div id="info-msg-Start" style="display:none;"></div>';

?>
