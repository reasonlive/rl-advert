<?PHP
ini_set('session.gc_maxlifetime', 432000);
ini_set('display_errors', 1);
error_reporting(E_ALL);




# Старт сессии
//session_set_cookie_params(60, "/", $_SERVER['HTTP_HOST'], 0,1);
@session_start([
    'cookie_lifetime' => 60*60*24*5,
]);



# Старт буфера
@ob_start();

# Переманная для обозначения заголовка, описания и ключевых слов страниц
$_OPTIMIZATION = array();



# Автоподгрузка классов
spl_autoload_register(function($name){
 include ("classes/_class.".$name.".php");
});

# Конфиг и функции находятся в папке classes

# Класс конфига
//все конфигурационные переменные берем через: $config->переменная
$config = new config;


# Функции
//автоматом ставится IP и Страна в объект $func
$func = new func;
/*echo $func->UserAgent;setcookie("_id", $user_data["id"], (time()+60*60*24*5), '/');
$func->GetBrowser();*/



# База данных
// обращаемся к базе через : $db->query(запрос)
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

# Шапка
@include("_header.php");



?>

<div class='main'>

	

<?php
  

if(!isset($_SESSION['userID'])){
	@include("_aside_left.php");
	$db->query("UPDATE `tb_users` SET online=0 WHERE lastiplog='$func->UserIP' ");
	//var_dump($_COOKIE);
}else{
	@include($_SERVER['DOCUMENT_ROOT'] . "/pages/account/_user_menu.php");
	@include("pages/account/_menu.php");
} 
?>
<div class='main-inner'>
<?php 

		
if(isset($_GET["menu"])){

			
$menu = strval($_GET["menu"]);


switch($menu){


case "account": include("pages/_account.php"); break; // Аккаунт
				
case "404": include("pages/_404.php"); break; // Страница ошибки
				
case "rules": include("pages/_tos.php"); break; // Правила проекта
				
//case "about": include("pages/_about.php"); break; // О проекте
				
case "agreement": include('pages/_person_agreement.php');break;

case "faq": include("pages/_faq.php"); break; // FAQ
				
case "advert": include("pages/_advertise.php"); break; // reklama


case "visits": include("pages/_visits.php"); break; // визиты

case "konkurs": include("pages/_konkurs.php"); break; // Конкурсы

case "stat_pay": include("pages/_stat_pay.php"); break; // выплаты
				
				
case "contacts": include("pages/_contacts.php"); break; // Контакты

				
//case "news": include("pages/_news.php"); break; // Новости*/
				

				
case "signup": include("pages/_signup.php"); break; // Регистрация
				
				    

				
//case "users": include("pages/_users_list.php"); break; // Пользователи
				
//case "payments": include("pages/_payments_list.php"); break; // Выплаты
                
//case "wheel": include("pages/account/_wheel.php"); break; // Выплаты
                
//case "help": include("pages/_help.php"); break; // Помощь
				
				

			
# Страница ошибки
			
default: @include("pages/_404.php"); 
break;

			
}

		
}else @include("pages/_index.php");


?>

</div>

<?php include("_aside_right.php"); ?>



</div>

<?php  
# Подвал
@include("_footer.php");






# Заносим контент в переменную
$content = ob_get_contents();


# Очищаем буфер
ob_end_clean();


$content = str_replace("{!TITLE!}",$_OPTIMIZATION["title"],$content);
	
$content = str_replace('{!DESCRIPTION!}',$_OPTIMIZATION["description"],$content);
	
//$content = str_replace('{!KEYWORDS!}',$_OPTIMIZATION["keywords"],$content);
	
	


// Выводим контент
echo $content;

?>

<script type="text/javascript">
	console.log(document.cookie)
</script>



