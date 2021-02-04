var stats_load = false;

function ShowHideStats(us_id, token) {
	if ( (stats_load == us_id) ) {
		document.getElementById('usersstat'+us_id).style.display = 'none';
		if(document.getElementById('img_stat'+us_id)) document.getElementById('img_stat'+us_id).innerHTML = '<img src="img/down_blue20.png" align="middle" border="0" alt="" title="Показать статистику" style="margin:0; padding:0; padding-right:5px;" />';
		stats_load = false;
	} else {
		if(stats_load) {
			document.getElementById('usersstat'+stats_load).style.display = 'none';
			if(document.getElementById('img_stat'+us_id)) document.getElementById('img_stat'+us_id).innerHTML = '<img src="img/down_blue20.png" align="middle" border="0" alt="" title="Показать статистику" style="margin:0; padding:0; padding-right:5px;" />';
		}

		var myReq = getXMLHTTPRequest();
		var params = "us_id="+us_id+"&token="+token;

		function setstate() {
			if ((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				if (resvalue != 'ERROR') {
					document.getElementById('usersstat'+us_id).innerHTML = resvalue;
					if(document.getElementById('img_stat'+us_id)) document.getElementById('img_stat'+us_id).innerHTML = '<img src="img/up_blue20.png" align="middle" border="0" alt="" title="Показать статистику" style="margin:0; padding:0; padding-right:5px;" />';

					stats_load = us_id;
					document.getElementById('usersstat'+us_id).style.display = '';
					document.getElementById('info-msg').style.display = 'none';
				}
			} else {
				document.getElementById('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
				document.getElementById('info-msg').style.display = 'block';
			}
		} 

		myReq.open("POST", "ajax/ajax_statsusers.php", true);
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