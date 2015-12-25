<?php
session_start();
require('vOauth.php');
$v = new vOauth();
$_SESSION['VOA']['PROVIDER'] = 'v';
$v->setClient(get_option('voa_v_api_id'));
$v->setSecret(get_option('voa_v_api_secret'));
$v->addScope("profile");
$v->setRedirect(rtrim(site_url(), '/') . '/');
define('HTTP_UTIL', get_option('voa_http_util'));
define('CLIENT_ENABLED', get_option('voa_v_api_enabled'));
if (!$_SESSION['VOA']['LAST_URL']) {
	$redirect_url = esc_url($_GET['redirect_to']);
	if (!$redirect_url) {$redirect_url = strtok($_SERVER['HTTP_REFERER'], "?");}
	$_SESSION['VOA']['LAST_URL'] = $redirect_url;}
if (!CLIENT_ENABLED) {$this->voa_end_login("This third-party authentication provider has not been enabled. Please notify the admin or try again later.");}
elseif (isset($_GET['error_description'])) {$this->voa_end_login($_GET['error_description']);}
elseif (isset($_GET['error_message'])) {$this->voa_end_login($_GET['error_message']);}
elseif (isset($_GET['code'])) {
	if ($_SESSION['VOA']['STATE'] == $_GET['state']) {
		$v->setCode($_GET['code']);
		$access_token = $v->getToken($_SESSION['VOA']['STATE']);
		$expires_in = $v->getExpiresIn();
		$expires_at = time() + $expires_in;
		if (!$access_token || !$expires_in) {$this->voa_end_login("Sorry, we couldn't log you in. Malformed access token result detected. Please notify the admin or try again later.");}
		else {
			$_SESSION['VOA']['ACCESS_TOKEN'] = $access_token;
			$_SESSION['VOA']['EXPIRES_IN'] = $expires_in;
			$_SESSION['VOA']['EXPIRES_AT'] = $expires_at;
			try {
				$vInfo = $v->getVInfo();
				$oauth_identity = array();
				$oauth_identity['provider'] = $_SESSION['VOA']['PROVIDER'];
				$oauth_identity['id'] = $vInfo->{'enlid'};
				$oauth_identity['agent'] = $vInfo->{'agent'};
				$oauth_identity['vlevel'] = $vInfo->{'vlevel'};
				$oauth_identity['vpoints'] = $vInfo->{'vpoints'};
				$oauth_identity['quarantine'] = $vInfo->{'quarantine'};
				$oauth_identity['blacklisted'] = $vInfo->{'blacklisted'};
				$oauth_identity['verified'] = $vInfo->{'verified'};

				$this->voa_login_user($oauth_identity);
			} catch (Exception $e) {echo $e->getMessage();}}} else {$this->voa_end_login("Sorry, we couldn't log you in. Please notify the admin or try again later.");}
} else {
	if ((empty($_SESSION['VOA']['EXPIRES_AT'])) || (time() > $_SESSION['VOA']['EXPIRES_AT'])) {$this->voa_clear_login_state();}
	$state = uniqid('', true);
	$_SESSION['VOA']['STATE'] = md5($state);
	header("Location: " . $v->getAuthURL($state));}
//this seems to break something not allowing logging in....leaving in for eventual fix
//$this->voa_end_login("Sorry, we couldn't log you in. The authentication flow terminated in an unexpected way. Please notify the admin or try again later.");