<?php

/*$pagetitle = "Конкурсы на сайте";
$pagdescription="На сайте присутствуют конкурсы:  рекламодатель недели, рекламодатель месяца, конкурс кликеров, конкурс  посетителей, комплексный,  по размещению ссылок в серфинге, реферальный конкурс, выполнения задания.";*/

$_OPTIMIZATION['title'] = 'коды и ссылки для размещения';
$_OPTIMIZATION['description'] = '';


$db->query("SELECT `price` FROM `tb_config` WHERE `item`='serfnets' AND `howmany`='1'") or die('sql error');
$serfnets =  $db->FetchRow();


$partnerid = $user_id;


echo '
<table border="0" width="100%" height="1%"> <td align="left" valign="middle"><h2 style=" border: 0px Solid #b7daab; padding: 5px; background-color: #fff;">

<p style=" font-size: 14px;line-height: 22px;">
<center><font color="red" size="5">Внимание!!!</font></center><br>

<p style=" font-size: 14px;line-height: 22px;">

<!--Вы получаете деньги, от <font color="red">'.$summa = ($serfnets * 1000).'</font> до <font color="red" size="2">13</font> руб.
за 1000 уникальных переходов по Реферальной ссылке, SmartLink, AutoSerfLink, Clickunder или показы баннера на вашем сайте! Заработанные деньги легко вывести на кошелек. Сливайте траф любым способом и получайте дополнительные призы по конкурсу.<br><br>-->';

 echo '<p style=" font-size: 14px;line-height: 22px;">Учитываются уники из России, Украины, Белоруссии, Казахстана.<br>'; 
echo '
<p style=" font-size: 14px;line-height: 17px;">По уникальности в пределах нашего ресурса, в течение 24 часов по IP.<br><br>
		   
		
		Статистика переходов >> <a href="/refhistory.php"><font color="red">Refhistory</font></a><br><br>
		   
		<font color="red">Запрещено:</font><br>
		- Размещать два кода на страницу. (Два кода ведут к уменьшению заработка)<br>
		- Покупать траф на ссылку с установленным кодом Serfnets.<br>
		- Заносить во iframe.<br>
		- Подменять HTTP рефер<br>
		- Рефку крутить в Авто-Серфинге<br><br>
                <font color="red">Также:</font> Рекламодателям важно, чтобы их рекламу заметили и перешли поэтому размещайте рекламу как можно выше.<br><br>


               <center><font color="red" size="2">Пример работы кода</font><br>
               <iframe src="//serfnets.ru/4bancod.php?r=1" width="468" height="70" scrolling="no" frameborder="0"></iframe>   <br><br>
			              </center>';
                 echo '<span class="msg-w" style="text-align:justify;">';
		         echo '<center>CPM коды на сайт и ссылки для монетизации. <br></center></span>';
			
		echo '<div id="newform" align="center">';
		
				
echo '<h2><p style=" font-size: 14px;line-height: 25px;">

<table border="0" cellpadding="0" cellspacing="0" width="90%" align=center>
	<tr>
        <td><input style="width:266px; text-align:center; margin:5px; padding:5px; display: block;" type="text" value="'.$config->url.'?r='.$partnerid.'" onmouseover="this.select()" readonly="readonly" /></td>
		<br><br>
Ваши реферальные ссылки. Платим <font color="red">'.$summa = ($serfnets * 1000).' руб. </font>
			за 1000 уник показов
		<td align="left" style="padding-right:16px"><input style="width:266px; text-align:center; margin:5px; padding:5px; display: block;" type="text" value="http://quarantinebux.site/?r='.$user_id.'" onmouseover="this.select()" readonly="readonly" />
</td>
	</tr>
</table></center></center></center>';

echo "<div class='serfnet-links' style='font-size:18px'>";
echo '<center>SmartLink: Прямая ссылка. Оплата <font color="red">13</font> руб. за 1000 уник переходов</center>';
echo '<textarea rows="2" cols="20" style="width:100%height:35px;" onfocus="this.select();" readonly="" class="ok">'.$config->url_smartlink.'4smartlink.php?r='.$user_id.'</textarea><br><br>';


echo '<center>AutoSerfLink: Для Авто-Серфинга. Оплата <font color="red">10</font> руб. за 1000 уник показов</center>';
echo '<textarea rows="2" cols="20" style="width:100%;height:35px;" onfocus="this.select();" readonly="" class="ok">'.$config->url_avtoserfLink.'?r='.$user_id.'</textarea><br><br>';


/*echo '<center>SerfLink: Для серфинга. Платим <font color="red">0.01</font> за уник показ</center>';
echo '<input style="width:266px; text-align:center; margin:5px; padding:5px; display: block;" type="text" value="'.$url_serfLink.'?r='.$user_id.'" onmouseover="this.select()" readonly="readonly" />
<br>';*/


                 //echo '<div id="newform" align="center">';
			echo 'Баннер + сайт рекламодателя. оплата <font color="red">'.$summa = ($serfnets * 1000).' руб. </font>
			за 1000 уникальных показов: ';

			
echo '<textarea rows="2" cols="30" style="width:100%;height:50px;" onfocus="this.select();" readonly="" class="ok">
<div id="serfnets_'.$partnerid.'" style="width: 468px;height: 60px;">
<script src="//'.$_SERVER["HTTP_HOST"].'/getjscode.php?r='.$partnerid.'" async></script>
</div></textarea>';
echo '<br><br>';


echo 'Баннер (fly left) + сайт рекламодателя. оплата <font color="red">0.0029</font> за уник показ: ';

echo '<textarea rows="2" cols="30" style="width:100%;height:65px;" onfocus="this.select();" readonly="" class="ok">
<div id="banner" style="z-index:1000; position: fixed; bottom:40px; left:5px">
<div id="serfnets_'.$partnerid.'" style="width: 468px;height: 60px;">
<script src="//'.$_SERVER["HTTP_HOST"].'/getfly.php?r='.$partnerid.'" async></script>
</div></div></textarea>';
echo '<br><br>';


echo '<center>Баннер. оплата <font color="red">1.20</font> руб. за 1000 уник показов:</center>';
echo '<textarea rows="2" cols="20" style="width:100%;height:35px;" onfocus="this.select();" readonly="" class="ok">
<iframe src="//'.$_SERVER["HTTP_HOST"].'/2bancod.php?r='.$partnerid.'" width="468" height="70" scrolling="no" frameborder="0"></iframe> </textarea>';

echo '<br><br>';

echo '<center>Лайт Баннер. оплата <font color="red">0.6</font> руб. за 1000 уник показов:</center>';
echo '<textarea rows="2" cols="20" style="width:100%;height:35px;" onfocus="this.select();" readonly="" class="ok">
<iframe src="//'.$_SERVER["HTTP_HOST"].'/4bancod.php?r='.$partnerid.'" width="468" height="70" scrolling="no" frameborder="0"></iframe> </textarea>';

echo '<br><br>';



/*echo '<center>SerfLink: Для серфинга. Оплата <font color="red" size="2">0.01</font> уник показ</center>';
echo '<textarea rows="2" cols="20" style="height:35px;" onfocus="this.select();" readonly="" class="ok">'.$url_smartlink.'?r='.$user_id.'</textarea>';

echo '<center>Вставьте этот JS-код в страницы вашего сайта: <font color="red" size="2">Тест</font></center>';
echo '<textarea rows="2" cols="20" style="height:35px;">
&lt;script type=&quot;text/javascript&quot; src=&quot;//'.$_SERVER["HTTP_HOST"].'/popup.php?id='.$user_id.'&quot;&gt;&lt;/script&gt;
</textarea>';



echo '<br><br><br><br>';
echo '<center>Старые коды</center>';
echo '<textarea rows="2" cols="20" style="height:40px;" onfocus="this.select();" readonly="" class="ok">
<iframe src="//'.$_SERVER["HTTP_HOST"].'/1bancod.php?r='.$partnerid.'" width="468" height="70" scrolling="no" frameborder="0"></iframe> </textarea>';*/

echo '<center>Clickunder. Оплата за клик в любом месте сайта <font color="red">тестируется 12</font> руб за 1000 уников</center>';
echo '<textarea rows="2" cols="20" style="width:100%;height:130px;" onfocus="this.select();" readonly="" class="ok">
<script>
	cr_flowid='.$partnerid.'; //ID вашего потока
	cr_subkey=""; //произвольный латинский ключ
	cr_timelimit=1440; // минут, открывать не чаще чем 
</script>
<script type="text/javascript" src="//serfnets.ru/jsunder.php?r='.$partnerid.'"></script></textarea><br><br>';


echo "<div>";
echo "</table>";



 ?>