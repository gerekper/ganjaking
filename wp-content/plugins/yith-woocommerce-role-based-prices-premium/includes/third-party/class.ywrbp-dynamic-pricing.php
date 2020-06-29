<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCRBP_YITH_Dynamic_Pricing_Module' ) ) {

	class YWCRBP_YITH_Dynamic_Pricing_Module {

		protected static $_instance;
		protected $dynamic_frontend;
		/**
		 * @var YITH_Role_Based_Prices_Product
		 */
		protected $role_based_product;

		public function __construct() {


			add_action( 'template_redirect', array( $this, 'init_integration' ) );


		}

		public function init_integration() {


			if ( apply_filters( 'ywcrbp_init_dynamic_integration', true ) ) {
				$this->dynamic_frontend   = YITH_WC_Dynamic_Pricing_Frontend();
				$this->role_based_product = YITH_Role_Based_Prices_Product();


				$this->remove_dynamic_filters();


				add_filter( 'ywcrbp_simple_your_price_html', array( $this, 'get_dynamic_pricing_html' ), 20, 3 );
				add_filter( 'ywcrbp_simple_regular_price_html', array(
					$this,
					'get_dynamic_regular_pricing_html'
				), 20, 3 );
				add_filter( 'ywcrbp_simple_sale_price_html', array( $this, 'get_dynamic_sale_pricing_html' ), 20, 3 );
				add_filter( 'woocommerce_product_get_sale_price', array( $this, 'remove_sale_price' ), 30, 2 );
				add_filter( 'woocommerce_product_variation_get_sale_price', array(
					$this,
					'remove_sale_price'
				), 30, 2 );
				add_filter( 'yith_ywrbp_price', array( $this, 'change_role_based_prices' ), 30, 2 );

				add_filter( 'ywcrbp_variable_regular_price_html', array(
					$this,
					'get_dynamic_variable_regular_price'
				), 20, 4 );
				add_filter( 'ywcrbp_variable_sale_price_html', array( $this, 'remove_sale_price' ), 20, 3 );
				add_filter( 'ywcrbp_variable_your_price_html', array(
					$this,
					'get_dynamic_variable_your_price'
				), 20, 4 );

				add_filter( 'ywdpd_get_variable_prices', array( $this, 'get_variable_role_based_prices' ), 20, 2 );


				add_filter( 'yith_ywdpd_get_discount_price', array( $this, 'show_right_price_table' ), 20, 4 );
			}

		}

		public function remove_dynamic_filters() {
			remove_filter( 'woocommerce_get_price_html', array( $this->dynamic_frontend, 'get_price_html' ), 10 );
			remove_filter( 'woocommerce_get_variation_price_html', array(
				$this->dynamic_frontend,
				'get_price_html'
			), 10 );
		}

		public function remove_role_based_filters() {
			remove_filter( 'woocommerce_get_price_html', array( YITH_Role_Based_Prices_Product(), 'get_price_html' ), 11 );
			remove_filter( 'woocommerce_get_variation_price_html', array(
				YITH_Role_Based_Prices_Product(),
				'get_price_html'
			), 11 );
		}

		public function reset_role_based_action() {


			global $product;
			$product_id            = $product->get_id();
			$all_role_based_prices = get_site_transient( 'ywcrb_rolebased_prices' );
			$all_role_based_prices = empty( $all_role_based_prices ) ? array() : $all_role_based_prices;
			$active_currency       = get_woocommerce_currency();

			$user_role = YITH_Role_Based_Prices_Product()->user_role['role'];
			if ( isset( $all_role_based_prices[ $product_id ][ $active_currency ][ $user_role ] ) ) {
				unset( $all_role_based_prices[ $product_id ][ $active_currency ][ $user_role ] );
				set_site_transient( 'ywcrb_rolebased_prices', $all_role_based_prices );
			}

		}

		/**
		 * @param string $price_html
		 * @param float $role_price
		 * @param WC_Product $product
		 */
		public function get_dynamic_pricing_html( $price_html, $role_price, $product ) {

			$dynamic_price = $this->get_dynamic_price( $product, $role_price );

			if ( $dynamic_price < $role_price ) {

				$display_your_price    = wc_get_price_to_display( $product, array(
					'price' => $role_price,
					'qty'   => 1
				) );
				$display_dynamic_price = wc_get_price_to_display( $product, array(
					'price' => $dynamic_price,
					'qty'   => 1
				) );
				$your_price_html       = wc_price( $display_your_price ) . YITH_Role_Based_Prices_Product()->get_price_suffix( $product, $role_price );
				$dynamic_price_html    = wc_price( $display_dynamic_price ) . YITH_Role_Based_Prices_Product()->get_price_suffix( $product, $dynamic_price );
				$your_price_txt        = apply_filters( 'ywrbp_change_price_prefix', get_option( 'ywcrbp_your_price_txt' ), $product );

				$price_format = YITH_WC_Dynamic_Pricing()->get_option( 'price_format', '<del>%original_price%</del> %discounted_price%' );

				$price_html = str_replace( '%original_price%', $your_price_html, $price_format );
				$price_html = str_replace( '%discounted_price%', $dynamic_price_html, $price_html );
				$price_html = $your_price_txt . ' ' . $price_html;
			}

			return $price_html;
		}

		/**
		 * @param WC_Product $product
		 * @param float $price
		 */
		public function get_dynamic_price( $product, $price ) {

			if ( ! $product->is_type( 'variable' ) ) {
				$show_minimum_price = YITH_WC_Dynamic_Pricing()->get_option( 'show_minimum_price' );
				if ( ywdpd_is_true( $show_minimum_price ) ) {
					$price = YITH_WC_Dynamic_Pricing_Frontend()->get_minimum_price( $product );
				} else {
					$price = YITH_WC_Dynamic_Pricing_Frontend()->get_minimum_price( $product, 1 );
				}


			}

			return $price;
		}

		/**
		 * @param string $price_html
		 * @param float $regular_price
		 * @param WC_Product $product
		 */
		public function get_dynamic_regular_pricing_html( $price_html, $regular_price, $product ) {

			$has_role_price = YITH_Role_Based_Prices_Product()->get_role_based_price( $product );

			if ( 'no_price' == $has_role_price ) {
				$dynamic_price = (float) YITH_WC_Dynamic_Pricing()->get_discount_price( $regular_price, $product );
				if ( $dynamic_price < $regular_price ) {

					$display_regular_price = wc_get_price_to_display( $product, array(
						'price' => $regular_price,
						'qty'   => 1
					) );
					$display_dynamic_price = wc_get_price_to_display( $product, array(
						'price' => $dynamic_price,
						'qty'   => 1
					) );

					$your_price_html    = wc_price( $display_regular_price ) . YITH_Role_Based_Prices_Product()->get_price_suffix( $product, $regular_price );
					$dynamic_price_html = wc_price( $display_dynamic_price ) . YITH_Role_Based_Prices_Product()->get_price_suffix( $product, $dynamic_price );
					$your_price_txt     = get_option( 'ywcrbp_regular_price_txt' );
					$price_format       = YITH_WC_Dynamic_Pricing()->get_option( 'price_format', '<del>%original_price%</del> %discounted_price%' );

					$price_html = str_replace( '%original_price%', $your_price_html, $price_format );
					$price_html = str_replace( '%discounted_price%', $dynamic_price_html, $price_html );
					$price_html = $your_price_txt . ' ' . $price_html;

					$price_html = YITH_Role_Based_Prices_Product()->get_formatted_price_html( $price_html, false, 'regular' );
				}
			}


			if ( $has_role_price == $regular_price ) {
				return '';
			}

			return $price_html;
		}

		/**
		 * @param string $price_html
		 * @param float $sale_price
		 * @param WC_Product $product
		 */
		public function get_dynamic_sale_pricing_html( $price_html, $sale_price, $product ) {

			$role_based = YITH_Role_Based_Prices_Product()->get_role_based_price( $product );
			if ( 'no_price' !== $role_based && YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {
				return '';
			}

			return $price_html;
		}

		/**
		 * @param $sale_price
		 * @param WC_Product $product
		 */
		public function remove_sale_price( $sale_price, $product ) {

			if ( $product instanceof  WC_Product) {
				if ( $product->is_type( 'variable' ) ) {
					$prices = $this->get_variable_role_based_prices( array(), $product );

					$role_based = count( $prices ) == 0 ? 'no_price' : '';
				} else {
					$role_based = YITH_Role_Based_Prices_Product()->get_role_based_price( $product );
				}
				if ( 'no_price' !== $role_based && YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {
					return '';
				}
			}

			return $sale_price;
		}

		/**
		 * @param string|float $role_based_prices
		 * @param WC_Product $product
		 *
		 * @return mixed
		 */
		public function change_role_based_prices( $role_based_prices, $product ) {

			if ( 'no_price' == $role_based_prices && YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {
				remove_filter( 'woocommerce_product_get_sale_price', array( $this, 'remove_sale_price' ), 30 );
				remove_filter( 'woocommerce_product_variation_get_sale_price', array(
					$this,
					'remove_sale_price'
				), 30 );
				$sale_price = $product->get_sale_price();
				if ( $sale_price > 0 ) {
					$role_based_prices = $sale_price;
				} else {

					$role_based_prices = $product->get_regular_price();
				}

			}


			return $role_based_prices;
		}

		/**
		 * @param string $regular_price_html
		 * @param WC_product $product
		 * @param float $min_regular_price
		 * @param float $max_regular_price
		 *
		 * @return string
		 */
		public function get_dynamic_variable_regular_price( $regular_price_html, $product, $min_regular_price, $max_regular_price ) {

			$your_prices = YITH_Role_Based_Prices_Product()->get_variation_new_prices( $product );


			if ( count( $your_prices ) > 0 && YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {
				$min_your_price = floatval( current( $your_prices ) );
				$max_your_price = floatval( end( $your_prices ) );

				if ( $min_regular_price == $min_your_price && $max_regular_price == $max_your_price ) {
					return '';
				}
			}

			return $regular_price_html;
		}

		/**
		 * @param string $your_price_html
		 * @param WC_Product_Variable $product
		 * @param float $min_your_price
		 * @param float $max_your_price
		 *
		 * @return |string
		 */
		public function get_dynamic_variable_your_price( $your_price_html, $product, $min_your_price, $max_your_price ) {


			$min_price = (float) $this->get_minimum_price( $product );
			$max_price = (float) $this->get_maximum_price( $product );


			$show_range_price = apply_filters( 'ywcrb_show_dynamic_range_price_html', true );

			$regular_price_txt = apply_filters( 'ywrbp_change_price_prefix', get_option( 'ywcrbp_your_price_txt' ), $product );

			if ( YITH_WC_Dynamic_Pricing()->check_discount( $product ) ) {


				$your_price_html = $regular_price_txt;

				$min_your_price_html = YITH_Role_Based_Prices_Product()->get_your_price_html( $product, $min_your_price );

				$max_your_price_html = '';

				if ( $show_range_price && $min_your_price !== $max_your_price ) {

					$max_your_price_html = YITH_Role_Based_Prices_Product()->get_your_price_html( $product, $max_your_price );
					$min_your_price_html = ywcrbp_get_format_price_from_to( $product, $min_your_price_html, $max_your_price_html );
				}


				if ( $show_range_price && $min_price !== $max_price && $max_your_price_html !== '' ) {
					$min_dp_price_html = YITH_Role_Based_Prices_Product()->get_your_price_html( $product, $min_price );
					$max_dp_price_html = YITH_Role_Based_Prices_Product()->get_your_price_html( $product, $max_price );
					$min_dp_price_html = ywcrbp_get_format_price_from_to( $product, $min_dp_price_html, $max_dp_price_html );
				} else {
					$min_dp_price_html = YITH_Role_Based_Prices_Product()->get_your_price_html( $product, $max_price );
				}

				if ( $min_price !== $min_your_price || $max_price !== $max_your_price ) {

					$price_format = YITH_WC_Dynamic_Pricing()->get_option( 'price_format', '<del>%original_price%</del> %discounted_price%' );
					$price_html   = str_replace( '%original_price%', $min_your_price_html, $price_format );
					$price_html   = str_replace( '%discounted_price%', $min_dp_price_html, $price_html );
				} else {
					$price_html = $min_your_price_html;
				}
				$your_price_html .= YITH_Role_Based_Prices_Product()->get_formatted_price_html( $price_html );
			}

			return $your_price_html;
		}

		/**
		 * @param array $prices
		 * @param WC_Product_Variable $product
		 *
		 * @return array
		 */
		public function get_variable_role_based_prices( $prices, $product ) {

			remove_filter( 'ywdpd_get_variable_prices', array( $this, 'get_variable_role_based_prices' ), 20 );
			remove_filter( 'yith_ywrbp_price', array( $this, 'change_role_based_prices' ), 30 );
			$your_prices = YITH_Role_Based_Prices_Product()->get_variation_new_prices( $product );

			if ( count( $your_prices ) > 0 ) {

				$prices['price'] = $your_prices;
			}
			add_filter( 'ywdpd_get_variable_prices', array( $this, 'get_variable_role_based_prices' ), 20, 2 );
			add_filter( 'yith_ywrbp_price', array( $this, 'change_role_based_prices' ), 20, 2 );

			return $prices;
		}

		/**
		 * @param WC_Product_Variable $product
		 *
		 * @return float;
		 */
		public function get_minimum_price( $product ) {
			$table_rules = $this->dynamic_frontend->get_table_rules( $product );

			$prices = YITH_Role_Based_Prices_Product()->get_variation_new_prices( $product );

			$min_price = min( $prices );

			if ( $table_rules ) {
				foreach ( $table_rules as $rules ) {

					foreach ( $rules['rules'] as $rule ) {
						if ( isset( $rule['min_quantity'] ) && 1 == $rule['min_quantity'] ) {
							$min_price = ywdpd_get_discounted_price_table( $min_price, $rule );;
						}
					}
				}
			}

			return $min_price;
		}

		/**
		 * @param float $new_price
		 * @param int $product_id
		 * @param float $old_price
		 * @param array $rule
		 *
		 * @return float
		 */
		public function show_right_price_table( $new_price, $product_id = false, $old_price = false, $rule = array() ) {

			remove_filter( 'yith_ywdpd_get_discount_price', array( $this, 'show_right_price_table' ), 20 );
			$product = wc_get_product( $product_id );


			if ( $product && ! empty( $rule['type_discount'] ) && 'fixed-price' == $rule['type_discount'] ) {

				YITH_Role_Based_Prices_Product()->load_global_rules();
				$current_rule = YITH_Role_Based_Prices_Product()->user_role['role'];
				$global_rules = YITH_Role_Based_Type()->get_price_rule_by_user_role( $current_rule, false );
				$role_price   = ywcrbp_calculate_product_price_role( $product, $global_rules, $current_rule, $new_price );

				$new_price = 'no_price' !== $role_price ? $role_price : $new_price;
			}

			return $new_price;
		}

		/**
		 * @param WC_Product_Variable $product
		 *
		 * @return float;
		 */
		public function get_maximum_price( $product ) {
			$table_rules = $this->dynamic_frontend->get_table_rules( $product );

			$prices = YITH_Role_Based_Prices_Product()->get_variation_new_prices( $product );

			$max_price = max( $prices );
			if ( $table_rules ) {
				foreach ( $table_rules as $rules ) {
					foreach ( $rules['rules'] as $rule ) {
						if ( isset( $rule['min_quantity'] ) && 1 == $rule['min_quantity'] ) {
							$max_price = ywdpd_get_discounted_price_table( $max_price, $rule );
						}
					}
				}
			}

			return $max_price;
		}


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}


if ( ! function_exists( 'YWCRBP_YITH_Dynamic_Pricing_Module' ) ) {
	function YWCRBP_YITH_Dynamic_Pricing_Module() {
		return YWCRBP_YITH_Dynamic_Pricing_Module::get_instance();
	}
}
YWCRBP_YITH_Dynamic_Pricing_Module();
