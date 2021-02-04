<?php 
if( !DEFINED("ADMINKA") | !DEFINED("PAY_VISITS_AJAX") ) {
    exit( my_json_encode("ERROR", "Access denied!") );
}

function ListStatus($index = false){
    global $mysqli;
    $status_arr = array(  );
    $sql_s = $mysqli->query("SELECT `id`,`rang`,`r_ot` FROM `tb_config_rang` WHERE `id`>'1' ORDER BY `id` ASC") or die( my_json_encode("ERROR", $mysqli->error) );
    if( 0 < $sql_s->num_rows ) {
        $status_arr[0] = "Все пользователи проекта";
        while( $row_s = $sql_s->fetch_assoc() ) {
            $status_arr[$row_s["id"]] = "С рейтингом <b>" . number_format($row_s["r_ot"], 0, ".", " ") . "</b> и более баллов (<b>" . $row_s["rang"] . "</b>)";
        }
        $sql_s->free();
    }else{
        $status_arr[0] = "Все пользователи проекта";
        $sql_s->free();
    }

    return ($index !== false && isset($status_arr[$index]) ? $status_arr[$index] : $status_arr);
}

$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='3',`date_end`='" . time() . "' WHERE `status`='1' AND `balance`<`price_adv`") or die( my_json_encode("ERROR", $mysqli->error) );
$mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1' WHERE `status`='3' AND `balance`>=`price_adv`") or die( my_json_encode("ERROR", $mysqli->error) );
$hide_httpref_arr = array( "Нет", "Да" );
$color_arr = array( "Нет", "Да" );
$revisit_arr = array( "Каждые 24 часа", "Каждые 48 часов", "1 раз в месяц" );
$uniq_ip_arr = array( "Нет", "Да (100% совпадение)", "Усиленный по маске до 2 чисел (255.255.X.X)" );
$datereg_user_arr = array( "Все пользователи проекта", 3 => "3 дня с момента регистрации", 7 => "7 дней с момента регистрации", 30 => "1 месяц с момента регистрации", 90 => "3 месяца с момента регистрации", 180 => "6 месяцев с момента регистрации", 365 => "1 год с момента регистрации" );
$no_ref_arr = array( "Все пользователи проекта", "Пользователям без реферера на " . ucfirst($_SERVER["HTTP_HOST"]) . "" );
$sex_user_arr = array( "Все пользователи проекта", "Только мужчины", "Только женщины" );
$to_ref_arr = array( "Все пользователи проекта", "Рефералам 1-го уровня", "Рефералам всех уровней" );
$content_arr = array( "Нет", "Да" );
$limit_entrys = 30;
$start_pos = 0;
if( $option == "adv-config" ) 
{
    $cena_hit = (isset($_POST["cena_hit"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_hit"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_hit"])), 4, ".", "") : false);
    $cena_hideref = (isset($_POST["cena_hideref"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_hideref"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_hideref"])), 4, ".", "") : false);
    $cena_color = (isset($_POST["cena_color"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_color"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_color"])), 4, ".", "") : false);
    $cena_revisit[1] = (isset($_POST["cena_revisit_1"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_revisit_1"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_revisit_1"])), 4, ".", "") : false);
    $cena_revisit[2] = (isset($_POST["cena_revisit_2"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_revisit_2"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_revisit_2"])), 4, ".", "") : false);
    $cena_uniq_ip[1] = (isset($_POST["cena_uniq_ip_1"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_uniq_ip_1"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_uniq_ip_1"])), 4, ".", "") : false);
    $cena_uniq_ip[2] = (isset($_POST["cena_uniq_ip_2"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_uniq_ip_2"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_uniq_ip_2"])), 4, ".", "") : false);
    $cena_up = (isset($_POST["cena_up"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_up"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_up"])), 2, ".", "") : false);
    $min_pay = (isset($_POST["min_pay"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["min_pay"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["min_pay"])), 2, ".", "") : false);
    $max_pay = (isset($_POST["max_pay"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["max_pay"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["max_pay"])), 2, ".", "") : false);
    $comis_sys = (isset($_POST["comis_sys"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["comis_sys"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["comis_sys"])), 2, ".", "") : false);
    $comis_del = (isset($_POST["comis_del"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["comis_del"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["comis_del"])), 2, ".", "") : false);
    if( $cena_hit <= 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за посещение должна быть больше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_hideref < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за скрытие HTTP_REFERER не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_color < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за выделение цветом не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_revisit[1] < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за доступно для просмотра каждые 48 часов не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_revisit[2] < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за доступно для просмотра 1 раз в месяц не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_uniq_ip[1] < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за уникальный IP(100% совпадение) не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_uniq_ip[2] < 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за уникальный IP(по маске до 2 чисел) не должна быть меньше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $cena_up <= 0 ) 
    {
        $result_text = "ERROR";
        $message_text = "Цена за поднятие в списке должна быть больше нуля!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $min_pay < 1 ) 
    {
        $result_text = "ERROR";
        $message_text = "Минимальная сумма пополнения должна быть не менее 1.00 руб.";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $max_pay == false ) 
    {
        $result_text = "ERROR";
        $message_text = "Максимальная сумма пополнения указана не верно!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $max_pay <= $min_pay ) 
    {
        $result_text = "ERROR";
        $message_text = "Максимальная сумма пополнения должна быть больше минимальной!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $comis_sys < 1 | 100 < $comis_sys ) 
    {
        $result_text = "ERROR";
        $message_text = "Комиссия сайта должна быть не менее 1% и не более 100%!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $comis_del < 0 | 100 < $comis_del ) 
    {
        $result_text = "ERROR";
        $message_text = "Комиссия за возврат средств должна быть не менее 0% и не более 100%!";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( $comis_sys < $comis_del ) 
    {
        $result_text = "ERROR";
        $message_text = "Комиссия за возврат средств не должна быть больше комиссии сайта!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $cena_hit_user = $cena_hit / (1 + $comis_sys / 100);
    $cena_hit_user = number_format($cena_hit_user, 4, ".", "");
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_hit . "' WHERE `item`='pvis_cena_hit' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_hit_user . "' WHERE `item`='pvis_cena_hit_user' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_hideref . "' WHERE `item`='pvis_cena_hideref' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_color . "' WHERE `item`='pvis_cena_color' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_revisit[1] . "' WHERE `item`='pvis_cena_revisit' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_revisit[2] . "' WHERE `item`='pvis_cena_revisit' AND `howmany`='2'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_uniq_ip[1] . "' WHERE `item`='pvis_cena_uniq_ip' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_uniq_ip[2] . "' WHERE `item`='pvis_cena_uniq_ip' AND `howmany`='2'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $cena_up . "' WHERE `item`='pvis_cena_up' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $min_pay . "' WHERE `item`='pvis_min_pay' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $max_pay . "' WHERE `item`='pvis_max_pay' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $comis_sys . "' WHERE `item`='pvis_comis_sys' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_config` SET `price`='" . $comis_del . "' WHERE `item`='pvis_comis_del' AND `howmany`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $cena_revisit[0] = 0;
    $cena_uniq_ip[0] = 0;
    $sql = $mysqli->query("SELECT `id`,`hide_httpref`,`color`,`revisit`,`uniq_ip` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`)") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        while( $row = $sql->fetch_assoc() ) {
            $id = $row["id"];
            $hide_httpref = $row["hide_httpref"];
            $color = $row["color"];
            $revisit = $row["revisit"];
            $uniq_ip = $row["uniq_ip"];
            $price_adv = $cena_hit + $hide_httpref * $cena_hideref + $color * $cena_color + $cena_revisit[$revisit] + $cena_uniq_ip[$uniq_ip];
            $price_adv = number_format($price_adv, 4, ".", "");
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `price_adv`='" . $price_adv . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        }
    }

    $sql->free();
    $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='3',`date_end`='" . time() . "' WHERE `status`='1' AND `balance`<`price_adv`") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1' WHERE `status`='3' AND `balance`>=`price_adv`") or die( my_json_encode("ERROR", $mysqli->error) );
    $result_text = "OK";
    $message_text = "<script>setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
    $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Изменения успешно сохранены!</div>";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "adv-add" ) {
    $token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "adv-add" . $security_key));
    /*if( $token_post == false | $token_post != $token_check ) {
        $result_text = "ERROR";
        $message_text = "Не верный токен, обновите страницу!";
        exit( my_json_encode($result_text, $message_text) );
    }*/

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

    if($cnt_bl>0 && $black_url != false ) {
        $row_bl = $sql_bl->fetch_assoc();
        $sql_bl->free();
        $result_text = "ERROR";
        $message_text = "Сайт " . $row_bl["domen"] . " находится в черном списке проекта " . strtoupper($_SERVER["HTTP_HOST"]) . "";
        exit( my_json_encode($result_text, $message_text) );
    }

    if( @getHost($url) != $_SERVER["HTTP_HOST"] && SFB_YANDEX($url) != false ) {
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

    $merch_tran_id = ($pvis_max_pay < $money_add ? exit : true);
    $mysqli->query("UPDATE `tb_statistics` SET `merch_tran_id`=`merch_tran_id`+'1' WHERE `id`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
    $mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `status`='0' AND `date`<'" . (time() - 24 * 60 * 60) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    $sql_adv = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`status_user_id`) WHERE `status`='0' AND `username`='" . $user_name . "' ORDER BY `id` DESC LIMIT 1") or die( my_json_encode("ERROR", $mysqli->error) );
    
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
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `merch_tran_id`='" . $merch_tran_id . "',`method_pay`='0',`wmid`='" . $user_wm_id . "',`username`='" . $user_name . "',`date_edit`='" . time() . "',`date_up`='" . $id_adv . "',`title`='" . $title . "',`description`='" . $description . "',`url`='" . $url . "',`hide_httpref`='" . $hide_httpref . "',`color`='" . $color . "',`revisit`='" . $revisit . "',`uniq_ip`='" . $uniq_ip . "',`date_reg_user`='" . $date_reg_user . "',`reit_user`='" . $reit_user . "',`no_ref`='" . $no_ref . "',`sex_user`='" . $sex_user . "',`to_ref`='" . $to_ref . "',`content`='" . $content . "',`geo_targ`='" . $country_code . "',`price_adv`='" . $price_adv . "',`balance`='0',`money`='" . $money_add . "',`ip`='" . $my_lastiplog . "' WHERE `id`='" . $id_adv . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    }else{
        $mysqli->query("INSERT INTO `tb_ads_pay_vis` (`status`,`session_ident`,`merch_tran_id`,`method_pay`,`wmid`,`username`,`date`,`date_edit`,`title`,`description`,`url`,`hide_httpref`,`color`,`revisit`,`uniq_ip`,`date_reg_user`,`reit_user`,`no_ref`,`sex_user`,`to_ref`,`content`,`geo_targ`,`price_adv`,`balance`,`money`,`ip`) VALUES('0','" . session_id() . "','" . $merch_tran_id . "','0','" . $user_wm_id . "','" . $user_name . "','" . time() . "','" . time() . "','" . $title . "','" . $description . "','" . $url . "','" . $hide_httpref . "','" . $color . "','" . $revisit . "','" . $uniq_ip . "','" . $date_reg_user . "','" . $reit_user . "','" . $no_ref . "','" . $sex_user . "','" . $to_ref . "','" . $content . "','" . $country_code . "','" . $price_adv . "','0','" . $money_add . "','" . $my_lastiplog . "')") or die( my_json_encode("ERROR", $mysqli->error) );
        $id_adv = $mysqli->insert_id;
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `date_up`='" . $id_adv . "' WHERE `id`='" . $id_adv . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    }

    $token_next_cancel = strtolower(md5($id_adv . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-cancel" . $security_key));
    $token_next_start = strtolower(md5($id_adv . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-start" . $security_key));
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
    if( 0 < count($country_code_arr) ) {
        $geo_targ_txt = false;
        for( $i = 0; $i < count($country_code_arr); $i++ ) {
            $geo_targ_txt .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code_arr[$i]) . ".gif\" alt=\"" . $country_code_arr[$i] . "\" title=\"" . $geo_code_name_arr[$country_code_arr[$i]] . "\" align=\"absmiddle\" style=\"margin:0; padding:0 2px 0;\" />";
        }
        $geo_targ_txt = ($geo_targ_txt != false ? $geo_targ_txt : "Все страны");
    }else{
        $geo_targ_txt = "Все страны";
    }

    $message_text = false;
    $message_text .= "<table class=\"tables order\" style=\"margin:0 auto;\">";
    $message_text .= "<thead><tr><th align=\"center\" colspan=\"2\">Информация о заказе</th></tr></thead>";
    $message_text .= "<tr><td width=\"190\">ID рекламы</td><td>" . $id_adv . "</td></tr>";
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
    $message_text .= "<tr><td>Цена одного посещения</td><td><span class=\"text-green\">" . my_num_format($price_adv, 4, ".", "", 2) . " руб.</span></td></tr>";
    $message_text .= "<tr><td>Сумма пополнения</td><td><span class=\"text-green\"><b>" . number_format($money_add, 2, ".", " ") . "</b> руб.</span></td></tr>";
    $message_text .= "<tr>";
    $message_text .= "<td align=\"center\" colspan=\"2\">";
    $message_text .= "<span onClick=\"FuncAdv('" . $id_adv . "', 'adv-start', '" . $type_ads . "', false, '" . $token_next_start . "', 'modal'); return false;\" class=\"sd_sub big green\">Разместить</span>";
    $message_text .= "<span onClick=\"ChangeAds();\" class=\"sd_sub big blue\" title=\"Редактировать параметры рекламной площадки\">Изменить</span>";
    $message_text .= "<span onClick=\"FuncAdv('" . $id_adv . "', 'adv-cancel', '" . $type_ads . "', false, '" . $token_next_cancel . "', 'modal'); return false;\" class=\"sd_sub big red\">Удалить</span>";
    $message_text .= "</td>";
    $message_text .= "</tr>";
    $message_text .= "</table>";
    $result_text = "OK";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "adv-edit" ) {
    $sql = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-edit" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 1 ) {
            $result_text = "ERROR";
            $message_text = "Перед редактированием, необходимо поставить площадку на паузу!";
            exit( my_json_encode($result_text, $message_text) );
        }

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

        if($cnt_bl>0 && $black_url != false ) {
            $row_bl = $sql_bl->fetch_assoc();
            $sql_bl->free();
            $result_text = "ERROR";
            $message_text = "Сайт " . $row_bl["domen"] . " находится в черном списке проекта " . strtoupper($_SERVER["HTTP_HOST"]) . "";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( @getHost($url) != $_SERVER["HTTP_HOST"] && SFB_YANDEX($url) != false ) {
            $result_text = "ERROR";
            $message_text = SFB_YANDEX($url);
            exit( my_json_encode($result_text, $message_text) );
        }

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
        $price_adv = number_format($price_adv, 4, ".", "");
        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `date_edit`='" . time() . "',`title`='" . $title . "',`description`='" . $description . "',`url`='" . $url . "',`hide_httpref`='" . $hide_httpref . "',`color`='" . $color . "',`revisit`='" . $revisit . "',`uniq_ip`='" . $uniq_ip . "',`date_reg_user`='" . $date_reg_user . "',`reit_user`='" . $reit_user . "',`no_ref`='" . $no_ref . "',`sex_user`='" . $sex_user . "',`to_ref`='" . $to_ref . "',`content`='" . $content . "',`geo_targ`='" . $country_code . "',`price_adv`='" . $price_adv . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $result_text = "OK";
        $message_text = "<script>var url_go = document.referrer || \"?op=\"+getUrlVars()[\"op\"]+\"&method=\"+getUrlVars()[\"method\"]+\"&operator=\"+getUrlVars()[\"operator\"]+\"&search=\"+getUrlVars()[\"search\"]+\"&page=\"+getUrlVars()[\"page\"]; setTimeout(function(){\$.modalpopup(\"close\"); location.replace(url_go);}, 1500);</script>";
        $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Рекламная площадка ID:<b>" . $id . "</b> успешно отредактирована!</div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "info-adv" ) {
    $sql = $mysqli->query("SELECT * FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-info-adv" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $country_code_arr = ($row["geo_targ"] != false ? explode(", ", $row["geo_targ"]) : array(  ));
        if( 0 < count($country_code_arr) ) {
            $geo_targ_txt = false;
            for( $i = 0; $i < count($country_code_arr); $i++ ) {
                $geo_targ_txt .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code_arr[$i]) . ".gif\" alt=\"" . $country_code_arr[$i] . "\" title=\"" . $geo_code_name_arr[$country_code_arr[$i]] . "\" align=\"absmiddle\" style=\"margin:0; padding:0 2px 0;\" />";
            }
            $geo_targ_txt = ($geo_targ_txt != false ? $geo_targ_txt : "Все страны");
        }else{
            $geo_targ_txt = "Все страны";
        }

        include_once(ROOT_DIR . "/geoip/geoipcity.inc");
        $gi = geoip_open(ROOT_DIR . "/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
        $record = @geoip_record_by_addr($gi, $row["ip"]);
        $country_code = (isset($record->country_code) && $record->country_code != false ? $record->country_code : false);
        $country_name = ($country_code != false ? get_country($country_code) : false);
        $ip_info = ($country_code != false && $country_name != false ? "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code) . ".gif\" alt=\"" . $country_code . "\" title=\"" . $country_name . "\" align=\"absmiddle\" style=\"margin:0; padding:0 4px 0;\" />" . $row["ip"] : $row["ip"]);
        $message_text = "<div align=\"justify\" style=\"margin:5px; color:#2F4F4F; font-size:11.5px;\">";
        $message_text .= "<h3 class=\"sp\" style=\"margin:0; font-weight:bold; font-size:12px;\">Площадка:</h3>";
        $message_text .= "<b>Заголовок:</b> <span class=\"text-green\">" . $row["title"] . "</span><br>";
        $message_text .= "<b>Краткое описание:</b> <span class=\"text-green\">" . $row["description"] . "</span><br>";
        $message_text .= "<b>URL сайта:</b> <a href=\"" . $row["url"] . "\" target=\"_blank\">" . $row["url"] . "</a>";
        $message_text .= "<h3 class=\"sp\" style=\"margin:10px 0 0; font-weight:bold; font-size:12px;\">Настройки:</h3>";
        $message_text .= "Скрытие HTTP_REFERER: <span class=\"text-green\">" . $hide_httpref_arr[$row["hide_httpref"]] . "</span><br>";
        $message_text .= "Выделение цветом: <span class=\"text-green\">" . $color_arr[$row["color"]] . "</span><br>";
        $message_text .= "Доступно для просмотра: <span class=\"text-green\">" . $revisit_arr[$row["revisit"]] . "</span><br>";
        $message_text .= "Уникальный IP: <span class=\"text-green\">" . $uniq_ip_arr[$row["uniq_ip"]] . "</span><br>";
        $message_text .= "По дате регистрации: <span class=\"text-green\">" . $datereg_user_arr[$row["date_reg_user"]] . "</span><br>";
        $message_text .= "По рейтингу пользователя: <span class=\"text-green\">" . liststatus($row["reit_user"]) . "</span><br>";
        $message_text .= "По наличию реферера: <span class=\"text-green\">" . $no_ref_arr[$row["no_ref"]] . "</span><br>";
        $message_text .= "По половому признаку: <span class=\"text-green\">" . $sex_user_arr[$row["sex_user"]] . "</span><br>";
        $message_text .= "Показывать только рефералам: <span class=\"text-green\">" . $to_ref_arr[$row["to_ref"]] . "</span><br>";
        $message_text .= "Контент 18+: <span class=\"text-green\">" . $content_arr[$row["content"]] . "</span><br>";
        $message_text .= "Геотаргетинг: <span class=\"text-green\">" . $geo_targ_txt . "</span>";
        if($row["status"]>0 ) {
            $message_text .= "<h3 class=\"sp\" style=\"margin:10px 0 0; font-weight:bold; font-size:12px;\">Статистика:</h3>";
            $message_text .= "Всего ссылка получила посещений: <span class=\"text-grey\"><b>" . number_format($row["cnt_visits_all"], 0, ".", "`") . "</b></span><br>";
            $message_text .= "Общая сумма пополнений: <span class=\"text-green\"><b>" . number_format($row["money"], 2, ".", "`") . "</b> руб.</span>";
        }

        $message_text .= "<h3 class=\"sp\" style=\"margin:10px 0 0; font-weight:bold; font-size:12px;\">Дополнительная информация:</h3>";
        $message_text .= "IP адрес: <span>" . $ip_info . "</span><br>";
        $message_text .= "Дата создания: <span class=\"text-green\">" . DATE("d.m.Y в H:i", $row["date"]) . "</span><br>";
        $message_text .= "Дата изменения: <span class=\"text-green\">" . DATE("d.m.Y в H:i", $row["date_edit"]) . "</span><br>";
        if($row["date_up"]>0 && $row["date_up"] != $row["id"] ) {
            $message_text .= "Дата поднятия в списке: <span class=\"text-green\">" . DATE("d.m.Y в H:i", $row["date_up"]) . "</span><br>";
        }

        if( $row["status"] == 3 && $row["date_end"] != 0 ) {
            $message_text .= "Дата завершения: <span class=\"text-green\">" . DATE("d.m.Y в H:i", $row["date_end"]) . "</span><br>";
        }

        $message_text .= "</div>";
        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "confirm-start-req" | $option == "start-req" ) {
    $sql = $mysqli->query("SELECT `id`,`status`,`money` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $money = $row["money"];
        $sql->free();
        if( $option == "confirm-start-req" ) {
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-start-req" . $security_key));
        }else{
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-start-req" . $security_key));
        }

        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-start-req" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status != 0 ) {
            $result_text = "ERROR";
            $message_text = "Рекламная площадка ID:<b>" . $id . "</b> уже запущена!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $option == "confirm-start-req" ) {
            $message_text = "<div style=\"text-align:center; margin:10px 0px 15px 0px; line-height:18px;\">";
            $message_text .= "Запустить показ рекламной площадки ID:<b>" . $id . "</b> ?";
            $message_text .= "</div>";
            $message_text .= "<div style=\"text-align:center;\">";
            $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncAdv(" . $id . ", 'start-req', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Да</span>";
            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
            $message_text .= "</div>";
        }else{
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`date_edit`='" . time() . "',`balance`=`money` WHERE `id`='" . $id . "' AND `status`='0'") or die( my_json_encode("ERROR", $mysqli->error) );
            $message_text = "<script>\$(\"#tr-adv-" . $id . "\").remove(); \$(\"#tr-hide-" . $id . "\").remove(); \$(\"#tr-info-" . $id . "\").remove(); if(\$(\".tr-adv\").length < 1 && !\$(\"span\").is(\"#adv-warning\")) {\$(\"#adv-tab\").append('<tr><td align=\"center\" colspan=\"3\" style=\"padding:3px 2px 2px;\"><span id=\"adv-warning\" class=\"msg-w\" style=\"margin:0 auto;\">Не оплаченных заказов нет</span></td></tr>');} setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
            $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Рекламная площадка ID:<b>" . $id . "</b> успешно запущена!</div>";
        }

        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "adv-start" ) {
    $sql = $mysqli->query("SELECT `id`,`status`,`money` FROM `tb_ads_pay_vis` USE INDEX (`username_id`) WHERE `id`='" . $id . "' AND `username`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $money = $row["money"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-start" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status != 0 ) {
            $result_text = "ERROR";
            $message_text = "Рекламная площадка ID:<b>" . $id . "</b> уже запущена!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`date_edit`='" . time() . "',`balance`=`money` WHERE `id`='" . $id . "' AND `status`='0' AND `username`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $result_text = "OK";
        $message_text = "<script>setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
        $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Рекламная площадка ID:<b>" . $id . "</b> успешно запущена!</div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "confirm-del-req" | $option == "confirm-adv-del" | $option == "del-req" | $option == "adv-cancel" | $option == "adv-del" ) {
    $sql = $mysqli->query("SELECT `id`,`username`,`status`,`money` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $username = $row["username"];
        $status = $row["status"];
        $money = $row["money"];
        $sql->free();
        if( $option == "confirm-del-req" ) {
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-del-req" . $security_key));
            $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-del-req" . $security_key));
        }else{
            if( $option == "confirm-adv-del" ) {
                $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-adv-del" . $security_key));
                $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-del" . $security_key));
            }else{
                if( $option == "del-req" ) {
                    $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-del-req" . $security_key));
                }else{
                    if( $option == "adv-cancel" ) {
                        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-cancel" . $security_key));
                    }else{
                        if( $option == "adv-del" ) {
                            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-adv-del" . $security_key));
                        }

                    }

                }

            }

        }

        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $option == "confirm-del-req" | $option == "confirm-adv-del" ) {
            $result_text = "OK";
            $message_text = "<div style=\"text-align:center; margin:10px 0px 15px 0px; line-height:18px;\">";
            $message_text .= "Удалить рекламную площадку ID:<b>" . $id . "</b> ?";
            $message_text .= "</div>";
            $message_text .= "<div style=\"text-align:center;\">";
            if( $option == "confirm-del-req" ) {
                $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncAdv(" . $id . ", 'del-req', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Да</span>";
            }else{
                $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncAdv(" . $id . ", 'adv-del', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Да</span>";
            }

            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
            $message_text .= "</div>";
        }else{
            if( $option == "del-req" | $option == "adv-cancel" ) {
                $mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `id`='" . $id . "' AND `status`='0'") or die( my_json_encode("ERROR", $mysqli->error) );
                $result_text = "OK";
                $message_text = ($option == "del-req" ? "<script>\$(\"#tr-adv-" . $id . "\").remove(); \$(\"#tr-hide-" . $id . "\").remove(); \$(\"#tr-info-" . $id . "\").remove(); if(\$(\".tr-adv\").length < 1 && !\$(\"span\").is(\"#adv-warning\")) {\$(\"#adv-tab\").append('<tr><td align=\"center\" colspan=\"3\" style=\"padding:3px 2px 2px;\"><span id=\"adv-warning\" class=\"msg-w\" style=\"margin:0 auto;\">Не оплаченных заказов нет</span></td></tr>');} setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>" : false);
                $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Рекламная площадка ID:<b>" . $id . "</b> успешно удалена!</div>";
            }else{
                if( $option == "adv-del" ) {
                    $mysqli->query("DELETE FROM `tb_ads_pay_vis` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $result_text = "OK";
                    $message_text = "<script>\$(\"#tr-adv-" . $id . "\").remove(); \$(\"#tr-hide-" . $id . "\").remove(); \$(\"#tr-info-" . $id . "\").remove(); setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
                    $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Рекламная площадка ID:<b>" . $id . "</b> успешно удалена!</div>";
                }else{
                    $result_text = "ERROR";
                    $message_text = "Что-то пошло не так...";
                }

            }

        }

        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "play-pause" ) {
    $sql = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-play-pause" . $security_key));
        $token_playpause = $token_check;
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 0 ) {
            $result_text = "ERROR";
            $message_text = "<div id=\"scr-" . $id . "\"><script>\$(\"#adv-status-" . $id . " span\").attr({\"class\":\"adv-play\", \"title\":\"Запустить показ рекламной площадки\"}); setTimeout(function(){\$(\"#scr-" . $id . "\").remove();},500);</script></div>";
            $message_text .= "Запуск не возможен, необходимо пополнить баланс площадки!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 1 ) {
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='2',`date_edit`='" . time() . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            $result_text = "OK";
            $message_text = "<span onClick=\"FuncAdv(" . $id . ", 'play-pause', '" . $type_ads . "', false, '" . $token_playpause . "');\" class=\"adv-play\" title=\"Запустить показ рекламной площадки\"></span>";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 2 ) {
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`date_edit`='" . time() . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            $result_text = "OK";
            $message_text = "<span onClick=\"FuncAdv(" . $id . ", 'play-pause', '" . $type_ads . "', false, '" . $token_playpause . "');\" class=\"adv-pause\" title=\"Приостановить показ рекламной площадки\"></span>";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 3 ) {
            $result_text = "ERROR";
            $message_text = "<div id=\"scr-" . $id . "\"><script>\$(\"#adv-status-" . $id . " span\").attr({\"class\":\"adv-play\", \"title\":\"Запустить показ рекламной площадки\"}); setTimeout(function(){\$(\"#scr-" . $id . "\").remove();},500);</script></div>";
            $message_text .= "Запуск не возможен, необходимо пополнить баланс площадки!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $result_text = "ERROR";
        $message_text = "Статус[" . $status . "] не определен!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "stat-adv" ) {
    $sql = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-stat-adv" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $QUERY_STAT = "SELECT `id`,`user_name`,`time`,`ip` FROM `tb_ads_pay_visits` USE INDEX (`stat_visits`) WHERE `ident`='" . $id . "' AND `status`='1'";
        $sql_v = $mysqli->query((string) $QUERY_STAT) or die( my_json_encode("ERROR", $mysqli->error) );
        $count_entrys = $sql_v->num_rows;
        $sql_v->free();
        $count_pages = ceil($count_entrys / $limit_entrys);
        $page = 1;
        $last_id_stat = false;
        $sql_v = $mysqli->query((string) $QUERY_STAT . " ORDER BY `id` DESC LIMIT " . $limit_entrys) or die( my_json_encode("ERROR", $mysqli->error) );
        if($sql_v->num_rows>0 ) {
            include_once(ROOT_DIR . "/geoip/geoipcity.inc");
            $gi = geoip_open(ROOT_DIR . "/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
            $message_text = "<script>var auto_lp = getCookie('" . $type_ads . "_auto_lp')==1 ? true : false; \$(document).ready(function(){if(auto_lp) \$(\"#auto_lp\").prop('checked', true); \$(\".box-modal-content\").on(\"scroll\", function() {if(auto_lp && \$(\"div\").is(\"#load-pages\")) {h_wt = (\$(window).height() + \$(window).scrollTop()); h_lp = (\$(\"#load-pages\").offset().top + \$(\"#load-pages\").innerHeight()); if(h_wt > h_lp) {FuncLP();}}});});</script>";
            $message_text .= "<div style=\"padding-bottom:10px;\">Здесь вы можете увидеть тех, кто просматривал вашу рекламную площадку, учтите что в списке отображаются записи которым не более одних суток.</div>";
            $message_text .= "<div style=\"float:left; padding-bottom:5px;\"><input type=\"checkbox\" id=\"auto_lp\" onClick=\"if(\$(this).prop('checked')) {auto_lp = true; setCookie('" . $type_ads . "_auto_lp', 1, '/', location.hostname);} else {auto_lp = false; setCookie('" . $type_ads . "_auto_lp', 0, '/', location.hostname);}\" style=\"height:16px; width:16px; margin:0px;\"></div>";
            $message_text .= "<div style=\"float:left; padding-bottom:5px; padding-left:5px;\">- автозагрузка данных при скролинге вниз</div>";
            $message_text .= "<table class=\"tables order\" id=\"tab-stat-" . $id . "\" style=\"margin:0 auto;\">";
            $message_text .= "<thead><tr align=\"center\">";
            $message_text .= "<th width=\"33%\">Логин</th>";
            $message_text .= "<th width=\"33%\">Дата</th>";
            $message_text .= "<th width=\"34%\">IP</th>";
            $message_text .= "</tr></thead>";
            $message_text .= "<tbody>";
            while( $row_v = $sql_v->fetch_assoc() ) {
                $last_id_stat = $row_v["id"];
                $record = @geoip_record_by_addr($gi, $row_v["ip"]);
                $country_code = (isset($record->country_code) && $record->country_code != false ? $record->country_code : false);
                $country_name = ($country_code != false ? get_country($country_code) : false);
                $message_text .= "<tr id=\"tr-stat-" . $row_v["id"] . "\">";
                $message_text .= "<td align=\"center\"><span class=\"text-blue\" style=\"cursor:help;\" title=\"Логин\">" . $row_v["user_name"] . "</span></td>";
                $message_text .= "<td align=\"center\">";
                if( DATE("d.m.Y", $row_v["time"]) == DATE("d.m.Y", time()) ) {
                    $message_text .= "<span style=\"color:#006400;\">Сегодня</span>, " . DATE("в H:i", $row_v["time"]);
                }else{
                    if( DATE("d.m.Y", $row_v["time"]) == DATE("d.m.Y", time() - 24 * 60 * 60) ) {
                        $message_text .= "<span style=\"color:#000080;\">Вчера</span>, " . DATE("в H:i", $row_v["time"]);
                    }else{
                        $message_text .= "<span style=\"color:#363636;\">" . DATE("d.m.Y", $row_v["time"]) . "</span> " . DATE("H:i", $row_v["time"]);
                    }

                }

                $message_text .= "</td>";
                $message_text .= "<td align=\"left\" style=\"padding:2px 25px;\">";
                if( $country_name != false ) {
                    $message_text .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code) . ".gif\" alt=\"" . $country_name . "\" title=\"" . $country_name . "\" align=\"absmiddle\" width=\"16\" height=\"11\" style=\"margin:0; padding:0 6px 0 0;\" />";
                }

                $message_text .= $row_v["ip"];
                $message_text .= "</td>";
                $message_text .= "</tr>";
            }
            $message_text .= "</tbody>";
            $message_text .= "</table>";
        }else{
            $message_text = "<div style=\"padding-bottom:10px;\">Здесь вы можете увидеть тех, кто просматривал вашу рекламную площадку, учтите что в списке отображаются записи которым не более одних суток.</div>";
            $message_text .= "<table class=\"tables order\" id=\"tab-stat-" . $id . "\" style=\"margin:0 auto;\">";
            $message_text .= "<thead><tr align=\"center\">";
            $message_text .= "<th width=\"33%\">Логин</th>";
            $message_text .= "<th width=\"33%\">Дата</th>";
            $message_text .= "<th width=\"34%\">IP</th>";
            $message_text .= "</tr></thead>";
            $message_text .= "<tbody>";
            $message_text .= "<tr><td align=\"center\" colspan=\"3\" style=\"padding:4px;\"><b>Нет данных для отображения</b></td></tr>";
            $message_text .= "</tbody>";
            $message_text .= "</table>";
        }

        $sql_v->free();
        if( $limit_entrys < $count_entrys ) {
            $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . $page . $last_id_stat . $limit_entrys . $security_key));
            $message_text .= "<div id=\"load-pages\" data-id=\"" . $id . "\" data-op=\"stat-adv-page\" data-type=\"" . $type_ads . "\" data-elid=\"tab-stat-" . $id . "\" data-page=\"" . $page . "\" data-lastid=\"" . $last_id_stat . "\" data-token=\"" . $token_next . "\" style=\"margin:10px auto 0;\" onClick=\"FuncLP();\">Показать ещё</div>";
        }

        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "stat-adv-page" ) {
    $last_id_stat = (isset($_POST["lastid"]) && is_string($_POST["lastid"]) && preg_match("|^[\\d]{1,11}\$|", intval($_POST["lastid"])) ? escape(intval($_POST["lastid"])) : false);
    $last_page_stat = (isset($_POST["page"]) && is_string($_POST["page"]) && preg_match("|^[\\d]{1,11}\$|", intval($_POST["page"])) ? escape(intval($_POST["page"])) : false);
    $sql = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . $last_page_stat . $last_id_stat . $limit_entrys . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $last_page_stat == false | $last_id_stat == false )  {
            $result_text = "OK";
            $message_text = array( "page" => "", "lastid" => "", "rows" => "" );
            exit( my_json_encode($result_text, $message_text) );
        }

        $QUERY_STAT = "SELECT `id`,`user_name`,`time`,`ip` FROM `tb_ads_pay_visits` USE INDEX (`stat_visits`) WHERE `ident`='" . $id . "' AND `status`='1'";
        $sql_v = $mysqli->query((string) $QUERY_STAT) or die( my_json_encode("ERROR", $mysqli->error) );
        $count_entrys = $sql_v->num_rows;
        $sql_v->free();
        $count_pages = ceil($count_entrys / $limit_entrys);
        $start_pos = intval($last_page_stat * $limit_entrys);
        $next_page_stat = false;
        $message_text = false;
        $now_rows = 0;
        $sql_v = $mysqli->query((string) $QUERY_STAT . " AND `id`<'" . $last_id_stat . "' ORDER BY `id` DESC LIMIT " . $limit_entrys) or die( my_json_encode("ERROR", $mysqli->error) );
        if($sql_v->num_rows>0 ) {
            include_once(ROOT_DIR . "/geoip/geoipcity.inc");
            $gi = geoip_open(ROOT_DIR . "/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
            while( $row_v = $sql_v->fetch_assoc() ) {
                $now_rows++;
                $last_id_stat = $row_v["id"];
                $record = @geoip_record_by_addr($gi, $row_v["ip"]);
                $country_code = (isset($record->country_code) && $record->country_code != false ? $record->country_code : false);
                $country_name = ($country_code != false ? get_country($country_code) : false);
                $message_text .= "<tr id=\"tr-stat-" . $row_v["id"] . "\">";
                $message_text .= "<td align=\"center\"><span class=\"text-blue\" style=\"cursor:help;\" title=\"Логин\">" . $row_v["user_name"] . "</span></td>";
                $message_text .= "<td align=\"center\">";
                if( DATE("d.m.Y", $row_v["time"]) == DATE("d.m.Y", time()) ) {
                    $message_text .= "<span style=\"color:#006400;\">Сегодня</span>, " . DATE("в H:i", $row_v["time"]);
                }else{
                    if( DATE("d.m.Y", $row_v["time"]) == DATE("d.m.Y", time() - 24 * 60 * 60) ) {
                        $message_text .= "<span style=\"color:#000080;\">Вчера</span>, " . DATE("в H:i", $row_v["time"]);
                    }else{
                        $message_text .= "<span style=\"color:#363636;\">" . DATE("d.m.Y", $row_v["time"]) . "</span> " . DATE("H:i", $row_v["time"]);
                    }

                }

                $message_text .= "</td>";
                $message_text .= "<td align=\"left\" style=\"padding:2px 25px;\">";
                if( $country_name != false ) {
                    $message_text .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code) . ".gif\" alt=\"" . $country_name . "\" title=\"" . $country_name . "\" align=\"absmiddle\" width=\"16\" height=\"11\" style=\"margin:0; padding:0 6px 0 0;\" />";
                }

                $message_text .= $row_v["ip"];
                $message_text .= "</td>";
                $message_text .= "</tr>";
            }
            $last_id_stat = ($limit_entrys <= $now_rows ? $last_id_stat : false);
            $next_page_stat = ($last_id_stat != false ? $last_page_stat + 1 : false);
        }else{
            $last_id_stat = false;
            $next_page_stat = false;
            $message_text = false;
        }

        $sql_v->free();
        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . $next_page_stat . $last_id_stat . $limit_entrys . $security_key));
        $message_text = array( "page" => $next_page_stat, "lastid" => $last_id_stat, "rows" => $message_text, "token" => $token_next );
        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "info-up" ) {
    $sql = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-info-up" . $security_key));
        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-start-up" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $result_text = "OK";
        $message_text = "Ссылка будет поднята на первую позицию в списке оплачиваемых посещений.";
        $message_text .= "<div style=\"text-align:center; margin:5px auto 0;\">";
        $message_text .= "<span class=\"sd_sub big orange\" onClick=\"FuncAdv(" . $id . ", 'start-up', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Поднять</span>";
        $message_text .= "</div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "start-up" ) {
    $sql = $mysqli->query("SELECT `id`,`status` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-start-up" . $security_key));
        if( $status == 1 ) {
            $sql_pos = $mysqli->query("SELECT COUNT(*) as `position` FROM `tb_ads_pay_vis` USE INDEX (`status_date_up`) WHERE `status`='1' AND `date_up`>(SELECT `date_up` FROM `tb_ads_pay_vis` WHERE `id`='" . $id . "')") or die( my_json_encode("ERROR", $mysqli->error) );
            $position = (0 < $sql_pos->num_rows ? $sql_pos->fetch_object()->position + 1 : 1);
            $sql_pos->free();
        }

        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status != 1 ) {
            $result_text = "ERROR";
            $message_text = "Для поднятия в списке ссылку необходимо запустить!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( isset($position) && $position == 1 ) {
            $result_text = "ERROR";
            $message_text = "Нет необходимости, ссылка уже находится на первой позиции!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $mysqli->query("UPDATE `tb_ads_pay_vis` SET `date_edit`='" . time() . "',`date_up`='" . time() . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        $pos_class = "adv-down";
        $pos_title = "Позиция ссылки в общем списке выдачи: 1";
        $pos_text = 1;
        $result_text = "OK";
        $message_text = "<script>last_info = false; \$(\"#adv-up-" . $id . "\").attr({class:\"" . $pos_class . "\", title:\"" . $pos_title . "\"}).html(\"" . $pos_text . "\"); \$(\".tr-info\").hide(); \$(\"#text-info-" . $id . "\").html(\"\"); setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
        $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Ссылка ID:<b>" . $id . "</b> успешно поднята на первую позицию!</div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "confirm-erase" | $option == "erase" ) {
    $sql = $mysqli->query("SELECT `id`,`status`,`cnt_visits_now` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $cnt_visits_now = $row["cnt_visits_now"];
        $sql->free();
        if( $option == "confirm-erase" ) {
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-erase" . $security_key));
        }else{
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-erase" . $security_key));
        }

        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-erase" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $status == 0 | $cnt_visits_now <= 0 ) {
            $result_text = "ERROR";
            $message_text = "Счетчик этой площадки уже равен нулю!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $option == "confirm-erase" ) {
            $message_text = "<div style=\"text-align:center; margin:10px 0px 15px 0px; line-height:18px;\">";
            $message_text .= "Сбросить статистику посещений рекламной площадки ID:<b>" . $id . "</b> ?";
            $message_text .= "</div>";
            $message_text .= "<div style=\"text-align:center;\">";
            $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncAdv(" . $id . ", 'erase', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Да</span>";
            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
            $message_text .= "</div>";
        }else{
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `cnt_visits_now`='0',`date_edit`='" . time() . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            $message_text = "<script>\$(\"#adv-stat-" . $id . "\").html(\"0\"); \$(\"#adv-erase-" . $id . "\").remove(); setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script>";
            $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Статистика посещений успешно сброшена.</div>";
        }

        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "info-bal" ) {
    $sql = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-info-bal" . $security_key));
        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-bal" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $pvis_min_pay = SqlConfig("pvis_min_pay", 1, 2);
        $pvis_max_pay = SqlConfig("pvis_max_pay", 1, 2);
        $message_text = "<div>Укажите сумму, которую вы хотите внести в бюджет рекламной площадки ID:<b>" . $id . "</b>.</div>";
        $message_text .= "<div>[ Минимум <span class=\"text-green\"><b>" . number_format($pvis_min_pay, 2, ".", "`") . "</b> руб.</span> ]</div>";
        $message_text .= "<form id=\"form-ins-money\" method=\"POST\" action=\"\" onSubmit=\"FuncAdv(" . $id . ", 'confirm-bal', '" . $type_ads . "', \$(this).attr('id'), '" . $token_next . "', 'modal'); return false;\">";
        $message_text .= "<input type=\"text\" id=\"money_add\" name=\"money_add\" maxlength=\"10\" value=\"100.00\" step=\"any\" min=\"" . $pvis_min_pay . "\" max=\"" . $pvis_max_pay . "\" class=\"payadv\" required=\"required\" autocomplete=\"off\" onKeydowm=\"isMoney(this);\" onKeyup=\"isMoney(this);\">";
        $message_text .= "<input type=\"submit\" value=\"Пополнить\" class=\"sd_sub big orange\">";
        $message_text .= "</form>";
        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "confirm-bal" ) {
    $sql = $mysqli->query("SELECT `id` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-bal" . $security_key));
        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-add-bal" . $security_key));
        $money_pay = (isset($_POST["money_add"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["money_add"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["money_add"])), 2, ".", "") : false);
        $pvis_min_pay = SqlConfig("pvis_min_pay", 1, 2);
        $pvis_max_pay = SqlConfig("pvis_max_pay", 1, 2);
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $money_pay == false | $money_pay == 0 ) {
            $result_text = "ERROR";
            $message_text = "Не указана сумма пополнения!";
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

        $message_text = "<div style=\"text-align:center; margin:10px 0px; line-height:20px;\">";
        $message_text .= "Пополнить бюджет рекламной площадки ID:<b class=\"text-grey\">" . $id . "</b> на <b class=\"text-green\">" . number_format($money_pay, 2, ".", "`") . "</b> руб.?";
        $message_text .= "</div>";
        $message_text .= "<div style=\"text-align:center;\">";
        $message_text .= "<form id=\"form-add-money\" method=\"POST\" action=\"\" style=\"display:inline-block;\" onSubmit=\"FuncAdv(" . $id . ", 'add-bal', '" . $type_ads . "', \$(this).attr('id'), '" . $token_next . "', 'modal'); return false;\">";
        $message_text .= "<input type=\"hidden\" name=\"money_add\" value=\"" . $money_pay . "\" autocomplete=\"off\">";
        $message_text .= "<input type=\"submit\" value=\"Да\" class=\"sd_sub green\" style=\"min-width:64px;\">";
        $message_text .= "</form>";
        $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
        $message_text .= "</div>";
        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "add-bal" ) {
    $sql = $mysqli->query("SELECT `id`,`status`,`cnt_visits_now`,`price_adv`,`balance`,`money` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $status = $row["status"];
        $cnt_visits_now = $row["cnt_visits_now"];
        $price_adv = $row["price_adv"];
        $balance = $row["balance"];
        $money = $row["money"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-add-bal" . $security_key));
        $money_pay = (isset($_POST["money_add"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["money_add"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["money_add"])), 2, ".", "") : false);
        $pvis_min_pay = SqlConfig("pvis_min_pay", 1, 2);
        $pvis_max_pay = SqlConfig("pvis_max_pay", 1, 2);
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $money_pay == false | $money_pay == 0 ) {
            $result_text = "ERROR";
            $message_text = "Не указана сумма пополнения!";
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

        if( $status == 0 ) {
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`date_edit`='" . time() . "',`balance`=`balance`+'" . $money_pay . "',`money`='" . $money_pay . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        }else{
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `status`='1',`date_edit`='" . time() . "',`balance`=`balance`+'" . $money_pay . "',`money`=`money`+'" . $money_pay . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        }

        $cnt_totals = floor(bcdiv($balance + $money_pay, $price_adv));
        $cnt_totals = number_format($cnt_totals, 0, ".", "`");
        $new_balance = my_num_format($balance + $money_pay, 4, ".", "`", 2);
        $new_money = my_num_format($money + $money_pay, 2, ".", "`", 2);
        $result_text = "OK";
        $message_text = "<div id=\"scr-" . $id . "\"><script>last_info = false; \$(\"#adv-status-" . $id . " span\").attr({\"class\":\"adv-pause\", \"title\":\"Приостановить показ рекламной площадки\"}); \$(\"#adv-totals-" . $id . "\").html('" . $cnt_totals . "'); \$(\"#adv-bal-" . $id . "\").attr(\"class\", \"add-money\").html('" . $new_balance . "'); \$(\"#adv-sumin-" . $id . "\").html('{" . $new_money . "}'); \$(\".tr-info\").hide(); \$(\"#text-info-" . $id . "\").html(\"\"); setTimeout(function(){\$(\"#scr-" . $id . "\").remove();},700); setTimeout(function(){\$.modalpopup(\"close\");}, 2000);</script></div>";
        $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Баланс рекламной площадки ID:<b>" . $id . "</b> успешно пополнен!</div>";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "confirm-clear-claims" | $option == "clear-claims" ) {
    $sql = $mysqli->query("SELECT `id`,`cnt_claims` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $cnt_claims = $row["cnt_claims"];
        $sql->free();
        if( $option == "confirm-clear-claims" ) {
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-confirm-clear-claims" . $security_key));
        }else{
            $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-clear-claims" . $security_key));
        }

        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-clear-claims" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $cnt_claims <= 0 ) {
            $result_text = "ERROR";
            $message_text = "Жалобы на рекламную площадку ID:<b>" . $id . "<b> не найдены!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $option == "confirm-clear-claims" ) {
            $message_text = "<div style=\"text-align:center; margin:10px 0px 15px 0px; line-height:18px;\">";
            $message_text .= "Удалить все жалобы с рекламной площадки ID:<b>" . $id . "</b> ?";
            $message_text .= "</div>";
            $message_text .= "<div style=\"text-align:center;\">";
            $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncAdv(" . $id . ", 'clear-claims', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\">Да</span>";
            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
            $message_text .= "</div>";
        }else{
            $mysqli->query("UPDATE `tb_ads_pay_vis` SET `cnt_claims`='0' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            $mysqli->query("DELETE FROM `tb_ads_claims` WHERE `ident`='" . $id . "' AND `type`='" . $type_ads . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            $message_text = "<script>\$(\"#adv-c-claims-" . $id . ", #adv-d-claims-" . $id . ", #adv-v-claims-" . $id . "\").remove(); setTimeout(function(){\$.modalpopup(\"close\");}, 2500);</script>";
            $message_text .= "<div class=\"msg-ok\" style=\"margin:0 auto; line-height:20px;\">Жалобы с рекламной площадки ID:<b>" . $id . "</b> успешно удалены!</div>";
        }

        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "view-claims" ) {
    $sql = $mysqli->query("SELECT `id`,`cnt_claims` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $cnt_claims = $row["cnt_claims"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-view-claims" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        if( $cnt_claims <= 0 ) {
            $result_text = "ERROR";
            $message_text = "Жалобы на рекламную площадку ID:<b>" . $id . "<b> не найдены!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $message_text = "<table class=\"tables order\" id=\"tab-claims-" . $id . "\" style=\"margin:0 auto;\">";
        $message_text .= "<thead><tr align=\"center\">";
        $message_text .= "<th width=\"120\">Логин</th>";
        $message_text .= "<th width=\"100\">Дата</th>";
        $message_text .= "<th width=\"120\">IP</th>";
        $message_text .= "<th>Текст жалобы</th>";
        $message_text .= "<th width=\"20\"></th>";
        $message_text .= "</tr></thead>";
        $message_text .= "<tbody>";
        $sql = $mysqli->query("SELECT * FROM `tb_ads_claims` WHERE `ident`='" . $id . "' AND `type`='" . $type_ads . "' ORDER BY `id` DESC") or die( my_json_encode("ERROR", $mysqli->error) );
        if($sql->num_rows>0 ) {
            include_once(ROOT_DIR . "/geoip/geoipcity.inc");
            $gi = geoip_open(ROOT_DIR . "/geoip/GeoLiteCity.dat", GEOIP_STANDARD);
            while( $row = $sql->fetch_assoc() ) {
                $record = @geoip_record_by_addr($gi, $row["ip"]);
                $country_code = (isset($record->country_code) && $record->country_code != false ? $record->country_code : false);
                $country_name = ($country_code != false ? get_country($country_code) : false);
                $token_next = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-del-claims" . $security_key));
                $message_text .= "<tr id=\"tr-claims-" . $row["id"] . "\" class=\"tr-claims\">";
                $message_text .= "<td align=\"center\"><span class=\"text-blue\" style=\"cursor:help;\" title=\"Логин\">" . $row["username"] . "</span></td>";
                $message_text .= "<td align=\"center\">";
                if( DATE("d.m.Y", $row["time"]) == DATE("d.m.Y", time()) ) {
                    $message_text .= "<span style=\"color:#006400;\">Сегодня</span>, " . DATE("в H:i", $row["time"]);
                }else{
                    if( DATE("d.m.Y", $row["time"]) == DATE("d.m.Y", time() - 24 * 60 * 60) ) {
                        $message_text .= "<span style=\"color:#000080;\">Вчера</span>, " . DATE("в H:i", $row["time"]);
                    }else{
                        $message_text .= "<span style=\"color:#363636;\">" . DATE("d.m.Y", $row["time"]) . "</span> " . DATE("H:i", $row["time"]);
                    }

                }

                $message_text .= "</td>";
                $message_text .= "<td align=\"left\" style=\"padding:2px 5px;\">";
                if( $country_name != false ) {
                    $message_text .= "<img src=\"//" . $_SERVER["HTTP_HOST"] . "/img/flags/" . strtolower($country_code) . ".gif\" alt=\"" . $country_name . "\" title=\"" . $country_name . "\" align=\"absmiddle\" width=\"16\" height=\"11\" style=\"margin:0; padding:0 6px 0 0;\" />";
                }

                $message_text .= $row["ip"];
                $message_text .= "</td>";
                $message_text .= "<td align=\"center\">" . $row["claims"] . "</td>";
                $message_text .= "<td align=\"center\"><span onClick=\"FuncAdv(" . $row["id"] . ", 'del-claims', '" . $type_ads . "', false, '" . $token_next . "', 'modal');\" class=\"act-del\" title=\"Удалить жалобу\"></span></td>";
                $message_text .= "</tr>";
            }
            geoip_close($gi);
        }else{
            $message_text .= "<tr><td align=\"center\" colspan=\"5\" style=\"padding:4px;\"><b>Жалобы не найдены</b></td></tr>";
        }

        $message_text .= "</tbody>";
        $message_text .= "</table>";
        $sql->free();
        $result_text = "OK";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

if( $option == "del-claims" ) {
    $sql = $mysqli->query("SELECT `id`,`ident` FROM `tb_ads_claims` WHERE `id`='" . $id . "' AND `type`='" . $type_ads . "'") or die( my_json_encode("ERROR", $mysqli->error) );
    if($sql->num_rows>0 ) {
        $row = $sql->fetch_assoc();
        $id = $row["id"];
        $ident = $row["ident"];
        $sql->free();
        $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "token-del-claims" . $security_key));
        if( $token_post == false | $token_post != $token_check ) {
            $result_text = "ERROR";
            $message_text = "Не верный токен, обновите страницу!";
            exit( my_json_encode($result_text, $message_text) );
        }

        $sql = $mysqli->query("SELECT `id`,`cnt_claims` FROM `tb_ads_pay_vis` USE INDEX (`PRIMARY`) WHERE `id`='" . $ident . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        if($sql->num_rows>0 ) {
            $row = $sql->fetch_assoc();
            $ident = $row["id"];
            $cnt_claims = $row["cnt_claims"];
            $sql->free();
            $mysqli->query("DELETE FROM `tb_ads_claims` WHERE `id`='" . $id . "' AND `type`='" . $type_ads . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            if( 1 <= $cnt_claims ) {
                $mysqli->query("UPDATE `tb_ads_pay_vis` SET `cnt_claims`=`cnt_claims`-'1' WHERE `id`='" . $ident . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            }

            $result_text = "OK";
            $message_text = array( "id_adv" => $ident, "id_claims" => $id, "cnt_claims" => $cnt_claims - 1 );
            exit( my_json_encode($result_text, $message_text) );
        }

        $sql->free();
        $result_text = "ERROR";
        $message_text = "Рекламная площадка ID:<b>" . $id . "</b> не найдена!";
        exit( my_json_encode($result_text, $message_text) );
    }

    $sql->free();
    $result_text = "ERROR";
    $message_text = "Жалоба №:<b>" . $id . "</b> не найдена!";
    exit( my_json_encode($result_text, $message_text) );
}

$result_text = "ERROR";
$message_text = "Option [" . $option . "] not found...";
exit( my_json_encode($result_text, $message_text) );



?>