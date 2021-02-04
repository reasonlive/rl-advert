<?php
session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
$js_result = array("result" => "ERROR", "message" => "Access denied!");
$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";
//sleep(0);

function json_encode_cp1251($json_arr) {
	$json_arr = json_encode($json_arr);
	$arr_replace_cyr = array("\u0410", "\u0430", "\u0411", "\u0431", "\u0412", "\u0432", "\u0413", "\u0433", "\u0414", "\u0434", "\u0415", "\u0435", "\u0401", "\u0451", "\u0416", "\u0436", "\u0417", "\u0437", "\u0418", "\u0438", "\u0419", "\u0439", "\u041a", "\u043a", "\u041b", "\u043b", "\u041c", "\u043c", "\u041d", "\u043d", "\u041e", "\u043e", "\u041f", "\u043f", "\u0420", "\u0440", "\u0421", "\u0441", "\u0422", "\u0442", "\u0423", "\u0443", "\u0424", "\u0444", "\u0425", "\u0445", "\u0426", "\u0446", "\u0427", "\u0447", "\u0428", "\u0448", "\u0429", "\u0449", "\u042a", "\u044a", "\u042b", "\u044b", "\u042c", "\u044c", "\u042d", "\u044d", "\u042e", "\u044e", "\u042f", "\u044f");
	$arr_replace_utf = array("А", "а", "Б", "б", "В", "в", "Г", "г", "Д", "д", "Е", "е", "Ё", "ё", "Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н","О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь","ь","Э","э","Ю","ю","Я","я");
	$json_arr = str_replace($arr_replace_cyr, $arr_replace_utf, $json_arr);
	return $json_arr;
}

function my_json_encode($ajax_json, $result_text, $message_text) {
	return ($ajax_json=="json") ? json_encode_cp1251(array("result" => iconv("CP1251", "UTF-8", $result_text), "message" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
}

function my_json_encode_page($ajax_json, $result_text, $page, $message_text) {
	return ($ajax_json=="json") ? json_encode_cp1251(array("result" => iconv("CP1251", "UTF-8", $result_text), "page" => intval($page), "ajax_code" => iconv("CP1251", "UTF-8", $message_text))) : $message_text;
}

function limpiarez($mensaje) {
	global $mysqli;
	$mensaje = trim($mensaje);
	$mensaje = str_replace("'", "", $mensaje);
	$mensaje = str_replace("`", "", $mensaje);
//	$mensaje = str_replace('"', "&#34;", $mensaje);
	$mensaje = str_replace("?", "&#063;", $mensaje);
	$mensaje = str_replace("$", "&#036;", $mensaje);

	$mensaje = preg_replace("#([-0-9a-z_\.]+@[-0-9a-z_\.]+\.[a-z]{2,6})#i", "", $mensaje);
	$mensaje = preg_replace("'<script[^>]*?>.*?</script>'si", "", $mensaje);
	$mensaje = preg_replace("'<[^>]*?>.*?'si", "", $mensaje);

	$mensaje = $mysqli->real_escape_string(trim($mensaje));
//	$mensaje = str_replace("\\", "", $mensaje);

	$mensaje = iconv("UTF-8", "CP1251//TRANSLIT", htmlspecialchars(trim($mensaje), NULL, "CP1251"));
	$mensaje = htmlspecialchars(trim($mensaje), NULL, "CP1251");
	$mensaje = str_replace("  ", " ", $mensaje);
	$mensaje = str_replace("&amp amp ", "&", $mensaje);
	$mensaje = str_replace("&amp;amp;", "&", $mensaje);
	$mensaje = str_replace("&&", "&", $mensaje);
	$mensaje = str_replace("http://http://", "http://", $mensaje);
	$mensaje = str_replace("https://https://", "https://", $mensaje);
	$mensaje = str_replace("&#063;", "?", $mensaje);
	$mensaje = str_replace("&amp;", "&", $mensaje);

	return $mensaje;
}

function myErrorHandler($errno, $errstr, $errfile, $errline, $js_result) {
	$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false) ? "json" : "nojson";
	$message_text = false;
	$errfile = str_replace(ROOT_DIR, "", $errfile);
	switch($errno) {
		case(1): $message_text = "Fatal error[$errno]: $errstr in line $errline in $errfile"; break;
		case(2): $message_text = "Warning[$errno]: $errstr in line $errline in $errfile"; break;
		case(8): $message_text = "Notice[$errno]: $errstr in line $errline in $errfile"; break;
		default: $message_text = "[$errno] $errstr in line $errline in $errfile"; break;
	}
	$message_text = "$message_text";
	exit(my_json_encode($ajax_json, "ERROR", $message_text));
}
$set_error_handler = set_error_handler("myErrorHandler", E_ALL);

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	if(isset($_SESSION["userLog_a"]) && isset($_SESSION["userPas_a"])) {
		require(ROOT_DIR."/config.php");
		require(ROOT_DIR."/funciones.php");
		require_once(ROOT_DIR."/merchant/func_cache.php");
		require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
/*
		function desc_bb($desc) {
			if($desc !== false) {
				$desc = new bbcode($desc);
				$desc = $desc->get_html();
				$desc = str_replace("&amp;", "&", $desc);
				return $desc;
			}
		}
*/
		$user_name = (isset($_SESSION["userLog_a"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,25}$|", trim($_SESSION["userLog_a"]))) ? htmlspecialchars(trim($_SESSION["userLog_a"])) : false;
		$user_pass = (isset($_SESSION["userPas_a"]) && preg_match("|^[0-9a-fA-F]{32}$|", trim($_SESSION["userPas_a"]))) ? htmlspecialchars(trim($_SESSION["userPas_a"])) : false;
		$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", htmlspecialchars(trim($_POST["op"]))) ) ? htmlspecialchars(trim($_POST["op"])) : false;
		$my_lastiplog = getRealIP();

		if($option == "AddNews") {
			$title = isset($_POST["title"]) ? limitatexto(limpiarez($_POST["title"]), 60) : false;
			$description = isset($_POST["description"]) ? limpiarez($_POST["description"]) : false;
			$description = get_magic_quotes_gpc() ? stripslashes($description) : $description;
			$link_forum = isset($_POST["link_forum"]) ? filter_var(trim($_POST["link_forum"]), FILTER_VALIDATE_URL) : false;
			$status_comments = ( isset($_POST["status_comments"]) && preg_match("|^[0-1]{1}$|", trim($_POST["status_comments"])) ) ? intval(trim($_POST["status_comments"])) : false;
			$re_not = ( isset($_POST["re_not"]) && preg_match("|^[0-1]{1}$|", trim($_POST["re_not"])) ) ? intval(trim($_POST["re_not"])) : false;

			if($title==false) {
				$result_text = "ERROR"; $message_text = "Вы не указали заголовок новости!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));

			}elseif($description==false) {
				$result_text = "ERROR"; $message_text = "Вы не указали текст новости!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));

			}else{
				$mysqli->query("INSERT INTO `tb_news` (`title`,`description`,`link_forum`,`status_comments`,`time`) 
				VALUES('$title','$description','$link_forum','$status_comments','".time()."')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				if($re_not == 1) $mysqli->query("UPDATE `tb_users` SET `read_news`='0' WHERE `read_news`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				cache_news();


				$result_text = "OK"; $message_text = "Новость успешно добавлена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}


		}elseif($option == "LoadNews") {
			$page = ( isset($_POST["page"]) && preg_match("|^[\d]{1,11}$|", htmlspecialchars(trim($_POST["page"]))) ) ? htmlspecialchars(trim($_POST["page"])) : false;
			$perpage = ( isset($_POST["num"]) && preg_match("|^[\d]{1,11}$|", htmlspecialchars(trim($_POST["num"]))) ) ? htmlspecialchars(trim($_POST["num"])) : false;
			$hash_post = ( isset($_POST["hash"]) && preg_match("/^[0-9a-fA-F]{32}$/", htmlspecialchars(trim($_POST["hash"]))) ) ? htmlspecialchars(trim($_POST["hash"])) : false;
			$message_text = false;

			$count = mysql_numrows($mysqli->query("SELECT `id` FROM `tb_news`")) or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			$pages_count = ceil($count / $perpage);
			$start_pos = intval($page * $perpage);

			$sql = $mysqli->query("SELECT * FROM `tb_news` ORDER BY `id` DESC LIMIT $start_pos, $perpage") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			if($sql->num_rows>0) {
				while ($row = $sql->fetch_assoc()) {
					$message_text.= '<tr align="center" id="IdDel'.$row["id"].'">';
						$message_text.= '<td align="left" style="padding:2px 0px;" id="Edit'.$row["id"].'">';
							$message_text.= '<div style="color:#9E003F; background-color:#FFEC82; padding:3px 7px;">'.DATE("d.m.Yг. H:i", $row["time"]).'</div>';
							$message_text.= '<div style=" padding:2px 5px; display:block; margin-top:3px; margin-bottom:3px; color:#008B8B; font-size:14px;">'.$row["title"].'</div>';
							$message_text.= '<div style=" padding:2px 5px; display:block; font-size:12px;">'.desc_bb($row["description"]).'</div>';
							$message_text.= '<div style="margin-top:5px;">';
								$message_text.= '<span class="adv-dell" onClick="DelNews('.$row["id"].');" title="Удалить новость"></span>';
								$message_text.= '<span class="adv-edit" onClick="EditNews('.$row["id"].');" title="Редактировать новость"></span>';
								$message_text.= '<span id="lock-'.$row["id"].'" class="adv-'.($row["status_comments"]=="1" ? "lock" : "unlock").'" title="'.($row["status_comments"]=="1" ? "Закрыть комментирование" : "Открыть комментирование").'" onClick="LockComNews('.$row["id"].', '.$row["status_comments"].');" style="margin-right:3px;"></span>';
							$message_text.= '</div>';
						$message_text.= '</td>';
					$message_text.= '</tr>';
				}

				$page++;
				$result_text = "OK";
				exit(my_json_encode_page($ajax_json, $result_text, $page, $message_text));

			}else{
				$result_text = "ERROR"; $message_text = "ERROR: NOW NEWS LOAD!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}

		}elseif($option == "DelNews") {
			$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(trim($_POST["id"])))) ? intval(trim($_POST["id"])) : false;

			$sql = $mysqli->query("SELECT `id` FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			if($sql->num_rows>0) {
				$mysqli->query("DELETE FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
				$mysqli->query("DELETE FROM `tb_news_comments` WHERE `ident`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				cache_news();

				$result_text = "OK"; $message_text = "Новость с ID:$id удалена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}else{
				$result_text = "ERROR"; $message_text = "Новость не найдена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}


		}elseif($option == "EditNews") {
			$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(trim($_POST["id"])))) ? intval(trim($_POST["id"])) : false;

			$sql = $mysqli->query("SELECT * FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			if($sql->num_rows>0) {
				$row = $sql->fetch_assoc();

				$message_text = false;
				$message_text.= '<div id="FormNews">';
				$message_text.= '<table class="tables" id="newform">';
				$message_text.= '<thead><tr align="center"><th width="220">Параметр</th><th>Значение</th></thead></tr>';
				$message_text.= '<tbody>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left"><b>Заголовок новости</b></td>';
						$message_text.= '<td align="left"><input type="text" id="title" value="'.(isset($row["title"]) ? $row["title"] : false).'" maxlength="60" class="ok" autocomplete="off" placeholder="Заголовок новости" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td colspan="2"><b>Текст новости &darr;</b></td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td colspan="2">';
							$message_text.= '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'description\'); return false;">Ж</span>';
							$message_text.= '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'description\'); return false;">К</span>';
							$message_text.= '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'description\'); return false;">Ч</span>';
							$message_text.= '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'description\'); return false;">ST</span>';
							$message_text.= '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'description\'); return false;"></span>';
							$message_text.= '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'description\'); return false;"></span>';
							$message_text.= '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'description\'); return false;"></span>';
							$message_text.= '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'description\'); return false;"></span>';
							$message_text.= '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'description\'); return false;">URL</span>';
							$message_text.= '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'description\'); return false;">IMG</span>';
							$message_text.= '<br>';
							$message_text.= '<div style="display: block; clear:both; padding-top:4px">';
								$message_text.= '<textarea id="description" class="ok" style="height:250px; width:99.2%;" placeholder="Текст новости" onKeyDown="$(this).attr(\'class\', \'ok\');">'.(isset($row["description"]) ? $row["description"] : false).'</textarea>';
							$message_text.= '</div>';
						$message_text.= '</td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left"><b>Ссылка на форум</b>, [ для обсуждения ]</td>';
						$message_text.= '<td align="left"><input type="text" id="link_forum" value="'.(isset($row["link_forum"]) ? $row["link_forum"] : false).'" maxlength="300" class="ok" autocomplete="off" placeholder="Не обязательно, пример: http://'.$_SERVER["HTTP_HOST"].'/forum.php" onKeyDown="$(this).attr(\'class\', \'ok\');" /></td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left"><b>Разрешить комментирование новости</b></td>';
						$message_text.= '<td align="left"><input type="checkbox" id="status_comments" value="1" '.( (isset($row["status_comments"]) && $row["status_comments"]==1) ? 'checked="checked"' : false).' style="height:20px; width:20px; margin:0px;"></td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left"><b>Уведомить об изменении новости</b></td>';
						$message_text.= '<td align="left"><input type="checkbox" id="re_not" value="1" style="height:20px; width:20px; margin:0px;"></td>';
					$message_text.= '</tr>';
				$message_text.= '</tbody>';
				$message_text.= '</table>';
				$message_text.= '<div align="center"><span onClick="SaveNews('.$row["id"].');" class="sub-blue160" style="float:none; width:160px;">Сохранить</span></div>';
				$message_text.= '</div>';
				$message_text.= '<div id="info-msg-news" style="display:none;"></div>';

				$result_text = "OK";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}else{
				$result_text = "ERROR"; $message_text = "Новость не найдена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}


		}elseif($option == "SaveNews") {
			$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(trim($_POST["id"])))) ? intval(trim($_POST["id"])) : false;
			$title = isset($_POST["title"]) ? limitatexto(limpiarez($_POST["title"]), 60) : false;
			$description = isset($_POST["description"]) ? limpiarez($_POST["description"]) : false;
			$description = get_magic_quotes_gpc() ? stripslashes($description) : $description;
			$link_forum = isset($_POST["link_forum"]) ? filter_var(trim($_POST["link_forum"]), FILTER_VALIDATE_URL) : false;
			$status_comments = ( isset($_POST["status_comments"]) && preg_match("|^[0-1]{1}$|", trim($_POST["status_comments"])) ) ? intval(trim($_POST["status_comments"])) : false;
			$re_not = ( isset($_POST["re_not"]) && preg_match("|^[0-1]{1}$|", trim($_POST["re_not"])) ) ? intval(trim($_POST["re_not"])) : false;

			$sql = $mysqli->query("SELECT * FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			if($sql->num_rows>0) {
				$row = $sql->fetch_assoc();

				if($title==false) {
					$result_text = "ERROR"; $message_text = "Вы не указали заголовок новости!";
					exit(my_json_encode($ajax_json, $result_text, $message_text));

				}elseif($description==false) {
					$result_text = "ERROR"; $message_text = "Вы не указали текст новости!";
					exit(my_json_encode($ajax_json, $result_text, $message_text));

				}else{
					$mysqli->query("UPDATE `tb_news` SET `title`='$title', `description`='$description', `link_forum`='$link_forum', `status_comments`='$status_comments' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

					if($re_not == 1) $mysqli->query("UPDATE `tb_users` SET `read_news`='0' WHERE `read_news`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

					cache_news();

					$sql = $mysqli->query("SELECT * FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
					if($sql->num_rows>0) {
						$row = $sql->fetch_assoc();
						$description = $row["description"];
					}else{
						$description = $description;
					}

					$message_text = false;
					$message_text.= '<div style="color:#9E003F; background-color:#FFEC82; padding:3px 7px;">'.DATE("d.m.Yг. H:i", $row["time"]).'</div>';
					$message_text.= '<div style=" padding:2px 5px; display:block; margin-top:3px; margin-bottom:3px; color:#008B8B; font-size:14px;">'.$title.'</div>';
					$message_text.= '<div style=" padding:2px 5px; display:block; font-size:12px;">'.desc_bb($row["description"]).'</div>';
					$message_text.= '<div style="margin-top:5px;">';
						$message_text.= '<span class="adv-dell" onClick="DelNews('.$row["id"].');" title="Удалить новость"></span>';
						$message_text.= '<span class="adv-edit" onClick="EditNews('.$row["id"].');" title="Редактировать новость"></span>';
						$message_text.= '<span id="lock-'.$row["id"].'" class="adv-'.($status_comments=="1" ? "lock" : "unlock").'" title="'.($status_comments=="1" ? "Закрыть комментирование" : "Открыть комментирование").'" onClick="LockComNews('.$row["id"].', '.$status_comments.');" style="margin-right:3px;"></span>';
					$message_text.= '</div>';

					$result_text = "OK";
					exit(my_json_encode($ajax_json, $result_text, $message_text));
				}
			}else{
				$result_text = "ERROR"; $message_text = "Новость не найдена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}

		}elseif($option == "LockComNews") {
			$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(trim($_POST["id"])))) ? intval(trim($_POST["id"])) : false;
			$lock = ( isset($_POST["lock"]) && preg_match("|^[0-1]{1}$|", trim($_POST["lock"])) ) ? intval(trim($_POST["lock"])) : false;

			$sql = $mysqli->query("SELECT * FROM `tb_news` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
			if($sql->num_rows>0) {
				if($lock==1) { $lock = 0; }else{ $lock = 1; }

				$mysqli->query("UPDATE `tb_news` SET `status_comments`='$lock' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

				cache_news();

				$result_text = "OK"; $message_text = "$lock";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}else{
				$result_text = "ERROR"; $message_text = "Новость не найдена!";
				exit(my_json_encode($ajax_json, $result_text, $message_text));
			}
		}else{
			$result_text = "ERROR"; $message_text = "ERROR: NO OPTION!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}else{
		$result_text = "ERROR"; $message_text = "Пользователь не идентифицирован. Необходимо авторизоваться!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}
}else{
	$result_text = "ERROR"; $message_text = "Access denied!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

$result_text = "ERROR"; $message_text = "Нет корректного AJAX запроса.";
exit(my_json_encode($ajax_json, $result_text, $message_text));

?>