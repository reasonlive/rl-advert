<?PHP
$tfstats = time() - 60*60*24;

$db->Query("SELECT 
(SELECT COUNT(*) FROM tb_users) all_users, 
(SELECT SUM(money_out) FROM tb_users ) all_payment,
(SELECT transfers  FROM tb_pay_stat WHERE `date` > '$tfstats') today_payment, 
(SELECT COUNT(*) FROM tb_users WHERE joindate > '$tfstats') new");
$stats_data = $db->FetchArray();


?>

	<section class="" style='color:white;text-align: center'>
		<div>
			<h2>Покупка рекламы</h2>
			<p>Покупка от 5 рублей</p>
			<p style=" font-size: 18px;line-height: 25px;">
				AutoSerfLink, Просмотры сайтов, Серфинг, Авто-Серфинг, <span style='background: white'><span style="color: #3F3F3F;">You</span><span style="border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;">Tube</span></span>, Тесты, Письма, Задания, Конкурсы </p>
		</div>
		 <div >
			<h2>Заработок на трафе</h2>

			<p><b>Вебмастерам</b><br>
			Достаточно установить рекламный код и получать прибыль от 5 руб. за 1000 уникальных показов баннера. Также: SmartLink, Clickunder, AutoSerfLink: 13 руб. Сливайте траф удобным для вас способом и получайте дополнительные призы по конкурсу.</p>
		</div> 

		<div>
			<p><font size="5">Монетизация <a href="https://serfnets.ru/1serfnet.php"></a></font><font color="yellow">трафика</font> от 4 руб. за 1000 показов</p>
		</div>
		<div>
			
		</div>

				<div>
					<p><font color="yellow"><b>Рефоводам</b></font>
					<font color="yellow"><b>3х</b></font> ур. реф-система букса <font color="yellow"><b>50</b></font>% + <font color="yellow"><b>7</b></font>% + <font color="yellow"><b>2</b></font>%.
					Высокое вознаграждение в <font color="yellow"><b>10</b></font>% от пополнения счета вашим рефералом.
					Оплата переходов по рефке.</p>
					<p>* Настраиваемые бонусы своим Рефералам</p>
				</div>

	</section>
	 <!-- <div class='flex-center'>
		<iframe data-aa="906844" src="//ad.a-ads.com/906844?size=728x90" scrolling="no" style="width:728px; height:90px; border:0px; padding:0; overflow:hidden" allowtransparency="true"></iframe>

	</div>  -->

<div>
		
		<table cellpadding="3" cellspacing="0" border="0" bordercolor="#336633" align="center" width="99%" class="" style='color:white'>
<tbody>
<tr bgcolor="gray">
<td style="border: 1px dashed #db8;" align="center" class="m-tb" colspan="2"><b>Букс - Реферальная программа</b></td>
</tr>
<tr class="htt">
<td style="border: 1px dashed #db8;" align="left"><style=" margin-left:="" 0px;="" "=""> <b>Серфинг и Просмотры сайтов</b>
</style="></td>
 <td style="border: 1px dashed #db8;" align="left"><font color="green"><b style="
    margin-left: 10px;
">50% + 7% + 2%</b></font> от дохода
</td>
</tr>
<tr class="htt">
<td style="border: 1px dashed #db8;" align="left"><style=" margin-left:="" 0px;="" "=""> <b>Тесты, Письма, Задания</b>
</style="></td>
<td style="border: 1px dashed #db8;" align="left"><font color="green"><b style="
    margin-left: 10px;
">4% + 2%</b></font> от дохода
</td>
</tr><tr class="htt">
<td style="border: 1px dashed #db8;" align="left"><style=" margin-left:="" 0px;="" "=""> <b>За каждое пополнение реферала</b>
</style="></td>
<td style="border: 1px dashed #db8;" align="left"><font color="green"><b style="
    margin-left: 10px;
">10%</b></font> от суммы
</td>
</tr>
</tbody>
</table>
</div>
	
	<div>
		<h3 align="center" ;="" style="width: 100%; border: 1px Solid #ffe70b; padding: 0px; background-color: #fff;"><p><font color="#000">Выплаты от <font color="red">2</font> руб. каждые <font color="red">2</font> часа</font></p></h3>
	</div>	

	<div class='flex-center'>
		<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/icon-pe.png) no-repeat 50%;"></div>
<label>Payeer</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/webmoney.png) no-repeat 50%;"></div>
<label>Webmoney</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/yandex.png) no-repeat 50%;"></div>
<label>Яндекс.Деньги</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/be16x16.png) no-repeat 50%;"></div>
<label>БИЛАЙН</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/mg16x16.png) no-repeat 50%;"></div>
<label>МЕГАФОН</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/mts6x16.png) no-repeat 50%;"></div>
<label>МТС</label>
</div>
<div class="selectPS">
<div class="imagesps" style="background: url(/img/wm/tl16x16.png) no-repeat 50%;"></div>
<label>ТЕЛЕ2</label>
</div>

	</div>


		<center> <div style="margin-top:10px;">
	<div class="flex-center stat">
		<div class="st1">
			<center>
				<div class="count flex-center"><?=$stats_data["all_users"]; ?> чел.</div>
				<h3 id="pref-text">Пользователей</h3>
			</center>
		</div>
		<!-- <div class="st1">
			<center>
				<div class="count blue">+ <?=$stats_data["new"]; ?> чел.</div>
				<h3 id="pref-text">Активных пользователей</h3>
			</center>
		</div> -->
		
		<div class="st1">
			<center>
				<div class="count flex-center"><?=sprintf("%.2f",$stats_data["all_payment"]); ?> <i class="fa fa-rouble"></i></div>
				<h3 id="pref-text">Выплачено</h3>
			</center>
		</div>

		<div class="st1">
			<center>
				<div class="count flex-center"><?=sprintf("%.2f",$stats_data["today_payment"]); ?> <i class="fa fa-rouble"></i></div>
				<h3 id="pref-text">Выплачено за сутки</h3>
			</center>
		</div>

              
	</div></div>
</center>

		 
		 
		 
		 
