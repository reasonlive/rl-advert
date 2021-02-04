<?php  

$charset = 'utf-8'; // Кодировка письма
$to = $mail; // Получатель
$subject = "Регистрация на проекте ".$_SERVER["HTTP_HOST"]; // Тема письма
$text = $name .", Вас приветсвует платформа SERFNETS.RU \r\n"; 
$text .= "Ваш пароль для входа на сервис:". $pass; // Контент письма
$from = $_SERVER['SERVER_ADMIN']; // Отправитель
$fromName = $_SERVER['HTTP_HOST']; // Имя отправителя
// Вот что такое заголовки
$headers = "MIME-Version: 1.0\n";
$headers .= "From: =?$charset?B?".base64_encode($fromName)."?= <$from>\n";
$headers .= "Content-type: text/html; charset=$charset\n";
$headers .= "Content-Transfer-Encoding: base64\n";

$res = mail("=?$charset?B?".base64_encode($to)."?= <$to>", "=?$charset?B?".base64_encode($subject)."?=", chunk_split(base64_encode($text)), $headers, "-f$from");

?>

 <?php  
$subject = "Регистрация на проекте ".$_SERVER["HTTP_HOST"];
				$subject = "=?utf-8?B?".base64_encode($subject)."?=";
				$headers   = array();
				$headers[] = "From: ".strtoupper($_SERVER["HTTP_HOST"])." <".$_SERVER['SERVER_ADMIN'].">";
				$headers[] = "Reply-To: ".strtoupper($_SERVER["HTTP_HOST"])." <support@".$_SERVER["HTTP_HOST"].">";
				$headers[] = "MIME-Version: 1.0";
				$headers[] = "Content-Type: text/html; charset=\"utf-8\"";
				$headers[] = "Content-Transfer-Encoding: base64";
				$headers[] = "X-Priority: 3";
				$headers[] = "X-Mailer: PHP/".phpversion();

				$message   = array();
				$message[] = '<html>';
				$message[] = "<head>";
				$message[] = "<title></title>";
				$message[] = "</head>";
				$message[] = '<body>';
				$message[] = '<table align="center" border="0" cellpadding="10" cellspacing="0" style="width:100%; background-color:#EBF1E7;">';
				$message[] = "<tbody>";
				$message[] = '<tr><td align="center">';
				$message[] = '<table align="center" cellpadding="0" cellspacing="0" style="border:1px solid #DDD; width:100%; background-color:#FFF;">';
				$message[] = "<tbody>";
				$message[] = '<tr><td style="background-color:#009E58; font-size:16px; line-height:16px; text-align:center; text-shadow: 1px 1px 1px #000; padding:15px; color:#FFF; font-weight: normal;">Добро пожаловать на проект <a style="text-decoration:none; color:#FFF;">'.$_SERVER["HTTP_HOST"].'</a></td></tr>';
				$message[] = '<tr><td align="left" style="font-size:12px; font-family:Arial,Helvetica,sans-serif; line-height:20px; padding:20px;">';
				$message[] = '<font color="red" size="3"><u>Ваши регистрационные данные:</u></font><br><br>';
				//$message[] = 'ID: <b>'.$id_user.'</b><br>';
				$message[] = 'Имя: <b>'.$name.'</b><br>';
				$message[] = 'Пароль: <b>'.$pass.'</b><br>';
				$message[] = 'IP адрес: <b>'.$ip.'</b><br><br>';
				$message[] = '<span style="color: #C80000; font-size: 23px;line-height: 29px;">Внимание! Обязательно сохраните данные. Пароль можно изменить в разделе профиль.</span>';
				$message[] = '</td></tr>';
				$message[] = '<tr><td align="left" style="border-top:1px solid #DDD; font-size:12px; padding:10px 20px;">С уважением, автоматическая служба уведомлений <a href="http://'.$_SERVER["HTTP_HOST"].'" style="color:#009E58;">'.$_SERVER["HTTP_HOST"].'</a></td></tr>';
				$message[] = "</tbody>";
				$message[] = "</table>";
				$message[] = "</td></tr>";
				$message[] = "</tbody>";
				$message[] = "</table>";
				$message[] = "</body>";
				$message[] = "</html>";
				$res = mail($mail, $subject, base64_encode(implode("\r\n", $message)), implode("\r\n", $headers));
				?> 