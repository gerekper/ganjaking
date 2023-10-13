<?php
/**
 * Class YITH_WCBK_Booking_Externals
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Externals' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Externals
	 * handle externals in DB
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Booking_Externals {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Db table name
		 *
		 * @var string
		 */
		private $table_name = '';

		/**
		 * Are all externals loaded?
		 *
		 * @var bool
		 */
		private $all_externals_loaded = false;

		/**
		 * YITH_WCBK_Booking_Externals constructor.
		 */
		private function __construct() {
			global $wpdb;
			$this->table_name = $wpdb->prefix . YITH_WCBK_DB::EXTERNAL_BOOKINGS_TABLE;
		}

		/**
		 * Add an external into DB
		 *
		 * @param YITH_WCBK_Booking_External $external The external object.
		 * @param bool                       $future   Future flag.
		 *
		 * @return false|int
		 */
		public function add_external( $external, $future = true ) {
			global $wpdb;

			if ( $future && $external->is_completed() ) {
				return 1;
			}

			$data = array(
				'product_id'    => $external->get_product_id(),
				'from'          => $external->get_from(),
				'to'            => $external->get_to(),
				'description'   => $external->get_description(),
				'summary'       => $external->get_summary(),
				'location'      => $external->get_location(),
				'uid'           => $external->get_uid(),
				'calendar_name' => $external->get_calendar_name(),
				'source'        => $external->get_source(),
				'date'          => current_time( 'mysql', true ),
			);

			$types = array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%d',
			);

			return $wpdb->insert( $this->table_name, $data, $types );
		}

		/**
		 * Add externals in DB
		 *
		 * @param YITH_WCBK_Booking_External[] $externals Externals to add.
		 * @param bool                         $future    Future flag.
		 */
		public function add_externals( $externals, $future = true ) {
			foreach ( $externals as $external ) {
				$this->add_external( $external, $future );
			}
		}


		/**
		 * Return an SQL where query from array
		 *
		 * @param array $_where The where clause array.
		 *
		 * @return string
		 */
		public static function get_sql_where( $_where = array() ) {
			global $wpdb;

			$where = '';
			if ( $_where ) {
				$relation            = isset( $_where['relation'] ) && in_array( $_where['relation'], array( 'AND', 'OR' ), true ) ? $_where['relation'] : 'AND';
				$where_defaults      = array(
					'key'     => '',
					'value'   => '',
					'compare' => '=',
					'type'    => 'CHAR',
				);
				$allowed_where_types = array( 'CHAR', 'NUMBER' );
				$where_list          = array();
				foreach ( $_where as $current_where ) {
					if ( ! is_array( $current_where ) || empty( $current_where['key'] ) ) {
						continue;
					}

					$current_where = wp_parse_args( $current_where, $where_defaults );

					$key     = $current_where['key'];
					$value   = $current_where['value'];
					$compare = $current_where['compare'];
					$type    = $current_where['type'];
					$type    = array_key_exists( $type, $allowed_where_types ) ? $type : 'CHAR';
					if ( 'CHAR' === $type ) {
						$where_list[] = "externals.{$key} {$compare} " . $wpdb->prepare( '%s', $value );
					} else {
						$where_list[] = "externals.{$key} {$compare} " . $wpdb->prepare( '%d', $value );
					}
				}

				if ( $where_list ) {
					$where = 'WHERE ' . implode( " {$relation} ", $where_list );
				}
			}

			return $where;
		}

		/**
		 * Retrieve an array of externals stored in externals table.
		 *
		 * @param array $args Arguments.
		 *
		 * @return YITH_WCBK_Booking_External[]
		 */
		public function get_externals( $args = array() ) {
			global $wpdb;

			$query = "SELECT * FROM {$this->table_name} as externals";

			if ( isset( $args['where'] ) ) {
				$query .= ' ' . self::get_sql_where( $args['where'] );
			}

			$results = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $results && is_array( $results ) ) {
				$results = array_map( 'yith_wcbk_booking_external', $results );
			} else {
				$results = array();
			}

			return $results;
		}

		/**
		 * Count externals stored in externals table
		 *
		 * @param array $args Arguments.
		 *
		 * @return int
		 */
		public function count_externals( $args = array() ) {
			global $wpdb;

			$query = "SELECT COUNT(*) as count FROM {$this->table_name} as externals";

			if ( isset( $args['where'] ) ) {
				$query .= ' ' . self::get_sql_where( $args['where'] );
			}

			$count = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			return $count;
		}


		/**
		 * Get externals related to a specific product id
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return YITH_WCBK_Booking_External[]
		 */
		public function get_externals_from_product_id( $product_id ) {
			$results    = array();
			$product_id = absint( $product_id );
			if ( $product_id ) {
				$results = $this->get_externals(
					array(
						array(
							'key'   => 'product_id',
							'value' => $product_id,
						),
					)
				);
			}

			return $results;
		}

		/**
		 * Count externals in period
		 *
		 * @param int $from       From timestamp.
		 * @param int $to         To timestamp.
		 * @param int $product_id Product ID.
		 *
		 * @return int
		 */
		public function count_externals_in_period( $from, $to, $product_id = 0 ) {
			$where = array(
				array(
					'key'     => 'from',
					'value'   => $to,
					'compare' => '<',
				),
				array(
					'key'     => 'to',
					'value'   => $from,
					'compare' => '>',
				),
			);

			$product_id = absint( $product_id );

			if ( $product_id ) {
				$where[] = array(
					'key'   => 'product_id',
					'value' => $product_id,
				);
			}

			return absint( $this->count_externals( array( 'where' => $where ) ) );
		}

		/**
		 * Get externals in period.
		 *
		 * @param int $from       From timestamp.
		 * @param int $to         To timestamp.
		 * @param int $product_id Product ID.
		 *
		 * @return array
		 */
		public function get_externals_in_period( $from, $to, $product_id = 0 ) {
			$where      = array(
				array(
					'key'     => 'from',
					'value'   => $to,
					'compare' => '<',
				),
				array(
					'key'     => 'to',
					'value'   => $from,
					'compare' => '>',
				),
			);
			$product_id = absint( $product_id );

			if ( $product_id ) {
				$where[] = array(
					'key'   => 'product_id',
					'value' => $product_id,
				);
			}

			return $this->get_externals( array( 'where' => $where ) );
		}

		/**
		 * Return product ids of products with externals to sync
		 * searches for products with externals expired
		 *
		 * @return WC_Product_Booking[]
		 */
		public function get_products_with_externals_to_sync() {
			$expiring_time = get_option( 'yith-wcbk-external-calendars-sync-expiration', 6 * HOUR_IN_SECONDS );
			$now           = time();

			$args = array(
				'posts_per_page' => - 1,
				'post_type'      => 'product',
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_yith_booking_external_calendars',
						'value'   => '',
						'compare' => '!=',
					),
					array(
						'relation' => 'OR',
						array(
							'key'     => '_yith_booking_external_calendars_loaded',
							'value'   => '',
							'compare' => '=',
						),
						array(
							'key'     => '_yith_booking_external_calendars_loaded',
							'value'   => $now - $expiring_time,
							'compare' => '<',
						),
					),
				),
				'tax_query'      => array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
				),
			);

			$ids = get_posts( $args );
			$ids = ! ! $ids ? $ids : array();

			return array_filter( array_map( 'wc_get_product', $ids ) );
		}

		/**
		 * Load all externals and store values in DB table
		 */
		public function maybe_load_all_externals() {
			if ( ! $this->all_externals_loaded ) {
				$products = $this->get_products_with_externals_to_sync();
				foreach ( $products as $product ) {
					$product->maybe_load_externals();
				}

				$this->all_externals_loaded = true;
			}
		}


		/**
		 * Delete externals for product.
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return false|int
		 */
		public function delete_externals_from_product_id( $product_id ) {
			global $wpdb;

			$product_id = absint( $product_id );

			return $wpdb->delete( $this->table_name, array( 'product_id' => $product_id ), array( '%d' ) );
		}

		/**
		 * Return an array of sync expiration times.
		 *
		 * @return array
		 */
		public static function get_sync_expiration_times() {
			$options = array(
				30 * MINUTE_IN_SECONDS => __( '30 minutes', 'yith-booking-for-woocommerce' ),
				HOUR_IN_SECONDS        => __( '1 hour', 'yith-booking-for-woocommerce' ),
				2 * HOUR_IN_SECONDS    => __( '2 hours', 'yith-booking-for-woocommerce' ),
				6 * HOUR_IN_SECONDS    => __( '6 hours', 'yith-booking-for-woocommerce' ),
				12 * HOUR_IN_SECONDS   => __( '12 hours', 'yith-booking-for-woocommerce' ),
				DAY_IN_SECONDS         => __( '1 day', 'yith-booking-for-woocommerce' ),
				2 * DAY_IN_SECONDS     => __( '2 days', 'yith-booking-for-woocommerce' ),
				7 * DAY_IN_SECONDS     => __( '7 days', 'yith-booking-for-woocommerce' ),
				MONTH_IN_SECONDS       => __( '1 month', 'yith-booking-for-woocommerce' ),
			);

			return apply_filters( 'yith_wcbk_externals_get_sync_expiration_times', $options );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_booking_externals' ) ) {
	/**
	 * Unique access to instance of YITH_WCBK_Booking_Externals class
	 *
	 * @return YITH_WCBK_Booking_Externals
	 * @since 2.0.0
	 */
	function yith_wcbk_booking_externals(): YITH_WCBK_Booking_Externals {
		return YITH_WCBK_Booking_Externals::get_instance();
	}
}
