<?php
@session_start();




# Старт буфера
@ob_start();




# Автоподгрузка классов
spl_autoload_register(function($name){
  include($_SERVER['DOCUMENT_ROOT']. "/classes/_class.".$name.".php");
});


# Класс конфига
$config = new config;


# Функции
$func = new func;

# База данных
if(!isset($mysqli)) $mysqli = new mysqli($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);
//$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);


if(!DEFINED("ADMINKA")) DEFINE("ADMINKA", true);
if(!DEFINED("ADVERTISE")) DEFINE("ADVERTISE", true);
if(!DEFINED("DOC_ROOT")) DEFINE("DOC_ROOT", $_SERVER["DOCUMENT_ROOT"]);
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
?>



<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Админка</title>
	
	<link rel="stylesheet" type="text/css" href="css/tablecloth/tablecloth.css?v=1.03" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/estilo.css?v=1.07" />
	<link rel="stylesheet" type="text/css" href="css/sdmenu/sdmenu.css?v=1.04" />
	<link rel="stylesheet" type="text/css" href="css/modalpopup.css?v=1.05" />
	<script src="/js/jquery/jquery-3.3.1.min.js"></script>
	<script src="/js/socket.io/socket.io-2.1.1.js"></script>
	<script src="/js/js_modalpopup-0.3.min.js"></script>
	<script src="/js/jquery.simpletip-1.3.1.pack.js"></script>
	<script src="js/js_adminka.js?v=1.05"></script>
	<script src="js/js_advs.js?v=1.05"></script>
	<script src="js/js_main.js?v=1.05"></script>

<body>
	<?php
	include("start.php");
	require_once(ROOT_DIR."/pay/method_pay/method_pay_sys.php");
	?>

	<div id="framecontent1"><div id="framecontent"><div class="innertube"><?php include('menu.php');?></div></div></div>
	<div id="maincontent"><div class="innertube">
		<div id="loading" style="display:none;"></div>
		<div id="LoadModal" style="display:none;"></div>

		<?php
	
		$op = (isset($_GET["op"])) ? trim($_GET["op"]) : false;
		

		if($op==false) {
			echo '<script type="text/javascript">location.replace("'.$_SERVER["PHP_SELF"].'?op=site_config");</script>';
			echo '<noscript><META HTTP-EQUIV="REFRESH" CONTENT="0;URL='.$_SERVER["PHP_SELF"].'?op=site_config"></noscript>';
			exit();
		}

		switch($op) {
			case("config_bonus"): include('config_site/config_bonus.php'); break;
			case("stat_advertise"): include('config_site/stat_pay.php'); break;
			case("config_cabinet"): include('config_site/config_cabinet.php'); break;
			case("config_kopilka"): include('config_site/config_kopilka.php'); break;
			case("site_config"): include('config_site/config_site.php'); break;
			case("yandex"):  include('yandex/yandex.php'); break;
			case("config_pay"): include('pay/config_pay_pay.php'); break;
			case("sahans_config"): include('config_site/sahans_config.php'); break;
			case("mail_vp_config"): include('config_site/mail_vp_config.php'); break;
			case("config_money_bonus"): include('config_site/config_money_bonus.php'); break;
			case("knb_config"): include('config_site/knb_config.php'); break;
			case("invest_delivery"):	include("invest_admin/invest_delivery/invest_delivery.php"); break;

			case(2): include('partner/config_partner.php'); break;
			case(3): include('config_site/site_stats.php'); break;
			case(4): include('black_list/add_bl_sites.php'); break;
			case(5): include('black_list/add_bl_wmid.php'); break;
			case(6): include("status/config_reiting.php"); break;
			case(7): include('status/config_status.php'); break;

			case(10): include('auto_ref/config_autoref.php'); break;
			case(11): include('auto_ref/list_autoref.php'); break;
			case(13): include('pay/pay_r.php'); break;
			case(14): include('pay/pay_a.php'); break;

			case("users_edit"): 	include("users/users_edit.php"); break;
			case("users_ban_add"): 	include("users/users_ban_add.php"); break;
			case("users_ban_list"): include("users/users_ban_list.php"); break;
			case("users_online"): 	include("users/users_online.php"); break;

			case(24): include('advertise/psevdo/config_psevdo.php'); break;
			case(25): include('advertise/psevdo/adreq_psevdo.php'); break;
			case(26): include('advertise/psevdo/add_psevdo.php'); break;
			case(27): include('advertise/psevdo/edit_psevdo.php'); break;

			case(32): include('advertise/auto_serf/config_auto_serf.php'); break;
			case(33): include('advertise/auto_serf/adreq_auto_serf.php'); break;
			case(34): include('advertise/auto_serf/add_auto_serf.php'); break;
			case(35): include('advertise/auto_serf/edit_auto_serf.php'); break;
			
			case("config_autoyou"): include('advertise/auto_serf_you/config_autoyou_serf.php'); break;
			case("adreq_autoyou"): include('advertise/auto_serf_you/adreq_autoyou_serf.php'); break;
			case("add_autoyou"): include('advertise/auto_serf_you/add_autoyou_serf.php'); break;
			case("edit_autoyou"): include('advertise/auto_serf_you/edit_autoyou_serf.php'); break;

			case(36): include('advertise/stat_link/config_slink.php'); break;
			case(37): include('advertise/stat_link/adreq_slink.php'); break;
			case(38): include('advertise/stat_link/add_slink.php'); break;
			case(39): include('advertise/stat_link/edit_slink.php'); break;

			case(40): include('advertise/kontext/config_kontext.php'); break;
			case(41): include('advertise/kontext/adreq_kontext.php'); break;
			case(42): include('advertise/kontext/add_kontext.php'); break;
			case(43): include('advertise/kontext/edit_kontext.php'); break;

			case(44): include('advertise/banners/config_banners.php'); break;
			case(45): include('advertise/banners/adreq_banners.php'); break;
			case(46): include('advertise/banners/add_banners.php'); break;
			case(47): include('advertise/banners/edit_banners.php'); break;

			case(48): include('advertise/txt_ob/config_txt_ob.php'); break;
			case(49): include('advertise/txt_ob/adreq_txt_ob.php'); break;
			case(50): include('advertise/txt_ob/add_txt_ob.php'); break;
			case(51): include('advertise/txt_ob/edit_txt_ob.php'); break;

			case(52): include('advertise/frm_links/config_frm_links.php'); break;
			case(53): include('advertise/frm_links/adreq_frm_links.php'); break;
			case(54): include('advertise/frm_links/add_frm_links.php'); break;
			case(55): include('advertise/frm_links/edit_frm_links.php'); break;

			case(56): include("advertise/mails/config_mails.php"); break;
			case(57): include("advertise/mails/adreq_mails.php"); break;
			case(58): include("advertise/mails/add_mails.php"); break;
			case(59): include("advertise/mails/edit_mails.php"); break;

			case(60): include("advertise/rek_cep/config_rek_cep.php"); break;
			case(61): include("advertise/rek_cep/adreq_rek_cep.php"); break;
			case(62): include("advertise/rek_cep/add_rek_cep.php"); break;
			case(63): include("advertise/rek_cep/edit_rek_cep.php"); break;

			case(100): include("advertise/packet_advertise/config_packet.php"); break;
			case(101): include("advertise/packet_advertise/adreq_packet.php"); break;
			
			case(200): include('advertise/task/config_task.php'); break;
			case(201): include('advertise/task/index_my_task.php'); break;
			case(202): include('advertise/task/users_task/task.php'); break;
			case("tack_blec"): 	include('advertise/task/tack_blec.php'); break;
			case("task_claims"): 	include("advertise/task/task_claims.php"); break;
			
			case("pay_visits_config"): 	include("advertise/pay_visits/pay_visits_config.php"); break;
			case("pay_visits_adreq"): 	include("advertise/pay_visits/pay_visits_adreq.php"); break;
			case("pay_visits_add"): 	include("advertise/pay_visits/pay_visits_add.php"); break;
			case("pay_visits_views"): 	include("advertise/pay_visits/pay_visits_views.php"); break;

			case(203): include('auction/config_auction.php'); break;
			case(204): include('auction/auction_stat.php'); break;
			case(205): include('auction/auction_active.php'); break;
			case(206): include('auction/auction_end.php'); break;

			case(207): include('forum/edit_razdel.php'); break;
			case(208): include('forum/edit_prazdel.php'); break;
			case(209): include('forum/config_status.php'); break;
			case(210): include('forum/users_status.php'); break;

			case(213): include('ref_birj/config_ref_birj.php'); break;
			case(214): include('ref_birj/edit_ref_birj.php'); break;
			case(215): include('ref_birj/stat_ref_birj.php'); break;
			
			case("taskcfg"): 	include("advertise/tasknew/taskcfg.php"); break;
			case("taskview"): 	include("advertise/tasknew/taskview.php"); break;
			case("taskstats"): 	include("advertise/tasknew/taskstats.php"); break;
			case("taskadd"): 	include("advertise/tasknew/taskadd.php"); break;
			
			case(403): include('advertise/stat_kat/config_slink.php'); break;
			case(404): include('advertise/stat_kat/adreq_slink.php'); break;
			case(405): include('advertise/stat_kat/add_slink.php'); break;
			case(406): include('advertise/stat_kat/edit_slink.php'); break;
			case(407): include("advertise/sent_emails_uzer/config_sent_emails_uzer.php"); break;
			case(408): include("advertise/sent_emails_uzer/adreq_sent_emails_uzer.php"); break;
			case(409): include("advertise/sent_emails_uzer/add_sent_emails_uzer.php"); break;
			case(500): include("advertise/sent_emails_uzer/edit_sent_emails_uzer.php"); break;
			
			case("konkurs_config_ads"): include("konkurs_new/konkurs_config_ads.php"); break;
			case("konkurs_config_ads_big"): include("konkurs_new/konkurs_config_ads_big.php"); break;
			case("konkurs_config_ref"): include("konkurs_new/konkurs_config_ref.php"); break;
			case("konkurs_config_click"): include("konkurs_new/konkurs_config_click.php"); break;
			case("konkurs_config_youtub"): include("konkurs_new/konkurs_config_youtub.php"); break;
			case("konkurs_config_task"): include("konkurs_new/konkurs_config_task.php"); break;
			case("konkurs_config_test"): include("konkurs_new/konkurs_config_test.php"); break;
			case("konkurs_title"): include("konkurs_new/konkurs_title.php"); break;
			case("konkurs_config_ed_hit"): include("konkurs_new/konkurs_config_ed_hit.php"); break;
			case("free_users"): include("konkurs_new/free_users.php"); break;
			case("konkurs_config_complex"): include("konkurs_new/konkurs_config_complex.php"); break;
			case("konkurs_users_exp"): include("konkurs_new/konkurs_users_exp.php"); break;
			case("konkurs_config_serf"): include("konkurs_new/konkurs_config_serf.php"); break;
			case("konkurs_config_ads_task"): include("konkurs_new/konkurs_config_ads_task.php"); break;
			case("konkurs_config_clic_ref"): include("konkurs_new/konkurs_config_clic_ref.php"); break;
			case("konkurs_config_best_ref"): include("konkurs_new/konkurs_config_best_ref.php"); break;
			
			case("beg_stroka_config"): 	include('advertise/beg_stroka/beg_stroka_config.php'); break;
			case("beg_stroka_adreq"): 	include('advertise/beg_stroka/beg_stroka_adreq.php'); break;
			case("beg_stroka_add"): 	include('advertise/beg_stroka/beg_stroka_add.php'); break;
			case("beg_stroka_edit"): 	include('advertise/beg_stroka/beg_stroka_edit.php'); break;

			case("dlinks_config"): 	include("advertise/dlinks/dlinks_config.php"); break;
			case("dlinks_adreq"): 	include("advertise/dlinks/dlinks_adreq.php"); break;
			case("dlinks_add"): 	include("advertise/dlinks/dlinks_add.php"); break;
			case("dlinks_edit"): 	include("advertise/dlinks/dlinks_edit.php"); break;
			case("dlinks_claims"): 	include("advertise/dlinks/dlinks_claims.php"); break;
			case("antiautocliker"): include("advertise/dlinks/dlinks_antiautocliker.php"); break;

			case("catalog_config"): include("advertise/catalog/catalog_config.php"); break;
			case("catalog_adreq"): 	include("advertise/catalog/catalog_adreq.php"); break;
			case("catalog_add"): 	include("advertise/catalog/catalog_add.php"); break;
			case("catalog_edit"): 	include("advertise/catalog/catalog_edit.php"); break;

			case("tests_config"): 	include("advertise/tests/tests_config.php"); break;
			case("tests_adreq"): 	include("advertise/tests/tests_adreq.php"); break;
			case("tests_add"): 	include("advertise/tests/tests_add.php"); break;
			case("tests_edit"): 	include("advertise/tests/tests_edit.php"); break;
			case("tests_claims"): 	include("advertise/tests/tests_claims.php"); break;

			case("config_redemption"): 	include("config_site/config_null_referer.php"); break;
			case("config_free_users"): 	include("config_site/config_free_users.php"); break;

			case("notification_add"): 	include("notification/notification_add.php"); break;
			case("notification_edit"): 	include("notification/notification_edit.php"); break;

			case("invest_config"): 		include('invest/invest_config.php'); break;
			case("invest_users"): 		include('invest/invest_users.php'); break;
			case("invest_buy_history"): 	include('invest/invest_buy_history.php'); break;
			case("invest_birja"): 		include('invest/invest_birja.php'); break;
			case("invest_birja_history"):	include('invest/invest_birja_history.php'); break;
			case("invest_news_add"):	include('invest/invest_news/invest_news_add.php'); break;
			case("invest_news_edit"):	include('invest/invest_news/invest_news_edit.php'); break;
			case("invest_delivery"):	include('invest/invest_delivery/invest_delivery.php'); break;
			case("invest_welcome"):		include('invest/invest_welcome/invest_welcome.php'); break;
			case("invest_money_in"): 	include('invest/invest_money_in.php'); break;
			case("config_money"): 	include('invest/config_money_bonus.php'); break;

			case("news_add"): 		include("news/news_add.php"); break;
			case("news_edit"): 		include("news/news_edit.php"); break;

			case("hint_tips_add"): 		include("hint_tips/hint_tips_add.php"); break;
			case("hint_tips_edit"): 	include("hint_tips/hint_tips_edit.php"); break;

			case("board_config"): 		include("config_site/board_config.php"); break;
			case("site_history"): 		include("config_site/site_history.php"); break;

			case("pay_config"): 		include("pay/pay_config.php"); break;
			case("pay_auto"): 		include("pay/pay_auto.php"); break;
			case("pay_hand"): 		include("pay/pay_hand.php"); break;

			case("pay_row_config"): 	include("advertise/pay_row/pay_row_config.php"); break;
			case("pay_row_add"): 		include("advertise/pay_row/pay_row_add.php"); break;
			case("pay_row_adreq"): 		include("advertise/pay_row/pay_row_adreq.php"); break;
			case("pay_row_edit"): 		include("advertise/pay_row/pay_row_edit.php"); break;
			case("pay_row_arhiv"):	 	include("advertise/pay_row/pay_row_arhiv.php"); break;

			case("articles_config"): 	include("advertise/articles/articles_config.php"); break;
			case("articles_add"): 		include("advertise/articles/articles_add.php"); break;
			case("articles_adreq"): 	include("advertise/articles/articles_adreq.php"); break;
			case("articles_moder"): 	include("advertise/articles/articles_moder.php"); break;
			case("articles_ban"):	 	include("advertise/articles/articles_ban.php"); break;
			case("articles_edit"): 		include("advertise/articles/articles_edit.php"); break;

			case("sent_emails_config"): 	include("advertise/sent_emails/sent_emails_config.php"); break;
			case("sent_emails_adreq"): 	include("advertise/sent_emails/sent_emails_adreq.php"); break;
			case("sent_emails_add"): 	include("advertise/sent_emails/sent_emails_add.php"); break;
			case("sent_emails_edit"): 	include("advertise/sent_emails/sent_emails_edit.php"); break;
			
			case("youtube_config"): 	include("advertise/youtube/dlinks_config.php"); break;
			case("youtube_adreq"): 	include("advertise/youtube/dlinks_adreq.php"); break;
			case("youtube_add"): 	include("advertise/youtube/dlinks_add.php"); break;
			case("youtube_edit"): 	include("advertise/youtube/dlinks_edit.php"); break;
			case("youtube_claims"): 	include("advertise/youtube/dlinks_claims.php"); break;
			case("antiautoclik"): include("advertise/youtube/dlinks_antiautocliker.php"); break;

			case("black_ip_reg"):		include("black_list/black_ip_reg.php"); break;

			case("site_action_config"): 	include("action/site_action_config.php"); break;
			case("site_action_ref"): 	include("action/site_action_ref.php"); break;

			case("config_ref_wall"):	include("config_site/config_ref_wall.php"); break;
			
			case("quick_mess_config"): 	include("advertise/quick_mess/quick_mess_config.php"); break;
			case("quick_mess_add"): 	include("advertise/quick_mess/quick_mess_add.php"); break;
			case("quick_mess_edit"): 	include("advertise/quick_mess/quick_mess_edit.php"); break;
			case("mails_claims"): 	include("advertise/mails/mails_claims.php"); break;
			case("task_claims"): 	include("advertise/task/task_claims.php"); break;
			case("task_claims_us"): 	include("advertise/task/task_claims_us.php"); break;
			case("task_stat"): 	include("advertise/task/task_stat.php"); break;
			
			case("chat_config"):		include("chat/chat_config.php"); break;
			case("chat_moder"):		include("chat/chat_moder.php"); break;
			case("chat_ban_users"):		include("chat/chat_ban_users.php"); break;
			case("chat_adv_list"):		include("chat/chat_adv_list.php"); break;
			case("chat_mess_arhiv"):	include("chat/chat_mess_arhiv.php"); break;

			default: echo "Админпанель";
		}
		?>

	</div></div>
</body>
</html>

<?php  


# Заносим контент в переменную
$content = ob_get_contents();


# Очищаем буфер
ob_end_clean();

// Выводим контент
echo $content;

?>