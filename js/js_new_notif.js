$(document).ready(function(){
	var pre_title = document.title;
	var new_title = false, re_title = false, w_focus = true;
	$(window).focus(function(){w_focus = true}).blur(function(){w_focus = false});

	function LoadNewNotif() {
		$.ajax({
			type: "POST", url: "ajax/ajax_new_notif.php", 
			data: {'op':'new_notif'}, dataType: 'json',
			success: function(data) {
				var result = data.result ? data.result : data;
				var message = data.message ? data.message : data;
				new_title = data.title_page ? data.title_page : false;

				if(result == "OK") {
					re_title = true;
					if($("div").is("#user-notif")) {$("#user-notif").html(message);}else{$("body").append('<div id="user-notif">'+message+'</div>');}
				}else{
					re_title = false; new_title = false;
					$(document).attr("title", pre_title);
					if($("div").is("#user-notif")) $("#user-notif").remove();
				}
			}
		});
		setTimeout(LoadNewNotif, 20000);
	}

	setTimeout(LoadNewNotif, 1000);

	setInterval(function(){
		if(new_title && !w_focus) {
			if(re_title){ re_title=false; $(document).attr("title", pre_title); }else{ re_title=true; $(document).attr("title", new_title); }
		} else if(new_title && w_focus) {
			if(re_title) { re_title=false; $(document).attr("title", pre_title); }
		}
	}, 1000);
});