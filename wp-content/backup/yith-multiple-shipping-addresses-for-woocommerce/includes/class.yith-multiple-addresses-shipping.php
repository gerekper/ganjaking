<?php

if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Multiple_Addresses_Shipping' ) ) {
	/**
	 * Class YITH_Multiple_Addresses_Shipping
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 */
	class YITH_Multiple_Addresses_Shipping {

        /**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $version = YITH_WCMAS_VERSION;

        /**
		 * Main Instance
		 *
		 * @var YITH_Advanced_Refund_System
		 * @since 1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Multiple_Addresses_Shipping_Admin
		 * @since 1.0
		 */
		protected $admin = null;

        /**
         * Main Frontend Instance
         *
         * @var YITH_Multiple_Addresses_Shipping_Frontend
         * @since 1.0
         */
        public $frontend = null;

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct() {
            add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
	        // Load Plugin Framework
	        add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
	        add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );
	        // Integration with YITH Product Shipping for WooCommerce
	        if ( defined( 'YITH_WCPS_PREMIUM' ) ) {
	        	add_action( 'init', array( $this, 'yith_wcmas_product_shipping_integration' ), 11 );
	        }

	        $this->init_requires();
	        $this->init_classes();
        }

        public function yith_wcmas_product_shipping_integration() {
        	if ( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'heartbeat' ) {
        		return;
	        }
        	if ( class_exists( 'YITH_WooCommerce_Product_Shipping' ) ) {
		        $multi_shipping_enabled = ! empty( WC()->session ) ? WC()->session->get( 'ywcmas_multi_shipping_enabled' ) : false;
		        if ( ! $multi_shipping_enabled || ! yith_wcmas_get_multi_shipping_array() ) {
			        return;
		        }
		        remove_action( 'woocommerce_after_shipping_rate', array( YITH_WooCommerce_Product_Shipping::instance(), 'cart_shipping_info' ), 10 );
		        add_action( 'woocommerce_after_shipping_rate', array( $this, 'yith_wcps_cart_shipping_info_integration' ), 10, 2 );
	        }
        }

		public function yith_wcps_cart_shipping_info_integration( $method, $index ) {
			$multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
			$multi_shipping = yith_wcmas_get_multi_shipping_array();
			if ( ! $multi_shipping_enabled || ! $multi_shipping ) {
				return;
			}

			if ( $method->id == 'yith_wc_product_shipping_method' ) {

				$cart_unique_cost = 0;
				$cart_weight		= 0;
				$package = WC()->shipping()->packages[$index];

				if ( sizeof( $package['contents'] ) > 0 ) {
					foreach ( $package['contents'] as $item_id => $cart_item ) {
						if ( $cart_item['quantity'] > 0 ) {
							if ( $cart_item['data']->needs_shipping() ) {
								$cart_item['weight'] = isset( $cart_item['weight'] ) ? $cart_item['weight'] : 0;
								$_product = wc_get_product( $cart_item['product_id'] );
								$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $cart_item['quantity'] > 0 ? $cart_item['quantity'] : 0 );
								$cart_weight += $weight;
							}
						}
					}
				}

				$html = '';

				$total_package_items = 0;
				foreach( $package['contents'] as $cart_item ) {
					$item_name	= yit_get_prop( $cart_item['data'],'name' );
					$quantity	= $cart_item['quantity'];
					$total_package_items += (int) $cart_item['quantity'];

					$shipping_row = false;
					$cart_item['weight'] = isset( $cart_item['weight'] ) ? $cart_item['weight'] : 0;

					/**
					 * Get Shipping Row
					 */
					if ( $cart_item['variation_id'] ) {
						$_product_id = $cart_item['variation_id'];
						$_product = wc_get_product( $_product_id );
						$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $cart_item['quantity'] > 0 ? $cart_item['quantity'] : 0 );
						$shipping_row = yith_wc_product_shipping_row( $cart_item['variation_id'], $package, $cart_item['quantity'], $weight, $cart_weight );
					}
					if ( $shipping_row === false ) {
						$_product_id = $cart_item['product_id'];
						$_product = wc_get_product( $_product_id );
						$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $cart_item['quantity'] > 0 ? $cart_item['quantity'] : 0 );
						$shipping_row = yith_wc_product_shipping_row( $cart_item['product_id'], $package, $cart_item['quantity'], $weight, $cart_weight );
					}

					/**
					 * Cost Calculation
					 */
					if ( $shipping_row ) {
						$shipping_cost = $shipping_row->shipping_cost;
						$product_cost = $shipping_row->product_cost;
						$unique_cost = $shipping_row->unique_cost;
					} else {
						return;
					}

					if ( $cart_unique_cost == 0 && $unique_cost > 0 ) {
						$cart_unique_cost = $unique_cost;
					}

					$item_cost = $shipping_cost + ( $quantity * $product_cost );
					$html .= '<br /><div class="product-details">
							<small>
								<strong>' . wc_price( $item_cost ) . '</strong> -
								<strong>' . $item_name . '</strong><br />
								- ' . esc_html__( 'Cost per product', 'yith-multiple-shipping-addresses-for-woocommerce' ) . ': <strong>' . wc_price( $shipping_cost ) . '</strong><br />
								- ' . esc_html__( 'Cost per quantity', 'yith-multiple-shipping-addresses-for-woocommerce' ) . ': <strong>' . wc_price( $product_cost ) . '</strong> x ' . $quantity . '
							</small>
						</div>';
				}

				if ( $cart_unique_cost > 0 ) {
					$html .= '<br /><div class="unique-cost"><small><strong>' . wc_price( $cart_unique_cost ) . '</strong> - <strong>' . esc_html__( 'Cost per package', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</strong></small></div>';
				}

				$html = '<div id="yith_wcps_info_cart" style="border: 1px dashed #ddd; padding: 10px; margin-top: 10px; line-height: 15px;">
							<div class="total-items">
								<small>' . esc_html__( 'Total package items', 'yith-multiple-shipping-addresses-for-woocommerce' ) . ': <strong>' . $total_package_items . '</strong></small>
							</div>
							' . $html . '
						</div>';

				echo apply_filters( 'yith_wcps_info_cart_html', $html );
			}
		}

        /**
		 * Main plugin Instance
		 *
		 * @return YITH_Multiple_Addresses_Shipping Main instance
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Load plugin framework
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Load privacy class
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.4
		 */
		public function load_privacy() {
			require_once( YITH_WCMAS_PATH . 'includes/class.yith-multiple-addresses-shipping-privacy.php' );
		}

		/**
		 * Require main files
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function init_requires() {
			require_once( YITH_WCMAS_PATH . 'includes/functions.yith-mas.php' );
			require_once( YITH_WCMAS_PATH . 'includes/functions.yith-mas-gdpr.php' );
			require_once( YITH_WCMAS_PATH . 'includes/class.yith-multiple-addresses-shipping-admin.php' );
			require_once( YITH_WCMAS_PATH . 'includes/class.yith-multiple-addresses-shipping-frontend.php' );
			require_once( YITH_WCMAS_PATH . 'includes/tables/class.yith-wcmas-table-template.php' );
			require_once( YITH_WCMAS_PATH . 'includes/tables/class.yith-wcmas-excluded-products-table-options.php' );
			require_once( YITH_WCMAS_PATH . 'includes/tables/class.yith-wcmas-excluded-categories-table-options.php' );
		}

        /**
		 * Class Initialization
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0.0
		 */
		public function init_classes() {
            if ( is_admin() ) {
				$this->admin = new YITH_Multiple_Addresses_Shipping_Admin();
	            YITH_WCMAS_Excluded_Products_Table_Options();
	            YITH_WCMAS_Excluded_Categories_Table_Options();
            }

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Multiple_Addresses_Shipping_Frontend();
			}

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( YITH_WCMAS_PATH . 'includes/elementor/class.yith-mas-elementor.php' );
			}
		}

		/*
		 * Register Email Classes
		 *
		 * Register the new email classes used by the plugin
		 * @param array $email_classes
		 *
		 * @return array
		 */
        function register_email_classes( $email_classes ) {
            $email_classes['YITH_MAS_Shipping_Status_Change_Email'] = include( 'emails/class.yith-mas-change-status-email.php' );
            return $email_classes;
        }

    }
}