$(document).ready(function(){
	$("#hint1").simpletip({
		fixed: true, 
		position: ["-550", "0"], 
		focus: false,
		content: '<b>Ставка</b> - минимум 1 руб.<br>Чем больше Ваша ставка тем больше шанс, что Вы будете больше времени<br>находиться на доске почета, а также больше шанс победить в конкурсе!'
	});
	$("#hint2").simpletip({
		fixed: true, 
		position: ["-550", "0"], 
		focus: false,
		content: '<b>Комментарий</b> - максимальная длинна 30 символов.<br>Комментарий будет отображаться у вас под аватаркой на доске почёта,<br>если не желаете добавлять комментарий то оставьте поле пустым,<br>стоимость добавления комментария 1 руб'
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
		document.getElementById("info-msg-board").innerHTML = "<span class='msg-error'>Необходимо указать размер ставки!</span>";
		document.getElementById("info-msg-board").style.display = "block";
		tm = setTimeout(function() {hidemsg()}, 2000);
	} else if (stavka < 1) {
		document.getElementById("info-msg-board").innerHTML = "<span class='msg-error'>Сумма ставки должна быть не менее 1 руб.</span>";
		document.getElementById("info-msg-board").style.display = "block";
		tm = setTimeout(function() {hidemsg()}, 2000);
	} else {
		var myReq = getXMLHTTPRequest();
		var params = "stavka="+stavka+"&comment="+comment;

		function setstate() {
			if((myReq.readyState == 4)&&(myReq.status == 200)) {
				var resvalue = myReq.responseText;
				if(resvalue == 'OK') {
					alert("Ваша ставка "+stavka+" руб. принята!\n\nВы успешно размещены на доске почёта");
				        window.location = '/board_of_honour.php';
				} else if(resvalue == 'ERROR-LOG') {
					alert("Необходимо авторизоваться!");
				        window.location = '/';
				} else if(resvalue == 'ERROR-NOMIN') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>Минимальная ставка 1 руб.</span>";
				} else if(resvalue == 'ERROR-NOMIN') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>Минимальная ставка 1 руб.</span>";
				} else if(resvalue == 'ERROR-ALREADY') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>Ваш аватар уже размещен на доске почета.</span>";
				} else if(resvalue == 'ERROR-NOCASH') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>На вашем рекламном счету недостаточно средств.</span>";
				} else if(resvalue == 'ERROR-NOMINCASH') {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>Ваша ставка не перебивает ставку лидера.</span>";
				} else {
					document.getElementById('info-msg-board').innerHTML = "<span class='msg-error'>"+resvalue+"</span>";
				}
				document.getElementById('info-msg-board').style.display = 'block';
				tm = setTimeout(function() {hidemsg()}, 2000);
			} else {
				document.getElementById('info-msg-board').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
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
		document.getElementById('info-msg-board').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
	}
	return false;
}