<?PHP

$_OPTIMIZATION['title'] = 'Аккаунт';


# Блокировка сессии
if(!isset($_SESSION['userID'])){
	@session_destroy();
 	Header("Location: /"); return; 
}



if(isset($_GET["title"])){
		
	$smenu = strval($_GET["title"]);
			
	switch($smenu){
		case "404": include("pages/_404.php"); break; // Страница ошибки
		
		case "profile": include("pages/account/_profile.php"); break; // профиль пользователя

		case "1serfnet": include("pages/account/_1serfnet.php"); break; // описание рекламы

		case "money": include("pages/account/_money_op.php"); break; // операции со счетом
		
				
	# Страница ошибки
	default: @include("pages/_404.php"); break;
			
	}
			
}else @include("pages/account/_profile.php");

?>

<script type="text/javascript">
	//console.log(document.cookie)
</script>