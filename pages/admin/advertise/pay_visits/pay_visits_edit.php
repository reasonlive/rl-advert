<?php
if(!DEFINED("PAY_VISITS_EDIT")) exit("Access denied!");

function SqlConfig($item, $howmany=1, $decimals=false){
	global $mysqli;

	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='$item' AND `howmany`='$howmany'") or die($mysqli->error);
	$price = $sql->num_rows > 0 ? $sql->fetch_object()->price : die("Error: item['$item'] or howmany['$howmany'] not found in table `tb_config`");
	$sql->free();

	return ($decimals!==false && is_numeric($price)) ? round($price, $decimals) : $price;
}

$sql = $mysqli->query("SELECT * FROM `tb_ads_pay_vis` USE INDEX (`username_id`) WHERE `id`='$id'") or die($mysqli->error);
if($sql->num_rows > 0) {
	$row = $sql->fetch_assoc();
	$id = $row["id"];
	$status = $row["status"];
	$sql->free();

	$geo_targ = (isset($row["geo_targ"]) && trim($row["geo_targ"])!=false) ? explode(", ", $row["geo_targ"]) : array();

	if($status==1) {
		echo '<span class="msg-error">Перед редактированием, необходимо поставить площадку на паузу!</span>';

		$mysqli->close();
		exit();
	}

}else{
	echo '<span class="msg-error">Рекламная площадка ID:<b>'.$id.'</b> не найдена!</span>';

	$sql->free();
	$mysqli->close();
	exit();
}

$pvis_cena_hit = SqlConfig('pvis_cena_hit', 1, 4);
$pvis_cena_hideref = SqlConfig('pvis_cena_hideref', 1, 4);
$pvis_cena_color = SqlConfig('pvis_cena_color', 1, 4);
$pvis_cena_revisit[1] = SqlConfig('pvis_cena_revisit', 1, 4);
$pvis_cena_revisit[2] = SqlConfig('pvis_cena_revisit', 2, 4);
$pvis_cena_uniq_ip[1] = SqlConfig('pvis_cena_uniq_ip', 1, 4);
$pvis_cena_uniq_ip[2] = SqlConfig('pvis_cena_uniq_ip', 2, 4);

$token_adv_edit = strtolower(md5($id.strtolower($user_name).$_SERVER["HTTP_HOST"]."token-adv-edit".$security_key));

$reit_user_arr = array();
$sql_s = $mysqli->query("SELECT `id`,`rang`,`r_ot`,`cnt_users` FROM `tb_config_rang` WHERE `id`>'1' ORDER BY `id` ASC") or die($mysqli->error);
if($sql_s->num_rows > 0) {
	$reit_user_arr[0] = "Все пользователи проекта";
	while ($row_s = $sql_s->fetch_assoc()) {
		$reit_user_arr[$row_s["id"]] = "С рейтингом ".number_format($row_s["r_ot"], 0, ".", " ")." и более баллов (".$row_s["rang"]." ~ ".number_format($row_s["cnt_users"], 0, ".", " ")." чел.)";
	}
	$sql_s->free();
}else{
	$reit_user_arr[0] = "Все пользователи проекта";
	$sql_s->free();
}
?>

<script>
$(document).ready(function(){
	var hint_ids, hint_txt = [];
	hint_txt[1] = '<b>Заголовок ссылки</b> - максимум <b>60</b> символов.<br>Заголовок должен быть кратким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После точки или запятой правильно ставить знак пробела.';
	hint_txt[2] = '<b>Описание ссылки</b> - максимум <b>80</b> символов.<br>Размещается под заголовком. Описание должно быть кратким и понятным. Соблюдайте грамматику. Небрежное написание оттолкнёт посетителей. Не пишите всё ЗАГЛАВНЫМИ БУКВАМИ, не ставьте множество однотипных знаков типа: !!!!!! и т.д. После точки или запятой правильно ставить знак пробела.';
	hint_txt[3] = '<b>URL-адрес сайта</b> должен начинаться с http:// или https:// и содержать не более 300 символов. Не используйте HTML-теги и Java-скрипты.';
	hint_txt[4] = '<b>Скрыть HTTP_REFERER</b> - Вы можете скрыть адрес веб-сайта с которого пришел посетитель. Некоторые браузеры, ранних версий, могут не поддерживать данную опцию.';
	hint_txt[5] = '<b>Выделить цветом</b> - Ваша ссылка будет <b class="text-red">выделена красным цветом</b>, что сделает её заметнее по сравнению с другими ссылками.';
	hint_txt[6] = '<b>Доступно для просмотра</b> - выберите как пользователи будут просматривать вашу рекламу. Стандартная настройка "Каждые 24 часа" означает, что один и тот же пользователь может просматривать вашу рекламу каждые 24 часа, подобный принцип у дополнительной настройки "Каждые 48 часов" и "1 раз в месяц".';
	hint_txt[7] = '<b>Уникальный IP</b> - Вы можете ограничить показ вашей рекламы при полном совпадении у пользователей IP или маски подсети. При включении данной опции вашу рекламу с одного IP или одной маски подсети смогут просмотреть только один раз.';
	hint_txt[8] = '<b>По дате регистрации</b> - Вы можете установить ограничение просмотра ссылки по дате регистрации пользователя на нашем проекте.';
	hint_txt[9] = '<b>По рейтингу пользователя</b> - Вы можете установить ограничение просмотра ссылки по рейтингу пользователя на нашем проекте.';
	hint_txt[10] = '<b>По наличию реферера</b> - Вы можете ограничить просмотры ссылки только пользователями не имеющими реферера на нашем проекте.';
	hint_txt[11] = '<b>По половому признаку</b> - Вы можете ограничить показ своей рекламы по половому признаку пользователей, разрешив показ своей рекламы только пользователям мужского либо женского пола.';
	hint_txt[12] = '<b>Показывать только рефералам</b> - Вы можете ограничить показ своей рекламы, разрешив показывать ее только своим рефералам.';
	hint_txt[13] = '<b>Контент <img src="/img/18+.png" alt="18+" width="16" height="16" align="absmiddle" style="margin:0 0 3px; padding:0;"></b> &mdash; если на вашем сайте присутствуют материалы для взрослых, обязательно установите галочку, в противном случае ваша реклама может быть удалена из показа, а деньги, потраченные на ее размещение, возвращены не будут. Это не касается порнографии, так как сайты, содержащие порнографию вообще запрещены для рекламирования на нашем сервисе. Также рекомендуем ознакомиться с <b>пунктом правил 3.5.3</b>';
	hint_txt[14] = '<b>Геотаргетинг</b> - это возможность ограничить посещения вашего сайта по территориальному признаку. Ссылка будет доступна для просмотра только пользователям из тех стран, которые вы отметите. По умолчанию переходы по ссылке разрешены пользователям из любых стран.';
	hint_txt[17] = '<b>Цена одного посещения</b> - стоимость за одно посещение сайта.';
	hint_ids = Object.keys(hint_txt);

	for (var i = 0; i < hint_ids.length; i++) {
		$("#hint-"+hint_ids[i]).simpletip({fixed: true, position: ["-609", "24"], focus: false, content: hint_txt[hint_ids[i]]});
	}

	$("html, body").animate({scrollTop: $(".scroll-to").offset().top-35}, 700);
})

function PlanChange(){
	var rprice = <?=$pvis_cena_hit;?>;
	var hide_httpref = $.trim($("#hide_httpref").val());
	var color = $.trim($("#color").val());
	var revisit = $.trim($("#revisit").val());
	var uniq_ip = $.trim($("#uniq_ip").val());

	if(hide_httpref == 1) rprice += <?=$pvis_cena_hideref;?>;
	if(color == 1) rprice += <?=$pvis_cena_color;?>;

	if(revisit == 1) rprice += <?=$pvis_cena_revisit[1];?>;
	else if (revisit == 2) rprice += <?=$pvis_cena_revisit[2];?>;

	if(uniq_ip == 1) rprice += <?=$pvis_cena_uniq_ip[1];?>;
	else if(uniq_ip == 2) rprice += <?=$pvis_cena_uniq_ip[2];?>;

	$("#price_one").html('<span class="text-green"><b>'+number_format_js(rprice, 4, ".", " ")+'</b> руб.</span>');
}

function CtrlEnter(event) {
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD))) {
		$("#subAdv").click();
	}
}
</script>

<?php
echo '<div class="scroll-to"></div>';

echo '<div id="newform" class="form-edit-adv ws.r" onKeyPress="CtrlEnter(event);"><form id="form-edit-'.$ads.'-'.$id.'" action="" method="POST" onSubmit="FuncAdv('.$id.', \'adv-edit\', \''.$ads.'\', $(this).attr(\'id\'), \''.$token_adv_edit.'\', \'modal\'); return false;">';
	echo '<table class="tables" style="margin:0 auto;">';
	echo '<thead><tr><th>Параметр</th><th colspan="2">Значение</th></tr></thead>';
	echo '<tbody>';
		echo '<tr>';
			echo '<td width="240"><b>Заголовок ссылки</b></td>';
			echo '<td><input type="text" id="title" name="title" maxlength="60" value="'.$row["title"].'" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-1" class="hint-quest"></span></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Краткое описание ссылки</b></td>';
			echo '<td><input type="text" id="description" name="description" maxlength="80" value="'.$row["description"].'" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-2" class="hint-quest"></span></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>URL сайта</b> (включая http:// или https://)</td>';
			echo '<td align="center"><input type="url" id="url" name="url" maxlength="300" value="'.$row["url"].'" class="ok" required="required"></td>';
			echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-3" class="hint-quest"></span></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';

	echo '<div id="adv-title-dopset" class="adv-title-open" onClick="SHBlock(\'dopset\');">Настройки</div>';
	echo '<div id="adv-block-dopset" style="display:block;">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td width="240"><b>Скрыть HTTP_REFERER</b></td>';
				echo '<td>';
					echo '<select id="hide_httpref" name="hide_httpref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["hide_httpref"]==0 ? 'selected="selected"' : false).'>Нет</option>';
						echo '<option value="1" '.($row["hide_httpref"]==1 ? 'selected="selected"' : false).'>Да '.($pvis_cena_hideref>0 ? "(+$pvis_cena_hideref руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-4" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Выделить цветом</td>';
				echo '<td>';
					echo '<select id="color" name="color" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["color"]==0 ? 'selected="selected"' : false).'>Нет</option>';
						echo '<option value="1" '.($row["color"]==1 ? 'selected="selected"' : false).'>Да '.($pvis_cena_color>0 ? "(+$pvis_cena_color руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-5" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><b>Доступно для просмотра</b></td>';
				echo '<td>';
					echo '<select id="revisit" name="revisit" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["revisit"]==0 ? 'selected="selected"' : false).'>Каждые 24 часа</option>';
						echo '<option value="1" '.($row["revisit"]==1 ? 'selected="selected"' : false).'>Каждые 48 часов '.($pvis_cena_revisit[1]>0 ? "(+$pvis_cena_revisit[1] руб./посещение)" : false).'</option>';
						echo '<option value="2" '.($row["revisit"]==2 ? 'selected="selected"' : false).'>1 раз в месяц '.($pvis_cena_revisit[2]>0 ? "(+$pvis_cena_revisit[2] руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-6" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Уникальный IP</td>';
				echo '<td>';
					echo '<select id="uniq_ip" name="uniq_ip" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["uniq_ip"]==0 ? 'selected="selected"' : false).'>Нет</option>';
						echo '<option value="1" '.($row["uniq_ip"]==1 ? 'selected="selected"' : false).'>Да (100% совпадение) '.($pvis_cena_uniq_ip[1]>0 ? "&mdash; (+$pvis_cena_uniq_ip[1] руб./посещение)" : false).'</option>';
						echo '<option value="2" '.($row["uniq_ip"]==2 ? 'selected="selected"' : false).'>Усиленный по маске до 2 чисел (255.255.X.X) '.($pvis_cena_uniq_ip[2]>0 ? "&mdash; (+$pvis_cena_uniq_ip[2] руб./посещение)" : false).'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-7" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По дате регистрации</td>';
				echo '<td>';
					echo '<select id="date_reg_user" name="date_reg_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["date_reg_user"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
						echo '<option value="3" '.($row["date_reg_user"]==3 ? 'selected="selected"' : false).'>3 дня с момента регистрации</option>';
						echo '<option value="7" '.($row["date_reg_user"]==7 ? 'selected="selected"' : false).'>7 дней с момента регистрации</option>';
						echo '<option value="30" '.($row["date_reg_user"]==30 ? 'selected="selected"' : false).'>1 месяц с момента регистрации</option>';
						echo '<option value="90" '.($row["date_reg_user"]==90 ? 'selected="selected"' : false).'>3 месяца с момента регистрации</option>';
						echo '<option value="180" '.($row["date_reg_user"]==180 ? 'selected="selected"' : false).'>6 месяцев с момента регистрации</option>';
						echo '<option value="365" '.($row["date_reg_user"]==365 ? 'selected="selected"' : false).'>1 год с момента регистрации</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-8" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По рейтингу пользователя</td>';
				echo '<td>';
					echo '<select id="reit_user" name="reit_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						foreach($reit_user_arr as $key => $val) {
							echo '<option value="'.$key.'" '.($row["reit_user"]==$key ? 'selected="selected"' : false).'>'.$val.'</option>';
						}
					echo '</select>';
				echo '</td>';
				echo '<td align="center" width="16"><span id="hint-9" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По наличию реферера</td>';
				echo '<td>';
					echo '<select id="no_ref" name="no_ref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["no_ref"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
						echo '<option value="1" '.($row["no_ref"]==1 ? 'selected="selected"' : false).'>Пользователям без реферера на '.$_SERVER["HTTP_HOST"].'</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-10" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>По половому признаку</td>';
				echo '<td>';
					echo '<select id="sex_user" name="sex_user" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["sex_user"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
						echo '<option value="1" '.($row["sex_user"]==1 ? 'selected="selected"' : false).'>Только мужчины</option>';
						echo '<option value="2" '.($row["sex_user"]==2 ? 'selected="selected"' : false).'>Только женщины</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-11" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Показывать только рефералам</td>';
				echo '<td>';
					echo '<select id="to_ref" name="to_ref" class="ok" onChange="PlanChange();" onClick="PlanChange();">';
						echo '<option value="0" '.($row["to_ref"]==0 ? 'selected="selected"' : false).'>Все пользователи проекта</option>';
						echo '<option value="1" '.($row["to_ref"]==1 ? 'selected="selected"' : false).'>Рефералам 1-го уровня</option>';
						echo '<option value="2" '.($row["to_ref"]==2 ? 'selected="selected"' : false).'>Рефералам всех уровней</option>';
					echo '</select>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-12" class="hint-quest"></span></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Контент 18+</td>';
				echo '<td>';
					echo '<div style="float:left;"><input type="checkbox" id="content" name="content" value="1" '.($row["content"]==1 ? 'checked="checked"' : false).' style="height:16px; width:16px; margin:0px;"></div>';
					echo '<div style="float:left; padding:1px 5px 0;">- на моем сайте присутствуют материалы для взрослых</div>';
				echo '</td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-13" class="hint-quest"></span></td>';
			echo '</tr>';
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<div id="adv-title-geotarg" class="adv-title-'.(count($geo_targ)>0 ? "open" : "close").'" onClick="SHBlock(\'geotarg\');">Геотаргетинг</div>';
	echo '<div id="adv-block-geotarg" style="'.(count($geo_targ)>0 ? false : "display:none;").'">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td colspan="2" align="center"><a onclick="SetChecked(\'country[]\', \'paste\');" style="width:100%; color:#008B00; font-weight:bold; cursor:pointer;"><center>Отметить все</center></a></td>';
				echo '<td colspan="2" align="center"><a onclick="SetChecked(\'country[]\', \'unpaste\');" style="width:100%; color:#FF0000; font-weight:bold; cursor:pointer;"><center>Снять все</center></a></td>';
				echo '<td rowspan="10" style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-14" class="hint-quest"></span></td>';
			echo '</tr>';
			include(ROOT_DIR."/advertise/func_geotarg_edit.php");
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<div id="adv-title-infopay" class="adv-title-open" onclick="SHBlock(\'infopay\');">Информация</div>';
	echo '<div id="adv-block-infopay" style="display:block;">';
		echo '<table class="tables" style="margin:0 auto;">';
		echo '<tbody>';
			echo '<tr>';
				echo '<td width="240" style="height:22px;">Цена одного посещения</td>';
				echo '<td id="price_one"></td>';
				echo '<td style="width:16px; text-align:center; background: #EDEDED;"><span id="hint-17" class="hint-quest"></span></td>';
			echo '</tr>';
		echo '</tbody>';
		echo '</table>';
	echo '</div>';

	echo '<table class="tables" style="margin:0 auto;">';
	echo '<tbody>';
		echo '<tr>';
			echo '<td align="center"><input id="subAdv" type="submit" value="Cохранить изменения" class="sd_sub big green"></td>';
		echo '</tr>';
	echo '</tbody>';
	echo '</table>';

echo '</form></div>';

?>
<script language="JavaScript">PlanChange();</script>