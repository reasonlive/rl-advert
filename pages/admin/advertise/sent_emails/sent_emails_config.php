<?php
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка рассылки на e-mail</b></h1>';

if(count($_POST)>0) {
	$cena_sent_emails = isset($_POST["cena_sent_emails"]) ? number_format(abs(str_replace(",", ".", trim($_POST["cena_sent_emails"]))), 2, ".", "") : false;

	$mysqli->query("UPDATE `tb_config` SET `price`='$cena_sent_emails' WHERE `item`='cena_sent_emails' AND `howmany`='1'") or die($mysqli->error);

	echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

	echo '<script type="text/javascript">
		setTimeout(function() {
			window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
		}, 1200);
		HideMsg("info-msg", 1210);
	</script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_sent_emails'");
$cena_sent_emails = number_format($sql->fetch_object()->price, 2, ".", "");

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:500px; margin:0px; padding:0px;">';
echo '<thead>';
	echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
echo '</thead>';
echo '<tbody>';
	echo '<tr align="left"><td><b>Стоимость рассылки</b>, [руб.]</td><td><input type="text" class="ok12" name="cena_sent_emails" value="'.$cena_sent_emails.'" style="text-align:center;"></td></tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</tbody>';
echo '</table>';
echo '</form>';

?>