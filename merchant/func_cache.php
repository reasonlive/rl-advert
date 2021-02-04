<?php
if(!DEFINED("ROOT_DIR")) DEFINE("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
require(ROOT_DIR."/config.php");
require_once(ROOT_DIR."/bbcode/bbcode.lib.php");

if(function_exists('desc_bb')===false) {
	function desc_bb($desc) {
		$desc = new bbcode($desc);
		$desc = $desc->get_html();
		$desc = str_replace("&amp;", "&", $desc);
		return $desc;
	}
}

function cache_stat_links(){
	global $mysqli;
	$sql = $mysqli->query("SELECT `id`,`url`,`description`,`color` FROM `tb_ads_slink` WHERE `status`='1' AND `date_end`>'".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_row()) {
			$stat_link_arr[] = array('id_sl' => $row["0"], 'url_sl' => $row["1"], 'desc_sl' => $row["2"], 'color_sl' => $row["3"]);						
		}
	}else{
		$stat_link_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/stat_link.inc", serialize($stat_link_arr));
	$mysqli->query("ALTER TABLE `tb_ads_slink` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_slink`");
}

function cache_stat_kat(){
	global $mysqli;
	$sql = $mysqli->query("SELECT `id`,`url`,`description`,`color` FROM `tb_ads_kat` WHERE `status`='1' AND `date_end`>'".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_row()) {
			$stat_kat_arr[] = array('id_sl' => $row["0"], 'url_sl' => $row["1"], 'desc_sl' => $row["2"], 'color_sl' => $row["3"]);						
		}
	}else{
		$stat_kat_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/stat_kat.inc", serialize($stat_kat_arr));
	$mysqli->query("ALTER TABLE `tb_ads_kat` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_kat`");
}

function cache_kontext(){
	global $mysqli;
	$sql = $mysqli->query("SELECT `id`,`title`,`description`,`color`,`date`,`plan`,`totals`,`views` FROM `tb_ads_kontext` WHERE `status`='1' AND `totals`>'0' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_row()) {
			$kontext_arr[] = array(
				'id_kl' => $row["0"], 
				'title_kl' => $row["1"], 
				'desc_kl' => $row["2"], 
				'color_kl' => $row["3"], 
				'date_kl' => $row["4"], 
				'plan_kl' => $row["5"], 
				'totals_kl' => $row["6"],
				'views_kl' => $row["7"]
			);						
		}
	}else{
		$kontext_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/kontext_link.inc", serialize($kontext_arr));
	$mysqli->query("ALTER TABLE `tb_ads_kontext` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_kontext`");
}

function cache_frm_links(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_ads_frm` WHERE `status`='1' AND `date_end`>='".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$frm_links_array[] = array('dataN' => $row['date'], 'dataO' => $row['date_end'], 'link' => $row['url'], 'text' => $row['description']);
		}
	}else{
		$frm_links_array = array();
	}
	@file_put_contents(ROOT_DIR."/cache/frm_links.inc", serialize($frm_links_array));
	$mysqli->query("ALTER TABLE `tb_ads_frm` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_frm`");
}

function cache_txt_links(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_ads_txt` WHERE `status`='1' AND `date_end`>='".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$txt_links_array[] = array('dataN' => $row['date'], 'dataO' => $row['date_end'], 'link' => $row['url'], 'text' => $row['description']);
		}
	}else{
		$txt_links_array = array();
	}
	@file_put_contents(ROOT_DIR."/cache/txt_links.inc", serialize($txt_links_array));
	$mysqli->query("ALTER TABLE `tb_ads_txt` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_txt`");
}


function cache_rek_cep(){
	global $mysqli;
	$sql_del = $mysqli->query("SELECT `id` FROM `tb_ads_rc` WHERE `status`='1' ORDER BY `id` DESC");
	$all_rek_cep = $sql_del->num_rows;
	if($all_rek_cep>5) {
		$kol_dell_rek_cep =($all_rek_cep-5);
		$mysqli->query("DELETE FROM `tb_ads_rc` WHERE `status`='1' ORDER BY `id` ASC LIMIT $kol_dell_rek_cep") or die($mysqli->error);
	}

	$sql = $mysqli->query("SELECT * FROM `tb_ads_rc` WHERE `status`='1' ORDER BY `id` DESC LIMIT 5");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$rek_cep_array[] = array('id' => $row['id'], 'color' => $row['color'], 'url' => $row['url'], 'description' => $row['description'], 'view' => $row['view']);
		}
	}else{
		$rek_cep_array = array();
	}
	@file_put_contents(ROOT_DIR."/cache/rek_cep.inc", serialize($rek_cep_array));
	$mysqli->query("ALTER TABLE `tb_ads_rc` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_rc`");
}

function cache_link(){
	global $mysqli;
	$sql_del = $mysqli->query("SELECT `id` FROM `tb_ads_link` WHERE `status`='1' ORDER BY `id` DESC");
	$all_link = $sql_del->num_rows;
	if($all_link>1) {
		$kol_dell_link =($all_link-1);
		$mysqli->query("DELETE FROM `tb_ads_link` WHERE `status`='1' ORDER BY `id` ASC LIMIT $kol_dell_link") or die($mysqli->error);
	}

	$sql = $mysqli->query("SELECT * FROM `tb_ads_link` WHERE `status`='1' ORDER BY `id` DESC LIMIT 1");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$link_array[] = array('id' => $row['id'], 'color' => $row['color'], 'url' => $row['url'], 'description' => $row['description'], 'view' => $row['view']);
		}
	}else{
		$link_array = array();
	}
	@file_put_contents(ROOT_DIR."/cache/link.inc", serialize($link_array));
	$mysqli->query("ALTER TABLE `tb_ads_link` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_link`");
}

function cache_banners(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_ads_banner` WHERE `status`='1' AND `date_end`>='".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$type_write = $row["type"];
			if($type_write=="100x100") 	$b_arr_100x100[] = array('datS' => $row['date'], 'datE' => $row['date_end'], 'id_b' => $row['id'], 'urlbanner' => $row["urlbanner_load"], 'count_visit' => $row['members']);
			if($type_write=="200x300") 	$b_arr_200x300[] = array('datS' => $row['date'], 'datE' => $row['date_end'], 'id_b' => $row['id'], 'urlbanner' => $row["urlbanner_load"], 'count_visit' => $row['members']);
			if($type_write=="468x60") 	$b_arr_468x60[] = array('datS' => $row['date'], 'datE' => $row['date_end'], 'id_b' => $row['id'], 'urlbanner' => $row["urlbanner_load"], 'count_visit' => $row['members']);
			if($type_write=="468x60_frm") 	$b_arr_468x60_frm[] = array('datS' => $row['date'], 'datE' => $row['date_end'], 'id_b' => $row['id'], 'urlbanner' => $row["urlbanner_load"], 'count_visit' => $row['members']);
			if($type_write=="728x90") 	$b_arr_728x90[] = array('datS' => $row['date'], 'datE' => $row['date_end'], 'id_b' => $row['id'], 'urlbanner' => $row["urlbanner_load"], 'count_visit' => $row['members']);

		}
	}else{
		$b_arr_100x100 = array();
		$b_arr_200x300 = array();
		$b_arr_468x60 = array();
		$b_arr_728x90 = array();
	}
	if(!isset($b_arr_100x100)) 	$b_arr_100x100 = array();
	if(!isset($b_arr_200x300)) 	$b_arr_200x300 = array();
	if(!isset($b_arr_468x60)) 	$b_arr_468x60 = array();
	if(!isset($b_arr_468x60_frm)) 	$b_arr_468x60_frm = array();
	if(!isset($b_arr_728x90)) 	$b_arr_728x90 = array();

	@file_put_contents(ROOT_DIR."/cache/banners100x100.inc", serialize($b_arr_100x100));
	@file_put_contents(ROOT_DIR."/cache/banners200x300.inc", serialize($b_arr_200x300));
	@file_put_contents(ROOT_DIR."/cache/banners468x60.inc", serialize($b_arr_468x60));
	@file_put_contents(ROOT_DIR."/cache/banners468x60_frm.inc", serialize($b_arr_468x60_frm));
	@file_put_contents(ROOT_DIR."/cache/banners728x90.inc", serialize($b_arr_728x90));
	$mysqli->query("ALTER TABLE `tb_ads_banner` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_banner`");
}

function cache_beg_stroka(){
	global $mysqli;
	$sql = $mysqli->query("SELECT `id`,`url`,`description`,`color` FROM `tb_ads_beg_stroka` WHERE `status`='1' AND `date_end`>'".time()."' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_row()) {
			$beg_stroka_arr[] = array('id_beg' => $row["0"], 'url_beg' => $row["1"], 'desc_beg' => $row["2"], 'color_beg' => $row["3"]);
		}
	}else{
		$beg_stroka_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/beg_stroka.inc", serialize($beg_stroka_arr));
	$mysqli->query("ALTER TABLE `tb_ads_beg_stroka` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_beg_stroka`");
}

function cache_catalog(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_ads_catalog` WHERE `status`='1' AND `date_end`>='".time()."' ORDER BY `id` DESC");
	if (is_object($sql) && $sql->num_rows > 0) {
	//if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$catalog_array[] = array("DateStart" => $row["date"], "DateEnd" => $row["date_end"], "Title" => $row["title"], "Link" => $row["url"], "Color" => $row["color"]);
		}
	}else{
		$catalog_array = array();
	}
	@file_put_contents(ROOT_DIR."/cache/catalog.inc", serialize($catalog_array));
	$mysqli->query("ALTER TABLE `tb_ads_catalog` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_catalog`");
}

function cache_notification(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_notification` WHERE `status`='1' ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_array()) {
			$not_arr[$row["id"]] = array(
				'id_not' => $row["id"], 
				'title_not' => $row["title"], 
				'desc_not' => desc_bb($row["description"]), 
				'url_not' => $row["url"], 
				'url_img_not' => $row["url_img"]
			);
		}
	}else{
		$not_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_notification.inc", serialize($not_arr));
	$mysqli->query("ALTER TABLE `tb_notification` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_notification`");
}

function cache_news(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_news` ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$news_arr[$row["id"]] = array(
				'id_news' => $row["id"], 
				'title_news' => $row["title"], 
				'desc_news' => desc_bb($row["description"]), 
				'link_forum_news' => $row["link_forum"],
				'comments_news' => $row["comments"],
				'comments_news_status' => $row["status_comments"],
				'time_news' => $row["time"]
			);
		}
	}else{
		$news_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_news.inc", serialize($news_arr));
	$mysqli->query("ALTER TABLE `tb_news` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_news`");
}

function cache_hints(){
	global $mysqli;
	$sql = $mysqli->query("SELECT * FROM `tb_hint_tips` ORDER BY `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_array()) {
			$hints_arr[] = array(
				'id_hint' => $row["id"], 
				'title_hint' => $row["title"], 
				'desc_hint' => desc_bb($row["description"])

			);
		}
	}else{
		$hints_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_hints.inc", serialize($hints_arr));
	$mysqli->query("ALTER TABLE `tb_hint_tips` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_hint_tips`");
}

function cache_articles(){
	global $mysqli;
	$sql = $mysqli->query("SELECT `id`,`username`,`date`,`title`,`url`,`desc_min`,`desc_big`,`views` FROM `tb_ads_articles` WHERE `status`='1' ORDER BY `up_list` DESC, `id` DESC");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$art_arr[$row["id"]] = array(
				'art_id' => $row["id"], 
				'art_avtor' => $row["username"], 
				'art_date' => $row["date"], 
				'art_title' => $row["title"], 
				'art_url' => $row["url"], 
				'art_desc_min' => desc_bb($row["desc_min"]), 
				'art_desc_big' => desc_bb($row["desc_big"]), 
				'art_views' => $row["views"]
			);
		}
	}else{
		$art_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_articles.inc", serialize($art_arr));
	$mysqli->query("ALTER TABLE `tb_ads_articles` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_articles`");
}

function cache_pay_row(){
	global $mysqli;
	$sql_set_status = $mysqli->query("SELECT `id` FROM `tb_ads_pay_row` WHERE `status`='1' ORDER BY `id` DESC");
	$all_set_status = $sql_set_status->num_rows;
	if($all_set_status > 1) {
		$kol_set_status = ($all_set_status - 1);
		$mysqli->query("UPDATE `tb_ads_pay_row` SET `status`='3' WHERE `status`='1' ORDER BY `id` ASC LIMIT $kol_set_status") or die($mysqli->error);
	}

	$sql = $mysqli->query("SELECT `id`,`username`,`date`,`url`,`description`,`views` FROM `tb_ads_pay_row` WHERE `status`='1' ORDER BY `id` DESC LIMIT 1");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$pay_row_arr[] = array(
				'id_pr' => $row["id"], 
				'user_pr' => $row["username"], 
				'date_pr' => $row["date"], 
				'url_pr' => $row["url"],
				'desc_pr' => $row["description"], 
				'views_pr' => $row["views"]
			);
		}
	}else{
		$pay_row_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_pay_row.inc", serialize($pay_row_arr));
	$mysqli->query("ALTER TABLE `tb_ads_pay_row` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_pay_row`");
}

function cache_quick_mess(){
	global $mysqli;
	$sql_set_status = $mysqli->query("SELECT `id` FROM `tb_ads_quick_mess` WHERE `status`='1' ORDER BY `id` DESC");
	$all_set_status = $sql_set_status->num_rows;
	if($all_set_status > 10) {
		$kol_set_status = ($all_set_status - 10);
		$mysqli->query("UPDATE `tb_ads_quick_mess` SET `status`='3' WHERE `status`='1' ORDER BY `id` ASC LIMIT $kol_set_status") or die($mysqli->error);
	}

	$sql_del = $mysqli->query("SELECT `id` FROM `tb_ads_quick_mess` WHERE `status`='3' ORDER BY `id` DESC");
	$all_del = $sql_del->num_rows;
	if($all_del > 5) {
		$kol_del = ($all_del - 5);
		$mysqli->query("DELETE FROM `tb_ads_quick_mess` WHERE `status`='3' ORDER BY `id` ASC LIMIT $kol_del") or die($mysqli->error);
	}

	$sql = $mysqli->query("SELECT `id`,`id_us`,`username`,`url`,`description`,`color` FROM `tb_ads_quick_mess` WHERE `status`='1' ORDER BY `id` DESC LIMIT 10");
	if($sql->num_rows>0) {
		while($row = $sql->fetch_assoc()) {
			$quick_mess_arr[] = array(
				'id_s' => $row["id"], 
				'user_id_s' => $row["id_us"], 
				'user_name_s' => $row["username"], 
				'url_s' => $row["url"],
				'desc_s' => $row["description"], 
				'color_s' => $row["color"]
			);
		}
	}else{
		$quick_mess_arr = array();
	}
	@file_put_contents(ROOT_DIR."/cache/cache_quick_mess.inc", serialize($quick_mess_arr));
	$mysqli->query("ALTER TABLE `tb_ads_quick_mess` ORDER BY `id` ASC");
	$mysqli->query("OPTIMIZE TABLE `tb_ads_quick_mess`");
}

?>