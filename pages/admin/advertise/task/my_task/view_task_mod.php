<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
?>
<script language="JavaScript">

function saveform(){
		arrayElem = document.forms["formtask"];
		var col=0;
		for (var i=0;i<arrayElem.length;i++){
			if (arrayElem[i].getAttribute("TYPE")=="radio" && arrayElem[i].checked == true && arrayElem[i].getAttribute("taskfalse")==1){
				if (arrayElem[i+1].value.length < 5){
					alert("Вы не указали причину, почему задание не выполнено!");
					arrayElem[i+1].style.backgroundColor = "#ff9999";
					arrayElem[i+1].focus();
					return false;
				}
			}
		}
	return true;
}
function setallyes(){
		arrayElem = document.forms["formtask"];
		var col=0;
		for (var i=0;i<arrayElem.length;i++){
			if (arrayElem[i].getAttribute("TYPE")=="radio" && arrayElem[i].getAttribute("taskyes")==1){
				arrayElem[i].checked = true;
			}
		}
	return false;
}
	
</script>


<?php

$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

echo '<b>Оплачиваемые задания:</b><br><br>';
echo '<b>Заявки на проверку выполнения задания #'.$rid.':</b><br><br>';


$date_s = DATE("d.m.Y");
$date_v = DATE("d.m.Y", (time()-24*60*60));

include("../geoip/geoipcity.inc");
include("../geoip/geoipregionvars.php");
$gi = geoip_open("../geoip/GeoLiteCity.dat",GEOIP_STANDARD);

function limpiarez($mensaje){
	$mensaje = htmlspecialchars(trim($mensaje));
	$mensaje = str_replace("'"," ",$mensaje);
	$mensaje = str_replace(";"," ",$mensaje);
	$mensaje = str_replace("$","$",$mensaje);
	$mensaje = str_replace("<","&#60;",$mensaje);
	$mensaje = str_replace(">","&#62;",$mensaje);
	$mensaje = str_replace("\\","",$mensaje);
	$mensaje = str_replace("&amp amp ","&amp;",$mensaje);
	$mensaje = str_replace("&amp quot ","&quot;",$mensaje);
	$mensaje = str_replace("&amp gt ","&gt;",$mensaje);
	$mensaje = str_replace("&amp lt ","&lt;",$mensaje);
	$mensaje = str_replace("\r\n","<br>",$mensaje);
	return $mensaje;
}


if(count($_POST) > 0 && isset($_POST["checktask"])) {
	$checktask = (isset($_POST["checktask"])) ? array_map('mysql_real_escape_string', array_map('intval', $_POST["checktask"])) : false;
	$checkmess = (isset($_POST["checkmess"])) ? array_map('mysql_real_escape_string', array_map('limpiarez', $_POST["checkmess"])) : false;
	$banuser = (isset($_POST["banuser"])) ? array_map('mysql_real_escape_string', array_map('intval', $_POST["banuser"])) : false;


	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username' AND (`totals`>'0' OR `wait`>'0')") or die($mysqli->error);
	if($sql->num_rows>0) {

		$row = $sql->fetch_array();
		$zdre = $row["zdre"];
		$rek_name = $row["username"];
		$zdprice = $row["zdprice"];


		foreach ($checktask as $k => $d) {
			$checktask[] = array();
			$checkmess[] = array();
			$banuser[] = array();
			$id_pay = $k;
			$status_t = $checktask[$k];

			$sql_w = $mysqli->query("SELECT `user_name` FROM `tb_ads_task_pay` WHERE `id`='$id_pay' AND `status`='wait' AND `type`='task' AND `rek_name`='$username' AND `ident`='$rid'");
			if($sql_w->num_rows>0) {
				$row_w = $sql_w->fetch_array();
				$user_name = $row_w["user_name"];

				if($status_t==1) {
					$status = "good"; $text_bad=""; $ban_user="0";
					$mysqli->query("UPDATE `tb_ads_task` SET `goods`=`goods`+'1', `wait`=`wait`-'1' WHERE `id`='$rid' AND `username`='$username' AND (`totals`>'0' OR `wait`>'0')") or die($mysqli->error);
					$mysqli->query("UPDATE `tb_users` SET `visits_t`=`visits_t`+1, `money`=`money`+'$zdprice', `money_t`=`money_t`+'$zdprice' WHERE `username`='$user_name'") or die($mysqli->error);

					$sql_user = $mysqli->query("SELECT `id`,`referer`,`referer2`,`referer3` FROM `tb_users` WHERE `username`='$user_name'");
					if($sql_user->num_rows>0) {
						$row_user = $sql_user->fetch_assoc();
						$my_referer_1 = $row_user["referer"];
						$my_referer_2 = $row_user["referer2"];
						$my_referer_3 = $row_user["referer3"];
						
					}else{
						$my_referer_1 = false;
						$my_referer_2 = false;
						$my_referer_3 = false;
						
					}

					$add_money_r_1 = 0; $add_money_r_2 = 0; $add_money_r_3 = 0;

					if($my_referer_1 != false ) {
						$sql_rang_1 = $mysqli->query("SELECT `t_1` FROM `tb_config_rang` WHERE `id`='1'");
						$ref_proc_1 = $sql_rang_1->num_rows>0 ? $sql_rang_1->fetch_object()->t_1 : 0;

						$add_money_r_1 = p_floor( ($zdprice * $ref_proc_1 / 100), 6);

						$mysqli->query("UPDATE `tb_users` SET `refmoney1`=`refmoney1`+'$add_money_r_1', `money`=`money`+'$add_money_r_1' WHERE `username`='$my_referer_1'") or die($mysqli->error);

						if($my_referer_2 != false ) {
							$sql_rang_2 = $mysqli->query("SELECT `t_2` FROM `tb_config_rang` WHERE `id`='1'");
							$ref_proc_2 = $sql_rang_2->num_rows>0 ? $sql_rang_2->fetch_object()->t_2 : 0;

							$add_money_r_2 = p_floor( ($zdprice * $ref_proc_2 / 100), 6);

							$mysqli->query("UPDATE `tb_users` SET `refmoney2`=`refmoney2`+'$add_money_r_2', `money`=`money`+'$add_money_r_2' WHERE `username`='$my_referer_2'") or die($mysqli->error);

							if($my_referer_3 != false ) {
								$sql_rang_3 = $mysqli->query("SELECT `t_3` FROM `tb_config_rang` WHERE `id`='1'");
								$ref_proc_3 = $sql_rang_3->num_rows>0 ? $sql_rang_3->fetch_object()->t_3 : 0;

								$add_money_r_3 = p_floor( ($zdprice * $ref_proc_3 / 100), 6);

								$mysqli->query("UPDATE `tb_users` SET `refmoney3`=`refmoney3`+'$add_money_r_3', `money`=`money`+'$add_money_r_3' WHERE `username`='$my_referer_3'") or die($mysqli->error);

							}
						}

						$sql_t = $mysqli->query("SELECT * FROM `tb_refkonkurs` WHERE `status`='1' AND `username`='$my_referer_1' AND `type_kon`='1' AND `date_s`<'".time()."' AND `date_e`>'".time()."' ORDER BY `id` DESC LIMIT 1");
						if($sql_t->num_rows>0) {
							$row_t = $sql_t->fetch_array();

							$sql_kon = $mysqli->query("SELECT `id` FROM `tb_refkonkurs_stat` WHERE `username`='$user_name' AND `ident`='".$row_t["id"]."' AND `type`='".$row_t["type_kon"]."'");
							if($sql_kon->num_rows > 0) {
								$mysqli->query("UPDATE `tb_refkonkurs_stat` SET `kolvo`=`kolvo`+'1' WHERE `username`='$user_name' AND `ident`='".$row_t["id"]."' AND `type`='".$row_t["type_kon"]."'") or die($mysqli->error);
							}else{
								$mysqli->query("INSERT INTO `tb_refkonkurs_stat` (`username`,`ident`,`type`,`kolvo`) VALUES('$user_name','".$row_t["id"]."','".$row_t["type_kon"]."','1')") or die($mysqli->error);
							}
						}
					}

					$sql_rek = $mysqli->query("SELECT `referer` FROM `tb_users` WHERE `username`='$username'");
					$row_rek = $sql_rek->fetch_row();

					$referer_rek = $row_rek["0"];

					if($referer_rek!="") {
						$sql_cr = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='ref_proc_rek' AND `howmany`='1'");
						$ref_proc_rek = $sql_cr->fetch_object()->price;

						$add_balans_rek = round(($zdprice * $ref_proc_rek / 100), 6);

						$mysqli->query("UPDATE `tb_users` SET `money`=`money`+'$add_balans_rek', `ref_money_rek`=`ref_money_rek`+'$add_balans_rek' WHERE `username`='$referer_rek'") or die($mysqli->error);
					}
				}else{

					$status = "bad"; $text_bad="$checkmess[$k]";
					if(isset($banuser[$k])) {
						if($banuser[$k]==1) {$ban_user="1";}else{$ban_user="0";}
					}else{
						$ban_user="0";
					}

					$mysqli->query("UPDATE `tb_ads_task` SET `totals`=`totals`+'1', `bads`=`bads`+'1', `wait`=`wait`-'1' WHERE `id`='$rid' AND `username`='$username' AND (`totals`>'0' OR `wait`>'0')") or die($mysqli->error);
				}

				$mysqli->query("UPDATE `tb_ads_task_pay` SET `status`='$status', `why`='$text_bad', `ban_user`='$ban_user', `pay`='$zdprice' WHERE `id`='$id_pay' AND `rek_name`='$username' AND `ident`='$rid' AND `type`='task' ORDER by `id` DESC LIMIT 1") or die($mysqli->error);
			}
		}
	}else{
		echo '<fieldset class="errorp">Ошибка! Необходимо пополнить баланс задания!</fieldset>';
	}
}


function universal_link_bar($op, $count, $ops_count, $show_link) {
	if ($ops_count == 1) return false;
		$sperator = ' &nbsp;';
		$style = 'style="font-weight: bold;"';
		$begin = $op - intval($show_link / 2);
		unset($show_dots);

		if ($ops_count <= $show_link + 1) $show_dots = 'no';
		if (($begin > 2) && !isset($show_dots) && ($ops_count - $show_link > 2)) {
			echo '<a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s=1> 1 </a> ';
		}

		for ($j = 0; $j < $op; $j++) {
			if (($begin + $show_link - $j > $ops_count) && ($ops_count-$show_link + $j > 0)) {
				$op_link = $ops_count - $show_link + $j;

				if (!isset($show_dots) && ($ops_count-$show_link > 1)) {
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($op_link - 1).'><b>...</b></a> ';
					$show_dots = "no";
				}

				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$op_link.'>'.$op_link.'</a> '.$sperator;
			} else continue;
		}

		for ($j = 0; $j <= $show_link; $j++) {
			$i = $begin + $j;

			if ($i < 1) { $show_link++; continue;}

			if (!isset($show_dots) && $begin > 1) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($i-1).'><b>...</b></a> ';
				$show_dots = "no";
			}

			if ($i > $ops_count) break;
			if ($i == $op) {
				echo ' <a '.$style.' ><b style="color:#FF0000; text-decoration:underline;">'.$i.'</b></a> ';
			}else{
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$i.'>'.$i.'</a> ';
			}

			if (($i != $ops_count) && ($j != $show_link)) echo $sperator;
			if (($j == $show_link) && ($i < $ops_count)) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($i+1).'><b>...</b></a> ';
		}
	}

	if ($begin + $show_link + 1 < $ops_count) {
		echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$ops_count.'> '.$ops_count.' </a>';
	}
	return true;
}


$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

	$perop = 10;
	if (empty($_GET["s"]) || ($_GET["s"] <= 0)) {
		$op = 1;
	}else{
		$op = intval($_GET["s"]);
	}

	$count = $mysqli->query("SELECT `id` FROM `tb_ads_task_pay` WHERE `status`='wait' AND `type`='task' AND `rek_name`='$username' AND `ident`='$rid'")->num_rows;
	$ops_count = ceil($count / $perop);

	if ($op > $ops_count) $op = $ops_count;
	$start_pos = ($op - 1) * $perop;
	if($start_pos<0) $start_pos=0;


	$sql_w = $mysqli->query("SELECT * FROM `tb_ads_task_pay` WHERE `status`='wait' AND `type`='task' AND `rek_name`='$username' AND `ident`='$rid' ORDER BY `id` ASC LIMIT $start_pos,$perop");
	$wait_all = $sql_w->num_rows;

	echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Оплачиваемое задание</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Название:</b></td>';
			echo '<td>&nbsp;'.str_replace("\r\n","<br>", $row["zdname"]).'</td>';
		echo '</tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td width="200" align="right" height="30px"><b>URL:</b></td>';
			echo '<td><a href="'.$row["zdurl"].'" target="_blank">'.$row["zdurl"].'</a></td>';
		echo '</tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Рейтинг:</b></td>';
			echo '<td><table width="100%"><tr><td width="200">&nbsp;'.round($row["reiting"], 2).' (проголосовало '.$row["all_coments"].')</td><td style="border-left: 1px solid #cccccc; padding-left:20px;"><span style="color:green;">'.$row["goods"].'</span> - <span style="color:red;">'.$row["bads"].'</span> - <span style="color:black;">'.$row["wait"].'</span></td></tr></table></td>';
		echo '</tr>';
		echo '<tr bgcolor="#1E90FF" align="center"><th align="center" colspan="2">Описание задания</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td colspan="2">'.$row["zdtext"].'</td>';
		echo '</tr>';
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Заявки на выполнение:</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td colspan="2" align="left">Заявок всего: <b>'.$count.'</b>, показано <b>'.$wait_all.'</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" OnClick="setallyes();"><img src="../../img/yes.png" title="отметить все положительные" alt="" width="16" height="16" align="absmiddle" border="0" /></a> <a href="javascript:void(0);" OnClick="setallyes();" style="color:green; font-size: 12px; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;">отметить все положительные</a></td>';
		echo '</tr>';
        echo '</table>';

	if($wait_all>0) {
		echo '<form action="" method="POST" name="formtask" id="formtask" onSubmit="return saveform();">';
		echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #1E90FF;">';

		while($row_w = $sql_w->fetch_assoc()) {
			$record = geoip_record_by_addr($gi, $row_w["ip"]);
			if($record==false) {
				$country_code="";
			}else{
				$country_code = $record->country_code;
			}

			echo '<tr bgcolor="#AFEEEE">';
				$sql_u = $mysqli->query("SELECT `id`,`username`,`avatar` FROM `tb_users` WHERE `username`='".$row_w["user_name"]."'");
				if($sql_u->num_rows>0) {
					$row_u = $sql_u->fetch_array();
					echo '<td align="center" width="100"><img src="../avatar/'.$row_u["avatar"].'" border="0" alt="" align="middle" />';
					echo '<td align="left" width="100%">Пользователь: #'.$row_u["id"].' <b>'.$row_u["username"].'</b><br>Начало выполнения задания: '.DATE("d.m.Yг. H:i", $row_w["date_start"]).'<br>Окончание выполнения задания:'.DATE("d.m.Yг. H:i", $row_w["date_end"]).'<br>IP-адрес: '.$row_w["ip"].' ['.$country_code.'] <img src="../img/flags/'.strtolower($country_code).'.gif" width="16" height="12" border="0"  align="middle" alt="" /></td>';
				}else{
					echo '<td align="center" width="100"><img src="../avatar/no.png" border="0" alt="" align="middle" />';
					echo '<td align="left" width="100%">Пользователь: #'.$row_w["id"].' <b>'.$row_w["username"].'</b> <span style="color:#FF0000;">Пользователь удален</span><br>Начало выполнения задания: '.DATE("d.m.Yг. H:i", $row_w["date_start"]).'<br>Окончание выполнения задания:'.DATE("d.m.Yг. H:i", $row_w["date_end"]).'<br>IP-адрес: '.$row_w["ip"].' ['.$country_code.'] <img src="img/flags/'.strtolower($country_code).'.gif" width="16" height="12" border="0"  align="middle" alt="" /></td>';
				}
			echo '</tr>';
			echo '<tr bgcolor="#ADD8E6">';
				echo '<td colspan="2"><b>Информация</b>, переданная пользователем, для проверки задания:<br>'.$row_w["ctext"].'</td>';
			echo '</tr>';
			echo '<tr bgcolor="#AFEEEE">';
				echo '<td colspan="2" style="border-bottom: 2px solid #1E90FF;">
					<input type="radio" name="checktask['.$row_w["id"].']" value="1" id="'.$row_w["id"].'" onChange="document.getElementById(\'bad'.$row_w["id"].'\').style.display=\'none\'" taskyes="1"> - <img src="../../img/yes.png" border="0" alt="" align="middle" title="задание выполнено" /> задание выполнено &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="checktask['.$row_w["id"].']" value="2" id="'.$row_w["id"].'" onChange="document.getElementById(\'bad'.$row_w["id"].'\').style.display=\'block\'" taskfalse="1"> - <img src="../../img/no.png" border="0" alt="" align="middle" title="задание выполнено" /> задание не выполнено';
					echo '<div id="bad'.$row_w["id"].'" style="display:none;">
						<b>Причина</b>, почему задание не выполнено: <input type="text" name="checkmess['.$row_w["id"].']" value="" size="80" maxlength="100"><br>
						<input type="checkbox" name="banuser['.$row_w["id"].']" id="'.$row_w["id"].'" value="1"> -  жалоба на пользователя
					</div>';
				echo '</td>';
			echo '</tr>';
		}
		echo '<tr align="center" bgcolor="#ADD8E6">';
			echo '<td colspan="2"><input type="submit" class="submit" value="&nbsp;&nbsp;&nbsp;Сохранить&nbsp;&nbsp;&nbsp;"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';

		if($count>$perop){
			echo '<div align="center" style="padding-top:10px;"><b>Страницы:</b> '; universal_link_bar($op, $count, $ops_count, 8); echo '</div>';
		}
	}
	geoip_close($gi);
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
}

?>