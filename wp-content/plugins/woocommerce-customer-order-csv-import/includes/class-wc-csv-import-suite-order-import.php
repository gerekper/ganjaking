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

if ( ! class_exists( 'WP_Importer' ) ) return;

defined( 'ABSPATH' ) or exit;

/**
 * WooCommerce Order Importer class for managing the import process of a CSV file.
 *
 * The main difficulty in importing orders is that by default WooCommerce relies
 * on the internal post_id to act as the order number, which we can not set when
 * importing orders. So we have to make some concessions based on the users
 * particular environment:
 *
 * 1. If they happen to have a custom order number plugin installed that makes
 *    use of the filter/action hooks provided by this plugin, then this import
 *    plugin will integrate seemlessly with that plugin and things will be happy.
 *    Granted, one assumption has to be made on the import format: that a custom
 *    order number will consist of a numeric (incrementing) piece, and a string
 *    formatted piece, but after that custom order number plugins can go nuts
 * 2. If the user does not have a custom order number plugin installed, then
 *    this plugin will compensate by at least setting the provided order number
 *    to the _order_number_formatted/_order_number metas used by the Sequential
 *    Order Number Pro plugin, and add an order note providing the original
 *    order number.
 *
 * The second tricky part is handling the order items. This is dealt with by
 * allowing an arbitrary number of columns of the form order_item_1, order_item_2,
 * etc. The value for each order item is a pipe-delimited string containing:
 * sku|quantity|price
 *
 * @since 1.0.0
 *
 * Class renamed from WC_CSV_Order_Import to WC_CSV_Import_Suite_Order_Import in 3.0.0
 */
class WC_CSV_Import_Suite_Order_Import extends \WC_CSV_Import_Suite_Importer {


	/** @var \WC_CSV_Import_Suite_Order_Import_Parser order parser instance */
	private $parser;

	/** @var array order line item types */
	public $line_types;


	/**
	 * Construct and initialize the importer
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->title = __( 'Import Orders', 'woocommerce-csv-import-suite' );

		$this->i18n = array(
			'count'          => esc_html__( '%s orders' ),
			'count_inserted' => esc_html__( '%s orders inserted' ),
			'count_merged'   => esc_html__( '%s orders merged' ),
			'count_skipped'  => esc_html__( '%s orders skipped' ),
			'count_failed'   => esc_html__( '%s orders failed' ),
		);

		$this->line_types = array(
			'line_item' => 'line_items',
			'shipping'  => 'shipping_lines',
			'fee'       => 'fee_lines',
			'tax'       => 'tax_lines',
			'coupon'    => 'coupon_lines',
		);

		add_filter( 'wc_csv_import_suite_woocommerce_order_csv_column_default_mapping', array( $this, 'column_default_mapping' ), 10, 2 );

		add_action( 'wc_csv_import_suite_column_mapping_options', array( $this, 'advanced_column_mapping_options' ), 10, 5 );

		add_action( 'wc_csv_import_suite_before_import_options_fields', array( $this, 'advanced_import_options' ) );
	}


	/**
	 * Get CSV column mapping options
	 *
	 * @since 3.0.0
	 * @return array Associative array of column mapping options
	 */
	public function get_column_mapping_options() {

		$billing_prefix  = __( 'Billing: %s',  'woocommerce-csv-import-suite' );
		$shipping_prefix = __( 'Shipping: %s', 'woocommerce-csv-import-suite' );

		// note that there are no mapping options for line item fields - they are
		// passed through as-is
		return array(

			__( 'Order data', 'woocommerce-csv-import-suite' ) => array(
				'order_id'                     => __( 'Order ID', 'woocommerce-csv-import-suite' ),
				'order_number_formatted'       => __( 'Formatted order number', 'woocommerce-csv-import-suite' ),
				'order_number'                 => __( 'Order number', 'woocommerce-csv-import-suite' ),
				'date'                         => __( 'Date', 'woocommerce-csv-import-suite' ),
				'status'                       => __( 'Status', 'woocommerce-csv-import-suite' ),
				'currency'                     => __( 'Currency', 'woocommerce-csv-import-suite' ),
				'shipping_total'               => __( 'Shipping total', 'woocommerce-csv-import-suite' ),
				'shipping_tax_total'           => __( 'Shipping tax total', 'woocommerce-csv-import-suite' ),
				'fee_total'                    => __( 'Fees total', 'woocommerce-csv-import-suite' ),
				'fee_tax_total'                => __( 'Fees tax total', 'woocommerce-csv-import-suite' ),
				'tax_total'                    => __( 'Tax total', 'woocommerce-csv-import-suite' ),
				'cart_discount'                => __( 'Discount', 'woocommerce-csv-import-suite' ),
				'order_total'                  => __( 'Order total', 'woocommerce-csv-import-suite' ),
				'refunded_total'               => __( 'Total refunded amount', 'woocommerce-csv-import-suite' ),
				'payment_method'               => __( 'Payment method', 'woocommerce-csv-import-suite' ),
				'shipping_method'              => __( 'Shipping method', 'woocommerce-csv-import-suite' ),
				'customer_note'                => __( 'Customer note', 'woocommerce-csv-import-suite' ),
				'order_notes'                  => __( 'Order notes', 'woocommerce-csv-import-suite' ),
				'download_permissions_granted' => __( 'Download Permissions Granted', 'woocommerce-csv-import-suite' ),
			),

			__( 'Order items', 'woocommerce-csv-import-suite' ) => array(
				'line_items'                   => __( 'Line items', 'woocommerce-csv-import-suite' ),
				'shipping_items'               => __( 'Shipping items', 'woocommerce-csv-import-suite' ),
				'tax_items'                    => __( 'Taxes', 'woocommerce-csv-import-suite' ),
				'fee_items'                    => __( 'Fees', 'woocommerce-csv-import-suite' ),
				'coupons'                      => __( 'Coupons', 'woocommerce-csv-import-suite' ),
			),

			__( 'Customer data', 'woocommerce-csv-import-suite' ) => array(
				'customer_user'       => __( 'Customer user (ID, username or email)', 'woocommerce-csv-import-suite' ),
				'billing_first_name'  => sprintf( $billing_prefix,  __( 'First name', 'woocommerce-csv-import-suite' ) ),
				'billing_last_name'   => sprintf( $billing_prefix,  __( 'Last name', 'woocommerce-csv-import-suite' ) ),
				'billing_company'     => sprintf( $billing_prefix,  __( 'Company', 'woocommerce-csv-import-suite' ) ),
				'billing_address_1'   => sprintf( $billing_prefix,  __( 'Address 1', 'woocommerce-csv-import-suite' ) ),
				'billing_address_2'   => sprintf( $billing_prefix,  __( 'Address 2', 'woocommerce-csv-import-suite' ) ),
				'billing_city'        => sprintf( $billing_prefix,  __( 'City', 'woocommerce-csv-import-suite' ) ),
				'billing_state'       => sprintf( $billing_prefix,  __( 'State', 'woocommerce-csv-import-suite' ) ),
				'billing_postcode'    => sprintf( $billing_prefix,  __( 'Postcode', 'woocommerce-csv-import-suite' ) ),
				'billing_country'     => sprintf( $billing_prefix,  __( 'Country', 'woocommerce-csv-import-suite' ) ),
				'billing_email'       => sprintf( $billing_prefix,  __( 'Email', 'woocommerce-csv-import-suite' ) ),
				'billing_phone'       => sprintf( $billing_prefix,  __( 'Phone', 'woocommerce-csv-import-suite' ) ),
				'shipping_first_name' => sprintf( $shipping_prefix, __( 'First name', 'woocommerce-csv-import-suite' ) ),
				'shipping_last_name'  => sprintf( $shipping_prefix, __( 'Last name', 'woocommerce-csv-import-suite' ) ),
				'shipping_company'    => sprintf( $shipping_prefix, __( 'Company', 'woocommerce-csv-import-suite' ) ),
				'shipping_address_1'  => sprintf( $shipping_prefix, __( 'Address 1', 'woocommerce-csv-import-suite' ) ),
				'shipping_address_2'  => sprintf( $shipping_prefix, __( 'Address 2', 'woocommerce-csv-import-suite' ) ),
				'shipping_city'       => sprintf( $shipping_prefix, __( 'City', 'woocommerce-csv-import-suite' ) ),
				'shipping_state'      => sprintf( $shipping_prefix, __( 'State', 'woocommerce-csv-import-suite' ) ),
				'shipping_postcode'   => sprintf( $shipping_prefix, __( 'Postcode', 'woocommerce-csv-import-suite' ) ),
				'shipping_country'    => sprintf( $shipping_prefix, __( 'Country', 'woocommerce-csv-import-suite' ) ),
			),

			'refunds' => __( 'Refunds', 'woocommerce-csv-import-suite' ),
		);
	}


	/**
	 * Adjust default mapping for CSV columns
	 *
	 * @since 3.0.0
	 * @param string $map_to
	 * @param string $column column
	 * @return string
	 */
	public function column_default_mapping( $map_to, $column ) {

		switch ( $column ) {

			// translations from the new JSON format (following WC API naming conventions)
			case 'id':                 return 'order_id';
			case 'created_at':         return 'date';
			case 'total':              return 'order_total';
			case 'cart_tax':           return 'tax_total';
			case 'total_shipping':     return 'shipping_total';
			case 'total_discount':     return 'cart_discount';
			case 'shipping_tax':       return 'shipping_tax_total';
			case 'total_refunded':     return 'refunded_total';
			case 'note':               return 'customer_note';
			case 'shipping_lines':     return 'shipping_items';
			case 'fee_lines':          return 'fee_items';
			case 'coupon_lines':       return 'coupons';
			case 'coupon_items':       return 'coupons';
			case 'tax_lines':          return 'tax_items';

			// translations for our own legacy format
			case 'order_shipping':     return 'shipping_total';
			case 'order_shipping_tax': return 'shipping_tax_total';
			case 'order_fees':         return 'fee_total';
			case 'order_fee_tax':      return 'fee_tax_total';
			case 'order_tax':          return 'tax_total';
			case 'order_currency':     return 'currency';
			case 'discount_total':     return 'cart_discount';

			// translations for the Customer/Order Export plugin legacy format
			case 'order_status':       return 'status';
			case 'shipping':           return 'shipping_total';
			case 'fees':               return 'fee_total';
			case 'fee_tax':            return 'fee_tax_total';
			case 'tax':                return 'tax_total';
			case 'billing_post_code':  return 'billing_postcode';
			case 'shipping_post_code': return 'shipping_postcode';
			case 'order_items':        return 'line_items';
			case 'customer_id':        return 'customer_user';

			// translations for the Customer/Order Export plugin one item per line legacy format
			case 'row_amount':         return 'item_quantity';
			case 'row_price':          return 'item_total';
			case 'item_variation':     return 'item_meta';
			case 'item_amount':        return 'item_quantity';
		}

		return $map_to;
	}


	/**
	 * Provide additional column mapping options for certain CSV input formats
	 *
	 * @since 3.0.0
	 * @param array $options Associative array of column mapping options
	 * @param string $importer Importer type
	 * @param array $headers Normalized headers
	 * @param array $raw_headers Raw headers from CSV file
	 * @param array $columns Associative array as 'column' => 'default mapping'
	 * @return array
	 */
	public function advanced_column_mapping_options( $options, $importer, $headers, $raw_headers, $columns ) {

		if ( 'woocommerce_order_csv' == $importer ) {

			$format = $this->detect_csv_file_format( $raw_headers );
			$group  = __( 'Order items', 'woocommerce-csv-import-suite' );

			switch ( $format ) {

				case 'csv_import_legacy':

					$order_item_options = $tax_item_options = $shipping_method_options = $shipping_cost_options = array();

					// add an option for each order item column
					foreach ( $columns as $column => $value ) {

						if ( Framework\SV_WC_Helper::str_starts_with( $column, 'order_item_' ) ) {

							$parts                         = explode( '_', $column );
							$number                        = array_pop( $parts );
							$order_item_options[ $column ] = sprintf( __( 'Order item %d', 'woocommerce-csv-import-suite' ), $number );
						}

						elseif ( Framework\SV_WC_Helper::str_starts_with( $column, 'tax_item_' ) ) {

							$parts                       = explode( '_', $column );
							$number                      = array_pop( $parts );
							$tax_item_options[ $column ] = sprintf( __( 'Tax item %d', 'woocommerce-csv-import-suite' ), $number );
						}

						elseif ( Framework\SV_WC_Helper::str_starts_with( $column, 'shipping_method_' ) ) {

							$parts                       = explode( '_', $column );
							$number                      = array_pop( $parts );
							$shipping_method_options[ $column ] = sprintf( __( 'Shipping method %d', 'woocommerce-csv-import-suite' ), $number );
						}

						elseif ( Framework\SV_WC_Helper::str_starts_with( $column, 'shipping_cost_' ) ) {

							$parts                       = explode( '_', $column );
							$number                      = array_pop( $parts );
							$shipping_cost_options[ $column ] = sprintf( __( 'Shipping cost %d', 'woocommerce-csv-import-suite' ), $number );
						}

					}

					$options[ $group ] = $order_item_options + $tax_item_options + $shipping_method_options + $shipping_cost_options;
				break;

				case 'csv_export_default_one_row_per_item':

					unset( $options[ $group ]['line_items'] );

					$new_options = array(
						'item_name'      => __( 'Item name', 'woocommerce-csv-import-suite' ),
						'item_sku'       => __( 'Item SKU', 'woocommerce-csv-import-suite' ),
						'item_quantity'  => __( 'Item quantity', 'woocommerce-csv-import-suite' ),
						'item_total_tax' => __( 'Item tax', 'woocommerce-csv-import-suite' ),
						'item_total'     => __( 'Item total', 'woocommerce-csv-import-suite' ),
						'item_meta'      => __( 'Item meta', 'woocommerce-csv-import-suite' ),
					);

					$options[ $group ] = $new_options + $options[ $group ];
				break;

				case 'csv_export_legacy_one_row_per_item':

					unset( $options[ $group ]['line_items'] );

					$new_options = array(
						'item_sku'       => __( 'Item SKU', 'woocommerce-csv-import-suite' ),
						'item_name'      => __( 'Item name', 'woocommerce-csv-import-suite' ),
						'item_meta'      => __( 'Item meta', 'woocommerce-csv-import-suite' ),
						'item_quantity'  => __( 'Item quantity', 'woocommerce-csv-import-suite' ), // Item Amount
						'item_total'     => __( 'Item total', 'woocommerce-csv-import-suite' ), // Row Price
					);

					$options[ $group ] = $new_options + $options[ $group ];
				break;
			}
		}

		return $options;
	}


	/**
	 * Render advanced options for order CSV import
	 *
	 * @since 3.0.0
	 */
	public function advanced_import_options() {

		if ( ! isset( $_GET['import'] ) || 'woocommerce_order_csv' !== $_GET['import'] ) {
			return;
		}

		?>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Allow unknown products', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[allow_unknown_products]" id="wc-csv-import-suite-order-allow-unknown-products" />
					<?php esc_html_e( 'Allow line items with unknown product sku/id. The line item will not be linked to any product, so this is not necessarily recommended.', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<?php if ( 'yes' === get_option( 'woocommerce_manage_stock', 'yes' ) ) : ?>

			<tr>
				<th scope="row">
					<?php esc_html_e( 'Reduce product stock', 'woocommerce-csv-import-suite' ); ?>
				</th>
				<td>
					<label>
						<input type="checkbox" value="1" name="options[reduce_product_stock]" id="wc-csv-import-suite-reduce-product-stock" checked="checked" />
						<?php esc_html_e( 'When enabled, the import will reduce product stock for products associated with paid orders.', 'woocommerce-csv-import-suite' ); ?>
					</label>
				</td>
			</tr>

		<?php endif; ?>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Re-calculate taxes & totals', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[recalculate_totals]" id="wc-csv-import-suite-order-recalculate-totals" />
					<?php esc_html_e( 'Re-calculate taxes and totals after importing the order. This may result in different tax and order totals than in the CSV file.', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Use addresses from customer profile', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[use_addresses_from_customer_profile]" id="wc-csv-import-suite-order-use-addresses-from-customer-profile" />
					<?php esc_html_e( 'Use addresses from the customer profile if no address columns are present in the CSV file.', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<tr>
			<th scope="row">
				<?php esc_html_e( 'Send emails', 'woocommerce-csv-import-suite' ); ?>
			</th>
			<td>
				<label>
					<input type="checkbox" value="1" name="options[send_order_emails]" id="wc-csv-import-suite-order-send-emails" />
					<?php esc_html_e( 'Send relevant order emails to customers and store managers. This includes new order emails as well as emails triggered by order status changes when merging/updating orders', 'woocommerce-csv-import-suite' ); ?>
				</label>
			</td>
		</tr>

		<?php
	}


	/**
	 * Checks whether the CSV uses a multi-line format
	 *
	 * Checks whether data for a single item spans across multiple physical lines
	 * in the CSV file.
	 *
	 * @since 3.0.0
	 * @see \WC_CSV_Import_Suite_Importer::is_multiline_format();
	 * @param array $raw_headers Raw CSV headers
	 * @return bool
	 */
	protected function is_multiline_format( $raw_headers ) {

		$format = $this->detect_csv_file_format( $raw_headers );

		return in_array( $format, array(
			'csv_export_default_one_row_per_item',
			'csv_export_legacy_one_row_per_item'
		) );
	}


	/**
	 * Get identifier for a single item
	 *
	 * Utility method to get a unique identifier for a single item in a CSV file.
	 * Useful for detecting physical lines in a CSV file to form a single item.
	 *
	 * @since 3.0.0
	 * @see \WC_CSV_Import_Suite_Importer::get_item_identifier();
	 * @param array $data Item data, either raw data from CSV parser, mapped to
	 *                    columns, or parsed item data
	 * @return int|string|null
	 */
	public function get_item_identifier( $data ) {

		if ( ! empty( $data['order_number_formatted'] ) ) {
			return $data['order_number_formatted'];
		}

		if ( ! empty( $data['order_number'] ) ) {
			return $data['order_number'];
		}

		if ( ! empty( $data['order_id'] ) ) {
			return $data['order_id'];
		}

		if ( ! empty( $data['id'] ) ) {
			return $data['id'];
		}

		return null;
	}


	/**
	 * Merge data from multiple parsed lines into one item
	 *
	 * @since 3.0.0
	 * @see \WC_CSV_Import_Suite_Importer::merge_parsed_items();
	 * @param array $items Array of parsed items
	 * @return array
	 */
	protected function merge_parsed_items( $items ) {

		$combined_item = array();

		foreach ( $items as $line_num => $item ) {

			// get full data set from first item
			if ( empty( $combined_item ) ) {
				$combined_item = $item;
			}

			// merge the line items from all other items
			else {
				$combined_item['line_items'][] = array_shift( $item['line_items'] );
			}

		}

		return $combined_item;
	}


	/**
	 * Parses raw order data, building and returning an array of order data
	 * to import into the database.
	 *
	 * @see \WC_CSV_Import_Suite_Order_Import_Parser::parse_order()
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Parsed order data
	 */
	protected function parse_item( $item, $options = array(), $raw_headers = array() ) {
		return $this->get_parser()->parse_order( $item, $options, $raw_headers );
	}


	/**
	 * Returns the order parser instance
	 *
	 * @since 3.3.0
	 * @return \WC_CSV_Import_Suite_Order_Parser parser instance
	 */
	private function get_parser() {

		if ( ! isset( $this->parser ) ) {
			$this->parser = wc_csv_import_suite()->load_class( '/includes/class-wc-csv-import-suite-order-parser.php', 'WC_CSV_Import_Suite_Order_Parser' );
		}

		return $this->parser;
	}


	/**
	 * Processes an order.
	 *
	 * @see \WC_CSV_Import_Suite_Order_Processor::process_order()
	 *
	 * @since 3.0.0
	 *
	 * @param array $data parsed order data, ready for processing, compatible with {@see wc_create_order()} or {@see wc_update_order()}
	 * @param array $options options (optional)
	 * @param array $raw_headers raw headers (optional)
	 * @return int|null
	 */
	protected function process_item( $data, $options = [], $raw_headers = [] ) {

		return $this->get_processor()->process_order( $data, $options, $raw_headers );
	}


	/**
	 * Returns the order processor instance
	 *
	 * @since 3.3.0
	 * @return \WC_CSV_Import_Suite_Order_Processor processor instance
	 */
	private function get_processor() {

		if ( ! isset( $this->processor ) ) {
			$this->processor = wc_csv_import_suite()->load_class( '/includes/class-wc-csv-import-suite-order-processor.php', 'WC_CSV_Import_Suite_Order_Processor' );
		}

		return $this->processor;
	}


	/**
	 * Detect CSV file format
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Import_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $headers
	 * @return bool
	 */
	public function detect_csv_file_format( $headers ) {

		// legacy CSV Import format
		if ( in_array( 'order_item_1', $headers ) ) {
			return 'csv_import_legacy';
		}

		// CSV Export legacy format
		if (
			in_array( 'Order ID',           $headers ) &&
			in_array( 'Order Status',       $headers ) &&
			in_array( 'Billing Post code',  $headers ) &&
			in_array( 'Shipping Post code', $headers ) &&
			in_array( 'Order Items',        $headers )
		) {
			return 'csv_export_legacy';
		}

		// CSV Export one row per item legacy format
		if (
			in_array( 'Item SKU',       $headers ) &&
			in_array( 'Item Name',      $headers ) &&
			in_array( 'Item Variation', $headers ) &&
			in_array( 'Item Amount',    $headers ) &&
			in_array( 'Row Price',      $headers ) &&
			in_array( 'Order ID',       $headers )
		) {
			return 'csv_export_legacy_one_row_per_item';
		}

		// CSV Export one row per item format
		if (
			in_array( 'item_name',     $headers ) &&
			in_array( 'item_sku',      $headers ) &&
			in_array( 'item_quantity', $headers ) &&
			in_array( 'item_total',    $headers ) &&
			in_array( 'item_meta',     $headers ) &&
			in_array( 'order_id',      $headers )
		) {
			return 'csv_export_default_one_row_per_item';
		}

		// CSV Export Default format is also considered default, since it's very
		// similar to the new default format
		return 'default';
	}


	/** Helper methods ******************************************************/


	/**
	 * Checks if a given post ID is a valid product post type.
	 *
	 * @since 3.0.0
	 *
	 * @param int $product_id the product ID
	 * @return bool
	 */
	public function is_valid_product( $product_id ) {

		/**
		 * Filters valid product post types.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $valid_types array fo valid post type names
		 */
		$valid_product_post_types = (array) apply_filters( 'wc_csv_import_suite_valid_product_post_types', [ 'product', 'product_variation' ] );

		$post_type = get_post_type( $product_id );
		$is_valid  = in_array( $post_type, $valid_product_post_types, true );

		if ( $is_valid && 'product_variation' === $post_type ) {
			$is_valid = (bool) wp_get_post_parent_id( $product_id );
		}

		return $is_valid;
	}


	/**
	 * Get product ID by item SKU
	 *
	 * @since 3.0.0
	 * @param string $sku
	 * @return int|null
	 */
	public function get_product_id_by_sku( $sku ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "
			SELECT post_id FROM $wpdb->postmeta
			WHERE meta_key='_sku' AND meta_value=%s LIMIT 1
		", $sku ) );
	}


	/**
	 * Check if input string is possibly a JSON array
	 *
	 * @since 3.0.0
	 * @param string $string
	 * @return bool True if string is possible JSON, false otherwise
	 */
	public function is_possibly_json_array( $string ) {
		return '[]' == $string || Framework\SV_WC_Helper::str_starts_with( $string, '[{' ) && Framework\SV_WC_Helper::str_ends_with( $string, '}]' );
	}


	/**
	 * Parse/decode a JSON string while throwing exceptions on errors
	 *
	 * @since 3.0.0
	 * @param string $string Input string
	 * @throws \WC_CSV_Import_Suite_Import_Exception json decoding errors
	 * @return array
	 */
	public function parse_json( $string ) {

		// decode the JSON data
		$result = json_decode( $string, true );
		$error  = null;

		// switch and check possible JSON errors
		switch ( json_last_error() ) {
			case JSON_ERROR_NONE:
				// JSON is valid // No error has occurred
			break;

			case JSON_ERROR_DEPTH:
				$error = esc_html__( 'The maximum stack depth has been exceeded.', 'woocommerce-csv-import-suite' );
			break;

			case JSON_ERROR_STATE_MISMATCH:
				$error = esc_html__( 'Invalid or malformed JSON.', 'woocommerce-csv-import-suite' );
			break;

			case JSON_ERROR_CTRL_CHAR:
				$error = esc_html__( 'Control character error, possibly incorrectly encoded.', 'woocommerce-csv-import-suite' );
			break;

			case JSON_ERROR_SYNTAX:
				$error = esc_html__( 'Syntax error, malformed JSON.', 'woocommerce-csv-import-suite' );
			break;

			// PHP >= 5.3.3
			case JSON_ERROR_UTF8:
				$error = esc_html__( 'Malformed UTF-8 characters, possibly incorrectly encoded.', 'woocommerce-csv-import-suite' );
			break;

			// PHP >= 5.5.0
			case JSON_ERROR_RECURSION:
				$error = esc_html__( 'One or more recursive references in the value to be encoded.', 'woocommerce-csv-import-suite' );
			break;

			// PHP >= 5.5.0
			case JSON_ERROR_INF_OR_NAN:
				$error = esc_html__( 'One or more NAN or INF values in the value to be encoded.', 'woocommerce-csv-import-suite' );
			break;

			case JSON_ERROR_UNSUPPORTED_TYPE:
				$error = esc_html__( 'A value of a type that cannot be encoded was given.', 'woocommerce-csv-import-suite' );
			break;

			default:
				$error = esc_html__( 'Unknown JSON error occured.', 'woocommerce-csv-import-suite' );
			break;
		}

		if ( $error ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_json_parse_error', $error );
		}

		// everything is OK
		return $result;
	}


	/**
	 * Parses the order date
	 *
	 * @since 3.3.0
	 *
	 * @param array $item raw order data
	 * @throws WC_CSV_Import_Suite_Import_Exception
	 * @return string $date the order date timestamp
	 */
	public function parse_order_date( $item ) {

		// default date
		$date = time();

		// validate order date
		if ( ! empty( $item['date'] ) ) {

			$item['date'] = get_gmt_from_date( $item['date'] );

			if ( false === ( $item['date'] = strtotime( $item['date'] ) ) ) {

				// invalid date format
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_date_format', sprintf( __( 'Invalid date format %s.', 'woocommerce-csv-import-suite' ), $item['date'] ) );
			} else {
				$date = $item['date'];
			}
		}

		return $date;
	}


	/** Delimited string parsing methods ***************************************/


	/**
	 * Parse items from a delimited string
	 *
	 * Extracts items from string where items are separated by a delimiter
	 * (default semicolon ;), properties are separated with another delimiter
	 * (default pipe :), and property key-value pairs are separated with yet
	 * another delimiter (default colon :).
	 *
	 * Supports parsing a special 'meta' property, where it expects the meta
	 * items be separated with comma (,) by default and meta key-value pairs be
	 * separated with an equals sign (=) by default.
	 *
	 * All delimiters can be customized.
	 *
	 * @since 3.0.0
	 * @param string $input Input string
	 * @param array $delimiters {
	 *        Optional. Associative array of delimiters.
	 *
	 *        @type string $item Item delimiter. Default ';'.
	 *        @type string $property Property delimiter. Default '|'.
	 *        @type string $property_key_value Property key-value delimiter. Default ':'.
	 *        @type string $meta Meta delimiter. Default ','.
	 *        @type string $meta_key_value Meta key-value delimiter. Default '='.
	 * }
	 * @return array
	 */
	public function parse_delimited_string( $input, $delimiters = array() ) {

		$d = wp_parse_args( $delimiters, array(
			'item'               => ';', // item separator
			'property'           => '|', // property separator
			'property_key_value' => ':', // property key-value separator
			'meta'               => ',', // meta separator
			'meta_key_value'     => '=', // meta key-value separator
		) );

		// split string into items, based on item delimiter
		$items = $this->split_on_delimiter( $input, $d['item'] );

		// parse each item
		foreach( $items as $key => $item ) {

			// split item into properties
			$item = $this->split_on_delimiter( $item, $d['property'] );
			// split properties into key-value pairs
			$item = $this->split_key_value_pairs( $item, $d['property_key_value'] );

			// split item meta into key-value pairs
			if ( isset( $item['meta'] ) && ! empty( $item['meta'] ) ) {
				$item['meta'] = $this->split_on_delimiter( $item['meta'], $d['meta'] );
				$item['meta'] = $this->split_key_value_pairs( $item['meta'], $d['meta_key_value'] );
			}

			$items[ $key ] = $item;
		}

		return $items;
	}


	/**
	 * Split an array of string based key-value pairs into an associative array
	 *
	 * @since 3.0.0
	 * @param array $pairs Array of strings of key-value pairs, joined together by
	 *                     $delimiter
	 * @param string $delimiter Delimiter separating keys and values
	 * @param bool $unescape_results Optional. Defaults to true. If true, will try
	 *                               to unescape any escaped delimiters in the
	 *                               results.
	 * @return array
	 */
	public function split_key_value_pairs( $pairs, $delimiter, $unescape_results = true ) {

		$data = array();

		foreach ( $pairs as $pair ) {

			// split to key-value pieces
			$pieces = $this->split_on_delimiter( $pair, $delimiter, $unescape_results );
			$name   = $pieces[0];
			$value  = isset( $pieces[1] ) ? $pieces[1] : null;

			if ( $name ) {
				$data[ $name ] = $value;
			}
		}

		return $data;
	}


	/**
	 * Split string on delimiter
	 *
	 * Will try to split on non-escaped delimiter first. Uses simple explode
	 * as a fallback
	 *
	 * @since 3.0.0
	 * @param string $string Input text
	 * @param string $delimiter
	 * @param bool $unescape_results Optional. Defaults to true. If true, will try
	 *                               to unescape any escaped delimiters in the
	 *                               resulting pieces.
	 * @return array Pieces
	 */
	public function split_on_delimiter( $string, $delimiter, $unescape_results = true ) {

		// split on non-escaped delimiters
		// http://stackoverflow.com/questions/6243778/split-string-by-delimiter-but-not-if-it-is-escaped
		$pieces = preg_split( '~\\\\.(*SKIP)(*FAIL)|\\' . $delimiter . '~s', $string );

		// fallback: try a simple explode, since the above apparently doesn't always work
		if ( $string && empty( $pieces ) ) {
			$pieces = explode( $delimiter, $string );
		}

		// unescape delimiter in results
		if ( $unescape_results && ! empty( $pieces ) ) {
			foreach ( $pieces as $key => $piece ) {

				$pieces[ $key ] = str_replace( '\\' . $delimiter, $delimiter, $piece );
			}
		}

		// see http://php.net/manual/en/function.array-filter.php#111091
		return array_filter( array_map( 'trim', $pieces ), 'strlen' );
	}

}
