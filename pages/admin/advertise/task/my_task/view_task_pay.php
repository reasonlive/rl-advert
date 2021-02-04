<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

echo '<b>Оплачиваемые задания:</b><br /><br />';
echo '<b>Статистика по выполнению задания #'.$rid.'</b><br />';

function universal_link_bar($page, $count, $pages_count, $show_link) {
	if ($pages_count == 1) return false;
		$sperator = ' &nbsp;';
		$style = 'style="font-weight: bold;"';
		$begin = $page - intval($show_link / 2);
		unset($show_dots);

		if ($pages_count <= $show_link + 1) $show_dots = 'no';
		if (($begin > 2) && !isset($show_dots) && ($pages_count - $show_link > 2)) {
			echo '<a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s=1> 1 </a> ';
		}

		for ($j = 0; $j < $page; $j++) {
			if (($begin + $show_link - $j > $pages_count) && ($pages_count-$show_link + $j > 0)) {
				$page_link = $pages_count - $show_link + $j;

				if (!isset($show_dots) && ($pages_count-$show_link > 1)) {
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($page_link - 1).'><b>...</b></a> ';
					$show_dots = "no";
				}

				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$page_link.'>'.$page_link.'</a> '.$sperator;
			} else continue;
		}

		for ($j = 0; $j <= $show_link; $j++) {
			$i = $begin + $j;

			if ($i < 1) { $show_link++; continue;}

			if (!isset($show_dots) && $begin > 1) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($i-1).'><b>...</b></a> ';
				$show_dots = "no";
			}

			if ($i > $pages_count) break;
			if ($i == $page) {
				echo ' <a '.$style.' ><b style="color:#FF0000; text-decoration:underline;">'.$i.'</b></a> ';
			}else{
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$i.'>'.$i.'</a> ';
			}

			if (($i != $pages_count) && ($j != $show_link)) echo $sperator;
			if (($j == $show_link) && ($i < $pages_count)) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.($i+1).'><b>...</b></a> ';
		}
	}

	if ($begin + $show_link + 1 < $pages_count) {
		echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.limpiar($_GET["page"]).'&rid='.intval($_GET["rid"]).'&s='.$pages_count.'> '.$pages_count.' </a>';
	}
	return true;
}

$perpage = 30;
if (empty($_GET["s"]) || ($_GET["s"] <= 0)) {
	$page = 1;
}else{
	$page = intval($_GET["s"]);
}

$count = $mysqli->query("SELECT `id` FROM `tb_ads_task_pay` WHERE `status`!='' AND `rek_name`='$username' AND `ident`='$rid'")->num_rows;
$pages_count = ceil($count / $perpage);

if ($page > $pages_count) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos=0;

if($count>$perpage){
	echo '<div align="center"><b>Страницы:</b> '; universal_link_bar($page, $count, $pages_count, 8); echo '</div>';
}
echo '<br><table align="center" border="1" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #000;">';
	echo '<tr bgcolor="#7EC0EE">';
	echo '<th align="center" rowspan="2" style="border: 1px solid #000;">id пользователя</th>';
	echo '<th align="center" rowspan="2" colspan="2" style="border: 1px solid #000;">Статус</th>';
	echo '<th align="center" rowspan="2" style="border: 1px solid #000;">IP</th>';
	echo '<th align="center" rowspan="2" style="border: 1px solid #000;">Информация для проверки</th>';
	echo '<th align="center" colspan="2" style="border: 1px solid #000;">Время</th>';
	echo '<th align="center" rowspan="2" style="border: 1px solid #000;">Вознаграждение</th>';
	echo '<th align="center" rowspan="2" style="border: 1px solid #000;">Оценка пользователя</th>';
	echo '</tr>';
	echo '<tr bgcolor="#7EC0EE">';
	echo '<th align="center" style="border: 1px solid #000;">начала</th>';
	echo '<th align="center" style="border: 1px solid #000;">окончания</th>';
	echo '</tr>';

$sql = $mysqli->query("SELECT * FROM `tb_ads_task_pay` WHERE `status`!='' AND `rek_name`='$username' AND `ident`='$rid' ORDER BY `id` DESC LIMIT $start_pos,$perpage");
if($sql->num_rows>0) {
	while ($row = $sql->fetch_array()) {

		$sql_u = $mysqli->query("SELECT `id`,`username` FROM `tb_users` WHERE `username`='".$row["user_name"]."'");
		if($sql_u->num_rows>0) {
			$row_u = $sql_u->fetch_array();
			$info_user = '#'.$row_u["id"].'<br>'.$row_u["username"].'';
		}else{
			$info_user = '#'.$row["user_id"].'<br>'.$row["user_name"].'<br><span style="color:#FF0000;">Пользователь удален</span>';
		}

		echo '<tr>';
		echo '<td align="center">'.$info_user.'</td>';

		if($row["status"]=="bad")
			echo '<td align="center" style="border-right:0;"><img src="../../img/no.png" border="0" alt="" align="middle" title="задание выполнено, но рекламодатель не подтвердил выполнение" /></td border="0" style="border-left:0;"><td align="center">задание выполнено, но рекламодатель не подтвердил выполнение<br><b>'.$row["why"].'</b></td>';
		elseif($row["status"]=="good")
			echo '<td align="center" style="border-right:0;"><img src="../../img/yes.png" border="0" alt="" align="middle" title="задание выполнено и рекламодатель подтвердил выполнение" /></td border="0" style="border-left:0;"><td align="center">задание выполнено и рекламодатель подтвердил выполнение</td>';
		elseif($row["status"]=="wait")
			echo '<td align="center" style="border-right:0;"><img src="../../img/help.png" border="0" alt="" align="middle" title="задание выполнено и ожидает проверки рекламодателем" /></td border="0" style="border-left:0;"><td align="center">задание выполнено и ожидает проверки рекламодателем</td>';
		else{
			echo '<td align="center" style="border-right:0;"></td><td align="center"></td>';
		}

		echo '<td align="center">'.$row["ip"].'</td>';
		echo '<td align="center">'.$row["ctext"].'</td>';
		echo '<td align="center">'.DATE("d.m.Yг. H:i", $row["date_start"]).'</td>';
		echo '<td align="center">'.DATE("d.m.Yг. H:i", $row["date_end"]).'</td>';

		if($row["status"]=="bad")
			echo '<td align="center">-</td>';
		elseif($row["status"]=="good")
			echo '<td align="center">'.$row["pay"].' руб.</td>';
		elseif($row["status"]=="wait")
			echo '<td align="center">'.$row["pay"].' руб.</td>';
		else{
			echo '<td align="center">-</td>';
		}

		if($row["ocenka"] > 0) {
			echo '<td align="center">'.$row["ocenka"].'</td>';
		}else{
			echo '<td align="center">не голосовал</td>';
		}

		echo '</tr>';
	}
}else{
	echo '<tr><td align="center" colspan="9">Задание еще не выполняли!</td></tr>';
}
echo '</table><br>';

if($count>$perpage){
	echo '<div align="center"><b>Страницы:</b> '; universal_link_bar($page, $count, $pages_count, 8); echo '</div>';
}

?>