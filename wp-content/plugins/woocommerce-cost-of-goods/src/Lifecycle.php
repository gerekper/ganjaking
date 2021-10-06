<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\COG;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 2.8.0
 *
 * @method \WC_COG get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Constructs the class.
	 *
	 * @since 2.8.2
	 *
	 * @param \WC_COG $plugin plugin instance
	 */
	public function __construct( \WC_COG $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.1.0',
			'1.3.3',
			'2.9.1',
		];
	}


	/**
	 * Handles plugin installation routine.
	 *
	 * @since 2.8.0
	 */
	protected function install() {

		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/class-wc-cog-admin.php' );

		$this->install_default_settings( \WC_COG_Admin::get_global_settings() );
	}


	/**
	 * Updates to v1.1.0.
	 *
	 * @since 2.8.2
	 */
	protected function upgrade_to_1_1_0() {

		// page through the variable products in blocks to avoid out of memory errors
		$offset         = (int) get_option( 'wc_cog_variable_product_offset', 0 );
		$posts_per_page = 500;

		do {

			// grab a set of variable product ids
			$product_ids = get_posts( [
				'post_type'      => 'product',
				'fields'         => 'ids',
				'offset'         => $offset,
				'posts_per_page' => $posts_per_page,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => [ 'variable' ],
						'operator' => 'IN',
					],
				],
			] );

			// some sort of bad database error: deactivate the plugin and display an error
			if ( is_wp_error( $product_ids ) ) {

				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				deactivate_plugins( 'woocommerce-cost-of-goods/woocommerce-cost-of-goods.php' );

				/* @type \WP_Error $product_ids */
				/* translators: Placeholders: %s - error messages */
				$error_message = sprintf( __( 'Error upgrading <strong>WooCommerce Cost of Goods</strong>: %s', 'woocommerce-cost-of-goods' ), '<ul><li>' . implode( '</li><li>', $product_ids->get_error_messages() ) . '</li></ul>' );

				wp_die( $error_message . ' <a href="' . admin_url( 'plugins.php' ) . '">' . __( '&laquo; Go Back', 'woocommerce-cost-of-goods' ) . '</a>' );
			}

			// otherwise go through the results and set the min/max/cost
			if ( is_array( $product_ids ) ) {

				foreach ( $product_ids as $product_id ) {

					$cost = \WC_COG_Product::get_cost( $product_id );

					if ( '' === $cost && ( $product = wc_get_product( $product_id ) ) ) {

						// get the minimum and maximum costs associated with the product
						list( $min_variation_cost, $max_variation_cost ) = \WC_COG_Product::get_variable_product_min_max_costs( $product_id );

						$product->update_meta_data( '_wc_cog_cost',               wc_format_decimal( $min_variation_cost ) );
						$product->update_meta_data( '_wc_cog_min_variation_cost', wc_format_decimal( $min_variation_cost ) );
						$product->update_meta_data( '_wc_cog_max_variation_cost', wc_format_decimal( $max_variation_cost ) );
						$product->save_meta_data();
					}
				}
			}

			// increment offset
			$offset += $posts_per_page;

			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_cog_variable_product_offset', $offset );

		} while ( count( $product_ids ) === $posts_per_page );  // while full set of results returned  (meaning there may be more results still to retrieve)
	}


	/**
	 * Updates to v1.3.3.
	 *
	 * In this version we are setting any variable product default costs, at the variation level with an indicator.
	 *
	 * @since 2.8.2
	 */
	protected function upgrade_to_1_3_3() {

		// page through the variable products in blocks to avoid out of memory errors
		$offset         = (int) get_option( 'wc_cog_variable_product_offset2', 0 );
		$posts_per_page = 500;

		do {

			// grab a set of variable product ids
			$product_ids = get_posts( [
				'post_type'      => 'product',
				'fields'         => 'ids',
				'offset'         => $offset,
				'posts_per_page' => $posts_per_page,
				'tax_query'      => [
					[
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => [ 'variable' ],
						'operator' => 'IN',
					],
				],
			] );

			// some sort of bad database error: deactivate the plugin and display an error.
			if ( is_wp_error( $product_ids ) ) {

				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

				// hardcode the plugin path so that we can use symlinks in development.
				deactivate_plugins( 'woocommerce-cost-of-goods/woocommerce-cost-of-goods.php' );

				/* @type \WP_Error $product_ids */
				/* translators: Placeholders: %s - error messages */
				$error_message = sprintf( __( 'Error upgrading <strong>WooCommerce Cost of Goods</strong>: %s', 'woocommerce-cost-of-goods' ), '<ul><li>' . implode( '</li><li>', $product_ids->get_error_messages() ) . '</li></ul>' );

				wp_die( $error_message . ' <a href="' . admin_url( 'plugins.php' ) . '">' . __( '&laquo; Go Back', 'woocommerce-cost-of-goods' ) . '</a>' );

			// ...otherwise go through the results and set the min/max/cost.
			} elseif ( is_array( $product_ids ) ) {

				foreach ( $product_ids as $product_id ) {

					if ( $product = wc_get_product( $product_id ) ) {

						$default_cost = $product->get_meta( '_wc_cog_cost_variable', true, 'edit' );

						// get all child variations
						$children = get_posts( [
							'post_parent'    => $product_id,
							'posts_per_page' => -1,
							'post_type'      => 'product_variation',
							'fields'         => 'ids',
							'post_status'    => 'publish',
						] );

						if ( $children ) {

							foreach ( $children as $child_product_id ) {

								if ( $child_product = wc_get_product( $child_product_id ) ) {

									// cost set at the child level?
									$cost = $child_product->get_meta( '_wc_cog_cost', true, 'edit' );

									if ( '' === $cost && '' !== $default_cost ) {
										// using the default parent cost
										$child_product->update_meta_data( '_wc_cog_cost', wc_format_decimal( $default_cost ) );
										$child_product->update_meta_data( '_wc_cog_default_cost', 'yes' );
									} else {
										// otherwise no default cost
										$child_product->update_meta_data( '_wc_cog_default_cost', 'no' );
									}

									$child_product->save_meta_data();
								}
							}
						}
					}
				}
			}

			// increment offset
			$offset += $posts_per_page;

			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_cog_variable_product_offset2', $offset );

		} while ( count( $product_ids ) === $posts_per_page );  // while full set of results returned  (meaning there may be more results still to retrieve)
	}


	/**
	 * Updates to v2.9.1.
	 *
	 * This upgrade routine removes duplicate _wc_cog_item_cost and _wc_cog_item_total_cost
	 * meta keys from existing order items metadata.
	 *
	 * @since 2.9.1
	 */
	protected function upgrade_to_2_9_1() {
		global $wpdb;

		// delete cache for Profit by product report
		wc_cog()->get_admin_reports_instance()->clear_report_transients();

		// loop through the order items in blocks to avoid memory errors
		$results_per_page = 500;

		do {

			// find the meta_id of duplicate _wc_cog_item_cost and _wc_cog_item_total_cost meta keys
            $query = $wpdb->prepare( "
			SELECT DISTINCT order_item_id, meta_key, MAX(meta_id) AS duplicate_meta_id

				FROM {$wpdb->order_itemmeta}

				WHERE meta_key IN ( '_wc_cog_item_total_cost', '_wc_cog_item_cost' )

				GROUP BY order_item_id, meta_key HAVING count(meta_key) > 1
				LIMIT %d
		", $results_per_page );

			$results = $wpdb->get_results( $query );

			// some sort of bad database error: log the error and skip the rest of the upgrade routine
			if ( $wpdb->last_error ) {

				$this->get_plugin()->log( "Database error trying to find duplicate _wc_cog_item_cost and _wc_cog_item_total_cost meta keys: {$wpdb->last_error}" );

				return;
			}

			// loop through the results to delete the duplicate meta keys
			if ( is_array( $results ) ) {

				foreach ( $results as $item ) {

					$where = [
						'order_item_id' => (int) $item->order_item_id,
						'meta_key'      => $item->meta_key,
						'meta_id'       => (int) $item->duplicate_meta_id,
					];

					$wpdb->delete( $wpdb->order_itemmeta, $where );
				}
			}

		} while ( count( $results ) === $results_per_page ); // while full set of results are returned (meaning there may be more results still to retrieve)
	}


}
