function gebi(id){
	return document.getElementById(id)
}

function hidemsg() {
	$('#info-msg').fadeOut('slow');
	if(tm) clearTimeout(tm);
}

$(document).ready(function(){
	var button = $('#uploadButton'), interval;

	$.ajax_upload (button, {
		action: 'ajax/ajax_load_avatar.php',
		name: 'user_avatar',

		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				gebi('info-msg').innerHTML = "<span class='msg-error'>Ошибка! Допустимые форматы аватара для загрузки - gif, png, jpeg, jpg</span>";
				gebi('info-msg').style.display = 'block';
				tm = setTimeout(function() {hidemsg()}, 3000);
				return false;
			}
			gebi('info-msg').innerHTML = "<span id='loading' title='Подождите пожалуйста...'></span>";
			gebi('info-msg').style.display = 'block';
			this.disable();
		},

		onComplete: function(file, response){
			//gebi('uploadButton').innerHTML = 'Загрузить еще';
			gebi('info-msg').innerHTML = "";

			this.enable();

			if (response == "SUCCES") {
			        window.location = '/profile.php';
				//gebi('avatar1').innerHTML = '<img src="avatar/'+file+'" width="80" height="80" border="0" align="middle" alt="" />';
				//gebi('avatar2').innerHTML = '<img src="avatar/'+file+'" width="80" height="80" border="0" align="middle" alt="" />';
			} else if (response == "ERROR") {
				gebi('info-msg').innerHTML = "<span class='msg-error'>Ошибка!</span>";
			} else {
				gebi('info-msg').innerHTML = "<span class='msg-error'>"+response+"</span>";
			}
			gebi('info-msg').style.display = 'block';
			tm = setTimeout(function() {hidemsg()}, 3000);
		}
	});
});