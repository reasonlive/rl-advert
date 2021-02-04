function StartClock() {
	loadSite = true;
	$("#BlockWait").remove();
	$("#BlockTimer").show();

	d_focus = ((typeof d.hasFocus != 'undefined' ? d.hasFocus() : w_focus) ? true : false);

	if(isClick > 0 && isClick > mClick) {
		if(mClick<0) mClick = 0;
		var url_s = $("iframe").attr("src");
		$("#Timer").html(isTimer).hide();
		$("#info-timer").html('<span class="red bold">Для продолжения необходимо сделать '+isClick+' '+inCline(["переход", "перехода", "переходов"], isClick)+' по сайту...<div style="padding-top:3px;">Сделано: '+mClick+' '+inCline(["переход", "перехода", "переходов"], mClick)+'</div></span>').show();

	} else if( ((isActive && d_focus) | !isActive) && (WinHeight>mWinHeight && WinWidth>mWinWidth) ) {
		$("#Timer").html(isTimer).show();
		$("#info-timer").html('<span class="grey">Дождитесь окончания таймера</span>').show();
		isTimer--; 

	} else if(WinHeight<mWinHeight | WinWidth<mWinWidth) {
		$("#Timer").html(isTimer).show();
		$("#info-timer").html('<span class="red bold">Размер окна вашего браузера слишком маленький для просмотра сайта.</span>');

	} else if(isActive && !d_focus) {
		$("#Timer").html(isTimer).show();
		$("#info-timer").html('<span class="red bold">ОКНО НЕ АКТИВНО!!!</span>');
	}

	if(isTimer >= 0) {
		tm = setTimeout("StartClock(0)", 1000);
	} else {
		if(tm) clearTimeout(tm);
		lcap = true;
		setTimeout(function(){LoadCaptcha();}, 500);
	}
}

function ClickSite() {
	mClick += 1;
}

function LoadCaptcha() {
	if(!lcap) {alert("ERROR"); return false;}
	$.ajax({
		type: "POST", url: "ajax/ajax_surfing_captcha.php?rnd="+Math.random(), 
		data: {'op':'LoadCaptcha', 'token':token, 'id_adv':id_adv}, 
		dataType: 'json',

		error: function(request, status, errortext) {
			var error = new Array();
			error["rState"] = request.readyState!==false ? request.readyState : false;
			error["rText"]  = request.responseText!=false ? request.responseText : errortext;
			error["status"] = request.status!==false ? request.status : false;
			error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#BlockVerify").html('<div class="block-error"><span>ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span></div>');
			//console.log(request, status, errortext);
		},

		beforeSend: function() {$("#BlockVerify").html('<div class="loading"></div>');}, 

		success: function(data) {
			localStorage.setItem("id_adv_l", id_adv);
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			$("#BlockVerify").html(message);
		}
	});
}

function CheckVerify(code) {
	var code = $.trim(code);
	var id_adv_l = localStorage.getItem("id_adv_l");
	$.ajax({
		type: "POST", url: "ajax/ajax_surfing_verify.php?rnd="+Math.random(), 
		data: {'op':'CheckVerify', 'token':token, 'code':code, 'id_adv':id_adv, 'id_adv_l':id_adv_l}, 
		dataType: 'json',

		error: function(request, status, errortext) {
			var error = new Array();
			error["rState"] = request.readyState!==false ? request.readyState : false;
			error["rText"]  = request.responseText!=false ? request.responseText : errortext;
			error["status"] = request.status!==false ? request.status : false;
			error["statusText"] = request.statusText!==false ? request.statusText : false;
			$("#BlockVerify").html('<div class="block-error"><span>ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span></div>');
			//console.log(request, status, errortext);
		},

		beforeSend: function() {$("#BlockVerify").html('<div class="loading"></div>');}, 

		success: function(data) {
			localStorage.clear("id_adv_l");
			var result = data.result ? data.result : data;
			var message = data.message ? data.message : data;
			$("#BlockVerify").html(message);
			if(result=="OK") { setTimeout("top.location = '"+$("iframe").attr("src")+"'", 1500); }
		}
	});
}

function ChekLoad() {
	if(!loadSite) {
		ClickSite();
		setTimeout(function(){
			setTimeout(function(){if(!loadSite) {isClick = 0; StartClock();}}, 5000);
			if(!loadSite) $("#BlockWait").html('<b>Загрузка страницы задержалась. Подождите 5 секунд</b>');
		}, 4000);
	} else if(loadSite) {
		setTimeout("StartClock()", 1000);
	}
}

function ReSize() {
	WinWidth = $.trim($(w).width());
	WinHeight = $.trim($(w).height());
}

function inCline(words, n) {
	if(n<0) n = 0;
	return words[(n%100>4 && n%100<20)?2:[2,0,1,1,1,2][Math.min(n%10,5)]];
}

$(d).ready(function(){
	ChekLoad(); 
	ReSize();

	w.onresize = function(){
		ReSize();
		//$("#framesite").attr("width", WinWidth);
		$("#framesite").attr("height", (WinHeight-115));
	}

	//$("#framesite").attr("width", WinWidth);
	$("#framesite").attr("height", (WinHeight-115));

	if(typeof RotFrmLinks !== 'udefined') RotFrmLinks();
});

function OnLoadFrm() {
	if(!loadSite) {loadSite=true; ChekLoad();}
	if(countLoadFrm!=0) ClickSite();
	countLoadFrm++;
}

function getRandomInt(min, max) {
	return Math.floor(Math.random() * (max - min)) + min;
}

function RotFrmLinks() {
	if(frm_link_arr) {
		var i = getRandomInt(0, frm_link_arr.length);
		var frmlink = '<a href="'+frm_link_arr[i].link+'" target="_blank" class="frmlink">'+frm_link_arr[i].text+'</a>';
		if(firstLoad == false | firstLoad == i) {
			firstLoad = i;
			$("#FrmLink").html(frmlink);
		}else{
			$("#FrmLink").fadeOut(500, function() {$("#FrmLink").html(frmlink).fadeIn(500);});
		}
		setTimeout("RotFrmLinks()", 8000);
	}
}
