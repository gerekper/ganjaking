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

use SkyVerge\WooCommerce\COG\Utilities\Previous_Orders_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * AJAX handler.
 *
 * @since 2.8.0
 */
class AJAX {


	/**
	 * Adds AJAX actions.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {

		// creates a new background job and starts applying costs to previous orders
		add_action( 'wp_ajax_wc_cog_apply_costs_to_previous_orders', array( $this, 'start_applying_costs_to_previous_orders' ) );
		// gets the status of the background process applying costs to previous orders
		add_action( 'wp_ajax_wc_cog_get_applying_costs_status',      array( $this, 'get_applying_costs_to_previous_orders_status' ) );
	}


	/**
	 * Gets order IDs to apply costs for.
	 *
	 * Helper method for the apply costs to previous orders handler.
	 *
	 * @since 2.8.0
	 *
	 * @param string $which_orders option
	 * @return int[] array of order IDs
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	private function get_order_ids( $which_orders = '' ) {

		$query_args = array(
			'post_type'   => 'shop_order',
			'nopaging'    => true,
			'fields'      => 'ids',
			'post_status' => 'any',
		);

		// if we're only applying costs only to orders that don't already have a cost
		if ( 'orders-without-costs' === $which_orders ) {

			$query_args['meta_query'] = array(
				array(
					'key'     => '_wc_cog_order_total_cost',
					'compare' => 'NOT EXISTS'
				),
			);
		}

		/**
		 * Filters the query used to fetch previous orders to apply costs to.
		 *
		 * @since 2.8.0
		 *
		 * @param array $query_args array of arguments
		 * @param array $posted_data array of user arguments
		 */
		$query_args = (array) apply_filters( 'wc_cost_of_goods_previous_orders_query', $query_args, $_POST );

		/**
		 * Filters the order IDs to set costs for.
		 *
		 * @since 2.8.0
		 *
		 * @param \WP_Error|int[] $order_ids normally an array of IDs
		 * @param array $query_args query arguments that produced the found order IDs
		 * @param array $posted_data data from $_POST
		 */
		$order_ids = apply_filters( 'wc_cost_of_goods_apply_costs_to_previous_orders_ids', get_posts( $query_args ), $query_args, $_POST );

		// some sort of database error
		if ( ! is_array( $order_ids ) ) {

			$error = is_wp_error( $order_ids ) ? implode( '. ', $order_ids->get_error_messages() ) : '';

			/* translators: Placeholder: %s - possible error (could be an empty string) */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( __( 'Database error while applying product costs. %s', 'woocommerce-cost-of-goods' ), ! empty( $error ) ? ' ' . $error : '' ) );
		}

		return array_unique( array_map( 'absint', $order_ids ) );
	}


	/**
	 * Starts applying costs to previous orders.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 */
	public function start_applying_costs_to_previous_orders() {

		check_ajax_referer( 'apply-cost-of-goods', 'security' );

		try {

			// apply to all orders
			$which_orders = 'all-orders';

			// create a background job
			$job = wc_cog()->get_previous_orders_handler_instance()->create_job( [
				'object_ids' => $this->get_order_ids( $which_orders ),
				/** @see Previous_Orders_Handler::process_item() */
				'orders'     => $which_orders,
			] );

			// dispatch the background processor
			wc_cog()->get_previous_orders_handler_instance()->dispatch();

			// send results
			wp_send_json_success( $job );

		} catch ( \Exception $e ) {

			wp_send_json_error( $e->getMessage() );
		}
	}


	/**
	 * Gets a cost-applying job's status.
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @throws \Exception
	 */
	public function get_applying_costs_to_previous_orders_status() {

		check_ajax_referer( 'get-applying-cost-of-goods-status', 'security' );

		try {

			$job_id = isset( $_POST['job_id'] ) ? $_POST['job_id'] : null;

			if ( empty( $job_id ) ) {
				wp_send_json_error( __( 'Applying costs job ID missing.', 'woocommerce-cost-of-goods' ) );
			}

			$job = wc_cog()->get_previous_orders_handler_instance()->get_job( $job_id );

			if ( empty( $job ) ) {
				wp_send_json_error( __( 'Applying costs job process could not be found.', 'woocommerce-cost-of-goods' ) );
			}

			// if loopback connections aren't supported, manually process the job as a batch
			if ( 'completed' !== $job->status && 'processing' !== $job->status && ! wc_cog()->get_previous_orders_handler_instance()->test_connection() ) {
				$job = wc_cog()->get_previous_orders_handler_instance()->process_job( $job );
			}

			if ( 'completed' === $job->status ) {

				// clear report transients
				wc_cog()->get_admin_reports_instance()->clear_report_transients();

				// delete the job
				wc_cog()->get_previous_orders_handler_instance()->delete_job( $job );
			}

			wp_send_json_success( [
				'id'       => $job->id,
				'status'   => $job->status,
				'progress' => isset( $job->progress ) ? $job->progress : 0,
				'total'    => count( $job->object_ids ),
			] );

		} catch ( Framework\SV_WC_Plugin_Exception $e ) {

			wp_send_json_error( $e->getMessage() );
		}
	}


}
