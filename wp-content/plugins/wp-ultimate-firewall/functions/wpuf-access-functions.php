<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Load Firewall
function load_access_settings() {

    // check admin
if (is_user_logged_in() && is_admin()) return '';

	if (ac_white_check_ip_address(get_option('get_white_list'))) {
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$remote_ua = $_SERVER['HTTP_USER_AGENT'];
		if (s_check_ip_address($remote_ip, get_option('get_ip_list')) ||
			s_check_user_agent($remote_ua, get_option('get_ua_list')) ||
			cch_check_user_agent(get_option('get_blocked_country_list'))) {
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
			exit;
		}
	}
}

add_action( 'plugins_loaded', 'load_access_settings' );

//Check IP Adress
function s_check_ip_address($ip, $wpuf_firewall_iplist) {

    $list_arr = explode("\r\n", $wpuf_firewall_iplist);

    // Check IP
    if (in_array($ip, $list_arr)) return true;

    // IP range
    foreach ($list_arr as $k => $v) {
        if (substr_count($v, '-')) {
            $curr_ip_range = explode('-', $v);
            $high_ip = ip2long(trim($curr_ip_range[1]));
            $low_ip = ip2long(trim($curr_ip_range[0]));
            $checked_ip = ip2long($ip);
            if (sprintf("%u", $checked_ip) <= sprintf("%u", $high_ip)  &&
                sprintf("%u", $low_ip) <= sprintf("%u", $checked_ip)) return true;
        }
    }

    return false;
}


//Check User Agent
function s_check_user_agent($ua, $wpuf_firewall_useragents) {
    $list_arr = explode("\r\n", $wpuf_firewall_useragents);
    if (in_array($ua, $list_arr)) return true;

    return false;
}

//Check Country
if ( !empty (get_option('get_blocked_country_list')) ) {
	function cch_check_user_agent($wpuf_firewall_useragents) {
		//Check Useragent
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		
		//Check IP
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		//Array Control
		$list_arr = explode("\r\n", $wpuf_firewall_useragents);
		
		//Get Country
		$url = 'http://www.geoplugin.net/xml.gp?ip='.$ip;
		$ch  = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_REFERER, "https://google.com");
		$countryCode = curl_exec($ch);
		curl_close($ch);

		$country_Text  = simplexml_load_string($countryCode);
		$country_check = $country_Text->geoplugin_countryCode;

		if (in_array($country_check, $list_arr)) return true;

		return false;
	}
} else { 	

	function cch_check_user_agent($wpuf_firewall_useragents) {
		return false;
	}
}

//Check WHITELIST
function ac_white_check_ip_address($white_ip_list) {
	
	//Check IP
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	//Array Control
    $white_list_arr = explode("\r\n", $white_ip_list);

    // Check IP
    if (in_array($ip, $white_list_arr)) return false;
	
	foreach ($white_list_arr as $k => $v) {
	if (substr_count($v, '-')) {
            $curr_ip_range = explode('-', $v);
            $high_ip = ip2long(trim($curr_ip_range[1]));
            $low_ip = ip2long(trim($curr_ip_range[0]));
            $checked_ip = ip2long($ip);
            if (sprintf("%u", $checked_ip) <= sprintf("%u", $high_ip)  &&
                sprintf("%u", $low_ip) <= sprintf("%u", $checked_ip)) return false;
        }
    }

    return true;
	
}