<?php
	//=== vk.com =========================================================================//
	$SocialTypeVK = 5;

	$vk_client_id = '7291542';												// ID приложения
	$vk_client_secret = 'V0lif9of1knZzwQKgVBN';								// Защищённый ключ
	$vk_redirect_uri = 'http://bitcoinbux.ru/social/vklogin.php';				// Доверенный redirect URI
	
	function GetSocialLinkVkCom() {
		global $vk_client_id;
		global $vk_redirect_uri;
		$login_url = "http://oauth.vk.com/authorize?client_id=".$vk_client_id."&redirect_uri=".$vk_redirect_uri."&scope=email&response_type=code";
		return $login_url;
	}	
	
	//=== Mail.Ru ========================================================================//
	$SocialTypeMailRu = 4;
	
	function GetSocialLinkMailRu() {
		return '/';		
	}
	
	//=== ок.ру =========================================================================//
	$SocialTypeOKRu = 3;

	function GetSocialLinkOkRu() {
		return '/';		
	}
	
	//== gmail.com =======================================================
	$SocialTypeGMail = 1;
	
	function GetSocialLinkGMail() {
		return '/';		
	}
	
	//== facebook.com =======================================================
	$SocialTypeFB = 2;

	function GetSocialLinkFacebookCom() {
		return '/';		
	}
	
	//== instagram.com =======================================================
	function GetSocialLinkInstagramCom() {
		return '/';		
	}
?>