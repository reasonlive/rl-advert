<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Настройки статусов</b></h1>';

if(count($_POST)>0) {
	$y=13;
	for($i=1; $i<=$y; $i++) {
		$id[$i] = intval(trim($_POST["id_$i"]));

		$r_ot[$i] = round(trim($_POST["r_ot_$i"]), 2);
		$r_do[$i] = round(trim($_POST["r_do_$i"]), 2);

		$c_1[$i] = intval(trim($_POST["c_1_$i"]));
		$c_2[$i] = intval(trim($_POST["c_2_$i"]));
		$c_3[$i] = intval(trim($_POST["c_3_$i"]));
		
		$youtube_1[$i] = intval(trim($_POST["youtube_1_$i"]));
		$youtube_2[$i] = intval(trim($_POST["youtube_2_$i"]));
		$youtube_3[$i] = intval(trim($_POST["youtube_3_$i"]));

		$m_1[$i] = intval(trim($_POST["m_1_$i"]));
		$m_2[$i] = intval(trim($_POST["m_2_$i"]));
		$m_3[$i] = intval(trim($_POST["m_3_$i"]));

		$t_1[$i] = intval(trim($_POST["t_1_$i"]));
		$t_2[$i] = intval(trim($_POST["t_2_$i"]));
		$t_3[$i] = intval(trim($_POST["t_3_$i"]));

		$test_1[$i] = intval(trim($_POST["test_1_$i"]));
		$test_2[$i] = intval(trim($_POST["test_2_$i"]));
		$test_3[$i] = intval(trim($_POST["test_3_$i"]));
		
		$pv_1[$i] = intval(trim($_POST["pv_1_$i"]));
		$pv_2[$i] = intval(trim($_POST["pv_2_$i"]));
		$pv_3[$i] = intval(trim($_POST["pv_3_$i"]));
		
		$pay_min_click[$i] = intval(trim($_POST["pay_min_click_$i"]));

		$balance_1[$i] = isset($_POST["balance_1_$i"]) ? number_format(trim(str_ireplace(",", ".", $_POST["balance_1_$i"])), 2, ".", "") : 0;

		$wall_comm[$i] = ( isset($_POST["wall_comm_$i"]) && preg_match("|^[0-1]{1}$|", intval(trim($_POST["wall_comm_$i"]))) ) ? intval(trim($_POST["wall_comm_$i"])) : 0;
		$max_pay[$i] = isset($_POST["max_pay_$i"]) ? number_format(trim($_POST["max_pay_$i"]), 2, ".", "") : false;

		$mysqli->query("UPDATE `tb_config_rang` SET 
			`r_ot`='$r_ot[$i]', `r_do`='$r_do[$i]', 
			`c_1`='$c_1[$i]', `c_2`='$c_2[$i]', `c_3`='$c_3[$i]', 
			`youtube_1`='$youtube_1[$i]', `youtube_2`='$youtube_2[$i]', `youtube_3`='$youtube_3[$i]',
			`m_1`='$m_1[$i]', `m_2`='$m_2[$i]', `m_3`='$m_3[$i]', 
			`t_1`='$t_1[$i]', `t_2`='$t_2[$i]', `t_3`='$t_3[$i]', 
			`test_1`='$test_1[$i]', `test_2`='$test_2[$i]', `test_3`='$test_3[$i]', 
			`balance_1`='$balance_1[$i]', `wall_comm`='$wall_comm[$i]',
            `pv_1`='$pv_1[$i]', `pv_2`='$pv_2[$i]', `pv_3`='$pv_3[$i]', 
			`max_pay`='$max_pay[$i]', `pay_min_click`='$pay_min_click[$i]'			
		WHERE `id`='$id[$i]'") or die($mysqli->error);

		if($i==$y) {
			echo '<span id="info-msg" class="msg-ok">Изменения успешно сохранены!</span>';
			echo '<script type="text/javascript">
				setTimeout(function() {window.history.replaceState(null, null, "'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'");}, 50);
				HideMsg("info-msg", 1500);
			</script>';
			echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
		}
	}
}

echo '<form method="post" action="" id="newform">';
echo '<table class="tables" style="margin-top:0px;">';
	echo '<tr>';
		echo '<th class="center" rowspan="2">Статус</th>';
		echo '<th class="center" rowspan="2">Рейтинг<br>от и до</th>';
		echo '<th class="center" colspan="7">Доход от рефералов, %</th>';
		echo '<th class="center" rowspan="2">Максимум<br>к выплате<br>(руб)</th>';
		echo '<th class="center" rowspan="2">Минимум<br>кликов для выплат<br>(руб)</th>';
		echo '<th class="center" rowspan="2">Отзывы на стенах</th>';
	echo '</tr>';
	echo '<tr>';
		echo '<th class="center">Серфинг</th>';
		echo '<th class="center">YouTube</th>';
		echo '<th class="center">Письма</th>';
		echo '<th class="center">Задания</th>';
		echo '<th class="center">Тесты</th>';
		echo '<th class="center">Посещения</th>';
		echo '<th class="center">От пополн.<br>рекл. счета (1ур.)</th>';
	echo '</tr>';

	$sql = $mysqli->query("SELECT * FROM `tb_config_rang` ORDER BY `id` ASC");
	if($sql->num_rows>0) {
		while ($row = $sql->fetch_array()) {
			echo '<input type="hidden" name="id_'.$row["id"].'" value="'.$row["id"].'">';
			echo '<tr>';
				echo '<td class="left"><b>'.$row["rang"].'</b></td>';
				if($row["id"]==13) {
	        			echo '<td class="center">более <input type="text" class="ok12" style="width:40px; text-align:center;" name="r_ot_'.$row["id"].'" value="'.$row["r_ot"].'"><input type="hidden" class="ok12" style="width:40px; text-align:center;" name="r_do_'.$row["id"].'" value="'.$row["r_do"].'"></td>';
				}else{
	        			echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="r_ot_'.$row["id"].'" value="'.$row["r_ot"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="r_do_'.$row["id"].'" value="'.$row["r_do"].'"></td>';
				}
        			echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="c_1_'.$row["id"].'" value="'.$row["c_1"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="c_2_'.$row["id"].'" value="'.$row["c_2"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="c_3_'.$row["id"].'" value="'.$row["c_3"].'"></td>';
        			echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="youtube_1_'.$row["id"].'" value="'.$row["youtube_1"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="youtube_2_'.$row["id"].'" value="'.$row["youtube_2"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="youtube_3_'.$row["id"].'" value="'.$row["youtube_3"].'"></td>';
	        		echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="m_1_'.$row["id"].'" value="'.$row["m_1"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="m_2_'.$row["id"].'" value="'.$row["m_2"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="m_3_'.$row["id"].'" value="'.$row["m_3"].'"></td>';
        			echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="t_1_'.$row["id"].'" value="'.$row["t_1"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="t_2_'.$row["id"].'" value="'.$row["t_2"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="t_3_'.$row["id"].'" value="'.$row["t_3"].'"></td>';
        			echo '<td class="center"><input type="text" class="ok12" style="width:40px; text-align:center;" name="test_1_'.$row["id"].'" value="'.$row["test_1"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="test_2_'.$row["id"].'" value="'.$row["test_2"].'"> - <input type="text" class="ok12" style="width:40px; text-align:center;" name="test_3_'.$row["id"].'" value="'.$row["test_3"].'"></td>';
					echo '<td align="center"><input type="text" class="ok12" style="width:25px; text-align:center;" name="pv_1_'.$row["id"].'" value="'.$row["pv_1"].'"> - <input type="text" class="ok12" style="width:25px; text-align:center;" name="pv_2_'.$row["id"].'" value="'.$row["pv_2"].'"> - <input type="text" class="ok12" style="width:25px; text-align:center;" name="pv_3_'.$row["id"].'" value="'.$row["pv_3"].'"></td>';
        			echo '<td class="center"><input type="text" class="ok12" style="width:60px; text-align:center;" name="balance_1_'.$row["id"].'" value="'.$row["balance_1"].'"></td>';
					echo '<td align="center"><input type="text" class="ok12" style="width:45px; text-align:center;" name="max_pay_'.$row["id"].'" value="'.$row["max_pay"].'"></td>';
					echo '<td align="center"><input type="text" class="ok12" style="width:45px; text-align:center;" name="pay_min_click_'.$row["id"].'" value="'.$row["pay_min_click"].'"></td>';
				echo '<td align="center"><input type="checkbox" name="wall_comm_'.$row["id"].'" value="1" '.($row["wall_comm"] == 1 ? 'checked="checked"' : false).' style="width:18px; height:18px;" /></td>';

			echo '</tr>';
		}

		echo '<tr>';
			echo '<td colspan="12" align="center" class="center">';
				echo '<div align="center"><input type="submit" value="Сохранить изменения" class="sub-blue160"></div>';
			echo '</td>';
		echo '</tr>';
	}
echo '</table>';
echo '</form>';
?>