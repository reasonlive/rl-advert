<?
include('checkcookie.php');
include('../cat.php');
include('../mrsfunc.php');

?>
<link rel="stylesheet" href="/jscss/jquery.cleditor.css" />
<script src="/jscss/jquery.min.js"></script>
<script src="/jscss/jquery.cleditor.js"></script>

<script type="text/javascript">
$(document).ready(function () {
$("#descr").cleditor()[0].focus();
});
</script>
<?

$user=$_SESSION["username"];
$mnres=$mysqli->query("select price from tb_config where item='kolmin'");
$mnpv=$mnres->fetch_array();
$kolmin=$mnpv["price"];
$mnres=$mysqli->query("select price from tb_config where item='prcmin'");
$mnpv=$mnres->fetch_array();
$prcmin=$mnpv["price"];
/*
$kolmin="5";
$prcmin="0.02";
*/

if(isset($_POST["action"]) && $_POST["action"]=="add")
{
$cat=$_POST["cattask"];
$title=$_POST["title"];
$descr=$_POST["descr"];

$descr=str_replace("font-family: Tahoma, Arial, sans-serif; font-size: 12px;","",$descr);

$amount=$_POST["amount"];
$starturl=$_POST["starturl"];
$quest=$_POST["quest"];
$kolvo=$_POST["kolvo"];
$hltask=$_POST["hltask"];
$vip=$_POST["vip"];
$prav=$_POST["prav"];
$repeat=$_POST["repeat"];
$author=$user;
$data=time();

$balance=$amount*$kolvo;

$mysqli->query("insert into tb_task (author,cat,title,descr,cost,kolvo,starturl,quest,prav,data,hltask,vip,balance,amount,`repeat`,`vipend`) values 
('$author','$cattask','$title','$descr','$amount','$kolvo','$starturl','$quest','$prav','$data','$hltask','$vip','$balance','$balance','$repeat','9999999999')");

echo "<br/><span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Задание добавлено</span><br/>&nbsp;<br/>";
}
?>
<script type="text/javascript">
function autochange(frm)
{
if (frm.cattask.value>0 && frm.cattask.value<50) {
document.getElementById("prav").style.display='none';
document.getElementById("hand").style.display='block';
}
if (frm.cattask.value>50 && frm.cattask.value<100) {
document.getElementById("prav").style.display='block';
document.getElementById("hand").style.display='none';
}
}
</script>

<h3 class="sp" style="margin-top:0; padding-top:0; width:600px"><b>Добавление заданий</b></h3>

<form action='' method='post' name='mainform' id='mainform'>
<table>
<tr>
<tr><th width="300">Категория</th>
<td>
<select name="cattask" id="cattask" onchange="autochange(this.form)">
<option value="0">Выберите из списка</option>
<optgroup label="С автоподтверждением">
<?
for ($i=51;$i<100;$i++) {
if ($catname[$i]!="") {
echo '<option value="'.$i.'"';
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
echo '>'.$catname[$i].'</option>';
}
}
?>
</select>
</td>
</tr>
<tr><th>Название</th><td><input type=text value="" name="title" id="title" size="50" maxlength="50"></td></tr>
<tr><th>Описание</th><td><div style='width:396px;'><textarea id='descr' name='descr'></textarea></div></td></tr>
<tr><th>Ссылка для начала выполнения</th><td><input type=text value="" name="starturl" size="50" maxlength="150"></td></tr>
<tr><th>Информация для подтверждения</th><td><input type=text value="" name="quest" id="quest" size="50" maxlength="1000"></td></tr>
<tr><th style='height:19px;'>Правильный ответ</th><td align='left'><input type="text" name="prav" id="prav" size="50" maxlength="150" value="" style='display:none;'/></td></tr>
<tr>
<th>Повторное выполнение</th>
<td align='left'><select name="repeat" id='hand' style='display:none;'>
<option value="0">Запретить</option>
<option value="1">Через 1 час</option>
<option value="2">Через 2 часа</option>
<option value="3">Через 3 часа</option>
<option value="6">Через 6 часов</option>
<option value="12">Через 12 часов</option>
<option value="24">Через 24 часа</option>
<option value="48">Через 48 часов</option>
</select>
</td>
</tr>
<tr><th>Выделение цветом</th><td>
<select name="hltask">
<option style="color: #000000;" value="0">Не выделять</option>
<option style="color: #CC0000;" value="1">Красным</option>
<option style="color: #0000CC;" value="2">Синим</option>
<option style="color: #009900;" value="3">Зеленым</option>
</select>
</td></tr>
<tr><th>VIP-статус</th><td><select name="vip"><option value="0">Нет</option><option value="1">Да</option></select></td></tr>
<tr><th>Оплата</th><td><input type=text value="<?=$prcmin?>" name="amount" size="10" maxlength="10"></td></tr>
<tr><th>Число выполнений</th><td><input type=text value="<?=$kolmin?>" name="kolvo" size="10" maxlength="10"></td></tr>
<tr><th colspan='2'>
<input type="button" value="Добавить задание" onclick='doReplace();submit();'>
<input type="hidden" value="add" name="action">
</th></tr>
</table>
</form>

<script type="text/javascript">
<!--
function doReplace() {
mainform.descr.value=mainform.descr.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.quest.value=mainform.quest.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.prav.value=mainform.prav.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
mainform.title.value=mainform.title.value.replace(/\</g, "[").replace(/\>/g, "]").replace(/\"/g, "&Prime;").replace(/\'/g, "&prime;");
}
//-->
</script>
