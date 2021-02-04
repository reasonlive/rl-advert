<?php
@session_start();
error_reporting (E_ALL);
header("Content-type: text/html; charset=utf-8");
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require(ROOT_DIR."/merchant/func_cache.php");
require(ROOT_DIR."/config.php");
require(ROOT_DIR."/funciones.php");
sleep(0);

function limpiarez($mensaje) {
	global $mysqli;
	$mensaje = trim($mensaje);
	$mensaje = str_replace("'", "", $mensaje);
	$mensaje = str_replace("`", "", $mensaje);

	$mensaje = preg_replace("#([-0-9a-z_\.]+@[-0-9a-z_\.]+\.[a-z]{2,6})#i", "", $mensaje);
	$mensaje = preg_replace("'<script[^>]*?>.*?</script>'si", "", $mensaje);
	$mensaje = preg_replace("'<[^>]*?>.*?'si", "", $mensaje);

	$mensaje = $mysqli->real_escape_string(trim($mensaje));
//	$mensaje = str_replace("\\", "", $mensaje);

	$mensaje = iconv("UTF-8", "CP1251", htmlspecialchars(trim($mensaje)));
	$mensaje = (trim($mensaje));
	$mensaje = str_replace("?", "&#063;", $mensaje);
	$mensaje = str_replace("$", "&#036;", $mensaje);
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

if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest") {
	if(isset($_SESSION["userLog_a"]) && isset($_SESSION["userPas_a"])) {
		$username = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\-_-]{3,20}$|", trim($_SESSION["userLog"]))) ? uc($_SESSION["userLog"]) : false;
		$option = ( isset($_POST["op"]) && preg_match("|^[a-zA-Z0-9\-_]{3,20}$|", limpiar($_POST["op"])) ) ? limpiar($_POST["op"]) : false;
		$id = (isset($_POST["id"]) && preg_match("|^[\d]{1,11}$|", intval(limpiar($_POST["id"])))) ? intval(limpiar($_POST["id"])) : false;
		$laip = getRealIP();

		if($option=="Add") {
			$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 60) : false;
			$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
			$url_img = (isset($_POST["url_img"])) ? limitatexto(limpiarez($_POST["url_img"]), 300) : false;
			$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]), 2000) : false;
			if (get_magic_quotes_gpc()) { $description = stripslashes($description); }

			if($title==false) {
				exit("Не заполнено поле заголовок уведомления!");

			}elseif($url==false | $url=="http://" | $url=="https://") {
				exit("Не указана ссылка на сайт!");

			}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
				exit("Не верно указана ссылка на сайт!");

			}elseif(is_url($url)!="true") {
				exit("Не верно указана ссылка на сайт!");

			}elseif($url_img==false | $url_img=="http://" | $url_img=="https://") {
				exit("Не указана ссылка на баннер!");

			}elseif((substr($url_img, 0, 7) != "http://" && substr($url_img, 0, 8) != "https://")) {
				exit("Не верно указана ссылка на баннер!");

			}elseif(is_url($url_img)!="true") {
				exit("Не верно указана ссылка на баннер!");

			}elseif(is_url_img($url_img, "cabinet")!="true") {
				echo is_url_img($url_img, "cabinet");
				exit();

			}elseif($description==false) {
				exit("Не заполнено поле описание уведомления!");

			}else{
				$mysqli->query("INSERT INTO `tb_notification` (`id`,`status`,`url`,`url_img`,`title`,`description`) 
				VALUES(NULL, '1','$url','$url_img','$title','$description')") or die($mysqli->error);

				cache_notification();

				exit("OK");
			}

		}elseif($option=="ShowForm") {
			//sleep(1);
			if(!DEFINED("LoadForm")) DEFINE("LoadForm", true);

			$sql = $mysqli->query("SELECT * FROM `tb_notification` WHERE `id`='$id'");
			if($sql->num_rows>0) {
				$row = $sql->fetch_array();

				include("notification_form.php");
			}else{
				exit("ERRORNOID");
			}
			exit();

		}elseif($option=="Save") {
			$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 60) : false;
			$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
			$url_img = (isset($_POST["url_img"])) ? limitatexto(limpiarez($_POST["url_img"]), 300) : false;
			$description = (isset($_POST["description"])) ? limitatexto(limpiarez($_POST["description"]), 2000) : false;
			if (get_magic_quotes_gpc()) { $description = stripslashes($description); }

			if($title==false) {
				exit("Не заполнено поле заголовок уведомления!");

			}elseif($url==false | $url=="http://" | $url=="https://") {
				exit("Не указана ссылка на сайт!");

			}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
				exit("Не верно указана ссылка на сайт!");

			}elseif(is_url($url)!="true") {
				exit("Не верно указана ссылка на сайт!");

			}elseif($url_img==false | $url_img=="http://" | $url_img=="https://") {
				exit("Не указана ссылка на баннер!");

			}elseif((substr($url_img, 0, 7) != "http://" && substr($url_img, 0, 8) != "https://")) {
				exit("Не верно указана ссылка на баннер!");

			}elseif(is_url($url_img)!="true") {
				exit("Не верно указана ссылка на баннер!");

			}elseif(is_url_img($url_img, "cabinet")!="true") {
				echo is_url_img($url_img, "cabinet");
				exit();

			}elseif($description==false) {
				exit("Не заполнено поле описание уведомления!");

			}else{
				$mysqli->query("UPDATE `tb_notification` SET `url`='$url', `url_img`='$url_img', `title`='$title', `description`='$description' WHERE `id`='$id'") or die($mysqli->error);

				cache_notification();

				exit("OK");
			}

		}elseif($option=="Del") {
			$sql = $mysqli->query("SELECT * FROM `tb_notification` WHERE `id`='$id'");
			if($sql->num_rows>0) {

				$mysqli->query("DELETE FROM `tb_notification` WHERE `id`='$id'") or die("ERROR");

				cache_notification();

				exit("OK");
			}else{
				exit("ERRORNOID");
			}

		}elseif($option=="PlayPause") {
			$sql = $mysqli->query("SELECT * FROM `tb_notification` WHERE `id`='$id'");
			if($sql->num_rows>0) {
				$row = $sql->fetch_array();
				$status = $row["status"];

				if($status == 1) {
					$mysqli->query("UPDATE `tb_notification` SET `status`='0' WHERE `id`='$id'") or die($mysqli->error);
					echo '<span class="adv-play" title="Запустить" onClick="PlayPauseNot('.$row["id"].', \'PlayPause\');"></span>';

					cache_notification();

					exit();
				}elseif($status == 0) {
					$mysqli->query("UPDATE `tb_notification` SET `status`='1' WHERE `id`='$id'") or die($mysqli->error);
					echo '<span class="adv-pause" title="Остановить" onClick="PlayPauseNot('.$row["id"].', \'PlayPause\');"></span>';

					cache_notification();

					exit();
				}else{
					exit("ERROR");
				}
			}else{
				exit("ERRORNOID");
			}

		}else{
			exit("Ошибка! Не удалось обработать запрос!");
		}
	}else{
		exit("Ошибка! Необходимо авторизоваться.");
	}
}else{
	exit("Ошибка! Не удалось обработать запрос.");
}

?>