<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit;
}

/**
 * SINGLETON: Class contain information about the envato item.
 *
 * @since 1.0.0
 *
 **/
final class EnvatoItem {

	/**
	 * The one true EnvatoItem.
	 *
	 * @var EnvatoItem
	 **/
	private static $instance;

	/**
	 * Return CodeCanyon Item ID.
	 *
     * @since 1.0.0
	 * @access public
     *
	 * @return int
	 **/
	public function get_id() {

	    return 24136249;

		/** Do we have Envato item id in cache? */
		$cache = new Cache();

        /** In this option we store Envato Item ID. */
        $key = 'mdp_' . Plugin::get_slug() . '_envato_id';
		$cached_item_id = $cache->get( $key, false );

		/** If cache exist */
		if ( ! empty( $cached_item_id ) ) {

			/** Extract item_id from cache record */
			$cached_item_id = json_decode( $cached_item_id, true );
			$item_id = (int)$cached_item_id[ $key ];

			/** ID out of the range of valid ID's */
			if ( $item_id <= 0 || $item_id > 99999999 ) {

				$item_id = 0;

				/** New request for outdated cache. */
				if ( ! $cache->get( $key, true ) ) {

					$item_id = $this->get_remote_plugin_id();
					$cache->set( $key, [ $key => $item_id ], false );

				}

			}

		/** If cache not exist */
		} else {

            $item_id = $this->get_remote_plugin_id();
            $cache->set( $key, [$key => $item_id], false );

		}

		return $item_id;

	}

	/**
	 * Return CodeCanyon Plugin ID from out server.
	 *
	 * @since 1.0.0
	 * @access private
     *
     * @return int
     **/
	private function get_remote_plugin_id() {

		/** Get url to request item id. */
		$url = $this->prepare_url();

		/** Get Envato item ID. */
		$item_id = wp_remote_get( $url, [
            'sslverify'  => false
        ] );

		/** Check for errors. */
		if ( is_wp_error( $item_id ) || empty( $item_id['body'] ) ) { return 0; }

		/** Now in $item_id we have item id. */
		$item_id = json_decode( $item_id['body'], true );

		return (int) $item_id;

	}

	/**
	 * Build url to request item id.
	 *
	 * @access private
     * @since 1.0.0
     *
	 * @return int
	 **/
	private function prepare_url() {

        $url = 'https://merkulove.host/wp-json/mdp/v2/get_id';
		$url .= '?name=' . urlencode( Plugin::get_name() );

		return $url;

	}

	/**
	 * Main EnvatoItem Instance.
	 * Insures that only one instance of EnvatoItem exists in memory at any one time.
	 *
	 * @static
     * @since 1.0.0
     * @access public
     *
	 * @return EnvatoItem
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
