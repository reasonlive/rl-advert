<div class='menu-btn' style='color:white;text-align: center;margin-top:-40px'><span>МЕНЮ</span>
</div>
	<br>
	<br>
	<br>
<div class='user-menu-ex' hidden>
	<p><a href="">Серфинг</a></p>
	<p><a href="">Youtube</a></p>
	<p><a href="">Автосерфинг</a></p>
	<p><a href="">Письма, чтение писем</a></p>
	<p><a href="">Прохождение тестов</a></p>
	<p><a href="">Задания</a></p>
	<p><a href="">Управление заданиями</a></p>
	<p><a href="">Бонус 24 часа</a></p>
	<p><a href="">Новости</a></p>
	<p><a href="">Рассылка пользователям</a></p>
	<p><a href="">Форум</a></p>
	
	<p><a href="">VKfast </a></p>
	<p><a href="">Freetraf</a></p>
	<p><a href="">Freetraf 2</a></p>
	<p><a href="">FREE SERF +</a></p>
	<p></p>
</div>

<script type="text/javascript">
	const menuBtn = document.querySelector(".menu-btn span");
	const menu = document.querySelector('.user-menu-ex');
	function toggle(){
		if(menu.hidden)menu.hidden = false;
		else menu.hidden = true;
	}
	menuBtn.onclick = ()=> toggle();
	
</script>