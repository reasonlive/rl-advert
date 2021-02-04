<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}

function limpiarez($mensaje){
	global $mysqli;
	$mensaje = trim($mensaje);
	$mensaje = str_replace("'", "", $mensaje);
	$mensaje = str_replace("`", "", $mensaje);
	$mensaje = str_replace("  ", " ", $mensaje);

	$mensaje = preg_replace("#([-0-9a-z_\.]+@[-0-9a-z_\.]+\.[a-z]{2,6})#i", "", $mensaje);
	$mensaje = preg_replace("'<script[^>]*?>.*?</script>'si", "", $mensaje);
	$mensaje = preg_replace("'<[^>]*?>.*?'si", "", $mensaje);

	$mensaje = $mysqli->real_escape_string(trim($mensaje));
//	$mensaje = str_replace("\\", "", $mensaje);

	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("http://http://", "http://", $mensaje);
	$mensaje = str_replace("https://https://", "https://", $mensaje);
	$mensaje = str_replace("$", "&#036;", $mensaje);
	$mensaje = str_replace("&&", "&", $mensaje);
	$mensaje = str_replace("&amp;", "&", $mensaje);
	$mensaje = str_replace("&amp;quot;","&quot;",$mensaje);
	return $mensaje;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='1'");
$cena_mails_1 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='2'");
$cena_mails_2 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='3'");
$cena_mails_3 = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_color' AND `howmany`='1'");
$cena_mails_color = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_active' AND `howmany`='1'");
$cena_mails_active = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails_gotosite' AND `howmany`='1'");
$cena_mails_gotosite = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='nacenka_mails' AND `howmany`='1'");
$nacenka_mails = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='min_mails' AND `howmany`='1'");
$min_mails = $sql->fetch_object()->price;

$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;

if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
	$sql_user = $mysqli->query("SELECT `wmid`,`wm_purse`,`money_rb` FROM `tb_users` WHERE `username`='$username'");
	if($sql_user->num_rows>0) {
		$row_user = $sql_user->fetch_row();
		$wmid_user = $row_user["0"];
		$wmr_user = $row_user["1"];
		$money_user = $row_user["2"];
	}else{
		$username = false;
		$wmid_user = false;
		$wmr_user = false;
		$money_user = false;

		echo '<span class="msg-error">Пользователь не найден.</span><br>';
		include('footer.php');
		exit();
	}

}else{
	$username = false;
	$wmid_user = false;
	$wmr_user = false;
	$money_user = false;
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		echo '<span class="msg-error">Ошибка! Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		include('footer.php');
		exit();
	}else{
		$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_mails` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$plan = $row["plan"];
			$money_pay = $row["money"];
			$merch_tran_id = $row["merch_tran_id"];

			if($money_user>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1', `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_mails` SET `status`='1', `date`='".time()."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username'  ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) 
				VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы [рекламные письма], ID:#$id_pay','Списано','rashod')") or die($mysqli->error);

				stat_pay('mails', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 1);
				ActionRef(number_format($money_pay,2,".",""), $username);

				echo '<span class="msg-ok">Ваша ссылка успешно размещена!<br>Спасибо, что пользуетесь услугами нашего сервиса</span>';
				include('footer.php');
				exit();
			}else{
				echo '<span class="msg-error">Ошибка! На вашем рекламном счете недостаточно средств для оплаты заказа!</span>';
				include('footer.php');
				exit();
			}
		}else{
			echo '<span class="msg-error">Ошибка! Заказа рекламы с №'.$id_pay.' не существует, либо заказ уже был оплачен!</span>';
			include('footer.php');
			exit();
		}
	}
}

$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]),255) : false;
$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]),300) : false;
$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),5000) : false;
if (get_magic_quotes_gpc()) {$description = stripslashes($description);}
$question = (isset($_POST["question"])) ? limitatexto(limpiarez($_POST["question"]),255) : false;
$answer_t = (isset($_POST["answer_t"])) ? limitatexto(limpiarez($_POST["answer_t"]),255) : false;
$answer_f_1 = (isset($_POST["answer_f_1"])) ? limitatexto(limpiarez($_POST["answer_f_1"]),255) : false;
$answer_f_2 = (isset($_POST["answer_f_2"])) ? limitatexto(limpiarez($_POST["answer_f_2"]),255) : false;
$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) && intval(limpiarez(trim($_POST["plan"]))) > $min_mails ) ? intval(limpiarez(trim($_POST["plan"]))) : "$min_mails";
$tarif = (isset($_POST["tarif"]) && (intval($_POST["tarif"])==1 | intval($_POST["tarif"])==2 | intval($_POST["tarif"])==3)) ? intval(trim($_POST["tarif"])) : "2";
$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
$active = (isset($_POST["active"]) && (intval($_POST["active"])==0 | intval($_POST["active"])==1)) ? intval(trim($_POST["active"])) : "0";
$gotosite = (isset($_POST["gotosite"]) && (intval($_POST["gotosite"])==0 | intval($_POST["gotosite"])==1)) ? intval(trim($_POST["gotosite"])) : "0";
$mailsre = (isset($_POST["mailsre"]) && (intval($_POST["mailsre"])==0 | intval($_POST["mailsre"])==1)) ? intval(trim($_POST["mailsre"])) : "0";
$method_pay = (isset($_POST["method_pay"])) ? intval(trim($_POST["method_pay"])) : "1";
$tosaccept = (isset($_POST["tosaccept"]) && (intval($_POST["tosaccept"])==0 | intval($_POST["tosaccept"])==1)) ? intval(trim($_POST["tosaccept"])) : "0";
$black_url = @getHost($url);

if(count($_POST)>0) {

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'");
	if($sql_bl->num_rows>0 && $black_url!=false) {
		$row = $sql_bl->fetch_array();
		echo '<span class="msg-error">Сайт <a href="http://'.$row["domen"].'/" target="_blank" style="color:#0000FF">'.$row["domen"].'</a> заблокирован и занесен в черный список проекта '.strtoupper($_SERVER["HTTP_HOST"]).' !<br>Причина: '.$row["cause"].'</span>';
	}elseif($url==false | $url=="http://" | $url=="https://") {
		echo '<span class="msg-error">Не указана ссылка на сайт!</span>';
	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		echo '<span class="msg-error">Не верно указана ссылка на сайт!</span>';
	}elseif(is_url($url)!="true") {
		echo is_url($url);
	}elseif($title==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле Заголовок письма.</span><br>';
	}elseif($description==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле Содержание письма.</span><br>';
	}elseif($question==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле Контрольный вопрос.</span><br>';
	}elseif($answer_t==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле вариант ответа (правильный).</span><br>';
	}elseif($answer_f_1==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле вариант ответа (ложный).</span><br>';
	}elseif($answer_f_2==false) {
		echo '<span class="msg-error">Ошибка! Не заполнено поле вариант ответа (ложный).</span><br>';
	}elseif($tosaccept==0) {
		echo '<span class="msg-error">Ошибка! Необходимо прочитать правила размещения рекламы и согласиться с ними.</span><br>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url).'</span>';
	}else{
		$system_pay[1] = "WebMoney";
		$system_pay[2] = "RoboKassa";
		$system_pay[3] = "Wallet One";
		$system_pay[4] = "InterKassa";
		$system_pay[5] = "Payeer";
		$system_pay[6] = "Qiwi";
		$system_pay[7] = "PerfectMoney";
		$system_pay[8] = "YandexMoney";
		$system_pay[9] = "MegaKassa";
		$system_pay[20] = "FreeKassa";
		$system_pay[10] = "Рекламный счет";

		$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_mails' AND `howmany`='$tarif'");
		$cena_mails = $sql->fetch_object()->price;

		$price_one = ($cena_mails + ($color*$cena_mails_color) + ($active*$cena_mails_active) + ($gotosite*$cena_mails_gotosite)) * ($nacenka_mails+100)/100;
		$summa = round(($price_one * $plan),2);

	        $mysqli->query("DELETE FROM `tb_ads_mails` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+1 WHERE `id`='1'") or die($mysqli->error);

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_mails` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($sql_check->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_mails` SET `merch_tran_id`='$merch_tran_id', `date`='".time()."',`wmid`='$wmid_user',`title`='$title',`url`='$url',`description`='$description',`question`='$question',`answer_t`='$answer_t',`answer_f_1`='$answer_f_1',`answer_f_2`='$answer_f_2',`plan`='$plan',`tarif`='$tarif',`color`='$color',`active`='$active',`gotosite`='$gotosite',`mailsre`='$mailsre',`method_pay`='$method_pay',`totals`='$plan',`money`='$summa',`ip`='".$_SERVER['REMOTE_ADDR']."' WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_mails` (`status`,`merch_tran_id`,`date`,`session_ident`,`wmid`,`username`,`title`,`url`,`description`,`question`,`answer_t`,`answer_f_1`,`answer_f_2`,`plan`,`tarif`,`color`,`active`,`gotosite`,`mailsre`,`method_pay`,`totals`,`money`,`ip`) VALUES('0','$merch_tran_id','".time()."','".session_id()."','$wmid_user','$username','$title','$url','$description','$question','$answer_t','$answer_f_1','$answer_f_2','$plan','$tarif','$color','$active','$gotosite','$mailsre','$method_pay','$plan','$summa','$ip')") or die($mysqli->error);
		}

        	$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_mails` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
	        $id_zakaz = $sql_id->fetch_object()->id;

		$mysqli->query("ALTER TABLE `tb_ads_mails` ORDER BY `id`") or die($mysqli->error);
		$mysqli->query("OPTIMIZE TABLE `tb_ads_mails`") or die($mysqli->error);

		$tarif_to[1]="VIP (60 секунд)";
		$tarif_to[2]="STANDART (40 секунд)";
		$tarif_to[3]="LITE (20 секунд)";

		$color_to[0]="НЕТ";
		$color_to[1]="ДА (+".($cena_mails_color*($nacenka_mails+100)/100)." руб. к письму)";

		$active_to[0]="НЕТ";
		$active_to[1]="ДА (+".($cena_mails_active*($nacenka_mails+100)/100)." руб. к письму)";

		$gotosite_to[0]="НЕТ";
		$gotosite_to[1]="ДА (+".($cena_mails_gotosite*($nacenka_mails+100)/100)." руб. к письму)";

		$mailsre_to[0]="Доступно для прочтения 1 раз в месяц";
		$mailsre_to[1]="Доступно для прочтения каждые 24 часа";

		$sql_desc = $mysqli->query("SELECT `description` FROM `tb_ads_mails` WHERE `id`='$id_zakaz'");
		$description_to = $sql_desc->fetch_object()->description;
		require_once ("bbcode/bbcode.lib.php");
		$description_to = new bbcode($description_to);
		$description_to = $description_to->get_html();
		$description_to = str_replace("&amp;", "&", $description_to);
		
		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="200"><b>Счет №</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td>Заголовок письма</td><td>'.$title.'</td></tr>';
			echo '<tr><td>URL сайта</td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td>Содержание письма</td><td>'.$description_to.'</td></tr>';
			echo '<tr><td>Контрольный вопрос к письму:</td><td>'.$question.'</td></tr>';
			echo '<tr><td>Вариант ответа <span style="color: #009125;">(правильный)</span></td><td>'.$answer_t.'</td></tr>';
			echo '<tr><td>Вариант ответа <span style="color: #FF0000;">(ложный)</span></td><td>'.$answer_f_1.'</td></tr>';
			echo '<tr><td>Вариант ответа <span style="color: #FF0000;">(ложный)</span></td><td>'.$answer_f_2.'</td></tr>';
			echo '<tr><td>Количество просмотров</td><td>'.$plan.'</td></tr>';
			echo '<tr><td>Тарифный план</td><td>'.$tarif_to[$tarif].'</td></tr>';
			echo '<tr><td>Выделение цветом</td><td>'.$color_to[$color].'</td></tr>';
			echo '<tr><td>Активное окно</td><td>'.$active_to[$active].'</td></tr>';
			echo '<tr><td>Последующий переход на сайт</td><td>'.$gotosite_to[$gotosite].'</td></tr>';
			echo '<tr><td>Технология прочтения письма</td><td>'.$mailsre_to[$mailsre].'</td></tr>';
			echo '<tr><td>Стоимость одного письма</td><td><b>'.round(number_format($price_one,4,".","`"),4).' руб.</b></td></tr>';
			if(isset($cab_text)) echo "$cab_text";
			echo '<tr><td><b>Способ оплаты</b></td><td><b>'.$system_pay[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				echo '<tr><td><b>Стоимость заказа</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате</b></td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';
			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоиомсть заказа:</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';

			}elseif($method_pay==30) {
				echo '<tr><td><b>Сумма к оплате</b></td><td><b style="color:#FF0000;">'.number_format(($summa*$auto_opit),2,"."," ").'</b> <b>kb-exp.</b></td></tr>';
			}else{
				echo '<tr><td><b>Сумма к оплате</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
		echo '</table>';


		$shp_item = "11";
		$inv_desc = "Оплата рекламы: рекламные письма, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: rek mails, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once("".DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
	}
}
?><script type="text/javascript" language="JavaScript">
	function gebi(id){
		return document.getElementById(id)
	}

	function ShowHideBlock(id) {
		if($("#adv-title"+id).attr("class") == "adv-title-open") {
			$("#adv-title"+id).attr("class", "adv-title-close")
		} else {
			$("#adv-title"+id).attr("class", "adv-title-open")
		}
		$("#adv-block"+id).slideToggle("slow");
	}

	function SbmFormB() {
	        if (document.forms['formzakaz'].title.value == '') {
        	    alert('Вы не указали Заголовок письма');
	            document.forms['formzakaz'].title.focus();
        	    return false;
	        }
		if ((document.forms["formzakaz"].url.value == '')|(document.forms["formzakaz"].url.value == 'http://')) {
			alert('Вы не указали адрес ссылки. URL должен начинаться с префикса “http://” или “https://”.');
			document.forms["formzakaz"].url.focus();
			return false;
		}
	        if (document.forms['formzakaz'].description.value == '') {
        	    alert('Вы не указали Содержание письма');
	            document.forms['formzakaz'].description.focus();
        	    return false;
	        }
	        if (document.forms['formzakaz'].question.value == '') {
        	    alert('Вы не указали Контрольный вопрос');
	            document.forms['formzakaz'].question.focus();
        	    return false;
	        }
	        if (document.forms['formzakaz'].answer_t.value == '') {
        	    alert('Вы не указали вариант ответа (правильный)');
	            document.forms['formzakaz'].answer_t.focus();
        	    return false;
	        }
	        if (document.forms['formzakaz'].answer_f_1.value == '') {
        	    alert('Вы не указали вариант ответа (ложный)');
	            document.forms['formzakaz'].answer_f_1.focus();
        	    return false;
	        }
	        if (document.forms['formzakaz'].answer_f_2.value == '') {
        	    alert('Вы не указали вариант ответа (ложный)');
	            document.forms['formzakaz'].answer_f_2.focus();
        	    return false;
	        }
		if ((document.forms["formzakaz"].plan.value == '')|(document.forms["formzakaz"].plan.value < <?=$min_mails;?>)) {
			alert('Вы не указали количество просмотров, минимум <?=$min_mails;?>');
			document.forms["formzakaz"].plan.focus();
			return false;
		}
		if (document.forms["formzakaz"].tosaccept.checked == false) {
			alert('Если вы прочитали правила, с ними нужно согласиться');
			return false;
		}         
		document.forms["formzakaz"].submit();
		return true;
	}

	function number_format(number, decimals, dec_point, thousands_sep) {
		var minus = "";
		number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		if(number < 0){
			minus = "-";
			number = number*-1;
		}
		var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',

		toFixedFix = function(n, prec) {
			var k = Math.pow(10, prec);
			return '' + (Math.round(n * k) / k).toFixed(prec);
		};

		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}

		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return minus + s.join(dec);
	}

	function obsch(){
		var plan = gebi('plan').value;
		var tarif = gebi('tarif').value;
		var color = gebi('color').value;
		var active = gebi('active').value;
		var gotosite = gebi('gotosite').value;

		if(tarif==1) {var price = <?php echo $cena_mails_1;?>;}
		if(tarif==2) {var price = <?php echo $cena_mails_2;?>;}
		if(tarif==3) {var price = <?php echo $cena_mails_3;?>;}

		var cenacolor = <?php echo $cena_mails_color;?>;
		var cenaactive = <?php echo $cena_mails_active;?>;
		var cenagotosite = <?php echo $cena_mails_gotosite;?>;

		var price_one = (price + (color*cenacolor) + (active*cenaactive) + (gotosite*cenagotosite)) * <?php echo ($nacenka_mails+100)/100;?>;
		var price_sum = ( (price + (color*cenacolor) + (active*cenaactive) + (gotosite*cenagotosite)) * <?php echo ($nacenka_mails+100)/100;?> * plan);

		gebi('price_one_t').innerHTML = 'Стоимость одного просмотра';
		gebi('price_one').innerHTML = '<span style="color:#228B22;">' + number_format(price_one, 4, '.', ' ') + ' руб.</span> (без учета вашей накопительной скидки)';

		gebi('price_t').innerHTML = 'Стоимость заказа';
		gebi('prices').innerHTML = '<span style="color:#FF0000;">' + number_format(price_sum, 2, '.', ' ') + ' руб.</span> (без учета вашей накопительной скидки)';
	}

	function descchange() {
		var description = gebi('description').value;
		if(description.length > 5000) {
			gebi('description').value = description.substr(0,5000);
		}
		gebi('count').innerHTML = 'Осталось <b>'+(5000-description.length)+'</b> символов';
	}

	function addtag(text1, text2) {
		if ((document.selection)){
			gebi('description').focus();
			gebi('description').document.selection.createRange().text = text1+gebi('description').document.selection.createRange().text+text2;
		} else if(gebi('description').selectionStart != undefined) {
			var element = gebi('description');
			var str = element.value;
			var start = element.selectionStart;
			var length = element.selectionEnd - element.selectionStart;
			element.value = str.substr(0, start) + text1 + str.substr(start, length) + text2 + str.substr(start + length);
		} else gebi('description').value += text1+text2;
	}
</script><?php

	echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:6px;">';
		echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Оплачиваемые письма - что это?</span>';
		echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
			echo 'Оплачиваемые письма на <b style="color:#3A5FCD">'.strtoupper($_SERVER["HTTP_HOST"]).'</b> &mdash; это возможность заинтересовать огромную аудиторию людей и привлечь их на рекламируемый ресурс. ';
			echo 'В письмах можно более полно и красочно описать все достоинства и преимущества вашего сайта, тем самым донести до читателей намного больше информации чем в любой другой рекламной кампании. ';
			echo 'Чтобы убедится в том что пользователь прочитал письмо, он должен ответить на контрольный вопрос содержащийся в тексте письма. Просмотр сайта - подтверждается проверочным кодом.';
			echo '<br>';
		echo '</div>';

		echo '<span id="adv-title-rules" class="adv-title-open" onclick="ShowHideBlock(\'-rules\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Правила</span>';
		echo '<div id="adv-block-rules" style="display:block; padding:5px 7px 10px 7px; text-align:center; background-color:#FFFFFF;">';
			echo '<span class="warning-info" style="margin-bottom:0; font-weight:normal;">';
	                        echo 'Ответ на контрольный вопрос должен быть в содержании письма!<br>';
				echo 'При нарушении правил, рекламные площадки будут удаляться без возврата средств!';
			echo '</span>';
		echo '</div>';
	echo '</div>';

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';
	echo '<thead><th colspan="2" class="top">Форма заказа рекламы</th></thead>';
	echo '<tbody>';
		echo '<tr>';
			echo '<td align="left" width="240"><b>Заголовок письма</b></td>';
			echo '<td align="left"><input class="ok" name="title" maxlength="255" value="'.$title.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>URL сайта</b></td>';
			echo '<td align="left"><input class="ok" name="url" maxlength="300" value="'.$url.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Содержание письма &darr;</b></td>';
			echo '<td align="left">';
				echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:addtag(\'[b]\',\'[/b]\'); return false;">Ж</span>';
				echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:addtag(\'[i]\',\'[/i]\'); return false;">К</span>';
				echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:addtag(\'[u]\',\'[/u]\'); return false;">Ч</span>';
				echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:addtag(\'[url]\',\'[/url]\'); return false;">URL</span>';
				echo '<span id="count" style="display: block; float:right; color:#696969; margin-top:3px; margin-right:3px;"></span>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2" align="left">';
				echo '<textarea name="description" id="description" class="ok" style="height:150px; width:98.5%;" onkeydown="this.style.background=\'#FFFFFF\';" onkeyup="descchange();">'.$description.'</textarea>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Контрольный вопрос к письму</b></td>';
			echo '<td align="left"><input class="ok" name="question" maxlength="255" value="'.$question.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #009125;">(правильный)</span></td>';
			echo '<td align="left"><input class="ok" name="answer_t" maxlength="255" value="'.$answer_t.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
			echo '<td align="left"><input class="ok" name="answer_f_1" maxlength="255" value="'.$answer_f_1.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Вариант ответа <span style="color: #FF0000;">(ложный)</span></td>';
			echo '<td align="left"><input class="ok" name="answer_f_2" maxlength="255" value="'.$answer_f_2.'" type="text" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Количество просмотров</b></td>';
			echo '<td align="left"><input class="ok" name="plan" id="plan" maxlength="7" value="'.$plan.'" type="text" autocomplete="off" onChange="obsch();" onKeyUp="obsch();" ></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Тарифный план</b></td>';
			echo '<td align="left">';
				echo '<select name="tarif" id="tarif" onChange="obsch();" onClick="obsch();" >';
					echo '<option value="1" '.("1" == $tarif ? 'selected="selected"' : false).'>VIP (60 секунд): стоимость - '.($cena_mails_1 * ($nacenka_mails+100)/100).'руб./письмо</option>';
					echo '<option value="2" '.("2" == $tarif ? 'selected="selected"' : false).'>STANDART (40 секунд): стоимость - '.($cena_mails_2 * ($nacenka_mails+100)/100).'руб./письмо</option>';
					echo '<option value="3" '.("3" == $tarif ? 'selected="selected"' : false).'>LITE (20 секунд): стоимость - '.($cena_mails_3 * ($nacenka_mails+100)/100).'руб./письмо</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Выделить письмо</td>';
			echo '<td align="left">';
				echo '<select name="color" id="color" onChange="obsch();" onClick="obsch();" >';
					echo '<option value="0" '.("0" == $color ? 'selected="selected"' : false).'>НЕТ</option>';
					echo '<option value="1" '.("1" == $color ? 'selected="selected"' : false).'>ДА (+ '.($cena_mails_color * ($nacenka_mails+100)/100).'руб. к письму)</option>';
				echo '</select>';
		        echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Активное окно</td>';
			echo '<td align="left">';
				echo '<select name="active" id="active" onChange="obsch();" onClick="obsch();" >';
					echo '<option value="0" '.("0" == $active ? 'selected="selected"' : false).'>НЕТ</option>';
					echo '<option value="1" '.("1" == $active ? 'selected="selected"' : false).'>Да (+ '.($cena_mails_active * ($nacenka_mails+100)/100).'руб. к письму)</option>';
				echo '</select>';
		        echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left">Последующий переход на сайт</td>';
			echo '<td align="left">';
				echo '<select name="gotosite" id="gotosite" onChange="obsch();" onClick="obsch();" >';
					echo '<option value="0" '.("0" == $gotosite ? 'selected="selected"' : false).'>НЕТ</option>';
					echo '<option value="1" '.("1" == $gotosite ? 'selected="selected"' : false).'>Да (+ '.($cena_mails_gotosite * ($nacenka_mails+100)/100).'руб. к письму)</option>';
				echo '</select>';
		        echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Технология прочтения письма</b></td>';
			echo '<td align="left">';
				echo '<select name="mailsre" >';
					echo '<option value="0" '.("0" == $mailsre ? 'selected="selected"' : false).'>Доступно для прочтения 1 раз в месяц</option>';
					echo '<option value="1" '.("1" == $mailsre ? 'selected="selected"' : false).'>Доступно для прочтения каждые 24 часа</option>';
				echo '</select>';
		        echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Способ оплаты</b></td>';
			echo '<td align="left">';
				echo '<select name="method_pay">';
					require_once("".DOC_ROOT."/method_pay/method_pay_form.php");
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td id="price_one_t"></td>';
			echo '<td id="price_one"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td id="price_t"></td>';
			echo '<td id="prices"></td>';
		echo '</tr>';
		echo '<tr><td colspan="2"><input type="checkbox" name="tosaccept" value="1" '.("1" == $tosaccept ? 'checked="checked"' : false).' />С <a href="/tos.php#p3" target="_blank" title="Откроются в новом окне"><b>Правилами</b></a> размещения рекламы полностью согласен(на)</td></tr>';
		echo '<tr>';
			echo '<td colspan="2" align="center"><input type="submit" value="Оформить заказ" class="submit" /></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</form>';

?>

<script language="JavaScript"> obsch(); descchange(); </script>