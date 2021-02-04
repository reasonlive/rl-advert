<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}
echo '<h3 class="sp" style="margin-top:0; padding-top:0;"><b>История операций</b></h1>';

$WHERE_TAB = false; $WHERE_GET = false; $SEARCH = false;
if(isset($_POST["search"])) {
	$SEARCH = (isset($_POST["search"]) && limpiar(trim($_POST["search"]))!=false) ? limpiar(trim($_POST["search"])) : false;

	if($SEARCH!="") {
		$WHERE_TAB = "WHERE `user`='$SEARCH'";
		$WHERE_GET = "&search=$SEARCH";
	}else{
		$WHERE_TAB = false;
		$WHERE_GET = false;
	}
}
if(isset($_GET["search"])) {
	$SEARCH = (isset($_GET["search"]) && limpiar(trim($_GET["search"]))!=false) ? limpiar(trim($_GET["search"])) : false;

	if($SEARCH != false) {
		$WHERE_TAB = "WHERE `user`='$SEARCH'";
		$WHERE_GET = "&search=$SEARCH";
	}else{
		$WHERE_TAB = false;
		$WHERE_GET = false;
	}
}

require("navigator/navigator.php");
$PERPAGE = 30;
$sql_p = $mysqli->query("SELECT `id` FROM `tb_history` $WHERE_TAB");
$count = $sql_p->num_rows;
$pages_count = ceil($count / $PERPAGE);
$page = (isset($_GET["page"]) && preg_match("|^[0-9\-]{1,11}$|", trim($_GET["page"]))) ? intval(trim($_GET["page"])) : "1";
if ($page > $pages_count | $page<0) $page = $pages_count;
$START_POS = ($page - 1) * $PERPAGE;
if($START_POS<0) $START_POS = 0;

$sql = $mysqli->query("SELECT * FROM `tb_history` $WHERE_TAB ORDER BY `id` DESC LIMIT $START_POS, $PERPAGE");
$all_hist = $sql->num_rows;

echo '<table style="margin:0; padding:0; margin-bottom:0px; width:auto;">';
echo '<tr>';
	echo '<td valign="top" style="width:250px; white-space:nowrap;">';
		echo 'Записе всего: <b>'.$count.'</b><br>Показано записей на странице: <b>'.$all_hist.'</b> из <b>'.$count.'</b>';
	echo '</td>';
	echo '<td valign="top" style="width:350px;">';
		echo '<form method="post" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'" id="newform">';
		echo '<table style="margin:0; padding:0;">';
		echo '<tr align="center">';
			echo '<td nowrap="nowrap" width="110" align="center"><b>Поиск по логину:</b></th>';
			echo '<td nowrap="nowrap" align="center"><input type="text" class="ok" name="search" value="'.$SEARCH.'" style="text-align:center;"></td>';
			echo '<td nowrap="nowrap" width="100" align="center"><input type="submit" value="Поиск" class="sub-green"></td>';
		echo '</table>';
		echo '</form>';
	echo '</td>';
	if(isset($WHERE_TAB) && $WHERE_TAB != false) {
		echo '<td>';
			echo '<form method="get" action="">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="submit" value="Сбросить поиск" class="sub-blue160" style="float:none;">';
			echo '</form>';
		echo '</td>';
	}
echo '</tr>';
echo '</table>';

if($count>$PERPAGE) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $PERPAGE, 10, '&page=', "?op=$op$WHERE_GET");
echo '<table class="tables" style="margin:1px auto;">';
echo '<thead><tr align="center">';
	echo '<th>#</th>';
	echo '<th>Логин</th>';
	echo '<th>Дата</th>';
	echo '<th>Сумма</th>';
	echo '<th>Операция</th>';
	echo '<th>Состояние</th>';
echo '</tr></thead>';

if($all_hist>0) {
	while ($row = $sql->fetch_array()) {
		echo '<tr align="center">';
			echo '<td height="17">'.$row["id"].'</td>';
			echo '<td><b>'.$row["user"].'</b></td>';
			echo '<td><b>'.$row["date"].'</b></td>';
			echo '<td><b>'.($row["amount"]>0 ? number_format($row["amount"],2,".","'") : "-").'</b></td>';
			echo '<td>'.((strtolower($row["method"])=="webmoney" | strtolower($row["method"])=="yandexmoney" | strtolower($row["method"])=="perfectmoney" | strtolower($row["method"])=="payeer" | strtolower($row["method"])=="qiwi" | strtolower($row["method"])=="mobile" | strtolower($row["method"])=="sberbank" | strtolower($row["method"])=="paypal" | strtolower($row["method"])=="advcash") ? "Выплата на ".(strtolower($row["method"])!="mobile" ? "платежную систему" : false)." <b>".str_ireplace("Mobile", "Мобильный телефон", str_ireplace("SberBank", "СберБанк", $row["method"]))."</b>" : $row["method"]).'</td>';
			echo '<td>';
				if($row["tipo"]==0 && $row["status_pay"]==0 && $row["status"]==false) {
					echo "Ожидает обработки";
				}elseif($row["status"]!=false){
					echo $row["status"];
				}elseif($row["status_pay"]==1){
					echo "Выплачено";
				}elseif($row["status_pay"]=="2") {
					echo 'Возвращено на баланс';
				}
			echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<tr align="center"><td colspan="6">Записей не найдено!</td></tr>';
}
echo '</table>';
if($count>$PERPAGE) universal_link_bar($count, $page, $_SERVER['PHP_SELF'], $PERPAGE, 10, '&page=', "?op=$op$WHERE_GET");

?>