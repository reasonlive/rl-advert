<?
include('checkcookie.php');
include('../cat.php');

if(isset($_POST["action"]) && $_POST["action"]=="edit")
{
$id=$_POST["id"];
$mysqli->query("update tb_taskstats set status='10',redo=redo+1 where id='$id'");
echo "<span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Изменения сохранены</span><br/>&nbsp;<br/>";
}

?>
<h3 class="sp" style="margin-top:0; padding-top:0; width:600px"><b>Выполнение заданий</b></h3>
<?

$res=$mysqli->query("select count(*) as kolvo1 from tb_taskstats");
$res=$res->fetch_array();
$alltask=$res["kolvo1"];

if($alltask=='0')
{
	exit();
}

echo "Всего выполнений: ".$alltask."<br/>&nbsp;<br/>";

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

echo "<table width='100%' align='center' style='line-height:14px;'>
<tr>
	<th>№</th>
	<th>Дата</th>
	<th>Исполнитель</th>
	<th>Ответ</th>
	<th>Статус</th>
	<th>Комментарий</th>
	<th>Повторов</th>
	<th>Выполнений</th>
	<th></th>
</tr>";

$res=$mysqli->query("select * from tb_taskstats order by data desc limit $p1,$p2");
while($row=$res->fetch_array())
{
if($row["status"]=='0' || $row["status"]=='20'){$status='В ожидании';}
if($row["status"]=='1' || $row["status"]=='21'){$status='Подтверждено';}
if($row["status"]=='2' || $row["status"]=='22'){$status='Отклонено';}
if($row["status"]=='4' || $row["status"]=='24'){$status='Автоподтверждено';}
if($row["status"]=='10'){$status='Повторить';}

	echo "<tr><td align=center>".$row["idtask"]."</td><td align=center>".date("d.m.Y H:i:s",$row["data"])."</td><td align=center>".
	$row["user"]."</td><td align=center>".$row["quest"]."</td><td align=center>".$status."</td><td align=center>".$row["comment"].
	"</td><td align=center>".$row["redo"]."</td><td align=center>".$row["done"]."</td><td align=center>";
	if ($row["status"]=='2' || $row["status"]=='22') {
	echo "<form action='' method='post'>
	<input type='hidden' value='".$row["id"]."' name='id'>
	<input type='hidden' value='edit' name='action'>
	<input type='submit' value='Повтор'>
	</form>";
	}
	echo "</td></tr>";
}
echo "</table><br/>";
