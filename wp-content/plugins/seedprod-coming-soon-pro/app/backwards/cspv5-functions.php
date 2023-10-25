<?php

	//Subscribe Callback
	add_action( 'wp_ajax_seed_cspv5_legacy_subscribe_callback', 'seed_cspv5_legacy_subscribe_callback' );
	add_action( 'wp_ajax_nopriv_seed_cspv5_legacy_subscribe_callback', 'seed_cspv5_legacy_subscribe_callback' );

	//ContactForm Callback
	add_action( 'wp_ajax_seed_cspv5_legacy_contactform_callback', 'seed_cspv5_legacy_contactform_callback' );
	add_action( 'wp_ajax_nopriv_seed_cspv5_legacy_contactform_callback', 'seed_cspv5_legacy_contactform_callback' );


function seedprod_pro_cspv5_remove_ngg_print_scripts() {
	if ( class_exists( 'C_Photocrati_Resource_Manager' ) ) {
		remove_all_actions( 'wp_print_footer_scripts', 1 );
	}
}


		/**
	 * Display the landing page
	 */
function seedprod_pro_cspv5_render_landing_page( $page_id ) {
	// Prevetn Plugins from caching
	// Disable caching plugins. This should take care of:
	//   - W3 Total Cache
	//   - WP Super Cache
	//   - ZenCache (Previously QuickCache)
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	if ( ! defined( 'DONOTCDN' ) ) {
		define( 'DONOTCDN', true );
	}

	if ( ! defined( 'DONOTCACHEDB' ) ) {
		define( 'DONOTCACHEDB', true );
	}

	if ( ! defined( 'DONOTMINIFY' ) ) {
		define( 'DONOTMINIFY', true );
	}

	if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
		define( 'DONOTCACHEOBJECT', true );
	}

	global  $seedprod_legacy_lp_path;

	$page_id = $seedprod_legacy_lp_path;

	// Get Page
	global $wpdb;
	$tablename = $wpdb->prefix . 'cspv5_pages';
	$sql       = "SELECT * FROM $tablename WHERE id= %d and deactivate = 0";
	$safe_sql  = $wpdb->prepare( $sql, $page_id );
	$page      = $wpdb->get_row( $safe_sql );
	if ( empty( $page ) ) {
		return false;
	}

	// Check for base64 encoding of settings
	if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
		$settings = unserialize( base64_decode( $page->settings ) );
	} else {
		$settings = unserialize( $page->settings );
	}

	// Check  for languages

		$lang_settings_name = 'seed_cspv5_' . $page_id . '_language';
		$lang_settings      = get_option( $lang_settings_name );
	if ( ! empty( $lang_settings ) ) {
		$lang_settings     = maybe_unserialize( $lang_settings );
		$lang_settings_all = $lang_settings;
		$langs             = array( '0' => $lang_settings['default_lang']['label'] );
		foreach ( $lang_settings as $k => $v ) {
			if ( substr( $k, 0, 5 ) === 'lang_' ) {
				$langs[ $k ] = $v['label'];
			}
		}
	}

		$lang_id = '';
	if ( ! empty( $_GET['lang'] ) ) {
		$lang_id = $_GET['lang'];
	}

		// if(isset($_GET['lang'])){
		//     var_dump($_GET);
		//     die();
		// }

		// Get lang settings
		$lang_settings_name = 'seed_cspv5_' . $page_id . '_language_' . $lang_id;
		$lang_settings      = get_option( $lang_settings_name );
	if ( ! empty( $lang_settings ) ) {
		$lang_settings = maybe_unserialize( $lang_settings );
	}

	if ( ! empty( $lang_id ) && ! empty( $lang_settings ) ) {
		$settings = array_merge( $settings, $lang_settings );
	}

		//If Referrer record it
	if ( isset( $_GET['ref'] ) ) {
		$id = intval( $_GET['ref'], 36 ) - 1000;

		global $wpdb;
		$tablename     = $wpdb->prefix . 'csp3_subscribers';
		$sql           = "UPDATE $tablename SET clicks = clicks + 1 WHERE id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $id );
		$update_result = $wpdb->get_var( $safe_sql );
	}

		// check if 3rd party plugins is enabled
	if ( ! empty( $settings['enable_wp_head_footer'] ) ) {
		add_action( 'wp_enqueue_scripts', 'seed_cspv5_legacy_deregister_frontend_theme_styles', PHP_INT_MAX );
	}

		header( 'HTTP/1.1 200 OK' );
		header( 'Cache-Control: max-age=0, private' );

		// render
		$upload_dir = wp_upload_dir();
	if ( is_multisite() ) {
		$path = $upload_dir['baseurl'] . '/seedprod/' . get_current_blog_id() . '/template-' . $page_id . '/index.php';
	} else {
		$path = $upload_dir['basedir'] . '/seedprod/template-' . $page_id . '/index.php';
	}

	if ( ! empty( $page->html ) ) {
		echo $page->html;
	} else {

		if ( file_exists( $path ) ) {
			require_once $path;
		} else {
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/template/index.php';
		}
	}

		exit();
}

	/**
	 * Display the coming soon page
	 */


function seedprod_pro_cspv5_render_comingsoon_page() {

	//var_dump('coming soon');

	// Setting
	$plugin_settings = seed_cspv5_legacy_get_settings();
	extract( $plugin_settings );

	// Page Info
	$page_id = 0;
	if ( isset( $_GET['seed_cspv5_preview'] ) ) {
		$page_id = $_GET['seed_cspv5_preview'];
	} else {
		//Get Coming Soon Page Id
		$page_id = get_option( 'seed_cspv5_coming_soon_page_id' );
	}

	// Get Page
	global $wpdb;
	$tablename = $wpdb->prefix . 'cspv5_pages';
	$sql       = "SELECT * FROM $tablename WHERE id= %d";
	$safe_sql  = $wpdb->prepare( $sql, $page_id );
	$page      = $wpdb->get_row( $safe_sql );

	// Check for base64 encoding of settings
	if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
		$settings = unserialize( base64_decode( $page->settings ) );
	} else {
		$settings = unserialize( $page->settings );
	}

	// Check  for languages

	$lang_settings_name = 'seed_cspv5_' . $page_id . '_language';
	$lang_settings      = get_option( $lang_settings_name );
	if ( ! empty( $lang_settings ) ) {
		$lang_settings     = maybe_unserialize( $lang_settings );
		$lang_settings_all = $lang_settings;
		$langs             = array( '0' => $lang_settings['default_lang']['label'] );
		foreach ( $lang_settings as $k => $v ) {
			if ( substr( $k, 0, 5 ) === 'lang_' ) {
				$langs[ $k ] = $v['label'];
			}
		}
	}

	$lang_id = '';
	if ( ! empty( $_GET['lang'] ) ) {
		$lang_id = $_GET['lang'];
	}

	// if(isset($_GET['lang'])){
	//     var_dump($_GET);
	//     die();
	// }

	// Get lang settings
	$lang_settings_name = 'seed_cspv5_' . $page_id . '_language_' . $lang_id;
	$lang_settings      = get_option( $lang_settings_name );
	if ( ! empty( $lang_settings ) ) {
		$lang_settings = maybe_unserialize( $lang_settings );
	}

	if ( ! empty( $lang_id ) && ! empty( $lang_settings ) ) {
		$settings = array_merge( $settings, $lang_settings );
	}

	// if(!isset($status)){
	//     $err =  new WP_Error('error', __("Please enter your settings.", 'seedprod-pro'));
	//     echo $err->get_error_message();
	//     exit();
	// }

	// Check if Preview
	$is_preview = false;
	if ( ( isset( $_GET['seed_cspv5_preview'] ) ) ) {
		//show_admin_bar( false );
		$is_preview = true;
	}

	// Die if preview and redirect mode
	if ( $status == '3' && $is_preview == true ) {
		$status = '1';
	}

	// Countdown Launch
	if ( $is_preview == false ) {
		if ( ! empty( $settings['countdown_date'] ) && ! empty( $settings['enable_countdown'] ) && ! empty( $settings['countdown_launch'] ) ) {
			$date      = new DateTime( $settings['countdown_date'], new DateTimeZone( $settings['countdown_timezone'] ) );
			$timestamp = $date->format( 'U' );
			// var_dump($timestamp);
			// var_dump(time());

			// Launch this biatch
			if ( $timestamp <= time() ) {
				// Email the admin the site has been launched
				$message = __( sprintf( '%s has been launched.', home_url() ), 'seedprod' );
				$result  = wp_mail( get_option( 'admin_email' ), __( sprintf( '%s has been launched.', home_url() ), 'seedprod' ), $message );

				$o = get_option( 'seed_cspv5_settings_content' );
				//var_dump($o);
				$o['status'] = 0;
				update_option( 'seed_cspv5_settings_content', $o );
				return false;
			}
		}
	}

	//If Referrer record it
	if ( isset( $_GET['ref'] ) ) {
		$id = intval( $_GET['ref'], 36 ) - 1000;

		global $wpdb;
		$tablename     = $wpdb->prefix . 'csp3_subscribers';
		$sql           = "UPDATE $tablename SET clicks = clicks + 1 WHERE id = %d";
		$safe_sql      = $wpdb->prepare( $sql, $id );
		$update_result = $wpdb->get_var( $safe_sql );
	}

	// Exit if feed and feedburner is enabled.
	if ( is_feed() && ! empty( $emaillist ) && $emaillist == 'feedburner' ) {
		return false;
	}

	//Bypass code
	if ( empty( $_GET['bypass'] ) ) {
		$_GET['bypass'] = false;
	}

	if ( empty( $alt_bypass ) ) {
		$alt_bypass = false;
	}

	if ( is_multisite() || $alt_bypass ) {

		// Multisite Clientview
		if ( empty( $_GET['bypass'] ) ) {
			$_GET['bypass'] = false;
		}

		if ( $is_preview == false ) {
			//Check for Client View
			if ( isset( $_COOKIE['wp-client-view'] ) && ( ( strtolower( basename( $_SERVER['REQUEST_URI'] ) ) == trim( strtolower( $client_view_url ) ) ) || ( strtolower( $_GET['bypass'] ) == trim( strtolower( $client_view_url ) ) ) ) && ! empty( $client_view_url ) ) {
				if ( ! empty( $_REQUEST['return'] ) ) {
					nocache_headers();
					header( 'Cache-Control: max-age=0, private' );
					header( 'Location: ' . urldecode( $_REQUEST['return'] ) );
					exit;
				} else {
					nocache_headers();
					header( 'Cache-Control: max-age=0, private' );
					header( 'Location: ' . home_url() . '?' . rand() );
					exit;
				}
			}

			// Don't show Coming Soon Page if client View is active
			$client_view_hash = md5( $client_view_url . get_current_blog_id() );
			if ( isset( $_COOKIE['wp-client-view'] ) && $_COOKIE['wp-client-view'] == $client_view_hash && ! empty( $client_view_url ) ) {
				nocache_headers();
				header( 'Cache-Control: max-age=0, private' );
				return false;
			} else {
				nocache_headers();
				header( 'Cache-Control: max-age=0, private' );
				setcookie( 'wp-client-view', '', time() - 3600 );
			}

			// If Client view is not empty and we are on the client view url set cookie.
			if ( ! empty( $client_view_url ) ) {
				if ( empty( $_GET['bypass'] ) ) {
					$_GET['bypass'] = '';
				}

				if ( ( strtolower( basename( $_SERVER['REQUEST_URI'] ) ) == trim( strtolower( $client_view_url ) ) ) || ( strtolower( $_GET['bypass'] ) == trim( strtolower( $client_view_url ) ) ) ) {
					if ( ! empty( $bypass_expires ) ) {
						$exipres_in = time() + ( 3600 * $bypass_expires );
					} else {
						$exipres_in = time() + 172800;
					}

					setcookie( 'wp-client-view', $client_view_hash, $exipres_in, COOKIEPATH, COOKIE_DOMAIN, false );

					if ( ! empty( $_REQUEST['return'] ) ) {
						nocache_headers();
						header( 'Cache-Control: max-age=0, private' );
						header( 'Location: ' . urldecode( $_REQUEST['return'] ) );
						exit;
					} else {
						nocache_headers();
						header( 'Cache-Control: max-age=0, private' );
						header( 'Location: ' . home_url() . '?' . rand() );
						exit;
					}
				}
			}
		}
	} else {

		// ClientView
		if ( ! empty( $client_view_url ) ) {
			if ( empty( $_GET['bypass'] ) ) {
				$_GET['bypass'] = '';
			}

			// If client view url is passed in log user in
			if ( ( strtolower( basename( $_SERVER['REQUEST_URI'] ) ) == trim( strtolower( $client_view_url ) ) ) || ( strtolower( $_GET['bypass'] ) == trim( strtolower( $client_view_url ) ) ) ) {
				if ( ! username_exists( 'seed_cspv5_clientview_' . $client_view_url ) ) {
					$user_id = wp_create_user( 'seed_cspv5_clientview_' . $client_view_url, wp_generate_password() );
					$user    = new WP_User( $user_id );
					$user->set_role( 'none' );
				}

				if ( ! empty( $bypass_expires ) ) {
					global $seed_cspv5_bypass_expires;
					$seed_cspv5_bypass_expires = ( 3600 * $bypass_expires );
				}

				$client_view_hash = md5( $client_view_url . get_current_blog_id() );
				setcookie( 'wp-client-view', $client_view_hash, 0, COOKIEPATH, COOKIE_DOMAIN, false );

				add_filter( 'auth_cookie_expiration', 'seed_cspv5_legacy_change_wp_cookie_logout' );

				// Log user in auto
				$username = 'seed_cspv5_clientview_' . $client_view_url;
				if ( ! is_user_logged_in() ) {
					$user    = get_user_by( 'login', $username );
					$user_id = $user->ID;
					wp_set_current_user( $user_id, $username );
					wp_set_auth_cookie( $user_id );
					do_action( 'wp_login', $username, $user );
					update_user_meta( $user_id, 'show_admin_bar_front', false );
				}

				if ( ! empty( $_REQUEST['return'] ) ) {
					nocache_headers();
					header( 'Cache-Control: max-age=0, private' );
					header( 'Location: ' . urldecode( $_REQUEST['return'] ) );
					exit;
				} else {
					nocache_headers();
					header( 'Cache-Control: max-age=0, private' );
					header( 'Location: ' . home_url() . '?' . rand() );
					exit;
				}
			}
		}
	}

	// Check for excluded IP's
	if ( $is_preview == false ) {
		if ( ! empty( $ip_access ) ) {
			$ip          = seed_cspv5_legacy_get_ip();
			$exclude_ips = explode( "\r\n", $ip_access );
			if ( is_array( $exclude_ips ) && in_array( $ip, $exclude_ips ) ) {
				return false;
			}
		}
	}

	if ( $is_preview == false ) {
		if ( ! empty( $include_exclude_options ) && $include_exclude_options == '2' ) {
			if ( substr( $include_url_pattern, 0, 3 ) != '>>>' ) {

				// Check for included pages
				if ( ! empty( $include_url_pattern ) ) {
					//$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
					// TODO lok for when WordPress is in sub folder
					$request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
					$url         = rtrim( ltrim( $request_uri[0], '/' ), '/' );

					$r = array_intersect( explode( '/', $url ), explode( '/', home_url() ) );

					$url = str_replace( $r, '', $url );

					$url = str_replace( '/', '', $url );
					//var_dump($url);

					$include_urls = explode( "\r\n", $include_url_pattern );
					$include_urls = array_filter( $include_urls );
					$include_urls = str_replace( home_url(), '', $include_urls );
					$include_urls = str_replace( '/', '', $include_urls );
					//$include_urls = array_filter($include_urls);
					//var_dump($include_urls);
					//var_dump($url);
					$post_id = '';
					global $post;
					//var_dump($post->ID);
					if ( ! empty( $post->ID ) ) {
						$post_id = $post->ID;
					}

					$show_coming_soon_page = false;

					if ( is_array( $include_urls ) && ( in_array( $url, $include_urls ) || in_array( $post_id, $include_urls ) ) ) {
						$show_coming_soon_page = true;
					}

					// check wildcard urls
					$urls_to_test = $include_urls;
					$urls_to_test = str_replace( home_url(), '', $urls_to_test );
					$url_uri      = $_SERVER['REQUEST_URI'];
					foreach ( $urls_to_test as $url_to_test ) {
						if ( strpos( $url_to_test, '*' ) !== false ) {
							// Wildcard url
							$url_to_test = str_replace( '*', '', $url_to_test );
							if ( strpos( $url_uri, untrailingslashit( $url_to_test ) ) !== false ) {
								$show_coming_soon_page = true;
							}
						}
					}

					if ( $show_coming_soon_page === false ) {
						return false;
					}
				}
			} else {
				// Check for included pages regex
				$include_url_pattern = substr( $include_url_pattern, 3 );
				if ( ! empty( $include_url_pattern ) && @preg_match( "/{$include_url_pattern}/", $_SERVER['REQUEST_URI'] ) == 0 ) {
					return false;
				}
			}
		}

		// Check for excludes pages
		if ( ! empty( $include_exclude_options ) && $include_exclude_options == '3' ) {
			if ( substr( $exclude_url_pattern, 0, 3 ) != '>>>' ) {
				if ( ! empty( $exclude_url_pattern ) ) {
					//$url = preg_replace('/\?ref=\d*/','',$_SERVER['REQUEST_URI']);
					$request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
					$url         = rtrim( ltrim( $request_uri[0], '/' ), '/' );

					$r = array_intersect( explode( '/', $url ), explode( '/', home_url() ) );

					$url = str_replace( $r, '', $url );

					$url = str_replace( '/', '', $url );
					//var_dump($url);

					$exclude_urls = explode( "\r\n", $exclude_url_pattern );
					$exclude_urls = array_filter( $exclude_urls );
					$exclude_urls = str_replace( home_url(), '', $exclude_urls );
					$exclude_urls = str_replace( '/', '', $exclude_urls );
					//$exclude_urls = array_filter($exclude_urls);
					$post_id = '';
					global $post;
					//var_dump($post->ID);
					if ( ! empty( $post->ID ) ) {
						$post_id = $post->ID;
					}

					// check exact urls
					if ( is_array( $exclude_urls ) && ( in_array( $url, $exclude_urls ) || in_array( $post_id, $exclude_urls ) ) ) {
						return false;
					}

					// check wildcard urls
					$urls_to_test = $exclude_urls;
					$urls_to_test = str_replace( home_url(), '', $urls_to_test );
					$url_uri      = $_SERVER['REQUEST_URI'];
					foreach ( $urls_to_test as $url_to_test ) {
						if ( strpos( $url_to_test, '*' ) !== false ) {
							// Wildcard url
							$url_to_test = str_replace( '*', '', $url_to_test );
							if ( strpos( $url_uri, untrailingslashit( $url_to_test ) ) !== false ) {
								return false;
							}
						}
					}

					// Check for affiliateWP
					if ( class_exists( 'Affiliate_WP' ) && ( strpos( $url, 'ref' ) !== false ) ) {
						return false;
					}
				}
			} else {

				// Check for excluded pages
				$exclude_url_pattern = substr( $exclude_url_pattern, 3 );
				if ( ! empty( $exclude_url_pattern ) && @preg_match( "/{$exclude_url_pattern}/", $_SERVER['REQUEST_URI'] ) > 0 ) {
					return false;
				}
			}
		}
	}

	// Only show the Coming Soon Page on the home page
	if ( ! empty( $include_exclude_options ) && $include_exclude_options == '1' && $is_preview == false ) {
		if ( $_SERVER['REQUEST_URI'] == '/' || substr( $_SERVER['REQUEST_URI'], 0, 2 ) == '/?' ) {
		} else {
			return false;
		}
	}

	// Check if redirect url and exclude
	if ( $status == '3' && ! empty( $redirect_url ) ) {
		$r_url = parse_url( $redirect_url );
		if ( $r_url['host'] == $_SERVER['HTTP_HOST'] && $r_url['path'] == $_SERVER['REQUEST_URI'] && $is_preview == false ) {
			return false;
		}
	}

	// Exit if a custom login page
	if ( empty( $disable_default_excluded_urls ) ) {
		if ( preg_match( '/login|admin|dashboard|account/i', $_SERVER['REQUEST_URI'] ) > 0 && $is_preview == false ) {
			return false;
		}
	}

	//Exit if wysija double opt-in
	if ( isset( $emaillist ) && $emaillist == 'wysija' && preg_match( '/wysija/i', $_SERVER['REQUEST_URI'] ) > 0 && $is_preview == false ) {
		return false;
	}

	if ( isset( $emaillist ) && $emaillist == 'mailpoet' && preg_match( '/mailpoet/i', $_SERVER['REQUEST_URI'] ) > 0 && $is_preview == false ) {
		return false;
	}

	if ( isset( $emaillist ) && $emaillist == 'mymail' && preg_match( '/confirm/i', $_SERVER['REQUEST_URI'] ) > 0 && $is_preview == false ) {
		return false;
	}

	//Limit access by role

	if ( $is_preview === false ) {
		if ( ! empty( $include_roles ) && ! isset( $_COOKIE['wp-client-view'] ) ) {
			foreach ( $include_roles as $v ) {
				if ( $v == 'anyone' && is_user_logged_in() ) {
					return false;
				}
				if ( current_user_can( $v ) ) {
					return false;
				}
			}
		} elseif ( is_user_logged_in() ) {
			return false;
		}
	}

	// check if 3rd party plugins is enabled
	if ( ! empty( $settings['enable_wp_head_footer'] ) ) {
		//add_action('wp_enqueue_scripts', 'seed_cspv5_deregister_frontend_theme_styles', PHP_INT_MAX);
	}

	// Finally check if we should show the coming soon page.

	// set headers
	if ( $status == '2' && $is_preview == false ) {
		if ( empty( $settings ) ) {
			echo __( 'Please create your Maintenance Page in the plugin settings.', 'seedprod-pro' );
			exit();
		}
		header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
		header( 'Status: 503 Service Temporarily Unavailable' );
		header( 'Retry-After: 86400' ); // retry in a day
		$seed_cspv5_maintenance_file = WP_CONTENT_DIR . '/maintenance.php';
		if ( ! empty( $enable_maintenance_php ) and file_exists( $seed_cspv5_maintenance_file ) ) {
			include_once $seed_cspv5_maintenance_file;
			exit();
		}
	} elseif ( $status == '3' ) {
		if ( ! empty( $redirect_url ) ) {
			wp_redirect( $redirect_url );
			exit;
		} else {
			echo __( 'Please create enter your redirect url in the plugin settings.', 'seedprod-pro' );
			exit();
		}
	} else {
		if ( empty( $settings ) ) {
			echo __( 'Please create your Coming Soon Page in the plugin settings.', 'seedprod-pro' );
			exit();
		}
		header( 'HTTP/1.1 200 OK' );
		header( 'Cache-Control: max-age=0, private' );
	}

	if ( is_feed() ) {
		header( 'Content-Type: text/html; charset=UTF-8' );
	}

	// Prevetn Plugins from caching
	// Disable caching plugins. This should take care of:
	//   - W3 Total Cache
	//   - WP Super Cache
	//   - ZenCache (Previously QuickCache)
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}

	if ( ! defined( 'DONOTCDN' ) ) {
		define( 'DONOTCDN', true );
	}

	if ( ! defined( 'DONOTCACHEDB' ) ) {
		define( 'DONOTCACHEDB', true );
	}

	if ( ! defined( 'DONOTMINIFY' ) ) {
		define( 'DONOTMINIFY', true );
	}

	if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
		define( 'DONOTCACHEOBJECT', true );
	}

	// render
	$upload_dir = wp_upload_dir();
	if ( is_multisite() ) {
		$path = $upload_dir['baseurl'] . '/seedprod/' . get_current_blog_id() . '/template-' . $page_id . '/index.php';
	} else {
		$path = $upload_dir['basedir'] . '/seedprod/template-' . $page_id . '/index.php';
	}

	if ( ! empty( $page->html ) ) {
		echo $page->html;
	} else {
		if ( file_exists( $path ) ) {
			require_once $path;
		} else {
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/template/index.php';
		}
	}

	exit();
}



function seed_cspv5_legacy_get_settings() {
	$settings = get_option( 'seed_cspv5_settings_content' );
	return apply_filters( 'seed_cspv5_get_settings', $settings );
}




/**
 * Remove theme's style sheets so they do not conflict with the coming soon page
 */

function seed_cspv5_legacy_deregister_frontend_theme_styles() {
	// remove scripts registered ny the theme so they don't screw up our page's style

	global $wp_styles;
	// list of styles to keep else remove
	$styles = 'admin-bar';
	$d      = explode( '|', $styles );
	$theme  = wp_get_theme();

	//loop styles to see which one's the theme registers
	$remove_these_styles = array( 'admin-bar' );
	foreach ( $wp_styles->registered as $k => $v ) {
		if ( in_array( $k, $wp_styles->queue ) ) {
			// if the src contains the template or stylesheet remove
			if ( strpos( $v->src, $theme->stylesheet ) !== false ) {
				$remove_these_styles[] = $k;
			}
			if ( strpos( $v->src, $theme->template ) !== false ) {
				if ( ! in_array( $k, $remove_these_styles ) ) {
					$remove_these_styles[] = $k;
				}
			}
		}
	}

	foreach ( $wp_styles->queue as $handle ) {
		//echo '<br> '.$handle;
		if ( ! empty( $remove_these_styles ) ) {
			if ( in_array( $handle, $remove_these_styles ) ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
				//echo '<br>removed '.$handle;
			}
		}
	}

	add_filter( 'show_admin_bar', '__return_false' );
}




function seed_cspv5_legacy_select_lang( $id, $option_values, $selected = null, $style = null ) {
	echo "<select id='$id' name='$id' class='form-control input-sm' style='{$style}'>";
	if ( ! empty( $option_values ) ) {
		foreach ( $option_values as $k => $v ) {
			if ( is_array( $v ) ) {
				echo '<optgroup label="' . ucwords( $k ) . '">';
				foreach ( $v as $k1 => $v1 ) {
					echo '<option value="' . $k1 . '"' . selected( $selected, $k1, false ) . ">$v1</option>";
				}
				echo '</optgroup>';
			} else {
				if ( ! isset( $options[ $id ] ) ) {
					$options[ $id ] = '';
				}
				$language_name = $v;
				$language_name = explode( '|', $language_name );
				if ( ! empty( $language_name[0] ) ) {
					$v = $language_name[0];
				}
				echo "<option value='$k' " . selected( $selected, $k, false ) . ">$v</option>";
			}
		}
	}
	echo '</select> ';
}


	/**
	 * Subscribe User to Mailing List or return an error.
	 */

function seed_cspv5_legacy_subscribe_callback() {
	//if(check_ajax_referer('seed_cspv5_subscribe_callback')){

	// Initialize a global var to store results in
	global $seed_cspv5_post_result;
	global $errors;
	$errors = array();

	// Get the page id
	$page_id = '';
	if ( ! empty( $_REQUEST['page_id'] ) ) {
		$page_id = $_REQUEST['page_id'];
	}
	$data['page_id'] = $page_id;

	//Get page settings
	global $wpdb;
	$tablename = $wpdb->prefix . 'cspv5_pages';
	$sql       = "SELECT * FROM $tablename WHERE id= %d";
	$safe_sql  = $wpdb->prepare( $sql, $page_id );
	$page      = $wpdb->get_row( $safe_sql );

	if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
		$settings = unserialize( base64_decode( $page->settings ) );
	} else {
		$settings = unserialize( $page->settings );
	}

	@extract( $settings );

	$cookie_submit = false;
	if ( ! empty( $_REQUEST['comment'] ) ) {
		$cookie_submit = true;
	}

	// Get language info
	$lang_id = '';
	if ( ! empty( $_REQUEST['lang'] ) ) {
		$lang_id = $_REQUEST['lang'];
	}

	if ( ! empty( $lang_id ) ) {
		$lang_settings_name = 'seed_cspv5_' . $page_id . '_language_' . $lang_id;
		$lang_settings      = get_option( $lang_settings_name );
		if ( ! empty( $lang_settings ) ) {
			$lang_settings = maybe_unserialize( $lang_settings );
		}
	}

	if ( ! empty( $lang_settings['thankyou_msg'] ) && ! empty( $lang_id ) ) {
		$ty_content = $lang_settings['thankyou_msg'];
	} else {
		$ty_content = $settings['thankyou_msg'];
	}

	if ( ! empty( $lang_settings['txt_stats_referral_url'] ) && ! empty( $lang_id ) ) {
		$txt_stats_referral_url = $lang_settings['txt_stats_referral_url'];
	} else {
		$txt_stats_referral_url = $settings['txt_stats_referral_url'];
	}

	if ( ! empty( $lang_settings['txt_stats_referral_stats'] ) && ! empty( $lang_id ) ) {
		$txt_stats_referral_stats = $lang_settings['txt_stats_referral_stats'];
	} else {
		$txt_stats_referral_stats = $settings['txt_stats_referral_stats'];
	}

	if ( ! empty( $lang_settings['txt_stats_referral_subscribers'] ) && ! empty( $lang_id ) ) {
		$txt_stats_referral_subscribers = $lang_settings['txt_stats_referral_subscribers'];
	} else {
		$txt_stats_referral_subscribers = $settings['txt_stats_referral_subscribers'];
	}

	if ( ! empty( $lang_settings['txt_already_subscribed_msg'] ) && ! empty( $lang_id ) ) {
		$txt_already_subscribed_msg = $lang_settings['txt_already_subscribed_msg'];
	} else {
		$txt_already_subscribed_msg = $settings['txt_already_subscribed_msg'];
	}

	// Get form info
	// Get form settings
	if ( seed_cspv5_legacy_cu( 'fb' ) ) {
		$form_settings_name = 'seed_cspv5_' . $page_id . '_form';
		$form_settings      = get_option( $form_settings_name );
		if ( ! empty( $form_settings ) ) {
			$form_settings = maybe_unserialize( $form_settings );
		}
	}

	// Collect request data
	// Spam check, this will be fined in if spam
	if ( ! empty( $_REQUEST['message'] ) ) {
		return false;
	}

	// Check field values
	$email = '';
	if ( ! empty( $_REQUEST['email'] ) ) {
		$email = sanitize_email( $_REQUEST['email'] );
	}

	$name = '';
	if ( ! empty( $_REQUEST['name'] ) ) {
		$name = sanitize_text_field( $_REQUEST['name'] );
	}

	$optin_confirmation = 0;
	if ( ! empty( $_REQUEST['optin_confirmation'] ) ) {
		$optin_confirmation = 1;
	}

	// Sanitize random fields
	if ( seed_cspv5_legacy_cu( 'fb' ) ) {
		foreach ( $_REQUEST as $k => $v ) {
			if ( substr( $k, 0, 6 ) === 'field_' ) {
				$_REQUEST[ $k ] = sanitize_text_field( $v );
			}
		}
	}

	$bypassed_emaillist = apply_filters( 'seed_cspv5_bypassed_emaillist', array( 'gravityforms', 'ninjaforms', 'formidable' ) );

	// Check it we need to validate recaptcha
	if ( ! in_array( $emaillist, $bypassed_emaillist ) ) {
		if ( ! empty( $enable_recaptcha ) && ! $cookie_submit && ! empty( $recaptcha_site_key ) && ! empty( $recaptcha_secret_key ) ) {
			$response = wp_remote_post(
				'https://www.google.com/recaptcha/api/siteverify',
				array(
					'body' => array(
						'secret'   => $recaptcha_secret_key,
						'response' => $_REQUEST['g-recaptcha-response'],
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				$seed_cspv5_post_result['status'] = '500';
				$seed_cspv5_post_result['html']   = $error_message;
			} else {
				$body = json_decode( wp_remote_retrieve_body( $response ) );
			}

			if ( $body->success === false ) {
				$seed_cspv5_post_result['status']    = '400';
				$seed_cspv5_post_result['msg']       = 'Invalid Recaptcha';
				$seed_cspv5_post_result['msg_class'] = 'alert-danger';
				$errors[]                            = $seed_cspv5_post_result['msg'];

				$emaillist = '';
			}
		}
	}

	// Check it we need to validate email
	if ( ! in_array( $emaillist, $bypassed_emaillist ) ) {
		if ( is_email( $email ) != $email || empty( $email ) ) {
			$seed_cspv5_post_result['status'] = '400';
			if ( ! empty( $lang_settings['txt_invalid_email_msg'] ) && ! empty( $lang_id ) ) {
				$seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_email_msg'];
			} else {
				$seed_cspv5_post_result['msg'] = $txt_invalid_email_msg;
			}
			$seed_cspv5_post_result['msg_class'] = 'alert-danger';
			$errors[]                            = $seed_cspv5_post_result['msg'];

			$emaillist = '';
		}
	}

	// Check it we need to validate name
	if ( ! in_array( $emaillist, $bypassed_emaillist ) ) {
		if ( ! empty( $display_name ) ) {
			if ( ! empty( $require_name ) && ! $cookie_submit ) {
				if ( empty( $name ) ) {
					$seed_cspv5_post_result['status'] = '400';
					if ( ! empty( $lang_settings['txt_invalid_name_msg'] ) && ! empty( $lang_id ) ) {
						$seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_name_msg'];
					} else {
						$seed_cspv5_post_result['msg'] = $txt_invalid_name_msg;
					}
					$seed_cspv5_post_result['msg_class'] = 'alert-danger';
					$errors[]                            = $seed_cspv5_post_result['msg'];

					$emaillist = '';
				}
			}
		}
		// Validate Optin Confirmation
		if ( ! empty( $display_optin_confirm ) ) {
			if ( empty( $optin_confirmation ) && ! $cookie_submit ) {
				$seed_cspv5_post_result['status'] = '400';
				if ( ! empty( $lang_settings['txt_optin_confirmation_required'] ) && ! empty( $lang_id ) ) {
					$seed_cspv5_post_result['msg'] = $lang_settings['txt_optin_confirmation_required'];
				} else {
					$seed_cspv5_post_result['msg'] = $txt_optin_confirmation_required;
				}
				$seed_cspv5_post_result['msg_class'] = 'alert-danger';
				$errors[]                            = $seed_cspv5_post_result['msg'];

				$emaillist = '';
			}
		}

		//Check custom fields for required
		if ( ! empty( $form_settings ) && seed_cspv5_legacy_cu( 'fb' ) ) {
			foreach ( $form_settings as $k => $v ) {
				if ( is_array( $v ) ) {
					if ( substr( $k, 0, 6 ) === 'field_' && $k != 'field_name' ) {
						if ( ! empty( $v['required'] ) && ! empty( $v['visible'] ) && ! $cookie_submit ) {
							if ( empty( $_REQUEST[ $k ] ) ) {
								$seed_cspv5_post_result['status'] = '400';
								// if(!empty($lang_settings['txt_invalid_name_msg']) && !empty($lang_id)){
								//    $seed_cspv5_post_result['msg'] = $lang_settings['txt_invalid_name_msg'];
								// }else{
								$seed_cspv5_post_result['msg'] = $v['label'] . ' Required';
								//}
								$seed_cspv5_post_result['msg_class'] = 'alert-danger';
								$errors[]                            = $seed_cspv5_post_result['msg'];

								$emaillist = '';
							}
						}
					}
				}
			}
		}
	}

	// Do email list action
	if ( ! empty( $emaillist ) ) {
		$data['settings'] = $settings;
		// Get settings
		$mod = '';
		if ( $emaillist == 'mailchimp' ) {
			$e_settings_name = 'seed_cspv5_' . $page_id . '_' . $emaillist;
			$e_settings      = get_option( $e_settings_name );
			if ( ! empty( $e_settings ) ) {
				$e_settings = maybe_unserialize( $e_settings );
			}
			if ( empty( $e_settings['mailchimp_api_key'] ) || ( ! empty( $e_settings['api_version'] ) && $e_settings['api_version'] == '3' ) ) {
				// Use V3
				$mod = '_v3';
			}
		}
		do_action( 'seed_cspv5_legacy_emaillist_' . $emaillist . $mod, $data );
	}
	//}

	$html = '';

	if ( isset( $GLOBALS['wp_embed'] ) ) {
		$ty_content = $GLOBALS['wp_embed']->autoembed( $ty_content );
	}
	$ty_content = do_shortcode( shortcode_unautop( wpautop( convert_chars( wptexturize( $ty_content ) ) ) ) );

	// Return HTML
	if ( '200' == $seed_cspv5_post_result['status'] ) {
		// New Subscriber
		$status = '200';

		$html = $ty_content . $settings['conversion_scripts'];
		if ( ! empty( $settings['enable_reflink'] ) ) {
			$html .= "<br><br><div id='cspio-ref-link'>" . $txt_stats_referral_url . '<br>' . seed_cspv5_legacy_ref_link() . '</div>';

			if ( ! empty( $settings['enable_prize_levels'] ) && $settings['enable_prize_levels'] == '1' ) {
				// get settings
				// Get form settings
				$prize_settings_name = 'seed_cspv5_' . $page_id . '_prizes';
				$prize_settings      = get_option( $prize_settings_name );
				if ( ! empty( $prize_settings ) ) {
					$prize_settings = maybe_unserialize( $prize_settings );
				}

				$html .= '<table id="cspio-prizes">';
				foreach ( $prize_settings as $k => $v ) {
					if ( strrpos( $k, 'prize_' ) !== false ) {
						if ( ! empty( $v['description'] ) ) {
							$class = '';
							if ( empty( $seed_cspv5_post_result['subscribers'] ) ) {
								$seed_cspv5_post_result['subscribers'] = 0;
							}
							if ( $seed_cspv5_post_result['subscribers'] >= $v['number'] ) {
								$class = 'cspio-reveal';
							}
							$html .= '<tr class="' . $class . '"><td class="cspio-prizes-desc">' . $v['description'] . '</td>';
							if ( $seed_cspv5_post_result['subscribers'] >= $v['number'] ) {
								$html .= '<td class="cspio-prizes-reveal">' . $v['reveal'] . '</td>';
							} else {
								if ( empty( $txt_prize_level_more ) ) {
									$txt_prize_level_more = 'Refer %d more subscribers to claim this.';
								}
								$need  = ( $v['number'] - $seed_cspv5_post_result['subscribers'] );
								$html .= '<td class="cspio-prizes-reveal">' . sprintf( $txt_prize_level_more, $need ) . ' </td>';
							}

							$html .= '</tr>';
						}
					}
				}
				$html .= '</table>';
			}
		}
		// Send Auto responder if setup
		// Get autoresponder settings
		$autoresponder_settings_name = 'seed_cspv5_' . $page_id . '_autoresponder';
		$autoresponder_settings      = get_option( $autoresponder_settings_name );
		if ( ! empty( $autoresponder_settings ) ) {
			if ( ! empty( $autoresponder_settings['autoresponder'] ) && $autoresponder_settings['from_email'] && $autoresponder_settings['subject'] ) {
				$autoresponder_settings = maybe_unserialize( $autoresponder_settings );
				// Send auto responder
				$msg           = $autoresponder_settings['autoresponder'];
				$template_tags = array(
					'{referral_url}' => seed_cspv5_legacy_ref_link(),
				);
				$msg           = strtr( $msg, $template_tags );
				$from_email    = sanitize_text_field( $autoresponder_settings['from_email'] );
				$subject       = sanitize_text_field( $autoresponder_settings['subject'] );

				$headers[] = 'From: ' . $from_email;
				$headers[] = 'Content-Type: text/html; charset=UTF-8';

				$mresult = wp_mail( $email, $subject, $msg, $headers );
			}
		}
	} elseif ( '409' == $seed_cspv5_post_result['status'] ) {
		// Already Subscribed
		$status = '409';

		$html = $txt_already_subscribed_msg;
		if ( ! empty( $settings['enable_reflink'] ) ) {
			// Already Subscribed send Referaral Info
			$html .= "<br><br><div id='cspio-ref-link'>" . $txt_stats_referral_url . '<br>' . seed_cspv5_legacy_ref_link();
			$html .= '</div>';
			$html .= '<br>' . $txt_stats_referral_stats . '<br>' . $txt_stats_referral_subscribers . ': <span class="cspio-subscriber-count">' . $seed_cspv5_post_result['subscribers'] . '</span>';

			if ( ! empty( $settings['enable_prize_levels'] ) && $settings['enable_prize_levels'] == '1' ) {
				// get settings
				// Get form settings
				$prize_settings_name = 'seed_cspv5_' . $page_id . '_prizes';
				$prize_settings      = get_option( $prize_settings_name );
				if ( ! empty( $prize_settings ) ) {
					$prize_settings = maybe_unserialize( $prize_settings );
				}
				unset( $prize_settings['page_id'] );
				unset( $prize_settings['settings_name'] );
				$html .= '<table id="cspio-prizes">';
				foreach ( $prize_settings as $k => $v ) {
					if ( strrpos( $k, 'prize_' ) !== false ) {
						if ( ! empty( $v['description'] ) ) {
							$class = '';
							if ( $seed_cspv5_post_result['subscribers'] >= $v['number'] ) {
								$class = 'cspio-reveal';
							}
							$html .= '<tr class="' . $class . '"><td class="cspio-prizes-desc">' . $v['description'] . '</td>';
							if ( $seed_cspv5_post_result['subscribers'] >= $v['number'] ) {
								$html .= '<td class="cspio-prizes-reveal">' . $v['reveal'] . '</td>';
							} else {
								if ( empty( $txt_prize_level_more ) ) {
									$txt_prize_level_more = 'Refer %d more subscribers to claim this.';
								}
								$need  = ( $v['number'] - $seed_cspv5_post_result['subscribers'] );
								$html .= '<td class="cspio-prizes-reveal">' . sprintf( $txt_prize_level_more, $need ) . ' </td>';
							}

							$html .= '</tr>';
						}
					}
				}
				$html .= '</table>';
			}
		}
	} elseif ( '400' == $seed_cspv5_post_result['status'] ) {
		// Validation Error
		$status = '400';
		$html   = '<div id="cspio-alert" class="alert ' . $seed_cspv5_post_result['msg_class'] . '"><ul>';
		foreach ( $errors as $e ) {
			$html .= '<li>' . $e . '</li>';
		}
		$html .= '</ul></div>';
	} elseif ( '500' == $seed_cspv5_post_result['status'] ) {
		// API Error
		$status = '500';
		$html   = $seed_cspv5_post_result['html'];
	}

	if ( $status != '500' ) {
		$html = '<div id="cspio-thankyoumsg">' . $html . '</div>';
	}

	$content = '';
	if ( $status == '200' || $status == '409' ) {
		ob_start();
		include SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/template/show_share_buttons_ty.php';
		$content = ob_get_clean();
	}
	header( 'Content-Type: text/javascript; charset=utf8' );
	// Return jsonp results
	$html = $html . $content;

	$response = array(
		'status' => $status,
		'html'   => $html,
	);
	echo sanitize_text_field( $_GET['callback'] ) . '(' . json_encode( $response ) . ')';
	exit();
}




function seed_cspv5_legacy_extensions() {
	$extensions = array(
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailchimp/mailchimp.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailchimp/mailchimp-v3.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/convertkit/convertkit.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/activecampaign/activecampaign.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/database/database.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/sendy/sendy.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mailpoet/mailpoet.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/madmimi/madmimi.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/infusionsoft/infusionsoft.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/icontact/icontact.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/htmlwebform/htmlwebform.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/gravityforms/gravityforms.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/ninjaforms/ninjaforms.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/followupemails/followupemails.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/formidable/formidable.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/getresponse/getresponse.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/feedburner/feedburner.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/constantcontact/constantcontact.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/campaignmonitor/campaignmonitor.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/aweber/aweber.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/drip/drip.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/mymail/mymail.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/feedblitz/feedblitz.php',
		SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/extentions/zapier/zapier.php',
	);

	$active_extensions = apply_filters( 'seed_cspv5_active_extensions', $extensions );

	foreach ( $active_extensions as $i ) {
		require_once $i;
	}
} // END seed_cspv5_extensions()

	seed_cspv5_legacy_extensions();






function seed_cspv5_legacy_cu( $rper = null ) {
	if ( ! empty( $rper ) ) {
		$uper = explode( ',', get_option( 'seed_cspv5_per' ) );
		if ( in_array( $rper, $uper ) ) {
			return true;
		} else {
			return false;
		}
	} else {
		$a = get_option( 'seed_cspv5_a' );
		if ( $a ) {
			return true;
		} else {
			return false;
		}
	}
}



	/**
 *  Get IP
 */
function seed_cspv5_legacy_get_ip() {
	$ip = '';
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) and strlen( $_SERVER['HTTP_X_FORWARDED_FOR'] ) > 6 ) {
		$ip = strip_tags( $_SERVER['HTTP_X_FORWARDED_FOR'] );
	} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) and strlen( $_SERVER['HTTP_CLIENT_IP'] ) > 6 ) {
		$ip = strip_tags( $_SERVER['HTTP_CLIENT_IP'] );
	} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) and strlen( $_SERVER['REMOTE_ADDR'] ) > 6 ) {
		$ip = strip_tags( $_SERVER['REMOTE_ADDR'] );
	}//endif
	if ( ! $ip ) {
		$ip = '127.0.0.1';
	}
	return strip_tags( $ip );
}



/**
 *  Get Ref Link
 */

function seed_cspv5_legacy_ref_link() {
	global $seed_cspv5_post_result;
	$ref_link = '';

	if ( ! empty( $seed_cspv5_post_result['ref'] ) ) {
		$ref_url = $_SERVER['HTTP_REFERER'];
		if ( empty( $ref_url ) ) {
			$ref_url = $_REQUEST['href'];
		}
		$ref_url_parts = parse_url( $ref_url );
		$port          = '';
		if ( ! empty( $ref_url_parts['port'] ) ) {
			$port = ':' . $ref_url_parts['port'];
		}
		if ( ! empty( $ref_url_parts['port'] ) ) {
			if ( $ref_url_parts['port'] == '80' ) {
				$port = '';
			}
		}
		$ref_link = $ref_url_parts['scheme'] . '://' . $ref_url_parts['host'] . $port . $ref_url_parts['path'];
		$ref_link = $ref_link . '?ref=' . $seed_cspv5_post_result['ref'];
	} else {
		if ( empty( $_REQUEST['href'] ) ) {
			$ref_link = 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		} else {
			$ref_link = $_REQUEST['href'];
		}
	}
	return $ref_link;
}



add_shortcode( 'seed_bypass_url', 'seed_cspv5_legacy_bypass_url' );

function seed_cspv5_legacy_bypass_url( $echo = true ) {
	global $seed_cspv5;
	$seed_cspv5 = get_option( 'seed_cspv5_settings_content' );
	extract( $seed_cspv5 );

	$output = home_url( '/' ) . '?bypass=' . $client_view_url . '&return=' . urlencode( $_SERVER['REQUEST_URI'] );

	$output = apply_filters( 'seed_cspv5_bypass_url', $output );

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}



add_shortcode( 'seed_bypass_link', 'seed_cspv5_legacy_bypass_link' );

function seed_cspv5_legacy_bypass_link( $atts, $echo = true ) {
	extract(
		shortcode_atts(
			array(
				'text'  => 'Bypass',
				'class' => '',
			),
			$atts
		)
	);

	global $seed_cspv5;
	$seed_cspv5 = get_option( 'seed_cspv5_settings_content' );
	extract( $seed_cspv5 );

	$output = '<a href="' . seed_cspv5_bypass_url( false ) . '" class="' . $class . '">' . $text . '</a>';

	$output = apply_filters( 'seed_cspv5_bypass_link', $output );

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}


add_shortcode( 'seed_contact_form', 'seed_cspv5_legacy_contact_form' );

function seed_cspv5_legacy_contact_form( $atts, $echo = true ) {
	extract(
		shortcode_atts(
			array(
				'text' => 'Contact Us',
				'icon' => true,
			),
			$atts
		)
	);

	global $seed_cspv5;
	$seed_cspv5 = get_option( 'seed_cspv5_settings_content' );
	extract( $seed_cspv5 );

	$icon_code = '';
	if ( $icon ) {
		$icon_code = '<i class="fa fa-envelope "></i>';
	}

	$output = '<a href="javascript:void(0)" onclick="javascript:' . "jQuery('#cspio-cf-modal').modal('show');" . '">' . $icon_code . ' ' . $text . '</a>';

	$output = apply_filters( 'seed_cspv5_contact_', $output );

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}


		/**
	 * Send Contact Form or return an error.
	 */
function seed_cspv5_legacy_contactform_callback() {
	// Get the page id
	$page_id = '';
	if ( ! empty( $_REQUEST['page_id'] ) ) {
		   $page_id = $_REQUEST['page_id'];
	}
		$data['page_id'] = $page_id;

		//Get page settings
		global $wpdb;
		$tablename = $wpdb->prefix . 'cspv5_pages';
		$sql       = "SELECT * FROM $tablename WHERE id= %d";
		$safe_sql  = $wpdb->prepare( $sql, $page_id );
		$page      = $wpdb->get_row( $safe_sql );

	if ( base64_encode( base64_decode( $page->settings, true ) ) === $page->settings ) {
		$settings = unserialize( base64_decode( $page->settings ) );
	} else {
		$settings = unserialize( $page->settings );
	}

		// Get language info
		$lang_id = '';
	if ( ! empty( $_REQUEST['lang'] ) ) {
		$lang_id = $_REQUEST['lang'];
	}

	if ( ! empty( $lang_id ) ) {
		$lang_settings_name = 'seed_cspv5_' . $page_id . '_language_' . $lang_id;
		$lang_settings      = get_option( $lang_settings_name );
		if ( ! empty( $lang_settings ) ) {
			$lang_settings = maybe_unserialize( $lang_settings );
		}
	}

	if ( ! empty( $lang_settings['txt_contact_form_error'] ) && ! empty( $lang_id ) ) {
		$settings['txt_contact_form_error'] = $lang_settings['txt_contact_form_error'];
	}

		@extract( $settings );

		//if(check_ajax_referer('seed_cspv5_contactform_callback')){
		$email     = sanitize_email( $_REQUEST['cspio-cf-email'] );
		$msg       = sanitize_textarea_field( $_REQUEST['cspio-cf-msg'] );
		$msg       = $email . PHP_EOL . PHP_EOL . $msg;
		$headers[] = 'Reply-To: ' . $email;
		$mresult   = false;
		$is_error  = false;
	if ( is_email( $email ) && ! empty( $msg ) ) {
		if ( empty( $cf_form_emails ) ) {
			$emails = get_option( 'admin_email' );
		} else {
			$emails = $cf_form_emails;
		}
		$mresult = wp_mail( $emails, '[' . home_url() . __( '] New Contact Form Message', 'seedprod' ), $msg, $headers );
		if ( $mresult == false ) {
			http_response_code( 500 );
			$status = '500';
		} else {
			$status = '200';
		}
	} else {
		$is_error = true;
		$status   = $txt_contact_form_error;
	}

		$html = '';
	if ( ! empty( $cf_confirmation_msg ) ) {
		$html = $cf_confirmation_msg;
	}

		// check recaptcha
	if ( $is_error == false && ! empty( $enable_recaptcha ) && ! empty( $recaptcha_site_key ) && ! empty( $recaptcha_secret_key ) ) {
		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => $recaptcha_secret_key,
					'response' => $_REQUEST['g-recaptcha-response'],
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$status = '500';
			$html   = $error_message;
		} else {
			$body = json_decode( wp_remote_retrieve_body( $response ) );
		}

		if ( $body->success === false ) {
			$status = 'Invalid Recaptcha';
		}
	}
		header( 'Content-Type: text/javascript; charset=utf8' );

		$response = array(
			'status' => $status,
			'html'   => $html,
		);

		echo sanitize_text_field( $_GET['callback'] ) . '(' . json_encode( $response ) . ')';
		//}
		exit();
}


   /**
 * Update cookie length for bypass url
 */
function seed_cspv5_legacy_change_wp_cookie_logout( $expirein ) {
	global $seed_cspv5_bypass_expires;
	if ( ! empty( $seed_cspv5_bypass_expires ) ) {
		return $seed_cspv5_bypass_expires; // Modify the exire cookie
	} else {
		return $expirein;
	}
}

function seed_legacy_bypass_form_func( $atts ) {
	$a = shortcode_atts(
		array(
			'msg'        => 'Password',
			'button-txt' => 'Enter',
			'return'     => '',
		),
		$atts
	);
	ob_start();
	?>
	<div class="row">
	<div class="col-md-12 seperate">
	<div class="input-group">
	<input type="password" id="cspio-bypass" class="form-control input-lg form-el sp-form-input" placeholder="<?php echo $a['msg']; ?>"></input>
	<span class="input-group-btn">
	<button id="cspio-bypass-btn" class="btn btn-lg btn-primary form-el noglow"><?php echo $a['button-txt']; ?></button>
	</span>
	</div>
	</div>
	</div>
	<script>
	jQuery( document ).ready(function($) {
		$( "#cspio-bypass-btn" ).click(function(e) {
		  e.preventDefault();
		  window.location = "?bypass="+$("#cspio-bypass").val()+'&return=<?php echo urlencode( $a['return'] ); ?>';
		});
	});
	</script>
	
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'seed_bypass_form', 'seed_legacy_bypass_form_func' );

