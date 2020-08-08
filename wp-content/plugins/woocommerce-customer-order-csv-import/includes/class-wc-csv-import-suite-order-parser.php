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
 * WooCommerce Order Parser class for parsing the raw order data from CSV file
 *
 * @since 3.3.0
 */
class WC_CSV_Import_Suite_Order_Parser {


	/** @var \WC_CSV_Import_Suite_Order_Import order importer instance */
	private $importer;

	/** @var \WC_CSV_Import_Suite_Order_Items_Parser order items parser instance */
	private $items_parser;

	/** @var array Known order meta fields */
	private $order_meta_fields;

	/** @var array Known order address fields */
	private $order_address_fields;

	/** @var array order statuses holder */
	private $order_statuses_clean;

	/** @var array order shipping methods holder */
	private $available_payment_gateways;


	/**
	 * Constructs and initializes the parser
	 *
	 * @since 3.3.0
	 */
	public function __construct() {

		$this->importer     = wc_csv_import_suite()->get_importers_instance()->get_importer( 'woocommerce_order_csv' );
		$this->items_parser = wc_csv_import_suite()->load_class( '/includes/class-wc-csv-import-suite-order-items-parser.php', 'WC_CSV_Import_Suite_Order_Items_Parser' );

		// either column/meta_key match, or provide a meta_key => column association
		$this->order_meta_fields = array(
			'order_tax'          => 'tax_total',
			'order_shipping'     => 'shipping_total',
			'order_shipping_tax' => 'shipping_tax_total',
			'cart_discount',
			'order_total',
			'payment_method',
			'customer_user',
			'download_permissions_granted',
		);

		$this->order_address_fields = array(
			'first_name',
			'last_name',
			'company',
			'email',
			'phone',
			'address_1',
			'address_2',
			'city',
			'state',
			'postcode',
			'country',
		);
	}


	/**
	 * Parses raw order data, building and returning an array of order data
	 * to import into the database.
	 *
	 * The order data is broken into two portions:	the couple of defined fields
	 * that make up the wp_posts table, and then the name-value meta data that is
	 * inserted into the wp_postmeta table.	Within the meta data category, there
	 * are known meta fields, such as 'billing_first_name' for instance, and then
	 * arbitrary meta fields are allowed and identified by a CSV column title with
	 * the prefix 'meta:'.
	 *
	 * @since 3.3.0
	 * @param array $item Raw order data from CSV
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array Parsed order data
	 */
	public function parse_order( $item, $options = array(), $raw_headers = array() ) {

		// default options, see more in WC_CSV_Import_Suite_Importer::dispatch()
		$options = wp_parse_args( $options, array(
			'recalculate_totals'                  => false,
			'insert_non_matching'                 => false,
			'allow_unknown_products'              => false,
			'reduce_product_stock'                => true,
			'use_addresses_from_customer_profile' => false,
		) );

		$csv_file_format = $this->importer->detect_csv_file_format( $raw_headers );

		$order_data = $postmeta = $terms = array();

		/* translators: Placeholders: %s - row number */
		$preparing = $options['merge'] ? __( '> Row %s - preparing for merge.', 'woocommerce-csv-import-suite' ) : __( '> Row %s - preparing for import.', 'woocommerce-csv-import-suite' );
		wc_csv_import_suite()->log( sprintf( $preparing, $this->importer->get_line_num() ) );

		list( $order_number, $order_number_formatted ) = $this->parse_order_number( $item, $csv_file_format );

		// set order number
		if ( ! is_null( $order_number ) ) {
			$order_data['order_number'] = $order_number;
		}

		if ( $order_number_formatted ) {
			$order_data['order_number_formatted'] = $order_number_formatted;
		}

		// determine order id
		$order_id = $this->parse_order_id( $item, $order_number_formatted, $options );

		if ( $order_id ) {
			$order_data['id'] = $order_id;
		}

		// we can only merge if a matching order id was found
		$merging = $options['merge'] && $order_id;


		// use isset check for customer, since 0 = guest customer, which would be considered empty
		if ( isset( $item['customer_user'] ) || ! $merging ) {
			$order_data['customer_id'] = $this->parse_customer_user( $item, $merging );
		}

		if ( ! empty( $item['status'] ) || ! $merging ) {
			$order_data['status'] = $this->parse_order_status( $item );
		}

		if ( ! empty( $item['date'] ) || ! $merging ) {
			$order_data['date'] = $this->importer->parse_order_date( $item );
		}

		// parse addresses
		$order_data = $this->parse_addresses( $order_data, $item, $options );

		// prepare order notes
		if ( ! empty( $item['order_notes'] ) ) {
			$order_data['order_notes'] = explode( '|', $item['order_notes'] );
		}

		// prepare customer notes
		if ( ! empty( $item['customer_note'] ) ) {
			$order_data['note'] = $item['customer_note'];
		}

		// parse meta & terms
		list( $postmeta, $terms ) = $this->parse_order_meta( $item, $merging );

		// totals
		$tax_total          = isset( $postmeta['_order_tax'] )          ? $postmeta['_order_tax']          : null;
		$shipping_total     = isset( $postmeta['_order_shipping'] )     ? $postmeta['_order_shipping']     : null;
		$shipping_tax_total = isset( $postmeta['_order_shipping_tax'] ) ? $postmeta['_order_shipping_tax'] : null;

		// taxes & tax total
		$tax_items = $this->items_parser->parse_tax_items( $item, $tax_total, $shipping_tax_total, $merging );

		// shipping items and shipping total
		$shipping_items = $this->items_parser->parse_shipping_items( $item, $shipping_total, $csv_file_format, $tax_items, $merging );

		// line items
		$line_items = $this->items_parser->parse_line_items( $item, $csv_file_format, $options['allow_unknown_products'], $tax_items, $merging );

		// unless merging, require at least 1 line item
		if ( ! $merging && empty( $line_items ) ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_line_items', esc_html__( 'Cannot import order without line items', 'woocommerce-csv-import-suite' ) );
		}

		// order fees
		$fee_items = $this->items_parser->parse_fee_items( $item, $tax_items, $merging );

		// coupons
		$coupons = $this->items_parser->parse_coupons( $item );


		// re-add the order tax totals to the order meta, in case they were calculated based on order items
		if ( ! is_null( $tax_total ) ) {
			$postmeta['_order_tax'] = number_format( (float) $tax_total, 2, '.', '' );
		}

		if ( ! is_null( $shipping_total ) ) {
			$postmeta['_order_shipping'] = number_format( (float) $shipping_total, 2, '.', '' );
		}

		if ( ! is_null( $shipping_tax_total ) ) {
			$postmeta['_order_shipping_tax'] = number_format( (float) $shipping_tax_total, 2, '.', '' );
		}


		// put parsed data together
		if ( ! empty( $postmeta ) ) {
			$order_data['order_meta'] = $postmeta;
		}

		if ( ! empty( $terms ) ) {
			$order_data['terms'] = $terms;
		}

		if ( ! empty( $line_items) ) {
			$order_data['line_items'] = $line_items;
		}

		if ( ! empty( $shipping_items) ) {
			$order_data['shipping_lines'] = $shipping_items;
		}

		if ( ! empty( $tax_items) ) {
			$order_data['tax_lines'] = $tax_items;
		}

		if ( ! empty( $fee_items) ) {
			$order_data['fee_lines'] = $fee_items;
		}

		if ( ! empty( $coupons) ) {
			$order_data['coupon_lines'] = $coupons;
		}

		// refunds
		$refunds = $this->items_parser->parse_refunds( $item, $order_data );

		if ( ! empty( $refunds ) ) {
			$order_data['refunds'] = $refunds;
		}


		// when merging, try to match items to existing order items
		if ( $merging ) {
			$order_data = $this->items_parser->match_order_lines( $order_data, $csv_file_format );
		}


		// parse & validate currency
		if ( ! empty( $item['currency'] ) ) {

			$currency_codes = array_keys( get_woocommerce_currencies() );

			if ( ! in_array( strtoupper( $item['currency'] ), $currency_codes ) ) {
				// invalid currency code
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_currency_code', sprintf( __( 'Skipped. Invalid or unsupported currency %s.', 'woocommerce-csv-import-suite' ),	$item['currency'] ) );
			} else {
				$order_data['currency'] = $item['currency'];
			}
		}


		/**
		 * Filters parsed order data
		 *
		 * Gives a chance for 3rd parties to parse data from custom columns
		 *
		 * @since 3.1.2
		 * @param array $order Parsed order data
		 * @param array $data Raw order data from CSV
		 * @param array $options Import options
		 * @param array $raw_headers Raw CSV headers
		 */
		return apply_filters( 'wc_csv_import_suite_parsed_order_data', $order_data, $item, $options, $raw_headers );
	}


	/**
	 * Parses the order number
	 *
	 * Returns an array with two values: order number and the formatted order number
	 *
	 * @since 3.3.0
	 * @param array $item raw order data from csv
	 * @param string $csv_file_format csv file format
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array
	 */
	private function parse_order_number( $item, $csv_file_format ) {

		$is_csv_export_file = Framework\SV_WC_Helper::str_starts_with( $csv_file_format, 'csv_export' );

		// standard format: optional integer order number and formatted order number
		$order_number           = ! empty( $item['order_number'] )           ? $item['order_number']           : null;
		$order_number_formatted = ! empty( $item['order_number_formatted'] ) ? $item['order_number_formatted'] : $order_number;

		// Customer/Order CSV Export plugin format. If the Sequential
		// Order Numbers Pro plugin is installed, order_number will be
		// available, if the Order ID is numeric use that, but otherwise
		// we have no idea what the underlying sequential order number might be
		if ( $is_csv_export_file ) {
			$order_number_formatted = ! empty( $item['order_id'] ) ? $item['order_id'] : null;
		}

		// use formatted for underlying order number if order number not supplied
		if ( is_numeric( $order_number_formatted ) && ! $order_number ) {
			$order_number = $order_number_formatted;
		}

		// validate the supplied formatted order number/order number
		if ( ! $is_csv_export_file && $order_number && ! is_numeric( $order_number ) ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_order_number_invalid', sprintf( __( 'Order number field must be an integer: %s.', 'woocommerce-csv-import-suite' ), $order_number ) );
		}

		if ( ! $is_csv_export_file && $order_number_formatted && ! $order_number ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_numerical_order_number', __( 'Formatted order number provided but no numerical order number, see the documentation for further details.', 'woocommerce-csv-import-suite' ) );
		}

		return array( $order_number, $order_number_formatted );
	}


	/**
	 * Parses the order identifier
	 *
	 * Returns an array with two values: order number and the formatted order number
	 *
	 * @since 3.3.0
	 * @param array $item raw order data from csv
	 * @param string $order_number_formatted the formated order number
	 * @param array $options import options
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return int|null order id or null if no matching order found
	 */
	private function parse_order_id( $item, $order_number_formatted, $options ) {

		$order_id = null;

		// prepare for merging
		if ( $options['merge'] ) {

			$order_id    = ! empty( $item['order_id'] ) ? $item['order_id'] : null;
			$found_order = false;

			// check that at least one required field for merging is provided
			if ( ! $order_id && ! $order_number_formatted ) {
				wc_csv_import_suite()->log( __( '> > Cannot merge without id or order number. Importing instead.', 'woocommerce-csv-import-suite' ) );
				return null;
			}

			// check if order exists

			// 1. try matching order number
			elseif ( $order_number_formatted ) {

				$found_order = $this->get_order_id_by_formatted_number( $order_number_formatted );

				// and secondly allowing other plugins to return an entirely different order number if the simple search above doesn't do it for them
				$found_order = apply_filters( 'woocommerce_find_order_by_order_number', $found_order, $order_number_formatted );

				if ( ! $found_order ) {

					if ( ! $order_id ) {

						if ( $options['insert_non_matching'] ) {
							wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find order with formatted number %s. Importing instead.', 'woocommerce-csv-import-suite' ), $order_number_formatted ) );
							return null;
						} else {
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_order', sprintf( __( 'Cannot find order with formatted number %s.', 'woocommerce-csv-import-suite' ), $order_number_formatted ) );
						}

					} else {
						// we can keep trying with order ID
						wc_csv_import_suite()->log( sprintf( __( '> > Cannot find order with formatted number %s.', 'woocommerce-csv-import-suite' ), $order_number_formatted ) );
					}

				} else {
					wc_csv_import_suite()->log( sprintf( __( '> > Found order with formatted number %s.', 'woocommerce-csv-import-suite' ), $order_number_formatted ) );
					$order_id = $found_order;
				}

			}

			// check if an order with the same ID exists
			if ( ! $found_order && $order_id ) {

				if ( 'shop_order' !== get_post_type( $order_id ) ) {

					if ( $options['insert_non_matching'] ) {
						wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find order with id %s. Importing instead.', 'woocommerce-csv-import-suite' ), $order_id ) );
						return null;
					} else {
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_order', sprintf( __( 'Cannot find order with id %s.', 'woocommerce-csv-import-suite' ), $order_id ) );
					}

				} else {
					wc_csv_import_suite()->log( sprintf( __( '> > Found order with ID %s.', 'woocommerce-csv-import-suite' ), $order_id ) );
				}
			}
		}

		// prepare for importing
		elseif ( $order_number_formatted ) {

			// ensure the order does not exist
			$order_id = $this->get_order_id_by_formatted_number( $order_number_formatted );

			if ( $order_id ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_order_already_exists', sprintf( __( 'Order %s already exists.', 'woocommerce-csv-import-suite' ), $order_number_formatted ) );
			}
		}

		return $order_id;
	}


	/**
	 * Parses the customer user
	 *
	 * Tries to match user first based on `customer_user` field and then on `billing_email`.
	 * When no match is found, will return `0` to indicate guest customer or `null` if merging
	 * and the `customer_user` column was not present in the CSV file. This allows users to
	 * only partially update an order, skipping `customer_user` column and keeping the original
	 * data intact.
	 *
	 * @since 3.3.0
	 * @param array $item raw order data from CSV
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return int|null customer id if found, 0 in case of guest customer and null if merging and data not present
	 */
	private function parse_customer_user( $item, $merging = false ) {

		$found_customer = false;
		$customer_id    = null;

		// if the `customer_user` column is set, try to get user id and default to `0` (guest)
		// if no user was found, regardless if merging or not
		if ( isset( $item['customer_user'] ) && $item['customer_user'] ) {

			if ( is_numeric( $item['customer_user'] ) ) {

				$user = get_user_by( 'id', $item['customer_user'] );

				if ( ! $user ) {

					$message = sprintf( __( 'Cannot find customer with id %s.', 'woocommerce-csv-import-suite' ), $item['customer_user'] );

					if ( $merging ) {
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_customer', $message );
					} else {
						wc_csv_import_suite()->log( $message );
					}

				} else {
					$found_customer = $user->ID;
				}
			}

			// check by email
			elseif ( is_email( $item['customer_user'] ) ) {
				$found_customer = email_exists( $item['customer_user'] );
			}

			// "But I still haven't found what I'm looking for..." ♫
			if ( ! $found_customer ) {
				$found_customer = username_exists( $item['customer_user'] );
			}

			// guest checkout - even if merging, since the column was present
			if ( ! $found_customer ) {
				$customer_id = 0;
			}
		}

		// see if we can link the user by billing email, but only if not merging
		if ( ! $found_customer && ! $merging && ! empty( $item['billing_email'] ) ) {

			// returns the customer id
			$found_customer = email_exists( $item['billing_email'] );
		}

		if ( $found_customer ) {
			$customer_id = $found_customer;
		}
		// set guest checkout, unless merging
		elseif ( ! $merging ) {
			$customer_id = 0;
		}

		return $customer_id;
	}


	/**
	 * Parses order status
	 *
	 * @since 3.3.0
	 * @param array $item raw order data
	 * @throws WC_CSV_Import_Suite_Import_Exception
	 * @return string $status order status
	 */
	private function parse_order_status( $item ) {

		// default status
		$status = 'processing';

		// validate order status
		if ( ! empty( $item['status'] ) ) {

			$item['status'] = str_replace( 'wc-', '', strtolower( $item['status'] ) );
			$order_statuses = $this->get_order_statuses_clean();

			// unknown order status
			if ( ! array_key_exists( $item['status'], $order_statuses ) ) {
				/* translators: Placeholders: %1$s - order status, %2$s - available order statuses */
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_order_status', sprintf( __( 'Unknown order status %1$s (%2$s).', 'woocommerce-csv-import-suite' ), $item['status'], implode( ', ', array_keys( $order_statuses ) ) ) );
			} else {
				$status = $item['status'];
			}

		}

		return $status;
	}


	/**
	 * Parses order addresses.
	 *
	 * @since 3.3.0
	 *
	 * @param array $order_data parsed order data
	 * @param array $item raw order data from csv
	 * @param array $options import options
	 * @throws WC_CSV_Import_Suite_Import_Exception
	 * @return array modified order data
	 */
	private function parse_addresses( $order_data, $item, $options ) {

		$merging = $options['merge'] && ! empty( $order_data['id'] );

		// construct order addresses
		$address_types = array( 'billing_', 'shipping_' );
		$order_data['billing_address']  = array();
		$order_data['shipping_address'] = array();

		foreach ( $address_types as $address_type ) {

			foreach ( $this->order_address_fields as $field ) {

				$key = $address_type . $field;

				if ( isset( $item[ $key ] ) ) {

					$value = wc_clean( $item[ $key ] );

					// support country name or code by converting to code
					if ( 'country' === $field && $country_code = array_search( $value, WC()->countries->countries, true ) ) {
						$value = $country_code;
					}

					if ( 'email' === $field && ! is_email( $value ) ) {
						/* translators: Placeholders: %s - email address */
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_email', sprintf( __( 'Invalid email: %s.', 'woocommerce-csv-import-suite' ),	$value ) );
					}

					$order_data[ $address_type . 'address' ][ $field ] = $value;
				}
			}
		}

		// if shipping address was not provided, copy it from the billing address
		if ( ! $merging && empty( $order_data['shipping_address'] ) ) {
			$order_data['shipping_address'] = $order_data['billing_address'];
		}

		// if no address columns were provided at all, copy them from customer profile when needed
		if ( empty( $order_data['billing_address'] ) && empty( $order_data['shipping_address'] ) ) {

			if ( ! empty( $order_data['customer_id'] ) && $options['use_addresses_from_customer_profile'] ) {

				foreach ( $address_types as $address_type ) {

					foreach ( $this->order_address_fields as $field ) {

						$key   = $address_type . $field;
						$value = get_user_meta( $order_data['customer_id'], $key, true );

						$order_data[ $address_type . 'address' ][ $field ] = $value;
					}
				}
			}
		}

		return $order_data;
	}


	/**
	 * Parses order meta & terms
	 *
	 * @since 3.3.0
	 * @param array $item raw order data
	 * @param bool $merging Optional. Whether we are merging or inserting a new order. Defaults to false.
	 * @return array
	 */
	private function parse_order_meta( $item, $merging = false ) {

		$postmeta = $terms = array();

		// get any known order meta fields, and default any missing ones to 0/null
		// the provided shipping/payment method will be used as-is, and if found in the list of available ones, the respective titles will also be set
		foreach ( $this->order_meta_fields as $meta_key => $column ) {

			if ( is_numeric( $meta_key ) ) {
				$meta_key = $column;
			}

			switch ( $column ) {

				case 'customer_user':
					// customer_user is handled outside of meta scope
				break;

				case 'payment_method':

					$value              = isset( $item[ $column ] ) ? $item[ $column ] : '';
					$available_gateways = $this->get_available_payment_gateways();

					// look up payment method by id or title
					$payment_method = isset( $available_gateways[ $value ] ) ? $value : null;

					if ( ! $payment_method ) {
						// try by title
						foreach ( $available_gateways as $method ) {

							if ( 0 === strcasecmp( $method->title, $value ) ) {
								$payment_method = $method->id;
								break; // go with the first one we find
							}
						}
					}

					if ( $payment_method ) {
						// known payment method found
						$postmeta['_payment_method']       = $payment_method;
						$postmeta['_payment_method_title'] = $available_gateways[ $payment_method ]->title;
					} elseif ( $value ) {
						// standard format, payment method but no title
						$postmeta['_payment_method']       = $value;
						$postmeta['_payment_method_title'] = '';
					} elseif ( ! $merging ) {
						// none
						$postmeta['_payment_method']       = '';
						$postmeta['_payment_method_title'] = '';
					}
				break;

				// handle numerics
				case 'shipping_total':
				case 'tax_total':
				case 'shipping_tax_total':
				case 'cart_discount':
				case 'order_total':
					// ignore blanks but allow zeroes
					if ( isset( $item[ $column ] ) && is_numeric( $item[ $column ] ) ) {
						$postmeta[ '_' . $meta_key ] = number_format( (float) $item[ $column ], 2, '.', '' );
					}
				break;

				default:
					if ( isset( $item[ $column ] ) ) {
						$postmeta[ '_' . $meta_key ] = $item[ $column ];
					}
				break;
			}
		}

		// get any custom meta fields
		foreach ( $item as $key => $value ) {

			if ( ! $value ) {
				continue;
			}

			// handle meta: columns - import as custom fields
			if ( Framework\SV_WC_Helper::str_starts_with( $key, 'meta:' ) ) {

				// get meta key name
				$meta_key = trim( str_replace( 'meta:', '', $key ) );

				// skip known meta fields
				if ( in_array( $meta_key, $this->order_meta_fields ) ) {
					continue;
				}

				// add to postmeta array
				$postmeta[ $meta_key ] = $value;
			}

			// handle tax: columns - import as taxonomy terms
			elseif ( Framework\SV_WC_Helper::str_starts_with( $key, 'tax:' ) ) {

				$results = $this->importer->parse_taxonomy_terms( $key, $value );

				if ( ! $results ) {
					continue;
				}

				// add to array
				$terms[] = array(
					'taxonomy' => $results[0],
					'terms'    => $results[1],
				);
			}
		}

		return array( $postmeta, $terms );
	}


	/** Helper methods ******************************************************/


	/**
	 * Returns order ID by looking up the order by the formatted order number
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @param int|string $formatted_number
	 * @return int Found order ID or 0 if no match found
	 */
	private function get_order_id_by_formatted_number( $formatted_number ) {

		// we'll give 3rd party plugins two chances to hook in their custom order number facilities:
		// first by performing a simple search using the order meta field name used by both this and the
		// Sequential Order Number Pro plugin, allowing other plugins to filter over it if needed,
		// while still providing this plugin with some base functionality
		$query_args = array(
			'numberposts' => 1,
			'meta_key'    => apply_filters( 'woocommerce_order_number_formatted_meta_name', '_order_number_formatted' ),
			'meta_value'  => $formatted_number,
			'post_type'   => 'shop_order',
			'post_status' => array_keys( wc_get_order_statuses() ),
			'fields'      => 'ids',
		);

		$order_id = 0;
		$orders   = get_posts( $query_args );

		if ( ! empty( $orders ) ) {
			list( $order_id ) = $orders;
			return $order_id;
		}

		// If we haven't found an order using the formatted meta key, keep checking
		// for the _order_number key (supports free version of Seq Order Numbers).
		else {

			$query_args['meta_key'] = apply_filters( 'woocommerce_order_number_meta_name', '_order_number' );
			$_orders                = get_posts( $query_args );

			if ( ! empty ( $_orders ) ) {
				list( $order_id ) = $_orders;
			}
		}

		return $order_id;
	}


	/**
	 * Returns WC order statuses without the prefix
	 *
	 * Caches the results for subsequent calls
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @return array
	 */
	private function get_order_statuses_clean() {

		if ( ! isset( $this->order_statuses_clean ) ) {

			$this->order_statuses_clean = array();

			foreach ( wc_get_order_statuses() as $slug => $name ) {
				$this->order_statuses_clean[ preg_replace( '/^wc-/', '', $slug ) ] = $name;
			}
		}

		return $this->order_statuses_clean;
	}


	/**
	 * Returns known, available payment gateways
	 *
	 * Caches the results for subsequent calls
	 *
	 * In 3.3.0 moved to \WC_CSV_Import_Suite_Order_Parser from \WC_CSV_Import_Suite_Order_Import
	 *
	 * @since 3.0.0
	 * @return array
	 */
	private function get_available_payment_gateways() {

		if ( ! isset( $this->available_payment_gateways ) ) {
			$this->available_payment_gateways = WC()->payment_gateways->payment_gateways();
		}

		return $this->available_payment_gateways;
	}

}
