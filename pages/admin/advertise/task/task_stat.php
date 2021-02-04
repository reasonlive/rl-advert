<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

$WHERE_ADD = false;
$WHERE_ADD_to_get = false;

require("navigator/navigator.php");
$perpage = 25;
$count = $mysqli->query("SELECT `id` FROM `tb_ads_task_pay` WHERE `status`!='' AND  `type`='task' $WHERE_ADD")->num_rows;
$pages_count = ceil($count / $perpage);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$start_pos = ($page - 1) * $perpage;
if($start_pos<0) $start_pos = 0;

$sql = $mysqli->query("SELECT * FROM `tb_ads_task_pay`  WHERE `status`!='' AND  `type`='task' ORDER BY `id` DESC LIMIT $start_pos,$perpage");
$all_pay = $sql->num_rows;
echo '<table class="adv-cab" style="margin:0; padding:0; margin-bottom:1px;"><tr>';
echo '<td align="left" width="230" valign="middle" style="border-right:solid 1px #DDDDDD;">';
	if($WHERE_ADD=="") {
		echo 'Всего: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_pay.'</b> из <b>'.$count.'</b>';
	}else{
	 	echo 'Найдено: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_pay.'</b> из <b>'.$count.'</b>';
	}
echo '</td>';
echo '</table>';
if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}
echo '<table class="tables">';
	echo '<thead><tr>';
		
		//if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}
		//echo '</td>';
	echo '</tr>';
	echo '<tr bgcolor="#42aaf">';
	echo '<th align="center" class="top">id задания</th>';
	echo '<th align="center" class="top">Статус</th>';
	echo '<th align="center" class="top">IP исполнителя<br/>Логин</th>';
	echo '<th align="center" class="top">Кол-во ошибок</th>';
	echo '<th align="center" class="top">начала<br>окончания</th>';
	echo '<th align="center" class="top">Отчет</th>';
	//echo '<th align="center" class="top">Оценка</th>';
	echo '</tr></thead>';

if($all_pay>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
		echo '<td align="center">'.$row["ident"].'<br/>Рекламодатель <b>'.$row["rek_name"].'</b></td>';

		if($row["status"]=="bad" && $row["kol_otv"]<3){
			echo '<td align="center"><img src="/img/no.png" border="0" alt="" align="middle" title="задание выполнено, но рекламодатель не подтвердил выполнение" /><br/>задание выполнено, но рекламодатель не подтвердил выполнение<br><b>'.$row["why"].'</b></td>';
		}elseif($row["status"]=="bad" && $row["kol_otv"]>=3){
			echo '<td align="center"><img src="/img/no.png" border="0" alt="" align="middle" title="Задание не выполнено, закончился лимит попыток ответа на вопрос" /><br/>Задание не выполнено, закончился лимит попыток ответа на вопрос</td>';
		}elseif($row["status"]=="good"){
			echo '<td align="center"><img src="/img/yes.png" border="0" alt="" align="middle" title="задание выполнено и рекламодатель подтвердил выполнение" /></td>';
		}elseif($row["status"]=="wait"){
			echo '<td align="center"><img src="/img/help.png" border="0" alt="" align="middle" title="задание выполнено и ожидает проверки рекламодателем" /></td>';
		}elseif($row["status"]=="dorab"){
			echo '<td align="center"><img src="/img/no.png" border="0" alt="" align="middle" title="Задание направленно на доработку" /><br/>задание отправлено на доработку<br><b>'.$row["why"].'</td>';
		//}elseif($row["status"]=="bad"){
			//echo '<td align="center"><img src="/img/no.png" border="0" alt="" align="middle" title="Задание не выполнено, рекламодатель не подтвердил выполнение" /><br/>Задание не выполнено, рекламодатель не подтвердил выполнение</td>';
		}else{
			echo '<td align="center"></td>';
		}

		echo '<td align="center">'.$row["ip"].'<br/><b>'.$row["user_name"].'</b></td>';
		echo '<td align="center">'.$row["kol_otv"].'</td>';
		echo '<td align="center">'.DATE("d.m.Yг. H:i", $row["date_start"]).'</br>';
		echo ''.DATE("d.m.Yг. H:i", $row["date_end"]).'</td>';

		if($row["status"]=="bad")
			echo '<td align="center">'.$row["ctext"].'</td>';
		elseif($row["status"]=="good")
			echo '<td align="center">'.$row["ctext"].'</td>';
		elseif($row["status"]=="wait")
			echo '<td align="center">'.$row["ctext"].'</td>';
		elseif($row["status"]=="dorab")
			echo '<td align="center">'.$row["why"].'</td>';
		//if($row["status"]=="bad")
			//echo '<td align="center">'.$row["why"].'</td>';
		else{
			//echo '<td align="center">-</td>';
		}

		echo '</tr>';
	}
	echo '<tr>';
		//echo '<td colspan="9" style="border: 1px solid #1E90FF;">Найдено записей <b>'.$count.'</b>, показано <b>'.$all_pay.'</b><br>';
		//if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}
		echo '</td>';
	echo '</tr>';
}else{
	echo '<tr><td align="center" colspan="9">отчеты не найдены!</td></tr>';
}
echo '</table><br>';
if($count>$perpage) {universal_link_bar($count, $page, $_SERVER["PHP_SELF"], $perpage, 10, '&page=', "?op=$op$WHERE_ADD_to_get");}
?>