<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WC_Catalog_Mode' ) ) {

	/**
	 * Implements features of YITH WooCommerce Catalog Mode plugin
	 *
	 * @class   YITH_WC_Catalog_Mode
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WC_Catalog_Mode {

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-catalog-mode/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-catalog-mode/';

		/**
		 * @var string Yith WooCommerce Catalog Mode panel page
		 */
		protected $_panel_page = 'yith_wc_catalog_mode_panel';

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Catalog_Mode
		 * @since 1.3.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Catalog_Mode
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YWCTM_DIR . '/' . basename( YWCTM_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );

			//  Add stylesheets and scripts files
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );
			add_action( 'yith_catalog_mode_premium', array( $this, 'premium_tab' ) );

			if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() ) {

				if ( ! is_admin() || $this->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

					if ( $this->disable_shop() ) {

						$priority = has_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ) );
						remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), $priority );

					}

					add_action( 'wp', array( $this, 'check_pages_redirect' ) );
					add_action( 'get_pages', array( $this, 'hide_cart_checkout_pages' ) );
					//add_action( 'woocommerce_single_product_summary', array( $this, 'hide_add_to_cart_single' ), 10 );

					add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'hide_add_to_cart_loop' ), 5 );
					add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'avoid_add_to_cart' ), 10, 2 );

					add_action( 'wp_head', array( $this, 'add_ywctm_styles' ) );
					add_filter( 'ywctm_css_classes', array( $this, 'hide_atc_single_page' ) );
					add_filter( 'ywctm_css_classes', array( $this, 'hide_cart_widget' ) );

					if ( defined( 'YITH_WCWL' ) && YITH_WCWL ) {
						add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_wishlist' ), 10, 2 );
					}

					// remove discount table from product (YITH WooCommerce Dynamic Discount Product)
					add_filter( 'ywdpd_exclude_products_from_discount', array( $this, 'hide_discount_quantity_table' ), 10, 2 );
				}

			}

		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array();

			if ( defined( 'YWCTM_PREMIUM' ) ) {
				$admin_tabs['premium-settings'] = __( 'Settings', 'yith-woocommerce-catalog-mode' );
				$admin_tabs['exclusions']       = __( 'Exclusion List', 'yith-woocommerce-catalog-mode' );
				$admin_tabs['alternative-text'] = __( 'Texts replacing price', 'yith-woocommerce-catalog-mode' );
				$admin_tabs['custom-url']       = __( 'Custom Button Url List', 'yith-woocommerce-catalog-mode' );

				if ( $this->is_multivendor_active() ) {
					$admin_tabs['vendors'] = __( 'Vendor Exclusion List', 'yith-woocommerce-catalog-mode' );
				}

			} else {
				$admin_tabs['settings']        = __( 'Settings', 'yith-woocommerce-catalog-mode' );
				$admin_tabs['premium-landing'] = __( 'Premium Version', 'yith-woocommerce-catalog-mode' );
			}


			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => __( 'Catalog Mode', 'yith-woocommerce-catalog-mode' ),
				'menu_title'       => 'Catalog Mode',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWCTM_DIR . '/plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Disable Shop
		 *
		 * @since   1.0.0
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function disable_shop() {

			$disabled = false;

			if ( get_option( 'ywctm_hide_cart_header' ) == 'yes' ) {

				global $post;

				$post_id = isset( $post ) ? $post->ID : '';

				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;

				if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
					$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
				}

				$disabled = $this->apply_catalog_mode( $post_id );

			}

			return $disabled;

		}

		/**
		 * Adds Catalog Mode styles
		 *
		 * @since   1.4.4
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_ywctm_styles() {

			$classes = apply_filters( 'ywctm_css_classes', array() );

			if ( $classes ) {

				ob_start();

				?>
                <style type="text/css">

                    <?php echo implode( ', ', $classes ); ?>
                    {
                        display: none !important
                    }

                </style>

				<?php

				echo ob_get_clean();
			}

		}

		/**
		 * Check if Catalog mode must be applied to current user
		 *
		 * @since   1.3.0
		 *
		 * @param   $post_id
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function apply_catalog_mode( $post_id ) {

			$target_users = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price_users', 'all' ), $post_id, 'ywctm_hide_price_users' );

			if ( $target_users == 'country' && defined( 'YWCTM_PREMIUM' ) ) {

				return $this->country_check( $post_id );

			} elseif ( $target_users == 'all' ) {

				return apply_filters( 'ywctm_applied_roles', true );

			} else {

				return apply_filters( 'ywctm_applied_roles', ! is_user_logged_in() );

			}

		}

		/**
		 * Check if catalog mode is enabled for administrator
		 *
		 * @since   1.0.2
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_user_admin_enable() {

			return ! ( current_user_can( 'administrator' ) && is_user_logged_in() && get_option( 'ywctm_admin_view' ) == 'no' );

		}

		/**
		 * Checks if "Cart & Checkout pages" needs to be hidden
		 *
		 * @since   1.0.2
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_hide_cart_checkout_pages() {

			return get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() && $this->disable_shop();

		}

		/**
		 * Hides "Add to cart" button from single product page
		 *
		 * @since   1.4.4
		 *
		 * @param   $classes
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function hide_atc_single_page( $classes ) {

			if ( $this->check_add_to_cart_single( true ) && is_singular() ) {

				$hide_variations = get_option( 'ywctm_hide_variations' );

				$args = array(
					'form.cart button.single_add_to_cart_button'
				);

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) || ( ( class_exists( 'YITH_Request_Quote_Premium' ) ) && ! YITH_Request_Quote_Premium()->check_user_type() ) ) {

					$args[] = 'form.cart .quantity';

				}

				if ( $hide_variations == 'yes' ) {

					$args[] = 'table.variations';
					$args[] = 'form.variations_form';
					$args[] = '.single_variation_wrap .variations_button';

				}

				$classes = array_merge( $classes, apply_filters( 'ywctm_catalog_classes', $args ) );

			}

			return $classes;


		}

		/**
		 * Checks if "Add to cart" needs to be hidden
		 *
		 * @since   1.0.2
		 *
		 * @param   $priority
		 * @param   $product_id
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_add_to_cart_single( $priority = true, $product_id = false ) {

			$hide = false;

			if ( apply_filters( 'ywctm_prices_only_on_cart', false ) ) {
				return $hide;
			}

			if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() ) {

				if ( $this->disable_shop() ) {

					$hide = true;

				} else {

					global $post;

					if ( ! $product_id && ! isset( $post ) ) {
						return false;
					}

					$post_id = ( $product_id ) ? $product_id : $post->ID;

					global $sitepress;
					$has_wpml = ! empty( $sitepress ) ? true : false;

					if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
						$post_id = yit_wpml_object_id( $post->ID, 'product', true, wpml_get_default_language() );
					}

					$product = wc_get_product( $post_id );

					if ( ! $product ) {
						return false;
					}

					$hide_add_to_cart_single = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_single' ), $post_id, 'ywctm_hide_add_to_cart_single' );

					if ( $hide_add_to_cart_single != 'yes' ) {
						$hide_add_to_cart_single = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price' ), $post_id, 'ywctm_hide_price' );
					}

					if ( $hide_add_to_cart_single == 'yes' ) {

						if ( $this->apply_catalog_mode( $post_id ) ) {

							$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart' ), $post_id, 'ywctm_exclude_hide_add_to_cart' );
							$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', $product->get_meta( '_ywctm_exclude_catalog_mode' ), $post_id, '_ywctm_exclude_catalog_mode' );

							$hide = ( $enable_exclusion != 'yes' ? true : ( $exclude_catalog != 'yes' ? true : false ) );

							$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ), $post_id, 'ywctm_exclude_hide_add_to_cart_reverse' );

							if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

								$hide = ! $hide;

							}

						}

					}

					if ( apply_filters( 'ywctm_check_price_hidden', false, $post_id ) ) {

						$hide = true;

					}

					$hide = apply_filters( 'ywctm_hide_on_single_anyway', $hide, $post_id );

				}

			}

			return $hide;

		}

		/**
		 * Checks if "Add to cart" needs to be avoided
		 *
		 * @since   1.0.5
		 *
		 * @param   $passed
		 * @param   $product_id
		 *
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function avoid_add_to_cart( $passed, $product_id ) {

			if ( apply_filters( 'ywctm_prices_only_on_cart', false ) ) {
				return $passed;
			}

			if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && $this->check_user_admin_enable() ) {

				if ( $this->disable_shop() ) {

					$passed = false;

				} else {

					global $sitepress;
					$has_wpml = ! empty( $sitepress ) ? true : false;

					if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
						$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
					}

					$product = wc_get_product( $product_id );

					if ( ! $product ) {
						return true;
					}

					$hide_add_to_cart_single = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_single' ), $product_id, 'ywctm_hide_add_to_cart_single' );

					if ( $hide_add_to_cart_single == 'yes' ) {

						if ( $this->apply_catalog_mode( $product_id ) ) {

							$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart' ), $product_id, 'ywctm_exclude_hide_add_to_cart' );
							$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', $product->get_meta( '_ywctm_exclude_catalog_mode' ), $product_id, '_ywctm_exclude_catalog_mode' );

							$passed = ( $enable_exclusion != 'yes' ? false : ( $exclude_catalog != 'yes' ? false : true ) );

							$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ), $product_id, 'ywctm_exclude_hide_add_to_cart_reverse' );

							if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

								$passed = ! $passed;

							}

						}

					}

					if ( apply_filters( 'ywctm_check_price_hidden', false, $product_id ) ) {

						$passed = false;

					}

					if ( apply_filters( 'ywctm_hide_on_single_anyway', false, $product_id ) && apply_filters( 'ywctm_hide_on_loop_anyway', false, $product_id ) ) {

						$passed = false;

					}

				}

			}

			return $passed;
		}

		/**
		 * Checks if "Add to cart" needs to be hidden from loop page
		 *
		 * @since   1.0.6
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function check_hide_add_cart_loop() {

			$hide = false;

			if ( apply_filters( 'ywctm_prices_only_on_cart', false ) ) {
				return $hide;
			}

			if ( $this->disable_shop() ) {

				$hide = true;

			} else {

				global $product;

				if ( ! $product ) {
					return $hide;
				}

				$product_id = $product->get_id();

				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;

				if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
					$product    = wc_get_product( $product_id );
				}

				if ( ! $product ) {
					return $hide;
				}

				$hide_add_to_cart_loop = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_loop' ), $product_id, 'ywctm_hide_add_to_cart_loop' );
				$hide_variations       = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_variations' ), $product_id, 'ywctm_hide_variations' );
				$hide_variations       = apply_filters( 'ywctm_hide_variations_on_loop', $hide_variations );

				$is_variable = $product->is_type( 'variable' );
				$is_grouped  = $product->is_type( 'grouped' );


				$can_hide = ( ( $is_variable || $is_grouped ) ? $hide_variations == 'yes' : true );

				if ( $hide_add_to_cart_loop != 'yes' ) {
					$hide_add_to_cart_loop = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price' ), $product_id, 'ywctm_hide_price' );
				}

				if ( $hide_add_to_cart_loop == 'yes' ) {

					if ( $this->apply_catalog_mode( $product_id ) ) {

						$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart' ), $product_id, 'ywctm_exclude_hide_add_to_cart' );
						$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', $product->get_meta( '_ywctm_exclude_catalog_mode' ), $product_id, '_ywctm_exclude_catalog_mode' );

						$hide = ( $enable_exclusion != 'yes' ? true : ( $exclude_catalog != 'yes' ? true : false ) );

						$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ), $product_id, 'ywctm_exclude_hide_add_to_cart_reverse' );

						if ( ( $is_variable || $is_grouped ) && $enable_exclusion != 'yes' ) {

							$hide = $can_hide;

						}

						if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

							$hide = ! $hide;

							if ( ( $is_variable || $is_grouped ) && ! $can_hide ) {

								$hide = false;

							}

						}


					}

				}

				if ( apply_filters( 'ywctm_check_price_hidden', false, $product_id ) && $can_hide ) {

					$hide = true;

				}

				$hide = apply_filters( 'ywctm_hide_on_loop_anyway', $hide, $product_id );

			}

			return $hide;

		}

		/**
		 * Hides "Add to cart" button, if not excluded, from loop page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_loop() {

			$ywctm_modify_woocommerce_after_shop_loop_item = apply_filters( 'ywctm_modify_woocommerce_after_shop_loop_item', true );

			if ( $this->check_hide_add_cart_loop() ) {

				if ( $ywctm_modify_woocommerce_after_shop_loop_item ) {
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}
				add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

			} else {

				if ( $ywctm_modify_woocommerce_after_shop_loop_item ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}

				remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

			}

		}

		/**
		 * Hide cart widget if needed
		 *
		 * @since   1.3.7
		 *
		 * @param   $classes
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function hide_cart_widget( $classes ) {

			if ( $this->disable_shop() ) {

				$args = array(
					'.widget.woocommerce.widget_shopping_cart'
				);

				$wp_theme   = wp_get_theme();
				$theme_name = is_child_theme() ? $wp_theme->get_template() : strtolower( $wp_theme->Name );

				if ( 'storefront' == $theme_name ) {

					$args[] = '.site-header-cart.menu';

				}

				$classes = array_merge( $classes, apply_filters( 'ywctm_cart_widget_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Avoid Cart and Checkout Pages to be visited
		 *
		 * @since   1.0.4
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function check_pages_redirect() {

			if ( $this->disable_shop() ) {

				$cart     = is_page( wc_get_page_id( 'cart' ) );
				$checkout = is_page( wc_get_page_id( 'checkout' ) );

				wp_reset_query();

				if ( $cart || $checkout ) {

					wp_redirect( home_url() );
					exit;

				}

			}

		}

		/**
		 * Removes Cart and checkout pages from menu
		 *
		 * @since   1.0.4
		 *
		 * @param   $pages
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function hide_cart_checkout_pages( $pages ) {

			if ( $this->disable_shop() ) {

				$excluded_pages = array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' )
				);

				for ( $i = 0; $i < count( $pages ); $i ++ ) {
					$page = &$pages[ $i ];

					if ( in_array( $page->ID, $excluded_pages ) ) {

						unset( $pages[ $i ] );

					}

				}

			}

			return $pages;

		}

		/**
		 * Say if the code is execute by quick view
		 *
		 * @since    1.0.7
		 * @return   bool
		 * @author   Andrea Frascaspata <andrea.frascaspata@yithemes.com>
		 */
		public function is_quick_view() {

			$actions = apply_filters( 'ywctm_quick_view_actions', array( 'yith_load_product_quick_view', 'yit_load_product_quick_view' ) );

			return defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions );
		}

		/**
		 * Hides add to cart on wishlist
		 *
		 * @since   1.2.2
		 *
		 * @param   $value
		 * @param   $product WC_Product
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_wishlist( $value, $product ) {

			global $yith_wcwl_is_wishlist;

			if ( $this->check_add_to_cart_single( true, $product->get_id() ) && $yith_wcwl_is_wishlist ) {

				$value = '';

			}

			return $value;

		}

        /**
         * Hide discount quantity table from YITH WooCommerce Dynamic Pricing Discount id the catalog mode is active
         *
         * @since 1.6.18
         * @author Francesco Licandro
         * @param boolean $value
         * @param WC_Product $product
         * @return boolean
         */
        public function hide_discount_quantity_table( $value, $product ) {
            return $product && $this->check_add_to_cart_single( true, $product->get_id() );
        }

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Load plugin framework
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
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
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YWCTM_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @return  string The premium landing link
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_premium_landing_uri() {
			return $this->_premium_landing;
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 * @since   1.0.0
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, false );

			return $links;

		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @since   1.0.0
		 *
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCTM_FREE_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug'] = YWCTM_SLUG;
			}

			return $new_row_meta_args;

		}

		/**
		 * DEPRECATED FUNCTIONS
		 */

		/**
		 * Hides "Add to cart" button from single product page
		 *
		 * @since   1.0.0
		 *
		 * @param   $action
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_single( $action = '' ) {

			/*if ( $action == '' ) {
				$action = 'woocommerce_single_product_summary';
			}

			$priority = has_action( $action, 'woocommerce_template_single_add_to_cart' );

			if ( $this->check_add_to_cart_single( $priority ) ) {


			}*/
			return;

		}

		/**
		 * Hide add to cart button in quick view
		 *
		 * @since   1.0.7
		 * @return  void
		 * @author  Francesco Licandro
		 */
		public function hide_add_to_cart_quick_view() {

			if ( $this->check_add_to_cart_single( true ) ) {

				$hide_variations = get_option( 'ywctm_hide_variations' );
				ob_start();

				$args = array(
					'form.cart button.single_add_to_cart_button'
				);

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) || ( ( class_exists( 'YITH_Request_Quote_Premium' ) ) && ! YITH_Request_Quote_Premium()->check_user_type() ) ) {

					$args[] = 'form.cart .quantity';

				}

				if ( $hide_variations == 'yes' ) {

					$args[] = 'table.variations';
					$args[] = 'form.variations_form';
					$args[] = '.single_variation_wrap .variations_button';

				}

				$classes = implode( ', ', apply_filters( 'ywctm_catalog_classes', $args ) );

				?>
                <style>

                    <?php echo $classes; ?>
                    {
                        display: none !important
                    }

                </style>
				<?php
				echo ob_get_clean();
			}

		}

	}

}