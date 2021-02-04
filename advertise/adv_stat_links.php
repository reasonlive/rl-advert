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

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink' AND `howmany`='1'");
$cena_slink = $sql->fetch_object()->price;
$cena_slink = number_format($cena_slink,2,".","");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink_color' AND `howmany`='1'");
$cena_slink_color = $sql->fetch_object()->price;
$cena_slink_color = number_format($cena_slink_color,2,".","");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_slink_min' AND `howmany`='1'");
$cena_slink_min = $sql->fetch_object()->price;
$cena_slink_min = number_format($cena_slink_min,0,".","`");

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
	
	### Скидка рефералам ###
	if(isset($_SESSION["userLog"]) && isset($_SESSION["userPas"]) && isset($my_referer)) {
		if($my_referer!=false) {
			$sql_p = $mysqli->query("SELECT `p_sl`,`discount_partner` FROM `tb_users_partner` WHERE `username`='$my_referer' AND `discount_partner`>'0'");
			if($sql_p->num_rows>0) {
				$row_p = $sql_p->fetch_array();
				$p_sl_ref = $row_p["p_sl"];
				$discount_partner = $row_p["discount_partner"];
				$my_discount = p_floor(($p_sl_ref * $discount_partner)/100, 2);
				$my_discount = number_format($my_discount,2,".","");
			}else{
				$my_discount = 0;
			}
		}else{
			$my_discount = 0;
		}
	}else{
		$my_discount = 0;
	}
	### Скидка рефералам ###

}else{
	$username = false;
	$wmid_user = false;
	$wmr_user = false;
	$money_user = false;
}

if(count($_POST)>0 && isset($_POST["id_pay"])) {
	if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
		echo '<span class="msg-error">Для оплаты с рекламного счета необходимо авторизоваться!</span>';
		include('footer.php');
		exit();
	}else{
		$id_pay = ( isset($_POST["id_pay"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["id_pay"])) ) ? intval(limpiarez(trim($_POST["id_pay"]))) : false;

		$sql_id = $mysqli->query("SELECT * FROM `tb_ads_slink` WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1");
		if($sql_id->num_rows>0) {
			$row = $sql_id->fetch_array();
			$plan = $row["plan"];
			$money_pay = $row["money"];
			$start_cena = $row["start_cena"];

			if($money_user>=$money_pay) {
				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'");
				$reit_rek = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'");
				$reit_ref_rek = $sql->fetch_object()->price;

				$reit_add_1 = floor($money_pay/10) * $reit_rek;
				$reit_add_2 = floor($money_pay/10) * $reit_ref_rek;

				if($my_referer!=false) {$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_2' WHERE `username`='$my_referer'") or die($mysqli->error);}

				$mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'$reit_add_1', `money_rb`=`money_rb`-'$money_pay', `money_rek`=`money_rek`+'$money_pay' WHERE `username`='$username'") or die($mysqli->error);
				$mysqli->query("UPDATE `tb_ads_slink` SET `status`='1', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `wmid`='$wmid_user' WHERE `id`='$id_pay' AND `status`='0' AND `username`='$username' ORDER BY `id` DESC LIMIT 1") or die($mysqli->error);
				$mysqli->query("INSERT INTO `tb_history` (`user`,`date`,`amount`,`method`,`status`,`tipo`) VALUES('$username','".DATE("d.m.Yг. H:i")."','$money_pay',  'Оплата рекламы: статическая ссылка ($plan дн.)','Списано','rashod')") or die($mysqli->error);

				stat_pay('statlink', $money_pay);
				ads_wmid($wmid_user, $wmr_user, $username, $money_pay);
				konkurs_ads_new($wmid_user, $username, $money_pay);
				invest_stat($money_pay, 4);

				require_once("".DOC_ROOT."/merchant/func_cache.php");
				cache_stat_links();
				
				PartnerSet($username, "p_sl", $start_cena, $plan, $type_banner=false);

				$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='partner_max_percent' AND `howmany`='1'");
				$partner_max_percent = $sql->fetch_object()->price;

				$sql = $mysqli->query("SELECT `price`,`howmany` FROM `tb_config` WHERE `item`='partner_count_day'");
				if($sql->num_rows>0) {
					$row_pd = $sql->fetch_array();
					$partner_count_day = $row_pd["howmany"];
					$partner_count_per = $row_pd["price"];
				}else{
					$partner_count_day = 1;
					$partner_count_per = 1;
				}

				$add_per = floor($plan/$partner_count_day * $partner_count_per);
				$add_percent_user = floor($p_sl + $add_per);
				if($add_percent_user>$partner_max_percent) $add_percent_user = $partner_max_percent;
				$mysqli->query("UPDATE `tb_users` SET `p_sl`='$add_percent_user' WHERE `username`='$username'") or die($mysqli->error);

				if($my_referer!=false) {
					$sql_up = $mysqli->query("SELECT `p_sl` FROM `tb_users` WHERE `username`='$my_referer' AND `p_sl`>'0'");
					if($sql_up->num_rows>0) {
						$row_up = $sql_up->fetch_row();
						$ref_p_sl = $row_up["0"];

						$money_add_referer = floatval($money_pay / 100 * $ref_p_sl);
						$mysqli->query("UPDATE `tb_users` SET `money`=`money`+'$money_add_referer' WHERE `username`='$my_referer'") or die($mysqli->error);

						$mysqli->query("INSERT INTO `tb_partner` (`time`, `username`, `referer`, `type`, `money`, `percent`) VALUES('".time()."','$username','$my_referer','p_sl','$money_add_referer', '$ref_p_sl')") or die("Error".$mysqli->error);
					}
				}


				echo '<span class="msg-ok">Ваша ссылка успешно размещена!<br>Спасибо, что пользуетесь услугами нашего сервиса!</span>';
				include('footer.php');
				exit();
			}else{
				echo '<span class="msg-error">На вашем рекламном счете недостаточно средств для оплаты заказа!</span>';
				include('footer.php');
				exit();
			}
		}else{
			echo '<span class="msg-error">Заказа рекламы с №'.$id_pay.' не существует, либо заказ уже был оплачен!</span>';
			include('footer.php');
			exit();
		}
	}
}

if(count($_POST)>0) {
	$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]),80) : false;
	$url = (isset($_POST["url"])) ? limpiarez($_POST["url"]) : false;
	$plan = ( isset($_POST["plan"]) && preg_match("|^[\d]{1,11}$|", trim($_POST["plan"])) ) ? intval(limpiarez(trim($_POST["plan"]))) : false;
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
		echo '<span class="msg-error">Не заполнено поле Описание ссылки.</span><br>';
	}elseif($plan<$cena_slink_min) {
		echo '<span class="msg-error">Минимальный заказ - '.$cena_slink_min.' дней.</span><br>';
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
        
		$color_to[0]="Нет";
		$color_to[1]="Да (".number_format(($cena_slink_color * $plan),2,".","`")." руб.)";

		$summa = round(($plan * ($cena_slink + $cena_slink_color * $color)),2);
		/* $summa = number_format(($summa * (100-$cab_skidka)/100),2,".",""); */

		$mysqli->query("DELETE FROM `tb_ads_slink` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die($mysqli->error);

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'");
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;
		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die($mysqli->error);

		$check_wmid = $mysqli->query("SELECT `id` FROM `tb_ads_slink` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		if($check_wmid->num_rows>0) {
			$mysqli->query("UPDATE `tb_ads_slink` SET `merch_tran_id`='$merch_tran_id', `method_pay`='$method_pay', `wmid`='$wmid_user', `username`='$username', `date`='".time()."', `date_end`='".(time()+$plan*24*60*60)."', `plan`='$plan', `url`='$url', `description`='$description', `color`='$color', `ip`='$laip', `money`='$summa' WHERE `status`='0' AND `session_ident`='".session_id()."'") or die($mysqli->error);
		}else{
			$mysqli->query("INSERT INTO `tb_ads_slink` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_end`,`plan`,`url`,`description`,`color`,`ip`,`money`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$wmid_user','$username','".time()."','".(time()+$plan*24*60*60)."','$plan','$url','$description','$color','$laip','$summa')") or die($mysqli->error);
		}

		$sql_id = $mysqli->query("SELECT `id` FROM `tb_ads_slink` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1");
		$id_zakaz = $sql_id->fetch_object()->id;
	
		echo '<br><span class="msg-ok" style="margin-bottom:0px;">Ваш заказ принят и будет выполнен автоматически после оплаты!</span>';
		echo '<table class="tables">';
			echo '<tr><td width="220"><b>Счет №:</b></td><td>'.$merch_tran_id.'</td></tr>';
			echo '<tr><td><b>URL ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$url.'</a></td></tr>';
			echo '<tr><td><b>Текст ссылки:</b></td><td><a href="'.$url.'" target="_blank">'.$description.'</a></td></tr>';
			echo '<tr><td><b>Количество дней:</b></td><td>'.$plan.' ('.number_format(($plan * $cena_slink),2,".","'").' руб.)</td></tr>';
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

		$shp_item = "6";
		$inv_desc = "Оплата рекламы: статическая ссылка, план:$plan, счет:$merch_tran_id";
		$inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
		$inv_desc_en = "Pay advertise: stat link, plan:$plan, order:$merch_tran_id";
		$money_add = number_format($summa,2,".","");
		require_once("".DOC_ROOT."/method_pay/method_pay.php");

		include('footer.php');
		exit();
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
			if ((document.forms["formzakaz"].description.value == '')) {
				alert('Вы не указали Описание ссылки');
				arrayElem[i+1].style.background = "#FFDBDB";
				arrayElem[i+1].focus();
				return false;
			}else{
				arrayElem[i+1].style.background = "#FFFFFF";
			}
			if ((document.forms["formzakaz"].plan.value == '') | (document.forms["formzakaz"].plan.value < <?=@$cena_slink_min;?>) ) {
				alert('Мнимальное количество дней <?=@$cena_slink_min;?>');
				arrayElem[i+2].style.background = "#FFDBDB";
				arrayElem[i+2].focus();
				return false;
			}else{
				arrayElem[i+2].style.background = "#FFFFFF";
			}
		}

		document.forms["formzakaz"].submit();
		return true;
	}

	function _obsch(){
		var plan = gebi('plan').value;
		var color = gebi('color').value;
		var cena_p = <?php echo $cena_slink;?>;
		var cena_color = <?php echo $cena_slink_color;?>;
		var price = (color * cena_color + cena_p) * plan;
		gebi('pricet').innerHTML = '<b>Стоимость заказа:</b>';
		gebi('prices').innerHTML = '<b style="color:#228B22;">' + price.toFixed(2) + ' руб.</b>';
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
	</script>

	<?php

	echo '<div id="InfoAds" style="display:block; align:justify; margin-bottom:15px;">';
		echo '<span id="adv-title-info" class="adv-title-open" onclick="ShowHideBlock(\'-info\');" style="border-top:solid 1px #46A2FF; text-align:left; padding-left:50px; margin-bottom:2px;">Статические ссылки</span>';
		echo '<div id="adv-block-info" style="display:block; padding:5px 7px 10px 7px; text-align:justify; background-color:#FFFFFF;">';
		echo 'Размещаются на определенное количество дней. При этом количество переходов по ним может быть неограниченное. Вы получаете именно заинтересованных посетителей на свой проект. ';
		echo 'Чем ярче вы сделаете описание, тем больше привлечете целевых посетителей на ваш сайт. ';
		echo 'Ссылка размещается на страницах - Просмотр сайтов и Платные письма.';
	echo '</div>';
	echo '<br><br>';

	echo '<form method="post" action="" name="formzakaz" onsubmit="return SbmFormB(); return false;" id="newform">';
	echo '<table class="tables">';
	echo '<thead><th colspan="2" class="top">Форма заказа рекламы</th></thead>';
	echo '<tbody>';
		echo '<tr>';
			echo '<td width="150"><b>URL сайта (ссылка):</b></td>';
			echo '<td><input type="text" name="url" maxlength="160" value="http://" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Описание ссылки:</b></td>';
			echo '<td><input type="text" name="description" maxlength="80" value="" class="ok" onkeydown="this.style.background=\'#FFFFFF\';"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Количество дней:</b></td>';
			echo '<td><input type="text" name="plan" id="plan" maxlength="7" value="1" class="ok12" style="text-align:center;" onChange="_obsch();" onKeyUp="_obsch();" onkeydown="this.style.background=\'#FFFFFF\';">&nbsp;&nbsp;(минимум '.@$cena_slink_min.')</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td align="left"><b>Выделить цветом:</b></td>';
			echo '<td>';
				echo '<select name="color" id="color" onChange="_obsch();" onClick="_obsch();">';
					echo '<option value="0">Нет</option>';
					echo '<option value="1">Да (+'.number_format($cena_slink_color,2,".","'").' руб./сутки)</option>';
				echo '</select>';
			echo '</td>';
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

?>

<script language="JavaScript">_obsch();</script>