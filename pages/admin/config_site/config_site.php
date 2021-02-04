<script type="text/javascript" src="js/jquery_min.js" ></script>
<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-ui-personalized-1.5.3.packed.js"></script>
<script type="text/javascript" src="js/ui.datepicker-ru.js"></script>
<script type="text/javascript">
	var $d = jQuery.noConflict();

	$d(document).ready(function() {
		$d.datepicker.setDefaults($d.datepicker.regional['ru']);

		$d("#startDate").datepicker({
		    yearRange: "<?php echo (DATE("Y")-1).":".(DATE("Y")+1);?>",
		    showOn: "both",
		    buttonImageOnly: true
		});
	});
</script>

<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки сайта</b></h3>';

if(count($_POST)>0) {

	$site_status = intval(trim($_POST["site_status"]));
	$site_status_txt = (trim($_POST["site_status_txt"]));

	$site_name=$_POST["sitename"];
	$site_wmid=$_POST["sitewmid"];
	$site_wmr=$_POST["sitewmr"];
	$site_email=$_POST["siteemail"];
	$site_isq=$_POST["siteisq"];
	$site_telefon=$_POST["sitetelefon"];
	$site_fio = trim($_POST["site_fio"]);
	$startdate = trim($_POST["startdate"]);
	$startdate = DATE("Y-m-d", strtotime($startdate));

	$del_users_status = (isset($_POST["del_users_status"]) && preg_match("|^[0-1]{1}$|", trim($_POST["del_users_status"])) ) ? intval($_POST["del_users_status"]) : 0;
	$del_ban_users_day = ( isset($_POST["del_ban_users_day"]) && preg_match("|^[\d]{1,3}$|", trim($_POST["del_ban_users_day"])) ) ? abs(intval(trim($_POST["del_ban_users_day"]))) : 180;
	$del_noact_users_day = ( isset($_POST["del_noact_users_day"]) && preg_match("|^[\d]{1,3}$|", trim($_POST["del_noact_users_day"])) ) ? abs(intval(trim($_POST["del_noact_users_day"]))) : 180;

	if($del_ban_users_day < 7) {
		echo '<span id="info-msg" class="msg-error">Для удаления заблокированных минимум 7 дней неактивности!</span>';

	}elseif($del_noact_users_day < 30) {
		echo '<span id="info-msg" class="msg-error">Для удаления неактивных минимум 30 дней неактивности!</span>';

	}else{
		$mysqli->query("UPDATE `tb_config` SET `price`='$del_users_status' WHERE `item`='del_users_status'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$del_ban_users_day' WHERE `item`='del_ban_users_day'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$del_noact_users_day' WHERE `item`='del_noact_users_day'") or die($mysqli->error);

		$mysqli->query("UPDATE `tb_site` SET 
			`site_status`='$site_status', `site_status_txt`='$site_status_txt', `sitename`='$site_name', `sitewmid`='$site_wmid', `sitewmr`='$site_wmr', 
			`siteemail`='$site_email', `siteisq`='$site_isq', `sitetelefon`='$site_telefon', `site_fio`='$site_fio', `startdate`='$startdate' 
		WHERE `id`='1'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}
        
	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 100);
		HideMsg("info-msg", 2500);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT * FROM `tb_site` WHERE `id`='1'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

}else{
	echo '<span class="msg-error">Нет данных!</span>';
}


$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='del_users_status'") or die($mysqli->error);
$del_users_status = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='del_ban_users_day'") or die($mysqli->error);
$del_ban_users_day = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='del_noact_users_day'") or die($mysqli->error);
$del_noact_users_day = $sql->fetch_object()->price;

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="margin:0px; padding:0px;">';
	echo '<tr align="center"><th width="300">Параметр</th><th width="350">Значение</th><th>Описание</th></tr>';
	echo '<tr><td align="left"><b>Имя сайта:</b></td><td><input type="text" name="sitename" value="'.$row["sitename"].'" class="ok"></td><td>&nbsp;&nbsp;</td></tr>';
	echo '<tr><td align="left"><b>Ф.И.О:</b></td><td><input type="text" name="site_fio" value="'.$row["site_fio"].'" class="ok"></td><td>Если заполнить то будет отображаться в контактах, при подключении сайта к автовыплатам или получения аттестата Продавца надо указать</td></tr>';
	echo '<tr><td align="left"><b>E-mail:</b></td><td><input type="text" name="siteemail" value="'.$row["siteemail"].'" class="ok"></td><td>Если заполнить то будет отображаться в контактах</td></tr>';
	echo '<tr><td align="left"><b>WMID:</b></td><td><input type="text" name="sitewmid" value="'.$row["sitewmid"].'" class="ok12"></td><td>&nbsp;&nbsp;</td></tr>';
	echo '<tr><td align="left"><b>WMR</b> (кошелек)<b>:</b></td><td><input type="text" name="sitewmr" value="'.$row["sitewmr"].'" class="ok12"><td>Кошелек для приема оплаты за рекламу и для выплат</td></tr>';
	echo '<tr><td align="left"><b>ICQ:</b></td><td><input type="text" name="siteisq" value="'.$row["siteisq"].'" class="ok12"></td><td>Если заполнить то будет отображаться в контактах</td></tr>';
	echo '<tr><td align="left"><b>Контактный телефон:</b></td><td><input type="text" name="sitetelefon" value="'.$row["sitetelefon"].'" class="ok12"></td><td>Если заполнить то будет отображаться в контактах</td></tr>';

	echo '<tr><td align="left"><b>Дата открытия сайта:</b></td><td><input type="text" class="ok12" id="startDate" name="startdate" value="'.DATE("d.m.Y", strtotime($row["startdate"])).'" style="text-align:center;"></td><td></td></tr>';

	echo '<tr>';
		echo '<td align="left"><b>Режим работы сайта:</b></td>';
		echo '<td align="left">';
			echo '<select name="site_status" style="width:125px; text-align:center;">';
				echo '<option value="0" '.("0" == $row["site_status"] ? 'selected="selected"' : false).'>Тех работы</option>';
				echo '<option value="1" '.("1" == $row["site_status"] ? 'selected="selected"' : false).'>Рабочий режим</option>';
			echo '</select>';
		echo '</td>';
		echo '<td>&nbsp;&nbsp;</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td><b>Текст для тех. работ:</b></td>';
		echo '<td>';
			echo '<textarea name="site_status_txt" class="ok" style="text-align:center;">'.$row["site_status_txt"].'</textarea>';
		echo '</td>';
		echo '<td>Описание для статуса технических работ</td>';
	echo '</tr>';

	echo '<tr>';
		echo '<td align="left"><b>Удаление неактивных (и/или заблокированных) пользователей</b></td>';
		echo '<td align="left">';
			echo '<select name="del_users_status" style="width:125px; text-align:center; text-align-last:center;">';
				echo '<option value="0" '.($del_users_status==0 ? 'selected="selected"' : false).'>Нет</option>';
				echo '<option value="1" '.($del_users_status==1 ? 'selected="selected"' : false).'>Да</option>';
			echo '</select>';
		echo '</td>';
		echo '<td>&nbsp;&nbsp;</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Количество дней не активности заблокированных</b></td>';
		echo '<td><input type="text" name="del_ban_users_day" value="'.$del_ban_users_day.'" class="ok12" style="text-align:center;"></td>';
		echo '<td>количество дней которые пользователь не посещал аккаунт</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Количество дней не активности неактивных</b></td>';
		echo '<td><input type="text" name="del_noact_users_day" value="'.$del_noact_users_day.'" class="ok12" style="text-align:center;"></td>';
		echo '<td>количество дней которые пользователь не посещал аккаунт</td>';
	echo '</tr>';

	echo '<tr align="center"><td>&nbsp;&nbsp;</td><td><input type="submit" value="Cохранить изменения" class="sub-blue160"><td>&nbsp;&nbsp;</td></tr>';
echo '</table>';
echo '</form><br><br>';


?>