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
 * WooCommerce Coupon Importer class for managing the import process of a CSV file.
 *
 * Coupon CSV column names are based on field names defined in WC_API_Coupons,
 * with a few exceptions: ID-based fields (such as product_ids) have been
 * replaced with SKU/slug-based fields (such as products) it's much more
 * likely that an external source will use the same SKUs/slugs rather than IDs.
 * The same applies to product categories:
 *
 * - product_ids => products
 * - exclude_product_ids => exclude_products
 * - product_category_ids => product_categories
 * - exclude_product_category_ids => exclude_product_categories
 *
 * @since 1.0.0
 *
 * Renamed from WC_CSV_Coupon_Import to WC_CSV_Import_Suite_Coupon_Import in 3.0.0
 */
class WC_CSV_Import_Suite_Coupon_Import extends \WC_CSV_Import_Suite_Importer {


	/** @var array Known coupon data fields */
	private $coupon_data_fields;

	/** @var array Known coupon meta fields */
	private $coupon_meta_fields;

	/** @var array Coupon meta <> input data field name mappings, based on WC_API_Coupons */
	private $coupon_meta_data_mapping;


	/**
	 * Construct and initialize the importer
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->title = __( 'Import Coupons', 'woocommerce-csv-import-suite' );

		$this->coupon_data_fields = array(
			'type', // required
			'amount',
			'enable_free_shipping',
			'expiry_date',
			'individual_use',
			'minimum_amount',
			'maximum_amount',
			'exclude_sale_items',
			'products',
			'exclude_products',
			'product_categories',
			'exclude_product_categories',
			'customer_emails',
			'usage_limit',
			'usage_limit_per_user',
			'usage_count',
			'limit_usage_to_x_items',
		);

		$this->coupon_meta_fields = array(
			'discount_type',
			'coupon_amount',
			'free_shipping',
			'expiry_date',
			'individual_use',
			'minimum_amount',
			'maximum_amount',
			'exclude_sale_items',
			'product_ids',
			'exclude_product_ids',
			'product_categories',
			'exclude_product_categories',
			'customer_email',
			'usage_limit',
			'usage_limit_per_user',
			'usage_count',
			'limit_usage_to_x_items',
		);

		$this->coupon_meta_data_mapping = array(
			'discount_type'              => 'type',
			'coupon_amount'              => 'amount',
			'free_shipping'              => 'enable_free_shipping',
			'product_categories'         => 'product_category_ids',
			'exclude_product_categories' => 'exclude_product_category_ids',
			'customer_email'             => 'customer_emails',
		);

		$this->i18n = array(
			'count'            => esc_html__( '%s coupons' ),
			'count_inserted'   => esc_html__( '%s coupons inserted' ),
			'count_merged'     => esc_html__( '%s coupons merged' ),
			'count_skipped'    => esc_html__( '%s coupons skipped' ),
			'count_failed'     => esc_html__( '%s coupons failed' ),
		);

		add_filter( 'wc_csv_import_suite_woocommerce_coupon_csv_column_default_mapping', array( $this, 'column_default_mapping' ), 10, 2 );
	}


	/**
	 * Get CSV column mapping options
	 *
	 * @since 3.0.0
	 * @return array Associative array of column mapping options
	 */
	public function get_column_mapping_options() {

		return array(

			__( 'Coupon data', 'woocommerce-csv-import-suite' ) => array(
				'code'                       => __( 'Coupon code', 'woocommerce-csv-import-suite' ),
				'type'                       => __( 'Discount type', 'woocommerce-csv-import-suite' ),
				'amount'                     => __( 'Coupon amount', 'woocommerce-csv-import-suite' ),
				'individual_use'             => __( 'Individual use only', 'woocommerce-csv-import-suite' ),
				'products'                   => __( 'Products', 'woocommerce-csv-import-suite' ),
				'exclude_products'           => __( 'Exclude products', 'woocommerce-csv-import-suite' ),
				'usage_limit'                => __( 'Usage limit per coupon', 'woocommerce-csv-import-suite' ),
				'usage_limit_per_user'       => __( 'Usage limit per user', 'woocommerce-csv-import-suite' ),
				'limit_usage_to_x_items'     => __( 'Limit usage to x items', 'woocommerce-csv-import-suite' ),
				'usage_count'                => __( 'Usage count', 'woocommerce-csv-import-suite' ),
				'expiry_date'                => __( 'Coupon expiry date', 'woocommerce-csv-import-suite' ),
				'enable_free_shipping'       => __( 'Allow free shipping', 'woocommerce-csv-import-suite' ),
				'product_categories'         => __( 'Categories', 'woocommerce-csv-import-suite' ),
				'exclude_product_categories' => __( 'Exclude categories', 'woocommerce-csv-import-suite' ),
				'exclude_sale_items'         => __( 'Exclude sale items', 'woocommerce-csv-import-suite' ),
				'minimum_amount'             => __( 'Minimum spend', 'woocommerce-csv-import-suite' ),
				'maximum_amount'             => __( 'Maximum spend', 'woocommerce-csv-import-suite' ),
				'customer_emails'            => __( 'Email restrictions', 'woocommerce-csv-import-suite' ),
				'description'                => __( 'Description', 'woocommerce-csv-import-suite' ),
			),

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

			case 'coupon_code':          return 'code';
			case 'coupon_amount':        return 'amount';
			case 'discount_type':        return 'type';
			case 'free_shipping':        return 'enable_free_shipping';
			case 'exclude_categories':   return 'exclude_product_categories';

		}

		return $map_to;
	}


	/**
	 * Parse raw coupon data
	 *
	 * @since 3.0.0
	 * @param array $item Raw coupon data from CSV
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @throws \WC_CSV_Import_Suite_Import_Exception validation, parsing errors
	 * @return array|bool Parsed coupon data or false on failure
	 */
	protected function parse_item( $item, $options = array(), $raw_headers = array() ) {

		$coupon_code         = isset( $item['code'] ) ? $item['code'] : null;
		$merging             = $options['merge'];
		$insert_non_matching = isset( $options['insert_non_matching'] ) && $options['insert_non_matching'];

		/* translators: Placeholders: %s - row number */
		$preparing = $merging ? __( '> Row %s - preparing for merge.', 'woocommerce-csv-import-suite' ) : __( '> Row %s - preparing for import.', 'woocommerce-csv-import-suite' );
		wc_csv_import_suite()->log( '---' );
		wc_csv_import_suite()->log( sprintf( $preparing, $this->get_line_num() ) );

		// prepare coupon & postmeta for import
		$coupon = $postmeta = $terms = array();

		// cannot merge or insert without coupon code
		if ( ! $coupon_code ) {
			throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_coupon_code', __( 'Missing coupon code.', 'woocommerce-csv-import-suite' ) );
		}

		global $wpdb;

		// check for existing coupons
		$found_coupon = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_status = 'publish' AND post_title = %s", $coupon_code ) );

		// prepare for merging
		if ( $merging ) {

			// no coupon found
			if ( ! $found_coupon ) {

				if ( $insert_non_matching ) {
					wc_csv_import_suite()->log( sprintf( __( '> > Skipped. Cannot find coupon with code %s. Importing instead.', 'woocommerce-csv-import-suite' ), esc_html( $coupon_code ) ) );
					$merging = false;
				} else {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_find_coupon', sprintf( __( 'Cannot find coupon with code %s.', 'woocommerce-csv-import-suite' ), esc_html( $coupon_code ) ) );
				}

			} else {
				/* translators: Placeholders: %s - coupon code */
				wc_csv_import_suite()->log( sprintf( __( "> > Found coupon with code '%s'.", 'woocommerce-csv-import-suite' ), esc_html( $coupon_code ) ) );

				// record the coupon ID
				$coupon['id'] = $found_coupon;
			}

		}

		// prepare for importing
		if ( ! $merging ) {

			// coupon already exists
			if ( $found_coupon ) {
				/* translators: Placeholders: %s - coupon code */
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_coupon_code_already_exists', sprintf( __( "Coupon code '%s' already exists.", 'woocommerce-csv-import-suite' ), esc_html( $coupon_code ) ) );
			}

			// check required fields
			if ( ! isset( $item['type'] ) || ! $item['type'] ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_coupon_type', __( "Missing coupon discount type.", 'woocommerce-csv-import-suite' ) );
			}
		}

		// get the set of possible coupon discount types
		$discount_types = wc_get_coupon_types();

		// check for the discount type validity both by key and value (ie either 'fixed_cart' or 'Cart Discount'
		if ( isset( $item['type'] ) && $item['type'] ) {

			$discount_type_is_valid = false;

			foreach ( $discount_types as $key => $value ) {

				if ( 0 === strcasecmp( $key, $item['type'] ) || 0 === strcasecmp( $value, __( $item['type'], 'woocommerce-csv-import-suite' ) ) ) {

					$discount_type_is_valid = true;
					$coupon['type'] = $key;
					break;
				}
			}

			if ( ! $discount_type_is_valid ) {
				/* translators: Placeholders: %s - discount type name */
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_coupon_type', sprintf( __( "Unknown discount type '%s'.", 'woocommerce-csv-import-suite' ), esc_html( $item['type'] ) ) );
			}
		}

		// build the coupon data object
		$coupon['code']        = $item['code'];
		$coupon['description'] = isset( $item['description'] ) ? $item['description'] : '';

		// get any known coupon data fields
		foreach ( $this->coupon_data_fields as $column ) {

			switch ( $column ) {

				case 'products': // handle products: look up by sku
				case 'exclude_products':

					$val  = isset( $item[ $column ] ) ? $item[ $column ] : '';
					$skus = array_filter( array_map( 'trim', explode( ',', $val ) ) );
					$val  = array();

					foreach ( $skus as $sku ) {

						// find by sku
						$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

						// no product found
						if ( ! $product_id ) {
							/* translators: Placeholders: %s - product SKU */
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_product_sku', sprintf( __( 'Unknown product sku: %s.', 'woocommerce-csv-import-suite' ), esc_html( $sku ) ) );
						}

						$val[] = $product_id;
					}

					// map to standard column name
					$column = ( 'products' == $column ? 'product_ids' : 'exclude_product_ids' );
				break;

				case 'product_categories':
				case 'exclude_product_categories':

					$val          = isset( $item[ $column ] ) ? $item[ $column ] : '';
					$product_cats = array_filter( array_map( 'trim', explode( ',', $val ) ) );
					$val          = array();

					foreach ( $product_cats as $product_cat ) {

						// validate product category
						$term = term_exists( $product_cat, 'product_cat' );

						// unknown category
						if ( ! $term ) {
							/* translators: Placeholders: %s - product category name */
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_unknown_product_category', sprintf( __( 'Unknown product category: %s.', 'woocommerce-csv-import-suite' ), esc_html( $product_cat ) ) );
						}

						$val[] = $term['term_id'];
					}

					// map to standard column name
					$column = ( 'product_categories' == $column ? 'product_category_ids' : 'exclude_product_category_ids' );
				break;

				case 'customer_emails':

					$val    = isset( $item[ $column ] ) ? $item[ $column ] : '';
					$emails = array_filter( array_map( 'trim', explode( ',', $val ) ) );
					$val    = array();

					foreach ( $emails as $email ) {

						// invalid email
						if ( ! is_email( $email ) ) {
							/* translators: Placeholders: %s - email address */
							throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_email', sprintf( __( 'Invalid email: %s.', 'woocommerce-csv-import-suite' ), esc_html( $email ) ) );
						}

						$val[] = $email;
					}
				break;

				case 'enable_free_shipping': // handle booleans, defaulting to 'no' on import (not merge)
				case 'individual_use':
				case 'exclude_sale_items':

					$val = isset( $item[ $column ] ) && $item[ $column ] ? strtolower( $item[ $column ] ) : ( $merging ? '' : 'no' );

					if ( $val && 'yes' != $val && 'no' != $val ) {
						/* translators: Placeholders: %s - column name */
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_value', sprintf( __( "Column '%s' must be 'yes' or 'no'.", 'woocommerce-csv-import-suite' ), esc_html( $column ) ) );
					}

					// transform into true boolean, so that the format matches with
					// WC_API_Coupons
					$val = 'yes' === $val;
				break;

				case 'expiry_date':

					$val = isset( $item[ $column ] ) ? $item[ $column ] : '';

					// invalid date format
					if ( $val && false === strtotime( $val ) ) {
						/* translators: Placeholders: %s - a date in invalid format */
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_date_format', sprintf( __( "Invalid date format '%s'", 'woocommerce-csv-import-suite' ), esc_html( $item[ $column ] ) ) );
					}
				break;

				case 'usage_limit': // handle integers
				case 'usage_count':
				case 'usage_limit_per_user':
				case 'limit_usage_to_x_items':

					$val = isset( $item[ $column ] ) ? $item[ $column ] : '';

					// invalid integer value
					if ( ! empty( $val ) && ! is_numeric( $val ) ) {
						/* translators: Placeholders: %1$s - column title, %2$s - column value */
						throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_value', sprintf( __( 'Invalid %1$s \'%2$s\'.', 'woocommerce-csv-import-suite' ), esc_html( $column ), esc_html( $val ) ) );
					}
				break;

				default:
					$val = isset( $item[ $column ] ) ? $item[ $column ] : '';
			}

			// only use non-empty values (zeroes are fine, but empty strings are not)
			if ( is_numeric( $val ) || ! empty( $val ) ) {
				$coupon[ $column ] = $val;
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
				if ( in_array( $meta_key, $this->coupon_meta_fields ) ) {
					continue;
				}

				// add to postmeta array
				$postmeta[ $meta_key ] = $value;
			}

			// handle tax: columns - import as taxonomy terms
			elseif ( Framework\SV_WC_Helper::str_starts_with( $key, 'tax:' ) ) {

				$results = $this->parse_taxonomy_terms( $key, $value );

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

		$coupon['coupon_meta'] = $postmeta;
		$coupon['terms']       = $terms;

		/**
		 * Filter parsed coupon data
		 *
		 * Gives a chance for 3rd parties to parse data from custom columns
		 *
		 * @since 3.0.0
		 * @param array $coupon Parsed coupon data
		 * @param array $data Raw coupon data from CSV
		 * @param array $options Import options
		 * @param array $raw_headers Raw CSV headers
		 */
		return apply_filters( 'wc_csv_import_suite_parsed_coupon_data', $coupon, $item, $options, $raw_headers );
	}


	/**
	 * Process a coupon
	 *
	 * @since 3.0.0
	 * @param mixed $data Parsed coupon data
	 * @param array $options Optional. Options
	 * @param array $raw_headers Optional. Raw headers
	 * @return int|null
	 */
	protected function process_item( $data, $options = array(), $raw_headers = array() ) {

		$merging = $options['merge'] && isset( $data['id'] ) && $data['id'];
		$dry_run = isset( $options['dry_run'] ) && $options['dry_run'];

		wc_csv_import_suite()->log( __( 'Processing coupon.', 'woocommerce-csv-import-suite' ) );

		$coupon_id = null;

		// merging
		if ( $merging ) {

			wc_csv_import_suite()->log( sprintf( __( "> Merging coupon '%s'.", 'woocommerce-csv-import-suite' ), esc_html( $data['code'] ) ) );

			if ( ! $dry_run ) {
				$coupon_id = $this->update_coupon( $data['id'], $data, $options );
			}
		}

		// importing
		else {

			// insert coupon
			wc_csv_import_suite()->log( sprintf( __( "> Inserting coupon '%s'", 'woocommerce-csv-import-suite' ), esc_html( $data['code'] ) ) );

			if ( ! $dry_run ) {
				$coupon_id = $this->create_coupon( $data, $options );
			}
		}

		// import failed
		if ( ! $dry_run && is_wp_error( $coupon_id ) ) {
			$this->add_import_result( 'failed', $coupon_id->get_error_message() );
			return null;
		}

		// TODO: is that OK to log and return as coupon_id in case of dry run?
		if ( $dry_run ) {
			$coupon_id = $merging ? $data['id'] : 9999;
		}

		$result  = $merging ? 'merged' : 'inserted';
		$message = $merging
						 ? __( '> Finished merging coupon %s.',   'woocommerce-csv-import-suite' )
						 : __( '> Finished importing coupon %s.', 'woocommerce-csv-import-suite' );

		wc_csv_import_suite()->log( sprintf( $message, esc_html( $data['code'] ) ) );

		$this->add_import_result( $result );

		return $coupon_id;
	}


	/**
	 * Create a coupon
	 *
	 * @since 3.0.0
	 * @param array $data
	 * @param array $options
	 * @return array|WP_Error
	 */
	public function create_coupon( $data, $options = array() ) {
		global $wpdb;

		try {

			/**
			 * Filter new coupon data from CSV
			 *
			 * @since 3.0.0
			 * @param array $data
			 * @param array $options
			 * @param object $this
			 */
			$data = apply_filters( 'wc_csv_import_suite_create_coupon_data', $data, $options, $this );

			// check if coupon code is specified
			if ( ! isset( $data['code'] ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_missing_coupon_code', sprintf( __( 'Missing parameter %s', 'woocommerce-csv-import-suite' ), 'code' ) );
			}

			$coupon_code = apply_filters( 'woocommerce_coupon_code', $data['code'] );

			// check for duplicate coupon codes
			$coupon_found = $wpdb->get_var( $wpdb->prepare( "
				SELECT $wpdb->posts.ID
				FROM $wpdb->posts
				WHERE $wpdb->posts.post_type = 'shop_coupon'
				AND $wpdb->posts.post_status = 'publish'
				AND $wpdb->posts.post_title = '%s'
			", $coupon_code ) );

			if ( $coupon_found ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_coupon_code_already_exists', __( 'The coupon code already exists', 'woocommerce-csv-import-suite' ) );
			}

			$defaults = array(
				'type'                         => 'fixed_cart',
				'amount'                       => 0,
				'individual_use'               => false,
				'product_ids'                  => array(),
				'exclude_product_ids'          => array(),
				'usage_limit'                  => '',
				'usage_limit_per_user'         => '',
				'limit_usage_to_x_items'       => '',
				'usage_count'                  => '',
				'expiry_date'                  => '', // TODO: replace with date_expires (https://github.com/woocommerce/woocommerce/commit/9e724d44cc34469d84b487d69b8944ed3b5de924) {WV 2020-02-17}
				'enable_free_shipping'         => false,
				'product_category_ids'         => array(),
				'exclude_product_category_ids' => array(),
				'exclude_sale_items'           => false,
				'minimum_amount'               => '',
				'maximum_amount'               => '',
				'customer_emails'              => array(),
				'description'                  => ''
			);

			$coupon_data = wp_parse_args( $data, $defaults );

			// validate coupon types
			if ( ! in_array( wc_clean( $coupon_data['type'] ), array_keys( wc_get_coupon_types() ) ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_coupon_type', sprintf( __( 'Invalid coupon type - the coupon type must be any of these: %s', 'woocommerce-csv-import-suite' ), implode( ', ', array_keys( wc_get_coupon_types() ) ) ) );
			}

			$new_coupon = array(
				'post_title'   => $coupon_code,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'post_type'    => 'shop_coupon',
				'post_excerpt' => $coupon_data['description']
	 		);

			$id = wp_insert_post( $new_coupon, $wp_error = true );

			if ( is_wp_error( $id ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_cannot_create_coupon', $id->get_error_message(), 400 );
			}

			// set coupon data (meta, terms)
			$this->update_coupon_data( $id, $coupon_data, $options );

			/**
			 * Triggered after a coupon has been created via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Coupon ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_create_coupon', $id, $coupon_data, $options );

			return $id;

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Update a coupon
	 *
	 * @since 3.0.0
	 * @param int $id the coupon ID
	 * @param array $data
	 * @param array $options
	 * @return array|WP_Error
	 */
	public function update_coupon( $id, $data, $options = array() ) {

		try {

			$id = absint( $id );

			// validate the coupon ID.
			if ( empty( $id ) ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_coupon_id', __( 'Invalid coupon ID', 'woocommerce-csv-import-suite' ) );
			}

			$post = get_post( $id );

			if ( null === $post ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_no_coupon_found', sprintf( __( 'No coupon found with the ID equal to %s', 'woocommerce-csv-import-suite' ), $id ) );
			}

			// validate post type
			if ( 'shop_coupon' !== $post->post_type ) {
				throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_coupon', __( 'Invalid coupon', 'woocommerce-csv-import-suite' ) );
			}

			/**
			 * Filter data that is going to be updated for a coupon via CSV
			 *
			 * @since 3.0.0
			 * @param array $data
			 * @param array $options
			 * @param object $this
			 */
			$data = apply_filters( 'wc_csv_import_suite_update_coupon_data', $data, $options, $this );

			$coupon_data = array();

			if ( isset( $data['code'] ) ) {
				global $wpdb;

				$coupon_code = apply_filters( 'woocommerce_coupon_code', $data['code'] );

				// check for duplicate coupon codes
				$coupon_found = $wpdb->get_var( $wpdb->prepare( "
					SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					WHERE $wpdb->posts.post_type = 'shop_coupon'
					AND $wpdb->posts.post_status = 'publish'
					AND $wpdb->posts.post_title = '%s'
					AND $wpdb->posts.ID != %s
				 ", $coupon_code, $id ) );

				if ( $coupon_found ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_coupon_code_already_exists', __( 'The coupon code already exists', 'woocommerce-csv-import-suite' ) );
				}

				$coupon_data['post_title'] = $coupon_code;
			}

			if ( isset( $data['description'] ) ) {
				$coupon_data['post_excerpt'] = $data['description'];
			}

			if ( ! empty( $coupon_data ) ) {

				$coupon_data['ID'] = intval( $id );
				$updated = wp_update_post( array( 'ID' => intval( $id ), 'post_title' => $coupon_code ), $wp_error = true );

				if ( is_wp_error( $updated ) ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( $updated->get_error_code(), $updated->get_error_message() );
				}
			}

			// validate coupon types
			if ( isset( $data['type'] ) ) {
				if ( ! in_array( wc_clean( $data['type'] ), array_keys( wc_get_coupon_types() ) ) ) {
					throw new \WC_CSV_Import_Suite_Import_Exception( 'wc_csv_import_suite_invalid_coupon_type', sprintf( __( 'Invalid coupon type - the coupon type must be any of these: %s', 'woocommerce-csv-import-suite' ), implode( ', ', array_keys( wc_get_coupon_types() ) ) ) );
				}
			}

			// update coupon data
			$this->update_coupon_data( $id, $data, $options );

			/**
			 * Triggered after a coupon has been updated via CSV import
			 *
			 * @since 3.0.0
			 * @param int $id Coupon ID
			 * @param array $data Data from CSV
			 * @param array $options Import options
			 */
			do_action( 'wc_csv_import_suite_update_coupon', $id, $data );

			return $id;

		} catch ( \WC_CSV_Import_Suite_Import_Exception $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}
	}


	/**
	 * Add/Update coupon data.
	 *
	 * @since 3.0.0
	 * @param int $id the coupon ID
	 * @param array $data
	 * @param array $options
	 */
	protected function update_coupon_data( $id, $data, $options ) {

		// update known coupon meta fields. A loop-based approach based on
		// WC_API_Coupons::create_coupon/update_coupon
		foreach ( $this->coupon_meta_fields as $meta_key ) {

			$data_field = $meta_key;

			if ( isset( $this->coupon_meta_data_mapping[ $meta_key ] ) ) {
				$data_field = $this->coupon_meta_data_mapping[ $meta_key ];
			}

			if ( isset( $data[ $data_field ] ) ) {

				$val = $data[ $data_field ];

				// transform data fields to meta fields and sanitize one last time
				// before DB insert
				switch ( $meta_key ) {

					// numeric values
					case 'coupon_amount':
					case 'minimum_amount':
					case 'maximum_amount':
						$val = wc_format_decimal( $val );
					break;

					// booleans
					case 'individual_use':
					case 'free_shipping':
					case 'exclude_sale_items':
						$val = true === $val ? 'yes' : 'no';
					break;

					// id lists
					case 'product_ids':
					case 'exclude_product_ids':
						$val = ! empty( $val ) ? implode( ',', array_filter( array_map( 'intval', $val ) ) ) : '';
					break;

					// category lists because WC treats this select differently
					// see `/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php`
					case 'product_categories':
					case 'exclude_product_categories':
						$val = ! empty( $val ) ? $val : array();
					break;

						// integer values
					case 'usage_limit':
					case 'usage_limit_per_user':
					case 'limit_usage_to_x_items':
					case 'usage_count':
						$val = absint( $val );
					break;

					case 'expiry_date':
						$val = $this->get_coupon_expiry_date( wc_clean( $val ) );
					break;

					case 'customer_email':
						$val = array_filter( array_map( 'sanitize_email', $val ) );
					break;
				}

				update_post_meta( $id, $meta_key, $val );
			}
		}


		// add/update coupon meta
		if ( ! empty( $data['coupon_meta'] ) ) {

			foreach ( $data['coupon_meta'] as $meta_key => $meta_value ) {

				update_post_meta( $id, $meta_key, maybe_unserialize( $meta_value ) );
			}
		}

		$this->process_terms( $id, $data['terms'] );

		/**
		 * Triggered after coupon data has been updated via CSV
		 *
		 * This will be triggered for both new and updated coupons
		 *
		 * @since 3.1.0
		 * @param int $id coupon ID
		 * @param array $data Coupon data
		 * @param array $options Import options
		 */
		do_action( 'wc_csv_import_suite_save_coupon_data', $id, $data, $options );
	}


	/**
	 * Get formatted coupon expiry date
	 *
	 * @since 3.0.0
	 * @param string $expiry_date
	 * @return string
	 */
	protected function get_coupon_expiry_date( $expiry_date ) {

		if ( '' != $expiry_date ) {
			return date( 'Y-m-d', strtotime( $expiry_date ) );
		}

		return '';
	}


}
