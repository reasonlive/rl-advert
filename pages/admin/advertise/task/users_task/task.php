<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}


error_reporting (E_ALL);

?><script type="text/javascript" language="JavaScript">
var s_h = false;
var new_id = false;

function LoadInfo(id, type, op) {
	if(s_h==(id + op)) {
		s_h = false;
		$("#load-info"+id).hide();
		$("#mess-info"+id).html("");
	} else {
		if(s_h && new_id) {
			$("#load-info"+new_id).hide();
			$("#mess-info"+new_id).html("");
		}
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json',
			beforeSend: function() { $("#loading").slideToggle(); }, 

			error: function(request, status, errortext) {
				$("#loading").slideToggle();
				new_id = id; s_h = id + op;

				var error = new Array();
				error["rState"] = request.readyState!==false ? request.readyState : false;
				error["rText"]  = request.responseText!=false ? request.responseText : errortext;
				error["status"] = request.status!==false ? request.status : false;
				error["statusText"] = request.statusText!==false ? request.statusText : false;

				$("#load-info"+id).show(); 
				$("#mess-info"+id).html('<span class="msg-error">ОШИБКА AJAX! readyState:'+error["rState"]+'; responseText:'+error["rText"]+'; status:'+error["status"]+'['+error["statusText"]+']<br>STATUS: '+status+'<br>ERROR:'+errortext+'</span>');
			},

			success: function(data) { 
				$("#loading").slideToggle();

				if(op == "GoDel") {
					new_id = id; s_h = id;
				}else{
					new_id = id; s_h = id + op;
				}

				$("#load-info"+id).show();

				if (data.result == "OK") {
					if(data.message) { $("#mess-info"+id).html(data.message); }
					else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
				} else {
					if(data.message) { $("#mess-info"+id).html('<span class="msg-error">' + data.message + '</span>'); }
					else { $("#mess-info"+id).html('<span class="msg-error">Ошибка обработки данных!</span>'); }
				}
			}
		});
	}
	return false;
}

function DelAdv(id, type, op) {
	if (confirm("Удалить рекламную площадку ID:"+id+" ?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#adv_dell"+id).remove();
					$("#hide"+id).remove();
					$("#load-info"+id).remove();
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function DelClaims(id, type, op) {
	if (confirm("Удалить жалобу?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#claims-"+id).remove();
					if(data.count_claims==0) {
						$("#adv_dell"+data.ident).remove();
						$("#LoadModalClaimsSurf").modalpopup('close');
					}else{
						$("#countclaims-"+data.ident).html(data.count_claims);
					}
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function DelAllClaims(id, type, op) {
	if (confirm("Удалить все жалобы?")) {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id }, 
			dataType: 'json', 
			success: function(data) {
				if (data.result=="OK") {
					$("#adv_dell"+id).remove();
				}else{
					alert(data.message);
				}
			}
		});
	}
}

function ViewClaims(id, type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		success: function(data) {
			if (data.result=="OK" && data.message) {
				$("#LoadModalClaimsSurf").html(data.message).show();
				StartModal();
				WinHeight = Math.ceil($.trim($(window).height()));
				ModalHeight = Math.ceil($.trim($("#table-content").height()));
				if((ModalHeight+100) >= WinHeight) ModalHeight = WinHeight-100;
				$("#table-content").css("height", ModalHeight+"px");
			}else{
				alert(data.message);
			}
		}
	});
}

function StartModal() {
	$("#LoadModalClaimsSurf").modalpopup({
		closeOnEsc: true,
		closeOnOverlayClick: false,
		beforeClose: function(data, el) {
			$("#LoadModalClaimsSurf").hide();
			return true;
		}
	});
}

function PlayPause(id, type, op) {
	$.ajax({
		type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
		data: { 'op': op, 'type': type, 'id': id }, 
		dataType: 'json', 
		beforeSend: function() { $("#loading").slideToggle(); }, 
		error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
		success: function(data) { 
			$("#loading").slideToggle();
			if (data.result == "OK") { 
				$("#playpauseimg"+id).html(data.status);
				if(data.message) { alert(data.message); }
			} else { 
				if(data.message) { alert(data.message); }
				else { alert("Ошибка обработки данных!"); return false; }
			}
			return false;
		}
	});
}

function Lock(id, type, op){
	var msg_lock = $.trim($("#msg_lock").val());

	if (!msg_lock | msg_lock == false) {
		$("#msg_lock").focus().attr("class", "err");
		alert("Укажите причину блокировки!");
	} else {
		$.ajax({
			type: "POST", url: "ajax_admin/ajax_json_adv.php?rnd="+Math.random(), 
			data: { 'op': op, 'type': type, 'id': id, 'msg_lock':msg_lock }, 
			dataType: 'json', 
			beforeSend: function() { $("#loading").slideToggle(); }, 
			error: function() { $("#loading").slideToggle(); alert("Ошибка обработки данных AJAX!"); return false; }, 
			success: function(data) { 
				$("#loading").slideToggle();
				if (data.result == "OK") {
					$("#load-info"+id).hide();
					$("#mess-info"+id).html("");
					$("#playpauseimg"+id).html('<span class="adv-block" title="Рекламная площадка заблокирована" onClick="PlayPause('+id+', \'task\', \'PlayPause\', \'1\');"></span>');
					$("#lock-"+id).attr({class: "adv-unlock", title: "Просмотр информации о блокировки"});
					s_h = false; new_id = false;

					//if(data.message) { alert(data.message); }
				} else {
					if(data.message) { alert(data.message); }
					else { alert("Ошибка обработки данных!"); return false; }
				}
			}
		});
	}
}
</script><?php

if(!isset($_SESSION["userLog"]) && !isset($_SESSION["userPas"])) {
	echo '<fieldset class="errorp">Ошибка! Для доступа к этой странице необходимо авторизоваться!</fieldset>';
}else{

	$username = (isset($_SESSION["userLog"])) ? uc($_SESSION["userLog"]) : false;
	$partnerid = ( isset($_SESSION["partnerid"]) && intval(($_SESSION["partnerid"]))>0 ) ? intval(($_SESSION["partnerid"])) : "0";
	$rid = (isset($_GET["rid"])) ? intval($_GET["rid"]) : false;

	function limpiarez($mensaje){
		$mensaje = htmlspecialchars(trim($mensaje));
		$mensaje = str_replace("'","",$mensaje);
		$mensaje = str_replace(";","",$mensaje);
		$mensaje = str_replace("$","&#036;",$mensaje);
		$mensaje = str_replace("<","&#60;",$mensaje);
		$mensaje = str_replace(">","&#62;",$mensaje);
		$mensaje = str_replace("\\","",$mensaje);
		$mensaje = str_replace("&amp amp ","&amp;",$mensaje);
		$mensaje = str_replace("&amp quot ","&quot;",$mensaje);
		$mensaje = str_replace("&amp gt ","&gt;",$mensaje);
		$mensaje = str_replace("&amp lt ","&lt;",$mensaje);
		$mensaje = str_replace("\r\n","<br>",$mensaje);
		return $mensaje;
	}


	if(isset($_GET["rid"]) && isset($_GET["option"]) && intval($_GET["rid"])>0 && limpiar($_GET["option"])=="dell") {
		include('task_del.php');
	}

	if(isset($_GET["rid"]) && isset($_GET["option"]) && intval($_GET["rid"])>0 && limpiar($_GET["option"])=="edit") {
		include('task_edit.php');
	}

	if(isset($_GET["rid"]) && isset($_GET["option"]) && intval($_GET["rid"])>0 && limpiar($_GET["option"])=="view") {
		include('task_view.php');
	}


	function universal_link_bar($page, $count, $pages_count, $show_link, $sort, $sort_z, $type, $task_search, $task_name, $task_auto, $task_price) {
		$sort_link = '&sort='.$sort.'&sort_z='.$sort_z.'&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'';

		if ($pages_count == 1) return false;
			$sperator = ' &nbsp;';
			$style = 'style="font-weight: bold;"';
			$begin = $page - intval($show_link / 2);
			unset($show_dots);
			if ($pages_count <= $show_link + 1) $show_dots = 'no';
			if (($begin > 2) && !isset($show_dots) && ($pages_count - $show_link > 2)) {
				echo '<a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s=1> 1 </a> ';
			}
			for ($j = 0; $j < $page; $j++) {
				if (($begin + $show_link - $j > $pages_count) && ($pages_count-$show_link + $j > 0)) {
					$page_link = $pages_count - $show_link + $j;
					if (!isset($show_dots) && ($pages_count-$show_link > 1)) {
						echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.($page_link - 1).'><b>...</b></a> ';
						$show_dots = "no";
					}
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.$page_link.'>'.$page_link.'</a> '.$sperator;
				} else continue;
			}
			for ($j = 0; $j <= $show_link; $j++) {
				$i = $begin + $j;
				if ($i < 1) { $show_link++; continue;}

				if (!isset($show_dots) && $begin > 1) {
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.($i-1).'><b>...</b></a> ';
					$show_dots = "no";
				}
				if ($i > $pages_count) break;
				if ($i == $page) {
					echo ' <a '.$style.' ><b style="color:#FF0000; text-decoration:underline;">'.$i.'</b></a> ';
				}else{
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.$i.'>'.$i.'</a> ';
				}
				if (($i != $pages_count) && ($j != $show_link)) echo $sperator;
				if (($j == $show_link) && ($i < $pages_count)) {
					echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.($i+1).'><b>...</b></a> ';
			}
		}
		if ($begin + $show_link + 1 < $pages_count) {
			echo ' <a '.$style.' href='.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).''.$sort_link.'&s='.$pages_count.'> '.$pages_count.' </a>';
		}
		return true;
	}

	function GET_DOMEN_T($url) {
		$parts = parse_url(trim($url));
		$host = $parts["host"];
		return $host;
	}

	function format_table($id, $country_targ, $name, $url, $reiting, $comments, $username, $cena, $re, $good, $bad, $wait, $date){
		global $mysqli;
		$sql = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE `id`='$id' ");
if($sql->num_rows>0) {
    
    while ($row = $sql->fetch_array()) {
        echo '<tr id="adv_dell'.$row["id"].'" >';
		echo '<td align="center" width="30" class="noborder1" style="border-right:solid 1px #DDDDDD;">';
			echo '<div id="playpauseimg'.$row["id"].'">';
				if($row["status"]=="wait") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="alert_nostart();"></span>';
				}elseif($row["status"]=="pay") {
					echo '<span class="adv-pause" title="Приостановить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="pause") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="wait") {
					echo '<span class="adv-play" title="Запустить показ рекламной площадки" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\');"></span>';
				}elseif($row["status"]=="4") {
					echo '<span class="adv-block" title="Рекламную площадку заблокировал '.$row["user_lock"].'" onClick="PlayPause('.$row["id"].', \'task\', \'PlayPause\', \'1\');"></span>';
				}
			echo '</div>';
		echo '</td>';
		
		//echo '<tr bgcolor="#ADD8E6">';
		echo '<td align="center">'.$id.'</td>';
		echo '<td>';
			if($date > (time()-24*60*60)) {echo '<b><font color="#9966FF" title="Задание создано в течении суток" style="cursor: pointer;">NEW</font></b>&nbsp;';}
			echo '<a href="'.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'&amp;option=view&amp;rid='.$id.'">'.$name.'</a><br>Сайт: <b>http://'.GET_DOMEN_T($url).'/</b><br>Рейтинг: <b>'.round($reiting,2).'</b>&nbsp;&nbsp;&nbsp;Комментариев: <a href="'.$_SERVER["PHP_SELF"].'?op='.limpiarez($_GET["op"]).'&amp;view=comments&amp;rid='.$id.'">'.$comments.'</a>&nbsp;Рекламодатель: <b>'.$username.'</b>';
		echo '</td>';
		echo '<td align="center">';
			echo "$cena руб.<br>";
			if($re>0){
				echo '<img src="../../img/clock.png" border="0" alt="" align="middle" title="Задание можно выполнять каждые '.$re.' часа" />';}else{echo "";
			}
			if($country_targ==0)
				echo '';
			elseif($country_targ==1)
				echo '&nbsp;<img src="../../img/flags/ru.gif" border="0" width="16" height="12" alt="" align="middle" title="Выполнение задания доступно только России" />';
			elseif($country_targ==2)
				echo '&nbsp;<img src="../../img/flags/ua.gif" border="0" width="16" height="12" alt="" align="middle" title="Выполнение задания доступно только Украины" />';
			else{
				echo '';
			}						
		echo '</td>';
		echo '<td align="center"><span style="color:green;">'.$good.'</span> - <span style="color:red;">'.$bad.'</span> - <span style="color:black;">'.$wait.'</span></td>';
		echo '<td><form method="GET" onClick=\'if(!confirm("Вы точно хотите удалить задание?")) return false;\'><input type="hidden" name="op" value="'.limpiar($_GET["op"]).'"><input type="hidden" name="option" value="dell"><input type="hidden" name="rid" value="'.$id.'"><input type="submit" value="Удалить"></form></td>';
		echo '<td><form method="GET"><input type="hidden" name="op" value="'.limpiar($_GET["op"]).'"><input type="hidden" name="option" value="edit"><input type="hidden" name="rid" value="'.$id.'"><input type="submit" value="Редактировать"></form></td>';
		echo '</tr>';
	}
  }
}

	$perpage = 30;
	if (empty($_GET["s"]) || ($_GET["s"] <= 0)) {
		$page = 1;
	}else{
		$page = intval($_GET["s"]);
	}


	$WHERE = "";
	$ORDER = "`date_up` DESC";
	$ORDER_Z = "";

	$type = (isset($_GET["type"])) ? limpiarez($_GET["type"]) : false;
	$sort = (isset($_GET["sort"]) && intval(limpiarez($_GET["sort"]))>0 ) ? intval(limpiarez($_GET["sort"])) : "1";
	$sort_z = (isset($_GET["sort_z"]) && intval(limpiarez($_GET["sort_z"]))>0 && intval(limpiarez($_GET["sort_z"]))<6 ) ? intval(limpiarez($_GET["sort_z"])) : false;
	$task_search = (isset($_GET["task_search"]) && intval(limpiarez($_GET["task_search"]))>0 && intval(limpiarez($_GET["task_search"]))<5) ? intval(limpiarez($_GET["task_search"])) : false;
	$task_name = (isset($_GET["task_name"])) ? limpiarez($_GET["task_name"]) : false;
	$task_auto = (isset($_GET["task_auto"]) && intval(limpiarez($_GET["task_auto"]))==2 ) ? intval(limpiarez($_GET["task_auto"])) : "1";
	$task_price = (isset($_GET["task_price"])) ? abs(round(floatval(str_replace(",",".",trim($_GET["task_price"]))), 2)) : false;


	if($task_search!=false) {
		if($task_search==1) {
			$task_name = intval($task_name);
			if($task_name>0) {
				$WHERE = " `id`='$task_name' AND `zdcheck`>='$task_auto' AND `zdprice`>='$task_price' AND ";
			}else{
				$WHERE = " `zdcheck`>='$task_auto' AND `zdprice`>='$task_price' AND ";
			}
		}elseif($task_search==2) {
			$WHERE = " `zdname` LIKE '%$task_name%' AND `zdcheck`>='$task_auto' AND `zdprice`>='$task_price' AND ";
		}elseif($task_search==3) {
			$WHERE = " `zdtext` LIKE '%$task_name%' AND `zdcheck`>='$task_auto' AND `zdprice`>='$task_price' AND ";
		}elseif($task_search==4) {
			$task_name = intval($task_name);
			$WHERE = " `user_id`='$task_name' AND `zdcheck`>='$task_auto' AND `zdprice`>='$task_price' AND ";
		}else{
			$WHERE = "";
		}
	}

	if($type==false) {$type_tab="";}else{$type_tab=" AND `zdtype`='$type'";}

	if($sort_z==false) {
		$ORDER_Z = "";
		$zpt = "";
	}elseif($sort_z==1){
		$WHERE = "$WHERE";
		$ORDER_Z = "`zdprice` DESC";
		$zpt = ",";
	}elseif($sort_z==2){
		$WHERE = "$WHERE";
		$ORDER_Z = "`zdprice` ASC";
		$zpt = ",";
	}elseif($sort_z==3){
		$WHERE = "$WHERE";
		$ORDER_Z = "`goods` DESC";
		$zpt = ",";
	}elseif($sort_z==4){
		$WHERE = "$WHERE";
		$ORDER_Z = "`bads` DESC";
		$zpt = ",";
	}elseif($sort_z==5){
		$WHERE = "$WHERE";
		$ORDER_Z = "`wait` DESC";
		$zpt = ",";
	}else{
		$ORDER_Z = "";
		$zpt = "";
	}


	if($sort==1) {
		$WHERE = "$WHERE";
		$ORDER = "$ORDER_Z $zpt `date_up` DESC";
	}elseif($sort==2){
		$WHERE = "$WHERE `date_add`>='".(time()-24*60*60)."' AND ";
		$ORDER = "$ORDER_Z $zpt `date_up` DESC";
	}elseif($sort==3){
		$WHERE = "$WHERE `reiting`>='4.5' AND `goods`>='1' AND ";
		$ORDER = "$ORDER_Z $zpt `date_up` DESC";
	}elseif($sort==4){
		$WHERE = "$WHERE";
		$ORDER = "$ORDER_Z $zpt `date_up` DESC";
	}elseif($sort==5){
		if($sort_z==false) {
			$WHERE = "$WHERE `reiting`>='3' AND ";
			$ORDER = "`reiting` DESC, `date_up` DESC";
		}else{
			$WHERE = "$WHERE `reiting`>='3' AND ";
			$ORDER = "`reiting` DESC $zpt $ORDER_Z $zpt `date_up` DESC";
		}
	}else{
		$WHERE = "$WHERE";
		$ORDER = "$ORDER";
	}


	echo '<table align="center" border="0" width="100%" cellspacing="1" cellpadding="1" style="border-collapse: collapse;">';
	echo '<tr>';
		echo '<td align="left">';
			$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_task' AND `howmany`='1'");
			$cena_task = $sql->fetch_object()->price;

			echo '<b>Поиск оплачиваемых заданий:</b><br><br>';
			echo '<div id="form"><form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
				echo '<input type="hidden" name="op" value="'.limpiar($_GET["op"]).'">';
				echo '<input type="hidden" name="sort" value="'.$sort.'">';
				echo '<input type="hidden" name="sort_z" value="'.$sort_z.'">';
				echo '<input type="hidden" name="type" value="'.$type.'">';
				echo '<select name="task_search">';
					echo '<option value="1"'; if($task_search==1){echo ' selected="selected"';} echo '>id задания</option>';
					echo '<option value="2"'; if($task_search==2){echo ' selected="selected"';} echo '>Название задания</option>';
					echo '<option value="3"'; if($task_search==3){echo ' selected="selected"';} echo '>Описание задания</option>';
					echo '<option value="4"'; if($task_search==4){echo ' selected="selected"';} echo '>id рекламодателя</option>';
				echo '</select><b>:</b> ';
				echo '<input type="text" name="task_name" size="5" style="width: 200px;" value=""><br>';
				echo '<input type="checkbox" name="task_auto" value="2"'; if($task_auto==2){echo ' checked="checked"';} echo '> - отображать задания <b>только</b> с автоподтверждением<br>';
				echo 'Стоимость задания больше: <input type="text" name="task_price" size="5" style="text-align: right;" value="'.number_format($cena_task,2,".","").'">';
				echo '<input type="hidden" name="s" value="'.$page.'">';
				echo '<input type="submit" class="submit" value="&nbsp;&nbsp;Найти&nbsp;&nbsp;">';
			echo '</form></div>';
		echo '</td>';
	echo '</tr>';
	echo '</table>';


	$count = $mysqli->query("SELECT `id` FROM `tb_ads_task` WHERE $WHERE `status`='pay' $type_tab")->num_rows;
	$pages_count = ceil($count / $perpage);

	if ($page > $pages_count) $page = $pages_count;
	$start_pos = ($page - 1) * $perpage;
	if($start_pos<0) $start_pos=0;
	$no_task_text = "Доступных заданий нет!";

	//echo "WHERE $WHERE status=pay $type_tab ORDER BY $ORDER LIMIT";

	$tabla = $mysqli->query("SELECT * FROM `tb_ads_task` WHERE $WHERE `status`!='0' $type_tab ORDER BY $ORDER LIMIT $start_pos,$perpage");
	$all_task = $tabla->num_rows;

	echo '<table align="center" border="1" width="100%" cellspacing="3" cellpadding="3" style="border-collapse: collapse; border: 1px solid #000; line-height: 1.4em;">';
		echo '<tr>';
			echo '<td colspan="6">Найдено записей <b>'.$count.'</b>, показано <b>'.$all_task.'</b><br>';
			if($count>$perpage) {echo '<div align="left"><b>Страницы:</b> '; universal_link_bar($page, $count, $pages_count, 8, $sort, $sort_z, $type, $task_search, $task_name, $task_auto, $task_price); echo '</div>';}
			echo '</td>';
		echo '</tr>';
		echo '<tr bgcolor="#7EC0EE">';
		echo '<th align="center" style="border: 1px solid #000;" rowspan="2" width="30">Статус</th>';
		echo '<th align="center" style="border: 1px solid #000;" rowspan="2" width="30">#</th>';
		echo '<th align="left" style="border: 1px solid #000;">Название: ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort=1&sort_z='.$sort_z.'&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($sort<2){echo ' class="b"';} echo '>Все</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort=2&sort_z='.$sort_z.'&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($sort==2){echo ' class="b"';} echo '>Новые</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort=3&sort_z='.$sort_z.'&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($sort==3){echo ' class="b"';} echo '>Лучшие</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort=5&sort_z='.$sort_z.'&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($sort==5){echo ' class="b"';} echo '>По рейтингу</a>';
		echo '</th>';
		echo '<th align="center" style="border: 1px solid #000;">Стоимость</th>';
		echo '<th align="center" style="border: 1px solid #000;">Статистика</th>';
		echo '<th rowspan="2" colspan="2" style="border: 1px solid #000;"></th>';
		echo '</tr>';
		echo '<tr bgcolor="#7EC0EE">';
		echo '<th align="left" style="border: 1px solid #000;">Категория: ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type<1){echo ' class="b"';} echo '>Все</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=1&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==1){echo ' class="b"';} echo '>Только регистрация</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=2&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==2){echo ' class="b"';} echo '>Регистрация с активностью</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=3&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==3){echo ' class="b"';} echo '>Только клики</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=4&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==4){echo ' class="b"';} echo '>Активность на видео сервисах</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=5&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==5){echo ' class="b"';} echo '>Социальные сети</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=6&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==6){echo ' class="b"';} echo '>Подписка на рассылку</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=7&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==7){echo ' class="b"';} echo '>Инвестиции</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=8&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==8){echo ' class="b"';} echo '>Играть в игры</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=9&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==9){echo ' class="b"';} echo '>Работа с каптчей</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=10&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==10){echo ' class="b"';} echo '>Статьи / Отзывы</a>&nbsp;| ';
			echo '<a  href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z='.$sort_z.'&type=11&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"'; if($type==11){echo ' class="b"';} echo '>Прочее</a>&nbsp;| ';
		echo '</th>';
		echo '<th align="center" style="border: 1px solid #000;" width="50">
			<table width="100%" style="border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
			<tr align="center">
			<th border="0"><a href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z=1&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"><img src="../../img/down.png" border="0" alt="" align="middle" title="Сортировка по уменьшению стоимости" /></a></th>
			<th border="0"><a href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z=2&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"><img src="../../img/up.png" border="0" alt="" align="middle" title="Сортировка по увеличению стоимости" /></a></th>
			</tr></table>
		</th>';
		echo '<th align="center" style="border: 1px solid #000;" width="70">
			<table width="100%" style="border-collapse: collapse;" border="0">
			<tr align="center">
			<th border="0"><a href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z=3&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"><img src="../../img/good.png" border="0" alt="" align="middle" title="Выполнено и оплачено" /></a></th>
			<th border="0"><a href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z=4&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"><img src="../../img/bad.png" border="0" alt="" align="middle" title="Отказано в оплате" /></a></th>
			<th border="0"><a href="'.$_SERVER['PHP_SELF'].'?op='.limpiarez($_GET["op"]).'&sort='.$sort.'&sort_z=5&type='.$type.'&task_search='.$task_search.'&task_name='.$task_name.'&task_auto='.$task_auto.'&task_price='.$task_price.'&s='.$page.'"><img src="../../img/wait.png" border="0" alt="" align="middle" title="Непроверенных заявок" /></a></th>
			</tr></table>
		</th>';
		echo '</tr>';

		if($all_task>0) {
			while($links_row = $tabla->fetch_assoc()) {
				format_table(
					$links_row["id"],
					$links_row["country_targ"],
					$links_row["zdname"],
					$links_row["zdurl"],
					$links_row["reiting"],
					$links_row["all_coments"],
					$links_row["username"],
					$links_row["zdprice"],
					$links_row["zdre"],
					$links_row["goods"],
					$links_row["bads"],
					$links_row["wait"],
					$links_row["date_add"]
				);
			}

			echo '<tr>';
				echo '<td colspan="6">Найдено записей <b>'.$count.'</b>, показано <b>'.$all_task.'</b><br>';
				if($count>$perpage) {echo '<div align="left"><b>Страницы:</b> '; universal_link_bar($page, $count, $pages_count, 8, $sort, $sort_z, $type, $task_search, $task_name, $task_auto, $task_price); echo '</div>';}
				echo '</td>';
			echo '</tr>';
		}else{
			echo '<tr><td align="center" colspan="6">'.$no_task_text.'</td></tr>';
		}
	echo "</table>";
}
?>