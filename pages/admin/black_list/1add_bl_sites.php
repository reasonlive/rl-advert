<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Добавление сайтов в черный спиcок</b></h3>';

function universal_link_bar($page, $count, $pages_count, $show_link) {
	if ($pages_count == 1) return false;
		$sperator = ' &nbsp;';
		$style1 = 'style="font-weight: bold; border:1px solid #B5B5B5; padding:1px 5px 1px 5px; background:#FFF"';
		$style2 = 'style="font-weight: bold; border:1px solid #B5B5B5; padding:1px 5px 1px 5px; background:#CCC"';
		$begin = $page - intval($show_link / 2);
		unset($show_dots);

		if ($pages_count <= $show_link + 1) $show_dots = 'no';
		if (($begin > 2) && !isset($show_dots) && ($pages_count - $show_link > 2)) {
			echo '<a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page=1> 1 </a> ';
		}

		for ($j = 0; $j < $page; $j++) {
			if (($begin + $show_link - $j > $pages_count) && ($pages_count-$show_link + $j > 0)) {
				$page_link = $pages_count - $show_link + $j;

				if (!isset($show_dots) && ($pages_count-$show_link > 1)) {
					echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($page_link - 1).'><b>...</b></a> ';
					$show_dots = "no";
				}

				echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$page_link.'>'.$page_link.'</a> '.$sperator;
			} else continue;
		}

		for ($j = 0; $j <= $show_link; $j++) {
			$i = $begin + $j;

			if ($i < 1) {
				$show_link++;
				continue;
			}

			if (!isset($show_dots) && $begin > 1) {
				echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($i-1).'><b>...</b></a> ';
				$show_dots = "no";
			}

			if ($i > $pages_count) break;
			if ($i == $page) {
				echo ' <a '.$style2.' >'.$i.'</a> ';
			}else{
				echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$i.'>'.$i.'</a> ';
			}

			if (($i != $pages_count) && ($j != $show_link)) echo $sperator;
			if (($j == $show_link) && ($i < $pages_count)) {
				echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.($i+1).'><b>...</b></a> ';
		}
	}

	if ($begin + $show_link + 1 < $pages_count) {
		echo ' <a '.$style1.' href='.$_SERVER['PHP_SELF'].'?op='.limpiar($_GET["op"]).'&page='.$pages_count.'> '.$pages_count.' </a>';
	}
	return true;
}

$perpage = 20;
if (empty($_GET['page']) || ($_GET['page'] <= 0)) {
	$page = 1;
}else{
	$page = (int)$_GET['page'];
}

$count = mysql_numrows(mysql_query("SELECT `id` FROM `tb_black_sites`"));
$pages_count = ceil($count / $perpage);

if ($page > $pages_count) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos=0;


$option = isset($_GET["option"]) ? limpiar(trim($_GET["option"])) : false;

if($option=="addbl" && count($_POST)>0) {
	$url = (isset($_POST["url"])) ? limpiar(trim($_POST["url"])) : false;
	$cause = (isset($_POST["cause"])) ? htmlspecialchars(trim($_POST["cause"])) : false;
	$domen = @getHost($url);

	$sql_bl = mysql_query("SELECT `domen` FROM `tb_black_sites` WHERE `domen`='$domen'");
	$row = mysql_fetch_array($sql_bl);

	if($url==false || $cause==false){
		echo '<span class="msg-error">Ошибка заполнены не все поля, либо заполнены не корректно!!!</span>';
	}elseif($domen==false) {
		echo '<span class="msg-error">Ошибка! Невозможно определить имя домена(субдомена), проверьте корректность URL сайта!</span>';
	}elseif(mysql_num_rows($sql_bl)>0){
		echo '<span class="msg-error">Ошибка! Домен(субдомен) <a href="http://'.$row["domen"].'/" target="_blank">'.$row["domen"].'</a>  уже есть в черном списке!</span>';
	}else{
		mysql_query("INSERT INTO `tb_black_sites` (`url`,`domen`,`cause`,`date`,`ip`) VALUES('$url','$domen','$cause','".time()."','".@$_SERVER['REMOTE_ADDR']."')") or die(mysql_error());

		echo '<span class="msg-ok">Домен(субдомен) <a href="http://'.$domen.'/" target="_blank">'.$domen.'</a> успешно добавлен в BlackList.</span>';
	}
}else{
	$url = false;
	$cause = false;
}

echo '<form action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'&option=addbl" method="POST" id="newform">';
	echo '<table>';
		echo '<tr><th>Параметр</th><th>Значение</th></tr>';
		echo '<tr><td width="200"><b>URL сайта:</b></td><td><input type="text" class="ok" name="url" value="'.$url.'"></td></tr>';
		echo '<tr><td width="200"><b>Причина блокировки:</b></tв><td><input type="text" class="ok" name="cause" value="'.$cause.'"></td></tr>';
		echo '<tr><td colspan="2" align="center"><input type="submit" value="Добавить сайт в BlackList" class="sub-blue160"></td></tr>';
	echo '</table>';
echo '</form>';
echo '<br><br>';



if($option=="edit") {
	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;
	$status_edit="NO";

	$sql_e = mysql_query("SELECT * FROM `tb_black_sites` WHERE `id`='$id'");
	if(mysql_num_rows($sql_e)>0) {
		$row_e = mysql_fetch_array($sql_e);

		if(count($_POST)>0) {
			$url = (isset($_POST["url"])) ? limpiar(trim($_POST["url"])) : false;
			$cause = (isset($_POST["cause"])) ? htmlspecialchars(trim($_POST["cause"])) : false;
			$domen = @getHost($url);

			$sql_bl = mysql_query("SELECT `domen` FROM `tb_black_sites` WHERE `domen`='$domen'");
			$row = mysql_fetch_array($sql_bl);

			if($url==false || $cause==false){
				echo '<span class="msg-error">Ошибка заполнены не все поля, либо заполнены не корректно!!!</span>';
			}elseif($domen==false) {
				echo '<span class="msg-error">Ошибка! Невозможно определить имя домена(субдомена), проверьте корректность URL сайта!</span>';
			}else{
				$status_edit="OK";
				mysql_query("UPDATE `tb_black_sites` SET `url`='$url',`domen`='$domen',`cause`='$cause',`date`='".time()."',`ip`='".@$_SERVER['REMOTE_ADDR']."'  WHERE `id`='$id'") or die(mysql_error());
				echo '<span class="msg-ok">Домен(субдомен) <a href="http://'.$domen.'/" target="_blank">'.$domen.'</a> успешно от редактирован.</span>';

				echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
				echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
			}
		}else{
			$url = $row_e["url"];
			$cause = $row_e["cause"];
		}

		if($status_edit=="NO") {
			echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Редактирование сайта из черного списка(BlackList)</b></h3>';
			echo '<form action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page='.$page.'&option=edit&id='.$id.'" method="POST" id="newform">';
				echo '<table>';
					echo '<tr><th>Параметр</th><th>Значение</th></tr>';
					echo '<tr><td width="200"><b>ID:</b></td><td><input type="hidden" name="id" value="'.$row_e["id"].'"></td></tr>';
					echo '<tr><td width="200"><b>URL сайта:</b></td><td><input type="text" class="ok" name="url" value="'.$url.'"></td></tr>';
					echo '<tr><td width="200"><b>Причина блокировки:</b></tв><td><input type="text" class="ok" name="cause" value="'.$cause.'"></td></tr>';
					echo '<tr><td colspan="2" align="center"><input type="submit" value="Сохранить изменения" class="sub-blue160"></td></tr>';
				echo '</table>';
			echo '</form>';
			echo '<br><br>';
		}
	}else{
		echo '<span class="msg-error">Ошибка! Домен(субдомен) с #'.$id.' не найден в черном списке!</span>';

		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}


if($option=="dell") {
	$id = (isset($_GET["id"]) && preg_match("|^[\d]{1,11}$|", trim($_GET["id"]))) ? intval(limpiar(trim($_GET["id"]))) : false;

	$sql_del = mysql_query("SELECT `id` FROM `tb_black_sites` WHERE `id`='$id'");
	if(mysql_num_rows($sql_del)>0){
		mysql_query("DELETE FROM `tb_black_sites` WHERE `id`='$id'") or die(mysql_error());

		echo '<span class="msg-ok">Домен(субдомен) успешно удален из BlackList.</span>';

		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}else{
		echo '<span class="msg-error">Ошибка домен(субдомен) уже удален из BlackList!</span>';

		echo '<script type="text/javascript"> setTimeout(\'location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'")\', 1000); </script>';
		echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'"></noscript>';
	}
}


echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>Список заблокированных сайтов</b></h3>';
echo '<table>';
	echo '<tr>';
		echo '<th>ID</th>';
		echo '<th>URL</th>';
		echo '<th>Домен(субдомен)</th>';
		echo '<th>Причина</th>';
		echo '<th>Дата</th>';
		echo '<th></th>';
		echo '<th></th>';
	echo '</tr>';



$sql = mysql_query("SELECT * FROM `tb_black_sites` ORDER BY `id` DESC LIMIT  $start_pos,$perpage");
if(mysql_num_rows($sql)>0) {
	while ($row=mysql_fetch_array($sql)) {
		echo '<tr>';
		echo '<td>'.$row["id"].'</td>';
		echo '<td>'.$row["url"].'</td>';
		echo '<td>'.$row["domen"].'</td>';
		echo '<td>'.$row["cause"].'</td>';
		echo '<td>'.DATE("d.m.Y H:i", $row["date"]).'</td>';

		echo '<td><form method="get" action="">';
			echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
			echo '<input type="hidden" name="page" value="'.$page.'">';
			echo '<input type="hidden" name="option" value="edit">';
			echo '<input type="hidden" name="id" value="'.$row["id"].'">';
			echo '<input type="submit" value="Редактировать" class="sub-blue">';
		echo '</form></td>';
		echo '<td><form method="get" action="">';
			echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
			echo '<input type="hidden" name="page" value="'.$page.'">';
			echo '<input type="hidden" name="option" value="dell">';
			echo '<input type="hidden" name="id" value="'.$row["id"].'">';
			echo '<input type="submit" value="Удалить" class="sub-red">';
		echo '</form></td>';
		echo '</tr>';
	}
}else{
	echo '<tr><td align="center" colspan="7">В черном списке сайтов нет!</td></tr>';
}
echo '</table>';
if($count>$perpage) {
	echo '<div align="center">';
		universal_link_bar($page, $count, $pages_count, 10);
	echo '</div>';
}

?>