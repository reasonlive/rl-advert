<?PHP
ini_set('display_errors',1);
error_reporting(E_ALL);

$rid = $config->URL_ID_WM_LOGIN;
require_once($_SERVER['DOCUMENT_ROOT'] ."/recaptcha/config_recaptcha.php");




//z8MXli serfnets.ru




?>
	

<?php if(isset($_GET['register'])): ?>
<div class='enter-popup '>
		<div class='form-wrap post-popup' style="display:none;">

			<div class='flex-center' style="width:400px">
			<p>МЫ ОТПРАВИЛИ ВАМ НА ПОЧТУ ПИСЬМО, В КОТОРОМ БУДЕТ ПАРОЛЬ ДЛЯ ВХОДА НА САЙТ. Возможно оно может оказаться в папке спама</p>
			<button class='btn' style='color:black;' onclick="location.href = '/' ">ПОНЯТНО</button>
			</div>
			</div>

                      


	<div class='form-wrap pre-popup' >
		

		<div class='flex-center' style="width:400px">
		<form action="/ajax/enter.php" method="post">
			 
			<label for='username'> ВВЕДИТЕ СВОЕ ИМЯ </label><br><br>
			<input type="text" name="username" placeholder="username"><br><br>
			<label for='email'> ВВЕДИТЕ СВОЙ E-MAIL</label><br><br>
			<input type="text" name="email" placeholder="example@gmail.com"><br><br>
			
			<div style='display:flex;align-items:center;'>
				
			<a style='color:black;' href="/rules">Согласен с правилами сайта</a>
			<input type="checkbox" name="agree">
			</div>
                        <div class='captcha'>
			 <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div> 
                         <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script> 
                        </div>
			<button class='btn btn-main' onclick='send(document.forms[0]);return false;'>СОЗДАТЬ АККАУНТ</button><br><br>
			<div class='flex-center' style='justify-content: space-around'>
				<button class='btn btn-extra' onclick ="location.href = '/' ;return false;">НА ГЛАВНУЮ</button>
				<button class='btn btn-extra' onclick ="location.href = '/signup/login' ; return false;">АВТОРИЗАЦИЯ</button>

			</div>
			<div class='enter-soc'>
					<p>Регистрация через соц сети</p>
					<a href="/social/login.php?go=vk&act=reg"><img src="/img/new/social-vk.svg" width='50' height="50"></a>
			</div>
		</form>
		</div>
	</div> 
</div>

<?php endif; ?>

<?php if(isset($_GET['login'])): ?>
<div class='enter-popup '>
	<!-- <div class='form-wrap post-popup' style="display:none;">

		<div class='flex-center' style="width:400px">
		<p>МЫ ОТПРАВИЛИ ВАМ НА ПОЧТУ ПИСЬМО, В КОТОРОМ БУДЕТ ПАРОЛЬ ДЛЯ ВХОДА НА САЙТ. Возможно оно может оказаться в папке спама</p>
		<button class='btn' onclick="location.href = '/' " style='color:black'>Понятно</button>
		</div>
	</div> -->
	<div class='form-wrap pre-popup' >

		<div class='flex-center' style="width:400px">
		<form action="/ajax/enter.php" method="post">
			<label for='email'> ВВЕДИТЕ СВОЙ E-MAIL</label><br><br>
			<input type="text" name="email" placeholder="example@gmail.com"><br><br>
			<label for='pass'> ВВЕДИТЕ СВОй ПАРОЛЬ </label><br><br>
			<input type="password" name="pass"><br><br>
			<label for='pass2'> ПОВТОРИТЕ ПАРОЛЬ </label><br><br>
			<input type="password" name="pass2"><br><br>
                        <div class='captcha'>
                        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div> 
                        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script> 
                        </div>
			<button class='btn btn-main' onclick ='send(document.forms[0]);return false;'>ВОЙТИ</button><br><br>
                        
			<button class='btn btn-main' onclick='location.href = "https://login.web.money/GateKeeper.aspx?RID=<?php echo $rid; ?>&lang=ru-RU&op=login";return false; '>ВХОД ЧЕРЕЗ LOGIN WM</button><br><br>
			<div class='flex-center' style='justify-content: space-between'>
				<button class='btn btn-extra' onclick ="location.href = '/' ; return false;">НА ГЛАВНУЮ</button>
				

			</div>
			<div class='enter-soc'>
					<p>Вход через соц сети</p>
					<a href="/social/login.php?go=vk"><img src="/img/new/social-vk.svg" width='50' height="50"></a>
				</div>
		</form>
		
		</div>
		<div class='forgotten'>
			<p style='margin-left:5px;'>Забыли пароль?&nbsp;<a style='color:green;' href="/signup/recovery">Восстановить</a></p>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if(isset($_GET['recovery'])): ?>
<div class='enter-popup '>
	<div class='form-wrap pre-popup' >

		<div class='flex-center' style="width:400px">
		<form action="/ajax/enter.php" method="post">
			<label for='email'> ВВЕДИТЕ СВОЙ E-MAIL</label><br><br>
			<input type="text" name="email" placeholder="example@gmail.com"><br><br>
			<input type="hidden" name="recovery">
			<button class='btn btn-main' onclick ='send(document.forms[0]);return false;'>ПОДТВЕРДИТЬ</button><br><br>
			<div class='flex-center' style='justify-content: space-between'>
				<button class='btn btn-extra' onclick ="location.href = '/' ; return false;">НА ГЛАВНУЮ</button>
				

			</div>
		</form>
		
		</div>
		
	</div>
</div>
<?php endif; ?>




	

<script type="text/javascript">




	function isMail(value){
		if(value.match(/^[a-zA-Z0-9-_]+\@[a-z]{2,15}\.[a-z]{1,6}$/))return true;
		else return false;
	}
       
        function isAgree(){
            let checkbox = document.querySelector('input[type=checkbox]');
            if(checkbox && !checkbox.checked){
                   alert("Вы не приняли соглашение!");
                   return false;
            }
           return true;    
        }
	function checkSamePassword(){
		
			let fields = document.querySelectorAll('input[type=password]');
			if(fields[0] && fields[0].value !== fields[1].value){
				alert('Пароли не совпадают!');
				fields[1].focus();
				return false;	
			}else return true;	
	}

	async function checkUniqueUser(username,email){

			let data = new FormData();
			data.set('username', username);
			data.set('email', email);

			let result = await fetch('/ajax/unique_user_test.php', {method: 'POST', body: data});
			let answer = await result.json();

			if(!answer[0])showCaution('Такое имя или почта уже были зарегистрированы!');
			else console.log('true')
	}


	function showCaution(message,toTheMainPage){
		alert(message);
		if(!toTheMainPage)
		return false;
		else location.href = '/';
	}

		
	function checkInputs(){
		if(document.forms[0]){
			for(let field of document.forms[0].children){
				if(field.nodeName === 'INPUT'){
					if(!field.value)return false;//console.log('not all value is fulfilled');
					if(field.name === 'email'){
						if(!isMail(field.value))return false;//console.log('mail is not valid');
						
					}
				}
				
			}
			return true;
		}
	}

	

	function getReaction(value){
		value = value.trim();
		
		if(value === 'registered'){
			document.getElementsByClassName('pre-popup')[0].style.display = 'none';
			document.getElementsByClassName('post-popup')[0].style.display = 'block';
		}
		if(value === 'loggedin'){
			location.href = '/account';
		}
                if(value === 'captcha_error'){
			showCaution("Ошибка! Вы не прошли проверку на робота! Попробуйте еще раз");
		}
		if(value === 'empty_or_online_error'){
			showCaution("Вы не зарегистрированы либо вы онлайн на другом устройстве!");
			location.href = '/';
		}
		if(value === 'mail_error'){
			showCaution("Ошибка! Письмо не было отправлено на вашу почту! Попробуйте еще раз");
		}
		if(value === 'login_error'){
			showCaution("Вы не были зарегистрированы!");
		}
		if(value === 'password_error'){
			showCaution('Пароль был введен неверно!');
		}
		if(value === 'repeat_error')
			showCaution("Такоя имя или почта уже используются!");
		if(value === 'recovery_fail')
			showCaution('Не удалось отправить письмо, попробуйте еще раз!');
		if(value === 'recovery_success')
			showCaution('Письмо было доставлено. Проверьте свою почту',true);
	}

	async function send(form){
                if(!isAgree())return;
		checkUniqueUser(form.username,form.email);
		if(!checkSamePassword()){
			return;
		}
		if(checkInputs()){
			let data = new FormData(form);

                       if(document.getElementsByClassName('g-recaptcha')[0]){
				data.set('captcha', 'captcha');
			} 

			let res = await fetch(document.forms[0].action, {method: 'POST', body: data});
			let ans = await res.text();
			console.log(ans)
			getReaction(ans);
		}else{
			showCaution("Вы заполнили не все поля");
		}
	}

	//send(document.forms[0])
	

</script>


<!-- <script type="text/javascript">
	function RecPwd() {
			$("#info-msg-aut").html("").hide();

			var log_user = $.trim($("#re_log_user").val());
			var email_user = $.trim($("#re_email_user").val());
			var wmid_user = $.trim($("#re_wmid_user").val());
			var send_wmid = $("#re_send_wmid").prop("checked") == true ? 1 : 0;
			
			var captha = $("#captha_v").val().trim();

			if (log_user == "" && email_user == "" && wmid_user == "") {
				$("#info-msg-aut").html('<span class="msg-error">Не указаны данные для восстановления пароля!</span>').slideToggle("fast");
				setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 3000);
				return false;
			} else if (captha == "") {
				$("#info-msg-aut").html('<span class="msg-error">Необходимо подтвердить, что Вы не робот!1</span>').slideToggle("fast");
				setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 3000);
				return false;
			} else {
				$.ajax({
					type: "POST", url: "recaptcha/ajax_recaptcha_rec.php?rnd="+Math.random(), 
					data: {'log_user':log_user, 'email_user':email_user, 'wmid_user':wmid_user, 'send_wmid':send_wmid, 'captha':captha}, 
					dataType: 'json',
					error: function() {
						$("#loading").slideToggle();
						$("#info-msg-aut").html('<span class="msg-error">Ошибка обработки данных ajax!</span>').slideToggle("fast");
						setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 5000);
						return false;
					}, 
					beforeSend: function() { $("#loading").slideToggle(); }, 

					success: function(data) {
						$("#loading").slideToggle();
						var result = data.result ? data.result : data;
						var message = data.message ? data.message : data;

						if (result == "OK") {
							$("#info-msg-aut").html('<span class="msg-ok">'+message+'</span>').slideToggle("fast");
							setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 3000);
							return false;
						} else {
							if (message) {
								
								$("#info-msg-aut").html('<span class="msg-error">'+message+'</span>').slideToggle("fast");
								setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 3000);
								return false;
							} else {
								
								$("#info-msg-aut").html('<span class="msg-error">Ошибка обработки данных!</span>').slideToggle("fast");
								setTimeout(function() {var tm; HideMsg("info-msg-aut");}, 3000);
								return false;
							}
						}
					}
				});
			}
		}
</script> -->