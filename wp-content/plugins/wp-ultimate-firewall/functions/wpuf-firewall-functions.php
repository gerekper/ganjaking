<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Code is poetry.';
    exit;
}

//Check WHITELIST
function white_check_ip_address($white_ip_list) {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
			
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

/* Block Type (For Sessions) */
function wpuf_block_type() {
if( get_option("wpuf_security_ban")  == 1 ) {
	
$useragentCheck = strtolower($_SERVER['HTTP_USER_AGENT']);
if (strpos(strtolower($useragentCheck), "googlebot") === false || strpos(strtolower($useragentCheck), "bingbot") === false || strpos(strtolower($useragentCheck), "yahoo! slurp") === false || strpos(strtolower($useragentCheck), "yandexbot/3.0") === false) {

	header('HTTP/1.1 403 Forbidden');
	header('Status: 403 Forbidden');
	header('Connection: Close');
	//die
	exit;

}
	
} elseif ( get_option("wpuf_security_ban")  == 2 ) {
	
	$useragentCheck = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strpos(strtolower($useragentCheck), "googlebot") === false || strpos(strtolower($useragentCheck), "bingbot") === false || strpos(strtolower($useragentCheck), "yahoo! slurp") === false || strpos(strtolower($useragentCheck), "yandexbot/3.0") === false) {
	
		$displayError = '';
		
		if(isset($_POST['submit'])) {
				if(isset($_POST['g-recaptcha-response'])) {
					$privateKey = get_option('wpuf_recaptcha_secretkey');
					$responseStatus = $_POST['g-recaptcha-response'];
					$remoteip = $_SERVER['REMOTE_ADDR'];
					$url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privateKey."&response=".$responseStatus."&remoteip=".$remoteip);

					$resultStatus = json_decode($url);

					if($resultStatus->success == true) {
					
					//Session Start
					if (!isset($_SESSION)) {
						session_start();
					}
						
					$_SESSION['last_request_count'] = 1;
					$_SESSION['last_request_count_bf'] = 1;
					$_SESSION['last_session_request_psp'] = 1;
					$_SESSION['last_session_request_hg'] = 1;
					 
					$displayError = "<div class='alertSuccess'>". __('Success! Please wait...', 'ua-protection-lang' ) ."</div>";
					$displayError .= "<meta http-equiv='refresh' content='2'>";
					
					} else {
					  $displayError = "<div class='alert'>" . __('Please verify reCAPTCHA.', 'ua-protection-lang' ) ."</div>";
					} 
				} else {
					$displayError = "<div class='alert'>" . __('Please enable JavaScript and verify reCAPTCHA!', 'ua-protection-lang' ) ."</div>";
				} 
		  }
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
				
			$echFirewallPage = '<!DOCTYPE html><html>
				<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>' . __("Please verify reCAPTCHA!", "ua-protection-lang" ) .'</title>
				<script src="https://www.google.com/recaptcha/api.js"></script>
				<style>input[type=submit] {
					padding:5px 15px; 
					margin:20px 0 0;
					background:#34495e; 
					color:#fff;
					border:0 none;
					cursor:pointer;
					-webkit-border-radius: 5px;
					border-radius: 5px; 
				}
				.centerFirewall { margin:1em;background-color:#ecf0f1; border: 1px solid #e5e5e5;padding:3em 2em;text-align:center; }
				.g-recaptcha{  display: table;margin: 0 auto;}
				.alert {display:block;margin:1em;padding:1em;background-color:#e74c3c;color:#fff;}.alertSuccess {display:block;margin:1em;padding:1em;background-color:#2ecc71;color:#fff;}</style>
				</head>
					<body>
				'. $displayError .'
				<div class="centerFirewall">
					<form action="" method="post">
					<h1>' . __("Attention!", "ua-protection-lang" ) .'</h1>
					<div class="g-recaptcha" data-sitekey="'. get_option("wpuf_recaptcha_sitekey") .'"></div>
					<input id="submit" type="submit" name="submit" value="' . __("Submit", "ua-protection-lang" ) .'">
					</form>
				</div>
				
				<div class="centerFirewall">
				<p>' . __("<strong>Information:</strong> Our systems have detected unusual traffic from your computer network. This page checks to see if it is really you sending the requests, and not a robot.", "ua-protection-lang" ) .'</p>
				<p><strong>' . __("Your IP Address:", "ua-protection-lang" ) .'</strong> '. $ip .'</p>
				<p><strong>' . __("Your User Agent:", "ua-protection-lang" ) .'</strong> '. $get_useragent .'</p>
				<p><strong>' . __("Time:", "ua-protection-lang" ) .'</strong> '.$time.'</p>
				</div>
				
					</body>
				</html>';
				
				$echFirewallPage_Min = preg_replace("/\s+/", " ", $echFirewallPage);
				$echFirewallPage_Clear = trim($echFirewallPage_Min);
				die($echFirewallPage_Clear);

		}
	}
}

//Access Security
if( get_option("wpuf_access_security")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
		
		include(ABSPATH . "wp-includes/pluggable.php"); 
		if ( !( current_user_can('administrator') )) {
		
		$useragentCheck = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (strpos(strtolower($useragentCheck), "googlebot") === false || strpos(strtolower($useragentCheck), "bingbot") === false || strpos(strtolower($useragentCheck), "yahoo! slurp") === false || strpos(strtolower($useragentCheck), "yandexbot/3.0") === false) {
			
			if (!isset($_SESSION)) {
				session_start();
			}

			if (!isset($_SESSION['last_session_request_hg'])) {

						$displayError = '';
						
						if(isset($_POST['submit'])) {
								if(isset($_POST['g-recaptcha-response'])) {
									$privateKey = get_option('wpuf_recaptcha_secretkey');
									$responseStatus = $_POST['g-recaptcha-response'];
									$remoteip = $_SERVER['REMOTE_ADDR'];
									$url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privateKey."&response=".$responseStatus."&remoteip=".$remoteip);

									$resultStatus = json_decode($url);

									if($resultStatus->success == true) {
										
									$_SESSION['last_request_count'] = 1;
									$_SESSION['last_request_count_bf'] = 1;
									$_SESSION['last_session_request_psp'] = 1;
									$_SESSION['last_session_request_hg'] = 1;
							
									 $displayError = "<div class='alertSuccess'>". __('Success! Please wait...', 'ua-protection-lang' ) ."</div>";
									 $displayError .= "<meta http-equiv='refresh' content='2'>";
									
									} else {
									  $displayError = "<div class='alert'>" . __('Please verify reCAPTCHA.', 'ua-protection-lang' ) ."</div>";
									} 
								} else {
									$displayError = "<div class='alert'>" . __('Please enable JavaScript and verify reCAPTCHA!', 'ua-protection-lang' ) ."</div>";
								} 
						  }
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

							$echFirewallPage = '<!DOCTYPE html><html>
								<head>
								<meta charset="UTF-8">
								<meta name="viewport" content="width=device-width, initial-scale=1">
								<title>' . __("Please verify reCAPTCHA!", "ua-protection-lang" ) .'</title>
								<script src="https://www.google.com/recaptcha/api.js"></script>
								<style>input[type=submit] {
									padding:5px 15px; 
									margin:20px 0 0;
									background:#34495e; 
									color:#fff;
									border:0 none;
									cursor:pointer;
									-webkit-border-radius: 5px;
									border-radius: 5px; 
								}
								.centerFirewall { margin:1em;background-color:#ecf0f1; border: 1px solid #e5e5e5;padding:3em 2em;text-align:center; }
								.g-recaptcha{  display: table;margin: 0 auto;}
								.alert {display:block;margin:1em;padding:1em;background-color:#e74c3c;color:#fff;}.alertSuccess {display:block;margin:1em;padding:1em;background-color:#2ecc71;color:#fff;}</style>
								</head>
									<body>
								'. $displayError .'
								<div class="centerFirewall">
									<form action="" method="post">
									<h1>' . __("Please complete the security check.", "ua-protection-lang" ) .'</h1>
									<div class="g-recaptcha" data-sitekey="'. get_option("wpuf_recaptcha_sitekey") .'"></div>
									<input id="submit" type="submit" name="submit" value="' . __("Submit", "ua-protection-lang" ) .'">
									</form>
								</div>
								<div class="centerFirewall">
									<p>' . __("<strong>Information:</strong> This page checks to see if it is really you sending the requests, and not a robot.", "ua-protection-lang" ) .'</p>
									<p><strong>' . __("Your IP Address:", "ua-protection-lang" ) .'</strong> '. $ip .'</p>
									<p><strong>' . __("Your User Agent:", "ua-protection-lang" ) .'</strong> '. $get_useragent .'</p>
									<p><strong>' . __("Time:", "ua-protection-lang" ) .'</strong> '.$time.'</p>
								</div>
				
									</body>
								</html>';
								
								$echFirewallPage_Min = preg_replace("/\s+/", " ", $echFirewallPage);
								$echFirewallPage_Clear = trim($echFirewallPage_Min);
								die($echFirewallPage_Clear);

				}

			}
		}
		
	}
}


// Header Security

if( get_option("wpuf_header_sec")  == 1 ) {
	//Set Security Header
	header('X-Content-Type-Options: nosniff');
	header("X-XSS-Protection: 1; mode=block");
	header("X-Frame-Options: SAMEORIGIN; mode=block");
	
}

//Comment Security
if( get_option("wpuf_comment_sec_wc")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
	
		function wpuf_comment_sec_func( array $data ) {
		  if( empty($data['comment_author_url']) ){
			return $data;
		  } else {
			//START MAIL Notify
			if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_fc")  == 1 ) {
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
				
				//URL
				$spamUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				
				$message = __("Spam Comments detected and blocked:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'URL: '. $spamUrl ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
					
				$email = wp_mail(
					get_option("wpuf_mail_notify"),
					
					trim("Spam Comment Alert - ". get_option("blogname")),
					
					stripslashes( trim($message) ),
					
					"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
				);
				
			}
			//END Mail Notify
			
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
			exit;
		  }
		}
		 
		add_filter('preprocess_comment','wpuf_comment_sec_func'); 

			function wpuf_comment_honeypot_css () {
				wp_enqueue_style("wpuf-comment-honeypot", WPUF_URL."functions/assets/css/comment-protection-honeypot.css");
			}
			
			add_filter( 'wp_print_styles', 'wpuf_comment_honeypot_css');

	}
}

//Comment Security with ReCaptcha
if( get_option("wpuf_comment_sec_wc")  == 2 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {

		function wpuf_frontend_recaptcha_script() {
			wp_register_script("wpuf_recaptcha", "https://www.google.com/recaptcha/api.js");
			wp_enqueue_script("wpuf_recaptcha");
		}
		add_action("comment_form_after_fields", "wpuf_frontend_recaptcha_script");


		function wpuf_display_recaptcha() { ?>
			<div id="g-recaptcha" class="g-recaptcha" style="margin:14px 0;" data-sitekey="<?php echo get_option('wpuf_recaptcha_sitekey'); ?>"></div>
		<?php }

		add_action( 'comment_form_after_fields', 'wpuf_display_recaptcha' );

		if (get_option('wpuf_disable_rcp_lgus') == 0) {
			add_action( 'comment_form_logged_in_after', 'wpuf_frontend_recaptcha_script');
			add_action( 'comment_form_logged_in_after', 'wpuf_display_recaptcha' );
		}

		function wpuf_verify_captcha($captcha_data) {
			if (isset($_POST['g-recaptcha-response'])) {
				$get_recaptcha_secretkey = get_option('wpuf_recaptcha_secretkey');
				$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $get_recaptcha_secretkey ."&response=". $_POST['g-recaptcha-response']);
				$response = json_decode($response["body"], true);
				if (true == $response["success"]) {
					return $captcha_data;
				} else {
				
				$message =  __('Please verify reCAPTCHA.', 'ua-protection-lang' );
				$title = 'reCAPTCHA Block';
				$args = array('response' => 200);
				wp_die( $message, $title, $args );
				exit(0);
			
				}
			} else {
				$message = __('Please enable JavaScript and verify reCAPTCHA', 'ua-protection-lang' );
				$title = 'reCAPTCHA Block';
				$args = array('response' => 200);
				wp_die( $message, $title, $args );
				exit(0);
			}
		}
	
	add_filter("preprocess_comment", "wpuf_verify_captcha");

	}
}

//Comment Pingback Security
if( get_option("wpuf_pingback_disable")  == 1 ) {
	
	//Pingback Attack Protection
	function pingback_func( &$links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, $home ) )
            unset($links[$l]);
	}
 
	add_action( 'pre_ping', 'pingback_func' );

}

//XML-RPC and REST API Security
if( get_option("wpuf_xr_security")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
	
		// Hide xmlrpc.php in HTTP response headers
		add_filter( 'wp_headers', function( $headers ) {
			unset( $headers[ 'X-Pingback' ] );
			return $headers;
		} );
		
		//REST API Security Alarm
		function wpuf_remove_api_alarm() {
			
			// Filters for WP-API version 1.x
			add_filter( 'json_enabled', '__return_false' );
			add_filter( 'json_jsonp_enabled', '__return_false' );

			// Filters for WP-API version 2.x
			add_filter( 'rest_enabled', '__return_false' );
			add_filter( 'rest_jsonp_enabled', '__return_false' );

			// Remove REST API info from head and headers
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
			
			if( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_cannot_access', __( 'REST API disabled.', 'ua-protection-lang' ), array( 'status' => rest_authorization_required_code() ) );
			}
			
		}
		
		add_filter( 'rest_authentication_errors', 'wpuf_remove_api_alarm' );
		
	}
}

//Prevent WPScan Requests
if( get_option("wpuf_wpscan_protection")  == 1 ) {
	
	// Remove Versions
	add_filter( 'the_generator', '__return_false', 10, 1 );
	
	function wpuf_version_remove_alarm( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
	}
	// Remove the version from enqueued stylesheets (CSS)
	add_filter( 'style_loader_src', 'wpuf_version_remove_alarm', 9999 );

	// Remove the version from enqueued javascript files
	add_filter( 'script_loader_src', 'wpuf_version_remove_alarm', 9999 );
	
}

//Spam Attacks
if( get_option("wpuf_spam_attacks") && get_option("wpuf_spam_attacks_general")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {	
		include(ABSPATH . "wp-includes/pluggable.php"); 
		if ( !( current_user_can('administrator') )) {

			function wpuf_flood_sec() {
				if (!isset($_SESSION)) {
					session_start();
				 }
				//spam cookie registering
				 if(@$_SESSION['last_session_request'] > time() - 3) {
					if(empty($_SESSION['last_request_count'])){
						$_SESSION['last_request_count'] = 1;
					} elseif($_SESSION['last_request_count'] < 5) {
						$_SESSION['last_request_count'] = $_SESSION['last_request_count'] + 1;
					} elseif($_SESSION['last_request_count'] == 5) {
					
					//START MAIL Notify
					if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_spam")  == 1 ) {
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
						
						//URL
						$spamUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							
						$message = __("Spam Attacks detected and blocked:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'URL: '. $spamUrl ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
							
						$email = wp_mail(
							get_option("wpuf_mail_notify"),
							
							trim("Spam Alert - ". get_option("blogname")),
							
							stripslashes( trim($message) ),
							
							"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
						);
						
					}
					//END Mail Notify	

						// Block for 30 minutes
						$_SESSION['last_request_count'] = $_SESSION['last_request_count'] + 1;
						$_SESSION['last_session_request'] = (time() + 1795);
						wpuf_block_type();
						
					} elseif($_SESSION['last_request_count'] >= 6) {
						
						// Block for 30 minutes
						$_SESSION['last_session_request'] = (time() + 1795);
						wpuf_block_type();
					}
					
				 } else {
					$_SESSION['last_request_count'] = 1;
				 }

				 $_SESSION['last_session_request'] = time();

			}
			
			add_action('init', 'wpuf_flood_sec');
		}
	}
}

//Brute Force
if( get_option("wpuf_spam_attacks") && get_option("wpuf_spam_attacks_bf")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
	
		function wp_limit_login_attempt_func() {
			global $pagenow;
			if( $pagenow == 'wp-login.php') {
				
				if (!isset($_SESSION)) {
					session_start();
				 }
				//spam cookie registering
				 if(@$_SESSION['last_session_request_bf'] > time() - 5) {
					if(empty($_SESSION['last_request_count_bf'])){
						$_SESSION['last_request_count_bf'] = 1;
					} elseif($_SESSION['last_request_count_bf'] < 5) {
						$_SESSION['last_request_count_bf'] = $_SESSION['last_request_count_bf'] + 1;
					} elseif($_SESSION['last_request_count_bf'] == 5) {
						
					//START MAIL Notify
					if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_bruteforce")  == 1 ) {
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
						
						//URL
						$spamUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
							
						$message = __("Brute-Force Attacks detected and blocked:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'URL: '. $spamUrl ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
							
						$email = wp_mail(
							get_option("wpuf_mail_notify"),
							
							trim("Brute-Force Alert - ". get_option("blogname")),
							
							stripslashes( trim($message) ),
							
							"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
						);
						
					}
					//END Mail Notify
						
						// Block for 30 minutes
						$_SESSION['last_request_count_bf'] = $_SESSION['last_request_count_bf'] + 1;
						$_SESSION['last_session_request_bf'] = (time() + 1795);
						wpuf_block_type();
						
					} elseif($_SESSION['last_request_count_bf'] >= 6) {

						// Block for 30 minutes
						$_SESSION['last_session_request_bf'] = (time() + 1795);
						
						wpuf_block_type();
					}
				 } else {
					$_SESSION['last_request_count_bf'] = 1;
				 }

				 $_SESSION['last_session_request_bf'] = time();
			}
		}
		
		add_action('init', 'wp_limit_login_attempt_func');
	}
}

//Potential Spam
if( get_option("wpuf_spam_attacks") && get_option("wpuf_spam_attacks_psp")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
		
		include(ABSPATH . "wp-includes/pluggable.php"); 
		if ( !( current_user_can('administrator') )) {
		
		$useragentCheck = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (strpos(strtolower($useragentCheck), "googlebot") === false || strpos(strtolower($useragentCheck), "bingbot") === false || strpos(strtolower($useragentCheck), "yahoo! slurp") === false || strpos(strtolower($useragentCheck), "yandexbot/3.0") === false) {
		
			//Cookie Checker
			setcookie("wpuf_cookie_check", "cookie_check", time() + 3600, '/');
			//User Agent Checker
			if(!isset($_COOKIE) || !isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
		
				if (!isset($_SESSION)) {
					session_start();
				}

				if (!isset($_SESSION['last_session_request_psp'])) {

						$displayError = '';
						
						if(isset($_POST['submit'])) {
								if(isset($_POST['g-recaptcha-response'])) {
									$privateKey = get_option('wpuf_recaptcha_secretkey');
									$responseStatus = $_POST['g-recaptcha-response'];
									$remoteip = $_SERVER['REMOTE_ADDR'];
									$url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privateKey."&response=".$responseStatus."&remoteip=".$remoteip);

									$resultStatus = json_decode($url);

									if($resultStatus->success == true) {
										
									$_SESSION['last_request_count'] = 1;
									$_SESSION['last_request_count_bf'] = 1;
									$_SESSION['last_session_request_psp'] = 1;
									$_SESSION['last_session_request_hg'] = 1;
							
									 $displayError = "<div class='alertSuccess'>". __('Success! Please wait...', 'ua-protection-lang' ) ."</div>";
									 $displayError .= "<meta http-equiv='refresh' content='2'>";
									
									} else {
									  $displayError = "<div class='alert'>" . __('Please verify reCAPTCHA.', 'ua-protection-lang' ) ."</div>";
									} 
								} else {
									$displayError = "<div class='alert'>" . __('Please enable JavaScript and verify reCAPTCHA!', 'ua-protection-lang' ) ."</div>";
								} 
						  }
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
							
							$echFirewallPage = '<!DOCTYPE html><html>
								<head>
								<meta charset="UTF-8">
								<meta name="viewport" content="width=device-width, initial-scale=1">
								<title>' . __("Please verify reCAPTCHA!", "ua-protection-lang" ) . '</title>
								<script src="https://www.google.com/recaptcha/api.js"></script>
								<style>input[type=submit] {
									padding:5px 15px; 
									margin:20px 0 0;
									background:#34495e; 
									color:#fff;
									border:0 none;
									cursor:pointer;
									-webkit-border-radius: 5px;
									border-radius: 5px; 
								}
								.centerFirewall { margin:1em;background-color:#ecf0f1; border: 1px solid #e5e5e5;padding:3em 2em;text-align:center; }
								.g-recaptcha{  display: table;margin: 0 auto;}
								.alert {display:block;margin:1em;padding:1em;background-color:#e74c3c;color:#fff;}.alertSuccess {display:block;margin:1em;padding:1em;background-color:#2ecc71;color:#fff;}</style>
								</head>
									<body>
								'. $displayError .'
								<div class="centerFirewall">
									<form action="" method="post">
									<h1>' . __("Please complete the security check.", "ua-protection-lang" ) .'</h1>
									<div class="g-recaptcha" data-sitekey="'. get_option("wpuf_recaptcha_sitekey") .'"></div>
									<input id="submit" type="submit" name="submit" value="' . __("Submit", "ua-protection-lang" ) .'">
									</form>
								</div>
								<div class="centerFirewall">
									<p>' . __("<strong>Information:</strong> Our systems have detected unrealistic and potential spam information from your browser. This page checks to see if it is really you sending the requests, and not a robot.", "ua-protection-lang" ) .'</p>
									<p><strong>' . __("Your IP Address:", "ua-protection-lang" ) .'</strong> '. $ip .'</p>
									<p><strong>' . __("Your User Agent:", "ua-protection-lang" ) .'</strong> '. $get_useragent .'</p>
									<p><strong>' . __("Time:", "ua-protection-lang" ) .'</strong> '.$time.'</p>
								</div>
									</body>
								</html>';
								
								$echFirewallPage_Min = preg_replace("/\s+/", " ", $echFirewallPage);
								$echFirewallPage_Clear = trim($echFirewallPage_Min);
								die($echFirewallPage_Clear);

					}

				}
			}
		}
		
	}
}

// Content Protection
if( get_option("wpuf_content_security")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
	
		//Disable for Admins
		include(ABSPATH . "wp-includes/pluggable.php"); 
		if ( !( current_user_can('administrator') )) {
			
			//Content Protection Function
			function wpuf_content_security_func () {
				wp_register_script("wpuf-content-sec", WPUF_URL."functions/assets/js/content-protection.js", null, null, true );
				wp_enqueue_script("wpuf-content-sec");
			}
			
			function wpuf_content_security_css_func () {
				wp_enqueue_style("wpuf-content-default", WPUF_URL."functions/assets/css/content-protection.css");
				wp_enqueue_style("wpuf-content-printing", WPUF_URL."functions/assets/css/content-protection-disable-printing.css", array(), false, 'print' );
			}
			
			add_filter( 'wp_footer', 'wpuf_content_security_func');
			add_filter( 'wp_print_styles', 'wpuf_content_security_css_func');
			
		}
	}
}

//Proxy Security - Low
if( get_option("wpuf_proxy_protection")  == 1 || get_option("wpuf_proxy_protection") == 2 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
		$useragentCheck = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (strpos(strtolower($useragentCheck), "googlebot") === false || strpos(strtolower($useragentCheck), "bingbot") === false || strpos(strtolower($useragentCheck), "yahoo! slurp") === false || strpos(strtolower($useragentCheck), "yandexbot/3.0") === false) {
			
			include(ABSPATH . "wp-includes/pluggable.php"); 
			if ( !(current_user_can('administrator')) ) {
				
				$wpuf_proxy_header = array(
					'HTTP_VIA',
					'HTTP_X_FORWARDED_FOR',
					'HTTP_FORWARDED_FOR',
					'HTTP_X_FORWARDED',
					'HTTP_FORWARDED',
					'HTTP_CLIENT_IP',
					'HTTP_FORWARDED_FOR_IP',
					'VIA',
					'X_FORWARDED_FOR',
					'FORWARDED_FOR',
					'X_FORWARDED',
					'FORWARDED',
					'CLIENT_IP',
					'FORWARDED_FOR_IP',
					'HTTP_PROXY_CONNECTION'
				);
				
				foreach ($wpuf_proxy_header as $proxy_alert) {
					if (isset($_SERVER[$proxy_alert])) {
						
						header('HTTP/1.1 503 Service Unavailable');
						header('Status: 503 Service Unavailable');
						header('Connection: Close');
						exit;
					}
				}
			}
		}
	}
}

//Proxy Security - High
if( get_option("wpuf_proxy_protection")  == 2 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
		include(ABSPATH . "wp-includes/pluggable.php"); 
		if ( !(current_user_can('administrator')) ) {
			
			//Start Proxy Security
			function wpuf_proxy_test_func () {
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
				
				//Proxy Check
				$url = 'http://www.shroomery.org/ythan/proxycheck.php?ip=' . $ip . '';
				$ch  = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
				curl_setopt($ch, CURLOPT_USERAGENT, $get_useragent);
				curl_setopt($ch, CURLOPT_REFERER, "https://google.com");
				$proxy_yn = curl_exec($ch);
				curl_close($ch);
				
				//If the user using a Proxy:
				if ($proxy_yn =="Y") {
					
				//START MAIL Notify
				if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_proxy")  == 1 ) {
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
					
					//URL
					$spamUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					
					$message = __("Proxy detected and blocked:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'URL: '. $spamUrl ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
						
					$email = wp_mail(
						get_option("wpuf_mail_notify"),
						
						trim("Proxy Alert - ". get_option("blogname")),
						
						stripslashes( trim($message) ),
						
						"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
					);
					
				}
				//END Mail Notify
				
				header('HTTP/1.1 503 Service Unavailable');
				header('Status: 503 Service Unavailable');
				header('Connection: Close');
				exit;
					
				}
			}
			
			add_action( 'init', 'wpuf_proxy_test_func' );
		}
	}
}

//Bad User Agents
if( get_option("wpuf_badbot_protection")  == 1 ) {
	
	//Kick bad bots
	function wpuf_badbot_alarm() {
		
		$bad_bots_list = array( "Abonti", "aggregator", "AhrefsBot", "almaden", "Anarchie", "ASPSeek", "asterias", "autoemailspider", "Bandit", "BDCbot", "BackWeb", "BatchFTP", "BlackWidow", "BLEXBot", "Bolt", "Buddy", "BuiltBotTough", "Bullseye", "bumblebee", "BunnySlippers", "ca-crawler", "CazoodleBot", "CCBot", "Cegbfeieh", "CheeseBot", "CherryPicker", "ChinaClaw", "CICC", "Collector", "Copier", "CopyRightCheck", "cosmos", "Crescent", "Custo", "DIIbot", "discobot", "DittoSpyder", "DOC", "DotBot", "Download Ninja", "Drip", "DSurf", "EasouSpider", "eCatch", "ecxi", "EmailCollector", "EmailSiphon", "EmailWolf", "EroCrawler", "Exabot", "EirGrabber", "ExtractorPro", "EyeNetIE", "Fasterfox", "FeedBooster", "FlashGet", "Foobot", "FrontPage", "Genieo", "GetRight", "GetSmart", "GetWeb!", "gigabaz", "Go!Zilla", "Go-Ahead-Got-It", "gotit", "Grabber", "GrabNet", "Grafula", "grub-client", "Harvest", "hloader", "httplib", "HMView", "HTTrack", "httpdown", "humanlinks", "IDBot", "id-search", "ieautodiscovery", "InfoNaviRobot", "InterGET", "InternetLinkagent", "IstellaBot", "InternetSeer", "Iria", "IRLbot", "JennyBot", "JetCar", "JustView", "k2spider", "Kenjin Spider", "Keyword Density", "larbin", "LeechFTP", "LexiBot", "lftp", "libWeb", "libwww-perl", "likse", "Link*Sleuth", "LinkextractorPro", "linko", "LinkScan", "LinkWalker", "LNSpiderguy", "lwp-trivial", "Mag-Net", "magpie", "Mata Hari", "MaxPointCrawler", "MegaIndex", "Memo", "MFC_Tear_Sample", "Microsoft URL Control", "MIDown", "MIIxpc", "Mippin", "Missigua Locator", "Mister PiX", "MJ12bot", "moget", "MSIECrawler", "Navroad", "NearSite", "NetAnts", "NetMechanic", "NetSpider", "NICErsPRO", "Niki-Bot", "Ninja", "NPBot", "Nutch", "Octopus", "Offline Explorer", "Openfind data gathere", "Openfind", "PageGrabber", "panscient.com", "pavuk", "pcBrowser", "PeoplePal", "PHP5.{", "PHPCrawl", "PingALink", "PleaseCrawl", "Pockey", "ProPowerBot", "ProWebWalker", "psbot", "Pump", "Python-urllib", "QueryN Metasearch", "QRVA", "Reaper", "Recorder", "ReGet", "RepoMonkey", "Rippers", "RMA", "SBIder", "Scooter", "Seeker", "SemrushBot", "SeznamBot", "Siphon", "SISTRIX", "sitecheck.Internetseer.com", "SiteSnagger", "SlySearch", "SmartDownload", "Snake", "SnapPreviewBot", "SpaceBison", "Sogou", "SpankBot", "spanner", "spbot", "Spinn3r", "sproose", "Steeler", "Stripper", "Sucker", "SuperBot", "SuperHTTP", "suzuran", "Szukacz", "tAkeOut", "Teleport", "TeleportPro", "Telesoft", "The Intraformant", "TheNomad", "TightTwatBot", "Titan", "toCrawlUrlDispatcher", "True_Robot", "turingos", "TurnitinBot", "UbiCrawler", "UnisterBot", "URLSpiderPro", "URLy Warning", "Vacuum", "VCI WebViewer VCI WebViewer", "VoidEYE", "webalta", "WebAuto", "Win32", "VCI", "WBSearchBot", "Web Downloader", "Web Image Collector", "WebBandit", "WebCollage", "WebCopier", "WebEMailExtrac", "WebEnhancer", "WebFetch", "WebGo", "WebHook", "WebLeacher", "WebmasterWorldForumBot", "WebMiner", "WebMirror", "WebReaper", "WebSauger", "Website Quester", "Webster Pro", "WebStripper", "WebZip", "Whacker", "Widow", "Wotbox", "Wget", "wsr-agent", "WWW-Collector-E", "WWW-Mechanize", "WWWOFFLE", "x-Tractor", "Xaldon", "Xenu", "Zao", "zermelo", "Zeus", "ZyBORG", "coccoc", "Incutio", "lmspider", "memoryBot", "serf", "Unknown", "uptime files", "craftbot", "DISCo", "Download Demon", "Express WebPictures", "Indy Library", "NetZIP", "Vampire", "Offline", "RealDownload", "Download", "Surfbot", "WebWhacker", "eXtractor", "WebSpider", "archiverloader", "casper", "clshttp", "cmswor", "curl", "diavol", "email", "extract", "flicky", "grab", "kmccrew", "miner", "nikto", "planetwork", "pycurl", "python", "scan", "skygrid", "winhttp", "Scanner", "DigExt", "MJ12", "majestic12", "80legs", "Semrush", "Ezooms", "Ahrefs", "%0A", "%0D", "%27", "%3C", "%3E", "%00", "!susie", "_irc", "_works", "+select+", "+union+", "&lt;?", "3gse", "4all", "4anything", "a1 site", "a_browser", "abac", "abach", "abby", "aberja", "abilon", "abont", "abot", "aboutoil", "accept", "access", "accoo", "accoon", "aceftp", "acme", "active", "address", "adopt", "adress", "advisor", "agent", "ahead", "aihit", "aipbot", "alarm", "albert", "alek", "alexa toolbar", "alltop", "alma", "alot", "alpha", "america online browser", "amfi", "amfibi", "andit", "anon", "ansearch", "answer", "answerbus", "answerchase", "antivirx", "apollo", "appie", "arach", "arian", "asps", "aster", "atari", "atlocal", "atom", "atrax", "atrop", "attrib", "autoh", "autohot", "av fetch", "avsearch", "axod", "axon", "baboom", "baby", "back", "baid", "bali", "barry", "basichttp", "batch", "bdfetch", "beat", "beaut", "become", "bee", "beij", "betabot", "biglotron", "bilgi", "binlar", "bison", "bitacle", "bitly", "blaiz", "blitz", "blogl", "blogscope", "blogzice", "bloob", "blow", "bond", "bord", "boris", "bost", "bot.ara", "botje", "botw", "bpimage", "brand", "brok", "broth", "browseabit", "browsex", "bruin", "bsalsa", "bsdseek", "built", "bulls", "bumble", "bunny", "busca", "busi", "buy", "bwh3", "cafek", "cafi", "camel", "cand", "captu", "catch", "ccubee", "cd34", "ceg", "cfnetwork", "cgichk", "cha0s", "chang", "chaos", "char", "char(", "chase x", "check_http", "checker", "checkonly", "checkpriv", "chek", "chill", "chttpclient", "cipinet", "cisco", "cita", "citeseer", "clam", "claria", "claw", "cloak", "clush", "coast", "code.com", "cogent", "coldfusion", "coll", "collect", "comb", "combine", "commentreader", "common", "comodo", "compan", "conc", "conduc", "contact", "control", "contype", "conv", "cool", "copi", "copy", "coral", "corn", "costa", "cowbot", "cr4nk", "craft", "cralwer", "crank", "crap", "crawler0", "crazy", "cres", "cs-cz", "cshttp", "cuill", "curry", "cute", "cz3", "czx", "daily", "daobot", "dark", "darwin", "data", "daten", "dcbot", "dcs", "dds explorer", "deep", "deps", "detect", "diam", "dillo", "ding", "disc", "disp", "ditto", "dlc", "doco", "drec", "dsdl", "dsok", "dts", "duck", "dumb", "eag", "earn", "earthcom", "easydl", "ebin", "echo", "edco", "egoto", "elnsb5", "emer", "empas", "encyclo", "enfi", "enhan", "enterprise_search", "envolk", "erck", "erocr", "eventax", "evere", "evil", "ewh", "exac", "exploit", "expre", "extra", "eyen", "fang", "fast", "fastbug", "faxo", "fdse", "feed24", "feeddisc", "feedfinder", "feedhub", "fetch", "filan", "fileboo", "fimap", "find", "firebat", "firedownload", "firefox0", "firs", "flam", "flash", "flexum", "flip", "fly", "focus", "fooky", "forum", "forv", "fost", "foto", "foun", "fount", "foxy1;", "free", "friend", "fuck", "fuer", "futile", "fyber", "gais", "galbot", "gbpl", "geni", "geo", "geona", "geth", "getr", "getw", "ggl", "gira", "gluc", "gnome", "goforit", "goldfire", "gonzo", "google wireless", "gosearch", "got-it", "gozilla", "graf", "greg", "grub", "grup", "gsa-cra", "gsearch", "gt::www", "guidebot", "guruji", "gyps", "haha", "hailo", "harv", "hash", "hatena", "hax", "head", "helm", "hgre", "hippo", "hmse", "holm", "holy", "hotbar", "hpprint", "hrefs", "httpclient", "httpconnect", "human", "huron", "hverify", "hybrid", "hyper", "iaskspi", "ibm evv", "iccra", "ichiro", "icopy", "ics)", "ie5.0", "ieauto", "iempt", "iexplore.exe", "ilium", "ilse", "iltrov", "indexer", "indy", "ineturl", "infonav", "innerpr", "inspect", "insuran", "intellig", "internet_explorer", "internetx", "intraf", "ip2", "ipsel", "isc_sys", "isilo", "isrccrawler", "isspi", "jady", "jaka", "jam", "jenn", "jet", "jiro", "jobo", "joc", "jupit", "just", "jyx", "jyxo", "kash", "kazo", "kbee", "kenjin", "kernel", "keywo", "kfsw", "kkma", "kmc", "know", "kosmix", "krae", "krug", "ksibot", "ktxn", "kum", "labs", "lanshan", "lapo", "leech", "lets", "lexi", "lexxe", "libby", "libcrawl", "libcurl", "libfetch", "linc", "lingue", "linkcheck", "linklint", "linkman", "lint", "list", "litefeeds", "livedoor", "livejournal", "liveup", "lmq", "loader", "locu", "london", "lone", "loop", "lork", "lth_", "lwp", "mac_f", "magi", "magp", "mail.ru", "main", "majest", "mam", "mama", "marketwire", "masc", "mass", "mata", "mcbot", "mecha", "mechanize", "metadata", "metalogger", "metaspin", "metauri", "mete", "mib2.2", "microsoft.url", "microsoft_internet_explorer", "mido", "miggi", "miix", "mindjet", "mindman", "mips", "mira", "mire", "miss", "mist", "mizz", "mlbot", "mlm", "mnog", "moge", "moje", "mooz", "more", "mouse", "mozdex", "mvi", "msie6xpv1", "msnbot-media", "msnbot-products", "msnptc", "msproxy", "msrbot", "musc", "mvac", "mwm", "my_age", "myapp", "mydog", "myeng", "myie2", "mysearch", "myurl", "name", "naver", "navr", "near", "netcach", "netcrawl", "netfront", "netinfo", "netmech", "netsp", "netx", "netz", "neural", "neut", "newsbreak", "newsgatorinbox", "newsrob", "newt", "next", "ng-s", "ng2", "nice", "nimb", "ninte", "nog", "noko", "nomad", "norb", "nuse", "nutex", "nwsp", "obje", "ocel", "octo", "odi3", "oegp", "offby", "omea", "omg", "omhttp", "onfo", "onyx", "openf", "openssl", "openu", "orac", "orbit", "oreg", "osis", "our", "outf", "owl", "p3p_", "page2rss", "pagefet", "pansci", "parser", "patw", "pavu", "pb2pb", "pcbrow", "pear", "peer", "pepe", "perfect", "perl", "petit", "phoenix0.", "phras", "picalo", "piff", "pig", "pingd", "pipe", "pirs", "plag", "planet", "plant", "platform", "playstation", "plesk", "pluck", "plukkie", "poe-com", "poirot", "pomp", "post", "postrank", "powerset", "preload", "privoxy", "probe", "program_shareware", "protect", "protocol", "prowl", "proxie", "pubsub", "pulse", "punit", "purebot", "purity", "pyq", "pyth", "query", "quest", "qweer", "radian", "rambler", "ramp", "rapid", "rawdog", "rawgrunt", "reap", "reeder", "refresh", "relevare", "repo", "requ", "request", "rese", "retrieve", "roboz", "rocket", "rogue", "rpt-http", "rsscache", "ruby", "ruff", "rufus", "rv:0.9.7)", "salt", "sample", "sauger", "savvy", "sbcyds", "sblog", "sbp", "scagent", "scej_", "sched", "schizo", "schlong", "schmo", "scorp", "scott", "scout", "scrawl", "screen", "screenshot", "script", "seamonkey", "search17", "searchbot", "searchme", "sega", "semto", "sensis", "seop", "seopro", "sept", "sezn", "seznam", "share", "sharp", "shaz", "shell", "shelo", "sherl", "shim", "shopwiki", "silurian", "simple", "simplepie", "siph", "sitekiosk", "sitescan", "sitevigil", "sitex", "skam", "skimp", "sledink", "slide", "sly", "smag", "smurf", "snag", "snapbot", "snif", "snip", "snoop", "sock", "socsci", "sohu", "solr", "some", "soso", "spad", "span", "sphere", "spin", "spurl", "sputnik", "spyder", "squi", "sqwid", "sqworm", "ssm_ag", "stack", "stamp", "statbot", "state", "steel", "stilo", "strateg", "stress", "strip", "style", "subot", "such", "suck", "sume", "sunos 5.7", "sunrise", "superbro", "supervi", "surf4me", "survey", "susi", "suza", "suzu", "sweep", "swish", "sygol", "synapse", "sync2it", "systems", "tagger", "tagoo", "tagyu", "take", "talkro", "tamu", "tandem", "tarantula", "tbot", "tcf", "tcs1", "teamsoft", "tecomi", "teesoft", "tencent", "terrawiz", "test", "texnut", "thomas", "tiehttp", "timebot", "timely", "tipp", "tiscali", "tmcrawler", "tmhtload", "tocrawl", "todobr", "tongco", "toolbar; (r1", "topic", "topyx", "torrent", "track", "translate", "traveler", "treeview", "tricus", "trivia", "trivial", "true", "tunnel", "turing", "turnitin", "tutorgig", "twat", "tweak", "twice", "tygo", "ubee", "uchoo", "ultraseek", "unavail", "unf", "universal", "upg1", "urlbase", "urllib", "urly", "user-agent:", "useragent", "usyd", "vagabo", "valet", "vamp", "veri~li", "versus", "vikspi", "virtual", "visual", "void", "voyager", "vsyn", "w0000t", "w3search", "walhello", "walker", "wand", "waol", "watch", "wavefire", "wbdbot", "weather", "web2mal", "web.ima", "webbot", "webcat", "webcor", "webcorp", "webcrawl", "webdat", "webdup", "webind", "webis", "webitpr", "weblea", "webmin", "webmoney", "webp", "webql", "webrobot", "webster", "websurf", "webtre", "webvac", "wells", "wep_s", "whiz", "win67", "windows-rss", "winht", "winodws", "wish", "wizz", "worio", "works", "world", "worth", "wwwc", "wwwo", "wwwster", "xirq", "y!tunnel", "yacy", "yahoo-mmaudvid", "yahooseeker", "yahooysmcm", "yamm", "yang", "yoono", "yori", "yotta", "yplus ", "ytunnel", "zade", "zagre", "zeal", "zebot", "zerx", "zhuaxia", "zipcode", "zixy", "zmao", "zmeu", "zune", "backdoorbot", "black hole", "blowfish", "botalot", "cherrypicker", "crescent internet toolpak http ole control", "linkscan unix", "mozilla4.0 (compatible; bullseye; windows 95)", "repomonkey bait &amp; tacklev1", "vci webviewer vci webviewer win32", "xenu's", "xenu's link sleuth", "zeus webster pro", "8484_Boston_Project", "#[Ww]eb[Bb]andit", "Abacho", "acontbot", "AdoSpeaker", "ah-ha", "AIBOT", "#almaden", "Amfibibot", "Arachmo", "Arameda", "Arellis", "Argus", "attach", "baiduspider", "BecomeBot", "BigCliqueBOT", "Bimbot", "boitho.com-dc", "Bot mailto:craftbot@yahoo.com", "BruinBot", "btbot", "CCGCrawl", "CipinetBot", "citenikbot", "ContextAd Bot", "contextadbot", "ConveraCrawler", "ConveraMultiMediaCrawler", "CostaCider", "CrawlConvera", "CrawlWave", "#Crescent", "CXL-FatAssANT", "DataCha0s", "DataFountains", "Deepindex", "devoll.roswellspringcatalog.info/spring-fashion-2003.html8/18/2006", "DiamondBot", "Digger", "DISCo Pump", "DM-Search", "Download Wonder", "Downloader", "Drecombot", "DTAagent", "EnfinBot", "Eule-Robot", "EuripBot", "Exabot-Images", "fantomas", "Favcollector", "Faxobot", "FDM_2.x", "FileHound", "Firefox_1.0.6_kasparek", "Firefox_kastaneta", "First_Browse_of_COnn", "fluffy", "Franklin_Locator", "FyberSpider", "Gaisbot", "GalaxyBot", "gazz", "GenericBot-ax", "genevabot", "GeoBot", "Girafabot", "GOFORITBOT", "GornKer", "GroschoBot", "gsa-crawler", "HappyFunBot", "Healthbot", "holmes", "HooWWWer", "Hotzonu", "htdig", "Html_Link_Validator_", "http_sample", "HttpProxy", "httpunit", "IconSurf", "Iltrovatore-Setaccio", "Image Stripper", "Image Sucker", "#Indy Library", "InfociousBot", "INGRID", "InnerpriseBot", "Internet Ninja", "InternetSeer.com", "intraVnews", "IOneSearch.bot", "ISC_Systems_iRc_Search", "Jakarta_Commons-HttpClient", "Jayde Crawler", "JetBot", "JOC Web Spider", "KakleBot", "Kyluka", "lanshanbot", "LapozzBot", "Link_Valet_Online", "LinkAlarm", "LocalcomBot", "LWP::Simple", "Mac_Finder", "Mackster", "Magnet", "Mass Downloader", "Matrix", "Metaspinner", "Microsoft_URL_Control", "MIDown tool", "Mirago", "Mirror", "Missigua_Locator", "Mnogosearch", "MonkeyCrawl", "Mozilla.*NEWT", "Mozzilla", "MVAClient", "My_WinHTTP_Connection", "NaverBot", "NavissoBot", "Net Vampire", "NetMind-Minder", "NetMonitor", "Networking4all", "Newsgroupreporter_LinkCheck", "NextGenSearchBot", "nicebot", "NimbleCrawler", "NLCrawler", "noxtrumbot", "NuSearch Spider", "NutchCVS", "ObjectsSearch", "oBot", "Ocelli", "Octora_Beta", "Offline Navigator", "OmniExplorer_Bot", "Omnipelagos", "online link validator", "Openbot", "Orbiter", "OutfoxBot", "page_verifier", "PageBitesHyperBot", "Pajaczek", "Papa Foto", "Patwebbot", "PEAR_HTTP_Request_class", "PEERbot", "PHP_version_tracker", "PhpDig", "pipeLiner", "POE-Component-Client-HTTP", "polybot", "Pompos", "Poodle_predictor", "Pooodle_predictor", "Popdexter", "Port_Huron_Labs", "process", "psbot test for robots.txt", "psycheclone", "PyQuery", "QweeryBot", "RAMPyBot", "Random", "Ranking-Manager", "REL_Link_Checker_Lite", "robschecker", "RRG", "RufusBot", "SandCrawler", "SANSARN", "schibstedsokbot", "#scooter", "Screw-Ball", "Scrubby", "Search-10", "search.ch", "Searchmee!", "SearchSpider", "Seekbot", "Sensis Web Crawler", "Sensis.com.au Web Crawler", "Shim+Bot", "ShunixBot", "shybunnie-engine", "SideWinder", "silk", "SiteSpider", "#SlySearch test robots.txt", "sna-", "Snappy", "Snoopy", "sohu-search", "Speed-Meter", "SpeedySpider", "Spinne", "SpokeSpider", "Squid-Prefetch", "SquidClamAV_Redirector", "SquigglebotBot", "StackRambler", "sureseeker", "SurveyBot", "SygolBot", "SynoBot", "Teleport Pro", "TerrawizBot", "ThisIsOurYear_Linkchecker", "thumbshots-de-Bot", "Tkensaku", "topicblogs", "TridentSpider", "troovziBot", "TutorGigBot", "#ua", "unchaos_crawler", "Updated", "URL Spider Pro", "URL Spider SQL", "Vagabondo", "vBSEO_", "VoilaBot", "W3CRobot", "Web Sucker", "Web_Downloader", "webcrawl.net", "WebDataCentreBot", "WebEMailExtrac.*", "WebFindBot", "WebGather", "WebGo IS", "WebIndexer", "Webnavigator", "webPluck", "Website", "Website eXtractor", "Wells_Search_II", "WEP_Search", "WhizBang", "WISEbot", "WWWeasel", "Xaldon WebSpider", "Xenu_Link_Sleuth", "Xombot", "XunBot", "yacybot", "YadowsCrawler", "Yeti", "YodaoBot", "YottaShopping_Bot", "Zatka", "Zealbot", "Zeus.*Webster", "#Zeus_", "ZipppBot", "Alexibot", "Aqua_Products", "b2w", "Bookmark search tool", "Copernic", "dumbot", "FairAd Client", "Flaming AttackBot", "Hatena Antenna", "Iron33", "LinkScan/8.1a Unix", "LinkScan/8.1a Unix User-agent: Kenjin Spider", "Morfeus", "Mozilla/4.0 (compatible; BullsEye; Windows 95)", "Oracle Ultra Search", "PerMan", "Radiation Retriever", "RepoMonkey Bait & Tackle", "searchpreview", "sootle", "toCrawl/UrlDispatcher", "URL Control", "URL_Spider_Pro", "WebmasterWorld Extractor", "Zeus 32297 Webster Pro V2.9 Win32", "Zeus Link Scout", "%", "<?", "1,1,1,", "2icommerce", "ActiveTouristBot", "adressendeutschland", "ADSARobot", "AESOP_com_SpiderMan", "Alligator", "AllSubmitter", "aktuelles", "Akregat", "amzn_assoc", "AnotherBot", "Apexoo", "ASPSe", "ASSORT", "ATHENS", "AtHome", "Atomic_Email_Hunter", "Atomz", "^attach", "autohttp", "BackStreet", "Badass", "BenchMark", "berts", "bew", "big.brother", "Bigfoot", "Biz360", "Black", "Black.Hole", "bladder.fusion", "Blog.Checker", "BlogPeople", "Blogshares.Spiders", "Bloodhound", "bmclient", "Board", "BOI", "boitho", "Bookmark.search.tool", "Boston.Project", "BotRightHere", "Bot.mailto:craftbot@yahoo.com", "botpaidtoclick", "brandwatch", "BravoBrian", "Bropwers", "Browsezilla", "c-spider", "char(32,35)", "charlotte", "Click.Bot", "clipping", "core-project", "cyberalert", "^DA$", "Daum", "Deweb", "Digimarc", "digout4uagent", "DnloadMage", "Doubanbot", "Download.Demon", "Download.Devil", "Download.Wonder", "DreamPassport", "DynaWeb", "e-collector", "EBM-APPLE", "EBrowse", "ecollector", "edgeio", "efp@gmx.net", "Email.Extractor", "EmailSearch", "ESurf", "Eval", "Exact", "EXPLOITER", "FairAd", "Fake", "fastlwspider", "FavOrg", "Favorites.Sweeper", "FDM_1", "FEZhead", "Firefox.2.0", "FlickBot", "flunky", "Foob", "Forex", "Franklin.Locator", "freefind", "FreshDownload", "FSurf", "Gamespy_Arcade", "Get", "Ginxbot", "glx.?v", "Go.Zilla", "Google.Wireless.Transcoder", "^gotit$", "Green.Research", "gvfs", "hack", "hhjhj@yahoo", "HomePageSearch", "HouxouCrawler", "http.generic", "HTTPGet", "HTTPRetriever", "IBM_Planetwide", "iGetter", "Image.Stripper", "Image.Sucker", "imagefetch", "iimds_monitor", "IncyWincy", "Industry.Program", "informant", "InfoTekies", "Ingelin", "InstallShield.DigitalWizard", "Insuran.", "Intelliseek", "Internet.Ninja", "Internet.x", "Irvine", "IUPUI.Research.Bot", "^Java", "java/", "Java(tm)", "JBH.agent", "Jenny", "JetB", "JetC", "jeteye", "Kapere", "KRetrieve", "ksoap", "KWebGet", "Lachesis", "leacher", "LeechGet", "leipzig.de", "libghttp", "libwhisker", "libwww-FM", "LightningDownload", "Link", "Link.Sleuth", "Linkie", "LINKS.ARoMATIZED", "linktiger", "lmcrawler", "looksmart", "lwp-request", "Mac.Finder", "Macintosh;.I;.PPC", "Mail.Sweeper", "MarcoPolo", "mark.blonin", "MarkWatch", "MaSagool", "Mass.Downloader", "mavi", "MCspider", "^Memo", "MEGAUPLOAD", "MetaProducts.Download.Express", "Microsoft.Data.Access", "Missauga", "Missigua.Locator", "Missouri.College.Browse", "mkdb", "MMMoCrawl", "Monster", "Monza.Browser", "Moreoverbot", "MOT-MPx220", "mothra/netscan", "MovableType", "Mozi!", "^Mozilla.*Indy", "^Mozilla.*NEWT", "^Mozilla*MSIECrawler", "Mp3Bot", "MS.FrontPage", "MS.?Search", "MSFrontPage", "multithreaddb", "MyFamilyBot", "MyGetRight", "NAMEPROTECT", "NASA.Search", "nationaldirectory", "netattache", "NetCarta", "Netcraft", "netprospector", "NetResearchServer", "Net.Vampire", "newLISP", "NEWT.ActiveX", "^NG", "NIPGCrawler", "Noga", "nogo", "Offline.Explorer", "Offline.Navigator", "OK.Mozilla", "Omni", "OpaL", "OpenTextSiteCrawler", "OrangeBot", "P3P", "PackRat", "PagmIEDownload", "Papa", "Pars", "PECL", "PersonaPilot", "Persuader", "PHP.vers", "PHPot", "Pige", "pigs", "^Ping", "playstarmusic", "Port.Huron", "Program.Shareware", "Progressive.Download", "prospector", "Provider.Protocol.Discover", "Prozilla", "PSurf", "^puf$", "PushSite", "PussyCat", "PuxaRapido", "QuepasaCreep", "Radiation", "RedCarpet", "RedKernel", "relevantnoise", "replacer", "Rover", "Rsync", "RTG30", ".ru)", "SAPO", "ScoutOut", "SearchExpress", "searchhippo", "searchterms", "Second.Street.Research", "Security.Kol", "Serious", "Shai", "Shiretoko", "SickleBot", "sitecheck", "SiteCrawler", "Site.Sniper", "SiteSucker", "Slurpy.Verifier", "So-net", "Spegla", "Sphider", "SpiderBot", "SpiderEngine", "SpiderView", "SQ.Webscanner", "Stamina", "Stanford", "studybot", "sun4m", "SurfWalker", "syncrisis", "TALWinHttpClient", "tarspider", "Tcs/1", "Templeton", "The.Intraformant", "TV33_Mercator", "Twisted.PageGetter", "UCmore", "UdmSearch", "UIowaCrawler", "UMBC", "UniversalFeedParser", "UtilMind", "URL.Control", "urldispatcher", "URLGetFile", "User-Agent", "vayala", "VB_", "Viewer", "visibilitygap", "vobsub", "vspider", "w:PACBHO60", "w3m", "WAPT", "web.by.mail", "Web.Data.Extractor", "Web.Downloader", "Web.Mole", "Web.Sucker", "Web2WAP", "WebaltBot", "WebCapture", "webcraft@bea", "Webclip", "WebCollector", "WebCopy", "WebDav", "webdevil", "webdownloader", "WebEMail", "Webinator", "WebFilter", "WebFountain", "Webmaster", "webmole", "webpic", "WebPin", "WebPix", "WebRipper", "Website.eXtractor", "Website.Quester", "WebSnake", "websucker", "webwalk", "WebWasher", "WebWeasel", "WEP.Search.00", "WeRelateBot", "Whack", "WhosTalking", "window.location", "Wildsoft.Surfer", "WinHttpRequest", "WinHTTrack", "Winnie.Poh", "wire", "wisenutbot", "WUMPUS", "Wweb", "WWW-Collector", "WWW.Mechanize", "www.ranks.nl", "^x$", "X12R1", "XGET", "Y!OASIS", "YaDirectBot", "ZBot", "Zyborg", "choppy", "g00g1e", "seekerspider", "siclab", "sqlmap", "turnit", "xxxyy", "youda", "finder", "acapbot", "semalt", "AITCSRobot", "Arachnophilia", "aspider", "AURESYS", "BackRub", "Big Brother", "BizBot", "BSpider", "linklooker", "SafetyNet Robot", "CACTVS Chemistry Spider", "EnigmaBot", "Checkbot" );
		
		foreach ($bad_bots_list as $kick_bad_bots) {
			if (stripos($_SERVER['HTTP_USER_AGENT'], $kick_bad_bots) !== false) {
			
				header('HTTP/1.1 503 Service Unavailable');
				header('Status: 503 Service Unavailable');
				header('Connection: Close');
				exit;
				
			}
		}
	}
	add_action('init', 'wpuf_badbot_alarm');
}

//SQL Injection Protection
if( get_option("wpuf_sql_protection")  == 1 ) {
	
	function wpuf_sql_protection_func() {
		//Call QUERY_STRING
		$query_string = $_SERVER['QUERY_STRING'];
		
		//SQL Injection Attack List
		$sqlinjlist = array( "union", "coockie", "concat", "alter", "exec", "shell", "wget", "**/", "/**", "0x3a", "null", "DR/**/OP/", "drop", "/*", "*/", "*", "--", ";", "||", "' #", "or 1=1", "'1'='1", "BUN", "S@BUN", "char", "OR%", "`", "[", "]", "<", ">", "++", "script", "1,1", "substring", "ascii", "sleep(", "insert", "between", "values", "truncate", "benchmark", "sql", "mysql", "%27", "%22", "(", ")", "<?", "<?php", "?>", "../", "/localhost", "127.0.0.1", "loopback", ":", "%0A", "%0D", "%3C", "%3E", "%00", "%2e%2e", "input_file", "execute", "mosconfig", "environ", "scanner", "path=.", "mod=.", "eval\(", "javascript:", "base64_", "boot.ini", "etc/passwd", "self/environ", "md5", "echo.*kae", "=%27$" );
		
		foreach ($sqlinjlist as $sqlinjfunc) {
			if (strlen($query_string) > 255 OR strpos(strtolower($query_string), strtolower($sqlinjfunc)) !== false) {
				header('HTTP/1.1 503 Service Unavailable');
				header('Status: 503 Service Unavailable');
				header('Connection: Close');
				
				//START MAIL Notify
				if( get_option("wpuf_mail_alarm") && get_option("wpuf_mail_alarm_hacker")  == 1 ) {
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
						
					//URL
					$spamUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					
					$message = __("SQL Injection detected and blocked:", "ua-protection-lang" ) . "\r\n\r\n" .'IP Address: '. $ip ."\r\n" . 'Date: ' . $time ."\r\n" . 'User Agent: '.$get_useragent ."\r\n" .'URL: '. $spamUrl ."\r\n\r\n" . __("If you want, you can block this IP address or User Agents from the WP Ultimate Firewall panel.", "ua-protection-lang" ) ."\r\n" . __("Your website is protected by WP Ultimate Firewall.", "ua-protection-lang" );
						
					$email = wp_mail(
						get_option("wpuf_mail_notify"),
						
						trim("Hacker Alert - ". get_option("blogname")),
						
						stripslashes( trim($message) ),
						
						"From:". trim(get_option("blogname"))." <".trim(get_option("admin_email")).">\r\nReply-To:".trim(get_option("admin_email"))
					);
					
				}
				//END Mail Notify
				exit;
			}
		}
	}
	
	add_action('init', 'wpuf_sql_protection_func');
}

//Fakebot Security
if( get_option("wpuf_fakebot_protection")  == 1 || get_option("wpuf_security_ban")  == 1 || get_option("wpuf_security_ban")  == 2 || get_option("wpuf_proxy_protection")  == 1 || get_option("wpuf_proxy_protection") == 2) {
	
	include(ABSPATH . "wp-includes/pluggable.php"); 
	if ( !(current_user_can('administrator') )) {
		
		function wpuf_fakebot_protection_func () {
			
			//*START  Call Functions *//	
			// Check IP
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			// Check User Agent
			$useragent = $_SERVER['HTTP_USER_AGENT'];
			
			// Check Host Name
			@$hostname = strtolower(gethostbyaddr($ip));
			//*END Call Functions *//
			
			//Fake Googlebot
			if (strpos(strtolower($useragent), "googlebot") !== false) {
				if (strpos($hostname, "googlebot.com") !== false OR strpos($hostname, "google.com") !== false) {
				} else {
					//block
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					
					exit;
				}
			}

			//Fake Bingbot Detection
			if (strpos(strtolower($useragent), "bingbot") !== false) {
				if (strpos($hostname, "search.msn.com") !== false) {
				} else {
					//block
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					
					exit;
					
				}
			}
			
			//Fake Yahoo slurp bot
			if (strpos(strtolower($useragent), "yahoo! slurp") !== false) {
				if (strpos($hostname, "yahoo.com") !== false OR strpos($hostname, "crawl.yahoo.net") OR strpos($hostname, "yandex.com") !== false) {
				} else {
					//block
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					
					exit;
				}
			}	
			
			//Fake Yandex bot
			if (strpos(strtolower($useragent), "yandexbot/3.0") !== false) {
				if (strpos($hostname, "yandex.com") !== false OR strpos($hostname, "yandex.com/bots") OR strpos($hostname, "yandex.ru") OR strpos($hostname, "yandex.net") OR strpos($hostname, "yandex.com.tr") !== false) {
				} else {
					//block
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					
					exit;
				}
			}

		}
		
		add_action( 'init', 'wpuf_fakebot_protection_func' );
	
	}
}

//Tor Protection
if( get_option("wpuf_tor_protection")  == 1 ) {
	
	function wpuf_tor_protection_func() {
		
		// Check IP
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$tor_dns_look = array(
			"tor.dan.me.uk",
			"tor.dnsbl.sectoor.de"
		);
		$get_tor_ip   = implode(".", array_reverse(explode(".", $ip)));
		
		foreach ($tor_dns_look as $torhost) {
			if (checkdnsrr($get_tor_ip . "." . $torhost . ".", "A")) {
				
				//block
				header('HTTP/1.1 403 Forbidden');
				header('Status: 403 Forbidden');
				header('Connection: Close');
				
				exit;

			}
		}
    }
	
	add_action( 'init', 'wpuf_tor_protection_func' );
}

//Disable File Edit
if( get_option("wpuf_disable_fileedit")  == 1 ) {
	
	function wpuf_disable_fileedit_func() {
      define('DISALLOW_FILE_EDIT', TRUE);
    }
	
    add_action('init','wpuf_disable_fileedit_func');
}

// Protect Login / Register / Lost Password Pages
if( get_option("wpuf_recaptcha_protection_lrl")  == 1 ) {
	if ( white_check_ip_address(get_option('get_white_list'))) {
		
		//ADD Recaptcha API JS
		function wpuf_lrg_recaptcha() {
			wp_register_script("wpuf_recaptcha_lg", "https://www.google.com/recaptcha/api.js", null, null, true );
			wp_enqueue_script("wpuf_recaptcha_lg");
		}
		add_action("login_enqueue_scripts", "wpuf_lrg_recaptcha");
		
		//Add Recaptcha Field
		function wpuf_recaptcha_add_register_field() { ?>
			<p>
				<label><div id="g-recaptcha" class="g-recaptcha" style="margin:10px 0;transform:scale(0.90);-webkit-transform:scale(0.90);transform-origin:0 0;-webkit-transform-origin:0 0;" data-sitekey="<?php echo get_option('wpuf_recaptcha_sitekey'); ?>"></div></label>
			</p>
		<?php }
		 
		function wpuf_lg_verify_captcha($captcha_data) {
			if (isset($_POST['g-recaptcha-response'])) {
				$get_recaptcha_secretkey = get_option('wpuf_recaptcha_secretkey');
				$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $get_recaptcha_secretkey ."&response=". $_POST['g-recaptcha-response']);
				$response = json_decode($response["body"], true);
				if (true == $response["success"]) {
					return $captcha_data;
				} else {
				
				$message =  __('Please verify reCAPTCHA.', 'ua-protection-lang' );
				$title = 'Please Verify reCAPTCHA';
				$args = array('response' => 200);
				wp_die( $message, $title, $args );
				exit(0);
			
				}
			} else {
				$message = __('Please enable JavaScript and verify reCAPTCHA', 'ua-protection-lang' );
				$title = 'Please enable JavaScript';
				$args = array('response' => 200);
				wp_die( $message, $title, $args );
				exit(0);
			}
		}
		
		if( get_option("wpuf_recaptcha_protection_lrl_registration")  == 1 ) {
		add_action( 'register_form', 'wpuf_recaptcha_add_register_field' );
		add_filter("register_post", "wpuf_lg_verify_captcha");
		}
		if( get_option("wpuf_recaptcha_protection_lrl_login")  == 1 ) {
		add_action( 'login_form', 'wpuf_recaptcha_add_register_field' );
		add_filter("wp_authenticate_user", "wpuf_lg_verify_captcha");
		}
		if( get_option("wpuf_recaptcha_protection_lrl_lpf")  == 1 ) {
		add_action( 'lostpassword_form', 'wpuf_recaptcha_add_register_field' );
		add_filter("lostpassword_post", "wpuf_lg_verify_captcha");
		}
	}
}