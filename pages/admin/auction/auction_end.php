<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список завершенных аукционов:</b></h3>';

function universal_link_bar($page, $count, $pages_count, $show_link) {
	if ($pages_count == 1) return false;
		$sperator = ' &nbsp;';
		//$style = 'style="color: #808000; text-decoration: none;"';
		$style = 'style="font-weight: bold;"';
		$begin = $page - intval($show_link / 2);
		unset($show_dots);

		if ($pages_count <= $show_link + 1) $show_dots = 'no';
		if (($begin > 2) && !isset($show_dots) && ($pages_count - $show_link > 2)) {
			echo '<a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page=1> 1 </a> ';
		}

		for ($j = 0; $j < $page; $j++) {
			if (($begin + $show_link - $j > $pages_count) && ($pages_count-$show_link + $j > 0)) {
				$page_link = $pages_count - $show_link + $j;

				if (!isset($show_dots) && ($pages_count-$show_link > 1)) {
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($page_link - 1).'><b>...</b></a> ';
					$show_dots = "no";
				}

				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$page_link.'>'.$page_link.'</a> '.$sperator;
			} else continue;
		}

		for ($j = 0; $j <= $show_link; $j++) {
			$i = $begin + $j;

			if ($i < 1) {
				$show_link++;
				continue;
			}

			if (!isset($show_dots) && $begin > 1) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($i-1).'><b>...</b></a> ';
				$show_dots = "no";
			}

			if ($i > $pages_count) break;
			if ($i == $page) {
				echo ' <a '.$style.' ><b style="color:#FF0000; text-decoration:underline;">'.$i.'</b></a> ';
			}else{
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$i.'>'.$i.'</a> ';
			}

			if (($i != $pages_count) && ($j != $show_link)) echo $sperator;
			if (($j == $show_link) && ($i < $pages_count)) {
				echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($i+1).'><b>...</b></a> ';
		}
	}

	if ($begin + $show_link + 1 < $pages_count) {
		echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$pages_count.'> '.$pages_count.' </a>';
	}
	return true;
}

$perpage = 50;
if (empty($_GET['page']) || ($_GET['page'] <= 0)) {
	$page = 1;
}else{
	$page = (int)$_GET['page'];
}

$count = $mysqli->query("SELECT `id` FROM `tb_auction` WHERE `status`='0'")->num_rows;
$pages_count = ceil($count / $perpage);

if ($page > $pages_count) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos=0;


$sql = $mysqli->query("SELECT * FROM `tb_auction` WHERE `status`='0' ORDER BY `date_end` DESC LIMIT  $start_pos,$perpage");
if($sql->num_rows>0) {
	if($count>$perpage) {echo '<b>Страницы:</b>&nbsp;'; universal_link_bar($page, $count, $pages_count, 10);}
	echo '<table align="center" border="1" width="100%" cellspacing="3" cellpadding="3" style="line-height : 1.5em; border-collapse: collapse; border: 1px solid #1E90FF;">';
	echo '<tr bgcolor="#1E90FF" align="center">';
		echo '<th align="center">#</th>';
		echo '<th align="center">Продавец</th>';
		echo '<th align="center">Победитель</th>';
		echo '<th align="center">Кол-во ставок</th>';
		echo '<th align="center">Размер ставки</th>';
		echo '<th align="center">Доход продавца</th>';
		echo '<th align="center">Доход системы</th>';
		echo '<th align="center">Завершен</th>';
	echo '</tr>';

	while ($row = $sql->fetch_array()) {
		echo '<tr bgcolor="#ADD8E6" align="center">';
			echo '<td align="center">'.$row["id"].'</td>';
			echo '<td align="center">'.$row["username"].'</td>';
			echo '<td align="center">'.$row["lider"].'</td>';
			echo '<td align="center">'.$row["kolstv"].'</td>';
			echo '<td align="center">'.$row["stavka"].'</td>';
			echo '<td align="center">'.round($row["summa"],4).'</td>';
			echo '<td align="center">'.round($row["proc"],4).'</td>';
			echo '<td align="center">'.DATE("d.m.Yг. H:i", $row["timer_end"]).'</td>';
		echo '</tr>';
	}
	echo '</table>';
	if($count>$perpage) {echo '<b>Страницы:</b>&nbsp;'; universal_link_bar($page, $count, $pages_count, 10);}
}else{
	echo '<div align="center" style="color:#FF0000; font-weight:bold;">Аукционы еще не проводились!</div>';
}
?>