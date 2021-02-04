<?php 
session_start();
header("Content-type: text/html; charset=utf-8");
if( !DEFINED("ROOT_DIR") ) 
{
    DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
}

$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false ? "json" : "nojson");

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	 global $ajax_json;
    global $message_text;
	
	$errfile = str_replace(ROOT_DIR, "", $errfile);
	switch($errno) {
		case(1): $message_text = "Fatal error[$errno]: $errstr in line $errline in $errfile"; break;
		case(2): $message_text = "Warning[$errno]: $errstr in line $errline in $errfile"; break;
		case(8): $message_text = "Notice[$errno]: $errstr in line $errline in $errfile"; break;
		default: $message_text = "[$errno] $errstr in line $errline in $errfile"; break;
	}
	//$message_text = '<div class="block-error">'.$message_text.'</div>';
	exit(my_json_encode($ajax_json, "ERROR", $message_text));
}

function json_encode_cp1251($json_arr) {
	$json_arr = json_encode($json_arr);
	$arr_replace_cyr = array("\u0410", "\u0430", "\u0411", "\u0431", "\u0412", "\u0432", "\u0413", "\u0433", "\u0414", "\u0434", "\u0415", "\u0435", "\u0401", "\u0451", "\u0416", "\u0436", "\u0417", "\u0437", "\u0418", "\u0438", "\u0419", "\u04'", "\u041a", "\u043a", "\u041b", "\u043b", "\u041c", "\u043c", "\u041d", "\u043d", "\u041e", "\u043e", "\u041f", "\u043f", "\u0420", "\u0440", "\u0421", "\u0441", "\u0422", "\u0442", "\u0423", "\u0443", "\u0424", "\u0444", "\u0425", "\u0445", "\u0426", "\u0446", "\u0427", "\u0447", "\u0428", "\u0448", "\u0429", "\u0449", "\u042a", "\u044a", "\u042b", "\u044b", "\u042c", "\u044c", "\u042d", "\u044d", "\u042e", "\u044e", "\u042f", "\u044f");
	$arr_replace_utf = array("А", "а", "Б", "б", "В", "в", "Г", "г", "Д", "д", "Е", "е", "Ё", "ё", "Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н","О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь","ь","Э","э","Ю","ю","Я","я");
	$json_arr = str_replace($arr_replace_cyr, $arr_replace_utf, $json_arr);
	return $json_arr;
}

function arrIconv($from, $to, $obj)
{
    if( is_array($obj) | is_object($obj) ) 
    {
        foreach( $obj as &$val ) 
        {
            $val = arrIconv($from, $to, $val);
        }
        return $obj;
    }
    else
    {
        return iconv($from, $to, $obj);
    }

}

function my_json_encode($result_text, $message_text)
{
    global $ajax_json;
    return ($ajax_json == "json" ? json_encode_cp1251(array( "result" => iconv("CP1251", "UTF-8", $result_text), "message" => arriconv("CP1251", "UTF-8", $message_text) )) : $message_text);
}

function json_encode_socket($data)
{
    return json_encode(arriconv("CP1251", "UTF-8", $data));
}

function escape($value)
{
    global $mysqli;
    return $mysqli->real_escape_string($value);
}
function limpiarez($mensaje, $erase = false)
{
    $mensaje = trim($mensaje);
    $mensaje = str_ireplace(array( "`", "\$", "&&", "  " ), array( "'", "&#036;", "&", " " ), $mensaje);
    $mensaje = preg_replace("'<[^>]*?>.*?'si", "", $mensaje);
    $mensaje = preg_replace(array( "#\\[img\\](.*?)\\[/img\\]#si", "#\\[img\\]#si", "#\\[/img\\]#si" ), array( "\$1", "", "" ), $mensaje);
    $mensaje = preg_replace(array( "#\\[b\\]\\[/b\\]#si", "#\\[i\\]\\[/i\\]#si", "#\\[u\\]\\[/u\\]#si", "#\\[s\\]\\[/s\\]#si" ), array( "\$1", "\$1", "\$1", "\$1" ), $mensaje);
    $mensaje = strip_tags($mensaje);
    $mensaje = iconv("UTF-8", "CP1251//TRANSLIT", htmlspecialchars($mensaje, ENT_QUOTES, "CP1251", false));
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, "CP1251", false);
    if( $erase != false ) 
    {
        $mensaje = preg_replace_callback("#https?://[^\\s]++#iU", "checkURL", $mensaje);
    }

    return trim($mensaje);
}

function checkURL($matches)
{
    global $allowed_url;
    foreach( $matches as $key => $val ) 
    {
        if( array_search(getHost($val), $allowed_url) === false ) 
        {
            $matches[$key] = "[font color=\"red\"]ссылка вырезана[/font]";
        }

    }
    return (isset($matches[0]) ? $matches[0] : false);
}

function desc_bb($desc)
{
    $desc = str_ireplace(array( "[url][font color=\"red\"]", "[url=[font color=\"red\"]", "[a][font color=\"red\"]", "[a=[font color=\"red\"]" ), "[font color=\"red\"]", $desc);
    $desc = new bbcode($desc);
    $desc = $desc->get_html();
    $desc = str_ireplace("&amp;", "&", $desc);
    return smile($desc);
}

function smile($mensaje)
{
    return preg_replace("#:smile-([\\d]|[1-9][\\d]|100):#si", "<img src=\"/smiles/smile-\$1.gif\" class=\"smile_img\" alt=\"\" />", $mensaje);
}

function LoadMess($row_chat, $user_name, $chat_status)
{
    global $security_key;
    $token_next_del = strtolower(md5($row_chat["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-mess-del" . $security_key));
    $token_next_ban = strtolower(md5($row_chat["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-form-ban-user" . $security_key));
    $load_mess = "<div class=\"box-message\" id=\"chat-mess-" . $row_chat["id"] . "\">";
    $load_mess .= "<table class=\"chat-table\">";
    $load_mess .= "<tbody>";
    $load_mess .= "<tr>";
    $load_mess .= "<td class=\"ta-center\" width=\"46\" style=\"padding-left:0;\"><a href=\"/wall?uid=" . $row_chat["user_id"] . "\" target=\"_blank\"><img src=\"/avatar/" . $row_chat["user_avatar"] . "\" class=\"chat-avatar\" align=\"absmiddle\" alt=\"\" title=\"Перейти на стену пользователя " . $row_chat["user_name"] . "\" /></a></td>";
    $load_mess .= "<td class=\"ta-left\">";
    $load_mess .= "<div class=\"chat-mess\">";
    $load_mess .= "<div class=\"chat-users\">";
    $load_mess .= "<span class=\"chat-author\" " . (($row_chat["user_color"] != false ? "style=\"color:" . $row_chat["user_color"] . ";\"" : false)) . " onClick=\"UserToChat('chat-user-to', '" . $row_chat["user_name"] . "');\">" . $row_chat["user_name"] . "</span>";
    $load_mess .= ($row_chat["user_name_to"] != false ? "<span class=\"user-to\"><b>&rarr;</b> " . $row_chat["user_name_to"] . "</span>" . (($row_chat["privat"] == 1 ? " <span class=\"chat-privat\" title=\"Приватное сообщение\"></span>" : false)) : false);
    $load_mess .= "</div>";
    $load_mess .= html_entity_decode($row_chat["chat_mess"], ENT_QUOTES);
    $load_mess .= "<div style=\"display:inline-block; width:auto; height:20px; float:right; text-align:right;\">";
    $load_mess .= "<span class=\"mess-del\" title=\"Удалить сообщение\" onClick=\"FuncChat(" . $row_chat["id"] . ", 'mess-del', false, '" . $token_next_del . "', true, 'Информация', 500);\"></span>";
    $load_mess .= "</div>";
    $load_mess .= "</div>";
    $load_mess .= "</td>";
    $load_mess .= "<td class=\"ta-right\" width=\"50\">";
    $load_mess .= "<span class=\"chat-time\">" . ((DATE("d.m.Y", $row_chat["time"]) == DATE("d.m.Y", time()) ? DATE("H:i", $row_chat["time"]) : (DATE("d.m.Y", $row_chat["time"]) == DATE("d.m.Y", time() - 24 * 60 * 60) ? DATE("H:i", $row_chat["time"]) . "<br>вчера" : DATE("H:i d.m.Y", $row_chat["time"])))) . "</span>";
    $load_mess .= "</td>";
    $load_mess .= "</tr>";
    $load_mess .= "</tbody>";
    $load_mess .= "</table>";
    $load_mess .= "</div>";
    return $load_mess;
}

//$ajax_json = (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false ? "json" : "nojson");
$set_error_handler = set_error_handler("myErrorHandler", E_ALL);
if( isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" ) 
{
    require(ROOT_DIR . "/config.php");
    require(ROOT_DIR . "/funciones.php");
    require(ROOT_DIR . "/merchant/func_mysql.php");
    require_once(ROOT_DIR . "/api/socket.io/socket.io.php");
    require_once(ROOT_DIR . "/bbcode/bbcode.lib.php");
    $user_name = (isset($_SESSION["userLog"]) && preg_match("|^[a-zA-Z0-9\\-_-]{3,25}\$|", trim($_SESSION["userLog"])) ? escape(htmlentities(stripslashes(trim($_SESSION["userLog"])))) : false);
    $user_pass = (isset($_SESSION["userPas"]) && preg_match("|^[0-9a-fA-F]{32}\$|", trim($_SESSION["userPas"])) ? escape(htmlentities(stripslashes(trim($_SESSION["userPas"])))) : false);
    $user_lastip = getRealIP();
    $id = (isset($_POST["id"]) && is_string($_POST["id"]) && preg_match("|^[\\d]{1,11}\$|", intval(limpiarez($_POST["id"]))) ? intval(limpiarez($_POST["id"])) : false);
    $option = (isset($_POST["op"]) && is_string($_POST["op"]) && preg_match("|^[a-zA-Z0-9\\-_]{3,20}\$|", limpiarez($_POST["op"])) ? limpiarez($_POST["op"]) : false);
    $token_post = (isset($_POST["token"]) && is_string($_POST["token"]) && preg_match("|^[0-9a-fA-F]{32}\$|", limpiarez($_POST["token"])) ? strtolower(limpiarez($_POST["token"])) : false);
    $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
	//$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
    $maxlength_mess = 300;
    $tab_max_mess = 200;
    $last_id_mess = 0;
    $allowed_url = array( $_SERVER["HTTP_HOST"], "youtu.be", "youtube.com", "yadi.sk", "skrinshoter.ru", "prntscr.com", "radikall.com", "radikal.ru", "rghost.ru", "joxi.ru", "mepic.ru", "screenshotlink.ru", "clip2net.com", "snag.gy" );
    $login_color_arr = array( "#CFCFCF", "#FFCCCC", "#FFCC99", "#FFFF99", "#FFFFCC", "#99FFFF", "#CCFFFF", "#CCCCFF", "#FFCCFF", "#CCCCCC", "#FF6666", "#FF9966", "#FFFF66", "#FFFF33", "#33FFFF", "#66FFFF", "#9999FF", "#FF99FF", "#C0C0C0", "#FF0000", "#FF9900", "#FFCC66", "#FFFF00", "#66CCCC", "#33CCFF", "#6666CC", "#CC66CC", "#999999", "#CC0000", "#FF6600", "#FFCC33", "#FFCC00", "#00CCCC", "#3366FF", "#6633FF", "#CC33CC", "#666666", "#990000", "#CC6600", "#CC9933", "#999900", "#3'999", "#3333FF", "#6600CC", "#993'9", "#333333", "#660000", "#993300", "#996633", "#666600", "#336666", "#000099", "#333'9", "#663366", "#000000", "#350000", "#663300", "#663333", "#333300", "#003333", "#000066", "#330099", "#330033" );
    $ban_period_arr = array( "30" => "30 минут", "60" => "1 час ", "720" => "12 часов", "1440" => "24 часа", "10080" => "7 дней", "43200" => "30 дней", "259200" => "6 месяцев", "525600" => "1 год" );
    if( isset($_SESSION["userLog_a"]) && isset($_SESSION["userPas_a"]) ) 
    {
        $sql_user = $mysqli->query("SELECT `id`,`username`,`user_status`,`avatar` FROM `tb_users` WHERE `username`='" . $user_name . "' AND md5(`password`)='" . $user_pass . "'") or die( my_json_encode("ERROR", $mysqli->error) );
        if( 0 < $sql_user->num_rows ) 
        {
            $row_user = $sql_user->fetch_assoc();
            $user_id = $row_user["id"];
            $user_name = $row_user["username"];
            $user_status = $row_user["user_status"];
            $user_avatar = $row_user["avatar"];
            $sql_user->free();
            if( $user_status != 1 ) 
            {
                $result_text = "ERROR";
                $message_text = "<script>setTimeout(function(){location.href = \"/login\";},5000);</script>";
                $message_text .= "Вы не являетесь администратором, доступ к этому разделу закрыт!";
                exit( my_json_encode($result_text, $message_text) );
            }

            $sql_chat = $mysqli->query("SELECT * FROM `tb_chat_users` WHERE `user_name`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
            if( 0 < $sql_chat->num_rows ) 
            {
                $row_chat = $sql_chat->fetch_assoc();
                $chat_status = $row_chat["user_status"];
                $chat_status = ($user_status == 1 && $chat_status != 2 && $chat_status != -1 ? 1 : $chat_status);
                $chat_color = $row_chat["user_color"];
                $chat_color_end = $row_chat["color_time_end"];
                $chat_color = (time() < $chat_color_end ? $chat_color : false);
                $chat_color = ($chat_status == 1 && $chat_color == false ? "#006600" : $chat_color);
                $chat_color = ($chat_status == 2 && $chat_color == false ? "#267E0E" : $chat_color);
                if( 0 < $chat_color_end && $chat_color_end < time() ) 
                {
                    $mysqli->query("UPDATE `tb_chat_users` SET `user_color`='', `color_time_end`='0' WHERE `user_name`='" . $user_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                }

            }
            else
            {
                $chat_status = 0;
                $chat_color = false;
                $chat_color_end = false;
                $mysqli->query("INSERT INTO `tb_chat_users` (`user_name`,`user_status`,`ip`) VALUES('" . $user_name . "','" . $user_status . "','" . escape($user_lastip) . "')") or die( my_json_encode("ERROR", $mysqli->error) );
            }

            $sql_chat->free();
            if( $option == "chat-config" ) 
            {
                $chat_access_reit = (isset($_POST["chat_access_reit"]) && is_string($_POST["chat_access_reit"]) && preg_match("|^[\\d]{1,4}\$|", intval($_POST["chat_access_reit"])) ? intval($_POST["chat_access_reit"]) : false);
                $cena_color_login = (isset($_POST["cena_color_login"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_color_login"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_color_login"])), 2, ".", "") : false);
                $cena_adv = (isset($_POST["cena_adv"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_adv"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_adv"])), 2, ".", "") : false);
                $cena_adv_color = (isset($_POST["cena_adv_color"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_adv_color"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_adv_color"])), 2, ".", "") : false);
                /*$status_mess_pay = (isset($_POST["status_mess_pay"]) && preg_match("|^[0-1]{1}\$|", trim($_POST["status_mess_pay"])) ? intval(trim($_POST["status_mess_pay"])) : 0);
                $method_mess_pay = (isset($_POST["method_mess_pay"]) && preg_match("|^[1-2]{1}\$|", trim($_POST["method_mess_pay"])) ? intval(trim($_POST["method_mess_pay"])) : 2);
                $cena_mess = (isset($_POST["cena_mess"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["cena_mess"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["cena_mess"])), 6, ".", "") : false);
                $lenght_mess_pay = (isset($_POST["lenght_mess_pay"]) && is_string($_POST["lenght_mess_pay"]) && preg_match("|^[\\d]{1,11}\$|", intval($_POST["lenght_mess_pay"])) ? intval($_POST["lenght_mess_pay"]) : false);
                $min_mess_pay = (isset($_POST["min_mess_pay"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["min_mess_pay"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["min_mess_pay"])), 2, ".", "") : false);
                $status_bonus = (isset($_POST["status_bonus"]) && preg_match("|^[0-1]{1}\$|", trim($_POST["status_bonus"])) ? intval(trim($_POST["status_bonus"])) : 0);
                $bonus_summa = (isset($_POST["bonus_summa"]) && preg_match("|^[+]?([\\d]{0,10})?(?:[\\.,][\\d]+)?\$|", trim($_POST["bonus_summa"])) ? number_format(floatval(str_ireplace(",", ".", $_POST["bonus_summa"])), 2, ".", "") : false);
                $bonus_period = (isset($_POST["bonus_period"]) && is_string($_POST["bonus_period"]) && preg_match("|^[\\d]{1,2}\$|", intval($_POST["bonus_period"])) ? intval($_POST["bonus_period"]) : false);
                $bonus_min_click = (isset($_POST["bonus_min_click"]) && is_string($_POST["bonus_min_click"]) && preg_match("|^[\\d]{1,2}\$|", intval($_POST["bonus_min_click"])) ? intval($_POST["bonus_min_click"]) : false);*/
                if( 1000 < $chat_access_reit ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Рейтинг не должен быть более 1000 баллов!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $chat_access_reit % 10 != 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Рейтинг должен быть кратен 10!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $cena_color_login < 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Стоимость цвета логина должна быть положительной!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $cena_adv < 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Стоимость размещения рекламы должна быть положительной!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $cena_adv_color < 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Стоимость выделения ссылки должна быть положительной!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                /*if( $lenght_mess_pay < 5 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Минимальное кол-во символов в сообщении для оплаты - 5";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_mess_pay == 1 && $cena_mess <= 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Стоимость сообщения должна быть больше нуля!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_mess_pay == 1 && $min_mess_pay <= 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Минимальная заработанная сумма для перевода на счёт пользователя должна быть больше нуля!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_mess_pay == 1 && $min_mess_pay <= $cena_mess ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Минимальная заработанная сумма для перевода на счёт пользователя должна быть больше стоимости за сообщение!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_bonus == 1 && $bonus_summa <= 0 ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Размер бонуса должен быть больше нуля!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_bonus == 1 && 72 < $bonus_period ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Периодичность получения бонуса должна быть в пределах от 1 до 72 часов!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $status_bonus == 1 && $bonus_min_click < 0 | 10 < $bonus_min_click ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Количество кликов по рекламе в ЧАТе должно быть в пределах от 0 до 10 кликов!";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $chat_access_reit . "' WHERE `item`='chat_access_reit'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $cena_color_login . "' WHERE `item`='cena_color_login'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $cena_adv . "' WHERE `item`='cena_adv'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $cena_adv_color . "' WHERE `item`='cena_adv_color'") or die( my_json_encode("ERROR", $mysqli->error) );
                /*$mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $status_mess_pay . "' WHERE `item`='status_mess_pay'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $method_mess_pay . "' WHERE `item`='method_mess_pay'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $cena_mess . "' WHERE `item`='cena_mess'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $lenght_mess_pay . "' WHERE `item`='lenght_mess_pay'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $min_mess_pay . "' WHERE `item`='min_mess_pay'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $status_bonus . "' WHERE `item`='status_bonus'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $bonus_summa . "' WHERE `item`='bonus_summa'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $bonus_period . "' WHERE `item`='bonus_period'") or die( my_json_encode("ERROR", $mysqli->error) );
                $mysqli->query("UPDATE `tb_chat_conf` SET `price`='" . $bonus_min_click . "' WHERE `item`='bonus_min_click'") or die( my_json_encode("ERROR", $mysqli->error) );*/
                $result_text = "OK";
                $message_text = "<script>setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                $message_text .= "<span class=\"msg-ok\">Изменения успешно сохранены!</span>";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-list" ) 
            {
                $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
				$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-list" . $security_key));
                /*if( $token_post == false | $token_post != $token_check ) 
                {
                    $result_text = "ERROR";
                    $message_text = "<tr><td colspan=\"2\" style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-red\">Не верный токен, обновите страницу!</b></td></tr>";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                $sql = $mysqli->query("SELECT * FROM `tb_chat_users` WHERE `user_status` IN ('1','2') ORDER BY `id` ASC") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $message_text = false;
                    while( $row = $sql->fetch_assoc() ) 
                    {
                        $token_moder_edit_form = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-edit-form" . $security_key));
                        $token_moder_conf_del = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-confirm-del" . $security_key));
                        $message_text .= "<tr id=\"tr-moder-" . $row["id"] . "\" class=\"tr-moder\">";
                        $message_text .= "<td align=\"center\"><b>" . $row["user_name"] . "</b></td>";
                        $message_text .= "<td align=\"left\">";
                        if( $row["user_status"] == 1 ) 
                        {
                            $message_text .= "<b class=\"text-red\">Администратор</b>, права: может удалять все сообщения, банить всех, редактировать рекламу";
                        }
                        else
                        {
                            if( $row["user_status"] == 2 ) 
                            {
                                $message_text .= "<b class=\"text-green\">Модератор</b>, права: может удалять все сообщения кроме своих, банить пользователей, редактировать рекламу";
                            }

                        }

                        $message_text .= "<div class=\"promotion-panel\">";
                        $message_text .= "<span class=\"promo-edit\" title=\"Изменить статус\" onClick=\"FuncChat(" . $row["id"] . ", 'moder-edit-form', false, '" . $token_moder_edit_form . "', true, 'Изменить статус', 500);\"></span>";
                        $message_text .= "<span class=\"promo-del\" title=\"Удалить из модераторов ЧАТа\" onClick=\"FuncChat(" . $row["id"] . ", 'moder-del-confirm', false, '" . $token_moder_conf_del . "', true, 'Подтверждение', 500);\"></span>";
                        $message_text .= "</div>";
                        $message_text .= "</td>";
                        $message_text .= "</tr>";
                    }
                    $sql->free();
                    $result_text = "OK";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "<tr><td colspan=\"2\" style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-red\">Модераторов ЧАТа нет!</b></td></tr>";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-add-form" ) 
            {
                $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
				$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-add-form" . $security_key));
                $token_next = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-add" . $security_key));
                /*if( $token_post == false | $token_post != $token_check ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Не верный токен, обновите страницу!";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                $message_text = "<div id=\"newform\" class=\"form-chat-moder ec.c\"><form method=\"POST\" id=\"form_moder\" onSubmit=\"FuncChat(false, 'moder-add', \$(this).attr('id'), '" . $token_next . "'); return false;\">";
                $message_text .= "<table class=\"tables\" style=\"padding:0; margin:0 auto;\">";
                $message_text .= "<tbody>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" height=\"25\" width=\"100\">Логин</td>";
                $message_text .= "<td align=\"left\" height=\"25\"><input type=\"text\" id=\"moder_name\" name=\"moder_name\" value=\"\" class=\"ok\" maxlength=\"25\" required=\"required\"></td>";
                $message_text .= "</tr>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" height=\"25\">Статус</td>";
                $message_text .= "<td align=\"left\" height=\"25\">";
                $message_text .= "<select id=\"moder_status\" name=\"moder_status\" class=\"ok\">";
                $message_text .= "<option value=\"1\">Администратор (права: может удалять все сообщения, банить всех, редактировать рекламу)</option>";
                $message_text .= "<option value=\"2\" selected=\"selected\">Модератор (права: может удалять все сообщения кроме своих, банить пользователей, редактировать рекламу)</option>";
                $message_text .= "</select>";
                $message_text .= "</td>";
                $message_text .= "</tr>";
                $message_text .= "</tbody>";
                $message_text .= "</table>";
                $message_text .= "<div align=\"center\" style=\"padding:7px 0 3px;\"><button class=\"sd_sub green\">Добавить</button></div>";
                $message_text .= "</form></div>";
                $message_text .= "<div align=\"center\" id=\"info-msg-chat\" style=\"display:none;\"></div>";
                $result_text = "OK";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-add" ) 
            {
                $moder_name = (isset($_POST["moder_name"]) && preg_match("|^[a-zA-Z0-9\\-_-]{3,25}\$|", trim($_POST["moder_name"])) ? htmlentities(stripslashes(trim($_POST["moder_name"]))) : false);
                $moder_status = (isset($_POST["moder_status"]) && preg_match("|^[1-2]{1}\$|", trim($_POST["moder_status"])) ? intval(trim($_POST["moder_status"])) : 0);
                $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
				$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-add" . $security_key));
                /*if( $token_post == false | $token_post != $token_check ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Не верный токен, обновите страницу!";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                if( $moder_name == false ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Укажите логин пользователя!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $moder_status == false ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Укажите статус пользователя в ЧАТе!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql = $mysqli->query("SELECT `id`,`username` FROM `tb_users` WHERE `username`='" . escape($moder_name) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $row = $sql->fetch_assoc();
                    $moder_name = $row["username"];
                    $sql->free();
                    $mysqli->query("INSERT INTO `tb_chat_users` (`user_name`,`user_status`) VALUES('" . $moder_name . "','" . $moder_status . "') ON DUPLICATE KEY UPDATE `user_name`='" . $moder_name . "', `user_status`='" . $moder_status . "', `banned_user`='', `ban_info`='', `ban_period`='0', `ban_time`='0', `ban_time_end`='0'\r\n\t\t\t\t") or die( my_json_encode("ERROR", $mysqli->error) );
                    $mysqli->query("UPDATE `tb_chat_mess` SET `user_status`='" . $moder_status . "' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $mysqli->query("UPDATE `tb_chat_online` SET `user_status`='" . $moder_status . "' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $result_text = "OK";
                    $message_text = "<script>FuncChat(false, \"moder-list\", false, token_moder); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                    $message_text .= "<span class=\"msg-ok\">Модератор успешно добавлен!</span>";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "Пользователя " . $moder_name . " нет в системе!";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-edit-form" ) 
            {
                $sql = $mysqli->query("SELECT `id`,`user_name`,`user_status` FROM `tb_chat_users` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $row = $sql->fetch_assoc();
                    $id = $row["id"];
                    $moder_name = $row["user_name"];
                    $moder_status = $row["user_status"];
                    $sql->free();
					$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
                    $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-edit-form" . $security_key));
                    $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-edit" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    $message_text = "<div id=\"newform\" class=\"form-chat-moder ec.c\"><form method=\"POST\" id=\"form_moder\" onSubmit=\"FuncChat(" . $id . ", 'moder-edit', \$(this).attr('id'), '" . $token_next . "'); return false;\">";
                    $message_text .= "<table class=\"tables\" style=\"padding:0; margin:0 auto;\">";
                    $message_text .= "<tbody>";
                    $message_text .= "<tr>";
                    $message_text .= "<td align=\"left\" height=\"25\" width=\"100\">Логин</td>";
                    $message_text .= "<td align=\"left\" height=\"25\"><b>" . $moder_name . "</b></td>";
                    $message_text .= "</tr>";
                    $message_text .= "<tr>";
                    $message_text .= "<td align=\"left\" height=\"25\">Статус</td>";
                    $message_text .= "<td align=\"left\" height=\"25\">";
                    $message_text .= "<select id=\"moder_status\" name=\"moder_status\" class=\"ok\">";
                    $message_text .= "<option value=\"1\" " . (($moder_status == 1 ? "selected=\"selected\"" : false)) . ">Администратор (права: может удалять все сообщения, банить всех, редактировать рекламу)</option>";
                    $message_text .= "<option value=\"2\" " . (($moder_status == 2 ? "selected=\"selected\"" : false)) . ">Модератор (права: может удалять все сообщения кроме своих, банить пользователей, редактировать рекламу)</option>";
                    $message_text .= "</select>";
                    $message_text .= "</td>";
                    $message_text .= "</tr>";
                    $message_text .= "</tbody>";
                    $message_text .= "</table>";
                    $message_text .= "<div align=\"center\" style=\"padding:7px 0 3px;\"><button class=\"sd_sub green\">Сохранить</button></div>";
                    $message_text .= "</form></div>";
                    $message_text .= "<div align=\"center\" id=\"info-msg-chat\" style=\"display:none;\"></div>";
                    $result_text = "OK";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "Модератор не найден!";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-edit" ) 
            {
                $sql = $mysqli->query("SELECT `id`,`user_name` FROM `tb_chat_users` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $row = $sql->fetch_assoc();
                    $id = $row["id"];
                    $moder_name = $row["user_name"];
                    $sql->free();
                    $token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-edit" . $security_key));
                    $moder_status = (isset($_POST["moder_status"]) && preg_match("|^[1-2]{1}\$|", trim($_POST["moder_status"])) ? intval(trim($_POST["moder_status"])) : 0);
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    if( $moder_status == false ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите статус пользователя в ЧАТе!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql = $mysqli->query("SELECT `id`,`username` FROM `tb_users` WHERE `username`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $moder_name = $row["username"];
                        $sql->free();
                        $mysqli->query("INSERT INTO `tb_chat_users` (`user_name`,`user_status`) \tVALUES('" . $moder_name . "','" . $moder_status . "') \tON DUPLICATE KEY UPDATE `user_name`='" . $moder_name . "', `user_status`='" . $moder_status . "', `banned_user`='', `ban_info`='', `ban_period`='0', `ban_time`='0', `ban_time_end`='0' ") or die( my_json_encode("ERROR", $mysqli->error) );
                        $mysqli->query("UPDATE `tb_chat_mess` SET `user_status`='" . $moder_status . "' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $mysqli->query("UPDATE `tb_chat_online` SET `user_status`='" . $moder_status . "' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $result_text = "OK";
                        $message_text = "<script>FuncChat(false, \"moder-list\", false, token_moder); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                        $message_text .= "<span class=\"msg-ok\">Изменения успешно сохранены!</span>";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $mysqli->query("DELETE FROM `tb_chat_users` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $result_text = "ERROR";
                    $message_text = "<script>FuncChat(false, \"moder-list\", false, token_moder); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                    $message_text .= "Пользователя " . $moder_name . " нет в системе!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "Модератор не найден!";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "moder-del-confirm" | $option == "moder-del" ) 
            {
                $sql = $mysqli->query("SELECT `id`,`user_name` FROM `tb_chat_users` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $row = $sql->fetch_assoc();
                    $id = $row["id"];
                    $moder_name = $row["user_name"];
                    $sql->free();
                    if( $option == "moder-del-confirm" ) 
                    {
                        $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
						$token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-confirm-del" . $security_key));
                        $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-del" . $security_key));
                    }
                    else
                    {
                        $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
						$token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-moder-del" . $security_key));
                    }

                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    if( $option == "moder-del-confirm" ) 
                    {
                        $message_text = "<div align=\"center\" style=\"margin:10px auto 20px;\">";
                        $message_text .= "Исключить пользователя <b>" . $moder_name . "</b> из модераторов?";
                        $message_text .= "</div>";
                        $message_text .= "<div align=\"center\" style=\"margin:0 auto;\">";
                        $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncChat(" . $id . ", 'moder-del', false, '" . $token_next . "'); return false;\"\">Да</span>";
                        $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
                        $message_text .= "</div>";
                        $result_text = "OK";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $mysqli->query("UPDATE `tb_chat_users` SET `user_status`='0' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $mysqli->query("UPDATE `tb_chat_mess` SET `user_status`='0' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $mysqli->query("UPDATE `tb_chat_online` SET `user_status`='0' WHERE `user_name`='" . $moder_name . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $result_text = "OK";
                    $message_text = "<script>FuncChat(false, \"moder-list\", false, token_moder); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                    $message_text .= "<span class=\"msg-ok\">Пользователь " . $moder_name . " успешно исключен из модераторов!</span>";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "Модератор не найден!";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "ban-users-list" ) 
            {
                $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
				$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-users-list" . $security_key));
                /*if( $token_post == false | $token_post != $token_check ) 
                {
                    $result_text = "ERROR";
                    $message_text = "<tr><td colspan=\"6\" style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-red\">Не верный токен, обновите страницу!</b></td></tr>";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                $sql = $mysqli->query("SELECT * FROM `tb_chat_users` WHERE `user_status`='-1' ORDER BY `ban_time` DESC") or die( my_json_encode("ERROR", $mysqli->error) );
                if( 0 < $sql->num_rows ) 
                {
                    $message_text = false;
                    while( $row = $sql->fetch_assoc() ) 
                    {
                        $token_ban_conf_del = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-confirm-del" . $security_key));
                        $message_text .= "<tr id=\"tr-moder-" . $row["id"] . "\" class=\"tr-moder\">";
                        $message_text .= "<td align=\"center\"><b class=\"text-blue\">" . $row["user_name"] . "</b></td>";
                        $message_text .= "<td align=\"center\"><span class=\"text-grey\">" . $row["banned_user"] . "</span></td>";
                        $message_text .= "<td align=\"left\">" . $row["ban_info"] . "</td>";
                        $message_text .= "<td align=\"center\">" . ((isset($ban_period_arr[$row["ban_period"]]) ? "<span class=\"text-red\">" . $ban_period_arr[$row["ban_period"]] . "</span>" : "не определен")) . "</td>";
                        $message_text .= "<td align=\"center\">";
                        $message_text .= "<div style=\"display:inline-block;\">" . DATE("H:i <b>d.m.Y</b>", $row["ban_time"]) . " &mdash; " . DATE("H:i <b>d.m.Y</b>", $row["ban_time_end"]) . "</div>";
                        $message_text .= "</td>";
                        $message_text .= "<td align=\"center\">";
                        $message_text .= "<span class=\"promo-del\" title=\"Разблокировать пользователя\" onClick=\"FuncChat(" . $row["id"] . ", 'ban-confirm-del', false, '" . $token_ban_conf_del . "', true, 'Подтверждение', 500);\"></span>";
                        $message_text .= "</td>";
                        $message_text .= "</tr>";
                    }
                    $sql->free();
                    $result_text = "OK";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $sql->free();
                $result_text = "ERROR";
                $message_text = "<tr><td colspan=\"6\" style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-grey\">Заблокированных пользователей нет!</b></td></tr>";
                exit( my_json_encode($result_text, $message_text) );
            }

            if( $option == "ban-user-add-form" ) 
            {
                $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
				$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-user-add-form" . $security_key));
                $token_next = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-user-add" . $security_key));
                /*if( $token_post == false | $token_post != $token_check ) 
                {
                    $result_text = "ERROR";
                    $message_text = "Не верный токен, обновите страницу!";
                    exit( my_json_encode($result_text, $message_text) );
                }*/

                $message_text = "<div id=\"newform\" class=\"form-chat-ban ec.c\"><form method=\"POST\" id=\"form_ban_user\" onSubmit=\"FuncChat(false, 'ban-user-add', \$(this).attr('id'), '" . $token_next . "'); return false;\">";
                $message_text .= "<table class=\"tables\" style=\"padding:0; margin:0 auto;\">";
                $message_text .= "<tbody>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" width=\"150\" height=\"25\">Логин</td>";
                $message_text .= "<td align=\"left\" height=\"25\"><input type=\"text\" id=\"ban_user\" name=\"ban_user\" value=\"\" class=\"ok\" maxlength=\"25\" required=\"required\"></td>";
                $message_text .= "</tr>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" height=\"25\">Период блокировки</td>";
                $message_text .= "<td align=\"left\" height=\"25\">";
                $message_text .= "<select id=\"ban_period\" name=\"ban_period\" class=\"ok\">";
                foreach( $ban_period_arr as $key => $val ) 
                {
                    $message_text .= "<option value=\"" . $key . "\">" . $val . "</option>";
                }
                $message_text .= "</select>";
                $message_text .= "</td>";
                $message_text .= "</tr>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" height=\"25\">Причина блокировки</td>";
                $message_text .= "<td align=\"left\" height=\"25\"><input type=\"text\" id=\"ban_cause\" name=\"ban_cause\" value=\"Нарушение правил чата п.\" class=\"ok\" maxlength=\"200\" required=\"required\"></td>";
                $message_text .= "</tr>";
                $message_text .= "<tr>";
                $message_text .= "<td align=\"left\" height=\"25\">Удалить сообщения</td>";
                $message_text .= "<td align=\"left\" height=\"25\"><input type=\"checkbox\" id=\"ban_mess_del\" name=\"ban_mess_del\" value=\"1\" checked=\"checked\" style=\"width:16px; height:16px;\"></td>";
                $message_text .= "</tr>";
                $message_text .= "</tbody>";
                $message_text .= "</table>";
                $message_text .= "<div align=\"center\" style=\"padding:10px 0 5px;\"><button class=\"sd_sub big red\">Заблокировать</button></div>";
                $message_text .= "</form></div>";
                $message_text .= "<div align=\"center\" id=\"info-msg-chat\" style=\"display:none;\"></div>";
                $result_text = "OK";
                exit( my_json_encode($result_text, $message_text) );
            }
            else
            {
                if( $option == "ban-user-add" ) 
                {
                    $ban_user = (isset($_POST["ban_user"]) && preg_match("|^[a-zA-Z0-9\\-_-]{3,25}\$|", trim($_POST["ban_user"])) ? htmlentities(stripslashes(trim($_POST["ban_user"]))) : false);
                    $ban_period = (isset($_POST["ban_period"]) && array_key_exists(intval($_POST["ban_period"]), $ban_period_arr) !== false ? intval($_POST["ban_period"]) : false);
                    $ban_cause = (isset($_POST["ban_cause"]) ? limitatexto(limpiarez($_POST["ban_cause"]), 200) : false);
                    $ban_mess_del = (isset($_POST["ban_mess_del"]) && preg_match("|^[0-1]{1}\$|", trim($_POST["ban_mess_del"])) ? intval(trim($_POST["ban_mess_del"])) : false);
                    $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
					$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-user-add" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    if( $ban_user == false ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите логин пользователя!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( $ban_period == false ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите период блокировки!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( $ban_cause == false ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите причину блокировки!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( mb_strtolower($ban_cause, "CP1251") == "нарушение правил чата п." ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите в причине блокировки пункт правил!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( strtolower($user_name) == strtolower($ban_user) ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Вы не можете заблокировать себя!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql = $mysqli->query("SELECT `id`,`username` FROM `tb_users` WHERE `username`='" . escape($ban_user) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $ban_user = $row["username"];
                        $sql->free();
                        $mysqli->query("INSERT INTO `tb_chat_users` (`user_name`,`user_status`,`banned_user`,`ban_info`,`ban_period`,`ban_time`,`ban_time_end`) \r\n\t\t\t\tVALUES('" . $ban_user . "','-1','" . $user_name . "','" . escape($ban_cause) . "','" . escape($ban_period) . "','" . time() . "','" . (time() + $ban_period * 60) . "') \r\n\t\t\t\tON DUPLICATE KEY UPDATE `user_status`='-1', `banned_user`='" . $user_name . "', `ban_info`='" . escape($ban_cause) . "', `ban_period`='" . escape($ban_period) . "', `ban_time`='" . time() . "', `ban_time_end`='" . (time() + $ban_period * 60) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        if( $ban_mess_del == 1 ) 
                        {
                            $mysqli->query("DELETE FROM `tb_chat_mess` WHERE `user_name`='" . $ban_user . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        }

                        $mysqli->query("DELETE FROM `tb_chat_online` WHERE `user_name`='" . $ban_user . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $chat_mess = "[font color=\"red\"]Пользователь [b]" . $ban_user . "[/b] получил бан на [b]" . $ban_period_arr[$ban_period] . "[/b], по следующей причине: " . mb_strtolower($ban_cause, "CP1251") . "[/font]";
                        $chat_mess = desc_bb($chat_mess);
                        $mysqli->query("INSERT INTO `tb_chat_mess` (`status`,`user_status`,`user_id`,`user_name`,`user_avatar`,`user_color`,`chat_mess`,`time`,`ip`) VALUES\r\n\t\t\t\t('1','" . $user_status . "','" . $user_id . "','" . $user_name . "','" . escape($user_avatar) . "','" . escape($chat_color) . "','" . escape($chat_mess) . "','" . time() . "','" . escape($user_lastip) . "')") or die( my_json_encode("ERROR", $mysqli->error) );
                        $last_id_mess = $mysqli->insert_id;
                        $sql_cng = $mysqli->query("SELECT `id` FROM `tb_chat_mess` WHERE `status`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $cnt_cng = ($sql_cng->num_rows < $tab_max_mess ? intval($tab_max_mess - $sql_cng->num_rows) : 0);
                        if( 0 < $cnt_cng ) 
                        {
                            $mysqli->query("UPDATE `tb_chat_mess` SET `status`='1' WHERE `status`='0' ORDER BY `id` DESC LIMIT " . $cnt_cng) or die( my_json_encode("ERROR", $mysqli->error) );
                        }

                        $sql_cng->free();
                        $data_socket = array( "chat_op" => "mess-load", "last_id" => 0, "time" => time() );
                        $data_socket = json_encode_socket($data_socket);
                        $socketio = @new SocketIO();
                        $sentSocket = @$socketio->send("ssl://" . $_SERVER["HTTP_HOST"], 5000, "Chat Update Ajax", $data_socket);
                        $result_text = "OK";
                        $message_text = "<script>FuncChat(false, \"ban-users-list\", false, token_chat); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                        $message_text .= "<span class=\"msg-ok\">Пользователь " . $ban_user . " успешно заблокирован!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Пользователя " . $ban_user . " нет в системе!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "ban-confirm-del" | $option == "ban-del" ) 
                {
                    $sql = $mysqli->query("SELECT `id`,`user_name` FROM `tb_chat_users` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $id = $row["id"];
                        $ban_user = $row["user_name"];
                        $sql->free();
                        if( $option == "ban-confirm-del" ) 
                        {
                            $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
							$token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-confirm-del" . $security_key));
                            $token_next = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-del" . $security_key));
                        }
                        else
                        {
                            $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
							$token_check = strtolower(md5($id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-ban-del" . $security_key));
                        }

                        /*if( $token_post == false | $token_post != $token_check ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Не верный токен, обновите страницу!";
                            exit( my_json_encode($result_text, $message_text) );
                        }*/

                        if( $option == "ban-confirm-del" ) 
                        {
                            $message_text = "<div align=\"center\" style=\"margin:10px auto 20px;\">";
                            $message_text .= "Разблокировать пользователя <b>" . $ban_user . "</b> ?";
                            $message_text .= "</div>";
                            $message_text .= "<div align=\"center\" style=\"margin:0 auto;\">";
                            $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncChat(" . $id . ", 'ban-del', false, '" . $token_next . "'); return false;\"\">Да</span>";
                            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
                            $message_text .= "</div>";
                            $result_text = "OK";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        $mysqli->query("UPDATE `tb_chat_users` SET `user_status`='0', `banned_user`='', `ban_info`='', `ban_period`='0', `ban_time`='0', `ban_time_end`='0' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $mysqli->query("UPDATE `tb_chat_mess` SET `user_status`='0' WHERE `user_name`='" . $ban_user . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $mysqli->query("UPDATE `tb_chat_online` SET `user_status`='0' WHERE `user_name`='" . $ban_user . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $result_text = "OK";
                        $message_text = "<script>FuncChat(false, \"ban-users-list\", false, token_chat); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                        $message_text .= "<span class=\"msg-ok\">Пользователь " . $ban_user . " успешно разблокирован!</span>";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Блокировка не найдена!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-list" ) 
                {
                    $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
					$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-list" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "<tr><td style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-red\">Не верный токен, обновите страницу!</b></td></tr>";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    $system_pay[0] = "Админка";
                    $system_pay[1] = "Рекламный счёт";
                    $system_pay[2] = "Внутренний счёт";
                    $sql = $mysqli->query("SELECT * FROM `tb_chat_adv` WHERE `status`='1' ORDER BY `id` DESC") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $message_text = false;
                        while( $row = $sql->fetch_assoc() ) 
                        {
                            $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
							$token_promo_form = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-form" . $security_key));
                            $token_promo_conf_del = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-confirm-del" . $security_key));
                            $message_text .= "<tr id=\"tr-adv-" . $row["id"] . "\" class=\"tr-adv\">";
                            $message_text .= "<td style=\"width:0; padding:0; margin:0;\"></td>";
                            $message_text .= "<td align=\"left\">";
                            $message_text .= "<div style=\"margin-bottom:3px;\">";
                            $message_text .= "<img id=\"adv-fav-" . $row["id"] . "\" width=\"16\" height=\"16\" border=\"0\" alt=\"\" style=\"margin:0; padding:0; padding-bottom:2px; padding-right:5px;\" src=\"//www.google.com/s2/favicons?domain=" . @gethost($row["url"]) . "\" align=\"absmiddle\" />";
                            $message_text .= "<a id=\"adv-title-" . $row["id"] . "\" class=\"" . (($row["color"] == 1 ? "promo-link-h" : "promo-link-n")) . "\" href=\"" . $row["url"] . "\" target=\"_blank\" title=\"" . $row["url"] . "\">" . $row["description"] . "</a>";
                            $message_text .= "</div>";
                            $message_text .= "<div class=\"info-text\" style=\"display:inline-block;\">";
                            $message_text .= "ID:<span style=\"padding:0px 8px 0 3px; font-weight:bold;\">" . $row["id"] . "</span>";
                            $message_text .= "Способ оплаты:<span style=\"padding:0px 8px 0 3px; font-weight:bold;\">" . $system_pay[$row["method_pay"]] . "</span>";
                            $message_text .= "Рекламодатель:<span style=\"padding:0px 8px 0 3px; font-weight:bold;\">" . $row["user_name"] . "</span>";
                            //$message_text .= "Переходов:<span style=\"padding:0px 8px 0 3px; font-weight:bold;\">" . $row["views"] . "</span>";
                            $message_text .= "</div>";
                            $message_text .= "<div class=\"promotion-panel\">";
                            $message_text .= "<span class=\"promo-edit\" title=\"Редактировать рекламную площадку\" onClick=\"FuncChat(" . $row["id"] . ", 'promo-form', false, '" . $token_promo_form . "', true, 'Редактирование ссылки', 550);\"></span>";
                            $message_text .= "<span class=\"promo-del\" title=\"Удалить рекламную площадку\" onClick=\"FuncChat(" . $row["id"] . ", 'promo-confirm-del', false, '" . $token_promo_conf_del . "', true, 'Подтверждение', 500);\"></span>";
                            $message_text .= "</div>";
                            $message_text .= "</td>";
                            $message_text .= "<td align=\"center\" width=\"60\" nowrap=\"nowrap\">";
                            $message_text .= "<span class=\"add-money-yes\" title=\"Стоимость заказа\">" . my_num_format($row["money"], 2, ".", "", 2) . "</span>";
                            $message_text .= "</td>";
                            $message_text .= "</tr>";
                        }
                        $sql->free();
                        $result_text = "OK";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "<tr><td style=\"text-align:center; padding:3px 2px 2px;\"><b class=\"text-grey\">Реклама не найдена!</b></td></tr>";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-form" ) 
                {
                    $sql = $mysqli->query("SELECT * FROM `tb_chat_adv` WHERE `id`='" . $id . "' AND `status`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
						$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
                        $token_check = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-form" . $security_key));
                        $token_next = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-save" . $security_key));
                        $sql->free();
                        /*if( $token_post == false | $token_post != $token_check ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Не верный токен, обновите страницу!";
                            exit( my_json_encode($result_text, $message_text) );
                        }*/

                        $message_text = "<div id=\"newform\" class=\"form-chat-promo ec.c\" onKeyPress=\"CtrlEnter(event);\"><form method=\"POST\" id=\"form_promotion\" onSubmit=\"FuncChat(" . $row["id"] . ", 'promo-save', \$(this).attr('id'), '" . $token_next . "'); return false;\">";
                        $message_text .= "<table class=\"tables\" style=\"padding:0; margin:0 auto;\">";
                        $message_text .= "<tbody>";
                        $message_text .= "<tr>";
                        $message_text .= "<td align=\"left\" height=\"25\" width=\"170\">Описание ссылки</td>";
                        $message_text .= "<td align=\"left\" height=\"25\"><input type=\"text\" id=\"promo_desc\" name=\"promo_desc\" value=\"" . $row["description"] . "\" class=\"ok\" maxlength=\"50\" required=\"required\"></td>";
                        $message_text .= "</tr>";
                        $message_text .= "<tr>";
                        $message_text .= "<td align=\"left\" height=\"25\">URL сайта (включая http://)</td>";
                        $message_text .= "<td align=\"left\" height=\"25\"><input type=\"url\" id=\"promo_url\" name=\"promo_url\" value=\"" . $row["url"] . "\" class=\"ok\" maxlength=\"300\" required=\"required\"></td>";
                        $message_text .= "</tr>";
                        $message_text .= "<tr>";
                        $message_text .= "<td align=\"left\" height=\"25\">Выделить ссылку</td>";
                        $message_text .= "<td align=\"left\" height=\"25\">";
                        $message_text .= "<select id=\"promo_color\" name=\"promo_color\" class=\"ok\">";
                        $message_text .= "<option value=\"0\" " . (($row["color"] == 0 ? "selected=\"selected\"" : false)) . ">Нет</option>";
                        $message_text .= "<option value=\"1\" " . (($row["color"] == 1 ? "selected=\"selected\"" : false)) . ">Да, красным</option>";
                        $message_text .= "</select>";
                        $message_text .= "</td>";
                        $message_text .= "</tr>";
                        $message_text .= "</tbody>";
                        $message_text .= "</table>";
                        $message_text .= "<div align=\"center\" style=\"padding:7px 0 3px;\"><button id=\"SubMit\" class=\"sd_sub green\">Сохранить</button></div>";
                        $message_text .= "</form></div>";
                        $message_text .= "<div align=\"center\" id=\"info-msg-chat\" style=\"display:none;\"></div>";
                        $result_text = "OK";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Реклама с ID: " . $id . " не найдена!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-save" ) 
                {
                    $sql = $mysqli->query("SELECT * FROM `tb_chat_adv` WHERE `id`='" . $id . "' AND `status`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $sql->free();
						$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
                        $token_check = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-save" . $security_key));
                        /*if( $token_post == false | $token_post != $token_check ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Не верный токен, обновите страницу!";
                            exit( my_json_encode($result_text, $message_text) );
                        }*/

                        $promo_desc = (isset($_POST["promo_desc"]) ? limitatexto(limpiarez($_POST["promo_desc"]), 50) : false);
                        $promo_url = (isset($_POST["promo_url"]) ? str_ireplace("&amp;", "&", limitatexto(limpiarez($_POST["promo_url"]), 300)) : false);
                        $promo_color = (isset($_POST["promo_color"]) && preg_match("|^[0-1]{1}\$|", trim($_POST["promo_color"])) ? intval(trim($_POST["promo_color"])) : 0);
                        $black_url = StringUrl($promo_url);
                        $sql_bl = $mysqli->query("SELECT `domen` FROM `tb_black_sites` WHERE `domen` IN (" . $black_url . ")") or die( my_json_encode("ERROR", $mysqli->error) );
                        $cnt_bl = $sql_bl->num_rows;
                        if( $cnt_bl <= 0 ) 
                        {
                            $sql_bl->free();
                        }

                        if( $promo_desc == false ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Укажите описание ссылки!";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        if( strlen($promo_desc) < 5 ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Описание ссылки должно содержать не менее 5 символов!";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        if( $promo_url == false | $promo_url == "http://" | $promo_url == "https://" ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Вы не указали URL-адрес сайта!";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        if( substr($promo_url, 0, 7) != "http://" && substr($promo_url, 0, 8) != "https://" ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "URL-адрес сайта указан неверно!";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        if( 0 < $cnt_bl && $black_url != false ) 
                        {
                            $row_bl = $sql_bl->fetch_assoc();
                            $sql_bl->free();
                            $result_text = "ERROR";
                            $message_text = "Сайт " . $row_bl["domen"] . " находится в черном списке проекта " . strtoupper($_SERVER["HTTP_HOST"]) . "";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        $mysqli->query("UPDATE `tb_chat_adv` SET `description`='" . escape($promo_desc) . "', `url`='" . escape($promo_url) . "', `color`='" . escape($promo_color) . "' WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $data_socket = array( "chat_op" => "promo-load", "last_id" => $id, "time" => time() );
                        $data_socket = json_encode_socket($data_socket);
                        $socketio = @new SocketIO();
                        $sentSocket = @$socketio->send("ssl://" . $_SERVER["HTTP_HOST"], 5000, "Chat Update Ajax", $data_socket);
                        $promo_class = ($promo_color == 1 ? "promo-link-h" : "promo-link-n");
                        $promo_domen = "//www.google.com/s2/favicons?domain=" . @gethost($promo_url);
                        $result_text = "OK";
                        $message_text = "<script>\$(\"#adv-fav-" . $id . "\").attr(\"src\", \"" . $promo_domen . "\"); \$(\"#adv-title-" . $id . "\").attr({\"class\": \"" . $promo_class . "\", \"title\":\"" . $promo_url . "\"}).html(\"" . $promo_desc . "\"); \$(\".box-modal-content\").css(\"padding\", \"5px\"); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                        $message_text .= "<span class=\"msg-ok\">Изменения успешно сохранены!</span>";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Реклама с ID: " . $id . " не найдена!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-confirm-del" | $option == "promo-del" ) 
                {
                    $sql = $mysqli->query("SELECT * FROM `tb_chat_adv` WHERE `id`='" . $id . "' AND `status`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $sql->free();
                        if( $option == "promo-confirm-del" ) 
                        {
                            $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
							$token_check = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-confirm-del" . $security_key));
                            $token_next = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-del" . $security_key));
                        }
                        else
                        {
                            $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
							$token_check = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-del" . $security_key));
                        }

                       /* if( $token_post == false | $token_post != $token_check ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Не верный токен, обновите страницу!";
                            exit( my_json_encode($result_text, $message_text) );
                        }*/

                        if( $option == "promo-confirm-del" ) 
                        {
                            $message_text = "<div align=\"center\" style=\"margin:10px auto 20px;\">";
                            $message_text .= "Удалить рекламную площадку ID:<b>" . $id . "</b>?";
                            $message_text .= "</div>";
                            $message_text .= "<div align=\"center\" style=\"margin:0 auto;\">";
                            $message_text .= "<span class=\"sd_sub green\" style=\"min-width:30px;\" onClick=\"FuncChat(" . $id . ", 'promo-del', false, '" . $token_next . "'); return false;\"\">Да</span>";
                            $message_text .= "<span class=\"sd_sub red\" style=\"min-width:30px;\" onClick=\"\$('#LoadModal').modalpopup('close'); return false;\">Нет</span>";
                            $message_text .= "</div>";
                            $result_text = "OK";
                            exit( my_json_encode($result_text, $message_text) );
                        }

                        $mysqli->query("DELETE FROM `tb_chat_adv` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $data_socket = array( "chat_op" => "promo-load", "last_id" => 0, "time" => time() );
                        $data_socket = json_encode_socket($data_socket);
                        $socketio = @new SocketIO();
                        $sentSocket = @$socketio->send("ssl://" . $_SERVER["HTTP_HOST"], 5000, "Chat Update Ajax", $data_socket);
                        $result_text = "OK";
                        $message_text = "<script>\$(\"#tr-adv-" . $id . "\").remove(); \$(\".box-modal-content\").css(\"padding\", \"5px\"); if(\$(\"#adv-chat .tr-adv\").length < 1) {FuncChat(false, \"promo-list\", false, token_promo);} setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                        $message_text .= "<span class=\"msg-ok\">Реклама успешно удалена!</span>";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Реклама с ID: " . $id . " не найдена!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-add-form" ) 
                {
                    $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
					$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-add-form" . $security_key));
                    $token_next = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-add" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    $message_text = "<div id=\"newform\" class=\"form-chat-promo ec.c\" onKeyPress=\"CtrlEnter(event);\"><form method=\"POST\" id=\"form_promotion\" onSubmit=\"FuncChat(false, 'promo-add', \$(this).attr('id'), '" . $token_next . "'); return false;\">";
                    $message_text .= "<table class=\"tables\" style=\"padding:0; margin:0 auto;\">";
                    $message_text .= "<tbody>";
                    $message_text .= "<tr>";
                    $message_text .= "<td align=\"left\" height=\"25\" width=\"170\">Описание ссылки</td>";
                    $message_text .= "<td align=\"left\" height=\"25\"><input type=\"text\" id=\"promo_desc\" name=\"promo_desc\" value=\"\" class=\"ok\" maxlength=\"50\" required=\"required\"></td>";
                    $message_text .= "</tr>";
                    $message_text .= "<tr>";
                    $message_text .= "<td align=\"left\" height=\"25\">URL сайта (включая http://)</td>";
                    $message_text .= "<td align=\"left\" height=\"25\"><input type=\"url\" id=\"promo_url\" name=\"promo_url\" value=\"\" class=\"ok\" maxlength=\"300\" required=\"required\"></td>";
                    $message_text .= "</tr>";
                    $message_text .= "<tr>";
                    $message_text .= "<td align=\"left\" height=\"25\">Выделить ссылку</td>";
                    $message_text .= "<td align=\"left\" height=\"25\">";
                    $message_text .= "<select id=\"promo_color\" name=\"promo_color\" class=\"ok\">";
                    $message_text .= "<option value=\"0\">Нет</option>";
                    $message_text .= "<option value=\"1\">Да, красным</option>";
                    $message_text .= "</select>";
                    $message_text .= "</td>";
                    $message_text .= "</tr>";
                    $message_text .= "</tbody>";
                    $message_text .= "</table>";
                    $message_text .= "<div align=\"center\" style=\"padding:7px 0 3px;\"><button id=\"SubMit\" class=\"sd_sub green\">Добавить</button></div>";
                    $message_text .= "</form></div>";
                    $message_text .= "<div align=\"center\" id=\"info-msg-chat\" style=\"display:none;\"></div>";
                    $result_text = "OK";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "promo-add" ) 
                {
                    $security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
					$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-promo-add" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    $promo_desc = (isset($_POST["promo_desc"]) ? limitatexto(limpiarez($_POST["promo_desc"]), 50) : false);
                    $promo_url = (isset($_POST["promo_url"]) ? str_ireplace("&amp;", "&", limitatexto(limpiarez($_POST["promo_url"]), 300)) : false);
                    $promo_color = (isset($_POST["promo_color"]) && preg_match("|^[0-1]{1}\$|", trim($_POST["promo_color"])) ? intval(trim($_POST["promo_color"])) : 0);
                    $method_pay = 0;
                    $black_url = StringUrl($promo_url);
                    $sql_bl = $mysqli->query("SELECT `domen` FROM `tb_black_sites` WHERE `domen` IN (" . $black_url . ")") or die( my_json_encode("ERROR", $mysqli->error) );
                    $cnt_bl = $sql_bl->num_rows;
                    if( $cnt_bl <= 0 ) 
                    {
                        $sql_bl->free();
                    }

                    if( $promo_desc == false ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Укажите описание ссылки!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( strlen($promo_desc) < 5 ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Описание ссылки должно содержать не менее 5 символов!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( $promo_url == false | $promo_url == "http://" | $promo_url == "https://" ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Вы не указали URL-адрес сайта!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( substr($promo_url, 0, 7) != "http://" && substr($promo_url, 0, 8) != "https://" ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "URL-адрес сайта указан неверно!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    if( 0 < $cnt_bl && $black_url != false ) 
                    {
                        $row_bl = $sql_bl->fetch_assoc();
                        $sql_bl->free();
                        $result_text = "ERROR";
                        $message_text = "Сайт " . $row_bl["domen"] . " находится в черном списке проекта " . strtoupper($_SERVER["HTTP_HOST"]) . "";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $mysqli->query("INSERT INTO `tb_chat_adv` (`status`,`method_pay`,`user_name`,`description`,`url`,`color`,`time_add`,`money`,`ip`) \r\n\t\t\t\tVALUES('1','" . $method_pay . "','" . $user_name . "','" . escape($promo_desc) . "','" . escape($promo_url) . "','" . escape($promo_color) . "','" . time() . "','0','" . escape($user_lastip) . "')") or die( my_json_encode("ERROR", $mysqli->error) );
                    $promo_id = $mysqli->insert_id;
                    $sql_cng = $mysqli->query("SELECT `id` FROM `tb_chat_adv` WHERE `status`='1'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $cnt_cng = (5 < $sql_cng->num_rows ? intval($sql_cng->num_rows - 5) : 0);
                    if( 0 < $cnt_cng ) 
                    {
                        $mysqli->query("UPDATE `tb_chat_adv` SET `status`='0' WHERE `status`='1' ORDER BY `id` ASC LIMIT " . $cnt_cng) or die( my_json_encode("ERROR", $mysqli->error) );
                    }

                    $sql_cng->free();
                    $data_socket = array( "chat_op" => "promo-load", "last_id" => $promo_id, "time" => time() );
                    $data_socket = json_encode_socket($data_socket);
                    $socketio = @new SocketIO();
                    $sentSocket = @$socketio->send("ssl://" . $_SERVER["HTTP_HOST"], 5000, "Chat Update Ajax", $data_socket);
                    $result_text = "OK";
                    $message_text = "<script>FuncChat(false, \"promo-list\", false, token_promo); \$(\".box-modal-content\").css(\"padding\", \"5px\"); setTimeout(function(){if(\$(\"div\").is(\".box-modal\")) \$.modalpopup(\"close\");}, 1500);</script>";
                    $message_text .= "<span class=\"msg-ok\">Реклама успешно добавлена!</span>";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "mess-arhiv-list" ) 
                {
                    //$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
					//$token_check = strtolower(md5($user_id . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-mess-arhiv-list" . $security_key));
                    /*if( $token_post == false | $token_post != $token_check ) 
                    {
                        $result_text = "ERROR";
                        $message_text = "Не верный токен, обновите страницу!";
                        exit( my_json_encode($result_text, $message_text) );
                    }*/

                    $mysqli->query("DELETE FROM `tb_chat_mess` WHERE `status`='0' AND `time`<'" . (time() - 3 * 24 * 60 * 60) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $mysqli->query("DELETE FROM `tb_chat_mess` WHERE `status`='2' AND `time`<'" . (time() - 3 * 24 * 60 * 60) . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    $sql = $mysqli->query("SELECT * FROM `tb_chat_mess` WHERE `status` IN ('0','2') ORDER BY `id` ASC") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $load_mess = false;
                        while( $row = $sql->fetch_assoc() ) 
                        {
                            $load_mess .= loadmess($row, $user_name, $chat_status);
                        }
                        $sql->free();
                        $result_text = "OK";
                        $message_text = ($load_mess != false ? $load_mess : "Сообщений нет!");
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $sql->free();
                    $result_text = "ERROR";
                    $message_text = "Архивных сообщений нет!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                if( $option == "mess-del" ) 
                {
                    $sql = $mysqli->query("SELECT `id`,`user_name` FROM `tb_chat_mess` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                    if( 0 < $sql->num_rows ) 
                    {
                        $row = $sql->fetch_assoc();
                        $sql->free();
						$security_key = "AChk^D&aw(*TnM#*hglkj7p8UI@if%TSA6N30Kn?HdA";
                        $token_check = strtolower(md5($row["id"] . strtolower($user_name) . $_SERVER["HTTP_HOST"] . "chat-mess-del" . $security_key));
                        /*if( $token_post == false | $token_post != $token_check ) 
                        {
                            $result_text = "ERROR";
                            $message_text = "Не верный токен, обновите страницу!";
                            exit( my_json_encode($result_text, $message_text) );
                        }*/

                        $mysqli->query("DELETE FROM `tb_chat_mess` WHERE `id`='" . $id . "'") or die( my_json_encode("ERROR", $mysqli->error) );
                        $result_text = "OK";
                        $message_text = "Сообщение успешно удалено!";
                        exit( my_json_encode($result_text, $message_text) );
                    }

                    $result_text = "ERROR";
                    $message_text = "Сообщение с ID: " . $id . " не найдено!";
                    exit( my_json_encode($result_text, $message_text) );
                }

                $result_text = "ERROR";
                $message_text = "Option [" . $option . "] not found...";
                exit( my_json_encode($result_text, $message_text) );
            }

        }
        else
        {
            $sql_user->free();
            if( isset($_SESSION) ) 
            {
                session_destroy();
            }

            $result_text = "ERROR-LOGIN";
            $message_text = "<script>setTimeout(function(){location.href = \"/login\";},5000);</script>";
            $message_text .= "Пользователь не идентифицирован!";
            exit( my_json_encode($result_text, $message_text) );
        }

    }
    else
    {
        if( isset($_SESSION) ) 
        {
            session_destroy();
        }

        $result_text = "ERROR";
        $message_text = "<script>setTimeout(function(){location.href = \"/login\";},5000);</script>";
        $message_text .= "Необходимо авторизоваться";
        exit( my_json_encode($result_text, $message_text) );
    }

}
else
{
    $result_text = "ERROR";
    $message_text = "Access denied";
    exit( my_json_encode($result_text, $message_text) );
}
?>