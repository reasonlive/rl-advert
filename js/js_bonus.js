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

function money_plus(rt_pls, inb) {
	if (rt_pls != '') {
		var myReq = getXMLHTTPRequest();
		var params = "cnt="+rt_pls;

		function setstate() {
			if ((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				$('#bonus_msg').fadeOut('fast');
				if (resvalue == 'OK') {
					document.getElementById('info-msg').innerHTML = "<span class='msg-ok'>Поздравляем! Вам зачислено +"+inb+" Рублей</span>";
					document.getElementById('info-msg').style.display = 'block';
				} else if (resvalue == 'NO') {
					document.getElementById('info-msg').innerHTML = "<span class='msg-w'>Бонус за сегодняшний день уже зачислен</span>";
					document.getElementById('info-msg').style.display = 'block';
				} else {
					document.getElementById('info-msg').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
					document.getElementById('info-msg').style.display = 'block';
				}
				setTimeout(function() {$('#info-msg').fadeOut('slow')}, 3000); clearTimeout();
			} else {
				document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
				document.getElementById('info-msg').style.display = 'block';
			}
		} 

		myReq.open("POST", "ajax/proc_money.php?op=money_plus&rnd="+Math.random(), true);
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