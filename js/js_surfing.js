$(document).ready(function(){
	$(".td-serfm").click(function(e){
		if ($(this).attr("class") == "td-serfm") {
			var elid = $(this).attr("id");
			if(typeof elid != "undefined" && start_us!=false) fixed(elid);
		}
	}); 
})

function ShowHideInfo(id) {
	if($("#"+id).css("display") == "none") {
		$("#"+id).css("display", "");
	} else {
		$("#"+id).css("display", "none");
	}
}

function StartPsevdo(url) {
	ws = window.open(url).focus();
	return false;
}

function StartSurfing(id, url) {
	$("#"+id).css("cursor","default").html('<div style="text-align:center; color:#2E8B57; font-size:14px; display:block; margin:0 auto; padding:0;">Спасибо за визит!</div>');
	if($("#info_serf_"+id).css("display")!="none") $("#info_serf_"+id).css("display", "none");
	if($("#claims_"+id).css("display")!="none") $("#claims_"+id).css("display", "none");
	ws = window.open(url).focus();
	return false;
}

function TestVirus(id, url) { 
	$("#id_virus"+id).hide();
	ws = window.open("http://online.us.drweb.com/result/?url="+url).focus();
	return false;
}

function fixed(id) {
	$.ajax({
		type: "POST", url: "/ajax/ajax_surfing.php?rnd="+Math.random(), 
		data: {'id':id}, 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		error: function() {$("#loading").slideToggle(); alert("Ошибка! Обновите страницу."); return false;}, 
		success: function(data) {
			$("#loading").slideToggle();
			var obj_data = jQuery.parseJSON(data);

			if(obj_data.result=="OK") {
				$("#"+id).attr({class:"td-serf"}).html('<div id="link_'+id+'" align="center" onClick="StartSurfing(\''+id+'\', \''+obj_data.url_link+'\');"><span class="startserf">Перейти к просмотру сайта рекламодателя</span></div>');
				if($("#info_serf_"+id).css("display")!="none") $("#info_serf_"+id).css("display", "none");
				if($("#claims_"+id).css("display")!="none") $("#claims_"+id).css("display", "none");
			} else if(obj_data.result=="ERROR") {
				$("#tr"+id).fadeOut(300, function () {$(this).remove();});
				$("#info_serf_"+id).remove();
				$("#claims_"+id).remove();
			} else {
				alert("RESULT: "+data);
			}
		}
	});
}

function AddClaims(id, type) {
	var tm;
	var claimstext = $.trim($("#claimstext"+id).val());

	function hidemsg() {
		$("#info-claims-"+id).slideToggle("slow");
		if (tm) clearTimeout(tm);
	}

	if(claimstext.length<10) {
		$("#info-claims-"+id).show().html('Укажите минимум 10 символов текста для жалобы.<br>');
		tm = setTimeout(function() {hidemsg()}, 3000);
		return false;
	} else {
		$.ajax({
			type: "POST", url: "/ajax/ajax_claims.php?rnd="+Math.random(), data: {'id':id, 'type':type, 'claimstext':claimstext}, 
			dataType: 'json', beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() {
				$("#loading").slideToggle();
				$("#info-claims-"+id).show().html('Ошибка обработки данных! Если ошибка повторяется, сообщите Администрации сайта.<br>');
				tm = setTimeout(function() {hidemsg()}, 5000);
				return false;
			}, 
			success: function(data) {
				$("#loading").slideToggle();
				$("#info-claims-"+id).html("");

				if (data.result == "OK") {
					$("#formclaims"+id).show().html(data.message);
					tm = setTimeout(function() {$("#claims"+id).click()}, 5000);
					return false;
				} else {
					if(data.message) {
						$("#info-claims-"+id).show().html(data.message);
						tm = setTimeout(function() {hidemsg()}, 3000);
						return false;
					} else {
						$("#info-claims-"+id).show().html("Ошибка обработки данных!");
						tm = setTimeout(function() {hidemsg()}, 3000);
						return false;
					}
				}
			}
		});
	}
}
