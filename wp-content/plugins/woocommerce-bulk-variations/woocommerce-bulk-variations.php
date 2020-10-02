<?php
/*
 * Plugin Name: WooCommerce Bulk Variations
 * Woo: 187872:aa3a54eef10ec085a1b1357375e86c2d
 * Plugin URI: https://www.elementstark.com/solutions/woocommerce-bulk-variation-forms/
 * Description: WooCommerce Bulk Variations allows your shoppers to add more than one variation at a time to the cart.  Great for wholesales, B2B sites, and for easing the process of adding more than one variation at a time for anyone.
 * Version: 1.6.6
 * Author: Lucas Stark
 * Author URI: https://elementstark.com
 * Requires at least: 3.3
 * Tested up to: 5.4.1
 * Text Domain: woocommerce-bulk-variations
 * Domain Path: /i18n/languages/
 * Copyright: © 2009-2020 Lucas Stark.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 3.0.0
 * WC tested up to: 4.1.0
 */


/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'aa3a54eef10ec085a1b1357375e86c2d', '187872' );

if ( is_woocommerce_active() ) {
	/**
	 * Localisation
	 * */
	load_plugin_textdomain( 'woocommerce-bulk-variations', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

	class WC_Bulk_Variations {
		/**
		 * @var WC_Bulk_Variations
		 */
	    private static $_instance;

		/**
		 * Registers the single instance.
		 */
		public static function register() {
	        if (self::$_instance === null){
	            self::$_instance = new WC_Bulk_Variations();
            }
        }

		/**
		 * @return WC_Bulk_Variations
		 */
        public static function instance() {
	        self::register();
	        return self::$_instance;
        }

		/** URLS ***************************************************************** */
		public $plugin_url;
		public $plugin_path;
		public $is_quick_view = false;
		public $assets_version = '1.6.5';

		public function __construct() {
			global $pagenow;
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			require 'class-wc-bulk-variations-compatibility.php';
			require 'woocommerce-bulk-variations-functions.php';
			if ( is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) ) {

				require 'woocommerce-bulk-variations-admin.php';
				$this->admin = new WC_Bulk_Variations_Admin();
			} elseif ( ! is_admin() ) {


				add_action( 'template_redirect', array( $this, 'on_template_redirect' ), 99 );

				//Register the hook to render the bulk form as late as possibile
				add_action( 'woocommerce_before_single_product', array( $this, 'render_bulk_form' ), 999 );

				add_action( 'wc_quick_view_before_single_product', array( $this, 'render_bulk_form' ), 999 );

				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'before_add_to_cart_form' ) );

				if ( isset( $_POST['add-variations-to-cart'] ) && $_POST['add-variations-to-cart'] ) {
					add_action( 'wp_loaded', array( $this, 'process_matrix_submission' ), 99 );
				}
			}

			add_action( 'wc_quick_view_before_single_product', array( $this, 'set_is_quick_view' ), 0 );
			add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'include_quickview_bulk_form_assets' ) );
			add_action('woocommerce_bv_before_add_to_cart_button', array($this, 'on_woocommerce_bv_before_add_to_cart_button'));

		}

		public function unbind_render_bulk_form() {
			remove_action( 'woocommerce_before_single_product', array( $this, 'render_bulk_form' ), 999 );
			remove_action( 'wc_quick_view_before_single_product', array( $this, 'render_bulk_form' ), 999 );
		}

		public function unbind_before_add_to_cart_form() {
			remove_action( 'woocommerce_before_add_to_cart_form', array( $this, 'before_add_to_cart_form' ) );
		}

		/**
		 * Localisation
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-bulk-variations' );
			$dir    = trailingslashit( WP_LANG_DIR );

			load_textdomain( 'woocommerce-bulk-variations', $dir . 'woocommerce-bulk-variations/woocommerce-bulk-variations-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-bulk-variations', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages/' );
		}


		public function set_is_quick_view() {
			$product             = wc_get_product( get_the_ID() );
			$bv_type             = wc_bv_get_post_meta( $product->get_id(), '_bv_type', true );
			$this->is_quick_view = ! empty( $bv_type );
		}

		public function is_bulk_variation_form() {
			global $post;

			if ( ! $post ) {
				return false;
			}

			if ( ! is_product() ) {
				return false;
			}

			$product = wc_get_product( $post->ID );

			if ( ! wc_bv_get_post_meta( $product->get_id(), '_bv_type', true ) ) {
				return false;
			}

			if ( $product && ! $product->has_child() && ! $product->is_type( 'variable' ) ) {
				return false;
			}

			return apply_filters( 'woocommerce_bv_render_form', true, $product );
		}

		public function is_only_bulk_variation_form() {
			global $post;
			if ( ! $this->is_bulk_variation_form() ) {
				return false;
			}

			$single_view =  wc_bv_get_post_meta( $post->ID, '_bv_single_view', true );
			if ( empty( $single_view ) ) {
				return true;
			}

			return apply_filters( 'woocommerce_bv_render_single_form', false );
		}

		public function on_template_redirect() {
			if ( $this->is_bulk_variation_form() ) {
				$this->include_bulk_form_assets();
			}
		}

		public function include_bulk_form_assets() {
			if ( $this->is_bulk_variation_form() ) {
				//Enqueue scripts and styles for bulk variations
				wp_enqueue_style( 'bulk-variations', $this->plugin_url() . '/assets/css/bulk-variations.css', null, $this->assets_version );
				wp_enqueue_script( 'jquery-validate', $this->plugin_url() . '/assets/js/jquery.validate.js', array( 'jquery' ), $this->assets_version );
				wp_enqueue_script( 'bulk-variations', $this->plugin_url() . '/assets/js/bulk-variations.js', array(
					'jquery',
					'jquery-validate'
				), $this->assets_version );

				$cur_single_view_show_by_default = wc_bv_get_post_meta( get_the_ID(), '_bv_single_view_show_by_default', true );
				$bv_params = array(
					'show_single_view_by_default' => $cur_single_view_show_by_default
				);

				wp_localize_script('bulk-variations', '_bv_params', $bv_params);

			}
		}

		public function include_quickview_bulk_form_assets() {
			if ( ! is_product() && ! is_single() ) {
				//Enqueue scripts and styles for bulk variations
				wp_enqueue_style( 'bulk-variations', $this->plugin_url() . '/assets/css/bulk-variations.css', null, $this->assets_version );
				wp_enqueue_script( 'jquery-validate', $this->plugin_url() . '/assets/js/jquery.validate.js', array( 'jquery' ), $this->assets_version );
				wp_enqueue_script( 'bulk-variations', $this->plugin_url() . '/assets/js/bulk-variations.js', array(
					'jquery',
					'jquery-validate'
				), $this->assets_version );

				$cur_single_view_show_by_default = wc_bv_get_post_meta( get_the_ID(), '_bv_single_view_show_by_default', true );
				$bv_params = array(
					'show_single_view_by_default' => $cur_single_view_show_by_default
				);

				wp_localize_script('bulk-variations', '_bv_params', $bv_params);
			}
		}

		public function render_bulk_form() {
			if ( $this->is_bulk_variation_form() || $this->is_quick_view ) {
				wc_get_template( 'variable-grid.php', array(), WC_TEMPLATE_PATH . '/single-product/', $this->plugin_path() . '/templates/single-product/' );
			}
		}

		public function before_add_to_cart_form() {
			wc_get_template( 'bulk-variable-buttons.php', array(), WC_TEMPLATE_PATH . '/single-product/', $this->plugin_path() . '/templates/single-product/' );
		}

		/**
		 * Renders product addons below the matrix table.
		 */
		public function on_woocommerce_bv_before_add_to_cart_button(){
		    if (defined('WC_PRODUCT_ADDONS_VERSION') && class_exists('WC_Product_Addons')) {

                global $Product_Addon_Display;

                add_filter('woocommerce_product_addons_show_grand_total', '__return_false', 9999);

                $before_html = '<h4>' . apply_filters('woocommerce_bv_before_product_addons_text', __('Additional Options', 'woocommerce-bulk-variations')) . '</h4>';
                echo apply_filters('woocommerce_bv_before_product_addons_html', $before_html);

                $Product_Addon_Display->display();
                remove_filter('woocommerce_product_addons_show_grand_total', '__return_false', 9999);
            }
        }




		//Helper functions
		/**
		 * Get the plugin url
		 */
		function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
		}

		/**
		 * Get the plugin path
		 */
		function plugin_path() {
			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		function get_setting( $key, $default = null ) {
			return get_option( $key, $default );
		}

		/**
		 * Ajax URL
		 */
		function ajax_url() {
			$url = admin_url( 'admin-ajax.php' );

			$url = ( is_ssl() ) ? $url : str_replace( 'https', 'http', $url );

			return $url;
		}

		//Add to cart handling
		public function process_matrix_submission() {
			global $woocommerce;

			$items          = $_POST['order_info'];
			$product_id     = $_POST['product_id'];
			$adding_to_cart = wc_get_product( $product_id );

			$added_count  = 0;
			$failed_count = 0;

			$success_message = '';
			$error_message   = '';

			foreach ( $items as $item ) {
				$q = floatval( $item['quantity'] ) ? floatval( $item['quantity'] ) : 0;
				if ( $q ) {

					$variation_id       = empty( $item['variation_id'] ) ? '' : absint( $item['variation_id'] );
					$missing_attributes = array(); //For validation, since 2.4
					$variations         = array();

					// Only allow integer variation ID - if its not set, redirect to the product page
					if ( empty( $variation_id ) ) {
						//wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
						$failed_count ++;
						continue;
					}

					$variation_data = wc_get_product_variation_attributes( $variation_id );
					$attributes     = $adding_to_cart->get_attributes();

					// Verify all attributes
					foreach ( $attributes as $attribute ) {
						if ( ! $attribute['is_variation'] ) {
							continue;
						}

						$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );


						if ( isset( $item['variation_data'][ $taxonomy ] ) ) {

							// Get value from post data
							if ( $attribute['is_taxonomy'] ) {
								// Don't use wc_clean as it destroys sanitized characters
								$value = sanitize_title( stripslashes( $item['variation_data'][ $taxonomy ] ) );
							} else {
								$value = wc_clean( stripslashes( $item['variation_data'][ $taxonomy ] ) );
							}

							// Get valid value from variation
							$valid_value = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';

							// Allow if valid
							if ( '' === $valid_value || $valid_value === $value ) {
								$variations[ $taxonomy ] = $value;
								continue;
							}
						} else {
							$missing_attributes[] = wc_attribute_label( $attribute['name'] );
						}
					}

					if ( empty( $missing_attributes ) ) {
						// Add to cart validation
						$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $q, $variation_id, $variations );

						if ( $passed_validation ) {
							$added = WC()->cart->add_to_cart( $product_id, $q, $variation_id, $variations );
						}
					} else {
						$failed_count ++;
						continue;
					}

					if ( $added ) {
						$added_count ++;
					} else {
						$failed_count ++;
					}
				}
			}

			if ( $added_count ) {
				woocommerce_bulk_variations_add_to_cart_message( $added_count );
			}

			if ( $failed_count ) {
				WC_Bulk_Variations_Compatibility::wc_add_error( sprintf( __( 'Unable to add %s to the cart.  Please check your quantities and make sure the item is available and in stock', 'woocommerce-bulk-variations' ), $failed_count ) );
			}

			if ( ! $added_count && ! $failed_count ) {
				WC_Bulk_Variations_Compatibility::wc_add_error( __( 'No product quantities entered.', 'woocommerce-bulk-variations' ) );
			}

			// If we added the products to the cart we can now do a redirect, otherwise just continue loading the page to show errors
			if ( $failed_count === 0 && wc_notice_count( 'error' ) === 0 ) {

				// If has custom URL redirect there
				if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false, $adding_to_cart ) ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
					wp_safe_redirect( wc_get_cart_url() );
					exit;
				}

			}
		}

	}

    WC_Bulk_Variations::register();
	$GLOBALS['wc_bulk_variations'] = WC_Bulk_Variations::instance();



}
