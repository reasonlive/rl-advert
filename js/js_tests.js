jQuery(document).ready(function(){
	$(".td-serfm").click(function(e){
		if (this.className == 'td-serfm') {
			var elid = $(this).attr("id");
			fixed(elid);
		}
	}); 
})

function GoTest(obj, url) {
	id_obj = $(obj.parentNode).attr("id");
	$("#"+id_obj).css("cursor","default").html('<div style="text-align:center; color:#2E8B57; font-size:14px; display:block; margin:0 auto; padding:0;">Спасибо за визит!</div>');
	ws = window.open(url).focus();
	return false;
}

function fixed(uid) {
	var myReq = getXMLHTTPRequest();
	var params = "uid="+uid;

	function setstate() {
		if ((myReq.readyState == 4)&&(myReq.status == 200)) {
			var resvalue = myReq.responseText;
			if (resvalue != '') {
				if (resvalue.length > 13) {
					$("#"+uid).attr({class:"td-serf", valign:"middle"}).html('<div id="'+uid+'" align="center" onClick="GoTest(this, \'/'+resvalue+'\');"><span class="startserf">Перейти к выполнению теста</span></div>');
				} else if (resvalue == 'ERROR') {
				        window.location = '/';
				} else {
					if (elem = document.getElementById(resvalue)) {
						$(elem).fadeOut('low', function() {
							elem.innerHTML = '<td colspan="3" class="td-serfm"></td>';
						});
					}
				}
			}
		}
	}

	myReq.open("POST", "ajax/ajax_work_tests.php?rnd="+Math.random(), true);
	myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	myReq.setRequestHeader("Accept-Language", "ru");
	myReq.setRequestHeader("Accept-Charset", "windows-1251");
	myReq.setRequestHeader("Content-lenght", params.length);
	myReq.setRequestHeader("Connection", "close");
	myReq.onreadystatechange = setstate;
	myReq.send(params);
	return false;
}

function TestVirus(id, url) { 
	$("#id_virus"+id).hide();
	ws = window.open("http://online.us.drweb.com/result/?url="+url).focus();
	return false;
}

function ShowHideInfo(id) {
	if(document.getElementById(id).style.display == 'none') {
		document.getElementById(id).style.display = '';
	} else {
		document.getElementById(id).style.display = 'none';
	}
	return false;
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

