function CheckTest(doc) {
	var id_test = $.trim($("#id_test").val());
	var token = $.trim($("#token").val());
	var cqa = $.trim($("#cqa").val());
	var answer1 = $.trim($("#answer1").val());
	var answer2 = $.trim($("#answer2").val());
	var answer3 = $.trim($("#answer3").val());
	var answer4 = $.trim($("#answer4").val());
	var answer5 = $.trim($("#answer5").val());
	var tm;

	function hidemsg() {
		$("#info-msg-test").slideToggle("slow");
		if (tm) clearTimeout(tm);
	}

	if ( answer1 == "" | answer2 == "" | answer3 == "" | (answer4 == "" && cqa>=4) | (answer5 == "" && cqa>=5) ) {
		$("#info-msg-test").show();
		$("#info-msg-test").html('<span class="msg-error">Ответьте пожалуйста на все вопросы теста!</span>');
		tm = setTimeout(function() {hidemsg()}, 3000);
	} else {
		$.ajax({
			type: "POST", url: "/ajax/ajax_work_tests_chk.php?rnd="+Math.random(), 
			data: {
				'op':'check', 'id':id_test, 'token':token, 
				'answer1':answer1, 'answer2':answer2, 'answer3':answer3, 'answer4':answer4, 'answer5':answer5
			}, 
			dataType: 'json',
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() {
				$("#loading").slideToggle();
				$("#info-msg-test").show();
				$("#info-msg-test").html('<span class="msg-error">Ошибка обработки данных! Если ошибка повторяется, сообщите Администрации сайта.</span>');
				tm = setTimeout(function() {hidemsg()}, 4000);
				return false;
			}, 
			success: function(data) {
				$("#loading").slideToggle();
				$("#info-msg-test").html("");

				if (data.result == "OK") {
					$("#test-content").show();
					$("#test-content").html(data.message);
					$("html, body").animate({scrollTop: $("#test-content").offset().top-100}, 700);
					return false;
				} else {
					if(data.message) {
						$("#test-content").show();
						$("#test-content").html(data.message);
						$("html, body").animate({scrollTop: $("#test-content").offset().top-100}, 700);
						return false;
					} else {
						$("#info-msg-test").show();
						$("#info-msg-test").html('<span class="msg-error">Ошибка обработки данных!</span>');
						tm = setTimeout(function() {hidemsg()}, 4000);
						return false;
					}
				}
			}
		});
	}
}

function SetAnsw(answer, qnum, num) {
	if (qnum == 1) $("#answer1").val(answer);
	else if (qnum == 2) $("#answer2").val(answer);
	else if (qnum == 3) $("#answer3").val(answer);
	else if (qnum == 4) $("#answer4").val(answer);
	else if (qnum == 5) $("#answer5").val(answer);

	document.getElementById("answ-sel"+qnum+"1").className = "test-answ-sel";
	document.getElementById("answ-sel"+qnum+"2").className = "test-answ-sel";
	document.getElementById("answ-sel"+qnum+"3").className = "test-answ-sel";
	document.getElementById("answ-sel"+qnum+num).className = "test-answ-sel-act";

	return false;
}

function ScrollTo(){
	$("html, body").animate({scrollTop: $("#ScrollTestWork").offset().top-40}, 700);
}
