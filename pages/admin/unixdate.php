<?

// Unix date converter. By Alessandro Pellegrini (alessandro.pellegrini@tin.it)
//
// That's an easy-to-use tool that will let you to convert a Unix Time Date to a normal date
// and vice versa.
//
// Just run it and fill in the forms!
// Enjoy!
//
//
// ## Do not edit anything below this line! ##

function date_to_timestamp ($date)
{
$split_date = split ('-', $date);
$timestamp = mktime ($split_date[3], $split_date[4], $split_date[5], $split_date[1], $split_date[2], $split_date[0]);
return $timestamp;
}

function timestamp_to_date ($timestamp)
{
$date = date ("d/m/Y - H:i:s" , $timestamp);
return $date;
}

function form_dati ()
{
?>
<table>
<tr>
<td width="50%">&nbsp;
</td>
<td width="50%">
<form method="post" action="unixdate.php">
Convert form Normal to Unix <input checked type="radio" name="modo" value="unix"><br>
<input type="text" class="color" name="giorno" size="10" maxlength="2"> &nbsp; Day (dd)<br>
<input type="text" class="color" name="mese" size="10" maxlength="2"> &nbsp; Month (mm)<br>
<input type="text" class="color" name="anno" size="10" maxlength="4"> &nbsp; Year (yyyy)<br>
<input type="text" class="color" name="ora" size="10" maxlength="2"> &nbsp; Hour (24)<br>
<input type="text" class="color" name="min" size="10" maxlength="2"> &nbsp; Minutes<br>
<input type="text" class="color" name="sec" size="10" maxlength="2"> &nbsp; Seconds<br><br>
Convert from Unix to Normal <input type="radio" name="modo" value="normale"><br>
<input type="text" class="color" name="unixmode" size="10"> &nbsp; Unix Date<br><br>
<center><input type="submit" class="color" value="Submit"></center>
</form>
</td>
</tr>
</table>
<br><br>
<?
}
?>
<html>
<head>
<title>Unix date Converter</title>
<meta name="author" content="Alessandro Pellegrini alessandro.pellegrini@tin.it">
</head>
<style>
input.color  {
		background-color: #99CCFF;
		font-family: arial;
		font-size: 11px;
		color: #000000;
		border: 1 solid #000000;
		height: 18px;		
}
</style>
<body text="black" bgcolor="white">
<center><h1>Unix Date Converter by <a href="mailto:alessandro.pellegrini@tin.it">Alessandro Pellegrini</a></h1></center><p>&nbsp;</p>
<?

if ($modo == "")
{
form_dati();
}
if ($modo == "unix")
{
form_dati();
$date = $anno."-".$mese."-".$giorno."-".$ora."-".$min."-".$sec;
$timestamp = date_to_timestamp($date);
echo "<center><b>Unix date: $timestamp</b></center>";
$modo = "";
}
if ($modo == "normale")
{
form_dati();
$date = timestamp_to_date($unixmode);
echo "<center><b>Normal date: $date</b> (dd/mm/yyyy - hh:mm) </center>";
$modo = "";
}
?>