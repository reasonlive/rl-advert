<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройка копилки проекта</b></h1>';

if(count($_POST)>0) {

	$kopilka_cash_in = round(floatval(abs(trim($_POST["kopilka_cash_in"]))),2);
	$kopilka_cash_out = round(floatval(abs(trim($_POST["kopilka_cash_out"]))),2);
	$kopilka_reit_out = intval(abs(trim($_POST["kopilka_reit_out"])));
	$kopilka_period_out = intval(abs(trim($_POST["kopilka_period_out"])));
	$kopilka_min_click = intval(abs(trim($_POST["kopilka_min_click"])));
	$reit_kop = isset($_POST["reit_kop"]) ? floatval(trim($_POST["reit_kop"])) : "0";

	if($kopilka_cash_in<=0) {
		echo '<span id="info-msg" class="msg-error" style="width:700px;">Минимальная сумма для пополнения копилки должна быть больше 0!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 5000); </script>';
	}elseif($kopilka_cash_out<=0) {
		echo '<span id="info-msg" class="msg-error" style="width:700px;">Сумма для получения средств из копилки должна быть больше 0!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 5000); </script>';
	}elseif($kopilka_cash_out>=$kopilka_cash_in) {
		echo '<span id="info-msg" class="msg-error" style="width:700px;">Сумма для получения средств из копилки должна быть меньше чем для пополнения!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 5000); </script>';
	}elseif($kopilka_reit_out<1) {
		echo '<span id="info-msg" class="msg-error" style="width:700px;">Для получения средств из копилки рейтинг должен быть более 1 балла!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 5000); </script>';
	}elseif($kopilka_period_out<3) {
		echo '<span id="info-msg" class="msg-error" style="width:700px;">Периодичность получения средств из копилки должна быть не менее 6 часов!</span>';
		echo '<script type="text/javascript"> setTimeout(function() {hidemsg()}, 5000); </script>';
	}else{
		$mysqli->query("UPDATE `tb_config` SET `price`='$kopilka_cash_in' WHERE `item`='kopilka_cash_in'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$kopilka_cash_out' WHERE `item`='kopilka_cash_out'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$kopilka_reit_out' WHERE `item`='kopilka_reit_out'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$kopilka_period_out' WHERE `item`='kopilka_period_out'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$kopilka_min_click' WHERE `item`='kopilka_min_click'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$reit_kop' WHERE `item`='reit_kop'") or die($mysqli->error);

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';

		echo '<script type="text/javascript">
			setTimeout(function() {
				window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");
			}, 1500);
			HideMsg("info-msg", 1500);
		</script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='kopilka_cash_in'");
$kopilka_cash_in = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='kopilka_cash_out'");
$kopilka_cash_out = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='kopilka_reit_out'");
$kopilka_reit_out = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='kopilka_period_out'");
$kopilka_period_out = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='kopilka_min_click'");
$kopilka_min_click = $sql->fetch_object()->price;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_kop'");
$reit_kop = $sql->fetch_object()->price;


echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:700px;">';
echo '<tr align="center"><th>Параметр</th><th width="125">Значение</th></tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальная сумма для пополнения копилки</b>, (руб.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="kopilka_cash_in" value="'.number_format($kopilka_cash_in,2,".","").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Сумма для получения средств из копилки</b>, (руб.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="kopilka_cash_out" value="'.number_format($kopilka_cash_out,2,".","").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальный рейтинг пользователя для получения средств из копилки</b>, (баллы.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="kopilka_reit_out" value="'.number_format($kopilka_reit_out,0,".","").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>
	<td align="left"><b>Рейтинг за каждые полные 10 руб потраченные на пополнение копилки</b>, (баллы.)<b>:</b></td>
	<td align="center"><input type="text" class="ok12" name="reit_kop" value="'.$reit_kop.'" style="text-align:center;"></td>
	</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальное количество кликов в серфинге для получения средств из копилки</b>, (шт./день)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="kopilka_min_click" value="'.number_format($kopilka_min_click,0,".","").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Периодичность получения средств из копилки минимум</b>, (часов.)<b>:</b></td>';
		echo '<td align="center"><input type="text" class="ok12" name="kopilka_period_out" value="'.number_format($kopilka_period_out,0,".","").'" style="text-align:center;"></td>';
	echo '</tr>';
	echo '<tr align="center"><td colspan="2"><input type="submit" value="Cохранить изменения" class="sub-blue160"></td></tr>';
echo '</table>';
echo '</form>';

?>