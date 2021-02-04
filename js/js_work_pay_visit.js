$(document).ready(function(){inTimer();})

function inTimer(){ 
	$(".timer").html("<b>"+timer+"</b> "+inCline(["секунду", "секунды", "секунд"], timer));
	if(timer <= 0) {
		clearInterval(timeoutID);
		$(".text-timer").html("Сайт загружается, подождите...");
		var win_adv = window.open(url_go);
		if(win_adv) {
			FuncWork(id, op, token);
		}else{
			$(".work-status").html('<span class="msg-error">Разблокируйте всплывающие окна для сайта '+domen+' в своём браузере!</span>').fadeIn(300);
		}
		return false;
	}else{
		timer--;
	}
}
timeoutID = setInterval(inTimer, 1000);   

function FuncWork(id, op, token) {
	$(".work-status").html("").hide();
	if(!status_work){
		$.ajax({
			type:"POST", cache:false, url:"/ajax/ajax_work_pay_visits.php", dataType:'json', data:{'id':id, 'op':op, 'token':token}, 
			beforeSend: function() { status_work = true; }, 
			error: function(request, status, errortext) {
				status_work = false;
				$(".work-status").html('<span class="msg-error">'+errortext+'<br>'+(request.status!=404 ? request.responseText : 'url ajax not found')+'</span>').fadeIn(300);
			}, 
			success: function(data) {
				status_work = false; $("#loading").hide();
				var result = data.result || data;
				var message = data.message || data;

				if(result == "OK") {
					$(".work-info").html(message);

					close();
				} else {
					$(".work-status").html('<span class="msg-error">'+result+': '+message+'</span>').fadeIn(300);
				}
			}
		});
	}
	return false;
}

function inCline(words, n) {
	if(n<0) n = 0;
	return words[(n%100>4 && n%100<20)?2:[2,0,1,1,1,2][Math.min(n%10,5)]];
}
