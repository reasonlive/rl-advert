<?php
$pagetitle="Контакты";


$db->query("SELECT * FROM tb_site WHERE id='1'");
$row_site = $db->FetchAssoc();
$site_wmid = $row_site["sitewmid"];
$site_wmr = $row_site["sitewmr"];
$site_email = $row_site["siteemail"];
$site_isq = $row_site["siteisq"];
$site_telefon = $row_site["sitetelefon"];
$site_fio = $row_site["site_fio"];
$startdate = intval((time() - strtotime($row_site["startdate"])));
$start_date = $row_site["startdate"];
$start_time = intval((time() - strtotime($start_date)));
$site_whatsApp = $row_site["sitewhatsApp"];;
$site_telegramm = $row_site["sitetelegramm"];

$site_skype = $row_site["siteskype"];
$site_viber = $row_site["siteviber"];;
$status_admin = 'admin';
?>

<div class='flex-center'>
<table class="tables">


<?php

if($site_fio!=false) echo '<thead><tr><th align="center" colspan="2" class="top">Администратор: '.$site_fio.'</th></tr></thead>';
if($site_email!=false) echo '<tr><td align="right" width="35%">E-mail <img src="/images/message.gif" width="16" align="absmiddle" border="0" height="16" alt="" /></td><td align="left"><a href="mailto:'.$site_email.'">'.str_replace("@"," @ ",$site_email).'</a></td></tr>';
if($site_telefon!=false) echo '<tr><td align="right" width="35%">Сотовый <img src="/img/phone.png" width="16" height="16"  align="absmiddle" border="0"  alt="phone" /></td><td align="left">'.$site_telefon.'</td></tr>';
if($site_isq!=false) echo '<tr><td align="right" width="35%">ICQ <img src="/img/icq.png" width="16" height="16"  align="absmiddle" border="0"  alt="ICQ" /></td><td align="left">'.$site_isq.'</td></tr>'; 
if($site_whatsApp!=false) echo '<tr><td align="right" width="35%">WhatsApp <img src="/img/whatsapp.png" width="16" height="16"  align="absmiddle" border="0"  alt="WhatsApp" /></td><td align="left">'.$site_whatsApp.'</td></tr>'; 
if($site_telegramm!=false) echo '<tr><td align="right" width="35%">Telegramm <img src="/img/telegram.png"  width="16" height="16"  align="absmiddle" border="0"  alt="Telegramm" /></td><td align="left">'.$site_telegramm.'</td></tr>'; 
if($site_skype!=false) echo '<tr><td align="right" width="35%">Skype <img src="/img/skype.png" width="16" height="16"  align="absmiddle" border="0"  alt="Skype" /></td><td align="left">'.$site_skype.'</td></tr>'; 
if($site_viber!=false) echo '<tr><td align="right" width="35%">Viber <img src="/img/viber.png" width="16" height="16"  align="absmiddle" border="0"  alt="Viber" /></td><td align="left">'.$site_viber.'</td></tr>'; 

echo '<tr><td align="right" width="35%"><img src="images/message.gif" width="16" align="absmiddle" border="0" height="16" alt="" /></td><td align="left"><a href="/newmsg.php?name=admin">Написать в Тех.поддержку</a></td></tr>';

echo '<tr><td align="right" width="35%">Статус тех.поддержки</td><td align="left">'.$status_admin.'</td></tr>';
?>
</table>
</div>

<?php
$sql_cnt = $db->query("SELECT * FROM `tb_users` WHERE `user_status`='2' ORDER BY `id` ASC");
if($db->NumRows()>0) {
	while ($row_cnt = $db->FetchAssoc()) {
		echo '<table class="tables" style="margin:15px auto ;">';
		echo '<thead><tr><th align="center" colspan="2" class="top">Модератор: '.ucfirst($row_cnt["username"]).'</th></tr></thead>';
		//if($row_cnt["wmid"]!=false) echo '<tr><td align="right" width="35%">WMid<img src="img/wmid.ico" width="16" align="absmiddle" border="0" height="16" alt="" /></td><td align="left">'.$row_cnt["wmid"].'</td></tr>';
		if($row_cnt["wm_purse"]!=false) echo '<tr><td align="right" width="35%">WMR<img src="img/wmr.ico" width="16" align="absmiddle" border="0" height="16" alt="" /></td><td align="left">'.$row_cnt["wm_purse"].'</td></tr>';
		if($row_cnt["email"]!=false) echo '<tr><td align="right" width="35%">E-mail <img src="images/message.gif" width="16" align="absmiddle" border="0" height="16" alt="" /></td><td align="left"><a href="mailto:'.$site_email.'">'.str_replace("@"," @ ",$row_cnt["email"]).'</a></td></tr>';
		if($row_cnt["email"]!=false) echo '<tr><td align="center" colspan="2"><a href="/newmsg.php?name='.$row_cnt["username"].'" class="sub-blue160" style="float:none; width:160px;">Написать модератору</a></td></tr>';
		echo '</table>';
	}
}
?>