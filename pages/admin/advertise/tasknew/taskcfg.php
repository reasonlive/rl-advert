<? include('checkcookie.php'); ?>

<h3 class="sp" style="margin-top:0; padding-top:0; width:600px"><b>Настройки заданий</b></h3>
<div style='padding:10px; text-align:center;'>
<center>
<?

if (isset($_POST["taskcomm"]))
{
$taskcomm=$_POST["taskcomm"];
$kolmin=$_POST["kolmin"];
$prcmin=$_POST["prcmin"];
$taskvip=$_POST["taskvip"];
$taskhl=$_POST["taskhl"];
$taskup=$_POST["taskup"];
$viptaskcount=$_POST["viptaskcount"];
$viptaskprice=$_POST["viptaskprice"];
$viptaskdays=$_POST["viptaskdays"];

$mysqli->query("update tb_config set price='$taskcomm' where item='taskcomm'");
$mysqli->query("update tb_config set price='$taskhl' where item='taskhl'");
$mysqli->query("update tb_config set price='$taskvip' where item='taskvip'");
$mysqli->query("update tb_config set price='$taskup' where item='taskup'");
$mysqli->query("update tb_config set price='$kolmin' where item='kolmin'");
$mysqli->query("update tb_config set price='$prcmin' where item='prcmin'");
$mysqli->query("update tb_config set price='$viptaskcount' where item='viptaskcount'");
$mysqli->query("update tb_config set price='$viptaskprice' where item='viptaskprice'");
$mysqli->query("update tb_config set price='$viptaskdays' where item='viptaskdays'");

echo "<span style='background:#FFFFCC; border: 1px solid #009900; color:#009900; padding:5px; font-weight:bold;'>Настройки сохранены</span><br/>&nbsp;<br/>";
}

$sql="select price from tb_config where item='taskcomm'";
$res=$mysqli->query($sql);
$taskcomm=$res->fetch_object()->price;

$sql="select price from tb_config where item='taskhl'";
$res=$mysqli->query($sql);
$taskhl=$res->fetch_object()->price;

$sql="select price from tb_config where item='taskvip'";
$res=$mysqli->query($sql);
$taskvip=$res->fetch_object()->price;

$sql="select price from tb_config where item='taskup'";
$res=$mysqli->query($sql);
$taskup=$res->fetch_object()->price;

$mnres=$mysqli->query("select price from tb_config where item='kolmin'");
$mnpv=$mnres->fetch_array();
$kolmin=$mnpv["price"];

$mnres=$mysqli->query("select price from tb_config where item='prcmin'");
$mnpv=$mnres->fetch_array();
$prcmin=$mnpv["price"];

$sql="select price from tb_config where item='viptaskcount'";
$res=$mysqli->query($sql);
$viptaskcount=$res->fetch_object()->price;

$sql="select price from tb_config where item='viptaskprice'";
$res=$mysqli->query($sql);
$viptaskprice=$res->fetch_object()->price;

$sql="select price from tb_config where item='viptaskdays'";
$res=$mysqli->query($sql);
$viptaskdays=$res->fetch_object()->price;

?>

<form method="post" action="">

<table style="border:1px solid #AAAAAA;">

<tr><th width="300">Комиссия системы (%) в заданиях</th><td><input type="text" name="taskcomm" value="<?=$taskcomm ?>" size='8' maxlength='8'></td></tr>
<tr><th>Мин. кол-во выполнений в заданиях</th><td><input type="text" name="kolmin" value="<?=$kolmin ?>" size='8' maxlength='8'></td></tr>
<tr><th>Мин. цена задания</th><td><input type="text" name="prcmin" value="<?=$prcmin ?>" size='8' maxlength='8'></td></tr>
<tr style="display:none;"><th>Доплата за VIP-задания (%)</th><td><input type="text" name="taskvip" value="<?=$taskvip ?>" size='8' maxlength='8'></td></tr>
<tr><th>Выделение цветом в заданиях</th><td><input type="text" name="taskhl" value="<?=$taskhl ?>" size='8' maxlength='8'></td></tr>
<tr><th>Стоимость подъема задания в списке</th><td><input type="text" name="taskup" value="<?=$taskup ?>" size='8' maxlength='8'></td></tr>
<tr style="display:none;"><th>Максимальное кол-во VIP-заданий в боковом блоке</th><td><input type="text" name="viptaskcount" value="<?=$viptaskcount ?>" size='8' maxlength='8'></td></tr>
<tr><th>Кол-во дней VIP-статуса</th><td><input type="text" name="viptaskdays" value="<?=$viptaskdays ?>" size='8' maxlength='8'></td></tr>
<tr><th>Стоимость VIP-статуса</th><td><input type="text" name="viptaskprice" value="<?=$viptaskprice ?>" size='8' maxlength='8'></td></tr>

<tr>
<td colspan="2" style='height:16px; background:#DDDDDD; border-top:1px solid #AAAAAA;text-align:center;'><input type="submit" value="Сохранить изменения" class="button"></td></tr>
</table>
</form>
<br/>