<?php
error_reporting(E_ALL);
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");
require_once(ROOT_DIR."/merchant/func_cache.php");

if(!DEFINED("ARTICLES_AJAX")) {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));

}elseif($type_ads != "articles") {
	$result_text = "ERROR"; $message_text = "Hacking attempt!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

$message_text = "";

$sql_p = $mysqli->query("SELECT `sitewmr` FROM `tb_site` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$site_wmr = $sql_p->fetch_object()->sitewmr;

$sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='cena_articles' AND `howmany`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
$cena_articles = number_format($sql->fetch_object()->price, 2, ".", "");


if($option == "Delete") {
	$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_check->num_rows>0) {
		$mysqli->query("DELETE FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

		cache_articles();

		$result_text = "OK"; $message_text = "Статья удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Статья с ID:$id не найдена, возможно она уже была удалена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Add") {
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 100) : false;
	$desc_min = isset($_POST["desc_min"]) ? limitatexto(limpiarez($_POST["desc_min"]), 1000) : false;
	$desc_min = get_magic_quotes_gpc() ? stripslashes($desc_min) : $desc_min;
	$desc_big = isset($_POST["desc_big"]) ? limitatexto(limpiarez($_POST["desc_big"]), 5000) : false;
	$desc_big = get_magic_quotes_gpc() ? stripslashes($desc_big) : $desc_big;
	$method_pay = 0;
	$black_url = getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

	if($title == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали заголовок статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($sql_bl->num_rows > 0 && $black_url != false) {
		$row_bl = $sql_bl->fetch_assoc();
		$result_text = "ERROR"; $message_text = "Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($url == false | $url == "http://" | $url == "https://") {
		$result_text = "ERROR"; $message_text = "Вы не указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		$result_text = "ERROR"; $message_text = "Вы неверно указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($desc_min == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали краткое описание статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(strlen($desc_min) < 50) {
		$result_text = "ERROR"; $message_text = "В кратком описание статьи должно быть не менее 50 символов!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($desc_big == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали описание статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(strlen($desc_big) < 100) {
		$result_text = "ERROR"; $message_text = "В описание статьи должно быть не менее 100 символов!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$summa = number_format($cena_articles, 2, ".", "");

		$sql_tranid = $mysqli->query("SELECT `merch_tran_id` FROM `tb_statistics` WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$merch_tran_id = $sql_tranid->fetch_object()->merch_tran_id;

		$mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		$mysqli->query("DELETE FROM `tb_ads_articles` WHERE `status`='0' AND `date`<'".(time()-(24*60*60))."'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='0' AND `session_ident`='".session_id()."' ORDER BY `id` DESC LIMIT 1") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_check->num_rows>0) {
			$id_zakaz = $sql_check->fetch_object()->id;

			$mysqli->query("UPDATE `tb_ads_articles` SET `merch_tran_id`='$merch_tran_id',`method_pay`='$method_pay',`wmid`='$user_wmid',`username`='$user_name',`date_edit`='".time()."',`title`='$title',`url`='$url',`desc_min`='$desc_min',`desc_big`='$desc_big',`money`='$summa',`ip`='$my_lastiplog' WHERE `id`='$id_zakaz'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}else{
			$mysqli->query("INSERT INTO `tb_ads_articles` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_edit`,`title`,`url`,`desc_min`,`desc_big`,`money`,`ip`) 
			VALUES('0','".session_id()."','$merch_tran_id','$method_pay','$user_wmid','$user_name','".time()."','".time()."','$title','$url','$desc_min','$desc_big','$summa','$my_lastiplog')") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			$id_zakaz = $mysqli->query("SELECT LAST_INSERT_ID() AS last_id FROM `tb_ads_articles`")->fetch_object()->last_id;

			$mysqli->query("UPDATE `tb_ads_articles` SET `up_list`='$id_zakaz' WHERE `id`='$id_zakaz'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}

        	$sql_id = $mysqli->query("SELECT `id`,`url`,`desc_min`,`desc_big`,`date`,`views` FROM `tb_ads_articles` WHERE `id`='$id_zakaz' AND `status`='0'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_id->num_rows>0) {
			$row_id = $sql_id->fetch_assoc();
		        $id_zakaz = $row_id["id"];
		        $art_url = $row_id["url"];
		        $art_date = $row_id["date"];
		        $art_views = $row_id["views"];
		        $desc_min_to = desc_bb($row_id["desc_min"]);
			$desc_big_to = desc_bb($row_id["desc_big"]);
		}else{
		        $art_url = $url;
		        $art_date = time();
		        $art_views = 0;
		        $desc_min_to = desc_bb($desc_min);
			$desc_big_to = desc_bb($desc_big);
		}

		$message_text.= '<div id="PreView" style="display:block;">';

			$message_text.= '<h3 class="sp">Предварительный просмотр статьи</h3>';
			$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
				$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$title.'</div>';
				$message_text.= '<div align="center" style="margin:7px auto 0px auto; font-size:13px; color:#828282; text-shadow: 1px 1px 2px #FFF;">Краткое содержание статьи</div>';
				$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$desc_min_to.'</div>';
				$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url.'" target="_blank" class="golinktest">'.$art_url.'</a></div>';

				$message_text.= '<div style="padding:0px 10px 30px 10px;">';
					$message_text.= '<span class="art-calendar">дата создания '.DATE("d.m.Y в H:i").'</span>';
					$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';

			$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
				$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$title.'</div>';
				$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$desc_big_to.'</div>';
				$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url.'" target="_blank" class="golinktest">'.$art_url.'</a></div>';

				$message_text.= '<div style="padding:0px 10px 30px 10px;">';
					$message_text.= '<span class="art-calendar">дата создания '.DATE("d.m.Y в H:i").'</span>';
					$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';

			$message_text.= '<div align="center" style="margin:5px auto 10px auto;">';
				$message_text.= '<span onClick="AddAdv(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-blue" style="float:none; display:inline-block;">Опубликовать</span>';
				$message_text.= '<span onClick="ChangeAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-green" style="float:none; display:inline-block;">Изменить</span>';
				$message_text.= '<span onClick="DeleteAds(\''.$id_zakaz.'\', \''.$type_ads.'\');" class="sub-red" style="float:none; display:inline-block;">Удалить</span>';
			$message_text.= '</div>';

		$message_text.= '</div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Start") {
	$sql_id = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_id->num_rows>0) {
		$row = $sql_id->fetch_assoc();
		$status = $row["status"];
		$money_pay = $row["money"];
		$merch_tran_id = $row["merch_tran_id"];

		if($status==2) {
			$mysqli->query("UPDATE `tb_ads_articles` SET `status`='1', `date_edit`='".time()."',`title_h`='', `url_h`='', `desc_min_h`='', `desc_big_h`='' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}else{
			$mysqli->query("UPDATE `tb_ads_articles` SET `status`='1', `date_edit`='".time()."' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		}

		cache_articles();

		$result_text = "OK"; $message_text = "Статья успешно опубликована!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Заказа рекламы с ID:$id не существует, либо заказ уже был оплачен!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}


}elseif($option == "GetInfo") {
       	$sql_id = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql_id->num_rows>0) {
		$row_id = $sql_id->fetch_assoc();
	        $art_status = $row_id["status"];
	        $art_id = $row_id["id"];
	        $art_title = $row_id["title"];
	        $art_url = $row_id["url"];
	        $art_date = $row_id["date"];
	        $art_date_edit = $row_id["date_edit"];
	        $art_views = $row_id["views"];
	        $art_desc_min = desc_bb($row_id["desc_min"]);
		$art_desc_big = desc_bb($row_id["desc_big"]);

	        $art_title_h = $row_id["title_h"];
	        $art_url_h = $row_id["url_h"];
	        $art_desc_min_h = desc_bb($row_id["desc_min_h"]);
		$art_desc_big_h = desc_bb($row_id["desc_big_h"]);

		$message_text.= '<div id="PreView" style="display:block; margin:5px; color: #333333; font-size:12px;">';

			if($art_title_h!= false | $art_url_h!=false | $art_desc_min_h!=false | $art_desc_big_h!=false) {
				$message_text.= '<h4 class="sp">Статья перед редактированием</h1>';

				$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; text-align:justify; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
					$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$art_title_h.'</div>';
					$message_text.= '<div align="center" style="margin:7px auto 0px auto; font-size:13px; color:#828282; text-shadow: 1px 1px 2px #FFF;">Краткое содержание статьи</div>';
					$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$art_desc_min_h.'</div>';
					$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url_h.'" target="_blank" class="golinktest">'.$art_url_h.'</a></div>';

					$message_text.= '<div style="padding:0px 10px 30px 10px;">';
						$message_text.= '<span class="art-calendar">дата создания '.DATE("d.m.Y в H:i", $art_date).'</span>';
						$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
					$message_text.= '</div>';
				$message_text.= '</div>';

				$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; text-align:justify; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
					$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$art_title_h.'</div>';
					$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$art_desc_big_h.'</div>';
					$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url_h.'" target="_blank" class="golinktest">'.$art_url_h.'</a></div>';

					$message_text.= '<div style="padding:0px 10px 30px 10px;">';
						$message_text.= '<span class="art-calendar">дата создания '.DATE("d.m.Y в H:i", $art_date).'</span>';
						$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
					$message_text.= '</div>';
				$message_text.= '</div>';

				$message_text.= '<h4 class="sp">Статья после редактирования</h1>';
			}

			$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; text-align:justify; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
				$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$art_title.'</div>';
				$message_text.= '<div align="center" style="margin:7px auto 0px auto; font-size:13px; color:#828282; text-shadow: 1px 1px 2px #FFF;">Краткое содержание статьи</div>';
				$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$art_desc_min.'</div>';
				$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url.'" target="_blank" class="golinktest">'.$art_url.'</a></div>';

				$message_text.= '<div style="padding:0px 10px 30px 10px;">';
					$message_text.= '<span class="art-calendar">дата редактирования '.DATE("d.m.Y в H:i", $art_date_edit).'</span>';
					$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';

			$message_text.= '<div style="margin:0 auto 20px auto; padding: 8px 1px 0px 1px; text-align:justify; background-color:#F9F9F9; border-radius:5px 5px 10px 10px; box-shadow: 0 0 0 1px rgb(194, 192, 184) inset, 0 5px 0 -4px rgb(255, 255, 255), 0 5px 0 -3px rgb(194, 192, 184), 0 11px 0 -8px rgb(255, 255, 255), 0 11px 0 -7px rgb(194, 192, 184), 0 17px 0 -12px rgb(255, 255, 255), 0 17px 0 -11px rgb(194, 192, 184);">';
				$message_text.= '<div class="test-blank-title" style="width:100%; margin:0 auto; border-radius:0px; text-shadow: 1px 1px 1px #000;">'.$art_title.'</div>';
				$message_text.= '<div style="padding:8px 10px 15px 10px;">'.$art_desc_big.'</div>';
				$message_text.= '<div style="padding:0px 10px 15px 10px;">Ссылка на сайт: <a href="'.$art_url.'" target="_blank" class="golinktest">'.$art_url.'</a></div>';

				$message_text.= '<div style="padding:0px 10px 30px 10px;">';
					$message_text.= '<span class="art-calendar">дата редактирования '.DATE("d.m.Y в H:i", $art_date_edit).'</span>';
					$message_text.= '<span class="art-eye">'.count_text(number_format($art_views, 0, ".", "`"), "просмотр", "просмотра", "просмотров").'</span>';
				$message_text.= '</div>';
			$message_text.= '</div>';

		$message_text.= '</div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Статья с ID:$id не найдена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "GoLock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];

		if($status==4) {
			$message_text.= '<div align="left" style="padding-left:40px;"><span style="color:#4F4F4F;">Заблокировал:</span> <b>'.$row["user_lock"].'</b></div>';
			$message_text.= '<div align="left" style="padding-left:40px;"><span style="color:#4F4F4F;">Причина блокировки:</span> <b>'.$row["msg_lock"].'</b></div>';

			$result_text = "OK";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}else{
			$message_text = '';
			$message_text.= '<div align="center" style="float:left; padding:9px 5px; font-weight:bold;">Укажите причину блокировки:</div>';
			$message_text.= '<div id="newform" align="center" style="float:left; width:calc(100% - 300px); padding:5px 5px 3px 0px;"><input class="ok" type="text" id="msg_lock" value="" maxlength="255" autocomplete="off" onKeyDown="$(this).attr(\'class\', \'ok\');" /></div>';
			$message_text.= '<div align="center" style="float:left; padding-left:5px; padding-top:6px;"><span onClick="Lock(\''.$row["id"].'\', \'articles\', \'Lock\');" class="sub-red" style="float:none;" title="Заблокировать">Заблокировать</span></div>';

			$result_text = "OK";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Статья с ID:$id не найдена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Lock") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$status = $row["status"];
		$msg_lock = (isset($_POST["msg_lock"])) ? limitatexto(limpiarez($_POST["msg_lock"]), 255) : false;

		if($status==4) {
			$result_text = "ERROR"; $message_text = "Статья уже заблокирована!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));

		}elseif($msg_lock==false) {
			$result_text = "ERROR"; $message_text = "Укажите причину блокировки!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));

		}else{
			$mysqli->query("UPDATE `tb_ads_articles` SET `status`='4', `date_edit`='".time()."', `user_lock`='$user_name', `msg_lock`='$msg_lock' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			cache_articles();

			$result_text = "OK"; $message_text = "Статья успешно заблокирована!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Статья с ID:$id не найдена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}


}elseif($option == "LoadForm") {
	$sql = $mysqli->query("SELECT * FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	if($sql->num_rows>0) {
		$row = $sql->fetch_assoc();

		$message_text.= '<div id="newform" onkeypress="CtrlEnter(event);">';
			$message_text.= '<table class="tables" style="border:none; margin:0; padding:10px; width:100%;">';
			$message_text.= '<thead><tr><th align="center" colspan="2">Редактирование статьи ID:'.$id.'</th></thead></tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left" width="220"><b>Заголовок статьи</b></td>';
				$message_text.= '<td align="left"><input type="text" id="title" maxlength="100" value="'.$row["title"].'" class="ok"></td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left"><b>URL сайта</b> (включая http://)</td>';
				$message_text.= '<td align="left"><input type="text" id="url" maxlength="300" value="'.$row["url"].'" class="ok"></td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="center" colspan="3"><b>Краткое описание статьи</b></td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left" colspan="2">';
					$message_text.= '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_min\'); return false;">Ж</span>';
					$message_text.= '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_min\'); return false;">К</span>';
					$message_text.= '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_min\'); return false;">Ч</span>';
					$message_text.= '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_min\'); return false;">ST</span>';
					$message_text.= '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_min\'); return false;"></span>';
					$message_text.= '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_min\'); return false;"></span>';
					$message_text.= '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_min\'); return false;"></span>';
					$message_text.= '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_min\'); return false;"></span>';
					$message_text.= '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_min\'); return false;">URL</span>';
					$message_text.= '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_min\'); return false;">IMG</span>';
					$message_text.= '<span id="count1" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:8px;">Осталось символов: 1000</span>';
					$message_text.= '<br>';
					$message_text.= '<div style="display: block; clear:both; padding-top:4px">';
						$message_text.= '<textarea id="desc_min" class="ok" style="height:120px; width:99%;" onKeyup="descchange(\'1\', this, \'1000\');" onKeydown="descchange(\'1\', this, \'1000\');" onClick="descchange(\'1\', this, \'1000\');">'.$row["desc_min"].'</textarea>';
					$message_text.= '</div>';
				$message_text.= '</td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="center" colspan="2"><b>Описание статьи</b></td>';
			$message_text.= '</tr>';
			$message_text.= '<tr>';
				$message_text.= '<td align="left" colspan="2">';
					$message_text.= '<span class="bbc-bold" style="float:left;" title="Выделить жирным" onClick="javascript:InsertTags(\'[b]\',\'[/b]\', \'desc_big\'); return false;">Ж</span>';
					$message_text.= '<span class="bbc-italic" style="float:left;" title="Выделить курсивом" onClick="javascript:InsertTags(\'[i]\',\'[/i]\', \'desc_big\'); return false;">К</span>';
					$message_text.= '<span class="bbc-uline" style="float:left;" title="Выделить подчёркиванием" onClick="javascript:InsertTags(\'[u]\',\'[/u]\', \'desc_big\'); return false;">Ч</span>';
					$message_text.= '<span class="bbc-tline" style="float:left;" title="Перечеркнутый текст" onClick="javascript:InsertTags(\'[s]\',\'[/s]\', \'desc_big\'); return false;">ST</span>';
					$message_text.= '<span class="bbc-left" style="float:left;" title="Выровнять по левому краю" onClick="javascript:InsertTags(\'[left]\',\'[/left]\', \'desc_big\'); return false;"></span>';
					$message_text.= '<span class="bbc-center" style="float:left;" title="Выровнять по центру" onClick="javascript:InsertTags(\'[center]\',\'[/center]\', \'desc_big\'); return false;"></span>';
					$message_text.= '<span class="bbc-right" style="float:left;" title="Выровнять по правому краю" onClick="javascript:InsertTags(\'[right]\',\'[/right]\', \'desc_big\'); return false;"></span>';
					$message_text.= '<span class="bbc-justify" style="float:left;" title="Выровнять по ширине" onClick="javascript:InsertTags(\'[justify]\',\'[/justify]\', \'desc_big\'); return false;"></span>';
					$message_text.= '<span class="bbc-url" style="float:left;" title="Выделить URL" onClick="javascript:InsertTags(\'[url]\',\'[/url]\', \'desc_big\'); return false;">URL</span>';
					$message_text.= '<span class="bbc-url" style="float:left;" title="Добавить изображение" onClick="javascript:InsertTags(\'[img]\',\'[/img]\', \'desc_big\'); return false;">IMG</span>';
					$message_text.= '<span id="count2" style="display: block; float:right; color:#696969; margin-top:2px; margin-right:8px;">Осталось символов: 5000</span>';
					$message_text.= '<br>';
					$message_text.= '<div style="display: block; clear:both; padding-top:4px">';
						$message_text.= '<textarea id="desc_big" class="ok" style="height:200px; width:99%;" onKeyup="descchange(\'2\', this, \'5000\');" onKeydown="descchange(\'2\', this, \'5000\');" onClick="descchange(\'2\', this, \'5000\');">'.$row["desc_big"].'</textarea>';
					$message_text.= '</div>';
				$message_text.= '</td>';
			$message_text.= '</tr>';
			$message_text.= '</table>';
		$message_text.= '</div>';

		$message_text.= '<div align="center"><span id="Save" onClick="SaveAds(\''.$id.'\', \'articles\', \'Save\');" class="sub-blue160" style="float:none; width:160px;">Сохранить изменения</span></div>';

		$result_text = "OK";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}else{
		$result_text = "ERROR"; $message_text = "Ошибка! Статья с ID:$id не найдена!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));
	}

}elseif($option == "Save") {
	$url = (isset($_POST["url"])) ? limitatexto(limpiarez($_POST["url"]), 300) : false;
	$title = (isset($_POST["title"])) ? limitatexto(limpiarez($_POST["title"]), 100) : false;
	$desc_min = isset($_POST["desc_min"]) ? limitatexto(limpiarez($_POST["desc_min"]), 1000) : false;
	$desc_min = get_magic_quotes_gpc() ? stripslashes($desc_min) : $desc_min;
	$desc_big = isset($_POST["desc_big"]) ? limitatexto(limpiarez($_POST["desc_big"]), 5000) : false;
	$desc_big = get_magic_quotes_gpc() ? stripslashes($desc_big) : $desc_big;
	$black_url = getHost($url);

	$sql_bl = $mysqli->query("SELECT * FROM `tb_black_sites` WHERE `domen`='$black_url'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

	if($title == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали заголовок статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($sql_bl->num_rows > 0 && $black_url != false) {
		$row_bl = $sql_bl->fetch_assoc();
		$result_text = "ERROR"; $message_text = "Сайт ".$row_bl["domen"]." заблокирован и занесен в черный список проекта ".strtoupper($_SERVER["HTTP_HOST"])." Причина: ".$row_bl["cause"]."";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($url == false | $url == "http://" | $url == "https://") {
		$result_text = "ERROR"; $message_text = "Вы не указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif((substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")) {
		$result_text = "ERROR"; $message_text = "Вы неверно указали URL-адрес сайта!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($desc_min == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали краткое описание статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(strlen($desc_min) < 50) {
		$result_text = "ERROR"; $message_text = "В кратком описание статьи должно быть не менее 50 символов!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif($desc_big == false) {
		$result_text = "ERROR"; $message_text = "Вы не указали описание статьи!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}elseif(strlen($desc_big) < 100) {
		$result_text = "ERROR"; $message_text = "В описание статьи должно быть не менее 100 символов!";
		exit(my_json_encode($ajax_json, $result_text, $message_text));

	}else{
		$sql_check = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
		if($sql_check->num_rows>0) {

			$mysqli->query("UPDATE `tb_ads_articles` SET `date_edit`='".time()."',`title`='$title',`url`='$url',`desc_min`='$desc_min',`desc_big`='$desc_big' WHERE `id`='$id'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));

			cache_articles();

			$result_text = "OK"; $message_text = "$title";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}else{
			$result_text = "ERROR"; $message_text = "Ошибка! Статья с ID:$id не найдена!";
			exit(my_json_encode($ajax_json, $result_text, $message_text));
		}
	}

}elseif($option == "StatMenu") {

	$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='0'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	$count_articles[0] = $sql_articles->num_rows;

	$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='1'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	$count_articles[1] = $sql_articles->num_rows;

	$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='2'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	$count_articles[2] = $sql_articles->num_rows;

	$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='4'") or die(my_json_encode($ajax_json, "ERROR", $mysqli->error));
	$count_articles[4] = $sql_articles->num_rows;

	$result_text = "OK";
	$message_text = '{"count_req":"'.$count_articles[0].'", "count_edit":"'.$count_articles[1].'", "count_moder":"'.$count_articles[2].'", "count_ban":"'.$count_articles[4].'"}';
	exit(my_json_encode($ajax_json, $result_text, $message_text));

}else{
	$result_text = "ERROR"; $message_text = "ERROR: NO OPTION!";
	exit(my_json_encode($ajax_json, $result_text, $message_text));
}

?>