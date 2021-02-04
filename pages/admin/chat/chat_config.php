<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки ЧАТа</b></h3>';

$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
$token_string = strtolower(md5($user_id.strtolower($user_name).$_SERVER["HTTP_HOST"]."chat-config".$security_key));

require(ROOT_DIR."/config.php");

function SqlConfig($item, $decimals=false){
	global $mysqli;

	$sql = $mysqli->query("SELECT `price` FROM `tb_chat_conf` WHERE `item`='$item'") or die($mysqli->error);
	$price = $sql->num_rows > 0 ? $sql->fetch_object()->price : die("Error: item['$item'] not found in table `tb_chat_conf`");
	$sql->free();

	return ($decimals!==false && is_numeric($price)) ? my_num_format($price, $decimals, ".", "", 2) : $price;
}

$chat_access_reit = SqlConfig('chat_access_reit', 0);
$cena_color_login = SqlConfig('cena_color_login', 2);
$cena_adv = SqlConfig('cena_adv', 2);
$cena_adv_color = SqlConfig('cena_adv_color', 2);

$mysqli->close();
?>

<script>
var status_form = false;

function isValidIn(){
	var cena_color_login = $.trim($("#cena_color_login").val());
	var cena_adv = $.trim($("#cena_adv").val());
	var cena_adv_color = $.trim($("#cena_adv_color").val());

	cena_color_login = cena_color_login.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	cena_color_login = cena_color_login[0] || ''; $("#cena_color_login").val(cena_color_login);

	cena_adv = cena_adv.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	cena_adv = cena_adv[0] || ''; $("#cena_adv").val(cena_adv);

	cena_adv_color = cena_adv_color.replace(/\,/g, ".").match(/(\d+(\.)?(\d){0,2})?/);
	cena_adv_color = cena_adv_color[0] || ''; $("#cena_adv_color").val(cena_adv_color);

	return false
}

function FuncChat(id, op, form_id, token, modal, title_win, width_win) {
	if(!status_form) {
		var datas = {}; datas["id"] = id || 0; datas["op"] = op || ''; datas["token"] = token || '';
		if(form_id) { var data_form = $("#"+form_id).serializeArray(); $.each(data_form, function(i, field) { datas[field.name] = field.value; }); }
		$.ajax({
			type:"POST", cache:false, url:"ajax/ajax_chat.php", dataType:'json', data:datas, 
			error: function(request, status, errortext) {
				status_form = false;
				ModalStart("Ошибка Ajax!", StatusMsg("ERROR", errortext+"<br>"+(request.status!=404 ? request.responseText : 'url ajax not found')), 500, true, false, false);
			}, 
			beforeSend: function() { status_form = true; $("input, textarea, select").blur(); }, 
			success: function(data) {
				status_form = false;
				var result = data.result || data;
				var message = data.message || data;
				width_win = width_win || 500;

				if(result=="OK") {
					title_win = title_win || "Информация";
				} else {
					title_win = title_win || "Ошибка";
					width_win = width_win>=500 ? 500 : width_win;
					message = StatusMsg(result, message);
				}

				if($("div").is(".box-modal") && message) {
					$(".box-modal-title").html(title_win);
					$(".box-modal-content").html(message);
					if(width_win) $(".box-modal").css("width", width_win);
				} else if(message) {
					ModalStart(title_win, message, width_win, true, false);
					if(width_win) $(".box-modal").css("width", width_win);
				}
			}
		});
	}
	return false;
}

function CtrlEnter(event) {
	e = event || window.event;
	if((e.ctrlKey) && (e.keyCode == 10 | e.keyCode == 13)) {
		$("#SubMit").click();
	}
	return false;
}
</script>

<?php
echo '<div id="newform" class="form-chat-config" onKeyPress="CtrlEnter(event);"><form id="form-chat-config" method="POST" onSubmit="FuncChat(0, \'chat-config\', $(this).attr(\'id\'), \''.$token_string.'\', \'modal\'); return false;">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
	echo '<thead><tr align="center"><th>Параметр</th><th width="120">Значение</th></tr></thead>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальный рейтинг для общения в ЧАТе</b></td>';
		echo '<td align="center"><input type="number" id="chat_access_reit" name="chat_access_reit" value="'.$chat_access_reit.'" min="0" max="1000" step="10" class="ok12" style="text-align:center; padding:1px 5px;" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость цвета логина</b></td>';
		echo '<td align="center"><input type="text" id="cena_color_login" name="cena_color_login" value="'.$cena_color_login.'" class="ok12" style="text-align:center; padding:1px 5px;" onKeyDowm="isValidIn();" onKeyUp="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость размещения рекламы</b></td>';
		echo '<td align="center"><input type="text" id="cena_adv" name="cena_adv" value="'.$cena_adv.'" class="ok12" style="text-align:center; padding:1px 5px;" onKeyDowm="isValidIn();" onKeyUp="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Стоимость выделения ссылки</b></td>';
		echo '<td align="center"><input type="text" id="cena_adv_color" name="cena_adv_color" value="'.$cena_adv_color.'" class="ok12" style="text-align:center; padding:1px 5px;" onKeyDowm="isValidIn();" onKeyUp="isValidIn();" required="required" autocomplete="off"></td>';
	echo '</tr>';

	echo '<tr align="center"><td colspan="3"><input id="SubMit" type="submit" value="Сохранить" class="sd_sub big green"></tr>';
echo '</table>';
echo '</form></div>';

?>

<script language="JavaScript">isValidIn();</script>