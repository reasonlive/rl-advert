<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


echo '<b>Редактирование оплачиваемого задания:</b><br><br>';


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_task' AND `howmany`='1'");
$cena_task = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='nacenka_task' AND `howmany`='1'");
$nacenka_task = $sql->fetch_object()->price;

if(count($_POST) > 0) {
	$zdname = (isset($_POST["zdname"])) ? limpiarez($_POST["zdname"]) : false;
	$zdname = limitatexto($zdname,100);
	$zdtext = (isset($_POST["zdtext"])) ? limpiarez($_POST["zdtext"]) : false;
	$zdtext = limitatexto($zdtext,3000);
	$zdtext2 = (isset($_POST["zdtext2"])) ? limpiarez($_POST["zdtext2"]) : false;
	$zdtext2 = limitatexto($zdtext2,2000);
	$zdurl = (isset($_POST["zdurl"])) ? limitatexto(limpiarez($_POST["zdurl"]),300) : false;
	$zdurl2 = (isset($_POST["zdurl2"])) ? getHost(limitatexto(limpiarez($_POST["zdurl2"]),300)) : false;
	$zdtype = (isset($_POST["zdtype"]) && (intval($_POST["zdtype"])==1 | intval($_POST["zdtype"])==2 | intval($_POST["zdtype"])==3 | intval($_POST["zdtype"])==4 | intval($_POST["zdtype"])==5 | intval($_POST["zdtype"])==6 | intval($_POST["zdtype"])==7 | intval($_POST["zdtype"])==8 | intval($_POST["zdtype"])==9 | intval($_POST["zdtype"])==10 | intval($_POST["zdtype"])==11)) ? intval($_POST["zdtype"]) : "11";
	$zdre = (isset($_POST["zdre"]) && (intval($_POST["zdre"])==0 | intval($_POST["zdre"])==1 | intval($_POST["zdre"])==3 | intval($_POST["zdre"])==6 | intval($_POST["zdre"])==12 | intval($_POST["zdre"])==24 | intval($_POST["zdre"])==48 | intval($_POST["zdre"])==72)) ? intval(limpiarez($_POST["zdre"])) : "0";
	$zdcountry = (isset($_POST["zdcountry"]) && (intval($_POST["zdcountry"])==0 | intval($_POST["zdcountry"])==1 | intval($_POST["zdcountry"])==2)) ? intval(limpiarez($_POST["zdcountry"])) : "0";
	$zdcheck = (isset($_POST["zdcheck"]) && (intval($_POST["zdcheck"])==1 | intval($_POST["zdcheck"])==2)) ? intval(limpiarez($_POST["zdcheck"])) : "1";
	$zdquest = (isset($_POST["zdquest"])) ? limpiarez($_POST["zdquest"]) : false;
	$zdquest = limitatexto($zdquest,255);
	$zdotv = (isset($_POST["zdotv"])) ? limpiarez($_POST["zdotv"]) : false;
	$zdotv = limitatexto($zdotv,255);
	$zdprice = (isset($_POST["zdprice"])) ? p_floor(abs(floatval(str_replace(",",".",trim($_POST["zdprice"])))),2) : "$cena_task";
	$wm_check = (isset($_POST["wm_check"]) && preg_match("|^[0-1]{1}$|", trim($_POST["wm_check"])) ) ? intval(trim($_POST["wm_check"])) : "0";
	
	if(strlen($zdname) < 1)
		echo '<fieldset class="errorp">Ошибка! Не указано название!</fieldset>';
	elseif($zdtext==false)
		echo '<fieldset class="errorp">Ошибка! Не указано описание!</fieldset>';
	elseif($zdtext2==false)
		echo '<fieldset class="errorp">Ошибка! Не указана информация для подтверждения!</fieldset>';
	elseif($zdurl==false)
		echo '<fieldset class="errorp">Ошибка! Не указана ссылка на сайт!</fieldset>';
	elseif(substr($zdurl, 0, 7) != "http://" && substr($zdurl, 0, 8) != "https://")
		echo '<fieldset class="errorp">Ошибка! Не верно указана ссылка на сайт!</fieldset>';
	elseif($zdcheck==2 && ($zdquest==false | $zdotv==false | strlen($zdquest) < 4 | strlen($zdotv) < 2) ) {
		if(strlen($zdquest) < 4)
			echo '<fieldset class="errorp">Ошибка! Не указан контрольный вопрос!</fieldset>';
		elseif(strlen($zdotv) < 2)
			echo '<fieldset class="errorp">Ошибка! Не указан ответ на контрольный вопрос!</fieldset>';
		else {}
	}elseif($zdprice<$cena_task) {
			echo '<fieldset class="errorp">Ошибка! Минимальная стоимость за выполнение задания '.number_format($cena_task,2,"."," ").' руб.</fieldset>';
	}else{
		$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid'");
		if($sql->num_rows>0) {
			$row = $sql->fetch_array();

			$mysqli->query("UPDATE `tb_ads_task` SET `wm_check`='$wm_check',`country_targ`='$zdcountry',`zdname`='$zdname',`zdtext`='$zdtext',`zdurl`='$zdurl',`zdurl2`='$zdurl2',`zdtype`='$zdtype',`zdre`='$zdre',`zdquest`='$zdquest',`zdotv`='$zdotv',`zdprice`='$zdprice' WHERE  `id`='$rid'") or die($mysqli->error);

			echo '<fieldset class="okp">Изменения успешно сохранены! <a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'">Перейти к списку заданий &gt;&gt;</a></fieldset>';
		}else{
			echo '<fieldset class="errorp">Ошибка! Такого задания нет! <a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'">Перейти к списку заданий &gt;&gt;</a></fieldset>';
			exit();
		}
	}
}else{
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_array();

		$id = $row["id"];
		$zdname = $row["zdname"];
		$zdtext = $row["zdtext"];
		$zdtext2 = $row["zdtext2"];
		$zdurl = $row["zdurl"];
		$zdtype = $row["zdtype"];
		$zdre = $row["zdre"];
		$zdcheck = $row["zdcheck"];
		$zdquest = $row["zdquest"];
		$zdotv = $row["zdotv"];
		$zdprice = $row["zdprice"];
		$zdcountry = $row["country_targ"];
		$wm_check = $row["wm_check"];
	}else{
		echo '<fieldset class="errorp">Ошибка! Такого задания нет! <a href="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'">Перейти к списку заданий &gt;&gt;</a></fieldset>';
		exit();
	}
}

if($zdcountry==1) {$sel_country1='selected="selected"';}else{$sel_country1="";}
if($zdcountry==2) {$sel_country2='selected="selected"';}else{$sel_country2="";}
if($zdtype==1) {$sel_type1='selected="selected"';}else{$sel_type1="";}
if($zdtype==2) {$sel_type2='selected="selected"';}else{$sel_type2="";}
if($zdtype==3) {$sel_type3='selected="selected"';}else{$sel_type3="";}
if($zdtype==4) {$sel_type4='selected="selected"';}else{$sel_type4="";}
if($zdtype==5) {$sel_type5='selected="selected"';}else{$sel_type5="";}
if($zdtype==6) {$sel_type6='selected="selected"';}else{$sel_type6="";}
if($zdtype==7) {$sel_type7='selected="selected"';}else{$sel_type7="";}
if($zdtype==8) {$sel_type8='selected="selected"';}else{$sel_type8="";}
if($zdtype==9) {$sel_type9='selected="selected"';}else{$sel_type9="";}
if($zdtype==10) {$sel_type10='selected="selected"';}else{$sel_type10="";}
if($zdtype==11) {$sel_type11='selected="selected"';}else{$sel_type11="";}
if($zdre==1)  {$sel_re1='selected="selected"';}else{$sel_re1="";}
if($zdre==3)  {$sel_re3='selected="selected"';}else{$sel_re3="";}
if($zdre==6)  {$sel_re6='selected="selected"';}else{$sel_re6="";}
if($zdre==12) {$sel_re12='selected="selected"';}else{$sel_re12="";}
if($zdre==24) {$sel_re24='selected="selected"';}else{$sel_re24="";}
if($zdre==48) {$sel_re48='selected="selected"';}else{$sel_re48="";}
if($zdre==72) {$sel_re72='selected="selected"';}else{$sel_re72="";}

echo '<div id="form">';
echo '<form id="form" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;option='.limpiar($_GET["option"]).'&amp;rid='.$rid.'" method="POST">';
echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="2" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
	echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Описание оплачиваемого задания</th></tr>';
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="left"><b>Заголовок задания:</b></td>';
		echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdname" maxlength="100" value="'.$zdname.'"></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Категория задания:</b></td>';
		echo '<td>&nbsp;<select name="zdtype">
			<option value="1" '.$sel_type1.'>Только регистрация</option>
			<option value="2" '.$sel_type2.'>Регистрация с активностью</option>
			<option value="3" '.$sel_type3.'>Только клики</option>
			<option value="4" '.$sel_type4.'>Активность на видео сервисах</option>
			<option value="5" '.$sel_type5.'>Социальные сети</option>
			<option value="6" '.$sel_type6.'>Подписка на рассылку</option>
			<option value="7" '.$sel_type7.'>Инвестиции</option>
			<option value="8" '.$sel_type8.'>Играть в игры</option>
			<option value="9" '.$sel_type9.'>Работа с каптчей</option>
			<option value="10" '.$sel_type10.'>Статьи / Отзывы</option>
			<option value="11" '.$sel_type11.'>Прочее</option>
		</select></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left" height="31"><b>Показ рекламы</b></td>';
		echo '<td>';
			echo '<select id="wm_check" name="wm_check">';
				echo '<option value="0" '.($wm_check==0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
				echo '<option value="1" '.($wm_check==1 ? 'selected="selected"' : false).'>Только пользователям с подтверждённым WMID</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td colspan="2" width="200" align="center"><b>Описание задания:</b></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td colspan="2">&nbsp;<textarea rows="7" name="zdtext" style="width:95%;">'.str_replace("<br>","\r\n", $zdtext).'</textarea></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td colspan="2" width="200" align="center"><b>Информация для подтверждения:</b></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td colspan="2">&nbsp;<textarea rows="7" name="zdtext2" style="width:95%;">'.str_replace("<br>","\r\n", $zdtext2).'</textarea></td>';
	echo '</tr>';
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="left"><b>Ссылка на сайт:</b></td>';
		echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdurl" maxlength="300" value="'.$zdurl.'"></td>';
	echo '</tr>';
	
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="left"><b>Повтор каждые XX ч. :</b></td>';
		echo '<td>&nbsp;<select name="zdre">
			<option value="0">Нет</option>
			<option value="1" '.$sel_re1.'>1 час</option>
			<option value="3" '.$sel_re3.'>3 часа</option>
			<option value="6" '.$sel_re6.'>6 часов</option>
			<option value="12" '.$sel_re12.'>12 часов</option>
			<option value="24" '.$sel_re24.'>24 часа (1 сутки)</option>
			<option value="48" '.$sel_re48.'>48 часа (2-е суток)</option>
			<option value="72" '.$sel_re72.'>72 часа (3-е суток)</option>
		</select></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Механизм проверки:</b></td>';
			if($zdcheck==1) {
				echo '<td>&nbsp;Ручной режим<input type="hidden" name="zdcheck" value="1"></td>';
			}else{
				echo '<td>&nbsp;Автоматический режим<input type="hidden" name="zdcheck" value="2"></td>';
			}
	echo '</tr>';
	echo '<tr bgcolor="#ADD8E6">';
		if($zdcheck==1) {
			echo '<td></td><td></td>';
		}else{
			echo '<td></td><td>&nbsp;<b style="color:#FF0000;">Внимание! Если указание на контрольное слово будет не точным, либо контрольное слово не будет сответствовать заданию, Администрация проекта по своему усмотрению может удалить не только такое задание, но и наложить штраф на аккаунт.</b></td>';
		}
	echo '</tr>';


	if($zdcheck==2) {
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Контрольный вопрос</th></tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td width="200" align="right"><b>Контрольный вопрос:</b><br>(от&nbsp;4&nbsp;до&nbsp;255&nbsp;символов)</td>';
			echo '<td>&nbsp;<textarea rows="3" name="zdquest" style="width:95%;">'.str_replace("<br>","\r\n", $zdquest).'</textarea></td>';
		echo '</tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right"><b>Ответ:</b><br>(от&nbsp;2&nbsp;до&nbsp;255&nbsp;символов)</td>';
			echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdotv" maxlength="255" value="'.$zdotv.'"></td>';
		echo '</tr>';
	}

	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Таргетинг по странам:</b></td>';
		echo '<td>&nbsp;<select name="zdcountry">
			<option value="0">Любые страны</option>
			<option value="1" '.$sel_country1.'>Только Россия</option>
			<option value="2" '.$sel_country2.'>Только Украина</option>
		</select></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Оплата исполнителю:</b></td>';
		echo '<td>&nbsp;<input type="text" size="7" style="text-align:right;" name="zdprice" maxlength="10" value="'.number_format($zdprice,2,".","").'">(минимум&nbsp;'.number_format($cena_task,2,".","").'&nbsp;руб.)</td>';
	echo '</tr>';
	echo '<tr align="center" bgcolor="#ADD8E6">';
		echo '<td colspan="2"><input type="submit" class="submit" value="&nbsp;&nbsp;&nbsp;Сохранить&nbsp;&nbsp;&nbsp;"></td>';
	echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';

exit();
?>