<?php

	if (!isset($_GET['go'])) {
		sleep(3);
		exit();
	}

	$reg = true;
	
	if (isset($_GET['act'])) {
		session_start();
		if (isset($_SESSION['reg'])) {
			unset($_SESSION['reg']);
		}
		if ($_GET['act'] == 'reg') {
			$_SESSION['reg'] = 'reg';
		}
	}

	@require_once('social_setup.php');	

	switch ($_GET['go']) {
		case 'wm':
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkWmCom().'" /></head>';
			exit ();
		break;	
		case 'vk':
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkVkCom().'" /></head>';
			exit ();
		break;

		case 'mr':
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkMailRu().'" /></head>';
			exit ();
		break;

		case 'ok':
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkOkRu().'" /></head>';
			exit ();
		break;
		case 'gm':
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkGMail().'" /></head>';
			exit ();
		break;
		case 'fb': //facebook
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkFacebookCom().'" /></head>';
			exit ();
		break;		
		case 'ig': //instagramm
			echo '<head><meta http-equiv="refresh" content="0;URL='.GetSocialLinkInstagramCom().'" /></head>';
			exit ();
		break;
	}
?>