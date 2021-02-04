<?php
if (!DEFINED("ADMINKA")) {die ("Hacking attempt!");}

$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='0'") or die($mysqli->error);
$count_articles[0] = $sql_articles->num_rows;

$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='1'") or die($mysqli->error);
$count_articles[1] = $sql_articles->num_rows;

$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='2'") or die($mysqli->error);
$count_articles[2] = $sql_articles->num_rows;

$sql_articles = $mysqli->query("SELECT `id` FROM `tb_ads_articles` WHERE `status`='4'") or die($mysqli->error);
$count_articles[4] = $sql_articles->num_rows;

?>

 <script>
$(document).ready(function(){
	setInterval(function(){
		if($(".text_blink").css("color") == "rgb(255, 0, 0)") {
			$(".text_blink").css("color", "#000000");
		}else{
			$(".text_blink").css("color", "#FF0000");
		}
	}, 1000);
});
</script> 

<ul id="nav">

	<li><b>Настройки сайта</b>
	<ul>
		<li><a href="index.php?op=site_config">Настройка сайта</a></li>
		<li><a href="index.php?op=site_history">История операций</a></li>
		<li><a href="index.php?op=2">Партнерская программа</a></li>
		<li><a href="index.php?op=3">Статистика сайта</a></li>
		<li><a href="index.php?op=stat_advertise">Статистика заказов</a></li>
		<li><a href="index.php?op=4">Черный список сайтов</a></li>
		<li><a href="index.php?op=5">Черный список WMID</a></li>
		<li><a href="index.php?op=black_ip_reg">ЧС IP адресов запрещенных для рег-ии</a></li>
		<li><a href="index.php?op=6">Настройки рейтинга</a></li>
		<li><a href="index.php?op=7">Настройка статусов</a></li>
                <li><a href="index.php?op=board_config">Доска почета</a></li>
                <li><a href="index.php?op=config_bonus">Бонусы</a></li>
                <li><a href="index.php?op=sahans_config">Бонус Удачи</a></li>
                <li><a href="index.php?op=knb_config">Игра КНБ</a></li>
				<li><a href="index.php?op=config_money_bonus">Бонус при пополнении</a></li>
                <li><a href="index.php?op=mail_vp_config">Очистка почты</a></li>
                <li><a href="index.php?op=config_cabinet">Кабинет рекламодателя</a></li>
                <li><a href="index.php?op=config_kopilka">Копилка проекта</a></li>
                <li><a href="index.php?op=config_redemption">Настройки выкупа у реферера</a></li>
                <li><a href="index.php?op=config_free_users">Свободные пользователи</a></li>
                <li><a href="index.php?op=config_ref_wall">Настройка Реф-Стены</a></li>
	</ul>
	</li>
	
	<li><b>ЧАТ</b>
	<ul>
		<li><a href="index.php?op=chat_config">Настройки</a></li>
		<li><a href="index.php?op=chat_adv_list">Реклама в ЧАТе</a></li>
		<li><a href="index.php?op=chat_moder">Модераторы ЧАТа</a></li>
		<li><a href="index.php?op=chat_ban_users">Заблокированные пользователи</a></li>
		<li><a href="index.php?op=chat_mess_arhiv">Архив сообщений</a></li>
	</ul>
	</li>
	
	<li><b>Рассылка сообщений</b>
	<ul>
		<li><a href="index.php?op=invest_delivery">Добавить рассылку</a></li>
	</ul>
	</li>
	
	<li><b>Платежные системы</b>
	<ul>
		<li><a href="index.php?op=config_pay">Настройки платежей</a></li>
		</ul>
	</li>
	
	<li><b>Выплаты</b>
	<ul>
	    <li><a href="index.php?op=pay_config">Настройки выплат</a></li>
		<!--<li><a href="index.php?op=12">Настройки выплат</a></li>-->
		<li><a href="index.php?op=13">Ручные выплаты</a></li>
		<li><a href="index.php?op=14">Авто-выплаты</a></li>
	</ul>
	</li>

	<li><b>Акции</b>
	<ul>
		<li><a href="index.php?op=site_action_config">Настройки акции</a></li>
		<li><a href="index.php?op=site_action_ref">Список рефералов для акции</a></li>
	</ul>
	</li>

	<li><b>Новости</b>
	<ul>
		<li><a href="index.php?op=news_add">Добавить новость</a></li>
		<li><a href="index.php?op=news_edit">Редактировать новости</a></li>
	</ul>
	</li>

	<li><b>Уведомления</b>
	<ul>
		<li><a href="index.php?op=notification_add">Добавить уведомление</a></li>
		<li><a href="index.php?op=notification_edit">Редактировать уведомления</a></li>
	</ul>
	</li>

	<li><b>Подсказки</b>
	<ul>
		<li><a href="index.php?op=hint_tips_add">Добавить подсказку</a></li>
		<li><a href="index.php?op=hint_tips_edit">Редактирование подсказок</a></li>
	</ul>
	</li>

	<li><b>Инвестиционный проект</b>
	<ul>
		<li><a href="index.php?op=invest_config">Настройки</a></li>
		<li><a href="index.php?op=invest_users">Список инвесторов</a></li>
		<li><a href="index.php?op=invest_money_in">История пополнений баланса</a></li>
		<li><a href="index.php?op=config_money">Бонус за пополнение баланса</a></li>
		<li><a href="index.php?op=invest_buy_history">История покупок акций</a></li>
		<li><a href="index.php?op=invest_birja">Биржа акций [модерация]</a></li>
		<li><a href="index.php?op=invest_birja_history">Биржа акций [история]</a></li>
		<li><a href="index.php?op=invest_news_add">Добавить новость</a></li>
		<li><a href="index.php?op=invest_news_edit">Редактировать новость</a></li>
		<li><a href="index.php?op=invest_delivery">Рассылка сообщений инвесторам</a></li>
		<li><a href="index.php?op=invest_welcome">Приветствие новым инвесторам</a></li>
	</ul>
	</li>

	<li><b>Пользователи</b>
	<ul>
		<li><a href="index.php?op=users_edit">Редактировать пользователя</a></li>
		<li><a href="index.php?op=users_ban_add">Забанить пользователя</a></li>
		<li><a href="index.php?op=users_ban_list">Забаненные пользователи</a></li>
		<li><a href="index.php?op=users_online">Пользователи онлайн</a></li>
	</ul>
	</li>
	
	<li><b>Оплачиваемые посещения</b>
	<ul>
		<li><a href="index.php?op=pay_visits_config">Настройки</a></li>
		<li><a href="index.php?op=pay_visits_adreq">Неоплаченные заказы</a></li>
		<li><a href="index.php?op=pay_visits_add">Добавить ссылку</a></li>
		<li><a href="index.php?op=pay_visits_views">Просмотр и редактирование</a></li>
	</ul>
	</li>

	<li><b>Платная строка</b>
	<ul>
		<li><a href="index.php?op=pay_row_config">Настройки</a></li>
		<li><a href="index.php?op=pay_row_add">Добавить рекламу</a></li>
		<li><a href="index.php?op=pay_row_adreq">Неоплаченные заказы</a></li>
		<li><a href="index.php?op=pay_row_edit">Редактирование рекламы</a></li>
		<li><a href="index.php?op=pay_row_arhiv">Архив ссылок</a></li>
	</ul>
	</li>
	
	<li><b>Быстрые сообщения</b>
	<ul>
		<li><a href="index.php?op=quick_mess_config">Настройки</a></li>
		<li><a href="index.php?op=quick_mess_add">Добавить сообщение</a></li>
		<li><a href="index.php?op=quick_mess_edit">Просмотр и редактирование</a></li>
	</ul>
	</li>

	<li><b>Оплачиваемые тесты</b>
	<ul>
		<li><a href="index.php?op=tests_config">Настройки тестов</a></li>
		<li><a href="index.php?op=tests_adreq">Неоплаченные заказы тестов</a></li>
		<li><a href="index.php?op=tests_add">Добавить тест</a></li>
		<li><a href="index.php?op=tests_edit">Редактирование тестов</a></li>
		<li><a href="index.php?op=tests_claims">Жалобы на тесты</a></li>
	</ul>
	</li>
	
	<li><b>Рассылка новостей на e-mail</b>
	<ul>
		<li><a href="index.php?op=407">Настройка рекламы</a></li>
		<li><a href="index.php?op=408">Заказы рекламы</a></li>
		<li><a href="index.php?op=409">Добавить рекламу</a></li>
		<li><a href="index.php?op=500">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Рассылка на e-mail</b>
	<ul>
		<li><a href="index.php?op=sent_emails_config">Настройки рассылок</a></li>
		<li><a href="index.php?op=sent_emails_adreq">Неоплаченные заказы рассылок</a></li>
		<li><a href="index.php?op=sent_emails_add">Добавить рассылку</a></li>
		<li><a href="index.php?op=sent_emails_edit">Редактирование рассылок</a></li>
	</ul>
	</li>

	<li><b>Каталог статей <?php if($count_articles[2] > 0) echo '<span id="li_art_moder" class="text_blink" style="float:right; display:block; padding-right:55px; color:#FF0000; font-size:14px; font-weight:bold; text-shadow:1px 2px 3px #FFF;">'.$count_articles[2].'</span>';?></b>
	<ul>
		<li><a href="index.php?op=articles_config">Настройки</a></li>
		<li><a href="index.php?op=articles_add">Добавить статью</a></li>
		<li><a href="index.php?op=articles_adreq">Неоплаченные заказы <span style="float:right; display:block; padding-right:12px; font-size:14px;">[<span id="art_req" style="color:blue; padding:0 1px; font-size:12px; font-weight:bold;"><?=$count_articles[0];?></span>]</span></a></li>
		<li><a href="index.php?op=articles_moder">Статьи на модерации <span style="float:right; display:block; padding-right:12px; font-size:14px;">[<span id="art_moder" style="color:red; padding:0 1px; font-size:12px; font-weight:bold;"><?=$count_articles[2];?></span>]</span></a></li>
		<li><a href="index.php?op=articles_ban">Заблокированные статьи <span style="float:right; display:block; padding-right:12px; font-size:14px;">[<span id="art_ban" style="color:black; padding:0 1px; font-size:12px; font-weight:bold;"><?=$count_articles[4];?></span>]</span></a></li>
		<li><a href="index.php?op=articles_edit">Редактирование статей <span style="float:right; display:block; padding-right:12px; font-size:14px;">[<span id="art_edit" style="color:green; padding:0 1px; font-size:12px; font-weight:bold;"><?=$count_articles[1];?></span>]</span></a></li>
	</ul>
	</li>
	
	<li><b>Индексируемые ссылки</b>
	<ul>
		<li><a href="index.php?op=403">Настройка рекламы</a></li>
		<li><a href="index.php?op=404">Заказы рекламы</a></li>
		<li><a href="index.php?op=405">Добавить рекламу</a></li>
		<li><a href="index.php?op=406">Просмотр рекламы</a></li>
	</ul>

	<li><b>Динамические ссылки</b>
	<ul>
		<li><a href="index.php?op=dlinks_config">Настройки</a></li>
		<li><a href="index.php?op=dlinks_adreq">Заказ рекламы</a></li>
		<li><a href="index.php?op=dlinks_add">Добавить ссылку</a></li>
		<li><a href="index.php?op=dlinks_edit">Редактирование ссылок</a></li>
		<li><a href="index.php?op=dlinks_claims">Жалобы</a></li>
		<li><a href="index.php?op=antiautocliker">Анти авто-кликер</a></li>
	</ul>
	</li>
	
	<li><b>YouTube ссылки</b>
	<ul>
		<li><a href="index.php?op=youtube_config">Настройки</a></li>
		<li><a href="index.php?op=youtube_adreq">Заказ рекламы</a></li>
		<li><a href="index.php?op=youtube_add">Добавить youtube</a></li>
		<li><a href="index.php?op=youtube_edit">Редактирование ссылок</a></li>
		<li><a href="index.php?op=youtube_claims">Жалобы</a></li>
		<li><a href="index.php?op=antiautoclik">Анти авто-кликер</a></li>
	</ul>
	</li>

	<li><b>Псевдо-динам. ссылки</b>
	<ul>
		<li><a href="index.php?op=24">Настройки</a></li>
		<li><a href="index.php?op=25">Заказ Рекламы</a></li>
		<li><a href="index.php?op=26">Добавить ссылку</a></li>
		<li><a href="index.php?op=27">Просмотр ссылок</a></li>
	</ul>
	</li>
	
	<li><b>Авто-серфинг <span style="color: #3F3F3F;">You</span><span style="border-radius: 5px; background-color: #E62117; padding: 2px; color: #FFFFFF;">Tube</span></b>
	<ul>
		<li><a href="index.php?op=config_autoyou">Настройка рекламы</a></li>
		<li><a href="index.php?op=adreq_autoyou">Заказы рекламы</a></li>
		<li><a href="index.php?op=add_autoyou">Добавить рекламу</a></li>
		<li><a href="index.php?op=edit_autoyou">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Авто-серфинг</b>
	<ul>
		<li><a href="index.php?op=32">Настройка рекламы</a></li>
		<li><a href="index.php?op=33">Заказы рекламы</a></li>
		<li><a href="index.php?op=34">Добавить рекламу</a></li>
		<li><a href="index.php?op=35">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Статические ссылки</b>
	<ul>
		<li><a href="index.php?op=36">Настройка рекламы</a></li>
		<li><a href="index.php?op=37">Заказы рекламы</a></li>
		<li><a href="index.php?op=38">Добавить рекламу</a></li>
		<li><a href="index.php?op=39">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Бегущая строка</b>
	<ul>
		<li><a href="index.php?op=beg_stroka_config">Настройка рекламы</a></li>
		<li><a href="index.php?op=beg_stroka_adreq">Заказы рекламы</a></li>
		<li><a href="index.php?op=beg_stroka_add">Добавить рекламу</a></li>
		<li><a href="index.php?op=beg_stroka_edit">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Индексируемые ссылки</b>
	<ul>
		<li><a href="index.php?op=403">Настройка рекламы</a></li>
		<li><a href="index.php?op=404">Заказы рекламы</a></li>
		<li><a href="index.php?op=405">Добавить рекламу</a></li>
		<li><a href="index.php?op=406">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Контекстная реклама</b>
	<ul>
		<li><a href="index.php?op=40">Настройка рекламы</a></li>
		<li><a href="index.php?op=41">Заказы рекламы</a></li>
		<li><a href="index.php?op=42">Добавить рекламу</a></li>
		<li><a href="index.php?op=43">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Статические баннеры</b>
	<ul>
		<li><a href="index.php?op=44">Настройка рекламы</a></li>
		<li><a href="index.php?op=45">Заказы рекламы</a></li>
		<li><a href="index.php?op=46">Добавить рекламу</a></li>
		<li><a href="index.php?op=47">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Текстовые объявления</b>
	<ul>
		<li><a href="index.php?op=48">Настройка рекламы</a></li>
		<li><a href="index.php?op=49">Заказы рекламы</a></li>
		<li><a href="index.php?op=50">Добавить рекламу</a></li>
		<li><a href="index.php?op=51">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Ссылки во фрейме</b>
	<ul>
		<li><a href="index.php?op=52">Настройка рекламы</a></li>
		<li><a href="index.php?op=53">Заказы рекламы</a></li>
		<li><a href="index.php?op=54">Добавить рекламу</a></li>
		<li><a href="index.php?op=55">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Рекламные письма</b>
	<ul>
		<li><a href="index.php?op=56">Настройка рекламы</a></li>
		<li><a href="index.php?op=57">Заказы рекламы</a></li>
		<li><a href="index.php?op=58">Добавить рекламу</a></li>
		<li><a href="index.php?op=59">Просмотр рекламы</a></li>
		<li><a href="index.php?op=mails_claims">Жалобы</a></li>
	</ul>
	</li>

	<li><b>Рекламная цепочка</b>
	<ul>
		<li><a href="index.php?op=60">Настройка рекламы</a></li>
		<li><a href="index.php?op=61">Заказы рекламы</a></li>
		<li><a href="index.php?op=62">Добавить рекламу</a></li>
		<li><a href="index.php?op=63">Просмотр рекламы</a></li>
	</ul>
	</li>

	<li><b>Пакеты рекламы</b>
	<ul>
		<li><a href="index.php?op=100">Настройка пакетов</a></li>
		<li><a href="index.php?op=101">Заказы пакетов</a></li>
	</ul>
	</li>
	
		<li><b>Задания NEW</b>
	<ul>
		<li><a href="index.php?op=200">Настройки</a></li>
		<li><a href="index.php?op=201&page=task_view">Управление своими заданиями</a></li>
		<li><a href="index.php?op=202">Просмотр всех заданий</a></li>
		<li><a href="index.php?op=task_stat">Стика выполнений</a></li>
		<li><a href="index.php?op=task_claims">Жалобы</a></li>
		<li><a href="index.php?op=task_claims_us">Жалобы на исполнителей</a></li>
	</ul>
	</li>

	<li><b>Задания</b>
	<ul>
		<li><a href="index.php?op=taskcfg">Настройка заданий</a></li>
		<li><a href="index.php?op=taskview">Просмотр заданий</a></li>
		<li><a href="index.php?op=taskstats">Выполнение заданий</a></li>
		<li><a href="index.php?op=taskadd">Добавить задание</a></li>
	</ul>
	</li>

	<li><b>Аукцион</b>
	<ul>
		<li><a href="index.php?op=203">Настройки</a></li>
		<li><a href="index.php?op=204">Статистика</a></li>
		<li><a href="index.php?op=205">Активные аукционы</a></li>
		<li><a href="index.php?op=206">Завершенные аукционы</a></li>
	</ul>
	</li>

	<li><b>Форум</b>
	<ul>
		<li><a href="index.php?op=207">Разделы форума</a></li>
		<li><a href="index.php?op=208">Подразделы форума</a></li>
		<li><a href="index.php?op=209">Настройки статусов</a></li>
		<li><a href="index.php?op=210">Пользователи</a></li>
	</ul>
	</li>
	
	<li><b>Настройки Конкурсов</b>
	<ul>
		<li><a href="index.php?op=konkurs_title">Заголовки к конкурсам</a></li>
		<li><a href="index.php?op=free_users">Пользователи без реферера</a></li>
		<li><a href="index.php?op=konkurs_users_exp">Пользователи исключенные из конкурсов</a></li>
		<li><a href="index.php?op=konkurs_config_ads">Конкурс рекламодателей №1</a></li>
		<li><a href="index.php?op=konkurs_config_ads_big">Конкурс рекламодателей №2</a></li>
		<li><a href="index.php?op=konkurs_config_serf">Размещению ссылок в серфинге</a></li>
		<li><a href="index.php?op=konkurs_config_ads_task">Конкурс по оплате заданий</a></li>
		<li><a href="index.php?op=konkurs_config_test">Конкурс по прохождению тестов</a></li>
		<li><a href="index.php?op=konkurs_config_click">Конкурс кликеров</a></li>
		<li><a href="index.php?op=konkurs_config_youtub">Конкурс YOUTUB</a></li>
		<li><a href="index.php?op=konkurs_config_ref">Конкурс привлечение рефералов</a></li>
		<li><a href="index.php?op=konkurs_config_task">Конкурс по выполнению заданий</a></li>
		<li><a href="index.php?op=konkurs_config_ed_hit">Ежедневный конкурс посетителей</a></li>
		<li><a href="index.php?op=konkurs_config_complex">Комплексный конкурс</a></li>
		<li><a href="index.php?op=konkurs_config_clic_ref">Лучший реферер</a></li>
		<li><a href="index.php?op=konkurs_config_best_ref">Лучший реферал</a></li>
	</ul>
	</li>
	
	<li><b>Биржа рефералов</b>
	<ul>
		<li><a href="index.php?op=213">Настройки</a></li>
		<li><a href="index.php?op=214">Модерация</a></li>
		<li><a href="index.php?op=215">Статистика</a></li>

	</ul>
	</li>

</ul>