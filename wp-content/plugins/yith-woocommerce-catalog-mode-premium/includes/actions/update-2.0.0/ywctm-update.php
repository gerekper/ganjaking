<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ywctm_upgrade_2_0_0' ) ) {

	/**
	 * Run plugin upgrade to version 2.0.0
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_upgrade_2_0_0() {

		if ( 'yes' === get_transient( 'ywctm_update' ) || YWCTM_VERSION === get_transient( 'ywctm_prune_settings' ) ) {
			WC()->queue()->cancel(
				'ywctm_update_callback',
				array(
					'callback' => 'ywctm_set_version',
					'args'     => array(),
				),
				'ywctm-updates-end'
			);

			return;
		}
		set_transient( 'ywctm_update', 'yes' );

		WC()->queue()->schedule_recurring(
			time(),
			10,
			'ywctm_update_callback',
			array(
				'callback' => 'ywctm_set_version',
				'args'     => array(),
			),
			'ywctm-updates-end'
		);

		WC()->queue()->schedule_single(
			time(),
			'ywctm_update_callback',
			array(
				'callback' => 'ywctm_upgrade_settings_premium',
				'args'     => array(),
			),
			'ywctm-updates'
		);

		WC()->queue()->schedule_single(
			time() + 604800,
			'ywctm_update_callback',
			array(
				'callback' => 'ywctm_prune_old_settings',
				'args'     => array(),
			),
			'ywctm-updates-prune'
		);

	}

	add_action( 'admin_init', 'ywctm_upgrade_2_0_0' );
}

if ( ! function_exists( 'ywctm_update_callback' ) ) {

	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @param   $callback   string
	 * @param   $args       array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_update_callback( $callback, $args ) {

		if ( is_callable( $callback ) ) {
			call_user_func( $callback, $args );
		}
	}

	add_action( 'ywctm_update_callback', 'ywctm_update_callback', 10, 2 );
}

if ( ! function_exists( 'ywctm_upgrade_settings_premium' ) ) {

	/**
	 * Upgrade settings to version 2.0.0
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_upgrade_settings_premium() {

		update_option( 'ywctm_apply_users', 'all' );

		//Disable shop settings
		if ( 'yes' === get_option( 'ywctm_hide_cart_header' ) ) {
			update_option( 'ywctm_disable_shop', 'yes' );
		}

		//Add to cart and exclusions settings
		$hide_single = get_option( 'ywctm_hide_add_to_cart_single' );
		$hide_loop   = get_option( 'ywctm_hide_add_to_cart_loop' );
		if ( 'no' === $hide_loop && 'no' === $hide_single ) {
			$atc_option = array(
				'action' => 'hide',
				'where'  => 'all',
				'items'  => 'all',
			);
		} else {
			switch ( true ) {
				case 'no' === $hide_loop && 'yes' === $hide_single:
					$where = 'product';
					break;
				case 'yes' === $hide_loop && 'no' === $hide_single:
					$where = 'shop';
					break;
				default:
					$where = 'all';
			}
			$atc_option = array(
				'action' => 'hide',
				'where'  => $where,
				'items'  => 'all',
			);
		}
		update_option( 'ywctm_hide_add_to_cart_settings', $atc_option );

	}
}

if ( ! function_exists( 'ywctm_prune_old_settings' ) ) {

	/**
	 * Remove old settings
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_prune_old_settings() {

		delete_option( 'ywctm_enable_plugin' );
		delete_option( 'ywctm_hide_cart_header' );
		delete_option( 'ywctm_exclude_hide_add_to_cart_reverse' );
		delete_option( 'ywctm_exclude_hide_add_to_cart' );
		delete_option( 'ywctm_hide_add_to_cart_single' );
		delete_option( 'ywctm_hide_add_to_cart_loop' );
		delete_transient( 'ywctm_prune_settings' );
	}
}

if ( ! function_exists( 'ywctm_set_version' ) ) {

	/**
	 * Set plugin version
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_set_version() {

		$functions = WC()->queue()->search(
			array(
				'group'  => 'ywctm-updates',
				'status' => 'pending',
			)
		);

		if ( empty( $functions ) ) {
			update_option( 'ywctm_update_version', YWCTM_VERSION );
			delete_transient( 'ywctm_update' );
			set_transient( 'ywctm_prune_settings', YWCTM_VERSION );
		}
	}
}
