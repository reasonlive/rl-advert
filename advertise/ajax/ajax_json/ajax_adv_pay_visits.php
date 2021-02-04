<?php 
if( !DEFINED("PAY_VISITS_AJAX") | $type_ads != "pay_visits" ) {
    exit( my_json_encode("ERROR", "Access denied!") );
}

function ListStatus($index = false){
    global $mysqli;
    $reit_user_arr = array(  );
    $sql_s = $mysqli->query("SELECT `id`,`rang`,`r_ot` FROM `tb_config_rang` WHERE `id`>'1' ORDER BY `id` ASC") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql_s->num_rows>0 ) {
        $reit_user_arr[0] = "Все пользователи проекта";
        while( $row_s = $sql_s->fetch_assoc() ) {
            $reit_user_arr[$row_s["id"]] = "С рейтингом <b>" . number_format($row_s["r_ot"], 0, ".", " ") . "</b> и более баллов (<b>" . $row_s["rang"] . "</b>)";
        }
        $sql_s->free();
    }else{
        $reit_user_arr[0] = "Все пользователи проекта";
        $sql_s->free();
    }

    return ($index !== false && isset($reit_user_arr[$index]) ? $reit_user_arr[$index] : $reit_user_arr);
}

$datereg_user_arr = array( "Все пользователи проекта", 3 => "3 дня с момента регистрации", 7 => "7 дней с момента регистрации", 30 => "1 месяц с момента регистрации", 90 => "3 месяца с момента регистрации", 180 => "6 месяцев с момента регистрации", 365 => "1 год с момента регистрации" );
if( $option == "adv-add" ) {
    $token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-add" . $security_key));
    if( $token_post == false | $token_post != $token_check ) {
        $result_text = "ERROR";
        $message_text = "Не верный токен, обновите страницу!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $pvis_min_pay = SqlConfig("pvis_min_pay", 1, 2);
    $pvis_max_pay = SqlConfig("pvis_max_pay", 1, 2);
    $StatusArr = liststatus();
    $title = (isset($_POST["title"]) && is_string($_POST["title"]) ? escape(limitatexto(limpiarez($_POST["title"]), 60)) : false);
    $description = (isset($_POST["description"]) && is_string($_POST["description"]) ? escape(limitatexto(limpiarez($_POST["description"]), 80)) : false);
    $url = (isset($_POST["url"]) && is_string($_POST["url"]) ? escape(limitatexto(limpiarez($_POST["url"]), 300)) : false);
    $hide_httpref = (isset($_POST["hide_httpref"]) && preg_match("|^[0-1]{1}\$|", intval($_POST["hide_httpref"])) ? intval($_POST["hide_httpref"]) : 0);
    $color = (isset($_POST["color"]) && preg_match("|^[0-1]{1}\$|", intval($_POST["color"])) ? intval($_POST["color"]) : 0);
    $revisit = (isset($_POST["revisit"]) && preg_match("|^[0-2]{1}\$|", intval($_POST["revisit"])) ? intval($_POST["revisit"]) : 0);
    $uniq_ip = (isset($_POST["uniq_ip"]) && preg_match("|^[0-2]{1}\$|", intval($_POST["uniq_ip"])) ? intval($_POST["uniq_ip"]) : 0);
    $date_reg_user = (isset($_POST["date_reg_user"]) && array_key_exists(intval($_POST["date_reg_user"]), $datereg_user_arr) !== false ? intval($_POST["date_reg_user"]) : 0);
    $reit_user = (isset($_POST["reit_user"]) && array_key_exists(intval($_POST["reit_user"]), $StatusArr) !== false ? intval($_POST["reit_user"]) : 0);
    $no_ref = (isset($_POST["no_ref"]) && preg_match("|^[0-1]{1}\$|", intval($_POST["no_ref"])) ? intval($_POST["no_ref"]) : 0);
    $sex_user = (isset($_POST["sex_user"]) && preg_match("|^[0-2]{1}\$|", intval($_POST["sex_user"])) ? intval($_POST["sex_user"]) : 0);
    $to_ref = (isset($_POST["to_ref"]) && preg_match("|^[0-2]{1}\$|", intval($_POST["to_ref"])) ? intval($_POST["to_ref"]) : 0);
    $content = (isset($_POST["content"]) && preg_match("|^[0-1]{1}\$|", intval($_POST["content"])) ? intval($_POST["content"]) : 0);
    $country = (isset($_POST["country"]) ? CheckCountry($_POST["country"], $geo_code_name_arr) : array(  ));
    $country_code = (isset($country) && is_array($country) && count($country) != count($geo_code_name_arr) ? implode(", ", array_keys($country)) : false);
    $method_pay = (isset($_POST["method_pay"]) && preg_match("|^[\\d]{1,2}\$|", intval($_POST["method_pay"])) ? intval($_POST["method_pay"]) : 1);
    $method_pay = (isset($method_pay_to[$method_pay]) ? $method_pay : 1);
    $money_add = (isset($_POST["money_add"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["money_add"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["money_add"])), 2, ".", "") : false);
    $black_url = StringUrl($url);
    $sql_bl = $mysqli->query("SELECT `domen` FROM `tb_black_sites` WHERE `domen` IN (" . $black_url . ")") or die( my_json_encode("ERROR", $mysqli->error) );
    $cnt_bl = $sql_bl->num_rows;
    if( $cnt_bl <= 0 ) {
        $sql_bl->free();
    }

    if( $title == false ) {
        $result_text = "ERROR";
        $message_text = "Вы не указали заголовок ссылки";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( strlen($title) < 5 ) {
        $result_text = "ERROR";
        $message_text = "Заголовок ссылки должен содержать минимум 5 символов";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $description == false ) {
        $result_text = "ERROR";
        $message_text = "Вы не указали описание ссылки";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( strlen($description) < 5 ) {
        $result_text = "ERROR";
        $message_text = "Описание ссылки должно содержать минимум 5 символов";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $url == false | $url == "http://" | $url == "https://" ) {
        $result_text = "ERROR";
        $message_text = "Вы не указали URL-адрес сайта";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://" ) {
        $result_text = "ERROR";
        $message_text = "URL-адрес сайта указан неверно";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( is_url($url) != "true" ) {
        $result_text = "ERROR";
        $message_text = "URL-адрес сайта указан неверно, возможно ссылка не существует";
        exit( my_json_encode($result_text, $message_text) );
    }

    if($cnt_bl>0 && $black_url != false )  {
        $row_bl = $sql_bl->fetch_assoc();
        $sql_bl->free();
        $result_text = "ERROR";
        $message_text = "Сайт " . $row_bl["domen"] . " находится в черном списке проекта " . strtoupper($_SERVER["HTTP_HOST"]) . "";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( @getHost($url) != $_SERVER["HTTP_HOST"] && SFB_YANDEX($url) != false )  {
        $result_text = "ERROR";
        $message_text = SFB_YANDEX($url);
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $money_add == false ) {
        $result_text = "ERROR";
        $message_text = "Укажите сумму пополнения";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $money_add < $pvis_min_pay ) {
        $result_text = "ERROR";
        $message_text = "Минимальная сумма пополнения <b>" . number_format($pvis_min_pay, 2, ".", " ") . "</b> руб.";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $pvis_max_pay < $money_add ) {
        $result_text = "ERROR";
        $message_text = "Максимальная сумма пополнения <b>" . number_format($pvis_max_pay, 2, ".", " ") . "</b> руб.";
        exit( my_json_encode($result_text, $message_text) );
    }

    $merch_tran_id = ($method_pay == 10 && $user_name == false ? 0 : true);
    $mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `status`='0' AND `date`<'" . (time() - 24 * 60 * 60) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    $sql_adv = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`status_session_id`) WHERE `status`='0' AND `session_ident`='" . session_id() . "' ORDER BY `id` DESC LIMIT 1") or die( my_json_encode("ERROR", $mysqli->error) );
    
	$pvis_cena_hit = SqlConfig("pvis_cena_hit", 1, 4);
        $pvis_cena_hideref = SqlConfig("pvis_cena_hideref", 1, 4);
        $pvis_cena_color = SqlConfig("pvis_cena_color", 1, 4);
        $pvis_cena_revisit[1] = SqlConfig("pvis_cena_revisit", 1, 4);
        $pvis_cena_revisit[2] = SqlConfig("pvis_cena_revisit", 2, 4);
        $pvis_cena_uniq_ip[1] = SqlConfig("pvis_cena_uniq_ip", 1, 4);
        $pvis_cena_uniq_ip[2] = SqlConfig("pvis_cena_uniq_ip", 2, 4);
        $pvis_cena_revisit[0] = 0;
        $pvis_cena_uniq_ip[0] = 0;
$price_adv = $pvis_cena_hit + $hide_httpref * $pvis_cena_hideref + $color * $pvis_cena_color + $pvis_cena_revisit[$revisit] + $pvis_cena_uniq_ip[$uniq_ip];

	if($sql_adv->num_rows>0 ) {
        $id_adv = $sql_adv->fetch_object()->id;
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `merch_tran_id`='" . $merch_tran_id . "',`method_pay`='" . $method_pay . "',`wmid`='" . $user_wm_id . "',`username`='" . $user_name . "',`date_edit`='" . time() . "',`date_up`=`id`,`title`='" . $title . "',`description`='" . $description . "',`url`='" . $url . "',`hide_httpref`='" . $hide_httpref . "',`color`='" . $color . "',`revisit`='" . $revisit . "',`uniq_ip`='" . $uniq_ip . "',`date_reg_user`='" . $date_reg_user . "',`reit_user`='" . $reit_user . "',`no_ref`='" . $no_ref . "',`sex_user`='" . $sex_user . "',`to_ref`='" . $to_ref . "',`content`='" . $content . "',`geo_targ`='" . $country_code . "',`price_adv`='" . $price_adv . "',`balance`='0',`money`='" . $money_add . "',`ip`='" . $my_lastiplog . "' WHERE `id`='" . $id_adv . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    }else{
        $mysqli->query("INSERT INTO `tb_ads_pay_vis` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_edit`,`title`,`description`,`url`,`hide_httpref`,`color`,`revisit`,`uniq_ip`,`date_reg_user`,`reit_user`,`no_ref`,`sex_user`,`to_ref`,`content`,`geo_targ`,`price_adv`,`balance`,`money`,`ip`) VALUES('0','" . session_id() . "','" . $merch_tran_id . "','" . $method_pay . "','" . $user_wm_id . "','" . $user_name . "','" . time() . "','" . time() . "','" . $title . "','" . $description . "','" . $url . "','" . $hide_httpref . "','" . $color . "','" . $revisit . "','" . $uniq_ip . "','" . $date_reg_user . "','" . $reit_user . "','" . $no_ref . "','" . $sex_user . "','" . $to_ref . "','" . $content . "','" . $country_code . "','" . $price_adv . "','0','" . $money_add . "','" . $my_lastiplog . "')") or die( my_json_encode("ERROR", $mysqli->error) );
        $id_adv = $mysqli->insert_id;
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `date_up`='" . $id_adv . "' WHERE `id`='" . $id_adv . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    }

    $token_next_del = strtolower(md5($id_adv . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-del" . $security_key));
    $token_next_pay = strtolower(md5($id_adv . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-pay" . $security_key));
    $hide_httpref_txt[0] = "Нет";
    $hide_httpref_txt[1] = "Да";
    $color_txt[0] = "Нет";
    $color_txt[1] = "Да";
    $revisit_txt[0] = "Каждые 24 часа";
    $revisit_txt[1] = "Каждые 48 часов";
    $revisit_txt[2] = "1 раз в месяц";
    $uniq_ip_txt[0] = "Нет";
    $uniq_ip_txt[1] = "Да (100% совпадение)";
    $uniq_ip_txt[2] = "Усиленный по маске до 2 чисел (255.255.X.X)";
    $datereg_user_txt = (isset($datereg_user_arr[$date_reg_user]) ? $datereg_user_arr[$date_reg_user] : $datereg_user_arr[0]);
    $reit_user_txt = (isset($StatusArr[$reit_user]) ? $StatusArr[$reit_user] : $StatusArr[0]);
    $no_ref_txt[0] = "Все пользователи проекта";
    $no_ref_txt[1] = "Пользователям без реферера на <b>" . $_SERVER["HTTP_HOST"] . "</b>";
    $no_ref_txt[2] = "1 раз в месяц";
    $sex_user_txt[0] = "Все пользователи проекта";
    $sex_user_txt[1] = "Только мужчины";
    $sex_user_txt[2] = "Только женщины";
    $to_ref_txt[0] = "Все пользователи проекта";
    $to_ref_txt[1] = "Рефералам 1-го уровня";
    $to_ref_txt[2] = "Рефералам всех уровней";
    $content_txt[0] = "Нет";
    $content_txt[1] = "Да";
    $country_code_arr = (isset($country_code) && $country_code != false ? explode(", ", $country_code) : array(  ));
    if( 0 < count($country_code_arr) )  {
        $geo_targ_txt = false;
        for( $i = 0; $i < count($country_code_arr); $i++ )  {
            $geo_targ_txt .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code_arr[$i]) . ".gif\" alt=\"" . $country_code_arr[$i] . "\" title=\"" . $geo_code_name_arr[$country_code_arr[$i]] . "\" align=\"absmiddle\" style=\"margin:0; padding:0 2px 0;\" />";
        }
        $geo_targ_txt = ($geo_targ_txt != false ? $geo_targ_txt : "Все страны");
    }else{
        $geo_targ_txt = "Все страны";
    }

    if( $method_pay == 8 ) {
        $money_add_ym = ($money_add * 0.005 < 0.01 ? $money_add + 0.01 : $money_add * 1.005);
        $money_add_ym = number_format($money_add_ym, 2, ".", "");
        $money_pay = $money_add_ym;
    }else{
        if( $method_pay == 3 ) {
            $money_add_w1 = number_format($money_add * 1.05, 2, ".", "");
            $money_pay = $money_add_w1;
        }else{
            $money_pay = $money_add;
        }

    }

    $shp_item = "32";
    $inv_desc = "Реклама: оплачиваемые посещения, Счет:" . $merch_tran_id . ", ID:" . $id_adv;
    $inv_desc_utf8 = iconv("CP1251", "UTF-8", $inv_desc);
    $inv_desc_en = "Pay advertise: pay visits, Order:" . $merch_tran_id . ", ID:" . $id_adv;
    $money_add = number_format($money_add, 2, ".", "");
    if( $method_pay == 7 ) {
        if( !isset($CURS_USD) ) {
            require_once(ROOT_DIR . "/curs/curs.php");
        }

        $money_pay_usd = number_format(round($money_add / $CURS_USD, 2), 2, ".", "");
    }

    $message_text = false;
    $message_text .= "<table class=\"tables\">";
    $message_text .= "<thead><tr><th align=\"center\" colspan=\"2\">Информация о заказе</th></tr></thead>";
    $message_text .= "<tr><td width=\"190\">Счет №</td><td>" . $merch_tran_id . "</td></tr>";
    $message_text .= "<tr><td>ID рекламы</td><td>" . $id_adv . "</td></tr>";
    $message_text .= "<tr><td>Заголовок ссылки</td><td>" . $title . "</td></tr>";
    $message_text .= "<tr><td>Описание ссылки</td><td>" . $description . "</td></tr>";
    $message_text .= "<tr><td>URL сайта</td><td><a href=\"" . $url . "\" target=\"_blank\">" . $url . "</a></td></tr>";
    $message_text .= "<tr><td>Скрыть HTTP_REFERER</td><td>" . $hide_httpref_txt[$hide_httpref] . "</td></tr>";
    $message_text .= "<tr><td>Выделить цветом</td><td>" . $color_txt[$color] . "</td></tr>";
    $message_text .= "<tr><td>Доступно для просмотра</td><td>" . $revisit_txt[$revisit] . "</td></tr>";
    $message_text .= "<tr><td>Уникальный IP</td><td>" . $uniq_ip_txt[$uniq_ip] . "</td></tr>";
    $message_text .= "<tr><td>По дате регистрации</td><td>" . $datereg_user_txt . "</td></tr>";
    $message_text .= "<tr><td>По рейтингу пользователя</td><td>" . $reit_user_txt . "</td></tr>";
    $message_text .= "<tr><td>По наличию реферера</td><td>" . $no_ref_txt[$no_ref] . "</td></tr>";
    $message_text .= "<tr><td>По половому признаку</td><td>" . $sex_user_txt[$sex_user] . "</td></tr>";
    $message_text .= "<tr><td>Показывать только рефералам</td><td>" . $to_ref_txt[$to_ref] . "</td></tr>";
    $message_text .= "<tr><td>Контент 18+</td><td>" . $content_txt[$content] . "</td></tr>";
    $message_text .= "<tr><td>Геотаргетинг</td><td>" . $geo_targ_txt . "</td></tr>";
    $message_text .= "<tr><td>Цена одного посещения</td><td><span class=\"text-green\">" . number_format($price_adv, 4, ".", "") . " руб.</span></td></tr>";
    $message_text .= "<tr><td>Сумма пополнения</td><td><span class=\"text-green\"><b>" . number_format($money_add, 2, ".", " ") . "</b> руб.</span></td></tr>";
    $message_text .= "<tr><td><b>Способ оплаты</b></td><td><b class=\"text-grey\">" . $method_pay_to[$method_pay] . "</b>, счет необходимо оплатить в течении 24 часов</td></tr>";
    $message_text .= "<tr><td><b>Сумма к оплате</b></td><td>" . (($method_pay == 7 && isset($money_pay_usd) ? "<b class=\"text-red\">" . number_format($money_pay_usd, 2, ".", " ") . "</b> USD" : "<b class=\"text-red\">" . number_format($money_pay, 2, ".", " ") . "</b> руб.")) . "</td></tr>";
    $message_text .= "<tr>";
    $message_text .= "<td align=\"center\" colspan=\"2\">";
    if( $method_pay == 10 ) {
        $message_text .= "<span onClick=\"FuncAdv('" . $id_adv . "', 'adv-pay', '" . $type_ads . "', false, '" . $token_next_pay . "', 'modal'); return false;\" class=\"sd_sub big green\" title=\"Оплатить заказ с рекламного счёта\">Оплатить</span>";
    }else{
        require_once(ROOT_DIR . "/method_pay/method_pay_json.php");
    }

    $message_text .= "<span onClick=\"ChangeAds();\" class=\"sd_sub big blue\" title=\"Редактировать параметры рекламной площадки\">Изменить</span>";
    $message_text .= "<span onClick=\"FuncAdv('" . $id_adv . "', 'adv-del', '" . $type_ads . "', false, '" . $token_next_del . "', 'modal'); return false;\" class=\"sd_sub big red\" title=\"Отменить и удалить заказ рекламы\">Удалить</span>";
    $message_text .= "</td>";
    $message_text .= "</tr>";
    $message_text .= "</table>";
    $result_text = "OK";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "adv-del" ) {
    $sql = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`status_session_id`) WHERE `id`='" . $id . "' AND `status`='0' AND `session_ident`='" . session_id() . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-del" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $result_text = "OK";
        $message_text = "Реклама удалена!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Реклама не найдена, возможно она уже была удалена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "adv-pay" ) {
    if( $user_name == false ) {
        $result_text = "ERROR";
        $message_text = "Для оплаты с рекламного счета необходимо авторизоваться!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql = $mysqli->query("SELECT `id`,`money`,`merch_tran_id` FROM `tb_ads_pay_vis` USE INDEX (`status_user_id`) WHERE `id`='" . $id . "' AND `status`='0' AND `username`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $money_pay = $row["money"];
        $merch_tran_id = $row["merch_tran_id"];
        $sql->free();
        $pvis_min_pay = SqlConfig("pvis_min_pay", 1, 2);
        $pvis_max_pay = SqlConfig("pvis_max_pay", 1, 2);
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-pay" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $money_pay == false | $money_pay == 0 ) {
            $result_text = "ERROR";
            $message_text = "Не указана сумма пополнения";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $money_pay < $pvis_min_pay ) {
            $result_text = "ERROR";
            $message_text = "Минимальная сумма пополнения <b>" . number_format($pvis_min_pay, 2, ".", " ") . "</b> руб.";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $pvis_max_pay < $money_pay ) {
            $result_text = "ERROR";
            $message_text = "Максимальная сумма пополнения <b>" . number_format($pvis_max_pay, 2, ".", " ") . "</b> руб.";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $user_money_rb < $money_pay ) {
            $result_text = "ERROR";
            $message_text = "На вашем рекламном счету <b>" . p_floor($user_money_rb, 2) . "</b> руб.<br>Для оплаты заказа необходимо <b>" . number_format($money_pay, 2, ".", " ") . "</b> руб.";
            exit( my_json_encode($result_text, $message_text) );
        }

        $sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_rek'") or die( my_json_encode("ERROR", $mysqli->error) );
        $reit_rek = ($sql->num_rows>0 ? $sql->fetch_object()->price : 0);
        $sql->free();
        $sql = $mysqli->query("SELECT `price` FROM `tb_config` WHERE `item`='reit_ref_rek'") or die( my_json_encode("ERROR", $mysqli->error) );
        $reit_ref_rek = ($sql->num_rows>0 ? $sql->fetch_object()->price : 0);
        $sql->free();
        $reit_add_1 = floor($money_pay / 10) * $reit_rek;
        $reit_add_2 = floor($money_pay / 10) * $reit_ref_rek;
        if( $user_referer_1 != false ) {
            $mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'" . $reit_add_2 . "' WHERE `username`='" . $user_referer_1 . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        }

        $mysqli->query("UPDATE `tb_users` SET `reiting`=`reiting`+'" . $reit_add_1 . "',`money_rb`=`money_rb`-'" . $money_pay . "',`money_rek`=`money_rek`+'" . $money_pay . "' WHERE `username`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`method_pay`='10',`date_edit`='" . time() . "',`wmid`='" . $user_wm_id . "',`balance`='" . $money_pay . "' WHERE `id`='" . $id . "' AND `status`='0' AND `username`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $mysqli->query("INSERT INTO `tb_history` (`status_pay`,`user`,`user_id`,`date`,`time`,`amount`,`method`,`status`,`tipo`) VALUES('1','" . $user_name . "','" . $user_id . "','" . DATE("d.m.Y H:i") . "','" . time() . "','" . $money_pay . "','Оплата рекламы (опл. посещения, ID:<b>" . $id . "</b>)','Оплачено','reklama')") or die( my_json_encode("ERROR", $mysqli->error) );
        $sum_user_ob = p_floor($user_money_ob, 4);
        $sum_user_rb = p_floor($user_money_rb - $money_pay, 4);
        stat_pay("pay_visits", $money_pay);
        ads_wmid($user_wm_id, $user_wm_purse, $user_name, $money_pay);
        //BonusSurf($user_name, $money_pay);
        $result_text = "OK";
        $message_text = "<script>\$(\"#my_bal_ob\").html('" . $sum_user_ob . "'); \$(\"#my_bal_rb\").html('" . $sum_user_rb . "');</script>";
        $message_text .= "<div class=\"text-green\" style=\"text-align:center; font-weight:bold;\">Ваша реклама успешно размещена!<br>Спасибо, что пользуетесь услугами нашего сервиса!</div>";
        $message_text .= "<div style=\"padding-top:10px; text-align:center;\"><span onClick=\"location.href = '/cabinet_ads?ads=" . $type_ads . "'\" class=\"sd_sub big red\">Перейти в кабинет управления рекламой</span></div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Заказа рекламы с <b>№" . $id . "</b> не существует, либо заказ уже был оплачен!";
    exit( my_json_encode($result_text, $message_text) );
}

?>