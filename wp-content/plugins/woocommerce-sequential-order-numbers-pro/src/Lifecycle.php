<?php
/**
 * WooCommerce Sequential Order Numbers Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Sequential_Order_Numbers_Pro;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Sequential Order Numbers Pro lifecycle handler.
 *
 * @since 1.13.0
 *
 * @method \WC_Seq_Order_Number_Pro get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.13.1
	 *
	 * @param \WC_Seq_Order_Number_Pro $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.5.5',
			'1.8.1',
			'1.16.0',
		];
	}


	/**
	 * Performs an initial install routine.
	 *
	 * @since 1.13.0
	 */
	protected function install() {
		global $wpdb;

		// initial installation (can't use woocommerce_sequential_order_numbers_pro_version unfortunately as older versions of the plugins didn't use this option)
		if ( false === get_option( 'woocommerce_order_number_start' ) ) {

			// if the free sequential order numbers plugin is installed and active, deactivate it
			if ( $this->get_plugin()->is_plugin_active( 'woocommerce-sequential-order-numbers.php' ) ) {

				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				deactivate_plugins( 'woocommerce-sequential-order-numbers/woocommerce-sequential-order-numbers.php' );
			}

			// initial install, set the order number for all existing orders to the post id:
			// page through the "publish" orders in blocks to avoid out of memory errors
			$offset         = (int) get_option( 'wc_sequential_order_numbers_pro_install_offset', 0 );
			$posts_per_page = 500;

			do {

				// grab a set of order ids
				$order_ids = get_posts( [ 'post_type' => 'shop_order', 'fields' => 'ids', 'offset' => $offset, 'posts_per_page' => $posts_per_page, 'post_status' => 'any' ] );

				// some sort of bad database error: deactivate the plugin and display an error
				if ( $order_ids instanceof \WP_Error ) {

					require_once ABSPATH . 'wp-admin/includes/plugin.php';

					// hardcode the plugin path so that we can use symlinks in development
					deactivate_plugins( 'woocommerce-sequential-order-numbers-pro/woocommerce-sequential-order-numbers-pro.php' );

					/* translators: Placeholders: %1$s - Plugin Name, %2$s - Error message(s) */
					wp_die( sprintf( __( 'Error activating and installing %1$s: %2$s', 'woocommerce-sequential-order-numbers-pro' ),
							'<strong>' . $this->get_plugin()->get_plugin_name() . '</strong>',
							'<ul><li>' . implode( '</li><li>', $order_ids->get_error_messages() ) . '</li></ul>' )
						. '<a href="' . admin_url( 'plugins.php' ) . '">' . esc_html__( '&laquo; Go Back', 'woocommerce-sequential-order-numbers-pro' ) . '</a>' );
				}

				// otherwise go through the results and set the order numbers
				if ( is_array( $order_ids ) ) {

					foreach( $order_ids as $order_id ) {

						$order                  = wc_get_order( $order_id );
						$order_number           = $order->get_meta( '_order_number' );
						$order_number_formatted = $order->get_meta( '_order_number_formatted' );

						// pre-existing order, set _order_number/_order_number_formatted
						if ( '' === $order_number && '' === $order_number_formatted ) {

							$order->update_meta_data( '_order_number', $order_id );
							$order->update_meta_data( '_order_number_formatted', $order_id );
							$order->save_meta_data();

						// an order from the free sequential order number plugin, add the _order_number_formatted field
						} elseif ( '' === $order_number_formatted ) {

							$order->update_meta_data( '_order_number_formatted', $order_number );
							$order->save_meta_data();
						}
					}
				}

				// increment offset
				$offset += $posts_per_page;
				// and keep track of how far we made it in case we hit a script timeout
				update_option( 'wc_sequential_order_numbers_pro_install_offset', $offset );

			// while full set of results returned  (meaning there may be more results still to retrieve)
			} while ( count( $order_ids ) === $posts_per_page );

			// set the best order number start value that we can
			$order_number = (int) $wpdb->get_var( "
				SELECT MAX( CAST( meta_value AS SIGNED ) )
				FROM {$wpdb->postmeta}
				WHERE meta_key='_order_number'
			" );

			add_option( 'woocommerce_order_number_start', $order_number ? $order_number + 1 : 1 );
		}

		// free order number index
		if ( false === get_option( 'woocommerce_free_order_number_start' ) ) {

			add_option( 'woocommerce_free_order_number_start', 1 );
		}
	}


	/**
	 * Updates to version 1.5.5
	 *
	 * In this version we dropped the `order_number_length` frontend setting in favor of accepting zero padding right in the order number start value.
	 *
	 * @since 1.13.1
	 */
	protected function upgrade_to_1_5_5() {

		$order_number_start  = get_option( 'woocommerce_order_number_start' );
		$order_number_length = get_option( 'woocommerce_order_number_length' );

		// option 1: an order number length is configured which is longer than the size of the current order number,
		if ( $order_number_length > strlen( $order_number_start ) ) {

			// update the order number start value to include the padding so it renders correctly on the frontend
			update_option( 'woocommerce_order_number_start', $this->get_plugin()->format_order_number( $order_number_start, '', '', $order_number_length ) );

		// option 2: starting order number is longer than the configured order number length, so update the length setting
		} elseif ( strlen( $order_number_start ) > $order_number_length ) {

			update_option( 'woocommerce_order_number_length', strlen( $order_number_start ) );
		}
	}


	/**
	 * Updates to version 1.8.1
	 *
	 * @since 1.13.1
	 */
	protected function upgrade_to_1_8_1() {

		// option used until support for WC 2.3 was dropped after 1.8.1
		delete_option( 'woocommerce_hash_before_order_number' );
	}


	/**
	 * Updates to version 1.16.0.
	 *
	 * @since 1.16.0
	 */
	protected function upgrade_to_1_16_0() {

		// for merchants updating, set a flag to not show the onboarding tips
		update_option( sprintf( 'wc_%s_onboarding_status', $this->get_plugin()->get_id() ), 'updated' );
	}


}
