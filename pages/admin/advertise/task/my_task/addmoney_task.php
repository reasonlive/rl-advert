<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

if($rid > 0) {
	echo '<b>Пополнение баланса оплачиваемого задания:</b><br><br>';
	$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$rid' AND `username`='$username'");
	if($sql->num_rows>0) {
		$row = $sql->fetch_array();

		$id = $row["id"];
		$zdname = $row["zdname"];
		$zdtext = $row["zdtext"];
		$zdurl = $row["zdurl"];
		$zdtype = $row["zdtype"];
		$zdre = $row["zdre"];
		$zdcheck = $row["zdcheck"];
		$zdprice = $row["zdprice"];
		$zdcountry = $row["country_targ"];

		if(count($_POST) > 0) {
			$add_plan = (isset($_POST["add_plan"])) ? abs(intval(trim($_POST["add_plan"]))) : false;

			if($add_plan>0) {
				$mysqli->query("UPDATE `tb_ads_task` SET `status`='pay', `plan`=`plan`+'$add_plan', `totals`=`totals`+'$add_plan' WHERE `id`='$id' AND `username`='$username'") or die($mysqli->error);
			}

			echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view");</script>';
			echo '<META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&page=task_view">';

			exit();
		}

		echo '<div id="form">';
		echo '<form id="form" action="'.$_SERVER["PHP_SELF"].'?op='.limpiar($_GET["op"]).'&amp;page='.limpiar($_GET["page"]).'&amp;rid='.$id.'" method="POST">';
		echo '<table align="center" border="0" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #1E90FF;">';
			echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Описание оплачиваемого задания</th></tr>';
			echo '<tr bgcolor="#ADD8E6">';
				echo '<td width="200" align="right" height="30px"><b>Название:</b></td>';
				echo '<td>&nbsp;'.$zdname.'</td>';
			echo '</tr>';
			echo '<tr bgcolor="#AFEEEE">';
				echo '<td width="200" align="right" height="30px"><b>Тип задания:</b></td>';
				if($zdtype==1)
					echo '<td>&nbsp;Клики</td>';
				elseif($zdtype==2)
					echo '<td>&nbsp;Регистрация без активности</td>';
				elseif($zdtype==3)
					echo '<td>&nbsp;Регистрация с активностью</td>';
				elseif($zdtype==4)
					echo '<td>&nbsp;Постинг в форум</td>';
				elseif($zdtype==5)
					echo '<td>&nbsp;Постинг в блоги</td>';
				elseif($zdtype==6)
					echo '<td>&nbsp;Голосование</td>';
				elseif($zdtype==7)
					echo '<td>&nbsp;Загрузка файлов</td>';
				elseif($zdtype==8)
					echo '<td>&nbsp;Прочее</td>';
				else{
					echo '<td></td>';
				}
			echo '</tr>';
			echo '<tr bgcolor="#ADD8E6">';
				echo '<td width="200" align="right" height="30px"><b>Ссылка на сайт:</b></td>';
				echo '<td>&nbsp;<a href="'.$zdurl.'" target="_blank">'.$zdurl.'</a></td>';
			echo '</tr>';
			echo '<tr bgcolor="#AFEEEE">';
				if($zdre==0) {echo '<td width="200" align="right" height="30px"><b>Повтор каждые XX ч. :</b></td>';}else{echo '<td width="200" align="right" height="30px"><b>Повтор каждые '.$zdre.' ч. :</b></td>';}
				if($zdre==0) {echo '<td>&nbsp;НЕТ</td>';}else{echo '<td>&nbsp;ДА</td>';}
			echo '</tr>';
			echo '<tr bgcolor="#ADD8E6">';
				echo '<td width="200" align="right" height="30px"><b>Механизм проверки:</b></td>';
				if($zdcheck==1) {
					echo '<td>&nbsp;Ручной режим</td>';
				}else{
					echo '<td>&nbsp;Автоматический режим</td>';
				}
			echo '</tr>';
			echo '<tr bgcolor="#AFEEEE">';
				echo '<td width="200" align="right" height="30px"><b>Стоимость выполнения:</b></td>';
				echo '<td>&nbsp;'.$zdprice.'&nbsp;руб.</td>';
			echo '</tr>';
			echo '<tr bgcolor="#ADD8E6">';
				echo '<td width="200" align="right" height="30px"><b>Таргетинг по странам:</b></td>';
				if($zdcountry==1)
					echo '<td>&nbsp;Только Россия</td>';
				elseif($zdcountry==2)
					echo '<td>&nbsp;Только Украина</td>';
				else{
					echo '<td>&nbsp;Любые страны</td>';
				}
			echo '</tr>';
		echo '<tr bgcolor="#1E90FF" align="center" height="30px"><th align="center" colspan="2">Пополнение баланса оплачиваемого задания</th></tr>';
		echo '<tr bgcolor="#ADD8E6">';
			echo '<td width="200" align="right" height="30px"><b>Кол-во заданий:</b></td>';
			echo '<td>&nbsp;<input type="text" size="10" style="text-align:right;" name="add_plan" id="add_plan" maxlength="10" value="10"></td>';
		echo '</tr>';
		echo '<tr bgcolor="#AFEEEE">';
			echo '<td align="center" colspan="2"><input type="submit" class="submit" value="&nbsp;&nbsp;Пополнить баланс задания&nbsp;&nbsp;"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		echo '</div>';

	}else{
		echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
		exit();
	}
}else{
	echo '<fieldset class="errorp">Ошибка! У Вас нет такого задания!</fieldset>';
	exit();
}
?>