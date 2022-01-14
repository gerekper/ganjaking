<?php
/**
 * Yoast SEO Plugin.
 *
 * WPSEO Premium plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2008-2019, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Yoast SEO Premium
 * Version:     17.9
 * Plugin URI:  https://yoa.st/2jc
 * Description: The first true all-in-one SEO solution for WordPress, including on-page content analysis, XML sitemaps and much more.
 * Author:      Team Yoast
 * Author URI:  https://yoa.st/2jc
 * Text Domain: wordpress-seo-premium
 * Domain Path: /languages/
 * License:     GPL v3
 * Requires at least: 5.6
 * Requires PHP: 5.6.20
 *
 * WC requires at least: 3.0
 * WC tested up to: 6.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Yoast\WP\SEO\Premium\Addon_Installer;

if ( ! defined( 'WPSEO_PREMIUM_FILE' ) ) {
	define( 'WPSEO_PREMIUM_FILE', __FILE__ );
}

if ( ! defined( 'WPSEO_PREMIUM_PATH' ) ) {
	define( 'WPSEO_PREMIUM_PATH', plugin_dir_path( WPSEO_PREMIUM_FILE ) );
}

if ( ! defined( 'WPSEO_PREMIUM_BASENAME' ) ) {
	define( 'WPSEO_PREMIUM_BASENAME', plugin_basename( WPSEO_PREMIUM_FILE ) );
}

/**
 * {@internal Nobody should be able to overrule the real version number as this can cause
 *            serious issues with the options, so no if ( ! defined() ).}}
 */
define( 'WPSEO_PREMIUM_VERSION', '17.9' );

// Initialize Premium autoloader.
$wpseo_premium_dir               = WPSEO_PREMIUM_PATH;
$yoast_seo_premium_autoload_file = $wpseo_premium_dir . 'vendor/autoload.php';

if ( is_readable( $yoast_seo_premium_autoload_file ) ) {
	require $yoast_seo_premium_autoload_file;
}

// This class has to exist outside of the container as the container requires Yoast SEO to exist.
$wpseo_addon_installer = new Addon_Installer( __DIR__ );
$wpseo_addon_installer->install_or_load_yoast_seo_from_vendor_directory();

// Load the container.
if ( ! wp_installing() ) {
	require_once __DIR__ . '/src/functions.php';
	YoastSEOPremium();
}

\register_activation_hook( \WPSEO_PREMIUM_FILE, [ 'WPSEO_Premium', 'install' ] );

/** NOTE: This Is Function Replace  SAME IN  = \wordpress-seo\inc\class-my-yoast-api-request.php  **/
class WPSEO_MyYoast_Api_Request {
	protected $url;
	protected $args = [
		'method'    => 'GET',
		'timeout'   => 5,
		'headers'   => [
			'Accept-Encoding' => '*',
			'Expect'          => '',
		],
	];
	protected $response;
	protected $error_message = '';
	public function __construct( $url, array $args = [] ) {
		$this->url  = 'https://api-yoast.txt?';
		$this->args = wp_parse_args( $args, $this->args );
	}
	public function fire() {
		try {
			$response       = $this->do_request( $this->url, $this->args );
			$this->response = $this->decode_response( $response );
			return true;
		}
		catch ( WPSEO_MyYoast_Bad_Request_Exception $bad_request_exception ) {
			$this->error_message = $bad_request_exception->getMessage();
			return false;
		}
	}
	public function get_response() {
		return $this->response;
	}
	protected function do_request( $url, $request_arguments ) {
		$request_arguments = $this->enrich_request_arguments( $request_arguments );
		$response          = wp_remote_request( $url, $request_arguments );
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		if ( $response_code === 200 || strpos( $response_code, '200' ) !== false ) {
			return wp_remote_retrieve_body( $response );
		}
		throw new WPSEO_MyYoast_Bad_Request_Exception( esc_html( $response_message ), (int) $response_code );
	}
	protected function decode_response( $response ) {
		$response = json_decode( $response );
		if ( ! is_object( $response ) ) {
			throw new WPSEO_MyYoast_Invalid_JSON_Exception(
				esc_html__( 'No JSON object was returned.', 'wordpress-seo' )
			);
		}
		return $response;
	}
	protected function enrich_request_arguments( array $request_arguments ) {
		$request_arguments     = wp_parse_args( $request_arguments, [ 'headers' => [] ] );
		$addon_version_headers = $this->get_installed_addon_versions();
		foreach ( $addon_version_headers as $addon => $version ) {
			$request_arguments['headers'][ $addon . '-version' ] = $version;
		}
		$request_body = $this->get_request_body();
		if ( $request_body !== [] ) {
			$request_arguments['body'] = $request_body;
		}
		return $request_arguments;
	}
	public function get_request_body() {
		return [ 'url' => WPSEO_Utils::get_home_url() ];
	}
	protected function get_installed_addon_versions() {
		$addon_manager = new WPSEO_Addon_Manager();
		return $addon_manager->get_installed_addons_versions();
	}
}
/** ENG API **/