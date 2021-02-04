<?PHP

$_OPTIMIZATION['title'] = 'Профиль пользователя';

$user_id = $_SESSION['userID'];
$db->Query("SELECT * FROM tb_users WHERE id=$user_id ");
$user = $db->FetchAssoc();
?>




<div class='profile-box'>
	
		<center class='line'>Персональня информация</center>

			<div style='display:flex;flex-direction: column;justify-content: space-between;background: gray;'>

				<div class='user-info'>
					<div>
					<p>Имя</p>
					<p>Город</p>
					<p>Почта</p>
				         </div>
				<div style='color:black;font-weight: bold;'>
					<p><?php echo $user['username']; ?></p>
					<p><?php $city = $user['city_title'] ? $user['city_title'] : 'неизвестно';echo $city; ?></p>
					<p><?php $mail =  strlen($user['email']) > 5 ? $user['email'] : "неизвестно"; echo $mail; ?></p>
				</div>
				</div>
				<hr style='width:100%'>
					<div class='user-info' style='background: transparent;'>
						<div>
						<p>Выбрать аватарку</p>
						<p>Изменить имя</p>
						<p>Изменить город</p>
                                                <?php if(!$user['email']): ?>
                                                 <p>Указать почту</p>
                                                 <?php endif; ?>
						</div>
						<form method="POST" enctype="multipart/form-data">
						<div>
							<p><input type="file" name="ava"></p>
							<p><input type="text" name="username" placeholder="изменить имя"></p>
							<p><input type="text" name="city" placeholder="изменить город"></p>
                                                            <?php if(!$user['email']): ?>
                                                          <p><input type="email" name="email" placeholder="указать почту" onblur='validateEmail(this.value)'></p>
                                                              <?php endif; ?>
						</div>

						</form>

					</div>
					
					
				
			</div>

			<center class="line edit-btn" onclick='changeInfo()'>Сохранить</center>
			<br><br>
			<center class='line'>Смена Пароля</center>
			<div class='line-content'>
				
				 <p><input type="password" name="pass0" placeholder="введите старый пароль"></p>
				<p><input type="password" name="pass1" placeholder="введите новый пароль"></p>
				<p><input type="password" name="pass2" placeholder="повторите новый пароль"></p> 
				<!-- <p><button style='height:33px;'>Сохранить</button></p> -->

			</div>
			<center class="line edit-btn" onclick='changePass()'>Сохранить</center>
			<br><br>
			<center class='line'>Платежные реквизиты</center>
			
			<div class='line-content'>
				<div class='names'>

					<p>Номер счета WebMoney</p>
					<p>WMID (для WebMoney)</p>
					<p>Номер счета ЯндексДеньги</p>
					<p>Номер счета Payeer</p>
					<p>Номер счета PerfectMoney</p>
					<p>Номер счета Qiwi</p>
					<p>Номер счета AdvaCash</p>
					
					<p>Номер счета Maestro</p>
					<p>Номер счета MasterCard</p>
					<p>Номер счета Visa</p>
					
					<p>Номер телефона Билайн</p>
					<p>Номер телефона МТС</p>
					<p>Номер телефона Мегафон</p>
					<p>Номер телефона Теле2</p>
				</div>

				<div class='values'>
					<p><?php $payer = $user['wm_purse'] ? $user['wm_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['wmid'] ? $user['wmid'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['ym_purse'] ? $user['ym_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['py_purse'] ? $user['py_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['pm_purse'] ? $user['pm_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['qw_purse'] ? $user['qw_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['ac_purse'] ? $user['ac_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['me_purse'] ? $user['me_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['ms_purse'] ? $user['ms_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['vs_purse'] ? $user['vs_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['be_purse'] ? $user['be_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['mt_purse'] ? $user['mt_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['mg_purse'] ? $user['mg_purse'] : 'неизвестно';echo $payer; ?></p>
					<p><?php $payer = $user['tl_purse'] ? $user['tl_purse'] : 'неизвестно';echo $payer; ?></p>
					
				</div>

				<div class='change-inputs'>

					<p><input type="text" name="wm" placeholder="изменить номер"></p>
					<p><input type="text" name="wmid" placeholder="изменить номер"></p>
					<p><input type="text" name="ym" placeholder="изменить номер"></p>
					<p><input type="text" name="py" placeholder="изменить номер"></p>
					<p><input type="text" name='pm' placeholder="изменить номер"></p>
					<p><input type="text" name="qw" placeholder="изменить номер"></p>
					<p><input type="text" name="ac" placeholder="изменить номер"></p>

					<p><input type="text" name="me" placeholder="изменить номер"></p>
					<p><input type="text" name="ms" placeholder="изменить номер"></p>
					<p><input type="text" name="vs" placeholder="изменить номер"></p>

					<p><input type="text" name="be" placeholder="изменить номер"></p>
					<p><input type="text" name="mt" placeholder="изменить номер"></p>
					<p><input type="text" name="mg" placeholder="изменить номер"></p>
					<p><input type="text" name="tl" placeholder="изменить номер"></p>
					
				</div>
			</div>

			<center class="line edit-btn"   onclick='changePurses()'>Сохранить</center>
                         <br><br>
                        <center class="line edit-btn" style='color:red'   onclick='deleteProfile()'>Удалить профиль</center>
			
				
				
 
			
	


</div>

<style type="text/css">
	.line-item{
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
	.profile-box{
		width:100%;
		
		color:white;
	}
	.line{
		padding: 5px 0 5px 0;
		background: rgba(0, 0, 0, 0.3);
		font-size:18px;
		font-weight: bold;
	}
	.line-content{
		background: gray;
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
	.user-info{
		
		color:white;
		display: flex;
		flex-direction: row;
		justify-content: space-around;
	}

	input[type=text] {
		border:none;
		outline: none;
  		box-sizing: border-box;
  		margin: -0.5px 0;
	}
	input[type=password]{
		border:none;
		outline: none;
		height: 30px;
	}

	.names, .values, .change-inputs{
		display: flex;
		flex-direction: column;
	}
	.values{
		color:black;
	}
	.edit-btn:hover{
		cursor: pointer;
		color:black;
		background: lightgreen;
	}
	




</style>

<script type="text/javascript">

          function validateEmail(value){
           let mail  = document.querySelector('input[type=email]');
           if(!mail || !mail.value)return false;
           if(!mail.value.match(/^[a-zA-Z0-9-_]+\@[a-z]{2,15}\.[a-z]{1,6}$/)){
                   
                   alert("Некорректный email!");
                   mail.value = "";
           }
           return true;
	   
         }

        async function deleteProfile(){
         let id = <?php echo $user_id; ?>;
         let data = new FormData();
         data.set('id', id);
         data.set('delete', 'delete');
         let req = await fetch("/ajax/edit_profile.php", {method: "POST", body: data});
			let res = await req.text();
			res = res.trim()
			console.log(res)
                        if(res.match(/success/)){
                        alert('Ваш профиль успешно удален');
                        location.href = "/";
                        
                        }else{
                       alert("Произошла ошибка при удалении");
                       location.reload();
                        }
         }

	async function changePass(){
		let inputs = document.querySelectorAll('input[type=password]');
		if(inputs[0].value){
			let data = new FormData();
			data.set('pass0', inputs[0].value);
			let req = await fetch("/ajax/edit_profile.php", {method: "POST", body: data});
			let res = await req.text();
			res = res.trim()
			console.log(res)
			if(res !== 'success'){
				alert('Вы ввели неверный пароль!');
				inputs[0].value = "";
				inputs[0].focus();
				return;
			}
		}else{
			alert('Старый пароль не введен!');
			inputs[0].focus();
			return;
		}
		if(!inputs[1].value || !inputs[2].value){
			location.reload();
			return;
		}
		if(inputs[1].value && inputs[2].value && inputs[1].value === inputs[2].value){
			let data = new FormData();
			data.set('pass2', inputs[2].value);
			let req = await fetch("/ajax/edit_profile.php", {method: "POST", body: data});
			let res = await req.text();
			res = res.trim();
			if(res !== 'success'){
				alert('Ошибка записи пароля!');
				location.reload();
			}else{
				alert('Ваш пароль успешно изменен');
				location.reload();
			}
		}else{
			alert("Вы неверно повторили пароль!");
			inputs[2].focus();
		}

	}

       

	async function changeInfo(){

               let inputs = document.forms[0].elements;
               
		let count = 0;

		
		for(let i=0;i<inputs.length;i++){
                    if(inputs[i].value)count++;
                }
		if(!count){
			alert("Поля для заполнения пусты");
			return;
		}
                
                
		let data = new FormData(document.forms[0]);
                
		let req = await fetch("/ajax/edit_profile.php", {method: "POST", body: data});
		let res = await req.text();
		console.log(res)
		if(!res.match(/success/)){
			alert("Ошибка в обработке данных на сервере! Попробуйте еще раз");
			location.reload();
		}else{
			alert("Данные успешно обновлены");
			location.reload();
		}
	}

	function validatePurses(input){

		if(input.name == 'wm' && input.value && !input.value.match(/^R[0-9]{12}$/)){
			return input;
		}
		if(input.name == 'wmid' && input.value && !input.value.match(/^[0-9]{12}$/)){
			return input;
		}

		if(input.name == 'ym' && input.value && !input.value.match(/^41[0-9]{13}$/)){
			return input;
		}
		if(input.name == 'py' && input.value && !input.value.match(/^P[0-9]{7}$/)){
			return input;
		}

		if(input.name == 'pm' && input.value && (!input.value.match(/^U|E|G[0-9]{7}$/) || !input.value.match(/^B[0-9]{8}$/))){
			return input;
		}
		if(input.name == 'qw' && input.value && !input.value.match(/^79[0-9]{9}$/)){
			return input;
		}
		if(input.name == 'ac' && input.value && !input.value.match(/^U|E|R|H[0-9]{12}$/)){
			return input;
		}
		

		if((input.name == 'me' || input.name == 'ms' || input.name == 'vs')
							 && input.value && !input.value.match(/^[0-9]{16}$/)){
			return input;
		}

		if((input.name == 'be' || input.name == 'mt' || input.name == 'mg' || input.name == 'tl')
							 && input.value && !input.value.match(/^79[0-9]{9}$/)){
			return input;
		}

	}

	function gatherData(inputs){
		let data = {};
		for(let i=0;i<inputs.length;i++){
			if(inputs[i].value){
				data[inputs[i].name] = inputs[i].value;
			}
		}
		return data;
	}

	async function changePurses(){
		let failed = false;
		let btn = document.getElementsByClassName('edit-btn')[2];
		let inputs = document.querySelectorAll('input[placeholder="изменить номер"]');
		let wm = document.querySelector('input[name=wm]');
		let wmid = document.querySelector('input[name=wmid]');
		let count = 0;
		
		for(let i=0;i<inputs.length;i++){
			if(inputs[i].value)count++;
			let val = validatePurses(inputs[i]);
			if(val){
				failed = val;
				break;
			}
		}
		if(failed){
			failed.focus();
			alert('Вы ввели некорректные данные!');
			return;
		}
		if(!count){
			alert("Нужно ввести хотя бы 1 кошелек");
			return;
		}

		if((wm.value && !wmid.value) || (wmid.value && !wm.value)){
			if(!wmid.value){
				alert("Нужно ввести wmid для WebMoney!");
				wmid.focus();
				return;
			}
			if(!wm.value){
				alert("Нужно ввести WebMoney для WMID!");
				wm.focus();
				return;
			}
			
		}

		let data = gatherData(inputs);

		let sendData = new FormData();
		for(let key in data){
			sendData.set(key, data[key]);
		}

		let req = await fetch("/ajax/edit_profile.php", {method: "POST", body: sendData});
		let res = await req.text();
		res = res.trim();
		console.log(res)
		if(res === 'success'){
			alert("Данные платежных систем успешно внесены!");
			document.location.reload();
		}else{
			alert("Вы не смогли изменить данные! Произошла ошибка, повторите еще раз");
			document.location.reload();
		}
		

	}


</script>

