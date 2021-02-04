<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки оплачиваемых тестов</b></h3>';

if(count($_POST)>0) {
	$tests_cena_hit = (isset($_POST["cena_hit"]) && floatval(abs(trim($_POST["cena_hit"])))>0) ? number_format(floatval(abs(trim($_POST["cena_hit"]))),4,".","") : 0;
	$tests_comis_del = (isset($_POST["comis_del"]) && floatval(abs(trim($_POST["comis_del"])))>0) ? number_format(floatval(abs(trim($_POST["comis_del"]))),0,".","") : 0;
	$tests_nacenka = (isset($_POST["nacenka"]) && floatval(abs(trim($_POST["nacenka"])))>0) ? number_format(floatval(abs(trim($_POST["nacenka"]))),0,".","") : 0;
	$tests_min_pay = (isset($_POST["min_pay"]) && floatval(abs(trim($_POST["min_pay"])))>0) ? number_format(floatval(abs(trim($_POST["min_pay"]))),2,".","") : 0;
	$tests_cena_quest = (isset($_POST["cena_quest"]) && floatval(abs(trim($_POST["cena_quest"])))>0) ? number_format(floatval(abs(trim($_POST["cena_quest"]))),4,".","") : 0;
	$tests_cena_color = (isset($_POST["cena_color"]) && floatval(abs(trim($_POST["cena_color"])))>0) ? number_format(floatval(abs(trim($_POST["cena_color"]))),4,".","") : 0;

	if($tests_cena_hit==0) {
		echo '<span id="info-msg" class="msg-error">Цена за тест не может быть равна 0</span>';
	}else{
		for($i=1; $i<=4; $i++) {
			$tests_cena_revisit[0] = 0;
			$tests_cena_revisit[$i] = (isset($_POST["cena_revisit_"."$i"]) && floatval(abs(trim($_POST["cena_revisit_"."$i"])))>0) ? number_format(floatval(abs(trim($_POST["cena_revisit_"."$i"]))),4,".","") : 0;
		}

		for($i=1; $i<=2; $i++) {
			$tests_cena_unic_ip[0] = 0;
			$tests_cena_unic_ip[$i] = (isset($_POST["cena_unic_ip_"."$i"]) && floatval(abs(trim($_POST["cena_unic_ip_"."$i"])))>0) ? number_format(floatval(abs(trim($_POST["cena_unic_ip_"."$i"]))),4,".","") : 0;
		}

		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_cena_hit' WHERE `item`='tests_cena_hit' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_comis_del' WHERE `item`='tests_comis_del' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_nacenka' WHERE `item`='tests_nacenka' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_min_pay' WHERE `item`='tests_min_pay' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_cena_quest' WHERE `item`='tests_cena_quest' AND `howmany`='1'") or die($mysqli->error);
		$mysqli->query("UPDATE `tb_config` SET `price`='$tests_cena_color' WHERE `item`='tests_cena_color' AND `howmany`='1'") or die($mysqli->error);

		for($i=1; $i<=4; $i++) {
			$mysqli->query("UPDATE `tb_config` SET `price`='$tests_cena_revisit[$i]' WHERE `item`='tests_cena_revisit' AND `howmany`='$i'") or die($mysqli->error);
		}

		for($i=1; $i<=2; $i++) {
			$mysqli->query("UPDATE `tb_config` SET `price`='$tests_cena_unic_ip[$i]' WHERE `item`='tests_cena_unic_ip' AND `howmany`='$i'") or die($mysqli->error);
		}

		$sql_test = $mysqli->query("SELECT `id`,`questions`,`revisit`,`color`,`unic_ip_user` FROM `tb_ads_tests` ORDER BY `id` DESC");
		if($sql_test->num_rows>0) {
			while ($row = $sql_test->fetch_assoc()) {
				$id = $row["id"];
				$questions = unserialize($row["questions"]);
				$summa_dd = 0;

				if(isset($questions[4]) && $questions[4]!="") $summa_dd+= $tests_cena_quest;
				if(isset($questions[5]) && $questions[5]!="") $summa_dd+= $tests_cena_quest;

				$cena_user = ($tests_cena_hit + $summa_dd)/(($tests_nacenka+100)/100);
				$cena_advs = ($tests_cena_hit + $summa_dd + $tests_cena_revisit[$row["revisit"]] + $tests_cena_color * $row["color"] + $tests_cena_unic_ip[$row["unic_ip_user"]]);

				$cena_user = number_format($cena_user, 4, ".", "");
				$cena_advs = number_format($cena_advs, 4, ".", "");

				$mysqli->query("UPDATE `tb_ads_tests` SET `cena_user`='$cena_user',`cena_advs`='$cena_advs' WHERE `id`='$id'") or die($mysqli->error);
			}
		}

		echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
	}

	echo '<script type="text/javascript"> setTimeout(function() {hidemsg("info-msg")}, 1000); </script>';
	echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
}

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_hit' AND `howmany`='1'");
$tests_cena_hit = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_comis_del' AND `howmany`='1'");
$tests_comis_del = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_nacenka' AND `howmany`='1'");
$tests_nacenka = number_format($sql->fetch_object()->price, 0, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_min_pay' AND `howmany`='1'");
$tests_min_pay = number_format($sql->fetch_object()->price, 2, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_quest' AND `howmany`='1'");
$tests_cena_quest = number_format($sql->fetch_object()->price, 4, ".", "");

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_color' AND `howmany`='1'");
$tests_cena_color = number_format($sql->fetch_object()->price, 4, ".", "");

for($i=1; $i<=4; $i++) {
	$tests_cena_revisit[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_revisit' AND `howmany`='$i'");
	$tests_cena_revisit[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

for($i=1; $i<=2; $i++) {
	$tests_cena_unic_ip[0] = 0;
	$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='tests_cena_unic_ip' AND `howmany`='$i'");
	$tests_cena_unic_ip[$i] = number_format($sql->fetch_object()->price, 4, ".", "");
}

?>

<script type="text/javascript">
function PlanChange(){
	var nacenka = $.trim($("#nacenka").val());
	var comis_del = $.trim($("#comis_del").val());
	var cena_hit = $.trim($("#cena_hit").val());
	var cena_quest = $.trim($("#cena_quest").val());
	var cena_color = $.trim($("#cena_color").val());
	var cena_unic_ip_1 = $.trim($("#cena_unic_ip_1").val());
	var cena_unic_ip_2 = $.trim($("#cena_unic_ip_2").val());
	var cena_revisit_1 = $.trim($("#cena_revisit_1").val());
	var cena_revisit_2 = $.trim($("#cena_revisit_2").val());
	var cena_revisit_3 = $.trim($("#cena_revisit_3").val());
	var cena_revisit_4 = $.trim($("#cena_revisit_4").val());
	var min_pay = $.trim($("#min_pay").val());

	nacenka = str_replace(",", ".", nacenka).match(/(\d+)?/);
	nacenka = nacenka[0] ? nacenka[0] : ''; $("#nacenka").val(nacenka);

	comis_del = str_replace(",", ".", comis_del).match(/(\d+)?/);
	comis_del = comis_del[0] ? comis_del[0] : ''; $("#comis_del").val(comis_del);

	cena_hit = str_replace(",", ".", cena_hit).match(/(\d+(\.)?(\d){0,4})?/);
	cena_hit = cena_hit[0] ? cena_hit[0] : ''; $("#cena_hit").val(cena_hit);

	cena_quest = str_replace(",", ".", cena_quest).match(/(\d+(\.)?(\d){0,4})?/);
	cena_quest = cena_quest[0] ? cena_quest[0] : ''; $("#cena_quest").val(cena_quest);

	cena_color = str_replace(",", ".", cena_color).match(/(\d+(\.)?(\d){0,4})?/);
	cena_color = cena_color[0] ? cena_color[0] : ''; $("#cena_color").val(cena_color);

	cena_unic_ip_1 = str_replace(",", ".", cena_unic_ip_1).match(/(\d+(\.)?(\d){0,4})?/);
	cena_unic_ip_1 = cena_unic_ip_1[0] ? cena_unic_ip_1[0] : ''; $("#cena_unic_ip_1").val(cena_unic_ip_1);

	cena_unic_ip_2 = str_replace(",", ".", cena_unic_ip_2).match(/(\d+(\.)?(\d){0,4})?/);
	cena_unic_ip_2 = cena_unic_ip_2[0] ? cena_unic_ip_2[0] : ''; $("#cena_unic_ip_2").val(cena_unic_ip_2);

	cena_revisit_1 = str_replace(",", ".", cena_revisit_1).match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_1 = cena_revisit_1[0] ? cena_revisit_1[0] : ''; $("#cena_revisit_1").val(cena_revisit_1);

	cena_revisit_2 = str_replace(",", ".", cena_revisit_2).match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_2 = cena_revisit_2[0] ? cena_revisit_2[0] : ''; $("#cena_revisit_2").val(cena_revisit_2);

	cena_revisit_3 = str_replace(",", ".", cena_revisit_3).match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_3 = cena_revisit_3[0] ? cena_revisit_3[0] : ''; $("#cena_revisit_3").val(cena_revisit_3);

	cena_revisit_4 = str_replace(",", ".", cena_revisit_4).match(/(\d+(\.)?(\d){0,4})?/);
	cena_revisit_4 = cena_revisit_4[0] ? cena_revisit_4[0] : ''; $("#cena_revisit_4").val(cena_revisit_4);

	min_pay = str_replace(",", ".", min_pay).match(/(\d+(\.)?(\d){0,2})?/);
	min_pay = min_pay[0] ? min_pay[0] : ''; $("#min_pay").val(min_pay);

	cena_hit_user = cena_hit/(1+nacenka/100);
	cena_quest_user = cena_quest/(1+nacenka/100);

	min_pay_ads = (cena_hit * 1000);
	max_pay_ads = min_pay_ads;
	max_pay_ads += (cena_quest * 2 * 1000);
	max_pay_ads += (cena_color * 1000);
	max_pay_ads += (cena_unic_ip_2 * 1000);
	max_pay_ads += (cena_revisit_4 * 1000);

	$("#cena_hit_user").html('<b style="color:#FF0000; font-size:12px">' +  number_format(cena_hit_user, 4, ".", "") + '</b>');
	$("#cena_quest_user").html('<b style="color:#FF0000; font-size:12px">' + number_format(cena_quest_user, 4, ".", "") + '</b>');
	$("#min_pay_ads").html('<b style="color:green; font-size:12px">' + number_format(min_pay_ads, 2, ".", "`") + '</b>');
	$("#max_pay_ads").html('<b style="color:green; font-size:12px">' + number_format(max_pay_ads, 2, ".", "`") + '</b>');
}
</script>

<?php
echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="width:650px; margin:0px; padding:0px;">';
	echo '<thead><tr align="center"><th>Параметр</th><th width="130">Для пользователя</th><th width="130">Для рекламодателя</th></tr></thead>';

	echo '<tr>';
		echo '<td align="left"><b>Цена за 1 тест</b>, (руб./тест)</td>';
		echo '<td align="center" id="cena_hit_user"></td>';
		echo '<td align="center"><input type="text" id="cena_hit" name="cena_hit" value="'.p_floor($tests_cena_hit,4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Цена за 1 дополнительный вопрос</b>, (руб./тест)</td>';
		echo '<td align="center" id="cena_quest_user"></td>';
		echo '<td align="center"><input type="text" id="cena_quest" name="cena_quest" value="'.p_floor($tests_cena_quest,4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Цена за выделение цветом</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_color" name="cena_color" value="'.p_floor($tests_cena_color,4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за уникальный IP, 100% совпадение</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_unic_ip_1" name="cena_unic_ip_1" value="'.p_floor($tests_cena_unic_ip[1],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за уникальный IP, по маске до 2 чисел</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_unic_ip_2" name="cena_unic_ip_2" value="'.p_floor($tests_cena_unic_ip[2],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно всем каждые 3 дня</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_revisit_1" name="cena_revisit_1" value="'.p_floor($tests_cena_revisit[1],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно всем каждую неделю</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_revisit_2" name="cena_revisit_2" value="'.p_floor($tests_cena_revisit[2],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно всем каждые 2 недели</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_revisit_3" name="cena_revisit_3" value="'.p_floor($tests_cena_revisit[3],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Доплата за доступно всем каждый месяц</b>, (руб./тест)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="cena_revisit_4" name="cena_revisit_4" value="'.p_floor($tests_cena_revisit[4],4).'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>Минимальная сумма пополнения</b>, (руб.)</td>';
		echo '<td align="center">&nbsp;-&nbsp;</td>';
		echo '<td align="center"><input type="text" id="min_pay" name="min_pay" value="'.$tests_min_pay.'" class="ok12" style="text-align:right; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2"><b>Комиссия сайта</b>, (%)</td>';
		echo '<td align="center"><input type="text" id="nacenka" name="nacenka" value="'.$tests_nacenka.'" class="ok12" style="width:60px; text-align:center; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2"><b>Комиссия за возврат средств при удалении теста</b>, (%)</td>';
		echo '<td align="center"><input type="text" id="comis_del" name="comis_del" value="'.$tests_comis_del.'" class="ok12" style="width:60px; text-align:center; padding:1px 5px;" onKeydowm="PlanChange();" onKeyup="PlanChange();" autocomplete="off"></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="right" colspan="2" height="23"><b>Минимальная/Максимальная цена за 1000 тестов</b>, руб.</td>';
		echo '<td align="center"><span id="min_pay_ads"></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span id="max_pay_ads"></span></td>';
	echo '</tr>';

	echo '<tr align="center"><td colspan="3"><input type="submit" value="Cохранить" class="sub-green"></tr>';
echo '</table>';
echo '</form><br><br>';

?>

<script language="JavaScript">PlanChange();</script>