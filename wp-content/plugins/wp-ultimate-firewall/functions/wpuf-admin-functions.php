<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Security Settings
switch (get_option("wpuf_select_set")) {
	case 2:
		update_option ('wpuf_header_sec', '1');
		update_option ('wpuf_xr_security', '0');
		update_option ('wpuf_disable_fileedit', '0');
		update_option ('wpuf_wpscan_protection', '1');
		update_option ('wpuf_proxy_protection', '0'); // 0 - 1 - 2
		update_option ('wpuf_access_security', '0'); // 0 - 1
		update_option ('wpuf_sql_protection', '0');
		update_option ('wpuf_badbot_protection', '0');
		update_option ('wpuf_fakebot_protection', '0');
		update_option ('wpuf_tor_protection', '0');
		
		update_option ('wpuf_recaptcha_protection_lrl', '0'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_login', '0'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_registration', '0'); //Child Settings		
			update_option ('wpuf_recaptcha_protection_lrl_lpf', '0'); //Child Settings		
			
		update_option ('wpuf_spam_attacks', '0'); //Child Settings
			update_option ('wpuf_spam_attacks_general', '0'); //Child Settings
			update_option ('wpuf_spam_attacks_bf', '0'); //Child Settings
		
		update_option ('wpuf_comment_sec_wc', '0'); // Comment Security - 0 - 1 (honeypot) - 2 (reCAPTCHA)
		update_option ('wpuf_pingback_disable', '1');
		update_option ('wpuf_content_security', '0');	
		break;
		
	case 3:
		update_option ('wpuf_header_sec', '1');
		update_option ('wpuf_xr_security', '1');
		update_option ('wpuf_wpscan_protection', '1');
		update_option ('wpuf_disable_fileedit', '1');
		update_option ('wpuf_proxy_protection', '1'); // 0 - 1 - 2
		update_option ('wpuf_access_security', '0'); // 0 - 1
		update_option ('wpuf_sql_protection', '0');
		update_option ('wpuf_badbot_protection', '1');
		update_option ('wpuf_fakebot_protection', '1');
		update_option ('wpuf_tor_protection', '0');
		
		update_option ('wpuf_recaptcha_protection_lrl', '1'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_login', '0'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_registration', '1'); //Child Settings		
			update_option ('wpuf_recaptcha_protection_lrl_lpf', '0'); //Child Settings	
			
		update_option ('wpuf_spam_attacks', '0'); //Child Settings
			update_option ('wpuf_spam_attacks_general', '0'); //Child Settings
			update_option ('wpuf_spam_attacks_bf', '0'); //Child Settings
		
		update_option ('wpuf_comment_sec_wc', '1'); // Comment Security - 0 - 1 (honeypot) - 2 (reCAPTCHA)
		update_option ('wpuf_pingback_disable', '1');
		update_option ('wpuf_content_security', '0');
		break;
		
	case 4:
		update_option ('wpuf_header_sec', '1');
		update_option ('wpuf_xr_security', '1');
		update_option ('wpuf_wpscan_protection', '1');
		update_option ('wpuf_disable_fileedit', '1');
		update_option ('wpuf_proxy_protection', '1'); // 0 - 1 - 2
		update_option ('wpuf_access_security', '0'); // 0 - 1
		update_option ('wpuf_sql_protection', '0');
		update_option ('wpuf_badbot_protection', '1');
		update_option ('wpuf_fakebot_protection', '1');
		update_option ('wpuf_tor_protection', '0');
		
		update_option ('wpuf_recaptcha_protection_lrl', '1'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_login', '1'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_registration', '1'); //Child Settings		
			update_option ('wpuf_recaptcha_protection_lrl_lpf', '1'); //Child Settings	
		
		update_option ('wpuf_spam_attacks', '1'); //Child Settings
			update_option ('wpuf_spam_attacks_general', '1'); //Child Settings
			update_option ('wpuf_spam_attacks_bf', '1'); //Child Settings
		
		update_option ('wpuf_comment_sec_wc', '2'); // Comment Security - 0 - 1 (honeypot) - 2 (reCAPTCHA)
		update_option ('wpuf_pingback_disable', '1');
		update_option ('wpuf_content_security', '1');
		break;
		
	case 5:
		update_option ('wpuf_header_sec', '1');
		update_option ('wpuf_xr_security', '1');
		update_option ('wpuf_wpscan_protection', '1');
		update_option ('wpuf_disable_fileedit', '1');
		update_option ('wpuf_proxy_protection', '2'); // 0 - 1 - 2
		update_option ('wpuf_access_security', '1'); // 0 - 1
		update_option ('wpuf_sql_protection', '1');
		update_option ('wpuf_badbot_protection', '1');
		update_option ('wpuf_fakebot_protection', '1');
		update_option ('wpuf_tor_protection', '1');
		
		update_option ('wpuf_recaptcha_protection_lrl', '1'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_login', '1'); //Child Settings
			update_option ('wpuf_recaptcha_protection_lrl_registration', '1'); //Child Settings		
			update_option ('wpuf_recaptcha_protection_lrl_lpf', '1'); //Child Settings	
		
		update_option ('wpuf_spam_attacks', '1'); //Child Settings
			update_option ('wpuf_spam_attacks_general', '1'); //Child Settings
			update_option ('wpuf_spam_attacks_bf', '1'); //Child Settings
		
		update_option ('wpuf_comment_sec_wc', '2'); // Comment Security - 0 - 1 (honeypot) - 2 (reCAPTCHA)
		update_option ('wpuf_pingback_disable', '1');
		update_option ('wpuf_content_security', '1');
		break;
		
	default:
	// Default
}

//Admin Logins
if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_admin") == 1 ) {
	
	//Session Start
	if (!isset($_SESSION)) {
		session_start();
	}
			
	// Check  admin
	function check_admin_logins() {
		if(current_user_can('manage_options')){
			return true;
		} else {
			return false;
		}
	}

	function if_admin_send_alert_mail() {

		if(check_admin_logins() === true and !isset($_SESSION['logged_in_once'])) {
			
			//Check IP
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			//Check User Agent
			$get_useragent = $_SERVER['HTTP_USER_AGENT'];
				
			//Date and Time
			$time = current_time('d F Y - H:i');

			$message = __("An admin logged into the Wordpress admin panel:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'Website: '. get_option("home") ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
			
			$email = wp_mail(
				get_option("wpuf_mail_notify"),
				
				trim("Administrator Logged in - ". get_option("blogname")),
				
				stripslashes( trim($message) ),
				
				"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
			);
			
			$_SESSION['logged_in_once'] = 1;
		}
	}
	
add_action('admin_notices', 'if_admin_send_alert_mail');

//if log out
function wpuf_if_admin_logged_out() {
	//Session Start
	if (!isset($_SESSION)) {
		session_start();
	}
	
	if( $_SESSION['logged_in_once'] == 1 ){
		unset($_SESSION['logged_in_once']);
	}
}

add_action('wp_logout', 'wpuf_if_admin_logged_out');

}

//Last Login Time
function set_last_login($login) {
	$user = get_user_by('login', $login);
	if (user_can($user->ID, 'administrator')) {
		$curent_login_time = get_user_meta(	$user->ID , 'ufcurrent_login', true);
		//add or update the last login value for logged in user
		if(!empty($curent_login_time)){
			update_user_meta( $user->ID, 'uf_last_login', $curent_login_time );
			update_user_meta( $user->ID, 'ufcurrent_login', current_time('mysql') );
		} else {
			update_user_meta( $user->ID, 'uf_last_login', current_time('mysql') );
			update_user_meta( $user->ID, 'ufcurrent_login', current_time('mysql') );
		}
	}
}

function get_last_login() {
	
   $user_ID = get_current_user_id(); 
   $check_option = get_user_meta($user_ID, 'uf_last_login', true);
   $last_login = get_user_meta($user_ID, 'uf_last_login', true);
   $date_format = get_option('date_format') . ' ' . get_option('time_format');
   
	if(wp_is_mobile()) {
		$last_login = date("M j, y, g:i a", strtotime($last_login));  
		if ( empty($check_option) ) {
			$the_last_login = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_last_login = $last_login;
		}
	}else {
		$last_login = date("M j, y, g:i a", strtotime($last_login));  
		if ( empty($check_option) ) {
			$the_last_login = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_last_login = $last_login;
		}
	}
   return $the_last_login;
}

function get_current_login() {
	
   $user_ID = get_current_user_id(); 
   $check_option = get_user_meta($user_ID, 'ufcurrent_login', true);
   $current_login = get_user_meta($user_ID, 'ufcurrent_login', true);
   $date_format = get_option('date_format') . ' ' . get_option('time_format');
   
	if(wp_is_mobile()) {
		$current_login = date("M j, y, g:i a", strtotime($current_login));  
		if ( empty($check_option) ) {
			$the_current_login = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_current_login = $current_login;
		} 
	}else {
		$current_login = date("M j, y, g:i a", strtotime($current_login));  
		if ( empty($check_option) ) {
			$the_current_login = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_current_login = $current_login;
		} 
	}
   return $the_current_login;
}

add_action('wp_login', 'set_last_login');

// Get IP Address

function set_last_admin_ip($login) {
				
	$user = get_user_by('login', $login);
	
	if (user_can($user->ID, 'administrator')) {
		//Check IP
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	
		$curent_admin_ip = get_user_meta($user->ID , 'last_admin_ip', true);
		//add or update the last login value for logged in user
		if(!empty($curent_admin_ip)) {
			update_user_meta( $user->ID, 'last_admin_ip', $curent_admin_ip );
			update_user_meta( $user->ID, 'current_admin_ip', $ip );
		} else {
			update_user_meta( $user->ID, 'last_admin_ip', $ip );
			update_user_meta( $user->ID, 'current_admin_ip', $ip );
		}
	}
}

function get_last_admin_ip() {
	
   $user_ID = get_current_user_id(); 
   $last_ip = get_user_meta($user_ID, 'last_admin_ip', true);
   $check_option = get_user_meta($user_ID, 'last_admin_ip', true);
   
	if(wp_is_mobile()) {
		if ( empty($check_option) ) {
			$the_last_ip = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_last_ip = $last_ip;
		} 
	}else {
		if ( empty($check_option) ) {
			$the_last_ip = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_last_ip = $last_ip;
		}
	}
	
   return $the_last_ip;
}

function get_current_admin_ip() {
	
   $user_ID = get_current_user_id(); 
   $current_ip = get_user_meta($user_ID, 'current_admin_ip', true);
   $check_option = get_user_meta($user_ID, 'current_admin_ip', true);
   
	if(wp_is_mobile()) {
		if ( empty($check_option) ) {
			$the_current_ip = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_current_ip = $current_ip;
		} 
	}else {
		if ( empty($check_option) ) {
			$the_current_ip = __("This setting will be activated in your other admin login.", "ua-protection-lang" );
		} else {
			$the_current_ip = $current_ip;
		}
	}
	
   return $the_current_ip;
}

add_action('wp_login', 'set_last_admin_ip');