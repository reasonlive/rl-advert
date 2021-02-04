<?php

$_OPTIMIZATION['title'] = "Пополнение рекламного счета";

$user_id = $_COOKIE['_id'];
$db->Query("SELECT * FROM tb_users WHERE id=$user_id ");
$user = $db->FetchAssoc();

//$user['wm_purse'] = '12324332424';

$purses = array(

"Webmoney"			=> $user['wm_purse'],            
"YandexMoney"			 => $user['ym_purse'],
"Payeer"			 => $user['py_purse'],
'PerfectMoney'			 => $user['pm_purse'],
"Qiwi"			 => $user['qw_purse'],
"AdvaCash"			 => $user['ac_purse'],
"Maestro"			 => $user['me_purse'],
"MasterCard"			 => $user['ms_purse'],
"Visa"			 => $user['vs_purse'],
"Beeline"			 => $user['be_purse'],
"Mts"			 => $user['mt_purse'],
"Megafon"			 => $user['mg_purse'],
"Tele2"			 => $user['tl_purse']




);
			 





?>





<div class='profile-box'>
<center>



</center>
	
		<center class='line'>Пополнить счет</center>

				<p style='color:black'>Акция!
При пополнении рекламного счета через (Payeer, Webmoney и Яндекс) на сумму от 1000 руб. получите:
+50 руб. на рекламный счет
Бонус начисляется автоматически</p>
<hr>

			<div class='line-content'>

				<div>Выбрать метод оплаты</div>

				<select class='purses'>
					<?php foreach($purses as $purse => $val): ?>
						<?php if($val): ?>
						<option><?php echo $purse; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>

			<div class='line-content'>
				<p>Указать сумму для пополнения</p>
				<p><input type="number" name="cash_in" placeholder="1000" oninput='validateValue(this)'></p>
			</div>
			<center><input type='button' value='Пополнить'></input></center>
			<br>

			
			
			
			<center class='line'>Вывести средства</center>

			<p style='text-align: center'>
Укажите сумму для вывода в рублях максимум до: 200 рублей
Внимание: Вывод доступен каждые 2 часа</p>
			
			<hr>
			<div class='line-content'>
				<div>Выберите куда выводить</div>

				<select class='purses'>
					<?php foreach($purses as $purse => $val): ?>
						<?php if($val): ?>
						<option><?php echo $purse; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>

			</div>

			<div class='line-content'>
				<p>Указать сумму для вывода</p>
				<p><input type="number" name="cash_out" placeholder="200" max=200 oninput='validateValue(this)'></p>
			</div>
			<center><input type='button' value='Вывести'></input></center>
			<br>

			
			
				
				

			
	


</div>


<style type="text/css">
	.line-item{
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
	.profile-box{
		width:100%;
		background: cadetblue;
		color:black;
		font-weight: bold;
	}
	.line{
		padding: 5px 0 5px 0;
		background: rgba(0, 0, 0, 0.3);
		font-size:18px;
		font-weight: bold;
	}
	.line-content{
		display: flex;
		flex-direction: row;
		justify-content: space-around;
	}
	.change-inputs{
		margin:-5px 0;
		width:200px;
	}

	input,select {
		height:30px;
		border:none;
		outline: none;
  		box-sizing: border-box;
  		margin: -0.5px 0;
	}
	

	.names, .values, .change-inputs{
		display: flex;
		flex-direction: column;
	}
	.values{
		color:black;
	}
	

</style>

<script type="text/javascript">
	
function validateValue(elem){
	if(elem.value && elem.name == 'cash_in' && elem.value.match(/^[0-9]{1,6}$/)){
		return true;
	}else if(elem.value && elem.name == 'cash_out' && elem.value.match(/^[0-9]{1,3}$/)){
		if(parseInt(elem.value) < 201){
			return true;
		}else{
			elem.value = "";
			return false;
		}
		
		
	}else{
		elem.value = "";
		return false;
	}
}

</script>
