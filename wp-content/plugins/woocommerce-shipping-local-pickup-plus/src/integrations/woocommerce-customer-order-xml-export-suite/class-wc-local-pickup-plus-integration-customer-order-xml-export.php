<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Appointments\Appointment;

/**
 * WooCommerce Customer Order XML Export Suite integration class.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Integration_Customer_Order_XML_Export {


	/**
	 * Initialize XML Export support.
	 *
	 * TODO should the integration with XML Exports expand further, consider that many methods here are similar or shared with the CSV export integration, therefore at some point an abstract class for both would be suitable {FN 2017-05-01}
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// flag some CSV Export admin pages for Local Pickup Plus to output its scripts
		add_filter( 'wc_local_pickup_plus_is_admin_screen', array( $this, 'add_xml_export_admin_screen' ) );

		// add an option to filter orders by pickup location
		add_filter( 'wc_customer_order_xml_export_suite_options', array( $this, 'add_pickup_locations_export_options' ) );

		// export orders associated to pickup locations
		add_filter( 'wc_customer_order_xml_export_suite_query_args', array( $this, 'export_orders_by_pickup_locations' ), 5, 2 );

		// add additional pickup location meta to the shipping line items
		add_filter( 'wc_customer_order_xml_export_suite_order_shipping_item', array( $this, 'add_shipping_item_pickup_data' ), 10, 2 );
	}


	/**
	 * Add CSV Export options page to Local Pickup Plus enabled admin screens.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param bool $is_admin_screen Local Pickup Plus admin screens
	 * @return bool
	 */
	public function add_xml_export_admin_screen( $is_admin_screen ) {
		global $current_screen;

		if ( isset( $current_screen, $current_screen->id ) ) {

			$xml_export_page = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc_customer_order_xml_export_suite' );

			if ( $xml_export_page === $current_screen->id ) {
				$is_admin_screen = true;
			}
		}

		return $is_admin_screen;
	}


	/**
	 * Add option to export orders by associated pickup locations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $options associative array of options
	 * @return array
	 */
	public function add_pickup_locations_export_options( $options ) {

		$new_options = array();
		$lpp_option  = array(
			'name'              => __( 'Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ),
			'id'                => 'wc-local-pickup-plus-pickup-location-search',
			'input_name'        => 'pickup_locations',
			'desc_tip'          => __( 'Orders featuring these pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
			'type'              => 'search_pickup_locations',
			'class'             => 'wc-local-pickup-plus-pickup-location-search show_if_orders',
			'css'               => 'min-width: 250px',
			'custom_attributes' => array(
				'data-allow_clear' => 'true',
				'data-placeholder' => __( 'Leave blank to export orders with any pickup location.', 'woocommerce-shipping-local-pickup-plus' ),
				'multiple'         => 'multiple',
			),
		);

		foreach ( $options as $k => $v ) {

			$new_options[] = $v;

			if ( isset( $v['id'] ) && 'statuses' === $v['id'] ) {
				$new_options[] = $lpp_option;
			}
		}

		return $new_options;
	}


	/**
	 * Export orders associated with pickup locations.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $query_args array of arguments for WP_Query
	 * @param string $export_type either 'orders' or 'customers
	 * @return array
	 */
	public function export_orders_by_pickup_locations( $query_args, $export_type ) {

		if ( 'orders' === $export_type && ! empty( $_POST['export_query']['pickup_locations'] ) ) {

			$filter_pickup_location_ids = array_map( 'absint', is_array(  $_POST['export_query']['pickup_locations'] ) ? $_POST['export_query']['pickup_locations'] : explode( ',', $_POST['export_query']['pickup_locations'] ) );

			if ( ! empty( $filter_pickup_location_ids ) ) {

				$query = new \WP_Query( $query_args );

				if ( ! empty( $query ) ) {

					$post__in = array();
					$orders   = wc_local_pickup_plus()->get_orders_instance();

					foreach ( $query->posts as $order_id ) {

						if ( $found_location_ids = $orders->get_order_pickup_location_ids( $order_id ) ) {

							foreach ( array_values( $found_location_ids ) as $found_location_id ) {

								if ( in_array( $found_location_id, $filter_pickup_location_ids, false ) && ! in_array( $order_id, $post__in, false ) ) {
									$post__in[] = (int) $order_id;
								}
							}
						}
					}

					if ( ! empty( $post__in ) ) {
						$query_args['post__in'] = isset( $query_args['post__in'] ) ? array_merge( (array) $query_args['post__in'], $post__in ) : $post__in;
					}
				}
			}
		}

		return $query_args;
	}


	/**
	 * Add additional shipping line item pickup data to exported orders.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $shipping_item the exported data for the shipping line item
	 * @param array $shipping order shipping item data
	 * @return array updated shipping item data to export
	 */
	public function add_shipping_item_pickup_data( $shipping_item, $shipping ) {

		if (    isset( $shipping_item['Id'], $shipping_item['MethodId'] )
		     && wc_local_pickup_plus_shipping_method_id() === $shipping_item['MethodId'] ) {

			$order_items_handler = wc_local_pickup_plus()->get_orders_instance()->get_order_items_instance();

			$shipping_item['PickupLocationId']      = $order_items_handler->get_order_item_pickup_location_id( $shipping_item['Id'] );
			$shipping_item['PickupLocationName']    = $order_items_handler->get_order_item_pickup_location_name( $shipping_item['Id'] );
			$shipping_item['PickupLocationAddress'] = $order_items_handler->get_order_item_pickup_location_address( $shipping_item['Id'], 'plain' );
			$shipping_item['PickupLocationPhone']   = $order_items_handler->get_order_item_pickup_location_phone( $shipping_item['Id'], false );

			try {
				$appointment                 = new Appointment( $shipping_item['id'] );
				$shipping_item['PickupDate'] = $appointment->get_start()->format( 'Y-m-d' );
			} catch ( \Exception $e ) {
				$shipping_item['PickupDate'] = '';
			}

			$shipping_item['PickupItemsIds'] = $order_items_handler->get_order_item_pickup_items( $shipping_item['Id'] );
		}

		return $shipping_item;
	}


}
