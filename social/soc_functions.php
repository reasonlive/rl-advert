<?php

// (!!!) encoding UTF-8 without BOM !!!! else headers will not sends and redirects not works

$site_URL = 'http://serfnets.ru/';

function send_and_exit ($msg_class, $msg, $redirect_url) {
	global $site_URL;
	if (isset($_SESSION['soc_msg_data'])) {
		unset($_SESSION['soc_msg_data']);
	}
	$_SESSION['soc_msg_data'] = '<span id="info-msg-profile" class="'.$msg_class.'">'. $msg.'</span>';// for  ASCII pages
	//$_SESSION['soc_msg_data'] = '<span id="info-msg-profile" class="'.$msg_class.'">'.$msg.'</span>';// for UTF-8 pages
    header('Location: ' . $site_URL.$redirect_url);
	exit ();
	
}
?>