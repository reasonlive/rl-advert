<?php
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка очистки внутреней почты</b></h1>';

if(count($_POST)>0) {
	
	$mail_vp_config = isset($_POST["mail_vp_config"]) ? number_format(abs(str_replace(",", ".", trim($_POST["mail_vp_config"]))), 2, ".", "") : false;
		
	$mysqli->query("UPDATE `tb_config` SET `price`='$mail_vp_config' WHERE `item`='mail_vp_config' AND `howmany`='1'") or die($mysqli->error);
	
	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1200);
		HideMsg("info-msg", 1210);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='mail_vp_config'");
$mail_vp_config = round(number_format($sql->fetch_object()->price, 2, ".", ""),2);

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:600px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left"><td><b>Очищать сообщения старше за n дней</b>, (дней)</td><td><input type="text" class="ok12" name="mail_vp_config" value="'.$mail_vp_config.'" style="text-align:center;"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>