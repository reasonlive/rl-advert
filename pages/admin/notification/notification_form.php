<?php
if (!DEFINED("LoadForm")) {exit ('<span class="msg-error">Ошибка загрузки формы редактирования</span>');}
if (!isset($row)) {exit ('<span class="msg-error">Ошибка! ROW</span>');}

echo '<div id="newform">';
echo '<table class="tables">';
echo '<thead><tr>';
	echo '<th class="top" width="220">Параметр</a>';
	echo '<th class="top">Значение</a>';
echo '</thead></tr>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Заголовок уведомления</b></td>';
		echo '<td align="left"><input type="text" id="title" name="title" value="'.$row["title"].'" maxlength="60" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL сайта</b></td>';
		echo '<td align="left"><input type="text" id="url" name="url" value="'.$row["url"].'" maxlength="300" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="left"><b>URL баннера</b></td>';
		echo '<td align="left"><input type="text" id="url_img" name="url_img" value="'.$row["url_img"].'" maxlength="300" class="ok" ></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="3"><b>Описание уведомления &darr;</b></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="2">';
			echo '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
			echo '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
			echo '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
			echo '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'description\'); return false;">ST</span>';
			echo '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'description\'); return false;"></span>';
			echo '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
			echo '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'description\'); return false;">IMG</span>';
			echo '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:3px;">Осталось символов: 2000</span>';
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:150px; width:99.2%;" onkeyup="DescChange(\'1\', this, \'2000\');">'.$row["description"].'</textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div align="center"><span onClick="SaveNot('.$row["id"].',\'Save\');" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';
echo '<div id="info-msg-admin"></div>';

?>

<script language="JavaScript">
	DescChange(1, description, 2000);
</script>