<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Order Processor class for processing parsed order data and saving it to database
 *
 * @since 3.3.0
 */
class WC_CSV_Import_Suite_Order_Processor {


	/** @var array refunded order item ids */
	private $refunded_item_order_ids = array();

	/** @var \WC_CSV_Import_Suite_Order_Import order importer instance */
	private $importer;


	/**
	 * Constructs and initializes the processor.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {

		$this->importer = wc_csv_import_suite()->get_importers_instance()->get_importer( 'woocommerce_order_csv' );

		// provide some base custom order number functionality, while allowing 3rd party plugins with custom order number functionality to unhook this and provide their own logic
		add_action( 'woocommerce_set_order_number', [ $this, 'woocommerce_set_order_number' ], 10, 3 );
	}


	/**
	 * Processes an order.
	 *
	 * @since 3.3.0
	 *
	 * @param array $data parsed order data, ready for processing, compatible with {@see wc_create_order()} or {@see wc_update_order()}
	 * @param array $options options (optional)
	 * @param array $raw_headers raw headers (optional)
	 * @return int|null
	 */
	public function process_order( $data, $options = [], $raw_headers = [] ) {

		// default options
		$options = (array) wp_parse_args( $options, [
			'recalculate_totals' => false,
			'merge'              => false,
		] );

		$merging      = $options['merge'] && isset( $data['id'] ) && $data['id'];
		$dry_run      = isset( $options['dry_run'] ) ? $options['dry_run'] : false;
		$reduce_stock = isset( $options['reduce_product_stock'] ) && wc_string_to_bool( $options['reduce_product_stock'] );
		$order_id     = null;

		// handles completed orders stock reduction
		add_filter( 'woocommerce_payment_complete_reduce_order_stock', [ $this, 'maybe_reduce_stock_level' ], 999, 2 );
		// enforces a user preference from import setting, reducing or prevent reducing the stock for completed orders
		add_filter( 'wc_csv_import_suite_reduce_order_stock_level', $reduce_stock ? '__return_true' : '__return_false' );

		wc_csv_import_suite()->log( __( '> Processing order', 'woocommerce-csv-import-suite' ) );

		$order_identifier = $this->importer->get_item_identifier( $data );

		if ( ! $dry_run ) {
			wc_transaction_query( 'start' );
		}

		try {

			$send_order_emails = ! empty( $options['send_order_emails'] );

			if ( ! $send_order_emails ) {
				$this->disable_order_emails();
			}

			if ( $merging ) {

				wc_csv_import_suite()->log( sprintf( __( '> Merging order %s.', 'woocommerce-csv-import-suite' ), $order_identifier ) );

				if ( ! $dry_run ) {
					$order_id = $this->update_order( $data['id'], $data, $options );
				}

			} else {

				// insert customer
				wc_csv_import_suite()->log( sprintf( __( '> Inserting order %s', 'woocommerce-csv-import-suite' ), esc_html( $order_identifier ) ) );

				if ( ! $dry_run ) {
					$order_id = $this->create_order( $data, $options );
				}
			}

			if ( ! $send_order_emails ) {
				$this->enable_order_emails();
			}

			// import failed
			if ( ! $dry_run && is_wp_error( $order_id ) ) {

				$this->importer->add_import_result( 'failed', $order_id->get_error_message() );

				return null;
			}

			// TODO: is that OK to log and return as order_id in case of dry run?
			if ( $dry_run ) {
				$order_id = $merging ? $data['id'] : 9999;
			}

			if ( ! $dry_run ) {
				wc_transaction_query( 'commit' );
			}

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {

			if ( ! $dry_run ) {
				wc_transaction_query( 'rollback' );
			}

			$this->importer->add_import_result( 'failed', $e->getMessage() );

			return null;
		}

		// no order identifier provided in CSV, use the order ID
		if ( ! $order_identifier ) {
			$order_identifier = $order_id;
		}

		if ( $merging ) {
			wc_csv_import_suite()->log( sprintf( __( '> Finished merging order %s.', 'woocommerce-csv-import-suite' ), $order_identifier ) );
			$this->importer->add_import_result( 'merged' );
		} else {
			wc_csv_import_suite()->log( sprintf( __( '> Finished importing order %s.', 'woocommerce-csv-import-suite' ), $order_identifier ) );
			$this->importer->add_import_result( 'inserted' );
		}

		return $order_id;
	}


	/**
	 * Creates an order.
	 *
	 * Based on {@see \WC_API_Orders::create_order}
	 * In 3.3.0 moved to {@see \WC_CSV_Import_Suite_Order_Processor} from {@see \WC_CSV_Import_Suite_Order_Import}
	 *
	 * @since 3.0.0
	 *
	 * @param array $data
	 * @param array $options
	 * @return int|\WP_Error
	 */
	private function create_order( $data, $options ) {

		try {

			/**
			 * Filters new order data from CSV
			 *
			 * @since 3.0.0
			 * @param array $data parsed order data
			 * @param array $options import options
			 * @param \WC_CSV_Import_Suite_Order_Import $importer order importer instance
			 */
			$data = apply_filters( 'wc_csv_import_suite_import_order_data', $data, $options, $this->importer );

			// default order args, note that status is checked for validity in wc_create_order()
			$default_order_args = array(
				'customer_note' => isset( $data['note'] ) ? $data['note'] : '',
				'customer_id'   => $data['customer_id'],
				'created_via'   => 'csv-import',
			);

			// create the pending order
			$order = $this->create_base_order( $default_order_args, $data );

			if ( is_wp_error( $order ) ) {

				$messages = $this->implode_wp_error_messages( $order );

				/* translators: Placeholders: %1$s - order identifier, %2$s - error message */
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_create_order', sprintf( __( 'Failed to insert order %1$s: %2$s;', 'woocommerce-csv-import-suite' ), esc_html( $post['order_number_formatted'] ), esc_html( $messages ) ) );
			}

			// add order notes - only when not merging, as order notes
			// are not keyed
			if ( ! empty( $data['order_notes'] ) ) {
				foreach ( $data['order_notes'] as $order_note ) {
					$order->add_order_note( $order_note );
				}
			}

			// do our best to provide some custom order number functionality while
			// also allowing 3rd party plugins to provide their own custom order
			// number facilities
			if ( ! empty( $data['order_number_formatted'] ) ) {

				do_action( 'woocommerce_set_order_number', $order, $data['order_number'], $data['order_number_formatted'] );
				$order->add_order_note( sprintf( __( 'Original order #%s', 'woocommerce-csv-import-suite' ), $data['order_number_formatted'] ) );

				// get the order so we can display the correct order number
				$order = wc_get_order( $order->get_id() );
			}

			// update order data, such as meta and items
			$this->update_order_data( $order, $data, $options );

			// calculate totals and set them
			if ( $options['recalculate_totals'] ) {
				$order->calculate_taxes();
				$order->calculate_totals();
			}

			// record the product sales - only recorded when creating an order,
			// following WC core.
			wc_update_total_sales_counts( $order->get_id() );

			// this will also save the order in WC 3+
			$order->update_status( $data['status'] );

			/**
			 * Triggered after an order has been created via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Order ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_create_order', $order->get_id(), $data, $options );

			return $order->get_id();

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {

			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Update an order
	 *
	 * Based on \WC_API_Orders::update_order
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param int $id
	 * @param array $data
	 * @param array $options
	 * @return int|WP_Error
	 */
	private function update_order( $id, $data, $options ) {

		try {

			$order      = wc_get_order( $id );
			$order_args = [ 'order_id' => $order->get_id() ];

			// customer note
			if ( isset( $data['note'] ) ) {
				$order_args['customer_note'] = $data['note'];
			}

			// update the order post to set customer note/date/modified date
			$order = wc_update_order( $order_args );

			if ( is_wp_error( $order ) ) {

				$messages         = $this->implode_wp_error_messages( $order );
				$order_identifier = $this->importer->get_item_identifier( $data );

				/* translators: Placeholders: %1$s - order identifier, %2$s - error message(s) */
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_update_order', sprintf( __( 'Failed to update order %1$s: %2$s;', 'woocommerce-csv-import-suite' ), esc_html( $order_identifier ), esc_html( $messages ) ) );
			}

			// customer id
			if ( isset( $data['customer_id'] ) && $data['customer_id'] !== $order->get_user_id() ) {
				try {
					$order->set_customer_id( $data['customer_id'] );
				} catch ( \Exception $e ) {
					throw new WC_CSV_Import_Suite_Import_Exception( $e->getCode(), $e->getMessage() );
				}
			}

			// do our best to provide some custom order number functionality while
			// also allowing 3rd party plugins to provide their own custom order
			// number facilities
			if ( ! empty( $data['order_number_formatted'] ) ) {

				$previous_order_number = $order->get_order_number();

				/**
				 * Fires when setting order number during CSV Import
				 *
				 * Actors can use this hook to customize the order number during import
				 *
				 * @since 1.0.0
				 * @param \WC_Order $order the order
				 * @param $order_number order number from the CSV file
				 * @param $order_number_formatted formatted order number from the CSV file
				 */
				do_action( 'woocommerce_set_order_number', $order, $data['order_number'], $data['order_number_formatted'] );

				// get the order so we can display the correct order number
				$order = wc_get_order( $order->get_id() );

				// only add a note when the order number actually changed
				if ( $order->get_order_number() != $previous_order_number ) {
					$order->add_order_note( sprintf( __( 'Original order #%s', 'woocommerce-csv-import-suite' ), $data['order_number_formatted'] ) );
				}

			}

			// update order data, such as meta and items
			$this->update_order_data( $order, $data, $options );

			// calculate totals and set them
			if ( $options['recalculate_totals'] ) {
				$order->calculate_taxes();
				$order->calculate_totals();
			}

			// update order status - note that this is done _after_ updating order data, so that we have a chance to
			// grant download permissions before WC automatically sets download permissions as already granted when
			// moving the order to processing or completed status
			if ( ! empty( $data['status'] ) ) {
				$order->update_status( $data['status'], isset( $data['status_note'] ) ? $data['status_note'] : '' );
			}

			$order->save();

			/**
			 * Fires after an order has been updated via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Order ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_update_order', $order->get_id(), $data, $options );

			return $order->get_id();

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {

			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Updates order data.
	 *
	 * Based on {@see \WC_API_Customers::update_customer_data()}
	 * In 3.3.0 moved to {@see \WC_CSV_Import_Suite_Order_Processor} from {@see \WC_CSV_Import_Suite_Order_Import}
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @param array $data import data
	 * @param array $options import options
	 * @throws \WC_CSV_Import_Suite_Import_Exception
	 */
	private function update_order_data( WC_Order $order, $data, $options ) {

		$order_id = $order->get_id();
		$merging  = $options['merge'] && isset( $data['id'] ) && $data['id'];

		try {

			// order date
			if ( isset( $data['date'] ) && $data['date'] ) {

				$order->set_date_created( $data['date'] );

				if ( $order->is_paid() ) {
					$order->set_date_paid( $data['date'] );
				}
			}

			// if an order paid date is specified in the meta, it should be set with its specific setter rather than purely meta data (see later below) to persist
			if ( isset( $data['order_meta']['_date_paid'] ) ) {
				$order->set_date_paid( $data['order_meta']['_date_paid'] ); // timestamp
			} elseif ( isset( $data['order_meta']['_paid_date'] ) ) {
				$order->set_date_paid( $data['order_meta']['_paid_date'] ); // datetime
			}

			if ( isset( $data['terms'] ) && is_callable( [ $this, 'process_terms' ] ) ) {
				$this->process_terms( $order->get_id(), $data['terms'] );
			}

			// set order addresses
			foreach ( array( 'billing', 'shipping' ) as $address_type ) {

				// don't set addresses if they're empty - this ensures partial updates/merges
				// are supported and won't wipe out order addresses
				if ( ! empty( $data[ $address_type . '_address' ] ) ) {
					$order->set_address( $data[ $address_type . '_address' ], $address_type );
				}
			}

			// clear any previously set refunded order item ids
			$this->refunded_item_order_ids = array();

			$items_changed = false;

			// set order lines
			foreach ( $this->importer->line_types as $line_type => $line ) {


				// don't set lines if they're empty - this ensures partial updates/merges
				// are supported and won't wipe out order lines
				if ( ! empty( $data[ $line ] ) && is_array( $data[ $line ] ) ) {
					$this->process_items( $order, $data[ $line ], $line_type, $merging );
					$items_changed = true;
				}
			}

			// bust order items cache, so that $order->get_items() will load the updated order items
			if ( $items_changed ) {
				wp_cache_delete( "order-items-{$order_id}", 'orders' );
			}

			// set order currency
			if ( isset( $data['currency'] ) ) {
				$order->set_currency( $data['currency'] );
			}

			// grant downloadable product permissions
			if ( isset( $data['order_meta']['_download_permissions_granted'] ) && $data['order_meta']['_download_permissions_granted'] ) {
				wc_downloadable_product_permissions( $order_id, ! $merging ); // force grant permissions when inserting
			}

			// set order meta
			if ( isset( $data['order_meta'] ) && is_array( $data['order_meta'] ) ) {
				$this->set_order_meta( $order_id, $data['order_meta'] );
			}

			// set the paying customer flag on the user meta if applicable
			$paid_statuses = array( 'processing', 'completed', 'refunded' );

			if ( ! empty( $data['customer_id'] ) && ! empty( $data['status'] ) && in_array( $data['status'], $paid_statuses ) ) {
				update_user_meta( $data['customer_id'], 'paying_customer', 1 );
			}

			// process refunds
			if ( ! empty( $data['refunds'] ) ) {

				// remove previous refunds
				foreach ( $order->get_refunds() as $refund ) {

					$refund_id = $refund->get_id();

					wc_delete_shop_order_transients( $refund_id );
					wp_delete_post( $refund_id, true );
				}

				foreach ( $data['refunds'] as $refund_data ) {

					// try mapping temp refunded item ids to real order item ids
					if ( ! empty( $refund_data['line_items'] ) ) {
						foreach ( $refund_data['line_items'] as $key => $refunded_item ) {

							if ( isset( $refunded_item['refunded_item_temp_id'] ) ) {

								$temp_id = $refunded_item['refunded_item_temp_id'];

								// get the real order item id for this refunded item
								$order_item_id = isset( $this->refunded_item_order_ids[ $temp_id ] ) ? $this->refunded_item_order_ids[ $temp_id ] : null;

								if ( $order_item_id ) {
									$refund_data['line_items'][ $order_item_id ] = $refunded_item;
									unset( $refund_data['line_items'][ $key ] );
								}
							}
						}
					}

					if ( isset( $refund_data['date'] ) ) {
						$refund_data['date'] = $this->importer->parse_order_date( $refund_data );
					} else {
						$refund_data['date'] = time();
					}

					// WC will try to update the order status rather than listen to our CSV file when creating the refund.
					// Disable it to keep the status at whatever it currently is in the CSV.
					add_filter( 'woocommerce_order_fully_refunded_status', '__return_false' );

					// `date_created` will be supported in WC 3.1+, change this version check then {BR 2017-05-25}
					$date_key      = 'date_created';
					$refunded_date = $refund_data['date'];

					$refund = wc_create_refund( array(
						'order_id'   => $order_id,
						'amount'     => $refund_data['amount'],
						'reason'     => $refund_data['reason'],
						'line_items' => $refund_data['line_items'],
						$date_key    => $refunded_date,
					) );

					if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.1' ) ) {

						if ( ! is_wp_error( $refund ) ) {
							$refund->set_date_created( $refund_data['date'] );
							$refund->set_currency( $data['currency'] );
							$refund->save();
						}
					}
				}
			}

			wc_delete_shop_order_transients( $order_id );

			/**
			 * Fires after order data has been updated via CSV.
			 *
			 * This will be triggered for both new and updated orders.
			 *
			 * @since 3.0.0
			 *
			 * @param int $id the order ID
			 * @param array $data order data
			 * @param array $options import options
			 */
			do_action( 'wc_csv_import_suite_update_order_data', $order_id, $data, $options );

		} catch ( \Exception $e ) {

			throw new \WC_CSV_Import_Suite_Import_Exception( $e->getCode(), $e->getMessage() );
		}
	}


	/**
	 * Creates a new WC_Order
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param $args array
	 * @return WC_Order
	 */
	private function create_base_order( $args, $data ) {
		return wc_create_order( $args );
	}


	/**
	 * Adds/updates order meta data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param int $order_id valid order ID
	 * @param array $order_meta order meta in array( 'meta_key' => 'meta_value' ) format
	 */
	private function set_order_meta( $order_id, $order_meta ) {

		foreach ( $order_meta as $meta_key => $meta_value ) {

			if ( is_string( $meta_key ) ) {
				update_post_meta( $order_id, $meta_key, $meta_value );
			}
		}
	}


	/**
	 * Process items for an order
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order The order object the items should be attached to
	 * @param array items Parsed items from CSV.
	 * @param string $type Optional. Line items type. Defaults to 'line_item'.
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 */
	private function process_items( WC_Order $order, $items = array(), $type = 'line_item', $merging = false ) {

		if ( empty( $items ) ) {
			return;
		}

		if ( $merging ) {
			$existing = $order->get_items( $type );
			$updated  = array();
		}

		foreach ( $items as $item ) {

			$order_item_id = null;

			if ( $merging ) {
				$order_item_id = isset( $item['order_item_id'] ) && $item['order_item_id'] ? $item['order_item_id'] : null;
			}

			// update existing item
			if ( $merging && $order_item_id ) {

				$this->update_order_item( $order, $order_item_id, $item, $type );

				// Mark item as updated - even if it failed, as otherwise it would be
				// deleted from the order below
				$updated[] = $order_item_id;
			}

			// insert as new
			else {
				$order_item_id = $this->add_order_item( $order, $item, $type );
			}

			// if create/update was successful, update order item meta as well
			if ( $order_item_id ) {
				$this->update_order_item_meta( $order_item_id, $item, $type );

				// store a mapping/reference to the real order item id
				if ( isset( $item['refunded_item_temp_id'] ) ) {
					$this->refunded_item_order_ids[ $item['refunded_item_temp_id'] ] = $order_item_id;
				}
			}
		}

		// delete existing items that were not present in the CSV when merging
		if ( $merging ) {
			$this->delete_removed_items( $existing, $updated );
		}
	}


	/**
	 * Adds an item to the provided order
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order
	 * @param array $item Parsed item data from CSV
	 * @param string $type Line item type
	 * @return int|false ID of the inserted order item, false on failure
	 */
	private function add_order_item( WC_Order $order, $item, $type ) {

		$result = false;

		switch ( $type ) {

			case 'line_item':
				$product = $this->get_product_for_item( $item );
				$args    = $this->prepare_product_args( $item );

				$quantity_key = 'quantity';
				$result       = $order->add_product( $product, $args[ $quantity_key ], $args );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot add order item "%s".', 'woocommerce-csv-import-suite' ), esc_html( $identifier ) ) );
				}
			break;

			case 'shipping':
				$args = array(
					'order_item_name' => $item['method_title'],
					'order_item_type' => 'shipping',
				);

				// we're using wc_add_order_item instead of $order->add_shipping because
				// we do not want the order total to be recalculated
				$result = wc_add_order_item( $order->get_id(), $args );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot add shipping method "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['title'] ) ) );
				}
			break;

			case 'tax':

				$args = array(
					'order_item_name' => $item['code'],
					'order_item_type' => 'tax',
				);

				$result = wc_add_order_item( $order->get_id(), $args );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot add tax "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['label'] ) ) );
				}
			break;

			case 'coupon':

				$coupon = new WC_Order_Item_Coupon();
				$coupon->set_props( [
					'code'         => $item['code'],
					'discount'     => $item['amount'],
					'discount_tax' => 0,
					'order_id'     => $order->get_id(),
				] );
				$coupon->save();

				$order->add_item( $coupon );

				$result = $coupon->get_id();

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot add coupon "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['code'] ) ) );
				}
			break;

			case 'fee':

				$taxable   = false;
				$tax       = 0;
				$tax_data  = [];
				$tax_class = '';

				// if taxable, tax class and total are required
				if ( isset( $item['taxable'] ) && $item['taxable'] ) {

					$taxable   = true;
					$tax_class = $item['tax_class'];

					if ( isset( $item['total_tax'] ) ) {
						$tax = isset( $item['total_tax'] ) ? (float) $item['total_tax'] : 0;
					}

					if ( isset( $item['tax_data'] ) ) {
						$tax_data = isset( $item['tax_data']['total'] ) ? $item['tax_data']['total'] : $item['tax_data'];
						$tax      = array_sum( $tax_data );
					}
				}

				$fee = new \WC_Order_Item_Fee();
				$fee->set_props( [
					'name'      => $item['title'],
					'tax_class' => $taxable ? $tax_class : 0,
					'total'     => isset( $item['total'] ) ? floatval( $item['total'] ) : 0,
					'total_tax' => $tax,
					'taxes'     => [
						'total' => $tax_data,
					],
					'order_id'  => $order->get_id(),
				] );
				$fee->save();

				$order->add_item( $fee );

				$result = $fee->get_id();

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot add fee "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['title'] ) ) );
				}

			break;
		}

		// store original order item ID
		if ( $result && isset( $item['order_item_id'] ) && $item['order_item_id'] > 0 ) {
			wc_update_order_item_meta( $result, '_original_order_item_id', $item['order_item_id'] );
		}

		return $result;
	}


	/**
	 * Updates an order item
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order WC_Order instance
	 * @param int $order_item_id Order item ID to update
	 * @param array $item Parsed item data from CSV
	 * @param string $type Line item type
	 * @return int|false ID of the updated order item, false on failure
	 */
	private function update_order_item( WC_Order $order, $order_item_id, $item, $type ) {

		$result = false;

		switch ( $type ) {

			case 'line_item':

				$product    = $this->get_product_for_item( $item );
				$args       = $this->prepare_product_args( $item );
				$order_item = $order->get_item( $order_item_id );

				// we couldn't find the item on the order
				// skip this one since we can't update it
				if ( ! $order_item ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: order item %d does not exist.', 'woocommerce-csv-import-suite' ), $order_item_id ) );
					break;
				}

				// handle quantity if set
				if ( isset( $args['quantity'] ) ) {

					if ( isset( $args['subtotal'] ) ) {
						$args['subtotal'] = $args['subtotal'] ? $args['subtotal'] : wc_get_price_excluding_tax( $product, array( 'qty' => $args['quantity'] ) );
					}
					$args['total'] = $args['total'] ? $args['total'] : wc_get_price_excluding_tax( $product, array( 'qty' => $args['quantity'] ) );
				}

				$order_item->set_order_id( $order->get_id() );
				$order_item->set_props( $args );

				if ( $order_item instanceof \WC_Order_Item_Product ) {
					$order_item->set_backorder_meta();
				}

				$result = $order_item->save();

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot update order item %d.', 'woocommerce-csv-import-suite' ), $order_item_id ) );
				}

			break;

			case 'shipping':
				$args   = array( 'order_item_name' => $item['method_title'] );
				$result = wc_update_order_item( $order_item_id, $args );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot update shipping method "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['title'] ) ) );
				}
			break;

			case 'tax':
				$args   = array( 'order_item_name' => $item['code'] );
				$result = wc_update_order_item( $order_item_id, $args );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot update tax "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['label'] ) ) );
				}
			break;

			case 'coupon':
				$args = [
					'code'            => $item['code'],
					'discount_amount' => $item['amount'],
					'order_id'        => $order->get_id(),
				];

				$coupon = $order->get_item( $order_item_id );

				if ( $coupon->is_type( 'coupon' ) ) {

					$coupon->set_props( $args );
					$result = $coupon->save();
				}

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot merge coupon "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['code'] ) ) );
				}
			break;

			case 'fee':
				$args = [
					'name'      => $item['title'],
					'total'     => $item['total'],
					'total_tax' => $item['total_tax'],
					'tax_class' => isset( $item['tax_class'] ) ? $item['tax_class'] : '',
					'order_id'  => $order->get_id(),
				];

				$fee = $order->get_item( $order_item_id );

				if ( $fee->is_type( 'fee' ) ) {

					$fee->set_props( $args );
					$result = $fee->save();
				}

				// update order item in order object (otherwise the changed values are reverted when the order is saved)
				$order->add_item( $fee );

				if ( ! $result ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Warning: cannot merge fee "%s".', 'woocommerce-csv-import-suite' ), esc_html( $item['title'] ) ) );
				}
			break;
		}

		return $result;
	}


	/**
	 * Updates order item meta after adding or inserting an item
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param int $order_item_id
	 * @param array $item Parsed item data from CSV
	 * @param string $type Line item type
	 */
	private function update_order_item_meta( $order_item_id, $item, $type ) {

		if ( empty( $item ) || ! $type ) {
			return;
		}

		switch ( $type ) {

			case 'line_item':

				if ( ! empty( $item['meta'] ) ) {
					foreach ( $item['meta'] as $meta_key => $meta_value ) {
						// replace encoded quote chars since we could have encoded them in JSON export formats
						$meta_value = str_replace( '&quot;', '"', $meta_value );
						wc_update_order_item_meta( $order_item_id, $meta_key, $meta_value );
					}
				}

				if ( isset( $item['tax_data'] ) ) {
					wc_update_order_item_meta( $order_item_id, '_line_tax_data', $item['tax_data'] );
				}
			break;

			case 'tax':
				wc_update_order_item_meta( $order_item_id, 'rate_id',             $item['rate_id'] );
				wc_update_order_item_meta( $order_item_id, 'label',               $item['label'] );
				wc_update_order_item_meta( $order_item_id, 'compound',            $item['compound'] );
				wc_update_order_item_meta( $order_item_id, 'tax_amount',          $item['tax_amount'] );
				wc_update_order_item_meta( $order_item_id, 'shipping_tax_amount', $item['shipping_tax_amount'] );
			break;

			case 'shipping':
				wc_update_order_item_meta( $order_item_id, 'method_id', $item['method_id'] );
				wc_update_order_item_meta( $order_item_id, 'cost',      $item['cost'] );

				if ( isset( $item['taxes'] ) ) {
					wc_update_order_item_meta( $order_item_id, 'taxes', $item['taxes'] );
				}
			break;

			case 'fee':
				if ( isset( $item['tax_data'] ) ) {
					wc_update_order_item_meta( $order_item_id, '_line_tax_data', $item['tax_data'] );
				}
			break;
		}
	}


	/**
	 * Deletes items that were not present in updated CSV when merging
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from
	 * \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param array $existing existing order items
	 * @param array $updated updated order items
	 */
	private function delete_removed_items( $existing, $updated ) {

		if ( count( $existing ) != count( $updated ) ) {

			// if this order item was not updated, it must be removed from the order
			foreach ( $existing as $order_item_id => $item ) {

				if ( ! in_array( $order_item_id, $updated ) ) {
					wc_delete_order_item( $order_item_id );
				}
			}
		}
	}


	/**
	 * Returns product for parsed line item from CSV
	 *
	 * This methods returns a 'bogus' product with id 0 for unknown products
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from
	 * \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Parsed line item data from CSV
	 * @return \WC_Product product for the item
	 */
	private function get_product_for_item( $item ) {

		$product = null;

		if ( isset( $item['product_id'] ) && $item['product_id'] ) {

			$product = wc_get_product( $item['product_id'] );

			// if the parent does not exist, this may be an orphaned variation
			if ( $product && $product->is_type( 'variation' ) && ! wc_get_product( $product->get_parent_id() ) ) {
				 $product = null;
			}
		}

		if ( ! $product ) {

			$product           = new \WC_Product( null );
			$product_name      = isset( $item['name'] ) ? $item['name'] : __( 'Unknown product', 'woocommerce-csv-import-suite' );
			$product_tax_class = isset( $item['tax_class'] ) ? $item['tax_class'] : '';

			$product->set_id( 0 );
			$product->set_tax_class( $product_tax_class );
			$product->set_name( $product_name );
		}

		return $product;
	}


	/**
	 * Prepares product arguments to be consumed by $order->add/update_product
	 *
	 * Based on WC_API_Orders::set_line_item()
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from
	 * \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param array $item
	 * @return array Product / line item arguments, ready to be used by
	 *               $order->add/update_product
	 */
	private function prepare_product_args( $item ) {

		$args = array();

		if ( isset( $item['quantity'] ) ) {

			$quantity_key = 'quantity';

			$args[ $quantity_key ] = $item['quantity'];
		}

		// total
		if ( isset( $item['total'] ) ) {
			$args['totals']['total'] = floatval( $item['total'] );
		}

		// total tax
		if ( isset( $item['total_tax'] ) ) {
			$args['totals']['tax'] = floatval( $item['total_tax'] );
		}

		// subtotal
		if ( isset( $item['subtotal'] ) ) {
			$args['totals']['subtotal'] = floatval( $item['subtotal'] );
		}

		// subtotal tax
		if ( isset( $item['subtotal_tax'] ) ) {
			$args['totals']['subtotal_tax'] = floatval( $item['subtotal_tax'] );
		}

		// tax data
		if ( isset( $item['tax_data'] ) ) {
			$args['totals']['tax_data'] = $item['tax_data'];
		}

		if ( isset( $args['totals'] ) ) {

			foreach ( $args['totals'] as $key => $value ) {
				if ( 'tax' === $key ) {
					$args['total_tax'] = $value;
				} elseif ( 'tax_data' === $key ) {
					$args['taxes'] = $value;
				} else {
					$args[ $key ] = $value;
				}
			}

			unset( $args['totals'] );
		}

		// variations
		if ( isset( $item['variations'] ) ) {
			$args['variation'] = $item['variations'];
		}

		return $args;
	}


	/**
	 * Action to set the custom order number.
	 *
	 * This can be modified by 3rd party plugins via the applied filters, or replaced wholesale if entirely different
	 * logic is required.
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order order object
	 * @param int $order_number incrementing order number piece
	 * @param string $order_number_formatted formatted order number piece
	 */
	public function woocommerce_set_order_number( $order, $order_number, $order_number_formatted ) {
		// the best we can do to tie the newly imported order to the old, is to
		// at least record the order number internally (allowing 3rd party plugins
		// to specify the order number meta field name), and set a visible order
		// note indicating the original order number.	If the user has a custom order
		// number plugin like the Sequential Order Number Pro installed, then things
		// will be even cleaner on the backend
		update_post_meta( $order->get_id(), apply_filters( 'woocommerce_order_number_meta_name', '_order_number' ), $order_number );
		update_post_meta( $order->get_id(), apply_filters( 'woocommerce_order_number_formatted_meta_name', '_order_number_formatted' ), $order_number_formatted );
	}


	/**
	 * Handles stock level reduction for imported orders.
	 *
	 * @see \WC_CSV_Import_Suite_Order_Processor::process_order()
	 * @internal
	 *
	 * @since 3.8.8
	 *
	 * @param bool $reduce_stock whether to reduce stock for the items in order
	 * @param int $order_id order ID
	 * @return bool
	 */
	public function maybe_reduce_stock_level( $reduce_stock, $order_id ) {

		/**
		 * Filters whether stock should be reduced for imported order.
		 *
		 * @since 3.8.8
		 *
		 * @param bool $reduce_stock whether stock should be reduced for the imported order
		 * @param int $order_id the order ID
		 */
		$reduce_stock = (bool) apply_filters( 'wc_csv_import_suite_reduce_order_stock_level', $reduce_stock, $order_id );

		// additional sanity checks for rare instances where the order is undetermined, or the item such as a variation is orphaned
		if ( $reduce_stock ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {

				$reduce_stock = false;

			} else {

				foreach ( $order->get_items() as $item ) {

					if ( ! $item instanceof \WC_Order_Item_Product || ! $item->get_product() ) {
						$reduce_stock = false;
						break;
					}
				}
			}
		}

		return $reduce_stock;
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns all error messages from WP_Error and glues them together
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Processor from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 *
	 * @param \WP_Error $wp_error
	 * @return string
	 */
	protected function implode_wp_error_messages( WP_Error $wp_error ) {

		$errors   = $wp_error->get_error_messages();
		$messages = array();

		foreach ( $errors as $error ) {
			$messages[] = $error;
		}

		return implode( ', ', $messages );
	}


	/**
	 * Gets the order emails to disable during order imports.
	 *
	 * @since 3.5.2
	 *
	 * @return string[] an array of email ids to disable
	 */
	private function get_order_emails_to_disable() {

		/**
		 * Filters the order emails to disable during order imports.
		 *
		 * @since 3.5.2
		 *
		 * @param string[] an array of email ids to disable
		 */
		return apply_filters( 'wc_csv_import_suite_order_emails_to_disable', array(
			'new_order',
			'cancelled_order',
			'failed_order',
			'customer_invoice',
			'customer_on_hold_order',
			'customer_processing_order',
			'customer_completed_order',
			'customer_refunded_order',
		) );
	}


	/**
	 * Disables order emails during order import.
	 *
	 * @since 3.5.2
	 */
	private function disable_order_emails() {

		foreach ( $this->get_order_emails_to_disable() as $email_id ) {
			add_filter( "woocommerce_email_enabled_{$email_id}", '__return_false', 1000 );
		}
	}

	/**
	 * Re-enables order emails during order import.
	 *
	 * @since 3.5.2
	 */
	private function enable_order_emails() {

		foreach ( $this->get_order_emails_to_disable() as $email_id ) {
			remove_filter( "woocommerce_email_enabled_{$email_id}", '__return_false', 1000 );
		}
	}

}
