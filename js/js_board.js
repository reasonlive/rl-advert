$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, 
		position: ["-550", "0"], 
		focus: false,
		content: '<b>������</b> - ������� 1 ���.<br>��� ������ ���� ������ ��� ������ ����, ��� �� ������ ������ �������<br>���������� �� ����� ������, � ����� ������ ���� �������� � ��������!'
	});
	$("#hint2").simpletip({
		fixed: true, 
		position: ["-550", "0"], 
		focus: false,
		content: '<b>�����������</b> - ������������ ������ 30 ��������.<br>����������� ����� ������������ � ��� ��� ��������� �� ����� ������,<br>���� �� ������� ��������� ����������� �� �������� ���� ������,<br>��������� ���������� ����������� 1 ���'
	});
})

function hidemsg() {
	$('#info-msg-board').fadeOut('slow');
	if(tm) clearTimeout(tm);
}

function go_to_board() {
	var stavka = document.forms['board_form'].stavka.value;
	var comment = document.forms['board_form'].comment.value;
	stavka = parseInt(stavka);

	if (isNaN(stavka)) {
		document.getElementById("info-msg-board").innerHTML = "<span class='msg-error'>���������� ������� ������ ������!</span>";
		document.getElementById("info-msg-board").style.display = "block";
		tm = setTimeout(function() {hidemsg()}, 2000);
	} else if (stavka < 1) {
		document.getElementById("info-msg-board").innerHTML = "<span class='msg-error'>����� ������ ������ ���� �� ����� 1 ���.</span>";
		document.getElementById("info-msg-board").style.display = "block";
		tm = setTimeout(function() {hidemsg()}, 2000);
	} else {
		var myReq = getXMLHTTPRequest();
		var params = "stavka="+stavka+"&comment="+comment;

		function setstate() {
			if((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				if(resvalue == 'OK') {
					alert("���� ������ "+stavka+" ���. �������!\n\n�� ������� ��������� �� ����� ������");
				        window.location = '/board_of_honour.php';
				} else if(resvalue == 'ERROR-LOG') {
					alert("���������� ��������������!");
				        window.location = '/';
				} else if(resvalue == 'ERROR-NOMIN') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>����������� ������ 1 ���.</span>";
				} else if(resvalue == 'ERROR-NOMIN') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>����������� ������ 1 ���.</span>";
				} else if(resvalue == 'ERROR-ALREADY') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>��� ������ ��� �������� �� ����� ������.</span>";
				} else if(resvalue == 'ERROR-NOCASH') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>�� ����� ��������� ����� ������������ �������.</span>";
				} else if(resvalue == 'ERROR-NOMINCASH') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>���� ������ �� ���������� ������ ������.</span>";
				} else {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
				}
				document.getElementById('info-msg-board').style.display = 'block';
				tm = setTimeout(function() {hidemsg()}, 2000);
			} else {
				document.getElementById('info-msg-board').innerHTML = "<span id='loading' title='��������� ����������...'></span>";
				document.getElementById('info-msg-board').style.display = 'block';
			}
			return true;
		} 

		myReq.open("POST", "ajax/ajax_board.php?rnd="+Math.random(), true);
		myReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		myReq.setRequestHeader("Accept-Language", "ru");
		myReq.setRequestHeader("Accept-Charset", "windows-1251");
		myReq.setRequestHeader("Content-lenght", params.length);
		myReq.setRequestHeader("Connection", "close");
		myReq.onreadystatechange = setstate;
		myReq.send(params);

		document.getElementById('info-msg-board').style.display = 'block';
		document.getElementById('info-msg-board').innerHTML = "<span id='loading' title='��������� ����������...'></span>";
	}
	return false;
}