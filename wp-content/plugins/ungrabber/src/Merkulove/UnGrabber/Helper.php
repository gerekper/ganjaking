<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

use Merkulove\UnGrabber;
use WP_Filesystem_Direct;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement work with WordPress filesystem.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class Helper {

	/**
	 * The one true Helper.
	 *
	 * @var Helper
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new Helper instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

	}

	/**
	 * Remove all ungrabber audio files.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 **/
	public function remove_audio_files() {

		/** Remove /wp-content/uploads/ungrabber/ folder. */
		$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'ungrabber';
		$this->remove_directory( $dir );

	}

	/**
	 * Remove directory with all contents.
	 *
	 * @param $dir - Directory path to remove.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function remove_directory( $dir ) {

		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		$fileSystemDirect = new WP_Filesystem_Direct( false );
		$fileSystemDirect->rmdir( $dir, true );

	}

	/**
	 * Render inline svg by id or icon name.
	 *
	 * @param int|string $icon - media id, or icon name.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return string Inline svg.
	 **/
	public function inline_svg_e( $icon ) {

		/** If this users custom svg. */
		if ( is_numeric( $icon ) ) {
			$icon = get_attached_file( $icon );

		/** If icon from library. */
		} else {
			$icon = UnGrabber::$path . 'images/mdc-icons/' . $icon;
		}
		$svg_icon = file_get_contents( $icon, false, stream_context_create( ['http' => ['ignore_errors' => true] ] ) );

		/** Escaping SVG with KSES. */
		$kses_defaults = wp_kses_allowed_html( 'post' );

		$svg_args = [
			'svg'   => [
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true, // <= Must be lower case!
			],
			'g'     => [ 'fill' => true ],
			'title' => [ 'title' => true ],
			'path'  => [ 'd' => true, 'fill' => true, ],
		];

		$allowed_tags = array_merge( $kses_defaults, $svg_args );

		echo wp_kses( $svg_icon, $allowed_tags );
	}

	/**
	 * Get remote contents.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param  string $url  The URL we're getting our data from.
	 * @return false|string The contents of the remote URL, or false if we can't get it.
	 **/
	public function get_remote( $url ) {

		$args = [
			'timeout'    => 30,
			'user-agent' => 'ungrabber-user-agent',
		];

		$response = wp_remote_get( $url, $args );
		if ( is_array( $response ) ) {
			return $response['body'];
		}

		// TODO: Add a message so that the user knows what happened.
		/** Error while downloading remote file. */
		return false;
	}

	/**
	 * Send Action to our remote host.
	 *
	 * @param $action - Action to execute on remote host.
	 * @param $plugin - Plugin slug.
	 * @param $version - Plugin version.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 **/
	public function send_action( $action, $plugin, $version ) {

		$domain = parse_url( site_url(), PHP_URL_HOST );
		$admin = base64_encode( get_option( 'admin_email' ) );
		$pid = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );

		$ch = curl_init();

		$url = 'https://merkulove.host/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/Validate.php?';
		$url .= 'action=' . $action . '&'; // Action.
		$url .= 'plugin=' . $plugin . '&'; // Plugin Name.
		$url .= 'domain=' . $domain . '&'; // Domain Name.
		$url .= 'version=' . $version . '&'; // Plugin version.
		$url .= 'pid=' . $pid . '&'; // Purchase Code.
		$url .= 'admin_e=' . $admin;

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		curl_exec( $ch );

	}

	/**
	 * Parser function to get formatted headers with response code.
	 *
	 * @param $headers - HTTP response headers.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 **/
	public function parse_headers( $headers ) {
		$head = [];
		foreach( $headers as $k => $v ) {
			$t = explode( ':', $v, 2 );
			if ( isset( $t[1] ) ) {
				$head[ trim($t[0]) ] = trim( $t[1] );
			} else {
				$head[] = $v;
				if ( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) ) {
					$head['response_code'] = intval($out[1]);
				}
			}
		}

		return $head;
	}

	/**
	 * Main Helper Instance.
	 *
	 * Insures that only one instance of Helper exists in memory at any one time.
	 *
	 * @static
	 * @return Helper
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Helper ) ) {
			self::$instance = new Helper;
		}

		return self::$instance;
	}

} // End Class Helper.
