<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Product_Frontend' ) ) {

	class YITH_Delivery_Date_Product_Frontend {

		/**
		 * @var YITH_Delivery_Date_Product_Frontend unique instance
		 */
		protected static $_instance;

		public function __construct() {

			$position = get_option( 'ywcdd_ddm_where_show_delivery_message', 15 );

			if ( ! is_admin() ) {
				if ( $position > 0 ) {
					add_action( 'woocommerce_single_product_summary', array( $this, 'show_date_info' ), $position );
				}

				add_filter( 'woocommerce_available_variation', array( $this, 'add_variation_data' ), 15, 3 );
				add_action( 'wp_enqueue_scripts', array( $this, 'include_scripts' ), 20 );
				add_filter( 'yith_delivery_date_show_date_info', array(
					$this,
					'check_for_custom_product_type'
				), 10, 2 );
			}
		}

		/**
		 * @return YITH_Delivery_Date_Product_Frontend
		 * @since 2.0.0
		 * @author YITH
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {

				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**show all info about shipping and delivery date
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function show_date_info() {

			global $product;

			$show = ! ( $product->is_downloadable() || $product->is_virtual() );

			if ( apply_filters( 'yith_delivery_date_show_date_info', $show, $product ) ) {
				$this->get_template_info( $product );
			}
		}

		/**
		 * @param WC_Product $product
		 */
		public function get_template_info( $product ){

			$args = $this->get_date_info( $product );
			wc_get_template( 'woocommerce/single-product/show_product_date_info.php', $args, YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );


			if ( 'variable' === $product->get_type() ) {

				wc_get_template( 'woocommerce/single-product/show_variation_date_info.php', array(), YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );

			}
		}

		/**
		 * @param WC_Product $product
		 *
		 * @return array
		 */
		public function get_date_info( $product ) {
			$option = get_option( 'ywcdd_processing_type', 'checkout' );
			$args   = array();
			if ( 'checkout' == $option ) {
				list( $shipping_id, $processing_date, $process_method_id ) = $this->get_min_processing_date( $product );

				if ( $processing_date ) {
					list( $carrier_id, $delivery_date ) = $this->get_min_delivery_date( $process_method_id, $processing_date );
					$last_useful_shipping_date = YITH_Delivery_Date_Manager()->get_last_shipping_date( $delivery_date, $process_method_id, $carrier_id );
					$timelimit                 = $this->get_order_within_information( $process_method_id );

					$args = array(
						'shipping_id'         => $shipping_id,
						'processing_id'       => $process_method_id,
						'carrier_id'          => $carrier_id,
						'first_shipping_date' => $processing_date,
						'last_shipping_date'  => $this->get_last_shipping_date_html( $last_useful_shipping_date ),
						'delivery_date'       => $this->get_delivery_info_html( $delivery_date, $timelimit ),
						'product'             => $product
					);
				}
			} else {
				$query_args           = array(
					'meta_query' => array(
						array(
							'key'     => '_ywcdd_type_checkout',
							'value'   => 'no',
							'compare' => '='
						)
					),
					'fields'     => 'ids'
				);
				$processing_method    = YITH_Delivery_Date_Processing_Method()->get_processing_method( $query_args );
				$processing_method_id = isset( $processing_method[0] ) ? $processing_method[0] : false;


				$product_table_id = YITH_Delivery_Date_Product_Table_Frontend()->get_product_table_id( $product );

				if ( $product_table_id ) {
					$carrier_id      = get_post_meta( $product_table_id, 'ywcdd_table_select_carrier', true );
					$need_table_days = get_post_meta( $product_table_id, 'ywcdd_table_need_days', true );
					$need_day        = get_post_meta( $processing_method_id, '_ywcdd_minworkday', true );

					if ( apply_filters( 'ywcdd_choose_max_processing_day', true ) ) {
						$need_table_days = max( $need_day, $need_table_days );
					} else {
						$need_table_days += $need_day;
					}

					$processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_method_id, array( 'min_working_day' => $need_table_days ) );

					if ( $processing_date ) {
						$delivery_dates = YITH_Delivery_Date_Manager()->get_all_delivery_dates( $carrier_id, array(
							'from_date' => $processing_date,
							'max_range' => 4
						) );

						$quantity_price = get_post_meta( $product_table_id, 'ywcdd_qty_product_table', true );

						$delivery_date = $delivery_dates[0];

						for ( $i = 0; $i < 4; $i ++ ) {

							if ( ! yith_delivery_date_column_is_disabled( $quantity_price, $i ) ) {
								$delivery_date = $delivery_dates[ $i ];
								break;
							}
						}

						$last_useful_shipping_date = YITH_Delivery_Date_Manager()->get_last_shipping_date( $delivery_date, $processing_method_id, $carrier_id );
						$timelimit                 = $this->get_order_within_information( $processing_method_id );

						$args = array(
							'shipping_id'         => false,
							'processing_id'       => $processing_method_id,
							'carrier_id'          => $carrier_id,
							'first_shipping_date' => $processing_date,
							'last_shipping_date'  => $this->get_last_shipping_date_html( $last_useful_shipping_date ),
							'delivery_date'       => $this->get_delivery_info_html( $delivery_date, $timelimit ),
							'product'             => $product
						);
					}
				}

			}

			return $args;
		}

		/**
		 * @param $last_shipping_date
		 *
		 * @return string
		 * @throws Exception
		 */
		public function get_last_shipping_date_html( $last_shipping_date ) {

			$show_shipping_date        = get_option( 'ywcdd_ddm_enable_shipping_message', 'no' );
			$last_shipping_date_string = get_option( 'ywcdd_ddm_shipping_message', '' );
			$last_shipping_date_html   = '';
			$span_html                 = "<span class='ywcdd_date_info shipping_date'>%s</span>";
			$last_shipping_format      = apply_filters( 'ywcdd_last_shipping_date_format', '' );
			if ( yith_plugin_fw_is_true( $show_shipping_date ) && ! empty( $last_shipping_date_string ) ) {
				$last_shipping_date_formatted = wc_format_datetime( new WC_DateTime( date( 'Y-m-d', $last_shipping_date ), new DateTimeZone( 'UTC' ) ), $last_shipping_format );
				$last_shipping_date_formatted = sprintf( $span_html, $last_shipping_date_formatted );
				$last_shipping_date_html      = str_replace( '{shipping_date}', $last_shipping_date_formatted, $last_shipping_date_string );
			}

			return apply_filters( 'yith_delivery_date_get_last_shipping_date_html', $last_shipping_date_html, $last_shipping_date, $last_shipping_format, $span_html );
		}

		/**
		 * @param $delivery_date
		 * @param $time_limit
		 *
		 * @return string|
		 * @throws Exception
		 */
		public function get_delivery_info_html( $delivery_date, $time_limit ) {

			$show_delivery_date = get_option( 'ywcdd_ddm_enable_delivery_message', 'no' );
			if ( empty( $time_limit ) ) {
				$delivery_date_string = get_option( 'ywcdd_ddm_time_limit_alternative_txt', '' );
			} else {
				$delivery_date_string = get_option( 'ywcdd_ddm_delivery_message', '' );
			}
			$delivery_date_html   = '';
			$delivery_info_format = apply_filters( 'ywcdd_delivery_info_format', '' );
			$span_delivery_html   = "<span class='ywcdd_date_info delivery_date'>%s</span>";
			$span_time_html       = "<span class='ywcdd_date_info time_limit'>%s</span>";

			if ( yith_plugin_fw_is_true( $show_delivery_date ) && ! empty( $delivery_date_string ) ) {

				$delivery_date_formatted = wc_format_datetime( new WC_DateTime( date( 'Y-m-d', $delivery_date ), new DateTimeZone( 'UTC' ) ), $delivery_info_format );
				$delivery_date_formatted = sprintf( $span_delivery_html, $delivery_date_formatted );
				$time_limit_formatted    = sprintf( $span_time_html, $time_limit );
				$delivery_date_string    = str_replace( '{delivery_date}', $delivery_date_formatted, $delivery_date_string );
				$delivery_date_html      = str_replace( '{time_limit}', $time_limit_formatted, $delivery_date_string );
			}

			return apply_filters( 'yith_delivery_date_get_delivery_info_html', $delivery_date_html, $delivery_date, $time_limit, $delivery_info_format, $span_delivery_html, $span_time_html );
		}

		/**
		 * calculate the "purchase within" value for receive the order in a specific date
		 *
		 * @param int $processing_method_id
		 *
		 * @return string
		 * @author Salvatore Strano
		 *
		 */
		public function get_order_within_information( $processing_method_id ) {
			$now                = current_time( 'Y-m-d H:i' );
			$now_time_stamp     = strtotime( $now );
			$wDay               = strtolower( date( "D", $now_time_stamp ) );
			$shipping_work_days = YITH_Delivery_Date_Processing_Method()->get_work_days( $processing_method_id );
			$is_a_work_day      = isset( $shipping_work_days[ $wDay ] );
			$is_a_holiday       = YITH_Delivery_Date_Calendar()->is_holiday( $processing_method_id, $now_time_stamp );
			$limit              = '';
			if ( $is_a_work_day && ! $is_a_holiday ) {
				$timelimit           = ! empty( $shipping_work_days[ $wDay ]['timelimit'] ) ? $shipping_work_days[ $wDay ]['timelimit'] : '23:59';
				$timelimit_timestamp = strtotime( $timelimit );

				if ( $timelimit_timestamp > $now_time_stamp ) {
					$limit     = date( 'Y-m-d', $now_time_stamp ) . " {$timelimit}";
					$datetime1 = new DateTime( $limit );
					$datetime2 = new DateTime( $now );
					$interval  = $datetime1->diff( $datetime2 );
					$hours     = $interval->h;
					$minutes   = $interval->i;

					$hours_label   = _nx( 'hour', 'hours', $hours, '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' );
					$minutes_label = _nx( 'minute', 'minutes', $minutes, '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' );

					$limit = sprintf( '%s %s %s %s %s', $interval->format( '%H' ), $hours_label, _x( 'and', '[Part of]: 4 hours and 50 minutes', 'yith-woocommerce-delivery-date' ), $interval->format( '%I' ), $minutes_label );
				}
			}

			return $limit;
		}

		/**
		 * get the shipping method for current customer
		 * @return array
		 * @since 2.0.0
		 * @author YITH
		 */
		public function get_customer_shipping_method() {
			$shipping_methods = array();
			if ( WC()->customer ) {
				$customer_packing = array(
					'destination' => array(
						'country'  => WC()->customer->get_shipping_country(),
						'state'    => WC()->customer->get_shipping_state(),
						'postcode' => WC()->customer->get_shipping_postcode()
					)
				);
				$zone1            = WC_Shipping_Zones::get_zone_matching_package( $customer_packing );

				$shipping_methods = $zone1->get_shipping_methods( true );
			}

			return $shipping_methods;
		}

		/**
		 * return the available order processing methods for the customer
		 *
		 * @param array $shipping_methods
		 *
		 * @return array
		 * @author  YITH
		 * @since 2.0.0
		 *
		 */
		public function get_order_processing_methods( $shipping_methods ) {
			$order_processing_method = array();
			foreach ( $shipping_methods as $shipping_id => $shipping_method ) {
				if ( ! empty( $shipping_method->instance_settings['select_process_method'] ) ) {
					$order_processing_method[ $shipping_id ] = $shipping_method->instance_settings['select_process_method'];
				}
			}

			return array_unique( $order_processing_method );
		}

		/**calculate the minimum processing date for the product
		 *
		 * @param WC_Product $product
		 *
		 * @return array
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function get_min_processing_date( $product ) {

			$min              = false;
			$min_shipping_id  = false;
			$min_process_id   = false;
			$shipping_methods = $this->get_customer_shipping_method();
			$processing_ids   = $this->get_order_processing_methods( $shipping_methods );

			$product_day = $this->get_custom_base_day_for_product( $product );


			if ( count( $processing_ids ) == 0 ) {

				$query_args     = array(
					'meta_query' => array(
						array(
							'key'     => '_ywcdd_type_checkout',
							'value'   => 'yes',
							'compare' => '='
						)
					),
					'fields'     => 'ids'
				);
				$processing_ids = YITH_Delivery_Date_Processing_Method()->get_processing_method( $query_args );
			}
			foreach ( $processing_ids as $shipping_id => $processing_id ) {

				if ( $product_day > 0 ) {
					$processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_id, array( 'min_working_day' => $product_day ) );
				} else {
					$processing_date = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_id );
				}
				if ( ! $min || $processing_date < $min ) {
					$min             = $processing_date;
					$min_shipping_id = $shipping_id;
					$min_process_id  = $processing_id;
				}
			}


			return array( $min_shipping_id, $min, $min_process_id );
		}

		public function get_min_delivery_date( $processing_id, $processing_date ) {

			$carriers = YITH_Delivery_Date_Processing_Method()->get_carriers( $processing_id );

			$min            = false;
			$min_carrier_id = false;
			if ( $carriers ) {
				foreach ( $carriers as $carrier_id ) {
					$delivery_date = YITH_Delivery_Date_Manager()->get_first_delivery_date( $carrier_id, array( 'shipping_date' => $processing_date ) );

					if ( ! $min || $delivery_date < $min ) {
						$min            = $delivery_date;
						$min_carrier_id = $carrier_id;
					}
				}
			}

			return array( $min_carrier_id, $min );
		}

		/**
		 * get the processing date for the product
		 *
		 * @param WC_Product $product
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_custom_base_day_for_product( $product, $qty = 1 ) {

			if ( ! $product instanceof WC_Product ) {
				global $product;
			}

			$product_id      = $product->get_id();
			$product_base_id = false;


			if ( $product->is_type( 'variation' ) ) {
				/**
				 * @var WC_Product_Variation $product
				 */
				$product_base_id = $product->get_parent_id();
			}

			$base_day = $this->get_need_day_for_product_rule( $product_id, $qty );


			if ( - 1 == $base_day && $product_base_id ) {
				$base_day = $this->get_need_day_for_product_rule( $product_base_id, $qty );
			}

			//after checked for product rule, try with product category rules
			if ( - 1 == $base_day ) {

				$product_id = $product_base_id ? $product_base_id : $product_id;

				$base_day = $this->get_need_day_for_product_categories_rule( $product_id, $qty );
			}


			return apply_filters( 'yith_delivery_date_product_processing_days', $base_day, $product, $qty );
		}

		/**
		 * return how processing day are needs for a product
		 *
		 * @param int $product_id
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_need_day_for_product_rule( $product_id, $qty ) {

			$product_rules = get_option( 'yith_new_shipping_day_prod', array() );
			$product_rules = empty( $product_rules ) ? array() : $product_rules;
			$need_day      = - 1;

			if ( count( $product_rules ) > 0 && isset( $product_rules[ $product_id ] ) ) {
				$is_enabled = isset( $product_rules[ $product_id ]['enabled'] ) ? $product_rules[ $product_id ]['enabled'] : 'yes';

				if ( yith_plugin_fw_is_true( $is_enabled ) ) {

					$rules = $product_rules[ $product_id ]['need_process_day'];

					// this is a compatibility with old version , where need_process_day was a number
					if ( ! is_array( $rules ) && is_numeric( $rules ) ) {
						$need_day = $rules;
					} else {

						foreach ( $rules as $rule ) {

							if ( ( ! empty( $rule['from'] ) && $rule['from'] <= $qty ) && ( empty( $rule['to'] ) || ( $qty <= $rule['to'] ) ) ) {

								$need_day = ! empty( $rule['day'] ) ? $rule['day'] : - 1;
								break;
							}
						}
					}
				}

			}

			return $need_day;
		}

		/**
		 * @param int $product_id
		 * @param int $qty
		 *
		 * @return int
		 */
		public function get_need_day_for_product_categories_rule( $product_id, $qty ) {
			$need_day = - 1;

			$category_rules = get_option( 'yith_new_shipping_day_cat', array() );

			if ( is_array( $category_rules ) && count( $category_rules ) > 0 ) {
				$terms    = wp_get_post_terms( $product_id, 'product_cat' );
				$term_ids = wp_list_pluck( $terms, 'term_id' );
				foreach ( $terms as $term ) {

					if ( $term->parent == 0 && in_array( $term->parent, $term_ids ) ) {

						continue;
					}
					if ( isset( $category_rules[ $term->term_id ] ) ) {
						$is_enabled = isset( $category_rules[ $term->term_id ]['enabled'] ) ? $category_rules[ $term->term_id ]['enabled'] : 'yes';

						if ( yith_plugin_fw_is_true( $is_enabled ) ) {
							$rules = $category_rules[ $term->term_id ]['need_process_day'];
							if ( ! is_array( $rules ) && is_numeric( $rules ) ) {
								$cat_day = $rules;
							} else {

								foreach ( $rules as $rule ) {

									if ( ( ! empty( $rule['from'] ) && $rule['from'] <= $qty ) && ( empty( $rule['to'] ) || ( $qty <= $rule['to'] ) ) ) {

										$cat_day = ! empty( $rule['day'] ) ? $rule['day'] : - 1;

									}
								}
							}

							$need_day = max( $need_day, $cat_day );
						}

					}
				}


			}

			return $need_day;
		}

		/**
		 * get date info for variation products
		 *
		 * @param array $variation_data
		 * @param WC_Product_Variable $variable_product
		 * @param WC_Product_Variation $variation_product
		 *
		 * @return array
		 * @author YITH
		 * @since 2.0.0
		 *
		 */
		public function add_variation_data( $variation_data, $variable_product, $variation_product ) {

			$show = ! ( $variation_product->is_downloadable() || $variation_product->is_virtual() );
			$show = apply_filters( 'yith_delivery_date_show_date_info', $show, $variation_product );
			if ( $show ) {

				$argument = $this->get_date_info( $variation_product );

				$variation_data['ywcdd_last_shipping_info'] = isset( $argument['last_shipping_date'] ) ? $argument['last_shipping_date'] : '';
				$variation_data['ywcdd_delivery_info']      = isset( $argument['delivery_date'] ) ? $argument['delivery_date'] : '';
			}

			return $variation_data;
		}

		/**
		 * include product scripts
		 * @author YITH
		 * @since 2.0.0
		 */
		public function include_scripts() {

			wp_register_script( 'ywcdd_single_product', YITH_DELIVERY_DATE_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_deliverydate_single_product.js' ), array( 'jquery' ), YITH_DELIVERY_DATE_VERSION, true );
			wp_register_style( 'ywcdd_single_product', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_deliverydate_single_product.css', array(), YITH_DELIVERY_DATE_VERSION );

			if ( is_product() ) {
				wp_enqueue_script( 'ywcdd_single_product' );
				wp_enqueue_style( 'ywcdd_single_product' );

				$bg_shipping = get_option( 'ywcdd_dm_customization_ready_bg', '#eff3f5' );
				$bg_delivery = get_option( 'ywcdd_dm_customization_customer_bg', '#ffdea5' );

				$icon_shipping = get_option( 'ywcdd_dm_customization_ready_icon', YITH_DELIVERY_DATE_ASSETS_URL . 'images/truck.png' );
				$icon_delivery = get_option( 'ywcdd_dm_customization_customer_icon', YITH_DELIVERY_DATE_ASSETS_URL . 'images/clock.png' );

				$extra_css = "#ywcdd_info_shipping_date {
							        background:  $bg_shipping;
							    }

							    #ywcdd_info_first_delivery_date {
							        background: $bg_delivery
							    }
							    #ywcdd_info_shipping_date .ywcdd_shipping_icon{
							        background-image: url( $icon_shipping );
							    }
							    #ywcdd_info_first_delivery_date .ywcdd_delivery_icon{
							        background-image: url(  $icon_delivery);
							    }";
				wp_add_inline_style( 'ywcdd_single_product', $extra_css );
			}


		}

		/**
		 * @param bool $show
		 * @param WC_Product $product
		 *
		 * @return bool
		 */
		public function check_for_custom_product_type( $show, $product ) {

			$check_type = array( 'booking', 'gift-card', 'ywf_deposit' );

			if ( in_array( $product->get_type(), $check_type ) ) {
				$show = false;
			}

			return $show;
		}

	}
}

if ( ! function_exists( 'YITH_Delivery_Date_Product_Frontend' ) ) {
	function YITH_Delivery_Date_Product_Frontend() {

		return YITH_Delivery_Date_Product_Frontend::get_instance();

	}
}
YITH_Delivery_Date_Product_Frontend();
