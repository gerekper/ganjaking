<?php
/**
 * WhiteLabel.
 *
 * @package Smush\Core
 */

namespace Smush\Core\Modules\Helpers;

use WPMUDEV_Dashboard;

defined( 'ABSPATH' ) || exit;
/**
 * Class WhiteLabel
 */
class WhiteLabel {
	/**
	 * Whether to activate white label.
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->hide_branding();
	}

	/**
	 * Whether to hide branding or not.
	 *
	 * @return bool
	 */
	public function hide_branding() {
		return apply_filters( 'wpmudev_branding_hide_branding', false );
	}

	/**
	 * Whether to hide doc link or not.
	 *
	 * @return bool
	 */
	public function hide_doc_link() {
		return apply_filters( 'wpmudev_branding_hide_doc_link', false );
	}

	/**
	 * Whether to custom plugin labels or not.
	 *
	 * @param int $plugin_id Plugin id.
	 *
	 * @return bool
	 */
	private function plugin_enabled( $plugin_id ) {
		if ( ! $this->enabled() ) {
			return false;
		}
		if (
			! class_exists( '\WPMUDEV_Dashboard' ) ||
			empty( WPMUDEV_Dashboard::$whitelabel ) ||
			! method_exists( WPMUDEV_Dashboard::$whitelabel, 'get_settings' )
		) {
			return false;
		}
		$whitelabel_settings = WPMUDEV_Dashboard::$whitelabel->get_settings();
		return ! empty( $whitelabel_settings['labels_enabled'] ) && ! empty( $whitelabel_settings['labels_config'][ $plugin_id ] );
	}

	/**
	 * Get custom plugin label.
	 *
	 * @param int $plugin_id Plugin id.
	 * @return bool|string
	 */
	public function get_plugin_name( $plugin_id ) {
		if ( ! $this->plugin_enabled( $plugin_id ) ) {
			return false;
		}
		$whitelabel_settings = WPMUDEV_Dashboard::$whitelabel->get_settings();
		if ( empty( $whitelabel_settings['labels_config'][ $plugin_id ]['name'] ) ) {
			return false;
		}
		return $whitelabel_settings['labels_config'][ $plugin_id ]['name'];
	}

	/**
	 * Get custom plugin logo url.
	 *
	 * @param int $plugin_id Plugin id.
	 * @return bool|string
	 */
	public function get_plugin_logo( $plugin_id ) {
		if ( ! $this->plugin_enabled( $plugin_id ) ) {
			return false;
		}
		$whitelabel_settings = WPMUDEV_Dashboard::$whitelabel->get_settings();
		$plugin_settings     = $whitelabel_settings['labels_config'][ $plugin_id ];

		if ( empty( $plugin_settings['icon_type'] ) ) {
			return false;
		}
		if ( 'link' === $plugin_settings['icon_type'] && ! empty( $plugin_settings['icon_url'] ) ) {
			return $plugin_settings['icon_url'];
		}
		if ( 'upload' === $plugin_settings['icon_type'] && ! empty( $plugin_settings['thumb_id'] ) ) {
			return wp_get_attachment_image_url( $plugin_settings['thumb_id'], 'full' );
		}

		return false;
	}
}