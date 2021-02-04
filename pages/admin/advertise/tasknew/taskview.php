<?
include('checkcookie.php');
include('../cat.php');

$searchstr="";
$search="1";
$catf="";

if (isset($_POST["search"])/* && !isset($_POST["pg"])*/) {
$search=$_POST["search"];
$searchstr=trim($_POST["searchstr"]);
if ($searchstr!="" && $search>'0' && $search<'7') {
if ($search=='1') {$catf.=" where `id`='".$searchstr."'"; /*$na="";*/}
if ($search=='2') {
$fuq=$mysqli->query("select `username` from `tb_users` where `id`='".$searchstr."'");
if ($fuq->num_rows>0) {
$fuf=$fuq->fetch_array();
$searchstr2=$fuf["username"];
$catf.=" where `author`='".$searchstr2."'";
}
}
if ($search=='3') {$catf.=" where `author`='".$searchstr."'"; }
if ($search=='4') {$catf.=" where `title` like '%".$searchstr."%'"; }
if ($search=='5') {$catf.=" where `descr` like '%".$searchstr."%'"; }
if ($search=='6') {$catf.=" where `starturl` like '%".$searchstr."%'"; }
}
}

if((isset($_GET["action"])) && ($_GET["action"]=='viewabuse'))
{
$id=intval($_GET["id"]);
 echo "<h3>Просмотр жалоб на задание $id</h3>";
if(isset($_POST["delete"]))
{
$delete=$_POST["delete"];
$mysqli->query("DELETE FROM sfb_taskabuses WHERE id='$delete'");
$mysqli->query("UPDATE tb_task SET abuses=abuses-1 WHERE id='$id'");
echo "<br/><span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Жалоба удалена</span><br/>&nbsp;<br/>";

echo "<center><font color='#007700'>Жалобой меньше :)</font></center>";
}
$res=$mysqli->query("SELECT * FROM sfb_taskabuses WHERE ident='$id'");
if($res->num_rows == '0')
{
echo "<br/><span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Жалоб на задание $id не подавалось</span><br/>&nbsp;<br/>";

}else{
?>
<br/>
<table width=100%>
<tr>
<th align='center'>Логин</th>
<th align='center'>Дата подачи</th>
<th align='center'>Текст жалобы</th>
<th align='center'>&nbsp;</th>
</tr>
<?
while($row=mysql_fetch_array($res))
{
?>
<tr>
<td align='center'><?=$row["username"]; ?></td>
<td align='center'><?=date("d.m.Y H:i", $row["data"]); ?></td>
<td align='center'><?=$row["text"]; ?></td>
<td align='center'>
<form action="" method="POST">
<input type="hidden" name="delete" value="<?=$row["id"]; ?>">
<input type="submit" value="Удалить жалобу">
</form></td></tr>
<?
}
echo "</table><br/>";
}

}

?>
<h3 class="sp" style="margin-top:0; padding-top:0; width:600px"><b>Просмотр заданий</b></h3>
<?

if(isset($_POST["action"]) && $_POST["action"]=="edit")
{
	$id=$_POST["id"];
	
	$res=$mysqli->query("select * from tb_task where id='$id'");
	$res=$res->fetch_array();

	?>
	<form action='' method='post' name='mainform' id='mainform'>
	<table>
	<tr><td>ID </td><td><?=$id?></td></tr>
	<tr><td>Рекламодатель </td><td><?=$res["author"]?></td></tr>
	<tr><td>Категория</td>
	<td>
	<select name="cattask" id="cattask">
	<optgroup label="С автоподтверждением">
	<?
	for ($i=51;$i<100;$i++) {
 	if ($catname[$i]!="") {
 	echo '<option value="'.$i.'"';
 	if ($res["cat"]==$i) {echo " selected='selected'";}
 	echo '>'.$catname[$i].'</option>';
 	}
	}
	?>
	</optgroup>
	<optgroup label="С ручным подтверждением">
	<?
	for ($i=1;$i<50;$i++) {
 	if ($catname[$i]!="") {
 	echo '<option value="'.$i.'"';
 	if ($res["cat"]==$i) {echo " selected='selected'";}
 	echo '>'.$catname[$i].'</option>';
 	}
	}
	?>
	</select>
	</td>
	</tr>
	<tr><td>Название </td><td><input type=text value="<?=$res["title"]?>" name="title" id="title" size=50 maxlength=50></td></tr>
	<tr><td>Описание </td><td>
	
<link rel="stylesheet" href="/jscss/jquery.cleditor.css" />
<script src="/jscss/jquery.min.js"></script>
<script src="/jscss/jquery.cleditor.js"></script>

<?
$descr=nl2br(str_replace('[','<',str_replace(']','>',$res["descr"])));
$descr=str_replace("&prime;","'",str_replace("&Prime;",'"',$descr));
$descr=str_replace(array("\r", "\n"), array(),$descr);
?>

<div style='width:396px;'><textarea id='descr' name='descr'><?=$descr?></textarea></div>

<script type="text/javascript">
<!--
$(document).ready(function () {
$("#descr").cleditor()[0].focus();
});

function doReplace() {
mainform.descr.value=mainform.descr.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.quest.value=mainform.quest.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.prav.value=mainform.prav.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.title.value=mainform.title.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
}
//-->
</script>
	
	<br/></td></tr>
	<tr><td>Ссылка для начала выполнения </td><td><input type=text value="<?=$res["starturl"]?>" name="starturl" size=50 maxlength=150></td></tr>
	<tr><td>Информация для подтверждения </td><td><input type=text value="<?=$res["quest"]?>" name="quest" id="quest" size=50 maxlength=1000></td></tr>
	<tr><td>Правильный ответ </td><td><input type=text value="<?=$res["prav"]?>" name="prav" id="prav" size=50 maxlength=1000></td></tr>

<tr>
<td>Повторное выполнение </td>
<td align='left'><select name="repeat">
<option value="0"<? if ($res["repeat"]=='0') {echo " selected='selected'";} ?>>Запретить</option>
<option value="1"<? if ($res["repeat"]=='1') {echo " selected='selected'";} ?>>Через 1 час</option>
<option value="2"<? if ($res["repeat"]=='2') {echo " selected='selected'";} ?>>Через 2 часа</option>
<option value="3"<? if ($res["repeat"]=='3') {echo " selected='selected'";} ?>>Через 3 часа</option>
<option value="6"<? if ($res["repeat"]=='6') {echo " selected='selected'";} ?>>Через 6 часов</option>
<option value="12"<? if ($res["repeat"]=='12') {echo " selected='selected'";} ?>>Через 12 часов</option>
<option value="24"<? if ($res["repeat"]=='24') {echo " selected='selected'";} ?>>Через 24 часа</option>
<option value="48"<? if ($res["repeat"]=='48') {echo " selected='selected'";} ?>>Через 48 часов</option>
</select>
</td>
</tr>
	<tr><td>Осталось выполнений </td><td><?=$res["kolvo"]?></td></tr>
	<tr><td>Стоимость </td><td><input type=text value="<?=$res["cost"]?>" name="amount" size=10 maxlength=10></td></tr>
	<tr><td>Баланс задания </td><td><?=$res["balance"]?></td></tr>
	<tr><td>Пополнить баланс задания на сумму </td><td><input type=text value="0" name="balplus" size=10 maxlength=10></td></tr>

<tr>	
<td>Статус задания </td>
<td>
<?
if ($res["status"]=='0') { echo "<b><font color='#007700'>Активно</font></b>"; $stopnm='Остановить';}
if ($res["status"]=='1') { echo "<b><font color='#990000'>Остановлено</font></b>"; $stopnm='Активировать';}
if ($res["status"]=='2') { echo "<b><font color='#990000'>Остановлено админом</font></b>"; $stopnm='Активировать';}
?>
&nbsp;&nbsp;<input type="button" value=" <? echo $stopnm; ?> " class="submit confirmbut submbut" onclick='statform.submit();'/>
</td>
</tr>

	<tr><td><input type='button' value="Удалить" onclick='delform.submit();'>
	</td><td><input type='button' value="Сохранить" onclick='doReplace();submit();' >
	<input type=hidden value="<?=$id?>" name="id">
	<input type=hidden value="editok" name="action">
	<input type=hidden value="<?=$res["balance"]?>" name="balance">
	</td></tr>
	</table>
	</form>
	<form action='' method='post' name='delform'>
	<input type=hidden value="<?=$id?>" name="id">
	<input type=hidden value="deletetask" name="action">
	</form>
<form action='' method='post' name='statform'>
<input type=hidden value="<?=$id?>" name="id">
<input type=hidden value="<?=$res["status"]?>" name="tskstatus">
<input type=hidden value="stattask" name="action">
</form>
	<br/>
	<font color='#007700'><b>Подтверждено: <?=$res["good"]?></b></font><br/>
	<font color='#990000'><b>Отклонено: <?=$res["bad"]?></b></font><br/>
	<font color='#666666'><b>Ожидают: <?=$res["wait"]?></b></font>
	<br/>&nbsp;<br/>
	<?
}

if(isset($_POST["action"]) && $_POST["action"]=="editok")
{
	$id=$_POST["id"];
	$title=strip_tags($_POST["title"]);
	$amount=strip_tags($_POST["amount"]);
	$descr=strip_tags($_POST["descr"]);
	
	$descr=str_replace("font-family: Tahoma, Arial, sans-serif; font-size: 12px;","",$descr);

	$starturl=strip_tags($_POST["starturl"]);
	$quest=strip_tags($_POST["quest"]);
	$balance=strip_tags($_POST["balance"]);
	$cattask=strip_tags($_POST["cattask"]);
	$balplus=strip_tags($_POST["balplus"]);
	$repeat=strip_tags($_POST["repeat"]);

	$res=$mysqli->query("select money_rb from tb_users where username='$user'");
	$res=$res->fetch_array();
	$money=$res["money_rb"];

	$balance=$balance+$balplus;
	
	$kolvo=floor($balance/$amount);
	if($kolvo<1)
	{
	echo "<font color='#990000'><b>Ошибка! На балансе задания недостаточно средств</b></font>";
	}else{
	$mysqli->query("update tb_task set cat='$cattask',title='$title',cost='$amount',descr='$descr',starturl='$starturl',quest='$quest',balance='$balance',kolvo='$kolvo',`repeat`='$repeat' where id='$id'");

echo "<span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Изменения сохранены</span><br/>&nbsp;<br/>";

	}
}

if(isset($_POST["action"]) && $_POST["action"]=="stattask")
{
$id=$_POST["id"];
$tskstatus=$_POST["tskstatus"];

if ($tskstatus=='0' || $tskstatus=='20') {

/* Автоподтверждение при остановке задания */
$stop_sql=$mysqli->query("SELECT * FROM tb_taskstats WHERE idtask='$id' && (status='0' || status='20')");
if($stop_sql->num_rows>0)
{
while($stop_row=$stop_sql->fetch_array())
{
$stop_idv=$stop_row["id"];
$stop_login=$stop_row["user"];
$stop_res=$mysqli->query("select * from tb_task where id='$id'");
$stop_res=$stop_res->fetch_array();
if(($stop_res["balance"]<$stop_res["cost"]) or ($stop_res["kolvo"]<1))
{
}else{
$stop_cost=$stop_res["cost"];
$stop_balance=$stop_res["balance"]-$stop_cost;
$stop_kolvo=$stop_res["kolvo"]-1;
$stop_good=$stop_res["good"]+1;
$stop_wait=$stop_res["wait"]-1;
if ($stop_wait<0) {$stop_wait=0;}
$zdre=$stop_row["repeat"];
$rek_name=$stop_row["author"];

$mysqli->query("update tb_task set balance='$stop_balance',kolvo='$stop_kolvo',good='$stop_good',wait='$stop_wait' where id='$id'");

$stop_res=$mysqli->query("select * from tb_users where username='$stop_login'");
$stop_res=$stop_res->fetch_array();
$wmiduser=$stop_res["wmid"];

$zdprice=$stop_cost;
$user_name=$stop_login;
$rid=$id;

include("/taskrez.php");

$mysqli->query("update tb_taskstats set amount='$stop_cost',status='4' where id='$stop_idv'");
}
}
}
/* END Автоподтверждение при остановке задания */


$tskstatus='2';
} else { $tskstatus='0'; }

//if ($tskstatus=='2') {$tskstatus='0';} else {$tskstatus='2';}
$mysqli->query("update tb_task set status='$tskstatus' where id='$id'");
echo "<span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Изменения сохранены</span><br/>&nbsp;<br/>";
}

if(isset($_POST["action"]) && $_POST["action"]=="deletetask")
{
	$id=$_POST["id"];
	$res=$mysqli->query("select kolvo,author,balance from tb_task where id='$id'");
	$res=$res->fetch_array();
	$dauthor=$res["author"];
	$dbalance=$res["balance"];
	$mysqli->query("update tb_users set money=money+'$dbalance', minrekl=minrekl-'$dbalance', fsmall=fsmall-'$dbalance' where username='$dauthor'");
	$mysqli->query("delete from tb_task where id='$id'");
	echo "<font color='#007700'><b>Задание удалено</b></font>";
}



$res=$mysqli->query("select price from tb_config where item='taskcomm'");
$res=$res->fetch_array();
$taskcomm=$res["price"];

$res=$mysqli->query("select count(*) as kolvo1 from tb_task");
$res=$res->fetch_array();
$alltask=$res["kolvo1"];

if($alltask=='0')
{
	exit();
}

echo "Всего заданий: ".$alltask."<br/>&nbsp;<br/>";

$pages=$alltask/30;
$pages1=floor($pages);

if($pages>$pages1)
{
	$pages=$pages1+1;
}

if((isset($_POST["page"])) && ($_POST["page"]!=""))
{
	$page=$_POST["page"];
}else{
	$page='1';
}

$p1=30*($page-1);
$p2=30;

echo "<b>Текущая страница $page из $pages</b><br/>&nbsp;<br/>
	<form action='' method=post>
	<select name='page'>";
for($i=1;$i<=$pages;$i++)
{
	echo "<option value='$i'>$i</option>";
}

echo "</select>
	<input type=submit value='Перейти'></form><br/><br/>";

?>

<center>
<br/>
<form action="" method="POST">
<table class="c_green" style="text-align:center;" cellpadding="0">
<tr>
<th colspan="3">Поиск оплачиваемых заданий</th>
</tr>
<tr>
<td style="padding:2px 4px 8px 4px;">
<select class="c_green border_green" name="search" style="width:180px;">
<option value="1"<? if ($search=='1') {echo " selected='selected'";} ?>>№ задания</option>
<option value="2"<? if ($search=='2') {echo " selected='selected'";} ?>>ID рекламодателя</option>
<option value="3"<? if ($search=='3') {echo " selected='selected'";} ?>>Логин рекламодателя</option>
<option value="4"<? if ($search=='4') {echo " selected='selected'";} ?>>Название задания</option>
<option value="5"<? if ($search=='5') {echo " selected='selected'";} ?>>Описание задания</option>
<option value="6"<? if ($search=='6') {echo " selected='selected'";} ?>>Ссылка (URL)</option>
</select>
</td>
<td style="padding:2px 4px 8px 4px;">
<input class="c_green border_green" style='width:200px;text-align:center;' type="text" name="searchstr" value="<?=$searchstr?>" placeholder=""/>
</td>
<td style="padding:0px 4px 10px 4px; vertical-align:bottom;">
<input type="submit" class="push_but_small greenbutton" style="font-size:12px!important;padding:1px 10px;cursor:pointer;margin:0;" value="&#128269; Искать" title="Искать"/>
</td>
</tr>
</table>
</form>
<br/>

<font color='#990000'>Сортировать по: <a href="index.php?op=taskview&sort=id">дате&nbsp;создания</a> | <a href="index.php?op=taskview&sort=amount">стоимости</a> | <a href="index.php?op=taskview&sort=abuses">кол-ву жалоб</a></font><br/>&nbsp;<br/>
<?
$sorting='id';
if(isset($_GET["sort"])) {
if($_GET["sort"]=='id') echo "Задания отсортированы по дате создания. Новые задания находятся в верхней части списка.<br/>&nbsp;<br/>";
if($_GET["sort"]=='amount') echo "Задания отсортированы по стоимости. Чем выше стоимость - тем выше в списке задание.<br/>&nbsp;<br/>";
if($_GET["sort"]=='abuses') echo "Задания отсортированы по кол-ву жалоб. Чем больше было жалоб на задание,тем выше оно в списке.<br/>&nbsp;<br/>";
}
?>
<table width='100%' align='center' style='line-height:14px;'>
<tr>
	<th>№</th>
	<th>Задание</th>
	<th>Стоимость</th>
	<th>Осталось</th>
	<th>Подтверждено</th>
	<th>Отклонено</th>
	<th>Ожидают</th>
	<th>Жалоб</th>
	<th>Статус</th>
	<th></th>
</tr>
<?
function HourPad ($hh) {
if ($hh=='1') {$hp=" час";}
if ($hh=='2' || $hh=='3' || $hh=='24') {$hp=" часа";}
if ($hh=='6' || $hh=='12' || $hh=='48') {$hp=" часов";}
return $hp;
}
if(isset($_GET["sort"])) {
if($_GET["sort"]=='id' or $_GET["sort"]=='') $sorting="id";
if($_GET["sort"]=='amount') $sorting="cost";
if($_GET["sort"]=='abuses') $sorting="abuses";
}
$res=$mysqli->query("select * from tb_task ".$catf." order by $sorting desc limit $p1,$p2");
while($row=$res->fetch_array())
{

if ($row["status"]=='0') { $tstatmes="<b><font color='#007700'>Активно</font></b>";}
if ($row["status"]=='1') { $tstatmes="<b><font color='#990000'>Остановлено</font></b>";}
if ($row["status"]=='2') { $tstatmes="<b><font color='#990000'>Остановлено админом</font></b>";}

if ($row["hltask"]!="#FFFFFF") {$hltask=$row["hltask"];} else {$hltask="#000000";}
	echo "<tr><td align=center>".$row["id"];
	if ($row["vip"]=='1') {echo "<br/><b><font color='#990000'>VIP</font></b>";}
	if ($row["repeat"]!='0') {echo "<br/><b><font color='#000099'>".$row["repeat"].HourPad($row["repeat"])."</font></b>";}
	echo "</td><td align=left><b>Категория: </b>".$catname[$row["cat"]]."<br/><b>Название: </b><span style='color:".$hltask.";'>".$row["title"]."</span><br/>
	<b>Рекламодатель: </b>".$row["author"]."<br/><b>Дата добавления: </b>".date("d.m.Y в H:i",$row["data"])."</td><td align=center>".
	$row["cost"]."</td><td align=center>".$row["kolvo"]."</td><td align=center><font color='#007700'><b>".$row["good"]."</b></font></td>
	<td align=center><font color='#990000'><b>".$row["bad"]."</b></font></td><td align=center><font color=#666666><b>".$row["wait"]."</b></font></td>
<td align='center'><a href=\"index.php?op=taskview&id=".$row["id"]."&action=viewabuse\" title=\"Нажмите для перехода на страницу с жалобами\">".$row["abuses"]."</a></td>
<td align='center' title='Включить / Отключить' style='cursor:pointer;' onclick='statform".$row["id"].".submit();'>
<form action='' method='post' name='statform".$row["id"]."'>
<input type='hidden' value=".$row["id"]." name='id'>
<input type='hidden' value=".$row["status"]." name='tskstatus'>
<input type='hidden' value='stattask' name='action'>
</form>
".$tstatmes."</td>

<td align=center>";

	?>
	<form action="" method="post">
	<input type="hidden" value="<?=$row["id"]?>" name="id">
	<input type="hidden" value="edit" name="action">
	<input type="submit" value="Редактировать">
	</form>
	<?

	echo "</td></tr>";
}
echo "</table><br/>";
