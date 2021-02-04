function getXMLHTTPRequest() {
	var req = false;
	try {
		req = new XMLHttpRequest();
	} catch(err) {
		try {
			req = new ActiveXObject("MsXML2.XMLHTTP");
		} catch(err) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch(err) {
				req = false;
			}
		}
	}
	return req;
}

function go_vacation() {
	var count_days = document.forms['count_vacation'].count_vac.value;
	var cnt = document.forms['count_vacation'].cnt.value;
	count_days = parseInt(count_days);

	if (isNaN(count_days)) {
		document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Неверно указано количество дней</span>";
		document.getElementById('info-msg').style.display = 'block';
	} else if (count_days < 1) {
		document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Количество дней отпуска должно быть более 1 дня</span>";
		document.getElementById('info-msg').style.display = 'block';
	} else if (count_days > 180) {
		document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Нельзя уходить в отпуск более чем на 6 месяцев за один раз</span>";
		document.getElementById('info-msg').style.display = 'block';
	} else {
		var myReq = getXMLHTTPRequest();
		var params = "cnt="+cnt+"&count_days="+count_days;

		function setstate() {
			if ((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				//$('#info-msg').fadeOut('fast');
				if (resvalue == 'OK') {
				        window.location = '/vacation.php';
				} else if (resvalue == 'NO') {
					document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Ошибка!</span>";
					document.getElementById('info-msg').style.display = 'block';
				} else {
					document.getElementById('info-msg').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
					document.getElementById('info-msg').style.display = 'block';
				}
			} else {
				document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
				document.getElementById('info-msg').style.display = 'block';
			}
			return true;
		} 

	        myReq.open("POST", "ajax/procvac.php?op=vacation&rnd="+Math.random(), true);
       		myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		myReq.setRequestHeader("Accept-Language", "ru");
		myReq.setRequestHeader("Accept-Charset", "windows-1251");
	        myReq.setRequestHeader("Content-lenght", params.length);
       		myReq.setRequestHeader("Connection", "close");
	        myReq.onreadystatechange = setstate;
       		myReq.send(params);

		document.getElementById('info-msg').style.display = 'block';
		document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
	}
	return false;
}

function isValidCode(myCode) { 
	return /^[a-zA-Z0-9\-_-]{6,20}$/.test(myCode);
} 

function hidemsg() {
	$('#info-msg-out').fadeOut('slow');
	if (tm) clearTimeout(tm);
}

function vac_out() {
	var pass_oper = document.forms['vacationout'].pass_oper.value;

	if (!pass_oper) {
		document.getElementById("info-msg-out").innerHTML = "<span class='msg-error'>Вы не указали пароль для операций!</span>";
		document.getElementById('info-msg-out').style.display = 'block';
		tm = setTimeout(function() {
			hidemsg()
		}, 1000);
	} else if (!isValidCode(pass_oper)) {
		document.getElementById("info-msg-out").innerHTML = "<span class='msg-error'>Вы не верно указали пароль для операций!</span>";
		document.getElementById('info-msg-out').style.display = 'block';
		tm = setTimeout(function() {
			hidemsg()
		}, 1000);
	} else {
		var myReq = getXMLHTTPRequest();
		var params = "pass_oper="+pass_oper;

		function setstate() {
			if ((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				if (resvalue == '') {
					document.getElementById('info-msg-out').innerHTML = "<span class='msg-error'>Не удалось обработать запрос</span>";
					document.getElementById('info-msg-out').style.display = 'block';
				} else if (resvalue == 'OK') {
				        window.location = '/members.php';
				} else if (resvalue == 'NO') {
					document.getElementById("info-msg-out").innerHTML = "<span class='msg-error'>Вы не верно указали пароль для операций!</span>";
					document.getElementById('info-msg-out').style.display = 'block';
				} else {
					document.getElementById('info-msg-out').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
					document.getElementById('info-msg-out').style.display = 'block';
				}
			} else {
				document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
				document.getElementById('info-msg').style.display = 'block';
			}
			return true;
		} 

	        myReq.open("POST", "ajax/procvacout.php?op=vacout&rnd="+Math.random(), true);
       		myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		myReq.setRequestHeader("Accept-Language", "ru");
		myReq.setRequestHeader("Accept-Charset", "windows-1251");
	        myReq.setRequestHeader("Content-lenght", params.length);
       		myReq.setRequestHeader("Connection", "close");
	        myReq.onreadystatechange = setstate;
       		myReq.send(params);

		document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
		document.getElementById('info-msg').style.display = 'block';
	}
	return true;
}


function pay_vacation() {
	var count_month = document.forms['pay_vac'].count_month.value;
	var cnt = document.forms['pay_vac'].cnt.value;
	count_month = parseInt(count_month);

	if (isNaN(count_month)) {
		document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Неверно указано количество месяцев</span>";
		document.getElementById('info-msg').style.display = 'block';
	} else if (count_month < 1) {
		document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Количество месяцев должно быть целым числом, от 1 месяца и более</span>";
		document.getElementById('info-msg').style.display = 'block';
	} else {
		var myReq = getXMLHTTPRequest();
		var params = "cnt="+cnt+"&count_month="+count_month;

		function setstate() {
			if ((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;

				if (resvalue == 'OK') {
				        window.location = '/vacation.php';
				} else if (resvalue == 'NO') {
					document.getElementById("info-msg").innerHTML = "<span class='msg-error'>Ошибка!</span>";
					document.getElementById('info-msg').style.display = 'block';
				} else {
					document.getElementById('info-msg').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
					document.getElementById('info-msg').style.display = 'block';
				}
			} else {
				document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
				document.getElementById('info-msg').style.display = 'block';
			}
			return true;
		} 

	        myReq.open("POST", "ajax/procvac2.php?op=vacation&rnd="+Math.random(), true);
       		myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		myReq.setRequestHeader("Accept-Language", "ru");
		myReq.setRequestHeader("Accept-Charset", "windows-1251");
	        myReq.setRequestHeader("Content-lenght", params.length);
       		myReq.setRequestHeader("Connection", "close");
	        myReq.onreadystatechange = setstate;
       		myReq.send(params);

		document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
		document.getElementById('info-msg').style.display = 'block';
	}
	return false;
}
