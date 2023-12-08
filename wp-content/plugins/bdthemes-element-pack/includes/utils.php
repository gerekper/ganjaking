<?php
namespace ElementPack;

if ( ! defined( 'ABSPATH' ) )  exit; // Exit if accessed directly

class Utils {
	
	/**
	 * A list of safe tage for `get_valid_html_tag` method.
	 */
	const ALLOWED_HTML_WRAPPER_TAGS = [
		'article',
		'aside',
		'div',
		'footer',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'header',
		'main',
		'nav',
		'p',
		'section',
		'span',
	];

	public static function get_client_ip() {
		$server_ip_keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	public static function get_site_domain() {
		return str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	}

	public static function readable_num( $size ) {
		$l    = substr( $size, -1 );
		$ret  = substr( $size, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
				break;
			case 'T':
				$ret *= 1024;
				break;
			case 'G':
				$ret *= 1024;
				break;
			case 'M':
				$ret *= 1024;
				break;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}
	
	/**
	 * Validate an HTML tag against a safe allowed list.
	 * @param string $tag
	 * @return string
	 */
	public static function get_valid_html_tag( $tag ) {
		return in_array( strtolower( $tag ), self::ALLOWED_HTML_WRAPPER_TAGS ) ? $tag : 'div';
	}
	
	/**
	 * Get placeholder image source.
	 * Retrieve the source of the placeholder image.
	 * @since 5.7.6
	 * @access public
	 * @static
	 * @return string The source of the default placeholder image used by Elementor.
	 */
	public static function get_placeholder_image_src() {
		$placeholder_image = ELEMENTOR_ASSETS_URL . 'images/placeholder.png';
		
		return $placeholder_image;
	}

	/**
	 * For get wp environment for element pack
	 * @return [type] [description]
	 */
	public static function get_environment_info(){

		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		}


		// WP memory limit.
		$wp_memory_limit = self::readable_num(WP_MEMORY_LIMIT);
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, self::readable_num( @ini_get( 'memory_limit' ) ) );
		}


		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'version'                   => BDTEP_VER,
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
			'external_object_cache'     => wp_using_ext_object_cache(),
			'server_info'               => isset( $_SERVER['SERVER_SOFTWARE'] ) ? wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) : '',
			'php_version'               => phpversion(),
			'php_post_max_size'         => self::readable_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'suhosin_installed'         => extension_loaded( 'suhosin' ),
			'max_upload_size'           => wp_max_upload_size(),
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'soapclient_enabled'        => class_exists( 'SoapClient' ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'gzip_enabled'              => is_callable( 'gzopen' ),
			'mbstring_enabled'          => extension_loaded( 'mbstring' ),
		);

	}

	/**
	 * Get timezone string.
	 *
	 * Retrieve timezone string from the WordPress database.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Timezone string.
	 */
	public static function get_timezone_string() {
		$current_offset = (float) get_option( 'gmt_offset' );
		$timezone_string = get_option( 'timezone_string' );

		// Create a UTC+- zone if no timezone string exists.
		if ( empty( $timezone_string ) ) {
			if ( $current_offset < 0 ) {
				$timezone_string = 'UTC' . $current_offset;
			} else {
				$timezone_string = 'UTC+' . $current_offset;
			}
		}

		return $timezone_string;
	}
	
}
