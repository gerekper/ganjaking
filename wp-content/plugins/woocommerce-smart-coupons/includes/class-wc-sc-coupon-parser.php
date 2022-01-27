<?php
/**
 * Coupon parser during import & export
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.1.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupon_Parser' ) ) {

	/**
	 * Class to parse values for WC_Coupon for importing
	 */
	class WC_SC_Coupon_Parser {

		/**
		 * The post type
		 *
		 * @var string $post_type
		 */
		public $post_type;

		/**
		 * Reserved fields
		 *
		 * @var array $reserved_fields
		 */
		public $reserved_fields;       // Fields we map/handle (not custom fields).

		/**
		 * POst defaults
		 *
		 * @var array $post_defaults
		 */
		public $post_defaults;         // Default post data.

		/**
		 * Postmeta defaults
		 *
		 * @var array $postmeta_defaults
		 */
		public $postmeta_defaults;     // default post meta.

		/**
		 * Term defaults
		 *
		 * @var array $term_defaults
		 */
		public $term_defaults;     // default term data.

		/**
		 * Row number
		 *
		 * @var int $row
		 */
		public $row;

		/**
		 * Constructor
		 *
		 * @param string $post_type The post type.
		 */
		public function __construct( $post_type = 'shop_coupon' ) {

			$this->post_type = $post_type;

			$this->reserved_fields = array(
				'id',
				'post_id',
				'post_type',
				'menu_order',
				'postmeta',
				'post_status',
				'post_title',
				'post_name',
				'comment_status',
				'post_date',
				'post_date_gmt',
				'post_content',
				'post_excerpt',
				'post_parent',
				'post_password',
				'discount_type',
				'coupon_amount',
				'free_shipping',
				'expiry_date',
				'minimum_amount',
				'maximum_amount',
				'individual_use',
				'exclude_sale_items',
				'product_ids',
				'exclude_product_ids',
				'product_categories',
				'exclude_product_categories',
				'customer_email',
				'usage_limit',
				'usage_limit_per_user',
				'limit_usage_to_x_items',
				'usage_count',
				'_used_by',
			);

			$this->post_defaults = array(
				'post_type'      => $this->post_type,
				'menu_order'     => '',
				'postmeta'       => array(),
				'post_status'    => 'publish',
				'post_title'     => '',
				'post_name'      => '',
				'comment_status' => 'closed',
				'post_date'      => '',
				'post_date_gmt'  => '',
				'post_content'   => '',
				'post_excerpt'   => '',
				'post_parent'    => 0,
				'post_password'  => '',
				'post_author'    => get_current_user_id(),
			);

			$this->postmeta_defaults = apply_filters(
				'smart_coupons_parser_postmeta_defaults',
				array(
					'discount_type'                => 'fixed_cart',
					'coupon_amount'                => '',
					'free_shipping'                => '',
					'expiry_date'                  => '',
					'sc_coupon_validity'           => '',
					'validity_suffix'              => '',
					'auto_generate_coupon'         => '',
					'coupon_title_prefix'          => '',
					'coupon_title_suffix'          => '',
					'is_pick_price_of_product'     => '',
					'minimum_amount'               => '',
					'maximum_amount'               => '',
					'individual_use'               => '',
					'exclude_sale_items'           => '',
					'product_ids'                  => '',
					'exclude_product_ids'          => '',
					'product_categories'           => '',
					'exclude_product_categories'   => '',
					'customer_email'               => '',
					'sc_disable_email_restriction' => '',
					'usage_limit'                  => '',
					'usage_limit_per_user'         => '',
					'limit_usage_to_x_items'       => '',
					'sc_is_visible_storewide'      => '',
					'usage_count'                  => '',
					'_used_by'                     => '',
					'sc_restrict_to_new_user'      => '',
					'wc_sc_max_discount'           => '',
				)
			);

			$this->term_defaults = array(
				'sc_coupon_category' => '',
			);
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Format data passed from CSV
		 *
		 * @param array  $data The data to format.
		 * @param string $enc encoding The encoding.
		 */
		public function format_data_from_csv( $data, $enc ) {
			return ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );
		}

		/**
		 * Parse data
		 *
		 * @param string $file Imported file.
		 * @return array parsed data with headers
		 */
		public function parse_data( $file ) {

			// Set locale.
			$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
			if ( $enc ) {
				setlocale( LC_ALL, 'en_US.' . $enc );
			}
			ini_set( 'auto_detect_line_endings', true ); // phpcs:ignore

			$parsed_data = array();

			$handle = fopen( $file, 'r' ); // phpcs:ignore

			// Put all CSV data into an associative array.
			if ( false !== $handle ) {

				$header = fgetcsv( $handle, 0 );

				while ( false !== ( $postmeta = fgetcsv( $handle, 0 ) ) ) { // phpcs:ignore
					$row = array();
					foreach ( $header as $key => $heading ) {

						$s_heading = strtolower( $heading );

						$row[ $s_heading ] = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( stripslashes( $postmeta[ $key ] ), $enc ) : '';

						$raw_headers[ $s_heading ] = $heading;
					}

					$parsed_data[] = $row;

					unset( $postmeta, $row );

				}

				fclose( $handle ); // phpcs:ignore
			}

			return array( $parsed_data, $raw_headers );
		}

		/**
		 * Parse data one row at a time
		 *
		 * @param  boolean $file_handler        CSV file handler.
		 * @param  array   $header        CSV header meta column name.
		 * @param  integer $file_position file pointer posistion to read from.
		 * @param  string  $encoding      Character encoding.
		 * @return array  $result parsed data with current file pointer position
		 */
		public function parse_data_by_row( $file_handler = false, $header = array(), $file_position = 0, $encoding = '' ) {

			$parsed_csv_data = array();

			$reading_completed = false;

			if ( false !== $file_handler ) {

				if ( $file_position > 0 ) {

					fseek( $file_handler, (int) $file_position );

				}

				if ( false !== ( $postmeta = fgetcsv( $file_handler, 0 ) ) ) { // phpcs:ignore
					$row = array();
					foreach ( $header as $key => $heading ) {

						$s_heading = strtolower( $heading );

						// Put all CSV data into an associative array by row.
						$row[ $s_heading ] = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( stripslashes( $postmeta[ $key ] ), $encoding ) : '';
					}

					$parsed_csv_data = $row;

					unset( $postmeta, $row );

				} else {

					$reading_completed = true;

				}

				$file_position = ftell( $file_handler );
			}

			$result = array(
				'parsed_csv_data'   => $parsed_csv_data,
				'file_position'     => $file_position,
				'reading_completed' => $reading_completed,
			);

			return $result;
		}

		/**
		 * Parse coupon
		 *
		 * @param array $item The imported item.
		 * @return array $coupon
		 */
		public function parse_coupon( $item ) {
			global $wc_csv_coupon_import, $wpdb;

			$this->row++;
			$postmeta  = array();
			$term_data = array();
			$coupon    = array();

			$post_id = ( ! empty( $item['id'] ) ) ? absint( $item['id'] ) : 0;
			$post_id = ( ! empty( $item['post_id'] ) ) ? absint( $item['post_id'] ) : $post_id;

			$product['post_id'] = $post_id;

			// Get post fields.
			foreach ( $this->post_defaults as $column => $default ) {
				if ( isset( $item[ $column ] ) ) {
					$product[ $column ] = $item[ $column ];
				}
			}

			// Get custom fields.
			foreach ( $this->postmeta_defaults as $column => $default ) {
				if ( isset( $item[ $column ] ) ) {
					$postmeta[ $column ] = (string) $item[ $column ];
				} elseif ( isset( $item[ '_' . $column ] ) ) {
					$postmeta[ $column ] = (string) $item[ '_' . $column ];
				}
			}

			// Get term fields.
			foreach ( $this->term_defaults as $column => $default ) {
				if ( isset( $item[ $column ] ) ) {
					$term_data[ $column ] = $item[ $column ];
				}
			}

			// Merge post meta with defaults.
			$coupon    = wp_parse_args( $product, $this->post_defaults );
			$postmeta  = wp_parse_args( $postmeta, $this->postmeta_defaults );
			$term_data = wp_parse_args( $term_data, $this->term_defaults );

			if ( ! empty( $postmeta['discount_type'] ) ) {
				$discount_type = $postmeta['discount_type'];
			} else {
				if ( $this->is_wc_gte_30() ) {
					$discount_type = 'Percentage discount';
				} else {
					$discount_type = 'Cart % Discount';
				}
			}

			$all_discount_types = wc_get_coupon_types();

			// discount types.
			if ( ! empty( $discount_type ) ) {

				if ( in_array( $discount_type, $all_discount_types, true ) ) {
					$postmeta['discount_type'] = array_search( $discount_type, $all_discount_types, true );
				}

				if ( empty( $postmeta['discount_type'] ) ) {
					$postmeta['discount_type'] = 'percent';
				}
			}

			// product_ids.
			if ( isset( $postmeta['product_ids'] ) && ! is_array( $postmeta['product_ids'] ) ) {
					$ids                     = array_filter( array_map( 'trim', explode( '|', $postmeta['product_ids'] ) ) );
					$ids                     = implode( ',', $ids );
					$postmeta['product_ids'] = $ids;
			}

			// exclude_product_ids.
			if ( isset( $postmeta['exclude_product_ids'] ) && ! is_array( $postmeta['exclude_product_ids'] ) ) {
					$ids                             = array_filter( array_map( 'trim', explode( '|', $postmeta['exclude_product_ids'] ) ) );
					$ids                             = implode( ',', $ids );
					$postmeta['exclude_product_ids'] = $ids;
			}

			// product_categories.
			if ( isset( $postmeta['product_categories'] ) && ! is_array( $postmeta['product_categories'] ) ) {
					$ids                            = array_filter( array_map( 'trim', explode( '|', $postmeta['product_categories'] ) ) );
					$postmeta['product_categories'] = $ids;
			}

			// exclude_product_categories.
			if ( isset( $postmeta['exclude_product_categories'] ) && ! is_array( $postmeta['exclude_product_categories'] ) ) {
					$ids                                    = array_filter( array_map( 'trim', explode( '|', $postmeta['exclude_product_categories'] ) ) );
					$postmeta['exclude_product_categories'] = $ids;
			}

			// customer_email.
			if ( isset( $postmeta['customer_email'] ) && ! is_array( $postmeta['customer_email'] ) ) {
					$email_ids                  = array_filter( array_map( 'trim', explode( ',', $postmeta['customer_email'] ) ) );
					$postmeta['customer_email'] = $email_ids;
			}

			// expiry date.
			if ( isset( $postmeta['expiry_date'] ) ) {
				$timestamp_expiry_date = ( ! empty( $postmeta['expiry_date'] ) ) ? strtotime( $postmeta['expiry_date'] ) : '';
				if ( ! empty( $postmeta['expiry_date'] ) && empty( $timestamp_expiry_date ) ) {
					/* translators: 1. Coupon code 2. Expiry date */
					$this->log( 'error', sprintf( __( 'Incorrect format for expiry date of coupon "%1$s". Entered date is %2$s. Expected date format: YYYY-MM-DD', 'woocommerce-smart-coupons' ), $coupon['post_title'], $postmeta['expiry_date'] ) );
				}
				$postmeta['expiry_date'] = ( ! empty( $timestamp_expiry_date ) ) ? gmdate( 'Y-m-d', $timestamp_expiry_date ) : '';
			}

			// usage count.
			if ( isset( $postmeta['usage_count'] ) ) {
				$postmeta['usage_count'] = ( ! empty( $postmeta['usage_count'] ) ) ? $postmeta['usage_count'] : 0;
			}

			// used_by.
			if ( isset( $postmeta['_used_by'] ) ) {
				$postmeta['_used_by'] = ( ! empty( $postmeta['_used_by'] ) ) ? $postmeta['_used_by'] : '';
			}

			// Put set core product postmeta into product array.
			foreach ( $postmeta as $key => $value ) {
				$coupon['postmeta'][] = array(
					'key'   => esc_attr( $key ),
					'value' => $value,
				);
			}

			// term data.
			foreach ( $term_data as $key => $value ) {
				$coupon['term_data'][] = array(
					'key'   => esc_attr( $key ),
					'value' => $value,
				);
			}

			unset( $item, $postmeta );

			return $coupon;

		}
	}

}
