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

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use Merkulove\UnGrabber as UnGrabber;
use stdClass;

/**
 * SINGLETON: Class used to implement plugin update mechanism.
 *
 * @since 2.0.2
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class PluginUpdater {
	
	/**
	 * URL from where download plugin information.
	 *
	 * @since 1.0.0
	 **/
	public $plugin_info_url = 'https://merkulove.host/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/CCPluginInfo.php';

	/**
	 * The one true PluginUpdater.
	 *
	 * @var PluginUpdater
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginUpdater instance.
	 *
	 * @since 2.0.2
	 * @access public
	 **/
	private function __construct() {

		/** Disable if plugin don't have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id === 0 ) { return; }

		/** Check do we have new version? */
		add_filter( 'site_transient_update_plugins', [$this, 'update_plugins'] );
		add_filter( 'transient_update_plugins', [$this, 'update_plugins'] );

		/** Show custom update message. */
		add_action( 'in_plugin_update_message-ungrabber/ungrabber.php', [$this, 'show_plugin_update_message' ], 10, 2 );

		/** Add plugin info to View details popup.  */
		add_filter( 'plugins_api', [$this, 'plugin_info'], 20, 3 );

	}

	/**
	 * Check do we have new version?
	 *
	 * @param $update_plugins
	 *
	 * @return mixed
	 **/
	public function update_plugins( $update_plugins ) {
		global $wp_version;

		/** Reset temporary cache. */
		$this->force_check();

		if ( ! is_object( $update_plugins ) ) {
			return $update_plugins;
		}

		if ( ! isset( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			$update_plugins->response = [];
		}
		/** This method runs multiple times, so we use short time cache.  */

		/** Trying to get from cache first. */
		$plugin_info = get_transient( 'mdp_ungrabber_plugin_info' );

		/** Download data from remote host. */
		if ( false === $plugin_info ) {

			/** Check if there's a new version of plugin. */
			$url = $this->plugin_info_url . '?';
			$url .= 'item_id=' . EnvatoItem::get_instance()->get_id();

			/** If this copy is activated. */
			if ( PluginActivation::get_instance()->is_activated() ) {
				/** Add PID to url. */
				$purchase_code = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );
				$url .= '&pid=' . $purchase_code;
			}

			$context = [
				'ssl' => [
					'verify_peer'       => false,
					'verify_peer_name'  => false
				]
			];
			$plugin_info = file_get_contents( $url, false, stream_context_create( $context ) );

			/** We don't have info about latest plugin version. */
			if ( false === $plugin_info ) {
				return $update_plugins;
			}

			/** Set cache for 5 min. */
			set_transient( 'mdp_ungrabber_plugin_info', $plugin_info, 300 ); // 5 min cache.
		}

		$plugin_info = json_decode( $plugin_info );

		/** Wrong JSON. */
		if ( null === $plugin_info ) {
			return $update_plugins;
		}

		/** When Envato return: "Error: Failed to get data for this item". */
		if ( empty( $plugin_info->version ) ) {
			return $update_plugins;
		}

		$current_version = UnGrabber::get_instance()->get_version();
		$latest_version = $plugin_info->version;

		/** If Latest version is newer, show  update version. */
		if ( version_compare( $latest_version, $current_version ) > 0 ) {

			$plugin = UnGrabber::get_instance();

			/** Download URL only for activated users. */
			$package = '';
			if ( ! empty( $plugin_info->download_url ) ) {
				$package = $plugin_info->download_url;
			}

			$update_plugins->response['ungrabber/ungrabber.php'] = (object)[
				'slug'          => 'ungrabber',
				'new_version'   => $latest_version, // The newest version
				'package'       => $package,
				'tested'        => $wp_version,
				'icons'         => [
					'2x'    => $plugin::$url . 'images/logo-color.svg',
					'1x'    => $plugin::$url . 'images/logo-color.svg',
				]
			];

		}

		return $update_plugins;
	}

	/**
	 * Prepare plugin info.
	 *
	 * @param $res
	 * @param $action
	 * @param $args
	 *
	 * @return bool|stdClass
	 * @since 2.0.2
	 * @access public
	 **/
	public function plugin_info( $res, $action, $args ) {
		global $wp_version;

		/** Reset temporary cache. */
		$this->force_check();

		/** @noinspection PhpUnusedLocalVariableInspection */
		$tmp = $res;

		/** Do nothing if this is not about getting plugin information. */
		if ( $action !== 'plugin_information' ) { return false; }

		/** Do nothing if it is not our plugin. */
		if ( 'ungrabber' !== $args->slug ) { return false; }
		
		/** Trying to get from cache first. */
		$remote = get_transient( 'mdp_ungrabber_upgrade' );

		if ( false === $remote ) {

			/** Download plugin JSON, if cache is empty. */
			$url = $this->plugin_info_url . '?';
			$url .= 'item_id=' . EnvatoItem::get_instance()->get_id();

			/** If this copy is activated. */
			if ( PluginActivation::get_instance()->is_activated() ) {
				/** Add PID to url. */
				$purchase_code = get_option( 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );
				$url .= '&pid=' . $purchase_code;
			}

			/** info.json is the file with the actual plugin information on our server. */
			$remote = wp_remote_get( $url,
				[
					'timeout' => 20,
					'headers' => [
						'Accept' => 'application/json'
					]
				]
			);

			/** Write data to transient. */
			if (
				! is_wp_error( $remote ) &&
	            isset( $remote['response']['code'] ) &&
		        $remote['response']['code'] == 200 &&
		        ! empty( $remote['body']
	        ) ) {
				set_transient( 'mdp_ungrabber_upgrade', $remote, 3600 ); // 1 hour cache.
			}

		}

		if ( $remote['body'] ) {

			$remote = json_decode( $remote['body'] );
			$res = new stdClass();

			$res->name = $remote->name; // Plugin name.
			$res->slug = $remote->slug; // Slug.
			$res->version = $remote->version; // Plugin version.

			$res->last_updated = $remote->last_updated;
			$res->active_installs = $remote->active_installs;

			/** Rating. */
			if( ! empty( $remote->rating ) ) {
				$res->rating = $remote->rating;
				$res->num_ratings = $remote->num_ratings;
			}

			$res->tested = $wp_version; // Tested up to WordPress version.
			$res->requires = $remote->requires; // Requires at least WordPress version.
			$res->requires_php = $remote->requires_php; // The minimum required version of PHP.

			$res->author = '<a href="' . esc_url( $remote->author_homepage ) . '" target="_blank">' . esc_html( $remote->author ) . '</a>';

			/** Prepare contributors. */
			if( ! empty( $remote->contributors ) ) {
				$contributors = [];
				foreach ( $remote->contributors as $contributor ) {
					$contributors[] = get_object_vars( $contributor );
				}

				$res->contributors = $contributors;
			}

			$res->homepage = $remote->homepage;

			/** Download url returned only for valid PID.  */
			if( ! empty( $remote->download_url ) ) {
				$res->download_link = $remote->download_url;
				$res->trunk = $remote->download_url;
			}

			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog,
			);

			/** FAQ section. */
			if( ! empty( $remote->sections->faq ) ) {
				$res->sections['faq'] = $remote->sections->faq;
			}

			/** Reviews section. */
			if( ! empty( $remote->sections->reviews ) ) {
				$res->sections['reviews'] = $remote->sections->reviews;
			}

			/** Screenshots section. */
			if( ! empty( $remote->sections->screenshots ) ) {
				$res->sections['screenshots'] = $remote->sections->screenshots;
			}

			/** Banners. */
			if( ! empty( $remote->banners ) ) {
				$banners = [];
				foreach ( $remote->banners as $key => $banner ) {
					$banners[$key] = $banner;
				}

				$res->banners = $banners;
			}

			return $res;

		}

		return false;

	}

	/**
	 * Reset temporary options on Force update check.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 **/
	public function force_check() {
		if ( isset( $_GET['force-check'] ) AND $_GET['force-check'] === '1' ) {
			$this->reset_cache();
		}
	}

	/**
	 * Reset plugin update cache.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 **/
	public function reset_cache() {
		delete_transient( 'mdp_ungrabber_upgrade' );
		delete_transient( 'mdp_ungrabber_plugin_info' );
	}

	/**
	 * Show custom update messages on plugins page.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function show_plugin_update_message() {

		/** Message for non activated users. */
		if ( ! PluginActivation::get_instance()->is_activated() ) {
			echo '<br /><span class="mdp-line">&nbsp;</span>';
			esc_attr_e( 'Please visit the plugin page on the Envato market to ', 'ungrabber' );
			echo ' <a href="' . EnvatoItem::get_instance()->get_url() . '" target="_blank">';
			esc_attr_e( 'download ', 'ungrabber' );
			echo '</a> ';
			esc_attr_e( 'the latest version.', 'ungrabber' );
		}

	}

	/**
	 * Main PluginUpdater Instance.
	 *
	 * Insures that only one instance of PluginUpdater exists in memory at any one time.
	 *
	 * @static
	 * @return PluginUpdater
	 * @since 2.0.2
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PluginUpdater ) ) {
			self::$instance = new PluginUpdater;
		}

		return self::$instance;
	}

} // End Class PluginUpdater.
