<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("'"," ",$mensaje);
	$mensaje = str_replace(";"," ",$mensaje);
	$mensaje = str_replace("$","$",$mensaje);
	$mensaje = str_replace("<","&#60;",$mensaje);
	$mensaje = str_replace(">","&#62;",$mensaje);
	$mensaje = str_replace("\\","",$mensaje);
	$mensaje = str_replace("&amp amp ","&amp;",$mensaje);
	$mensaje = str_replace("&amp quot ","&quot;",$mensaje);
	$mensaje = str_replace("&amp gt ","&gt;",$mensaje);
	$mensaje = str_replace("&amp lt ","&lt;",$mensaje);
	$mensaje = str_replace("\r\n","<br>",$mensaje);
	return $mensaje;
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_task' AND `howmany`='1'");
$cena_task = $sql->fetch_object()->price;


$zdname = (isset($_POST["zdname"])) ? limpiarez($_POST["zdname"]) : false;
$zdname = limitatexto($zdname,100);
$zdtext = (isset($_POST["zdtext"])) ? limpiarez($_POST["zdtext"]) : false;
$zdtext = limitatexto($zdtext,1000);
$zdtext2 = (isset($_POST["zdtext2"])) ? limpiarez($_POST["zdtext2"]) : false;
$zdtext2 = limitatexto($zdtext2,1000);
$zdurl = (isset($_POST["zdurl"])) ? limitatexto(limpiarez($_POST["zdurl"]),300) : false;
$zdurl2 = (isset($_POST["zdurl2"])) ? getHost(limitatexto(limpiarez($_POST["zdurl2"]),300)) : false;

$zdtype = (isset($_POST["zdtype"]) && (intval($_POST["zdtype"])==1 | intval($_POST["zdtype"])==2 | intval($_POST["zdtype"])==3 | intval($_POST["zdtype"])==4 | intval($_POST["zdtype"])==5 | intval($_POST["zdtype"])==6 | intval($_POST["zdtype"])==7 | intval($_POST["zdtype"])==8)) ? intval($_POST["zdtype"]) : "8";
$zdre = (isset($_POST["zdre"]) && (intval($_POST["zdre"])==0 | intval($_POST["zdre"])==1 | intval($_POST["zdre"])==3 | intval($_POST["zdre"])==6 | intval($_POST["zdre"])==12 | intval($_POST["zdre"])==24 | intval($_POST["zdre"])==48 | intval($_POST["zdre"])==72)) ? intval(limpiarez($_POST["zdre"])) : "0";
$zdcountry = (isset($_POST["zdcountry"]) && (intval($_POST["zdcountry"])==0 | intval($_POST["zdcountry"])==1 | intval($_POST["zdcountry"])==2)) ? intval(limpiarez($_POST["zdcountry"])) : "0";
$zdcheck = (isset($_POST["zdcheck"]) && (intval($_POST["zdcheck"])==1 | intval($_POST["zdcheck"])==2)) ? intval(limpiarez($_POST["zdcheck"])) : "1";
$zdquest = (isset($_POST["zdquest"])) ? limpiarez($_POST["zdquest"]) : false;
$zdquest = limitatexto($zdquest,255);
$zdotv = (isset($_POST["zdotv"])) ? limpiarez($_POST["zdotv"]) : false;
$zdotv = limitatexto($zdotv,255);
$zdprice = (isset($_POST["zdprice"])) ? p_floor(abs(floatval(str_replace(",",".",trim($_POST["zdprice"])))),2) : "$cena_task";
$wm_check = (isset($_POST["wm_check"]) && preg_match("|^[0-1]{1}$|", trim($_POST["wm_check"])) ) ? intval(trim($_POST["wm_check"])) : "0";

$sel1=""; $sel="";
if($zdcheck==2){
	$display = 'style="display:block;"';
	$sel1="";
	$sel2='selected="selected"';
}else{
	$display = 'style="display:none;"';
	$sel1='selected="selected"';
	$sel2="";
}

if(count($_POST) > 0) {
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
		if($zdcheck==1) {$zdquest=""; $zdotv="";}

		$mysqli->query("INSERT INTO `tb_ads_task` (`status`,`wm_check`,`date_up`,`country_targ`,`username`,`user_id`,`zdname`,`zdtext`,`zdurl`,`zdurl2`,`zdtype`,`zdre`,`zdcheck`,`zdquest`,`zdotv`,`zdprice`,`date_add`,`date_act`) 
		VALUES('wait','$wm_check','".time()."','$zdcountry','$username','$partnerid','$zdname','$zdtext','$zdurl','$zdurl2','$zdtype','$zdre','$zdcheck','$zdquest','$zdotv','$zdprice','".time()."','".time()."')") or die($mysqli->error);

		echo '<fieldset class="okp">Задание добавлено!</fieldset>';

		echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view");</script>';
		echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view">';

		
		exit();
	}
}

echo '<div id="form">';
echo '<form id="form" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page='.limpiar($_GET["page"]).'" method="POST">';
echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="2" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
	echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Описание оплачиваемого задания</th></tr>';
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="right"><b>Название:</b></td>';
		echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdname" maxlength="100" value="'.$zdname.'"></td>';
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
	echo '<tr>';
		echo '<td align="left" height="31"><b>Показ рекламы</b></td>';
		echo '<td>';
			echo '<select id="wm_check" name="wm_check">';
				echo '<option value="0" '.($wm_check==0 ? 'selected="selected"' : false).'>Всем пользователям проекта</option>';
				echo '<option value="1" '.($wm_check==1 ? 'selected="selected"' : false).'>Только пользователям с подтверждённым WMID</option>';
			echo '</select>';
		echo '</td>';
	echo '</tr>';

	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="right"><b>Ссылка на сайт:</b></td>';
		echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdurl" maxlength="300" value="'.$zdurl.'"></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="right"><b>Категория:</b></td>';
		echo '<td>&nbsp;<select name="zdtype">
			<option value="1">Клики</option>
			<option value="2">Регистрация без активности</option>
			<option value="3">Регистрация с активностью</option>
			<option value="4">Постинг в форум</option>
			<option value="5">Постинг в блоги</option>
			<option value="6">Голосование</option>
			<option value="7">Загрузка файлов</option>
			<option value="8">Прочее</option>
		</select> Если Вы неправильно выберите раздел - <b>Штраф 1.00 руб.</b></td>';
	echo '</tr>';
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="left"><b>Повтор каждые XX ч. :</b></td>';
		echo '<td>&nbsp;<select name="zdre">
			<option value="0">Нет</option>
			<option value="1">1 час</option>
			<option value="3">3 часа</option>
			<option value="6">6 часов</option>
			<option value="12">12 часов</option>
			<option value="24">24 часа (1 сутки)</option>
			<option value="48">48 часа (2-е суток)</option>
			<option value="72">72 часа (3-е суток)</option>
		</select></td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Механизм проверки:</b></td>';
		echo '<td>&nbsp;<select name="zdcheck">
			<option value="1" onclick="document.getElementById(\'quest\').style.display=\'none\'" '.$sel1.'>Ручной режим</option>
			<option value="2" onclick="document.getElementById(\'quest\').style.display=\'block\'" '.$sel2.'>Автоматический режим</option>
		</select></td>';
	echo '</tr>';
echo '</table>';

echo '<div id="quest" '.$display.'>';
echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="2" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
	echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Контрольный вопрос</th></tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="right"><b>Контрольный вопрос:</b><br>(от&nbsp;4&nbsp;до&nbsp;255&nbsp;символов)</td>';
		echo '<td>&nbsp;<textarea rows="3" name="zdquest" style="width:95%;">'.str_replace("<br>","\r\n", $zdquest).'</textarea></td>';
	echo '</tr>';
	echo '<tr bgcolor="#ADD8E6">';
		echo '<td width="200" align="right"><b>Ответ:</b><br>(от&nbsp;2&nbsp;до&nbsp;255&nbsp;символов)</td>';
		echo '<td>&nbsp;<input type="text" style="width:95%;" name="zdotv" maxlength="255" value="'.$zdotv.'"></td>';
	echo '</tr>';
echo '</table>';
echo '</div>';

echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="2" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
	echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Настройка выполнения задания</th></tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="left"><b>Стоимость выполнения:</b></td>';
		echo '<td>&nbsp;<input type="text" size="7" style="text-align:right;" name="zdprice" maxlength="10" value="'.number_format($zdprice,2,".","").'">(минимум&nbsp;'.number_format($cena_task,2,".","").'&nbsp;руб.)</td>';
	echo '</tr>';
	echo '<tr bgcolor="#AFEEEE">';
		echo '<td width="200" align="right"><b>Таргетинг по странам:</b></td>';
		echo '<td>&nbsp;<select name="zdcountry">
			<option value="0">Любые страны</option>
			<option value="1">Только Россия</option>
			<option value="2">Только Украина</option>
		</select></td>';
	echo '</tr>';
	echo '<tr align="center" bgcolor="#ADD8E6">';
		echo '<td colspan="2"><input type="submit" class="submit" value="Добавить задание"></td>';
	echo '</tr>';
echo '</table>';
echo '</form>';
echo '</div>';

?>