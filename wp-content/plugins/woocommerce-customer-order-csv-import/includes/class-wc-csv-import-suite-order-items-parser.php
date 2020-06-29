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
 * WooCommerce Order Items Parser class for parsing the raw order items from CSV file
 *
 * @since 3.3.0
 */
class WC_CSV_Import_Suite_Order_Items_Parser {

	/** @var \WC_CSV_Import_Suite_Order_Import order importer instance */
	private $importer;

	/** @var array field mappings for the `line_item`-type line items **/
	private $line_item_mapping;

	/** @var array field mappings for the `tax`-type line items **/
	private $tax_item_mapping;

	/** @var array field mappings for the `shipping`-type line items **/
	private $shipping_item_mapping;

	/** @var array field mappings for the `fee`-type line items **/
	private $fee_item_mapping;

	/** @var array order shipping methods holder */
	private $available_shipping_methods;


	/**
	 * Construct and initialize the items parser
	 *
	 * @since 3.3.0
	 */
	public function __construct() {

		$this->importer = wc_csv_import_suite()->get_importers_instance()->get_importer( 'woocommerce_order_csv' );

		$this->line_item_mapping = array(
			'id' => 'order_item_id',
		);

		$this->tax_item_mapping = array(
			'id'                => 'order_item_id',
			'name'              => 'code',
			'title'             => 'label',
			'total'             => 'tax_amount',
			'shipping_total'    => 'shipping_tax_amount',
			'tax_rate_compound' => 'compound',
		);

		$this->shipping_item_mapping = array(
			'id'                => 'order_item_id',
			'method'            => 'method_title', // translation from CSV Export default
			'total'             => 'cost',
		);

		$this->fee_item_mapping = array(
			'id'    => 'order_item_id',
			'title' => 'name',
			'tax'   => 'total_tax',
		);
	}


	/**
	 * Parse line items from raw CSV data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import and changed from private to public
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV file
	 * @param string $format CSV file format
	 * @param bool $allow_unknown_products. Optional. Defaults to false.
	 * @param array $tax_items Parsed tax items
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Parsed line item data
	 */
	public function parse_line_items( $item = array(), $format, $allow_unknown_products = false, $tax_items, $merging = false ) {

		$line_items = $raw_line_items = array();

		switch ( $format ) {

			case 'default':

				if ( empty( $item['line_items'] ) ) {
					return array();
				}

				// default format supports line items both in JSON and in a "simple" format
				if ( $this->importer->is_possibly_json_array( $item['line_items'] ) ) {

					try {
						$raw_line_items = $this->importer->parse_json( $item['line_items'] );
					} catch( \WC_CSV_Import_Suite_Import_Exception $e ) {
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_line_items_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing line items for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
					}

				} else {
					$raw_line_items = $this->importer->parse_delimited_string( $item['line_items'] );
				}

				// validate / parse line items
				if ( ! empty( $raw_line_items ) ) {
					foreach ( $raw_line_items as $raw_line_item ) {

						// normalize line item fields
						foreach ( $this->line_item_mapping as $from => $to ) {

							if ( isset( $raw_line_item[ $from ] ) ) {

								$raw_line_item[ $to ] = $raw_line_item[ $from ];
								unset( $raw_line_item[ $from ] );
							}
						}

						$line_items[] = $this->parse_line_item( $raw_line_item, $allow_unknown_products, $tax_items );
					}
				}
			break;

			case 'csv_import_legacy':

				if ( ! empty( $item['order_item_1'] ) ) {

					// one or more order items
					$i = 1;
					while ( ! empty( $item[ 'order_item_' . $i ] ) ) {

						// split on non-escaped pipes
						$_item_data = $this->importer->split_on_delimiter( $item[ 'order_item_' . $i ], '|' );

						// pop off the special sku, qty and total values
						$product_identifier = array_shift( $_item_data );	// sku or product_id:id
						$qty                = array_shift( $_item_data );
						$total              = array_shift( $_item_data );

						if ( ! $product_identifier || ! $qty || ! is_numeric( $total ) ) {
							// invalid item
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_sku_quantity_or_email', sprintf( esc_html__( 'Missing SKU, quantity or total for %s on line %s.', 'woocommerce-csv-import-suite' ), 'order_item_' . $i, $this->importer->get_line_num() ) );
						}

						// product_id or sku
						if ( Framework\SV_WC_Helper::str_starts_with( $product_identifier, 'product_id:' ) ) {

							// product by product_id
							$product_id = substr( $product_identifier, 11 );

							// not a product
							if ( ! $this->importer->is_valid_product( $product_id ) ) {
								$product_id = '';
							}

						} else {
							// find by sku
							$product_id = $this->importer->get_product_id_by_sku( $product_identifier );
						}

						if ( ! $allow_unknown_products && ! $product_id ) {
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_order_item', sprintf( esc_html__( 'Unknown order item: %s.', 'woocommerce-csv-import-suite' ), $product_identifier ) );
						}

						// get any additional item meta
						$item_meta = ! empty( $_item_data ) ? $this->importer->split_key_value_pairs( $_item_data, ':' ) : array();

						$line_items[] = array( 'product_id' => $product_id, 'quantity' => $qty, 'total' => $total, 'meta' => $item_meta );

						$i++;
					}
				}
			break;

			case 'csv_export_legacy_one_row_per_item':
				$line_items[] = $this->parse_csv_export_line_item( $item, $allow_unknown_products, ':' );
			break;

			case 'csv_export_default_one_row_per_item':
				$line_items[] = $this->parse_csv_export_line_item( $item, $allow_unknown_products );
			break;

			case 'csv_export_legacy':

				// split line items on non-escaped semicolons
				$_line_items = $this->importer->split_on_delimiter( $item['line_items'], ';' );

				if ( ! empty( $_line_items ) ) {
					foreach ( $_line_items as $_line_item ) {

						// replace any escaped semicolons
						$_line_item = str_replace( '\;', ';', $_line_item );

						$name     = $_line_item;
						$quantity = 1;

						$meta = $sku = null;

						// try to detect item quantity. quantity is either the last thing
						// at the end of string, or just before a dash that's separating
						// basic item data from item meta
						if ( preg_match( '/(x[0-9]+)$/', $_line_item, $matches ) ) {

							$name     = trim( str_replace( $matches[0], '', $_line_item ) );
							$quantity = $matches[0];

						} else if ( preg_match( '/(x[0-9]+ - )/', $_line_item, $matches ) ) {

							$parts = array_map( 'trim', explode( $matches[0], $_line_item ) );
							$name  = $parts[0];
							$meta  = isset( $parts[1] ) ? $parts[1] : null;

						}

						// try to get the item sku, which should be the last part of the name
						// such as: item name (sku)
						if ( preg_match( '/\(([^)]+)\)/', $name, $matches ) ) {
							$sku  = $matches[1];
							$name = trim( str_replace( $matches[0], '', $name ) );
						}

						$_line_item = array(
							'item_name'     => $name,
							'item_sku'      => $sku,
							'item_quantity' => $quantity,
							'item_meta'     => $meta,
							'item_total'    => null,
						);

						// the format does not provide a total for any items... so we can't
						// really support that format at all, left here for phun :)

						// parse line item data
						$line_items[] = $this->parse_csv_export_line_item( $_line_item, $allow_unknown_products, ':' );
					}
				}
			break;
		}

		// attach variation data to line items, in a similar format as it's done in WC_CLI/WC_API classes
		foreach ( $line_items as $line_item_key => $line_item ) {

			$product    = ! empty( $line_item['product_id'] ) ? wc_get_product( $line_item['product_id'] ) : null;
			$attributes = is_callable( array( $product, 'get_variation_attributes' ) ) ? $product->get_variation_attributes() : array();

			// get variation data from product
			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $attribute_key => $value ) {

					$attribute_key = str_replace( 'attribute_', '', $attribute_key );

					$line_items[ $line_item_key ]['variations'][ $attribute_key ] = $value;
				}
			}

			// get product attribute/variation data from item meta - this will override the variation / attribute values set by the product itself
			if ( ! empty( $line_item['meta'] ) ) {

				$attribute_keys = array_keys( $attributes );

				foreach ( $line_item['meta'] as $meta_key => $value ) {

					$is_product_attribute = false;
					$attribute_key        = null;

					// meta key is a product attribute
					if ( Framework\SV_WC_Helper::str_starts_with( $meta_key, 'pa_' ) ) {

						$is_product_attribute = true;
						$attribute_key        = $meta_key;

					} elseif ( ! empty( $attribute_keys ) ) {

						// try to match product attribute based on it's formatted label, as to avoid adding duplicate
						// product attributes if the CSV file already contains formatted attributes
						foreach ( $attribute_keys as $attribute_key ) {

							$attribute_key   = str_replace( 'attribute_', '', $attribute_key );
							$formatted_label = wc_attribute_label( $attribute_key, $product );

							if ( $meta_key === $formatted_label ) {

								$is_product_attribute = true;
								break;
							}
						}
					}

					if ( $is_product_attribute ) {

						// override the attribute value set by product - use the one from the CSV
						$line_items[ $line_item_key ]['variations'][ $attribute_key ] = $value;

						// remove attribute meta
						unset( $line_items[ $line_item_key ]['meta'][ $meta_key ] );
					}
				}
			}
		}

		return $line_items;
	}


	/**
	 * Parse a line item - expects it to be in the default CSV format
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $item Raw item data
	 * @param bool $allow_unknown_products Optional. Defaults to false
	 * @param array $tax_items Parsed tax items
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Parsed item data
	 */
	private function parse_line_item( $item, $allow_unknown_products = false, $tax_items ) {

		$product_id = $this->get_array_key_value( $item, 'product_id' );
		$sku        = $this->get_array_key_value( $item, 'sku' );
		$qty        = $this->get_array_key_value( $item, 'quantity' );
		$total      = $this->get_array_key_value( $item, 'total' );

		$product_identifier = $product_id ? $product_id : $sku;

		if ( ! $product_identifier || ! $qty || ! is_numeric( $total ) ) {

			// invalid item
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_product_id_sku_quantity_or_total', sprintf( __( 'Missing product ID/SKU, quantity or total for order item on line %s.', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num() ) );
		}

		// match product by product_id
		if ( $product_id && ! $this->importer->is_valid_product( $product_id ) ) {
			$product_id = null; // not a product
		}

		// match product by SKU
		if ( ! $product_id && $sku ) {
			$product_identifier = $sku;
			$product_id         = $this->importer->get_product_id_by_sku( $sku );
		}

		if ( ! $allow_unknown_products && ! $product_id ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_order_item', sprintf( __( 'Unknown order item: %s.', 'woocommerce-csv-import-suite' ), $product_identifier ) );
		}

		// map line item taxes to the correct tax rates
		if ( ! empty( $item['tax_data'] ) && ! empty( $tax_items ) ) {
			$item['tax_data'] = $this->map_tax_data_rates( $item['tax_data'], $tax_items );
		}

		$item['product_id'] = $product_id;

		return $item;
	}


	/**
	 * Parse a CSV Export (legacy and default one row per item) formatted line item
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $item
	 * @param bool $allow_unknown_products Optional. Defaults to false
	 * @param string $meta_key_value_separator Optional. Defaults to `=`
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array
	 */
	private function parse_csv_export_line_item( $item, $allow_unknown_products = false, $meta_key_value_separator = '=' ) {

		$sku   = $this->get_array_key_value( $item, 'item_sku' );
		$qty   = $this->get_array_key_value( $item, 'item_quantity' );
		$total = $this->get_array_key_value( $item, 'item_total' );

		// Consider item invalid if:
		// - no quantity is specified
		// - no SKU is specified (unless unknown products are allowed)
		// - total is not a number
		if ( ! $qty || ( ! $sku && ! $allow_unknown_products ) || ! is_numeric( $total ) ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_sku_quantity_or_total', sprintf( __( 'Missing SKU, quantity or total for order item on line %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num() ) );
		}

		// find by sku, if specified
		$product_id = $sku ? $this->importer->get_product_id_by_sku( $sku ) : null;

		// bail if unknown product is not allowed
		if ( ! $allow_unknown_products && ! $product_id ) {
			/* translators: Placeholders: %s - order item SKU */
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_order_item', sprintf( __( 'Unknown order item: %s.', 'woocommerce-csv-import-suite' ), $sku ) );
		}

		// get any additional item meta
		$item_meta = array();

		if ( isset( $item['item_meta'] ) && ! empty( $item['item_meta'] ) ) {
			$item_meta = $this->importer->split_on_delimiter( $item['item_meta'], ',' );
			$item_meta = $this->importer->split_key_value_pairs( $item_meta, $meta_key_value_separator );
		}

		$item_data = array(
			'product_id' => $product_id,
			'quantity'   => $qty,
			'total'      => $total,
			'meta'       => $item_meta,
		);

		if ( isset( $item['item_name'] ) && ! empty( $item['item_name'] ) ) {
			// replace encoded quote chars since we could have encoded them in JSON export formats
			$item_data['name'] = trim( str_replace( '&quot;', '"', $item['item_name'] ) );
		}

		if ( isset( $item['item_total_tax'] ) ) {
			$item_data['tax_total'] = $item['item_total_tax'];
		}

		return $item_data;
	}


	/**
	 * Parse shipping items from raw CSV data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import and changed from private to public
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV file
	 * @param int $shipping_total Optional. Shipping total. Passed by reference.
	 * @param string $format Optional. CSV file format.
	 * @param array $tax_items Parsed tax items form CSV file
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Array with 2 values: order shipping methods and shipping_total
	 */
	public function parse_shipping_items( $item = array(), &$shipping_total = null, $format = null, $tax_items = array(), $merging = false ) {

		// shipping methods & costs
		$shipping_items    = array();
		$available_methods = $this->get_available_shipping_methods();

		// shipping items - applies to modern formats
		if ( ! empty( $item['shipping_items'] ) ) {

			if ( $this->importer->is_possibly_json_array( $item['shipping_items'] ) ) {

				try {
					$raw_shipping_items = $this->importer->parse_json( $item['shipping_items'] );
				} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_shipping_items_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing shipping items for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
				}

			} else {
				$raw_shipping_items = $this->importer->parse_delimited_string( $item['shipping_items'] );
			}

			foreach( $raw_shipping_items as $raw_shipping_item ) {

				// normalize shipping fields
				foreach ( $this->shipping_item_mapping as $from => $to ) {
					if ( isset( $raw_shipping_item[ $from ] ) ) {

						$raw_shipping_item[ $to ] = $raw_shipping_item[ $from ];
						unset( $raw_shipping_item[ $from ] );
					}
				}

				if ( ! isset( $raw_shipping_item['cost'] ) ) {
					$raw_shipping_item['cost'] = 0;
				}

				$shipping_items[] = $raw_shipping_item;
			}
		}


		// pre WC 2.1 format of a single shipping method, left for backwards
		// compatibility of import files, applies to csv_import && csv_export
		// legacy formats
		else if ( ! empty( $item['shipping_method'] ) ) {

			// CSV export supports multiple comma-separated shipping methods
			$methods = array_map( 'trim', explode( ',', $item['shipping_method'] ) );

			// cost however, is always the same
			$cost = isset( $item['shipping_cost'] ) ? $item['shipping_cost'] : $shipping_total;

			// collect the shipping method id/cost
			// unfortunately, in case of multiple shipping methods, there's no way to
			// get the cost for each shipping method, so we simply use the full cost
			// for the first shipping method, and leave the rest with empty hands
			foreach( $methods as $key => $method ) {

				$shipping_items[] = array(
					'method_id'    => 'csv_import_legacy' == $format ? $method : null, // legacy CSV import format provides method ID
					'method_title' => 'csv_import_legacy' != $format ? $method : null, // Other formats provide method title
					'cost'         => $key < 1 ? $cost : null, // use cost for first method only
				);
			}

		}

		// collect any additional shipping methods, or update details for already
		// collected methods.
		// applies to csv_import_legacy format
		$i = null;
		if ( isset( $item['shipping_method_1'] ) ) {
			$i = 1;
		} elseif( isset( $item['shipping_method_2'] ) ) {
			$i = 2;
		}

		if ( ! is_null( $i ) ) {

			while ( ! empty( $item[ 'shipping_method_' . $i ] ) ) {

				$shipping_items[ $i - 1 ] = array(
					'method_id'    => $item[ 'shipping_method_' . $i ],
					'method_title' => null,
					'cost'         => isset( $item[ 'shipping_cost_' . $i ] ) ? $item[ 'shipping_cost_' . $i ] : null,
				);

				$i++;
			}
		}

		// if the order shipping total wasn't set, calculate it, unless merging
		if ( is_null( $shipping_total ) && ! $merging ) {

			$shipping_total = 0;

			foreach ( $shipping_items as $shipping_item ) {
				$shipping_total += abs( $shipping_item['cost'] );
			}

		} elseif ( null !== $shipping_total && 1 == count( $shipping_items ) && is_null( $shipping_items[0]['cost'] ) ) {
			// special case: if there was a total order shipping but no cost for the single shipping method, use the total shipping for the order shipping line item
			$shipping_items[0]['cost'] = $shipping_total;
		}


		// match shipping items to known, available shipping methods
		foreach ( $shipping_items as $key => $shipping_item ) {

			// look up shipping method by id or title
			$shipping_method = isset( $shipping_item['method_id'] ) ? $this->get_array_key_value( $available_methods, $shipping_item['method_id'] ) : null;

			if ( ! $shipping_method && ! empty( $shipping_item['method_title'] ) ) {

				// try by title
				foreach ( $available_methods as $method ) {

					if ( 0 === strcasecmp( $method->title, $shipping_item['method_title'] ) ) {
						$shipping_method = $method;
						break; // go with the first one we find
					}
				}
			}

			// known shipping method found
			if ( $shipping_method ) {
				$shipping_items[ $key ]['method_id']    = $shipping_method->id;
				$shipping_items[ $key ]['method_title'] = $shipping_method->title;
			}

			// map shipping taxes to the correct tax rates
			if ( ! empty( $shipping_item['taxes'] ) && ! empty( $tax_items ) ) {
				$shipping_items[ $key ]['taxes'] = $this->map_tax_data_rates( $shipping_item['taxes'], $tax_items );
			}
		}

		return $shipping_items;
	}


	/**
	 * Parse shipping items from raw CSV data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import and changed from private to public
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV file
	 * @param int $tax_total Optional. Tax total
	 * @param int $shipping_tax_total Optional. Shipping tax total
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Array with 3 items: tax items, tax total and shipping tax total
	 */
	public function parse_tax_items( $item, &$tax_total = null, &$shipping_tax_total = null, $merging = false ) {

		$tax_items     = array();
		$raw_tax_items = array();
		$tax_rates     = $this->get_tax_rates();

		// CSV import legacy tax item format which supports multiple tax items in
		// numbered columns containing a pipe-delimited, colon-labeled format
		if ( ! empty( $item['tax_item_1'] ) || ! empty( $item['tax_item'] ) ) {

			// get the first tax item
			$_tax_item = ! empty( $item['tax_item_1'] ) ? $item['tax_item_1'] : $item['tax_item'];

			$i = 1;

			while ( $_tax_item ) {

				// turn "label: Tax | tax_amount: 10" into an associative array
				$tax_item_data = $this->importer->split_on_delimiter( $_tax_item, '|' );
				$tax_item_data = $this->importer->split_key_value_pairs( $tax_item_data, ':' );

				$raw_tax_items[] = $tax_item_data;

				// get the next tax item (if any)
				$i++;
				$_tax_item = isset( $item[ 'tax_item_' . $i ] ) ? $item[ 'tax_item_' . $i ] : null;
			}
		}

		// default format
		else if ( ! empty( $item['tax_items'] ) ) {

			if ( $this->importer->is_possibly_json_array( $item['tax_items'] ) ) {

				try {
					$raw_tax_items = $this->importer->parse_json( $item['tax_items'] );
				} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_tax_items_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing taxes for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
				}

			} else {
				$raw_tax_items = $this->importer->parse_delimited_string( $item['tax_items'] );
			}
		}

		// parse extracted (but still raw & uncooked) tax items
		if ( ! empty( $raw_tax_items ) ) {

			$tax_amount_sum = $shipping_tax_amount_sum = 0;

			foreach( $raw_tax_items as $raw_tax_item ) {

				if ( $tax_item = $this->parse_tax_item( $raw_tax_item ) ) {

					$tax_items[] = $tax_item;

					// sum up the order totals, in case it wasn't part of the import
					$tax_amount_sum          += $tax_item['tax_amount'];
					$shipping_tax_amount_sum += $tax_item['shipping_tax_amount'];
				}
			}

			// set calculated tax totals if not defined and not merging
			if ( ! $merging ) {
				if ( ! is_numeric( $tax_total ) ) {
					$tax_total = $tax_amount_sum;
				}

				if ( ! is_numeric( $shipping_tax_total ) ) {
					$shipping_tax_total = $shipping_tax_amount_sum;
				}
			}
		}

		// default tax and shipping totals to zero if not set & not merging
		if ( ! $merging ) {
			if ( ! is_numeric( $tax_total ) ) {
				$tax_total = 0;
			}
			if ( ! is_numeric( $shipping_tax_total ) ) {
				$shipping_tax_total = 0;
			}
		}


		// no tax items specified, so create a default one using the tax totals,
		// but only if a tax total was provided and not merging
		if ( 0 === count( $tax_items ) && ( $tax_total || $shipping_tax_total ) && ! $merging ) {

			$tax_items[] = array(
				'code'                => '',
				'rate_id'             => 0,
				'label'               => esc_html__( 'Tax', 'woocommerce-csv-import-suite' ),
				'compound'            => '',
				'tax_amount'          => $tax_total,
				'shipping_tax_amount' => $shipping_tax_total,
			);
		}

		return $tax_items;
	}


	/**
	 * Parse a single tax item
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $tax_item_data Raw tax item data
	 * @return array|null Parsed tax item data or null
	 */
	private function parse_tax_item( $tax_item_data ) {

		$tax_rates = $this->get_tax_rates();

		// normalize tax fields
		foreach ( $this->tax_item_mapping as $from => $to ) {
			if ( isset( $tax_item_data[ $from ] ) ) {

				$tax_item_data[ $to ] = $tax_item_data[ $from ];
				unset( $tax_item_data[ $from ] );
			}
		}

		// if neither tax or shipping amount provided, bail out
		// TODO: should we throw here instead? {IT 2016-05-30}
		if ( ! isset( $tax_item_data['tax_amount'] ) && ! isset( $tax_item_data['shipping_tax_amount'] ) ) {
			return;
		}

		// default rate id to 0 if not set
		if ( ! isset( $tax_item_data['rate_id'] ) ) {
			$tax_item_data['rate_id'] = 0;
		}

		// keep a reference to the rate_id provided in the CSV file
		$original_rate_id = $tax_item_data['rate_id'];

		// try and look up rate id by code
		// Code is made up of COUNTRY-STATE-NAME-Priority. E.g GB-VAT-1, US-AL-TAX-1.
		// We do this instead of relying blindly on rate_id because tax code is more
		// portable than rate_id across stores
		if ( isset( $tax_item_data['code'] ) ) {

			foreach ( $tax_rates as $tax_rate ) {

				if ( \WC_Tax::get_rate_code( $tax_rate->tax_rate_id ) == $tax_item_data['code'] ) {

					// found the tax by code
					$tax_item_data['rate_id'] = $tax_rate->tax_rate_id;
					$tax_item_data['label']   = $tax_rate->tax_rate_name;
					break;
				}
			}
		}

		// try and look up rate id by label if needed
		if ( ! $tax_item_data['rate_id'] && isset( $tax_item_data['label'] ) && $tax_item_data['label'] ) {
			foreach ( $tax_rates as $tax_rate ) {

				if ( 0 === strcasecmp( $tax_rate->tax_rate_name, $tax_item_data['label'] ) ) {

					// found the tax by label
					$tax_item_data['rate_id'] = $tax_rate->tax_rate_id;
					break;
				}
			}
		}

		// check for a rate being specified which does not exist, and clear it out (technically an error?)
		if ( $tax_item_data['rate_id'] && ! isset( $tax_rates[ $tax_item_data['rate_id'] ] ) ) {
			$tax_item_data['rate_id'] = 0;
		}

		// fetch tax rate code
		if ( $tax_item_data['rate_id'] && ( ! isset( $tax_item_data['code'] ) || $tax_item_data['code'] ) ) {
			$tax_item_data['code'] = \WC_Tax::get_rate_code( $tax_item_data['rate_id'] );
		} else {
			$tax_item_data['code'] = '';
		}

		// default label of 'Tax' if not provided
		if ( ! isset( $tax_item_data['label'] ) || ! $tax_item_data['label'] ) {
			$tax_item_data['label'] = esc_html__( 'Tax', 'woocommerce-csv-import-suite' );
		}

		// default tax amounts to 0 if not set
		if ( ! isset( $tax_item_data['tax_amount'] ) ) {
			$tax_item_data['tax_amount'] = 0;
		}

		if ( ! isset( $tax_item_data['shipping_tax_amount'] ) ) {
			$tax_item_data['shipping_tax_amount'] = 0;
		}

		// handle compound flag by using the defined tax rate value (if any)
		if ( ! isset( $tax_item_data['compound'] ) ) {
			$tax_item_data['compound'] = '';

			if ( $tax_item_data['rate_id'] ) {
				$tax_item_data['compound'] = $tax_rates[ $tax_item_data['rate_id'] ]->tax_rate_compound;
			}
		}

		// store a reference of the original rate_id, so that we can match refund
		// taxes to the correct tax rate
		if ( $tax_item_data['rate_id'] && $tax_item_data['rate_id'] != $original_rate_id ) {
			$tax_item_data['_original_rate_id'] = $original_rate_id;
		}

		return $tax_item_data;
	}


	/**
	 * Parse fee items from raw CSV data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV file
	 * @param array $tax_items Parsed tax items
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Array with 3 items: fee items, fee total and fee tax total
	 */
	public function parse_fee_items( $item, $tax_items, $merging = false ) {

		// get order fee totals
		$fee_tax_total = $fee_total = null;

		foreach ( array( 'fee_total', 'fee_tax_total' ) as $column ) {
			// ignore blanks but allow zeroes
			if ( isset( $item[ $column ] ) && is_numeric( $item[ $column ] ) ) {
				$$column = $item[ $column ];
			}
		}

		$fee_items = array();

		// `fee_items` is supported by the default format(s)
		if ( ! empty( $item['fee_items'] ) ) {

			$fee_amount_sum = $fee_tax_amount_sum = 0;

			if ( $this->importer->is_possibly_json_array( $item['fee_items'] ) ) {

				try {
					$raw_fee_items = $this->importer->parse_json( $item['fee_items'] );
				} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_fee_items_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing fees for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
				}

			} else {
				$raw_fee_items = $this->importer->parse_delimited_string( $item['fee_items'] );
			}

			// parse raw fee items into something edible
			foreach ( $raw_fee_items as $raw_fee_item ) {

				// normalize fee fields
				foreach ( $this->fee_item_mapping as $from => $to ) {
					if ( isset( $raw_fee_item[ $from ] ) ) {

						$raw_fee_item[ $to ] = $raw_fee_item[ $from ];
						unset( $raw_fee_item[ $from ] );
					}
				}

				// make sure total & tax have proper values
				$fee_item_total = $this->get_array_key_value( $raw_fee_item, 'total', 0 );
				$fee_item_tax   = $this->get_array_key_value( $raw_fee_item, 'total_tax', 0 );

				if ( ! is_numeric( $fee_item_total ) ) {
					$fee_item_total = 0;
				}

				if ( ! is_numeric( $fee_item_tax ) ) {
					$fee_item_tax = 0;
				}

				// TODO: should we require the fee name/title? WC-API seems to do so.
				$fee_item = array(
					'order_item_id' => $this->get_array_key_value( $raw_fee_item, 'order_item_id' ),
					'title'         => $this->get_array_key_value( $raw_fee_item, 'name', esc_html__( 'Fee', 'woocommerce-csv-import-suite' ) ),
					'total'         => $fee_item_total,
					'total_tax'     => $fee_item_tax,
					'taxable'       => $this->get_array_key_value( $raw_fee_item, 'taxable', !!$fee_item_tax ),
					'tax_class'     => $this->get_array_key_value( $raw_fee_item, 'tax_class', '' ),
					'tax_data'      => $this->get_array_key_value( $raw_fee_item, 'tax_data' ),
				);

				if ( ! empty( $fee_item['tax_data'] ) && ! empty( $tax_items ) ) {
					$fee_item['tax_data'] = $this->map_tax_data_rates( $fee_item['tax_data'], $tax_items );
				}

				$fee_items[] = $fee_item;

				// sum up the fee totals, in case it wasn't part of the import
				$fee_amount_sum     += $fee_item_total;
				$fee_tax_amount_sum += $fee_item_tax;
			}


			// set fee and tax totals if they were not provided
			if ( ! is_numeric( $fee_total ) ) {
				$fee_total = $fee_amount_sum;
			}

			if ( ! is_numeric( $fee_tax_total ) ) {
				$fee_tax_total = $fee_tax_amount_sum;
			}
		}

		// default fee and tax totals to zero if not set
		if ( ! is_numeric( $fee_total ) ) {
			$fee_total = 0;
		}

		if ( ! is_numeric( $fee_tax_total ) ) {
			$fee_tax_total = 0;
		}

		// no fee items specified, but fee total (should be > 0) is available, so create a default
		// one using the fee totals, but only if not merging
		if ( empty( $fee_items ) && $fee_total > 0 && ! $merging ) {

			$fee_items[] = array(
				'title'          => esc_html__( 'Fee', 'woocommerce-csv-import-suite' ),
				'total'          => $fee_total,
				'total_tax'      => $fee_tax_total,
				'taxable'        => $fee_tax_total != 0,
				'tax_class'      => '',
			);
		}

		return $fee_items;
	}


	/**
	 * Parse coupons from raw CSV data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import and changed from private to public
	 *
	 * @since 3.0.0
	 * @param array $item Raw order data from CSV file
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Array of coupons, if any
	 */
	public function parse_coupons( $item ) {

		$coupons = array();

		if ( isset( $item['coupons'] ) && ! empty( $item['coupons'] ) ) {

			if ( $this->importer->is_possibly_json_array( $item['coupons'] ) ) {

				try {
					$_coupons = $this->importer->parse_json( $item['coupons'] );
				} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_coupons_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing coupons for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
				}

			} else {
				$_coupons = $this->importer->parse_delimited_string( $item['coupons'] );
			}

			foreach ( $_coupons as $_coupon_data ) {

				if ( ! $this->get_array_key_value( $_coupon_data, 'code' ) ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_coupon_code', sprintf( esc_html__( 'Missing coupon code on line %d', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num() ) );
				}

				$coupons[] = $_coupon_data;
			}
		}

		return $coupons;
	}


	/**
	 * Parse refunds
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import and changed from private to public
	 *
	 * @since 3.0.0
	 * @param int $item Raw order data
	 * @param array $order_data Parsed order data, passed by reference
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array|null
	 */
	public function parse_refunds( $item, &$order_data, $merging = false ) {

		$refunds = null;

		// if refunds data is readily available, use that
		if ( ! empty( $item['refunds'] ) ) {

			if ( $this->importer->is_possibly_json_array( $item['refunds'] ) ) {

				try {
					$refunds = $this->importer->parse_json( $item['refunds'] );
				} catch( \WC_CSV_Import_Suite_Import_Exception $e ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_refunds_importer->parse_json_error', sprintf( esc_html__( 'Error while parsing refunds for line %d: %s', 'woocommerce-csv-import-suite' ), $this->importer->get_line_num(), $e->getMessage() ) );
				}

				if ( ! empty( $refunds ) ) {

					foreach ( $refunds as $key => $refund ) {

						if ( ! empty( $refund['line_items'] ) ) {

							foreach ( $refund['line_items'] as $item_key => $refunded_item ) {

								// generate unique id for the refunded item, so that we can later
								// map the refunded items to the order items
								$refunded_item_temp_id = uniqid( 'refunded_item_' );

								// store temp id on refunded line item
								$refunds[ $key ]['line_items'][ $item_key ]['refunded_item_temp_id'] = $refunded_item_temp_id;

								// find the refunded line item, and store the temp id there as well
								foreach ( $this->importer->line_types as $line_type ) {

									if ( empty( $order_data[ $line_type ] ) ) {
										continue;
									}

									foreach ( $order_data[ $line_type ] as $line_key => $order_item ) {

										if ( ! isset( $order_item['order_item_id'] ) ) {
											continue;
										}

										if ( $order_item['order_item_id'] == $refunded_item['refunded_item_id'] ) {
											$order_data[ $line_type ][ $line_key ]['refunded_item_temp_id'] = $refunded_item_temp_id;
										}
									}
								}

								// map taxes to the correct tax rates
								if ( ! empty( $refunded_item['refund_tax'] ) && ! empty( $order_data['tax_lines'] ) ) {
									$refunds[ $key ]['line_items'][ $item_key ]['refund_tax'] = $this->map_tax_data_rates( $refunded_item['refund_tax'], $order_data['tax_lines'] );
								}
							}
						}
					}
				}
			}

			else {

				$refunds = $this->importer->parse_delimited_string( $item['refunds'] );

				if ( ! empty( $refunds ) ) {

					// map refunded line items to the first refund, since we have no way
					// of knowing any better
					$refunds[0]['line_items'] = $this->get_refunded_items( $order_data );
				}
			}

		}


		// If no refunds were provided, extract data about them from other fields unless merging
		if ( ! $refunds && ! $merging ) {

			$refunded_total       = isset( $item['refunded_total'] ) && is_numeric( $item['refunded_total'] ) ? floatval( $item['refunded_total'] ) : null;
			$refunded_items       = array();
			$refunded_items_total = 0;

			$refunded_items = $this->get_refunded_items( $order_data );

			// if total wasn't provided, use the calculated value
			if ( is_null( $refunded_total ) && ! empty( $refunded_items ) ) {

				// add up item refund amounts
				foreach ( $refunded_items as $refunded_item ) {
					$refunded_items_total += $refunded_item['refund_total'];
				}

				if ( $refunded_items_total ) {
					$refunded_total = $refunded_items_total;
				}
			}

			// no total and no refunded items... no refunds!
			if ( ! $refunded_total && empty( $refunded_items ) ) {
				return null;
			}

			$refunds = array(
				array(
					'amount'     => $refunded_total,
					'reason'     => null,
					'line_items' => $refunded_items,
					'date'       => null,
				),
			);
		}

		return $refunds;
	}


	/**
	 * Get refunded items from order data
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.3
	 * @param array $order_data Parsed order data, passed by reference
	 * @return array
	 */
	private function get_refunded_items( &$order_data ) {

		$refunded_items = array();

		foreach ( $this->importer->line_types as $line_type ) {

			if ( empty( $order_data[ $line_type ] ) ) {
				continue;
			}

			foreach ( $order_data[ $line_type ] as $line_key => $order_item ) {

				// this item is not refunded
				if ( empty( $order_item['refunded'] ) || $order_item['refunded'] <= 0 ) {
					continue;
				}

				// generate unique id for the refunded item, so that we can later
				// map the refunded items to the order items
				$refunded_item_temp_id = uniqid( 'refunded_item_' );

				$order_data[ $line_type ][ $line_key ]['refunded_item_temp_id'] = $refunded_item_temp_id;

				$refunded_item = array(
					'refunded_item_temp_id' => $refunded_item_temp_id,
					'refund_total'          => $order_item['refunded'],
				);

				if ( isset( $order_item['refunded_qty'] ) ) {
					$refunded_item['qty'] = $order_item['refunded_qty'];
				}

				if ( isset( $order_item['refunded_tax'] ) ) {
					$refunded_item['refund_tax'] = $order_item['refunded_tax'];
				}

				$refunded_items[] = $refunded_item;
			}
		}

		return $refunded_items;
	}


	/**
	 * Map tax data rate IDs to known tax rate IDs
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $tax_data
	 * @param array $tax_items Parsed tax items
	 * @return array
	 */
	private function map_tax_data_rates( $tax_data, $tax_items ) {

		// sanity check
		if ( ! is_array( $tax_data ) || empty( $tax_data ) ) {
			return array();
		}

		foreach ( $tax_data as $rate_id => $total ) {

			// handle detailed total/subtotal tax_data. in this case $rate_id will be
			// a string key, either total or subtotal, and actual tax data will be it's value
			if ( ! is_numeric( $rate_id ) && is_array( $total ) ) {
				$tax_data[ $rate_id ] = $this->map_tax_data_rates( $total, $tax_items );
			}

			// map each rate_id=>total pair to a known, previously mapped tax rate
			else {
				foreach ( $tax_items as $tax_item ) {

					// found a match on _original_rate_id
					if ( isset( $tax_item['_original_rate_id'] ) && $tax_item['_original_rate_id'] == $rate_id ) {

						// remove taxes for original rate_id
						unset( $tax_data[ $rate_id ] );

						// and assign them to the found rate
						$tax_data[ $tax_item['rate_id'] ] = $total;
					}
				}
			}
		}

		return $tax_data;
	}


	/**
	 * Match order lines to existing order lines
	 *
	 * @since 3.3.0
	 * @param array $order_data parsed order data
	 * @param string $csv_file_format csv file format
	 * @return array modified order data
	 */
	public function match_order_lines( $order_data, $csv_file_format ) {

		foreach ( $this->importer->line_types as $line_type => $key ) {

			if ( empty( $order_data[ $key ] ) ) {
				continue;
			}

			$items = $this->match_order_items( $order_data['id'], $order_data[ $key ], $line_type, $csv_file_format );

			// if there were errors, skip merging any order items of the same type,
			// as we can't reliably merge on errors.
			if ( is_wp_error( $items ) ) {
				wc_csv_import_suite()->log( $items->get_error_message() );

				// unsetting the order item type array will ensure that items of this
				// type will not be touched during processing, see: process_items()
				unset( $order_data[ $key ] );
				break;
			}

			$order_data[ $key ] = $items;
		}

		return $order_data;
	}


	/**
	 * Match order items to existing order items
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param int $order_id
	 * @param array $items
	 * @param string $type
	 * @param string $csv_file_format
	 * @return array Items
	 */
	private function match_order_items( $order_id, $items, $type, $csv_file_format ) {

		$order    = wc_get_order( $order_id );
		$existing = $order->get_items( $type );

		foreach ( $items as $key => $item ) {

			$order_item_id = $this->get_matching_order_item_id( $existing, $item, $type, $csv_file_format );

			if ( is_wp_error( $order_item_id ) ) {
				return $order_item_id;
			}

			if ( $order_item_id ) {

				$items[ $key ]['order_item_id'] = $order_item_id;

			} else {

				switch ( $type ) {

					case 'line_item':
						$message = sprintf( __( '> > Cannot update product "%s" without order item ID. Inserting instead.', 'woocommerce-csv-import-suite' ), $item['product_id'] );
					break;

					case 'coupon':
						$message = sprintf( __( '> > Cannot update coupon "%s" without order item ID. Inserting instead.', 'woocommerce-csv-import-suite' ), $item['code'] );
					break;

					case 'fee':
						$message = sprintf( __( '> > Cannot update fee "%s" without order item ID. Inserting instead.', 'woocommerce-csv-import-suite' ), $item['title'] );
					break;

					case 'shipping':
						$message = sprintf( __( '> > Cannot update shipping method "%s" without order item ID. Inserting instead.', 'woocommerce-csv-import-suite' ), $item['title'] );
					break;

					case 'tax':
						$message = sprintf( __( '> > Cannot update tax rate "%s" without order item ID. Inserting instead.', 'woocommerce-csv-import-suite' ), $item['code'] );
					break;
				}

				if ( $message ) {
					wc_csv_import_suite()->log( $message );
				}
			}
		}

		return $items;
	}


	/**
	 * Match a single order item to an existing item
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $existing
	 * @param array $item
	 * @param string $type
	 * @param string $csv_file_format
	 * @return int|null Order item ID or null if no match was found
	 */
	private function get_matching_order_item_id( $existing, $item, $type, $csv_file_format ) {

		$order_item_id = null;

		// try matching based on provided order_item_id
		if ( isset( $item['order_item_id'] ) && $item['order_item_id'] ) {

			$new_order_item_id = $item['order_item_id'];

			// first, try matching based on _original_order_item_id
			foreach ( $existing as $existing_order_item_id => $existing_item ) {

				$_meta                   = $existing_item['item_meta'];
				$_original_order_item_id = isset( $_meta['_original_order_item_id'] ) && isset( $_meta['_original_order_item_id'][0] )
																 ? $_meta['_original_order_item_id'][0]
																 : null;

				if ( $_original_order_item_id && $new_order_item_id == $_original_order_item_id ) {
					$order_item_id = $existing_order_item_id;
					break; // we're extremely happy with the first match we've found :)
				}
			}

			// if no match was found, try matching directly on order_item_id
			if ( ! $order_item_id ) {

				if ( isset( $existing[ $new_order_item_id ] ) ) {
					$order_item_id = $new_order_item_id;
				}
			}
		}

		// No direct match was found, try matching based on other properties.
		//
		// This will give a chance for older CSV formats to be able to merge orders.
		// The basic idea is that if there is a property that could be used to
		// more or less uniquely identify an order item, we try it. If we find only
		// a single match (probably 95% of cases), it should be fairly safe to
		// update an item based on that. If there are multiple matches (which can
		// happen with a more complex store setup or order ), an error should be
		// returned instead, which will result in the current order being skipped.
		if ( ! $order_item_id ) {

			$properties = array();

			switch ( $type ) {

				case 'line_item':
					$properties = array( 'variation_id' => 'product_id', 'product_id' => 'product_id', 'name' => 'title' );
				break;

				case 'coupon':
				case 'fee':
					$properties = array( 'name' => 'title' );
				break;

				case 'shipping':
					$properties = array( 'method_id' => 'method_id', 'name' => 'title' );
				break;

				case 'tax':
					$properties = array( 'rate_id' => 'rate_id', 'name' => 'code' );
				break;
			}

			$order_item_id = $this->get_matching_order_item_id_by_properties( $existing, $item, $type, $properties );
		}


		/**
		 * Filters the matching order_item_id when updating order
		 *
		 * @since 3.0.0
		 * @param mixed $order_item_id order item identifier
		 * @param array|object $item order item data
		 * @param array $existing_items array of existing order items
		 * @param string $type order item type
		 * @param string $csv_file_format CSV file format
		 */
		return apply_filters( 'wc_csv_import_suite_found_order_item_id', $order_item_id, $item, $existing, $type, $csv_file_format );
	}


	/**
	 * Try to match an order item by comparing a list of properties
	 *
	 * Property order is significant - higher priorty properties should be listed
	 * first.
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $existing Existing items
	 * @param array $item Item to be inserted/merged
	 * @param string $type Order item type
	 * @param array $properties Prioritized list of properties to match against
	 * @return int|null|WP_Error
	 */
	private function get_matching_order_item_id_by_properties( $existing, $item, $type, $properties = array() ) {

		if ( empty( $existing ) ) {
			return null;
		}

		$order_item_id = null;
		$matches       = array();

		// loop over prioritized properties and try to find a match
		foreach ( $properties as $existing_item_property => $item_property ) {

			// compare each existing item with the one at hand, one by one
			foreach ( $existing as $existing_order_item_id => $existing_item ) {

				// get all matches
				if ( isset( $existing_item[ $existing_item_property ] ) &&
						 isset( $item[ $item_property ] ) &&
						 $existing_item[ $existing_item_property ] == $item[ $item_property ] ) {
					$matches[] = $existing_order_item_id;
				}
			}

			// some matches were found on this property
			if ( ! empty( $matches ) ) {

				// more than 1 item matches - this means that we cannot reliably
				// determine which existing order item to update, so we must fail
				// TODO: Idea: we could also check if the new items also include multiple
				// items with the same "identifier". If not, then it _should_ be fairly
				// safe to match against the first one, as the others will be deleted
				// anyway.
				if ( count( $matches ) > 1 ) {

					// we're passing WP_Error here to give 3rd parties a chance to adjust
					// the found order_item_id before failure

					switch ( $type ) {

						case 'line_item':
							$message = sprintf( __( '> > Cannot update product "%s" without order item ID, as multiple similar products exist for the order and no direct match could be determined. Skipping merging line items.', 'woocommerce-csv-import-suite' ), $item['name'] );
						break;

						case 'coupon':
							$message = sprintf( __( '> > Cannot update coupon "%s" without order item ID, as multiple similar coupons exist for the order and no direct match could be determined. Skipping merging coupons.', 'woocommerce-csv-import-suite' ), $item['code'] );
						break;

						case 'fee':
							$message = sprintf( __( '> > Cannot update fee "%s" without order item ID, as multiple similar fees exist for the order and no direct match could be determined. Skipping merging fees.', 'woocommerce-csv-import-suite' ), $item['title'] );
						break;

						case 'shipping':
							$message = sprintf( __( '> > Cannot update shipping method "%s" without order item ID, as multiple similar shipping methods exist for the order and no direct match could be determined. Skipping merging shipping methods.', 'woocommerce-csv-import-suite' ), $item['title'] );
						break;

						case 'tax':
							$message = sprintf( __( '> > Cannot update tax rate "%s" without order item ID, as multiple similar tax rates exist for the order and no direct match could be determined. Skipping merging tax rates.', 'woocommerce-csv-import-suite' ), $item['code'] );
						break;

						default:
							$message = __( 'Cannot determine a unique match for order item', 'woocommerce-csv-import-suite' );
						break;
					}

					return new \WP_Error( 'wc_csv_import_suite_ambiguous_order_item_match', $message );
				}
				// we've found the "perfect" match
				else {
					return $matches[0];
				}
			}

		}

		return $order_item_id;
	}


	/** Helper methods ******************************************************/


	/**
	 * Safely get a value for a key from an array
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param array $array
	 * @param int|string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private function get_array_key_value( $array, $key, $default = null ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : $default;
	}


	/**
	 * Get known, available shipping methods
	 *
	 * Caches the results for subsequent calls
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @return array
	 */
	private function get_available_shipping_methods() {

		if ( ! isset( $this->available_shipping_methods ) ) {
			$this->available_shipping_methods = WC()->shipping()->load_shipping_methods();
		}

		return $this->available_shipping_methods;
	}


	/**
	 * Get all defined tax rates, keyed off of ID
	 *
	 * Caches the results for subsequent calls
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Items_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @return array
	 */
	private function get_tax_rates() {

		if ( ! isset( $this->tax_rates ) ) {

			$this->tax_rates = array();

			global $wpdb;

			foreach ( $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates" ) as $row ) {
				$this->tax_rates[ $row->tax_rate_id ] = $row;
			}
		}

		return $this->tax_rates;
	}

}
