<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;
$date_s = DATE("d.m.Y");
$date_v = DATE("d.m.Y", (time()-24*60*60));

echo '<b>Подробная статистика по оплачиваемому заданию #'.$rid.':</b><br><br>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
if($sql->num_rows>0) {
	$row = $sql->fetch_array();

	$sql_vs = $mysqli->query("SELECT `id` FROM `tb_ads_task_stat` WHERE `type`='view' AND `ident`='$rid' AND `date`='$date_s'");
	$views_s = $sql_vs->num_rows;
	$sql_vv = $mysqli->query("SELECT `id` FROM `tb_ads_task_stat` WHERE `type`='view' AND `ident`='$rid' AND `date`='$date_v'");
	$views_v = $sql_vv->num_rows;

	$sql_cs = $mysqli->query("SELECT `id` FROM `tb_ads_task_stat` WHERE `type`='click' AND `ident`='$rid' AND `date`='$date_s'");
	$clicks_s = $sql_cs->num_rows;
	$sql_cv = $mysqli->query("SELECT `id` FROM `tb_ads_task_stat` WHERE `type`='click' AND `ident`='$rid' AND `date`='$date_v'");
	$clicks_v = $sql_cv->num_rows;

	if($views_s>0) {$ctr_s = (($clicks_s/$views_s) * 100);}else{$ctr_s=0;}
	if($views_v>0) {$ctr_v = (($clicks_v/$views_v) * 100);}else{$ctr_v=0;}
	if($row["views"]>0) {$ctr = (($row["clicks"]/$row["views"]) * 100);}else{$ctr=0;}

	echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Оплачиваемое задание</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Название:</b></td>';
			echo '<td>&nbsp;'.$row["zdname"].'</td>';
		echo '</tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td width="200" align="right" height="30px"><b>Тип задания:</b></td>';
			if($row["zdtype"]==1)
				echo '<td>&nbsp;Клики</td>';
			elseif($row["zdtype"]==2)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==3)
				echo '<td>&nbsp;Регистрация с активностью</td>';
			elseif($row["zdtype"]==4)
				echo '<td>&nbsp;Постинг в форум</td>';
			elseif($row["zdtype"]==5)
				echo '<td>&nbsp;Постинг в блоги</td>';
			elseif($row["zdtype"]==6)
				echo '<td>&nbsp;Голосование</td>';
			elseif($row["zdtype"]==7)
				echo '<td>&nbsp;Загрузка файлов</td>';
			elseif($row["zdtype"]==8)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==9)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==10)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==11)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==12)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==13)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==14)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==15)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==16)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==17)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==18)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==19)
				echo '<td>&nbsp;Регистрация без активности</td>';
			elseif($row["zdtype"]==20)
				echo '<td>&nbsp;Прочее</td>';
			else{
				echo '<td></td>';
			}
		echo '</tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>URL:</b></td>';
			echo '<td><a href="'.$row["zdurl"].'" target="_blank">'.$row["zdurl"].'</a></td>';
		echo '</tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td width="200" align="right" height="30px"><b>Повтор каждые '; if($row["zdre"]>0) {echo $row["zdre"];}else{echo "XX";} echo ' ч.:</b></td>';
			if($row["zdre"]>0) {
				echo '<td>&nbsp;ДА</td>';
			}else{
				echo '<td>&nbsp;НЕТ</td>';
			}
		echo '</tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Механизм проверки:</b></td>';
			if($row["zdcheck"]==1) {
				echo '<td>&nbsp;Ручной режим</td>';
			}else{
				echo '<td>&nbsp;Автоматический режим</td>';
			}
		echo '</tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td width="200" align="right" height="30px"><b>Стоимость выполнения:</b></td>';
			echo '<td>&nbsp;'.number_format($row["zdprice"],2,".","").' руб.</td>';
		echo '</tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Таргетинг по странам:</b></td>';
			echo '<td>&nbsp;';
			if($row["country_targ"]==1)
				echo 'Только Россия';
			elseif($row["country_targ"]==2)
				echo 'Только Украина';
			else{
				echo 'Любые страны';
			}
			echo '</td>';
		echo '</tr>';
	echo '</table>';
	echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Статистика по заданию</th></tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td align="center" width="50%">';
				echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse;">';
					echo '<tr><td align="right" width="50%"><b>Выполнено и оплачено:</b></td><td align="left"><b style="color:#54b948;">'.$row["goods"].'</b></td></tr>';
					echo '<tr><td align="right" width="50%"><b>Отказано в оплате:</b></td><td align="left"><b style="color:#c24c2c;">'.$row["bads"].'</b></td></tr>';
					echo '<tr><td align="right" width="50%"><b>Непроверенных заявок:</b></td><td align="left"><b style="color:#AAAAAA;">'.$row["wait"].'</b></td></tr>';
				echo '</table>';
			echo '</td>';
			echo '<td align="center" width="50%">';
				echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse;">';
					echo '<tr bgcolor="#1E90FF" align="center" height="25px"><th align="center"></th><th align="center">просмотров</th><th align="center">кликов</th><th align="center">CTR</th></tr>';
					echo '<tr><td align="left">Сегодня:</td><td align="center">'.$views_s.'</td><td align="center">'.$clicks_s.'</td><td align="center">'.round($ctr_s, 2).'%</td></tr>';
					echo '<tr><td align="left">Вчера:</td><td align="center">'.$views_v.'</td><td align="center">'.$clicks_v.'</td><td align="center">'.round($ctr_v, 2).'%</td></tr>';
					echo '<tr><td align="left">Всего:</td><td align="center">'.$row["views"].'</td><td align="center">'.$row["clicks"].'</td><td align="center">'.round($ctr, 2).'%</td></tr>';
				echo '</table>';
			echo '</td>';
		echo '</tr>';
	echo '</table>';
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
}

?>