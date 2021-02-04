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
if(!isset($testdrive_status)) {
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='testdrive_youtube_status' AND `howmany`='1'");
	$testdrive_status = $sql->fetch_object()->price;
}

if($testdrive_status==0) {
	echo '<span class="msg-error">Функция Test Drive временно не доступна!</span>';
	include('footer.php');
	exit();
}

if(!isset($testdrive_count)) {
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='testdrive_youtube_count' AND `howmany`='1'");
	$testdrive_count = $sql->fetch_object()->price;
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='testdrive_youtube_timer' AND `howmany`='1'");
$testdrive_timer = $sql->fetch_object()->price;

$plan = $testdrive_count;
$timer = $testdrive_timer;
$type_serf = -1;

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

$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;

if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
	$sql_user = $mysqli->query("SELECT `wmid`,`wm_purse`,`money_rb`,`test_drive_youtube`,`reiting` FROM `tb_users` WHERE `username`='$username'");
	if($sql_user->num_rows>0) {
		$row_user = $sql_user->fetch_row();
		$wmid_user = $row_user["0"];
		$wmr_user = $row_user["1"];
		$money_user = $row_user["2"];
		$test_drive_youtube = $row_user["3"];

		if($test_drive_youtube==1) {
			echo '<span class="msg-error">Вы уже использовали функцию TEST DRIVE!</span>';
			include('footer.php');
			exit();
		}elseif($reiting<300) {       
                echo '<span class="msg-error">Для использовали функцию TEST DRIVE у вас должен быть статус <b>Рабочий</b> или выше!</span>';
                        include('footer.php');
                        exit();
                }

		$sql_b = $mysqli->query("SELECT * FROM `tb_black_users` WHERE `name`='$username' ORDER BY `id` DESC");
		if($sql_b->num_rows>0) {
			$row_b = $sql_b->fetch_assoc();
			$prichina = $row_b["why"];
			$kogda = $row_b["date"];

			echo '<span class="msg-error">Ваш аккаунт заблокирован! Вы не можете использовать функцию TEST DRIVE!<br><u>Причина блокировки</u>: '.$row_b["why"].'<br><u>Дата блокировки</u>: '.$row_b["date"].'</span>';
			include('footer.php');
			exit();
		}

	}else{
		echo '<span class="msg-error">Для использования функции TEST DRIVE необходимо зарегистрироваться!</span>';
		include('footer.php');
		exit();
	}

}else{
	echo '<span class="msg-error">Для использования функции TEST DRIVE необходимо зарегистрироваться!</span>';
	include('footer.php');
	exit();
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"])) {
		$id_pay = (isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"]))) ? intval(limpiar(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_youtube` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {

			$mysqli->query("UPDATE `tb_users` SET `test_drive_youtube`='1' WHERE `username`='$username'") or die($mysqli->error);
			$mysqli->query("UPDATE `tb_ads_youtube` SET `status`='1', `date`='".time()."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username'  ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);

			ads_date();

			echo '<span class="msg-ok">Реклама успешно добавлена!<br>Спасибо, что пользуетесь услугами нашего сервиса!</span>';
			include('footer.php');
			exit();
		}else{
			echo '<span class="msg-error">Реклама не найдена!</span>';
			include('footer.php');
			exit();
		}
	}else{
		echo '<span class="msg-error">Для использования функции TEST DRIVE необходимо зарегистрироваться!</span>';
		include('footer.php');
		exit();
	}
}



if(count($_POST)>0) {
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]),60) : false;
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),80) : false;
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$content = (isset($_POST["content"]) && (intval($_POST["content"])==0 | intval($_POST["content"])==1)) ? intval($_POST["content"]) : "0";
	$color = 0;
	$active = 0;
	$country = false;
	$revisit = 0;
	$nolimitdate = 0;
	$limit_d = $plan;
	$limit_h = $plan;
	$method_pay = "-2";
	$laip = getRealIP();
	$black_url = @getHost($url);
	$p = explode("youtu.be/", $_POST["url"]);

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
		echo '<span class="msg-error">Вы не указали заголовок ссылки.</span><br>';
	/*}elseif($description==false) {
		echo '<span class="msg-error">Вы не указали краткое описание ссылки.</span><br>';*/
	}elseif(count($p)<='1') {
		echo '<span class="msg-error">Не верно указана ссылка youtub!</span>';
	}elseif(@getHost($url)!=$_SERVER["HTTP_HOST"] && SFB_YANDEX($url)!=false) {
		echo '<span class="msg-error">'.SFB_YANDEX($url).'</span>';
	}else{
		$color_to[0]="НЕТ";
		$color_to[1]="ДА";

		$content_to[0]="НЕТ";
		$content_to[1]="ДА";

		$active_to[0]="НЕТ";
		$active_to[1]="ДА";

		$revisit_to[0] = "Каждые 24 часа";
		$revisit_to[1] = "Каждые 48 часов";
		$revisit_to[2] = "1 раз";

		$timer_to = "$timer";
		
		$img_youtube='https://img.youtube.com/vi/'.$p[1].'/1.jpg';
	    $ytflag=1;

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die($mysqli->error);
		$mysqli->query("DELETE FROM `tb_ads_youtube` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_youtube` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($sql_check->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_youtube` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`type_serf`='$type_serf',`date`='".time()."',`wmid`='$wmid_user',`username`='$username',`geo_targ`='$country',`content`='$content',`active`='$active',`revisit`='$revisit',`color`='$color',`timer`='$timer',`nolimit`='0',`limit_d`='$limit_d',`limit_h`='$limit_h',`limit_d_now`='$limit_d',`limit_h_now`='$limit_h',`url`='$url',`title`='$title',`description`='$description',`plan`='$plan',`totals`='$plan',`ip`='$laip',`money`='0' WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
		}else{
		    $mysqli->query("INSERT INTO `tb_ads_youtube` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`type_serf`,`date`,`wmid`,`username`,`geo_targ`,`content`,`active`,`revisit`,`color`,`timer`,`nolimit`,`limit_d`,`limit_h`,`limit_d_now`,`limit_h_now`,`url`,`title`,`description`,`plan`,`totals`,`ip`,`money`, `ytflag`, `img_youtube`) 
            VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$type_serf','".time()."','$wmid_user','$username','$country','$content','$active','$revisit','$color','$timer','$nolimitdate','$limit_d','$limit_h','$limit_d','$limit_h','$url','$title','$description','$plan','$plan','$laip','0','$ytflag', '$img_youtube')") or die($mysqli->error);
			//$mysqli->query("INSERT INTO `tb_ads_youtube` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`type_serf`,`date`,`wmid`,`username`,`geo_targ`,`content`,`active`,`revisit`,`color`,`timer`,`nolimit`,`limit_d`,`limit_h`,`limit_d_now`,`limit_h_now`,`url`,`title`,`description`,`plan`,`totals`,`ip`,`money, `ytflag`, `img_youtube`) 
			//VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$type_serf','".time()."','$wmid_user','$username','$country','$content','$active','$revisit','$color','$timer','$nolimitdate','$limit_d','$limit_h','$limit_d','$limit_h','$url','$title','$description','$plan','$plan','$laip','$ytflag','$img_youtube')") or die($mysqli->error);
		}
		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_youtube` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;
	
		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Параметры заказа!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="200"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>Заголовок видеоролика:</b></td><td><a href="'.$url.'" target="_blank">'.$title.'</a></td></tr>';
			//echo '<tr><td><b>Краткое описание ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$description.'</a></td></tr>';
			echo '<tr><td><b>URL сайта:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Количество визитов:</b></td><td>'.$plan.'</td></tr>';
			echo '<tr><td><b>Таймер, сек.:</b></td><td>'.$timer_to.'</td></tr>';
			echo '<tr><td><b>Выделение цветом:</b></td><td>'.$color_to[$color].'</td></tr>';
			echo '<tr><td><b>Активное окно:</b></td><td>'.$active_to[$active].'</td></tr>';
			echo '<tr><td><b>Доступно для просмотра:</b></td><td>'.$revisit_to[$revisit].'</td></tr>';
			echo '<tr><td><b>Контент "18+":</b></td><td>'.$content_to[$content].'</td></tr>';
			echo '<tr><td><b>Геотаргетинг:</b></td><td>Все страны</td></tr>';
		echo '</table>';

		echo '<table class="tables"><tr align="center"><td>';
			echo '<form action="" method="post">';
				echo '<input type="hidden" name="id_pay" value="'.$id_zakaz.'">';
				echo '<input type="hidden" name="method_pay" value="'.$method_pay.'">';
				echo '<input type="submit" value="Разместить ссылку" class="sub-blue160" style="float:none;">';
			echo '</form>';
		echo '</td></tr></table>';
	}
}else{
	?>

	<script type="text/javascript" language="JavaScript"> 

	function gebi(id){
		return document.getElementById(id)
	}

	function SbmFormB() {
		arrayElem = document.forms["formzakaz"];
		var col=0;

		for (var i=0;i<arrayElem.length;i++){
			if ((document.forms["formzakaz"].url.value == '')|(document.forms["formzakaz"].url.value == 'http://')|(document.forms["formzakaz"].url.value == 'https://')) {
				alert('Вы не указали URL-адрес сайта');
				arrayElem[i+0].style.background = "#FFDBDB";
				arrayElem[i+0].focus();
				return false;
			}else{
				arrayElem[i+0].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].title.value == '')) {
				alert('Вы не указали заголовок ссылки');
				arrayElem[i+1].style.background = "#FFDBDB";
				arrayElem[i+1].focus();
				return false;
			}else{
				arrayElem[i+1].style.background = "#FFFFFF";
			}
			/*if ((document.forms["formzakaz"].description.value == '')) {
				alert('Вы не указали краткое описание ссылки');
				arrayElem[i+2].style.background = "#FFDBDB";
				arrayElem[i+2].focus();
				return false;*/
			}else{
				arrayElem[i+2].style.background = "#FFFFFF";
			}
		}

		document.forms["formzakaz"].submit();
		return true;
	}

	</script>

	<?php

	echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:15px;">';
		echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">YouTube серфинг ТЕСТ-ДРАВ - что это?</span>';
		echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Это <span style="color:#FF0000;">Test-Drive YouTube</span> - '.$plan.' просмотров вашего видеоролика бесплатно<br>';
		echo 'Ваша видеоролик будет размещён на странице просмотра видеороликов автоматически, и будет снята только после того количества показов, которое было заказано Вами (при условии соблюдения правил*).<br>';
		echo 'Функция <span style="color:#FF0000;">Test-Drive YouTube</span> доступна зарегистрированным пользователям только 1 раз!!!<br>';
	echo '</div>';
	echo '<br><br>';

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';
		echo '<thead><th colspan="2" class="top">Форма размещения тестовой рекламы</th></thead>';
		echo '<tbody>';
		echo '<tr>';
			echo '<td width="180" align="left"><b>URL сайта (ссылка):</b></td>';
			echo '<td align="left"><input type="text" name="url" maxlength="160" value="https://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Заголовок видеоролика:</b></td>';
			echo '<td align="left"><input type="text" name="title" maxlength="60" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		/*echo '<tr>';
			echo '<td align="left"><b>Краткое описание ссылки:</b></td>';
			echo '<td align="left"><input type="text" name="description" maxlength="60" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';*/
		echo '<tr>';
			echo '<td align="left"><b>Количество просмотров:</b></td>';
			echo '<td align="left"><b>'.$plan.'</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Время просмотра:</b></td>';
			echo '<td align="left"><b>'.$timer.'</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Выделение цветом:</b></td>';
			echo '<td align="left"><b>Нет</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Активное окно:</b></td>';
			echo '<td align="left"><b>Нет</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Доступно для просмотра:</b></td>';
			echo '<td align="left"><b>Каждые 24 часа</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Контент 18+:</b></td>';
			echo '<td align="left"><input type="checkbox" name="content" value="1"> - на моем сайте присутствуют материалы для взрослых</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td colspan="2" align="center"><input type="submit" value="Оформить заказ" class="sub-blue160" style="float:none;" /></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</form>';
}

?>

<script language="JavaScript"> obsch(); </script>