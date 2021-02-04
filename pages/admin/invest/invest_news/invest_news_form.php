<?php
if (!DEFINED("LoadForm")) {exit ('<span class="msg-error">Ошибка загрузки формы редактирования</span>');}
if (!isset($row)) {exit ('<span class="msg-error">Ошибка! ROW</span>');}

echo '<div id="newform">';
echo '<table class="tables">';
echo '<thead><tr>';
	echo '<th class="top" width="180">Параметр</a>';
	echo '<th class="top">Значение</a>';
echo '</thead></tr>';
echo '<tbody>';
	echo '<tr>';
		echo '<td align="left"><b>Заголовок новости</b></td>';
		echo '<td align="left"><input type="text" id="title" value="'.(trim($row["title"])!=false ? $row["title"] : false).'" maxlength="60" class="ok" autocomplete="off" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="3"><b>Текст новости &darr;</b></td>';
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
			echo '<br>';
			echo '<div style="display: block; clear:both; padding-top:4px">';
				echo '<textarea id="description" class="ok" style="height:300px; width:99.2%;" onKeyDown="$(this).attr(\'class\', \'ok\');">'.(trim($row["description"])!=false ? $row["description"] : false).'</textarea>';
			echo '</div>';
		echo '</td>';
	echo '</tr>';
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div align="center"><span onClick="SaveNews('.$row["id"].',\'Save\');" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';
echo '<div id="info-msg-news" style="display:none;"></div>';

?>