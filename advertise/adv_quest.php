<?php
if (!DEFINED("ADVERTISE")) {die ("Hacking attempt!");}
?>

<script type="text/javascript">
function ShowHideBlock(id) {
	if($("#adv-title"+id).attr("class") == "adv-title-open") {
		$("#adv-title"+id).attr("class", "adv-title-close")
	} else {
		$("#adv-title"+id).attr("class", "adv-title-open")
	}
	$("#adv-block"+id).slideToggle("slow");
}

function setChecked(type){
	var nodes = document.getElementsByTagName("input");
	for (var i = 0; i < nodes.length; i++) {
		if (nodes[i].name == "country[]") {
			if(type == "paste") nodes[i].checked = true;
			else  nodes[i].checked = false;
		}
	}
}
</script>

<?php
function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("?","&#063;",$mensaje);
	$mensaje = str_replace(">","&#062;",$mensaje);
	$mensaje = str_replace("<","&#060;",$mensaje);
	$mensaje = str_replace("'","&#039;",$mensaje);
	$mensaje = str_replace("`","&#096;",$mensaje);
	$mensaje = str_replace("$","&#036;",$mensaje);
	$mensaje = str_replace('"',"&#034;",$mensaje);
	$mensaje = str_replace("  "," ",$mensaje);
	$mensaje = str_replace("&amp amp ","&",$mensaje);
	$mensaje = str_replace("&amp;amp;","&",$mensaje);
	$mensaje = str_replace("&&","&",$mensaje);
	$mensaje = str_replace("http://http://","http://",$mensaje);
	$mensaje = str_replace("https://https://","https://",$mensaje);
	$mensaje = str_replace("&#063;","?",$mensaje);
	return $mensaje;
}

if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
	echo '<span class="msg-error">Для доступа к этой странице необходимо авторизоваться!</span>';
}else{
	echo '<span class="msg-w">Функция платные вопросы больше не доступна!<br>Работает функция<a href="advertise.php?ads=tests"><span style="color:#FFFFFF;font-size:18px;"> - <u>Оплачиваемые тесты</u></span></a></span>';
/*
$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='quest_price'");
$cena = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE item='quest_color'");
$cena_color = $sql->fetch_object()->price;
$cena_color = number_format($cena_color,2,'.','');

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='quest_min'");
$min_plan = intval($sql->fetch_object()->price);


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

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_questions` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$plan = $row["plan"];
			$money_pay = $row["money"];

			if($money_user>=$money_pay) {
				$mysqli->query("UPDATE `tb_ads_questions` SET `status`='1', `date`='".time()."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_users` SET `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы: - Платные (оплачиваемые) вопросы $plan шт.','Списано','rashod')") or die($mysqli->error);

				stat_pay('quest', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 3);

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

if(count($_POST)>0) {
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),255) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
	$var1 = (isset($_POST["var1"])) ? limitatexto(limpiarez($_POST["var1"]),100) : false;
	$var2 = (isset($_POST["var2"])) ? limitatexto(limpiarez($_POST["var2"]),100) : false;
	$var3 = (isset($_POST["var3"])) ? limitatexto(limpiarez($_POST["var3"]),100) : false;
	$var4 = (isset($_POST["var4"])) ? limitatexto(limpiarez($_POST["var4"]),100) : false;
	$var5 = (isset($_POST["var5"])) ? limitatexto(limpiarez($_POST["var5"]),100) : false;
	$varok = ( isset($_POST["varok"]) && preg_match("|^[\d]{1,2}$|", trim($_POST["varok"])) ) ? intval(limpiarez(trim($_POST["varok"]))) : false;
	$color = (isset($_POST["color"]) && (intval($_POST["color"])==0 | intval($_POST["color"])==1)) ? intval(trim($_POST["color"])) : "0";
	$method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\d]{1,2}$|", intval(limpiarez($_POST["method_pay"])))) ? intval(limpiarez($_POST["method_pay"])) : false;
	$laip = getRealIP();
	$black_url = @getHost($url);

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
	}elseif($description==false) {
		echo '<span class="msg-error">Не указан вопрос на который пользователь должен ответить!</span><br>';
	}elseif($plan==false | $plan<$min_plan) {
		echo '<span class="msg-error">Минимальный заказ - '.$min_plan.'.</span><br>';
	}elseif(empty($var1)|empty($var2)|empty($var3)|empty($var4)|empty($var5)) {
		echo '<span class="msg-error">Необходимо указать варианты ответов, один из которых должен быть правильным!</span><br>';
	}elseif($var1==$var2|$var1==$var3|$var1==$var4|$var1==$var5|$var2==$var3|$var2==$var4|$var2==$var5|$var3==$var4|$var3==$var5|$var4==$var5) {
		echo '<span class="msg-error">Варианты ответов не должны совпадать друг с другом, проверьте правильность ввода данных!!!</span><br>';
	}elseif(empty($varok)|$varok<1|$varok>5) {
		echo '<span class="msg-error">Необходимо указать номер правильного ответа на вопрос!</span><br>';
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
$system_pay[21] = "AdvCash";
        $system_pay[10] = "Рекламный счет";
       
		$color_to[0]="НЕТ (0.00 руб.)";
		$color_to[1]="ДА (".number_format($cena_color,2,".","")." руб.)";

		$summa = round(($cena * $plan + $cena_color * $color),2);
		/* $summa = number_format(($summa * (100-$cab_skidka)/100),2,".",""); 

		$mysqli->query("DELETE FROM `tb_ads_questions` WHERE `status`='0' AND `date`<'".(time()-24*60*60)."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+1 WHERE `id`='1'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_questions` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_questions` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$wmid_user',`username`='$username', `plan`='$plan', `totals`='$plan', `color`='$color', `url`='$url', `description`='$description', `var1`='$var1', `var2`='$var2', `var3`='$var3', `var4`='$var4', `var5`='$var5', `var_ok`='$varok', `ip`='$laip', `date`='".time()."', `money`='$summa' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_questions` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`plan`,`totals`,`color`,`url`,`description`,`var1`,`var2`,`var3`,`var4`,`var5`,`var_ok`,`ip`,`date`,`money`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','$plan','$plan','$color','$url','$description','$var1','$var2','$var3','$var4','$var5','$varok','$laip', '".time()."','$summa')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_questions` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;


		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="150"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>URL сайта:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Количество ответов:</b></td><td>'.$plan.'</td></tr>';
			echo '<tr><td><b>Ваш вопрос:</b></td><td>'.$description.'</td></tr>';
			echo '<tr><td valign="top"><b>Варианты ответов:</b></td><td valign="top">1. '.$var1.'<br>2. '.$var2.'<br>3. '.$var3.'<br>4. '.$var4.'<br>5. '.$var5.'<br></td></tr>';
			echo '<tr><td><b>Номер правильного ответа:</b></td><td><font color="#FF0000">'.$varok.'</font></td></tr>';
			echo '<tr><td><b>Выделение цветом:</b></td><td>'.$color_to[$color].'</td></tr>';
			echo "$cab_text";
			echo '<tr><td><b>Способ оплаты:</b></td><td><b>'.$system_pay[$method_pay].'</b>, счет необходимо оплатить в течении 24 часов</td></tr>';
			@require_once("".$_SERVER['DOCUMENT_ROOT']."/curs/curs.php");
				$money_add_usd = number_format(round(($summa/$CURS_USD),2),2,".","");
				
			if($method_pay==8) {
				if(($summa*0.005)<0.01) {$money_add_ym = $summa + 0.01;}else{$money_add_ym = number_format(($summa*1.005),2,".","");}

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_ym,2,".","`").'</b> <b>руб.</b></td></tr>';
			
			}elseif($method_pay==3) {
				$money_add_w1 = number_format(($summa * 1.05), 2, ".", "");

				echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#76B15D;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#76B15D;">'.number_format($money_add_w1,2,".","`").'</b> <b>руб.</b></td></tr>';
				
			}elseif($method_pay==7) {
						echo '<tr><td><b>Стоимость заказа:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
						echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($money_add_usd,2,".","`").'</b> <b>USD</b></td></tr>';
			}else{
				echo '<tr><td><b>Сумма к оплате:</b></td><td><b style="color:#FF0000;">'.number_format($summa,2,".","`").'</b> <b>руб.</b></td></tr>';
			}
		echo '</table>';

		$shp_item = "14";
		$inv_desc = "Оплата рекламы: платные вопросы, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: cash questions, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once("".DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
	}
}else{
	?><script type="text/javascript" language="JavaScript"> 

	function gebi(id){
		return document.getElementById(id)
	}

	function SbmFormB() {
		arrayElem = document.forms["formzakaz"];
		var col=0;

		for (var i=0;i<arrayElem.length;i++){
			if ((document.forms["formzakaz"].url.value == '')|(document.forms["formzakaz"].url.value == 'http://')) {
				alert('Вы не указали URL-адрес сайта');
				arrayElem[i+0].style.background = "#FFDBDB";
				arrayElem[i+0].focus();
				return false;
			}else{
				arrayElem[i+0].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].plan.value == '')|(document.forms["formzakaz"].plan.value < <?=$min_plan;?> )) {
				alert('Мнимальное количество ответов <?=$min_plan;?>');
				arrayElem[i+1].style.background = "#FFDBDB";
				arrayElem[i+1].focus();
				return false;
			}else{
				arrayElem[i+1].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].desc.value == '')) {
				alert('Вы не указали Ваш вопрос');
				document.forms["formzakaz"].desc.style.background = "#FFDBDB";
				document.forms["formzakaz"].desc.focus();
				return false;
			}else{
				document.forms["formzakaz"].desc.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].var1.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].var1.style.background = "#FFDBDB";
				document.forms["formzakaz"].var1.focus();
				return false;
			}else{
				document.forms["formzakaz"].var1.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].var2.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].var2.style.background = "#FFDBDB";
				document.forms["formzakaz"].var2.focus();
				return false;
			}else{
				document.forms["formzakaz"].var2.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].var3.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].var3.style.background = "#FFDBDB";
				document.forms["formzakaz"].var3.focus();
				return false;
			}else{
				document.forms["formzakaz"].var3.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].var4.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].var4.style.background = "#FFDBDB";
				document.forms["formzakaz"].var4.focus();
				return false;
			}else{
				document.forms["formzakaz"].var4.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].var5.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].var5.style.background = "#FFDBDB";
				document.forms["formzakaz"].var5.focus();
				return false;
			}else{
				document.forms["formzakaz"].var5.style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].varok.value == '')) {
				alert('Вы не указали Варианты ответа');
				document.forms["formzakaz"].varok.style.background = "#FFDBDB";
				document.forms["formzakaz"].varok.focus();
				return false;
			}else{
				document.forms["formzakaz"].varok.style.background = "#FFFFFF";
			}
		}

		document.forms["formzakaz"].submit();
		return true;
	}

	function _obsch(){
		var plan = gebi('plan').value;
		var color = gebi('color').value;

		var cena = <?php echo $cena;?>;
		var cena_color = <?php echo $cena_color;?>;
		var price = plan * cena + color * cena_color;

		gebi('price').innerHTML = '<span style="color:#228B22;">' + cena.toFixed(2) + ' руб.</span>';
		gebi('pricet').innerHTML = '<b>Стоимость заказа:</b>';
		gebi('prices').innerHTML = '<span style="color:#FF0000;">' + price.toFixed(2) + ' руб.</span>';
		gebi('prices1').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices2').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices3').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices4').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices5').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices6').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices7').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices8').innerHTML = '<b style="color:#f6f9f6;">' + (price/60).toFixed(2) + '</b>';
		//gebi('prices9').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices10').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';
		gebi('prices11').innerHTML = '<b style="color:#f6f9f6;">' + price.toFixed(2) + '</b>';

	}

	function descchange() {
		var desc = gebi('desc').value;

		if(desc.length > 255) {
			gebi('desc').value = desc.substr(0,255);
		}
		gebi('count').innerHTML = 'Осталось <b>'+(255-desc.length)+'</b> символов';
	}
	</script><?php

	echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:15px;">';
		echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Платные вопросы - что это?</span>';
		echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Это один из самых эффективных способов рекламы Вашего сайта. Пользователь не только перейдет по ссылке на Ваш сайт, но и ГАРАНТИРОВАНО просмотрит его, в поисках правильного ответа на поставленный Вами вопрос. Один пользователь проекта сможет ответить на Ваш вопрос только ОДИН раз. При любом варианте ответа, пользователь НЕ имеет права отвечать на вопрос повторно. Таким образом Вы защищены от того, что пользователь просто переберет все варианты, без перехода на сайт. Посмотреть данную услугу в действии Вы можете на странице Вопросы.<br>';
		echo '<b>Стоимость и условия размещения:</b><br>';
		echo ' - Вопрос должен быть сформулирован четко, не допуская двойного трактования;<br>';
		echo ' - Ответ на вопрос должен находиться не далее 3-й (третьей) страницы после перехода по ссылке на ваш сайт;<br>';
		echo ' - На сайте не должно быть вредоносного кода;<br>';
		echo ' - Категорически запрещены ссылки на всевозможные т.н. "партнерки";<br>';
		echo ' - Для размещения вопроса, ответ на который требует скачивания файла/лов, воспользуйтесь разделом <a href="/advertise_files.php">Платные скачивания</a>. Вопросы, размещенные не в том разделе, будут удалены без возврата вложенных средств;<br>';
		echo ' - Размещение производится без какого-либо ограничения по времени. Вопрос будет удален из системы только по достижении того количества правильных ответов, которое Вы оплатите при заказе;<br>';
	echo '</div>';
	echo '<br><br>';

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';
	echo '<thead><th colspan="2" class="top">Форма заказа рекламы</th></thead>';
	echo '<tbody>';
		echo '<tr>';
			echo '<td width="200"><b>URL сайта (ссылка):</b></td>';
			echo '<td><input type="text" name="url" maxlength="160" value="http://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Количество правильных ответов:</b></td>';
			echo '<td><input type="text" name="plan" id="plan" value="50" class="ok12" style="text-align:center;" onChange="_obsch();" onKeyUp="_obsch();" onkeydown="this.style.background=\'#FFFFFF\';">&nbsp;&nbsp;&nbsp;(минимум '.$min_plan.')</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Ваш вопрос:</b><br>(Максимум 255 символов)</td>';
			echo '<td>';
				echo '<textarea name="description" id="desc" value="" onkeydown="this.style.background=\'#FFFFFF\';" onLoad="descchange();" onChange="descchange();" onKeyUp="descchange();"></textarea>';
				echo '<div align="right" id="count" style="color:#696969;"></div>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Введите 5 вариантов ответов:</b><br>один из которых должен быть правильным</td>';
			echo '<td>';
				echo '<input type="text" name="var1" maxlength="100" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"><br>';
				echo '<input type="text" name="var2" maxlength="100" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"><br>';
				echo '<input type="text" name="var3" maxlength="100" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"><br>';
				echo '<input type="text" name="var4" maxlength="100" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"><br>';
				echo '<input type="text" name="var5" maxlength="100" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"><br>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Укажите номер правильного ответа:</b></td>';
			echo '<td>';
				echo '1-<input type="radio" name="varok" value="1" onkeydown="this.style.background=\'#FFFFFF\';" style="margin:0px; background:none;">&nbsp;&nbsp;&nbsp;';
				echo '2-<input type="radio" name="varok" value="2" onkeydown="this.style.background=\'#FFFFFF\';" style="margin:0px; background:none;">&nbsp;&nbsp;&nbsp;';
				echo '3-<input type="radio" name="varok" value="3" onkeydown="this.style.background=\'#FFFFFF\';" style="margin:0px; background:none;">&nbsp;&nbsp;&nbsp;';
				echo '4-<input type="radio" name="varok" value="4" onkeydown="this.style.background=\'#FFFFFF\';" style="margin:0px; background:none;">&nbsp;&nbsp;&nbsp;';
				echo '5-<input type="radio" name="varok" value="5" onkeydown="this.style.background=\'#FFFFFF\';" style="margin:0px; background:none;">';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Выделить цветом:</b></td>';
			echo '<td>';
				echo '<select name="color" id="color" onChange="_obsch();" onClick="_obsch();">';
					echo '<option value="0">Нет</option>';
					echo '<option value="1">Да (+'.number_format($cena_color,2,".","'").' руб.)</option>';
				echo '</select>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td id=""><b>Стоимость одного ответа:</b></td>';
			echo '<td id="price"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td id="pricet"></td>';
			echo '<td id="prices"></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '<div class="blok" style="text-align:center;">';
	echo '<span id="adv-title3" class="adv-title-open" onclick="ShowHideBlock(3);">Выбрать способ оплаты</span>';
	echo '<div id="adv-block3" style="display:block;">';
	
		echo '<button id="method_pay"  name="method_pay" value="10" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rs1">';
      echo '<div><div><div><span class="line-green"><span id="prices1"></span> руб.</span></div></div></div>';
	  echo '</div> </button>';
	
    if($site_pay_wm!=1) {
        echo '<div class="cash-wm1">';
    	  echo '<div class="cash-wm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="1" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-wm1">';
    echo '<div><div><div><span class="line-green"><span id="prices2"></span> (+0.8%) руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_ym!=1) {
        echo '<div class="cash-yd1">';
    	  echo '<div class="cash-yd1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="8" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-yd1">';
    echo '<div><div><div><span class="line-green"><span id="prices3"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_robo!=1) {
        echo '<div class="cash-rb1">';
    	  echo '<div class="cash-rb1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="2" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-rb1">';
    echo '<div><div><div><span class="line-green"><span id="prices4"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}

	if($site_pay_mega!=1) {
    	  echo '<div class="cash-ik1">';
    	  echo '<div class="cash-ik1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
	echo '<button id="method_pay" name="method_pay" value="9" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ik1">';
    echo '<div><div><div><span class="line-green"><span id="prices5"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
 
  if($site_pay_qw!=1) {
    	  echo '<div class="cash-qw1">';
    	  echo '<div class="cash-qw1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="6" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-qw1">';
    echo '<div><div><div><span class="line-green"><span id="prices6"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_payeer!=1) {
    	  echo '<div class="cash-pr1">';
    	  echo '<div class="cash-pr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
    echo '<button id="method_pay" name="method_pay" value="5" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pr1">';
    echo '<div><div><div><span class="line-green"><span id="prices7"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
  
  if($site_pay_pm!=1) {
    	  echo '<div class="cash-pm1">';
    	  echo '<div class="cash-pm1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
	}else{
     echo '<button id="method_pay" name="method_pay" value="7" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-pm1" >';
     echo '<div><div><div><span class="line-green"><span id="prices8"></span> руб.</span></div></div></div>';
	 echo '</div> </button>';
	}
	
	if($site_pay_free!=1) {
    	  echo '<div class="cash-fr1">';
    	  echo '<div class="cash-fr1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
    	  echo '</div>';
	}else{
    echo '<button name="method_pay" value="20" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-fr1">';
    echo '<div><div><div><span class="line-green"><span id="prices10"></span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	if($site_pay_advcash!=1) {
    	  echo '<div class="cash-ah1">';
    	  echo '<div class="cash-ah1">';
    	  echo '<div><div><div<span class="line-red">Временно отключен</span></div></div></div>';
    	  echo '</div>';
    	  echo '</div>';
	}else{
    echo '<button name="method_pay" value="21" style="background-color: #eff0f1; color: black; border: 2px solid #eff0f1;"><div class="cash-ah1">';
    echo '<div><div><div><span class="line-green"><span id="prices11">1</span> руб.</span></div></div></div>';
	echo '</div> </button>';
	}
	
	 echo '</div>';
	echo '</form>';
}
*/
}
?>

<script language="JavaScript"> descchange(); _obsch(); </script>