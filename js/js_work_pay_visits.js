var status_work;

$(document).ready(function(){
	$(".td-work").on("click", function(){
		if($(this).attr("class") == "td-work") {
			FuncWork($(this).data("id"), $(this).data("op"), $(this).data("token"));
		}
	}); 
})

function FuncWork(id, op, token, modal, width_win, title_win) {
	if(TmMod) clearTimeout(TmMod);
	if(!status_work){
		$.ajax({
			type:"POST", cache:false, url:"/ajax/ajax_work_pay_visits.php", dataType:'json', data:{'id':id, 'op':op, 'token':token, 'claims_text':$.trim($("#claims_text").val())}, 
			beforeSend: function() { status_work = true; $("input, textarea, select").blur(); $("#loading").show(); }, 
			error: function(request, status, errortext) {
				status_work = false; $("#loading").hide();
				ModalStart("Ошибка Ajax!", StatusMsg("ERROR", errortext+"<br>"+(request.status!=404 ? request.responseText : 'url ajax not found')), 500, true, false, false);
			}, 
			success: function(data) {
				status_work = false; $("#loading").hide();
				var result = data.result || data;
				var message = data.message || data;
				width_win = width_win || 500;

				if(result == "OK") {
					title_win = title_win || "Информация";

					if(op == "start-work") {
						$("#td-work-"+id).attr("class", "td-work-go").html(message);

					} else {
						if($("div").is(".box-modal") && message && modal) {
							$(".box-modal-title").html(title_win);
							$(".box-modal-content").html(message);
						} else if(message && modal) {
							ModalStart(title_win, message, width_win, true, false);
						}
					}
				}else{
					title_win = title_win || "Ошибка";

					if(message && modal && $("div").is(".box-modal")) {
						$(".box-modal-title").html(title_win);
						$(".box-modal-content").html(StatusMsg(result, message));
						TmMod = setTimeout(function(){$.modalpopup("close");}, 5000);
					} else if(message && modal) {
						ModalStart(title_win, StatusMsg(result, message), width_win, true, false, 5);
					} else {
						$("#td-work-"+id).attr("class", "td-work-go").html('<span class="work-mess error">'+message+'</span>');
					}
				}
			}
		});
	}
	return false;
}

function SHBlock(id) {
	if($("#"+id).css("display")=="none") {
		$("#"+id).show();
	} else {
		$("#"+id).hide();
	}
}

function CheckVir(id, url) {
	var win_vir = window.open("http://online.us.drweb.com/result/?url="+url);
	if(win_vir) $("#"+id).remove();
	return false;
}
