<?php
@session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require(ROOT_DIR."/config.php");
require(ROOT_DIR."/funciones.php");
sleep(0);

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
		$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
		$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", limpiar($_POST["op"])) ) ? limpiar($_POST["op"]) : false;
		$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(limpiar($_POST["id"])))) ? intval(limpiar($_POST["id"])) : false;
		$laip = getRealIP();

		if($option=="AddWelcome") {
			$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 100) : false;
			$description = (isset($_POST["description"])) ? limpiarez($_POST["description"]) : false;
			if (get_magic_quotes_gpc()) { $description = stripslashes($description); }

			if($title==false) {
				exit("Вы не указали тему сообщения!");
			}elseif($description==false) {
				exit("Вы не указали текст сообщения!");
			}else{
				$sql = $mysqli->query("SELECT `id` FROM `tb_invest_welcome`") or die($mysqli->error);
				if($sql->num_rows>0) {
					$mysqli->query("UPDATE `tb_invest_welcome` SET `title_text`='$title', `desc_text`='$description'") or die($mysqli->error);

					exit("OK");
				}else{
					$mysqli->query("INSERT INTO `tb_invest_welcome` (`title_text`,`desc_text`) 
					VALUES('$title','$description')") or die($mysqli->error);

					exit("OK");
				}
			}

		}elseif($option=="LoadWelcome") {
			$sql = $mysqli->query("SELECT  * FROM `tb_invest_welcome`") or die($mysqli->error);
			if($sql->num_rows>0) {
				$row = $sql->fetch_assoc();
				$title = (isset($row["title_text"])) ? limitatexto(trim($row["title_text"]), 100) : false;
				$description = (isset($row["desc_text"])) ? trim($row["desc_text"]) : false;

				require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
				$desc = new bbcode($description);
				$desc = $desc->get_html();

				$message_text = "";
				$message_text.= '<table class="tables">';
				$message_text.= '<tbody>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left" style="padding:5px 10px;">'.$title.'</td>';
						$message_text.= '<td align="center" rowspan="2" width="50">';
							$message_text.= '<span class="adv-edit" onClick="EditWelcome(\'EditWelcome\');" title="Редактировать" style="float:none; display:inline-block;"></span>';
							$message_text.= '<span class="adv-dell" onClick="DelWelcome(\'DelWelcome\');" title="Удалить" style="float:none; display:inline-block;"></span>';
						$message_text.= '</td>';
					$message_text.= '</tr>';
					$message_text.= '<tr>';
						$message_text.= '<td align="left" style="padding:5px 10px;">'.$desc.'</td>';
					$message_text.= '</tr>';
				$message_text.= '</tbody>';
				$message_text.= '</table></div>';

				$result_text = "OK";
				$my_json_encode = json_encode_cp1251(array(
					"result" => iconv("CP1251", "UTF-8", $result_text), 
					"message" => iconv("CP1251", "UTF-8", $message_text), 
					"title" => iconv("CP1251", "UTF-8", $title), 
					"description" => iconv("CP1251", "UTF-8", $description)
				));
				exit($my_json_encode);
			}else{
				$result_text = "ERROR";
				$message_text = "Приветствие не найдено!";
				$title = false;
				$description = false;
				$my_json_encode = json_encode_cp1251(array(
					"result" => iconv("CP1251", "UTF-8", $result_text), 
					"message" => iconv("CP1251", "UTF-8", $message_text), 
					"title" => iconv("CP1251", "UTF-8", $title), 
					"description" => iconv("CP1251", "UTF-8", $description)
				));
				exit($my_json_encode);
			}

		}elseif($option=="DelWelcome") {
			$mysqli->query("TRUNCATE TABLE `tb_invest_welcome`") or die($mysqli->error);
			exit("OK");

		}else{
			exit("Не удалось обработать запрос!");
		}
	}else{
		exit("Необходимо авторизоваться.");
	}
}else{
	exit("Не удалось обработать запрос.");
}

?>