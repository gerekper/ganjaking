<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YITH_WC_Product_Countdown
 * @since   1.0.0
 * @author  Your Inspiration Themes
 * @package Yithemes
 */

if ( ! class_exists( 'YITH_WC_Product_Countdown' ) ) {

	class YITH_WC_Product_Countdown {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YITH_WC_Product_Countdown
		 */
		protected static $instance;

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_panel = null;

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-product-countdown/';

		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-product-countdown/';

		/**
		 * @var string Yith WooCommerce Catalog Mode panel page
		 */
		protected $_panel_page = 'yith-wc-product-countdown';

		/**
		 * @var string id for Product Sales Countdown tab in product edit page
		 */
		var $_product_tab = 'product_countdown';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Product_Countdown
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 12 );
			add_filter( 'plugin_action_links_' . plugin_basename( YWPC_DIR . '/' . basename( YWPC_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'init', array( $this, 'set_plugin_requirements' ), 20 );
			add_action( 'admin_menu', array( $this, 'add_menu_page' ), 5 );

			// Include required files
			$this->includes();

			if ( is_admin() ) {

				add_action( 'woocommerce_admin_field_custom-radio', 'YWPC_Custom_Radio::output' );
				add_action( 'woocommerce_admin_field_custom-radio-topbar', 'YWPC_Custom_Radio_Topbar::output' );
				add_action( 'woocommerce_admin_field_custom-selector', 'YWPC_Custom_Select::output' );
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
				add_action( 'ywpc_bulk_operations', 'YWPC_Bulk_Operations::output' );
				add_action( 'wp_ajax_ywpc_json_search_product_categories', 'YWPC_Bulk_Operations::json_search_product_categories', 10 );


			}

			if ( get_option( 'ywpc_enable_plugin' ) == 'yes' ) {

				add_action( 'widgets_init', array( $this, 'register_widget' ) );
				add_action( 'init', array( $this, 'initialize_styles' ) );


				if ( is_admin() ) {

					add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'add_countdown_tab' ), 98 );
					add_action( 'woocommerce_product_data_panels', array( $this, 'write_tab_options' ) );
					add_action( 'woocommerce_process_product_meta', array( $this, 'save_countdown_tab' ), 10 );
					add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_sale_options_product_variable' ), 10, 3 );
					add_action( 'woocommerce_save_product_variation', array( $this, 'save_sale_options_product_variable' ), 10, 2 );
					add_action( 'manage_product_posts_custom_column', array( $this, 'render_ywpc_column' ), 3 );
					add_filter( 'manage_product_posts_columns', array( $this, 'add_ywpc_column' ), 11 );

				} else {

					add_action( 'woocommerce_before_single_product', array( $this, 'check_show_ywpc_product' ), 5 );
					add_action( 'woocommerce_before_shop_loop_item', array( $this, 'check_show_ywpc_category' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
					add_filter( 'ywpc_timer_title', array( $this, 'get_timer_title' ), 10, 2 );
					add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'correct_quantity_product' ) );
					add_action( 'woocommerce_order_status_on-hold', array( $this, 'update_quantity_product_sold' ), 10, 2 );
					add_action( 'woocommerce_order_status_processing', array( $this, 'update_quantity_product_sold' ), 10, 2 );
					add_action( 'woocommerce_order_status_completed', array( $this, 'update_quantity_product_sold' ), 10, 2 );

					$end_sale = get_option( 'ywpc_end_sale' );

					if ( $end_sale == 'disable' ) {

						/** If on expired sale the product must be disabled */
						add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'avoid_add_to_cart' ), 10, 2 );

						add_action( 'wp_enqueue_scripts', array( $this, 'hide_add_to_cart_single' ), 15 );
						add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'hide_add_to_cart_loop' ), 5 );

					} elseif ( $end_sale == 'remove' ) {

						/** If on expired sale the product must be hidden */
						add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'avoid_add_to_cart' ), 10, 2 );
						add_filter( 'woocommerce_variation_is_visible', array( $this, 'hide_variations' ), 10, 2 );

						add_action( 'woocommerce_product_query', array( $this, 'hide_product_from_catalog' ), 10, 1 );
						add_action( 'woocommerce_shortcode_products_query', array( $this, 'hide_product_from_shortcodes' ), 10, 1 );
						add_filter( 'woocommerce_related_products', array( $this, 'hide_from_related_products' ), 10 );
						add_action( 'template_redirect', array( $this, 'avoid_direct_access' ) );

					}

					add_action( 'init', array( $this, 'initialize_topbar' ) );

				}

			}

		}

		/**
		 * Include required core files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function includes() {

			include_once( 'includes/functions-ywpc-countdown.php' );
			include_once( 'includes/class-ywpc-shortcode.php' );
			include_once( 'includes/class-ywpc-widget.php' );

			if ( is_admin() ) {

				include_once( 'templates/admin/custom-select.php' );
				include_once( 'templates/admin/custom-radio.php' );
				include_once( 'templates/admin/custom-radio-topbar.php' );
				include_once( 'templates/admin/bulk-operations.php' );

			}

		}

		/**
		 * Register YWPC Widget
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function register_widget() {
			register_widget( 'YWPC_Widget' );
		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 * @use     /Yit_Plugin_Panel class
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_menu_page() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array();

			$admin_tabs['general'] = esc_html__( 'General', 'yith-woocommerce-product-countdown' );
			$admin_tabs['style']   = esc_html__( 'Customization', 'yith-woocommerce-product-countdown' );
			$admin_tabs['bulk']    = esc_html__( 'Bulk Operations', 'yith-woocommerce-product-countdown' );
			$admin_tabs['topbar']  = esc_html__( 'Top/Bottom Countdown bar', 'yith-woocommerce-product-countdown' );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => esc_html__( 'Product Countdown', 'yith-woocommerce-product-countdown' ),
				'menu_title'       => 'Product Countdown',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YWPC_DIR . 'plugin-options'
			);

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Register script e style files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function initialize_styles() {
			$template = get_option( 'ywpc_template', '1' );
			wp_register_style( 'ywpc-frontend', YWPC_ASSETS_URL . '/css/ywpc-style-' . $template . '.css' );

			$inline_css = '';

			if ( get_option( 'ywpc_timer_title' ) == '' ) {

				$inline_css .= '
				.ywpc-countdown > .ywpc-header,
				.ywpc-countdown-loop > .ywpc-header {
					display: none;
				}';

			}

			if ( get_option( 'ywpc_sale_bar_title' ) == '' ) {

				$inline_css .= '
				.ywpc-sale-bar > .ywpc-header,
				.ywpc-sale-bar-loop > .ywpc-header {
					display: none;
				}';

			}

			if ( get_option( 'ywpc_appearance' ) == 'cust' ) {

				$text_font_size       = get_option( 'ywpc_text_font_size', 25 );
				$text_font_size_loop  = get_option( 'ywpc_text_font_size_loop', 15 );
				$timer_font_size      = get_option( 'ywpc_timer_font_size', 28 );
				$timer_font_size_loop = get_option( 'ywpc_timer_font_size_loop', 15 );
				$text_color           = get_option( 'ywpc_text_color', '#a12418' );
				$border_color         = get_option( 'ywpc_border_color', '#dbd8d8' );
				$back_color           = get_option( 'ywpc_back_color', '#fafafa' );
				$timer_fore_color     = get_option( 'ywpc_timer_fore_color', '#3c3c3c' );
				$timer_back_color     = get_option( 'ywpc_timer_back_color', '#ffffff' );
				$bar_fore_color       = get_option( 'ywpc_bar_fore_color', '#a12418' );
				$bar_back_color       = get_option( 'ywpc_bar_back_color', '#e6e6e6' );

				$inline_css .= '
				.ywpc-countdown,
				.ywpc-sale-bar {
					background: ' . $back_color . ';
					border: 1px solid ' . $border_color . ';
				}

				.ywpc-countdown > .ywpc-header,
				.ywpc-sale-bar > .ywpc-header {
					color: ' . $text_color . ';
					font-size: ' . $text_font_size . 'px;
				}

				.ywpc-countdown-loop > .ywpc-header,
				.ywpc-sale-bar-loop > .ywpc-header {
					color: ' . $text_color . ';
					font-size: ' . $text_font_size_loop . 'px;
				}';

				if ( get_option( 'ywpc_template', '1' ) == '1' ) {

					$inline_css .= '
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $timer_back_color . ';
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size . 'px;
					}
		
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $timer_back_color . ';
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size_loop . 'px;
					}';

				} else {

					$inline_css .= '
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount,
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount {
						background: ' . $timer_back_color . ';
					}
		
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size . 'px;
					}
		
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size_loop . 'px;
					}';

				}

				$inline_css .= '
				.ywpc-sale-bar > .ywpc-bar > .ywpc-back,
				.ywpc-sale-bar-loop > .ywpc-bar > .ywpc-back {
					background: ' . $bar_back_color . ';
				}
	
				.ywpc-sale-bar > .ywpc-bar > .ywpc-back > .ywpc-fore,
				.ywpc-sale-bar-loop > .ywpc-bar > .ywpc-back > .ywpc-fore {
					background: ' . $bar_fore_color . ';
				}';

			}

			if ( get_option( 'ywpc_topbar_appearance' ) == 'cust' && get_option( 'ywpc_topbar_enable' ) == 'yes' ) {

				$topbar_text_font_size   = get_option( 'ywpc_topbar_text_font_size', 30 );
				$topbar_timer_font_size  = get_option( 'ywpc_topbar_timer_font_size', 18 );
				$topbar_text_color       = get_option( 'ywpc_topbar_text_color', '#a12418' );
				$topbar_label_color      = get_option( 'ywpc_topbar_text_label_color', '#232323' );
				$topbar_back_color       = get_option( 'ywpc_topbar_back_color', '#ffba00' );
				$topbar_timer_text_color = get_option( 'ywpc_topbar_timer_text_color', '#363636' );
				$topbar_timer_back_color = get_option( 'ywpc_topbar_timer_back_color', '#ffffff' );
				$topbar_border_color     = get_option( 'ywpc_topbar_timer_border_color', '#ff8a00' );

				$inline_css .= '
				.ywpc-countdown-topbar {
					background: ' . $topbar_back_color . ';
				}

				.ywpc-countdown-topbar > .ywpc-header {
					color: ' . $topbar_text_color . ';
					font-size: ' . $topbar_text_font_size . 'px;
				}

				.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-label {
					color: ' . $topbar_label_color . ';
				}';

				if ( get_option( 'ywpc_topbar_template', '2' ) == '2' ) {

					$inline_css .= '
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $topbar_timer_back_color . ';
						color: ' . $topbar_timer_text_color . ';
						font-size: ' . $topbar_timer_font_size . 'px;
					}
					
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount {
						background: ' . $topbar_border_color . ';
					}';

				} else {

					$inline_css .= '
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount {
						background: ' . $topbar_timer_back_color . ';
						border: 1px solid ' . $topbar_border_color . ';
					}
					
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $topbar_timer_text_color . ';
						font-size: ' . $topbar_timer_font_size . ' px;
					}';

				}

			}

			if ( $inline_css ) {

				wp_add_inline_style( 'ywpc-frontend', $inline_css );

			}
		}

		/**
		 * Enqueue admin script files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			$screen = get_current_screen();

			wp_register_style( 'jquery-ui-datetimepicker-style', YWPC_ASSETS_URL . '/css/timepicker.css' );
			wp_register_style( 'ywpc-admin', YWPC_ASSETS_URL . '/css/ywpc-admin' . ywpc_get_minified() . '.css' );


			wp_register_script( 'jquery-ui-datetimepicker', YWPC_ASSETS_URL . '/js/timepicker' . ywpc_get_minified() . '.js', array( 'jquery', 'jquery-ui-datepicker' ), YWPC_VERSION, true );
			wp_register_script( 'jquery-plugin', YWPC_ASSETS_URL . '/js/jquery.plugin' . ywpc_get_minified() . '.js', array( 'jquery' ) );
			wp_register_script( 'jquery-countdown', YWPC_ASSETS_URL . '/js/jquery.countdown' . ywpc_get_minified() . '.js', array( 'jquery' ), '2.1.0' );
			wp_register_script( 'ywpc-admin', YWPC_ASSETS_URL . '/js/ywpc-admin' . ywpc_get_minified() . '.js', array( 'jquery', 'jquery-ui-datetimepicker' ), YWPC_VERSION, true );


			if ( in_array( $screen->id, array( 'product', 'edit-product' ) ) ) {

				wp_enqueue_style( 'ywpc-admin' );
				wp_enqueue_script( 'jquery-plugin' );
				wp_enqueue_script( 'jquery-countdown' );

			}

			if ( in_array( $screen->id, array( 'product', 'edit-product', 'yith-plugins_page_yith-wc-product-countdown' ) ) ) {

				wp_enqueue_style( 'jquery-ui-datetimepicker-style' );
				wp_enqueue_script( 'jquery-ui-datetimepicker' );
				wp_enqueue_script( 'ywpc-admin' );

				$js_vars = array(
					'gmt'    => get_option( 'gmt_offset' ),
					'is_rtl' => is_rtl(),
				);

				wp_localize_script( 'ywpc-admin', 'ywpc', $js_vars );

			}

			if ( $screen->id == 'yith-plugins_page_yith-wc-product-countdown' ) {

				$template_topbar = get_option( 'ywpc_topbar_template', '1' );

				wp_enqueue_style( 'ywpc-google-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400,700' );
				wp_enqueue_style( 'ywpc-frontend' );
				wp_enqueue_style( 'ywpc-frontend-topbar', YWPC_ASSETS_URL . '/css/ywpc-bar-style-' . $template_topbar . '.css' );
				wp_enqueue_script( 'ywpc-admin-panel-footer', YWPC_ASSETS_URL . '/js/ywpc-admin-panel' . ywpc_get_minified() . '.js', array( 'jquery', 'woocommerce_settings' ), YWPC_VERSION, true );

			}

		}

		/**
		 * Add sales countdown tab in product edit page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_countdown_tab() {

			?>

            <li class="<?php echo $this->_product_tab; ?>_options <?php echo $this->_product_tab; ?>_tab hide_if_grouped hide_if_external">
                <a href="#<?php echo $this->_product_tab; ?>_tab"><span><?php esc_html_e( 'Product Countdown', 'yith-woocommerce-product-countdown' ); ?></span></a>
            </li>

			<?php

		}

		/**
		 * Add sales countdown tab content in product edit page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function write_tab_options() {

			global $post;

			$product = wc_get_product( $post );

			$sale_price_dates_from = ( $date = yit_get_prop( $product, '_ywpc_sale_price_dates_from', true ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '';
			$sale_price_dates_to   = ( $date = yit_get_prop( $product, '_ywpc_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '';

			?>

            <div id="<?php echo $this->_product_tab; ?>_tab" class="panel woocommerce_options_panel">

                <div class="options_group sales_countdown">

					<?php

					woocommerce_wp_checkbox(
						array(
							'id'            => '_ywpc_enabled',
							'wrapper_class' => '',
							'label'         => esc_html__( 'Enable ', 'yith-woocommerce-product-countdown' ),
							'description'   => esc_html__( 'Enable YITH WooCommerce Product Countdown for this product', 'yith-woocommerce-product-countdown' )
						)
					);

					if ( $product->is_type( 'variable' ) ) {

						woocommerce_wp_checkbox(
							array(
								'id'          => '_ywpc_variations_global_countdown',
								'label'       => esc_html__( 'General countdown', 'yith-woocommerce-product-countdown' ),
								'description' => esc_html__( 'Set a general countdown for all the variations rather than for each single variation', 'yith-woocommerce-product-countdown' ),
							)
						);

					}

					?>
                    <p class="form-field ywpc-dates">
                        <label for="_ywpc_sale_price_dates_from"><?php esc_html_e( 'Countdown Dates', 'yith-woocommerce-product-countdown' ) ?></label>
                        <input type="text" autocomplete="off" class="short ywpc_sale_price_dates_from" name="_ywpc_sale_price_dates_from" id="_ywpc_sale_price_dates_from" value="<?php echo esc_attr( $sale_price_dates_from ) ?>" placeholder="<?php esc_html_e( 'From&hellip;', 'yith-woocommerce-product-countdown' ) ?> YYYY-MM-DD hh:mm" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])" />
                        <input type="text" autocomplete="off" class="short ywpc_sale_price_dates_to" name="_ywpc_sale_price_dates_to" id="_ywpc_sale_price_dates_to" value="<?php echo esc_attr( $sale_price_dates_to ) ?>" placeholder="<?php esc_html_e( 'To&hellip;', 'yith-woocommerce-product-countdown' ) ?>  YYYY-MM-DD hh:mm" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])" />
						<?php echo wc_help_tip( esc_html__( 'The sale will end at the beginning of the set date.', 'yith-woocommerce-product-countdown' ) ); ?>
                    </p>
					<?php

					woocommerce_wp_text_input(
						array(
							'id'                => '_ywpc_discount_qty',
							'label'             => esc_html__( 'Discounted products', 'yith-woocommerce-product-countdown' ),
							'placeholder'       => '',
							'desc_tip'          => 'true',
							'description'       => esc_html__( 'The number of discounted products.', 'yith-woocommerce-product-countdown' ),
							'default'           => '0',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0'
							)
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'                => '_ywpc_sold_qty',
							'label'             => esc_html__( 'Already sold products', 'yith-woocommerce-product-countdown' ),
							'placeholder'       => '',
							'desc_tip'          => 'true',
							'description'       => esc_html__( 'The number of already sold products.', 'yith-woocommerce-product-countdown' ),
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 'any',
								'min'  => '0'
							)
						)
					);

					if ( $product->is_type( 'variable' ) ) {

						?>

                        <script type="text/javascript">

                            jQuery(function ($) {

                                $(window).load(function () {

                                    $('#_ywpc_discount_qty').change(function () {

                                        if (!$('#_ywpc_variations_global_countdown').is(':checked')) {

                                            $('.ywpc_discount_qty').val($(this).val());
                                            $('.woocommerce_variation').addClass('variation-needs-update');

                                        }

                                    });

                                    $('#_ywpc_sold_qty').change(function () {

                                        if (!$('#_ywpc_variations_global_countdown').is(':checked')) {

                                            $('.ywpc_sold_qty').val($(this).val());
                                            $('.woocommerce_variation').addClass('variation-needs-update');

                                        }

                                    });


                                });

                            });

                        </script>

						<?php

					}

					?>

                </div>

            </div>

			<?php

		}

		/**
		 * Add sales options to product variable
		 *
		 * @param   $loop
		 * @param   $variation_data
		 * @param   $variation
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_sale_options_product_variable( $loop, $variation_data, $variation ) {

			$variation_object      = wc_get_product( $variation->ID );
			$sale_price_dates_from = ( $date = yit_get_prop( $variation_object, '_ywpc_sale_price_dates_from' ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '';
			$sale_price_dates_to   = ( $date = yit_get_prop( $variation_object, '_ywpc_sale_price_dates_to' ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '';
			$discount_qty          = ( $dq = yit_get_prop( $variation_object, '_ywpc_discount_qty' ) ) ? $dq : '';
			$sold_qty              = ( $sq = yit_get_prop( $variation_object, '_ywpc_sold_qty' ) ) ? $sq : '';

			?>
            <div class="ywpc-dates">
                <p class="form-row form-row-first">
                    <label><?php esc_html_e( 'Countdown start date', 'yith-woocommerce-product-countdown' ); ?></label>
                    <input type="text" autocomplete="off" class="ywpc_sale_price_dates_from ywpc-variation-field" name="_ywpc_sale_price_dates_from_var[<?php echo $loop; ?>]" value="<?php echo $sale_price_dates_from; ?>" placeholder="<?php echo esc_attr_x( 'From&hellip;', 'placeholder', 'yith-woocommerce-product-countdown' ) ?> YYYY-MM-DD hh:mm" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])" />
                </p>

                <p class="form-row form-row-last">
                    <label><?php esc_html_e( 'Countdown end date', 'yith-woocommerce-product-countdown' ); ?></label>
                    <input type="text" autocomplete="off" class="ywpc_sale_price_dates_to ywpc-variation-field" name="_ywpc_sale_price_dates_to_var[<?php echo $loop; ?>]" value="<?php echo $sale_price_dates_to; ?>" placeholder="<?php echo esc_attr_x( 'To&hellip;', 'placeholder', 'yith-woocommerce-product-countdown' ) ?> YYYY-MM-DD hh:mm" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9])" />
                </p>
            </div>
            <div class="ywpc-sales">
                <p class="form-row form-row-first">
                    <label><?php esc_html_e( 'Discounted products', 'yith-woocommerce-product-countdown' ); ?><?php echo wc_help_tip( esc_html__( 'The number of discounted products.', 'yith-woocommerce-product-countdown' ) ); ?></label>
                    <input type="number" class="ywpc_discount_qty ywpc-variation-field" name="_ywpc_discount_qty_var[<?php echo $loop; ?>]" value="<?php echo $discount_qty; ?>" step="any" min="0" />
                </p>

                <p class="form-row form-row-last">
                    <label><?php esc_html_e( 'Already sold products', 'yith-woocommerce-product-countdown' ); ?><?php echo wc_help_tip( esc_html__( 'Already sold products.', 'yith-woocommerce-product-countdown' ) ); ?></label>
                    <input type="number" class="ywpc_sold_qty ywpc-variation-field" name="_ywpc_sold_qty_var[<?php echo $loop; ?>]" value="<?php echo $sold_qty; ?>" step="any" min="0" />
                </p>
            </div>
			<?php

		}

		/**
		 * Save sales options of product variations
		 *
		 * @param   $variation_id
		 * @param   $loop
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_sale_options_product_variable( $variation_id, $loop ) {

			$variation_object    = wc_get_product( $variation_id );
			$is_pre_order        = isset( $_POST['_ywpo_preorder'][ $loop ] ) ? 'yes' : 'no';
			$override_variations = isset( $_POST['_ywpc_variations_global_countdown'] ) ? 'yes' : 'no';

			if ( $is_pre_order == 'yes' ) {
				$override_variations = 'no';
			}

			if ( $override_variations == 'yes' ) {

				$args = array(
					'_ywpc_sold_qty'              => 0,
					'_ywpc_discount_qty'          => 0,
					'_ywpc_sale_price_dates_from' => '',
					'_ywpc_sale_price_dates_to'   => ''
				);

			} else {

				$wc_start_date   = isset( $_POST['_ywpc_sale_price_dates_from_var'][ $loop ] ) ? $_POST['_ywpc_sale_price_dates_from_var'][ $loop ] : '';
				$wc_end_date     = isset( $_POST['_ywpc_sale_price_dates_to_var'][ $loop ] ) ? $_POST['_ywpc_sale_price_dates_to_var'][ $loop ] : '';
				$wc_sale_qty     = isset( $_POST['_ywpc_sold_qty_var'][ $loop ] ) ? $_POST['_ywpc_sold_qty_var'][ $loop ] : 0;
				$wc_discount_qty = isset( $_POST['_ywpc_discount_qty_var'][ $loop ] ) ? $_POST['_ywpc_discount_qty_var'][ $loop ] : 0;
				$wc_stock_qty    = isset( $_POST['variable_stock'][ $loop ] ) ? $_POST['variable_stock'][ $loop ] : 0;
				$wc_manage_stock = isset( $_POST['variable_manage_stock'][ $loop ] ) ? $_POST['variable_manage_stock'][ $loop ] : 'off';

				if ( $wc_manage_stock == 'on' ) {

					switch ( true ) {

						case ( $wc_stock_qty < 1 ):
							$wc_discount_qty = 0;

							break;

						case ( $wc_discount_qty > $wc_stock_qty ):
							$wc_discount_qty = $wc_stock_qty;

							break;

					}

				}

				if ( $wc_end_date && ! $wc_start_date ) {
					$wc_start_date = date( 'Y-m-d' );
				}

				if ( $is_pre_order == 'yes' ) {

					$wc_end_date = isset( $_POST['_ywpo_for_sale_date'][ $loop ] ) ? wc_clean( $_POST['_ywpo_for_sale_date'][ $loop ] ) : '';

					if ( ! empty( $wc_end_date ) ) {

						$wc_end_date   = str_replace( '/', '-', $wc_end_date );
						$wc_end_date   = $wc_end_date . ':00';
						$wc_start_date = date( 'Y-m-d' );

					}

				}

				$args = array(
					'_ywpc_sale_price_dates_from' => $wc_start_date ? strtotime( $wc_start_date ) : '',
					'_ywpc_sale_price_dates_to'   => $wc_end_date ? strtotime( $wc_end_date ) : '',
					'_ywpc_sold_qty'              => $wc_sale_qty,
					'_ywpc_discount_qty'          => $wc_discount_qty,
					'_ywpo_variation'             => $is_pre_order == 'yes' ? 'yes' : 'no',
				);

			}

			yit_save_prop( $variation_object, $args );

		}

		/**
		 * Save sales countdown tab options
		 *
		 * @param   $post_id
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function save_countdown_tab( $post_id ) {

			$product         = wc_get_product( $post_id );
			$ywpc_enabled    = isset( $_POST['_ywpc_enabled'] ) ? 'yes' : 'no';
			$date_from       = isset( $_POST['_ywpc_sale_price_dates_from'] ) ? wc_clean( $_POST['_ywpc_sale_price_dates_from'] ) : '';
			$date_to         = isset( $_POST['_ywpc_sale_price_dates_to'] ) ? wc_clean( $_POST['_ywpc_sale_price_dates_to'] ) : '';
			$wc_sold_qty     = isset( $_POST['_ywpc_sold_qty'] ) ? $_POST['_ywpc_sold_qty'] : 0;
			$wc_stock_qty    = isset( $_POST['_stock'] ) ? $_POST['_stock'] : 0;
			$wc_manage_stock = isset( $_POST['_manage_stock'] ) ? $_POST['_manage_stock'] : 'no';
			$wc_discount_qty = isset( $_POST['_ywpc_discount_qty'] ) ? $_POST['_ywpc_discount_qty'] : 0;

			if ( $date_to && ! $date_from ) {
				$date_from = date( 'Y-m-d' );
			}

			if ( $wc_manage_stock == 'yes' ) {

				switch ( true ) {

					case ( $wc_stock_qty < 1 ):
						$wc_discount_qty = 0;

						break;

					case ( $wc_discount_qty > $wc_stock_qty ):
						$wc_discount_qty = $wc_stock_qty;

						break;

				}

			}
			$args = array(
				'_ywpc_enabled'               => $ywpc_enabled,
				'_ywpc_sale_price_dates_from' => strtotime( $date_from ),
				'_ywpc_sale_price_dates_to'   => strtotime( $date_to ),
				'_ywpc_sold_qty'              => esc_attr( $wc_sold_qty ),
				'_ywpc_discount_qty'          => esc_attr( $wc_discount_qty ),
			);

			$is_pre_order = isset( $_POST['_ywpo_preorder'] ) && ! is_array( $_POST['_ywpo_preorder'] ) ? 'yes' : 'no';

			if ( $is_pre_order == 'yes' ) {

				$new_sale_date = isset( $_POST['_ywpo_for_sale_date'] ) ? wc_clean( $_POST['_ywpo_for_sale_date'] ) : '';

				if ( ! empty( $new_sale_date ) ) {

					$new_sale_date                       = str_replace( '/', '-', $new_sale_date );
					$new_sale_date                       = $new_sale_date . ':00';
					$args['_ywpc_sale_price_dates_from'] = strtotime( date( 'Y-m-d' ) );
					$args['_ywpc_sale_price_dates_to']   = strtotime( $new_sale_date );
					$args['_ywpc_enabled']               = 'yes';

				}

			}

			if ( $product->is_type( 'variable' ) ) {

				$override_variations                       = isset( $_POST['_ywpc_variations_global_countdown'] ) ? 'yes' : 'no';
				$args['_ywpc_variations_global_countdown'] = $override_variations;

				if ( $override_variations != 'yes' ) {

					$args['_ywpc_sold_qty']              = 0;
					$args['_ywpc_discount_qty']          = 0;
					$args['_ywpc_sale_price_dates_from'] = '';
					$args['_ywpc_sale_price_dates_to']   = '';

				}

			}

			yit_save_prop( $product, $args );

		}

		/**
		 * Add the countdown column
		 *
		 * @param   $columns
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_ywpc_column( $columns ) {

			$columns['ywpc_status'] = esc_html__( 'Countdown', 'yith-woocommerce-product-countdown' );

			return $columns;

		}

		/**
		 * Render the order fraud risk column
		 *
		 * @param   $column
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function render_ywpc_column( $column ) {

			global $post;

			if ( 'ywpc_status' == $column ) {

				$args = ywpc_get_product_args( $post->ID );

				if ( empty( $args['items'] ) ) {
					esc_html_e( 'Countdown not set', 'yith-woocommerce-product-countdown' );

					return;
				}

				$extra_class = ( $args['class'] ) ? $args['class'] . '-' : '';

				foreach ( $args['items'] as $id => $item ) {

					if ( isset( $args['active_var'] ) && $args['active_var'] != $id ) {
						continue;
					}

					if ( isset( $item['expired'] ) && $item['expired'] == 'expired' ) {
						esc_html_e( 'Countdown expired', 'yith-woocommerce-product-countdown' );

						return;
					}

					?>

					<?php if ( isset( $item['end_date'] ) ) : ?>

						<?php $date = ywpc_get_countdown( $item['end_date'] ); ?>

                        <div class="ywpc-countdown-admin ywpc-item-<?php echo $extra_class . $id; ?>">
                            <span class="ywpc-days">
                                <?php $days = ( ( is_rtl() ) ? strrev( $date['dd'] ) : $date['dd'] ); ?>
                                <span class="ywpc-char-0"><?php echo substr( $days, 0, 1 ); ?></span><span class="ywpc-char-1"><?php echo substr( $days, 1, 1 ); ?></span><span class="ywpc-char-2"><?php echo substr( $days, 2, 1 ); ?></span>
	                            <?php echo _nx( 'd', 'dd', $date['dd'], 'Abbreviation for Days', 'yith-woocommerce-product-countdown' ); ?>
                            </span>
                            <span class="ywpc-hours">
                                <?php $hours = ( ( is_rtl() ) ? strrev( $date['hh'] ) : $date['hh'] ); ?>
                                <span class="ywpc-char-1"><?php echo substr( $hours, 0, 1 ); ?></span><span class="ywpc-char-2"><?php echo substr( $hours, 1, 1 ); ?></span>
								<?php echo _nx( 'h', 'hh', $date['hh'], 'Abbreviation for Hours', 'yith-woocommerce-product-countdown' ); ?>
                            </span>
                            <span class="ywpc-minutes">
                                <?php $minutes = ( ( is_rtl() ) ? strrev( $date['mm'] ) : $date['mm'] ); ?>
                                <span class="ywpc-char-1"><?php echo substr( $minutes, 0, 1 ); ?></span><span class="ywpc-char-2"><?php echo substr( $minutes, 1, 1 ); ?></span>
								<?php echo _nx( 'm', 'mm', $date['mm'], 'Abbreviation for Minutes', 'yith-woocommerce-product-countdown' ); ?>
                            </span>
                            <span class="ywpc-seconds">
                                <?php $seconds = ( ( is_rtl() ) ? strrev( $date['ss'] ) : $date['ss'] ); ?>
                                <span class="ywpc-char-1"><?php echo substr( $seconds, 0, 1 ); ?></span><span class="ywpc-char-2"><?php echo substr( $seconds, 1, 1 ); ?></span>
								<?php echo _nx( 's', 'ss', $date['ss'], 'Abbreviation for Seconds', 'yith-woocommerce-product-countdown' ); ?>
                            </span>
                            <input type="hidden" value="<?php echo( date( 'Y', $date['to'] ) ) ?>.<?php echo( date( 'm', $date['to'] ) - 1 ) ?>.<?php echo( date( 'd', $date['to'] ) ) ?>.<?php echo( date( 'H', $date['to'] ) ) ?>.<?php echo( date( 'i', $date['to'] ) ) ?>">

                        </div>

					<?php endif; ?>

					<?php if ( isset( $item['sold_qty'] ) && isset( $item['discount_qty'] ) ): ?>

                        <div class="ywpc-label">
							<?php
							if ( ! is_rtl() ) {
								printf( esc_html__( '%d/%d Sold', 'yith-woocommerce-product-countdown' ), $item['sold_qty'], $item['discount_qty'] );
							} else {
								printf( esc_html__( '%d/%d Sold', 'yith-woocommerce-product-countdown' ), $item['discount_qty'], $item['sold_qty'] );
							}
							?>
                        </div>
					<?php endif; ?>

					<?php
				}

			}

		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Enqueue frontend script files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function frontend_scripts() {

			wp_enqueue_style( 'ywpc-google-fonts', '//fonts.googleapis.com/css?family=Open+Sans:400,700', array(), null );
			wp_enqueue_style( 'ywpc-frontend' );

			wp_enqueue_script( 'jquery-plugin', YWPC_ASSETS_URL . '/js/jquery.plugin' . ywpc_get_minified() . '.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'jquery-countdown', YWPC_ASSETS_URL . '/js/jquery.countdown' . ywpc_get_minified() . '.js', array( 'jquery', 'jquery-plugin' ), '2.1.0', true );
			wp_enqueue_script( 'ywpc-footer', YWPC_ASSETS_URL . '/js/ywpc-footer' . ywpc_get_minified() . '.js', array( 'jquery', 'jquery-countdown' ), false, true );

			global $post;
			$variation = false;
			if ( $post ) {

				$product = wc_get_product( $post->ID );

				if ( $product ) {

					$variation = $product->get_meta( '_ywpc_variations_global_countdown', true ) == 'yes';


				}

			}


			$theme   = wp_get_theme();
			$js_vars = array(
				'gmt'       => get_option( 'gmt_offset' ),
				'is_rtl'    => is_rtl(),
				'theme'     => $theme->name,
				'variation' => $variation
			);

			wp_localize_script( 'ywpc-footer', 'ywpc_footer', $js_vars );

			if ( apply_filters( 'ywpc_force_two_cypher_days', false ) ) {

				$css = '.ywpc-char-0{ display: none!important; }';

				$template = get_option( 'ywpc_template', '1' );

				if ( '1' == $template ) {

					$css .= '.ywpc-countdown-loop > .ywpc-timer > .ywpc-days { width: 54px; }';

				}

				wp_add_inline_style( 'ywpc-frontend', $css );

			}


		}

		/**
		 * Check if ywpc needs to be showed in product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function check_show_ywpc_product() {

			global $post;

			if ( isset( $post ) ) {
				$product_id = $post->ID;

				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;

				if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
					$product_id = yit_wpml_object_id( $post->ID, $post->post_type, true, wpml_get_default_language() );
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					return;
				}

				$has_countdown  = yit_get_prop( $product, '_ywpc_enabled' );
				$show_countdown = get_option( 'ywpc_where_show', 'page' );

				if ( $has_countdown == 'yes' && ( $show_countdown == 'both' || $show_countdown == 'page' ) ) {

					$args   = $this->get_position_product();
					$action = apply_filters( 'ywpc_override_standard_position', 'woocommerce_' . $args['hook'] . '_summary', $args );

					add_action( $action, array( $this, 'add_ywpc_product' ), $args['priority'] );

				}

			}

		}

		/**
		 * Check if ywpc needs to be showed in category page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function check_show_ywpc_category() {

			global $post, $ywpc_loop;

			$product_id = $post->ID;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $post->ID, $post->post_type, true, wpml_get_default_language() );
			}

			$product        = wc_get_product( $product_id );
			$has_countdown  = yit_get_prop( $product, '_ywpc_enabled', true );
			$show_countdown = get_option( 'ywpc_where_show' );

			if ( $has_countdown == 'yes' && ( ( $show_countdown == 'both' || $show_countdown == 'loop' ) || ( $show_countdown == 'code' && $ywpc_loop != '' ) ) ) {

				$args   = $this->get_position_category();
				$action = apply_filters( 'ywpc_override_standard_position_loop', 'woocommerce_' . $args['hook'] );

				add_action( $action, array( $this, 'add_ywpc_category' ), $args['priority'] );

			}

		}

		/**
		 * Get countdown e sale bar position in product page
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_position_product() {

			$position = get_option( 'ywpc_position_product' );

			switch ( $position ) {

				case '1':
					return array(
						'hook'     => 'single_product',
						'priority' => 15
					);
					break;

				case '2':
					return array(
						'hook'     => 'single_product',
						'priority' => 25
					);
					break;

				case '3':
					return array(
						'hook'     => 'after_single_product',
						'priority' => 5
					);
					break;

				case '4':
					return array(
						'hook'     => 'after_single_product',
						'priority' => 15
					);
					break;

				case '5':
					return array(
						'hook'     => 'after_single_product',
						'priority' => 25
					);
					break;

				default:
					return array(
						'hook'     => 'before_single_product',
						'priority' => 5
					);

			}

		}

		/**
		 * Get countdown e sale bar position in product page
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_position_category() {

			$position = get_option( 'ywpc_position_category' );

			switch ( $position ) {

				case '1':
					return array(
						'hook'     => 'after_shop_loop_item_title',
						'priority' => 9
					);
					break;

				case '2':
					return array(
						'hook'     => 'after_shop_loop_item',
						'priority' => 9
					);
					break;

				case '3':
					return array(
						'hook'     => 'after_shop_loop_item',
						'priority' => 15
					);
					break;

				default:
					return array(
						'hook'     => 'before_shop_loop_item_title',
						'priority' => 15
					);

			}

		}

		/**
		 * Add product countdown to product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_ywpc_product() {

			global $post;

			if ( ! isset( $post ) ) {
				return;
			}
			$product_id = $post->ID;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $post->ID, $post->post_type, true, wpml_get_default_language() );
			}

			ywpc_get_template( $product_id, 'single-product' );

		}

		/**
		 * Add sales countdown to category page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_ywpc_category() {

			global $post;

			$product_id = $post->ID;

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $post->ID, $post->post_type, true, wpml_get_default_language() );
			}

			ywpc_get_template( $product_id, 'category' );

		}

		/**
		 * Check and correct quantity sold
		 *
		 * @param   $order_id
		 * @param   $order
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function update_quantity_product_sold( $order_id, $order ) {

			$processed = yit_get_prop( $order, '_ywpc_processed' );

			if ( $processed != 'yes' ) {

				$items = $order->get_items();

				foreach ( $items as $item ) {

					$product_id = $item['product_id'];

					global $sitepress;
					$has_wpml = ! empty( $sitepress ) ? true : false;

					if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
						$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
					}

					$product  = wc_get_product( $product_id );
					$has_ywpc = yit_get_prop( $product, '_ywpc_enabled' );

					if ( $has_ywpc == 'yes' ) {

						$time_from    = yit_get_prop( $product, '_ywpc_sale_price_dates_from' );
						$current_time = strtotime( current_time( "Y-m-d G:i:s" ) );

						if ( $time_from > $current_time ) {
							continue;
						}

						$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

						if ( $item['variation_id'] == 0 || $variation_global == 'yes' ) {

							$sold_qty = yit_get_prop( $product, '_ywpc_sold_qty' );
							$sold_qty += (int) $item['qty'];
							yit_save_prop( $product, '_ywpc_sold_qty', esc_attr( $sold_qty ) );

						} else {

							$product_id = $item['variation_id'];

							global $sitepress;
							$has_wpml = ! empty( $sitepress ) ? true : false;

							if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
								$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
							}

							$variation = wc_get_product( $product_id );
							$sold_qty  = yit_get_prop( $variation, '_ywpc_sold_qty' );
							$sold_qty  += $item['qty'];
							yit_save_prop( $product, '_ywpc_sold_qty', esc_attr( $sold_qty ) );

						}

					}

				}

				yit_save_prop( $order, '_ywpc_processed', 'yes' );

			}

		}

		/**
		 * Check and correct quantity in sale
		 *
		 * @param   $data
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function correct_quantity_product( $data ) {

			if ( isset( $data['product_id'] ) ) {

				$product      = wc_get_product( $data['product_id'] );
				$discount_qty = yit_get_prop( $product, '_ywpc_discount_qty' );
				$sold_qty     = yit_get_prop( $product, '_ywpc_sold_qty' );
				$sold_qty     = $sold_qty ? $sold_qty : 0;

				if ( $discount_qty ) {

					$available_qty = $discount_qty - $sold_qty;

					if ( $available_qty > 0 && $available_qty < $data['quantity'] ) {

						$data['quantity'] = $available_qty;

					}

				}

			}

			return $data;

		}

		/**
		 * Get timer title
		 *
		 * @param   $value
		 * @param   $before
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_timer_title( $value, $before ) {

			$arg = '';

			if ( $before === true ) {
				$arg = '_before';
			}

			return get_option( 'ywpc_timer_title' . $arg );

		}

		/**
		 * Add countdown css to product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_custom_css() {

			$inline_css = '';

			if ( get_option( 'ywpc_timer_title' ) == '' ) {

				$inline_css .= '
				.ywpc-countdown > .ywpc-header,
				.ywpc-countdown-loop > .ywpc-header {
					display: none;
				}';

			}

			if ( get_option( 'ywpc_sale_bar_title' ) == '' ) {

				$inline_css .= '
				.ywpc-sale-bar > .ywpc-header,
				.ywpc-sale-bar-loop > .ywpc-header {
					display: none;
				}';

			}

			if ( get_option( 'ywpc_appearance' ) == 'cust' ) {

				$text_font_size       = get_option( 'ywpc_text_font_size', 25 );
				$text_font_size_loop  = get_option( 'ywpc_text_font_size_loop', 15 );
				$timer_font_size      = get_option( 'ywpc_timer_font_size', 28 );
				$timer_font_size_loop = get_option( 'ywpc_timer_font_size_loop', 15 );
				$text_color           = get_option( 'ywpc_text_color', '#a12418' );
				$border_color         = get_option( 'ywpc_border_color', '#dbd8d8' );
				$back_color           = get_option( 'ywpc_back_color', '#fafafa' );
				$timer_fore_color     = get_option( 'ywpc_timer_fore_color', '#3c3c3c' );
				$timer_back_color     = get_option( 'ywpc_timer_back_color', '#ffffff' );
				$bar_fore_color       = get_option( 'ywpc_bar_fore_color', '#a12418' );
				$bar_back_color       = get_option( 'ywpc_bar_back_color', '#e6e6e6' );

				$inline_css .= '
				.ywpc-countdown,
				.ywpc-sale-bar {
					background: ' . $back_color . ';
					border: 1px solid ' . $border_color . ';
				}

				.ywpc-countdown > .ywpc-header,
				.ywpc-sale-bar > .ywpc-header {
					color: ' . $text_color . ';
					font-size: ' . $text_font_size . 'px;
				}

				.ywpc-countdown-loop > .ywpc-header,
				.ywpc-sale-bar-loop > .ywpc-header {
					color: ' . $text_color . ';
					font-size: ' . $text_font_size_loop . 'px;
				}';

				if ( get_option( 'ywpc_template', '1' ) == '1' ) {

					$inline_css .= '
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $timer_back_color . ';
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size . 'px;
					}
		
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $timer_back_color . ';
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size_loop . 'px;
					}';

				} else {

					$inline_css .= '
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount,
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount {
						background: ' . $timer_back_color . ';
					}
		
					.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size . 'px;
					}
		
					.ywpc-countdown-loop > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $timer_fore_color . ';
						font-size: ' . $timer_font_size_loop . 'px;
					}';

				}

				$inline_css .= '
				.ywpc-sale-bar > .ywpc-bar > .ywpc-back,
				.ywpc-sale-bar-loop > .ywpc-bar > .ywpc-back {
					background: ' . $bar_back_color . ';
				}
	
				.ywpc-sale-bar > .ywpc-bar > .ywpc-back > .ywpc-fore,
				.ywpc-sale-bar-loop > .ywpc-bar > .ywpc-back > .ywpc-fore {
					background: ' . $bar_fore_color . ';
				}';

			}

			if ( get_option( 'ywpc_topbar_appearance' ) == 'cust' && get_option( 'ywpc_topbar_enable' ) == 'yes' ) {

				$topbar_text_font_size   = get_option( 'ywpc_topbar_text_font_size', 30 );
				$topbar_timer_font_size  = get_option( 'ywpc_topbar_timer_font_size', 18 );
				$topbar_text_color       = get_option( 'ywpc_topbar_text_color', '#a12418' );
				$topbar_label_color      = get_option( 'ywpc_topbar_text_label_color', '#232323' );
				$topbar_back_color       = get_option( 'ywpc_topbar_back_color', '#ffba00' );
				$topbar_timer_text_color = get_option( 'ywpc_topbar_timer_text_color', '#363636' );
				$topbar_timer_back_color = get_option( 'ywpc_topbar_timer_back_color', '#ffffff' );
				$topbar_border_color     = get_option( 'ywpc_topbar_timer_border_color', '#ff8a00' );

				$inline_css .= '
				.ywpc-countdown-topbar {
					background: ' . $topbar_back_color . ';
				}

				.ywpc-countdown-topbar > .ywpc-header {
					color: ' . $topbar_text_color . ';
					font-size: ' . $topbar_text_font_size . 'px;
				}

				.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-label {
					color: ' . $topbar_label_color . ';
				}';

				if ( get_option( 'ywpc_topbar_template', '2' ) == '2' ) {

					$inline_css .= '
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span {
						background: ' . $topbar_timer_back_color . ';
						color: ' . $topbar_timer_text_color . ';
						font-size: ' . $topbar_timer_font_size . 'px;
					}
					
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount {
						background: ' . $topbar_border_color . ';
					}';

				} else {

					$inline_css .= '
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount {
						background: ' . $topbar_timer_back_color . ';
						border: 1px solid ' . $topbar_border_color . ';
					}
					
					.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span {
						color: ' . $topbar_timer_text_color . ';
						font-size: ' . $topbar_timer_font_size . ' px;
					}';

				}

			}

			if ( $inline_css ) {

				wp_add_inline_style( 'ywpc-frontend', $inline_css );

			}

		}

		/**
		 * Check if product/variation is valid or expired
		 *
		 * @param   $id
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function check_ywpc_expiration( $id ) {

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$id = yit_wpml_object_id( $id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $id );

			if ( ! $product ) {
				return false;
			}

			$current_time       = strtotime( current_time( "Y-m-d G:i:s" ) );
			$before_sale        = get_option( 'ywpc_before_sale_start' );
			$before_sale_status = get_option( 'ywpc_before_sale_start_status' );
			$sale_start         = yit_get_prop( $product, '_ywpc_sale_price_dates_from' );
			$sale_end           = yit_get_prop( $product, '_ywpc_sale_price_dates_to' );
			$discount_qty       = yit_get_prop( $product, '_ywpc_discount_qty' );
			$sold_qty           = yit_get_prop( $product, '_ywpc_sold_qty' );
			$expired            = false;

			if ( ! empty( $sale_start ) && ! empty( $sale_end ) ) {

				switch ( true ) {

					case ( $current_time < $sale_start && $before_sale == 'yes' ):
						$expired = ( $before_sale_status == 'yes' );
						break;

					case ( $current_time >= $sale_start && $current_time <= $sale_end ):
						$expired = false;
						break;

					case ( $current_time > $sale_end ) :
						$expired = true;
						break;

				}

			}

			if ( ! $expired ) {

				if ( $sold_qty < $discount_qty ) {

					$expired = false;

				} else {

					if ( $sold_qty == 0 && $discount_qty == 0 || $discount_qty < $sold_qty ) {

						$expired = false;

					} else {

						$expired = true;

					}

				}

			}

			if ( defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM && get_option( 'ywpc_end_sale' ) == 'disable' ) {

				if ( get_option( 'ywctm_enable_plugin' ) == 'yes' && YITH_WCTM()->check_user_admin_enable() ) {

					if ( YITH_WCTM()->disable_shop() ) {

						$expired = true;

					} else {

						$hide_add_to_cart_single = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_single' ), $id, 'ywctm_hide_add_to_cart_single' );
						$hide_add_to_cart_loop   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_add_to_cart_loop' ), $id, 'ywctm_hide_add_to_cart_loop' );
						$hide_price              = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price' ), $id, 'ywctm_hide_price' );

						if ( $hide_add_to_cart_single == 'yes' || $hide_add_to_cart_loop == 'yes' || $hide_price == 'yes' ) {

							if ( YITH_WCTM()->apply_catalog_mode( $id ) ) {

								$enable_exclusion = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart' ), $id, 'ywctm_exclude_hide_add_to_cart' );
								$exclude_catalog  = apply_filters( 'ywctm_get_exclusion', yit_get_prop( $product, '_ywctm_exclude_catalog_mode' ), $id, '_ywctm_exclude_catalog_mode' );

								$expired = ( $enable_exclusion != 'yes' ? true : ( $exclude_catalog != 'yes' ? true : false ) );

								$reverse_criteria = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ), $id, 'ywctm_exclude_hide_add_to_cart_reverse' );

								if ( $enable_exclusion == 'yes' && $reverse_criteria == 'yes' ) {

									$expired = ! $expired;

								}

							}

						}

						if ( apply_filters( 'ywctm_check_price_hidden', false, $id ) ) {

							$expired = true;

						}

					}

				}


			}

			return $expired;

		}

		/**
		 * Get custom loop for widget and shortcode
		 *
		 * @param   $ids
		 * @param   $type
		 * @param   $options
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function get_ywpc_custom_loop( $ids, $type, $options = array() ) {

			global $ywpc_loop;

			$ywpc_loop = 'ywpc_' . $type;

			if ( $ids ) {

				$query_args = array(
					'posts_per_page' => apply_filters( 'ywpc_number_of_products_to_show', '-1' ),
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'post__in'       => $ids,
				);

			} else {

				$query_args = array(
					'posts_per_page' => apply_filters( 'ywpc_number_of_products_to_show', '-1' ),
					'no_found_rows'  => 1,
					'post_status'    => 'publish',
					'post_type'      => 'product',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'   => '_ywpc_enabled',
							'value' => 'yes',
						),
						array(
							'key'     => '_ywpc_sale_price_dates_from',
							'value'   => strtotime( 'NOW', current_time( 'timestamp' ) ),
							'compare' => '<=',
							'type'    => 'NUMERIC'
						),
						array(
							'key'     => '_ywpc_sale_price_dates_to',
							'value'   => strtotime( 'NOW', current_time( 'timestamp' ) ),
							'compare' => '>=',
							'type'    => 'NUMERIC'
						)
					)
				);

			}
			$products = new WP_Query( $query_args );

			if ( $products->have_posts() ) {

				wc_get_template( '/frontend/widget-shortcode-loop.php', array(
					'type'     => $type,
					'products' => $products,
					'options'  => $options
				), '', YWPC_TEMPLATE_PATH );

			}

			wp_reset_query();

			$ywpc_loop = false;

		}

		/**
		 * Function for Widget and Shortcode to hide loop elements
		 *
		 * @param    $value
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywpc_loop_hide_filter( $value ) {

			global $ywpc_loop;

			return ( ( $ywpc_loop == 'ywpc_shortcode' || $ywpc_loop == 'ywpc_widget' ) ? '' : $value );

		}

		/**
		 * Hides "Add to cart" button from loop page (if option is enabled)
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_loop() {

			$ywpc_alt_loop_hook = apply_filters( 'ywpc_alternative_loop_hook', true );
			$result             = $this->hide_add_to_cart_check();

			if ( ! empty( $result ) ) {

				if ( $result['type'] == 'simple' ) {

					if ( $ywpc_alt_loop_hook ) {
						remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
					}
					add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );
				}

			} else {

				if ( $ywpc_alt_loop_hook ) {
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				}

				remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string', 10 );

			}

		}

		/**
		 * Hides "Add to cart" button from single product page (if option is enabled)
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_single() {

			$result = $this->hide_add_to_cart_check();

			if ( ! empty( $result ) ) {

				$args = array();

				if ( ! class_exists( 'YITH_YWRAQ_Frontend' ) ) {

					$args[] = 'form.cart .quantity';

				}

				if ( $result['type'] == 'simple' || $result['type'] == 'variable-all' ) {

					$args[] = 'form.cart button.single_add_to_cart_button';
					$args[] = 'form.cart .woocommerce-variation-add-to-cart';

				} else {

					$expired = ( ( isset( $result['ids'] ) && ! empty( $result['ids'] ) ) ? implode( ', ', $result['ids'] ) : '' );

					ob_start();

					?>

                    jQuery(function ($) {

                    var expired = [ <?php echo $expired ?> ],
                    value = parseInt($('.single_variation_wrap .variation_id, .single_variation_wrap input[name="variation_id"]').val());

                    if (expired.length > 0 && $.inArray(value, expired) != -1) {
                    $('.single_variation_wrap .variations_button').hide()
                    }

                    $(document).on('woocommerce_variation_has_changed', hide_variations);
                    $(document).on('found_variation', hide_variations);


                    function hide_variations () {
                    value = parseInt($('.single_variation_wrap .variation_id, .single_variation_wrap input[name="variation_id"]').val());

                    if (expired.length > 0) {
                    if ($.inArray(value, expired) == -1) {
                    $('.single_variation_wrap .variations_button').show();
                    } else {
                    $('.single_variation_wrap .variations_button').hide();
                    }
                    }

                    }

                    });

					<?php

					$inline_js = ob_get_clean();
					wp_add_inline_script( 'ywpc-footer', $inline_js );

				}

				$classes = implode( ', ', apply_filters( 'ywpc_hide_classes', $args ) );

				ob_start();
				?>
				<?php echo $classes; ?>
                {
                display: none<?php echo( $result['type'] == 'simple' ? ' !important' : '' ); ?>;
                }
				<?php

				$inline_css = ob_get_clean();
				wp_add_inline_style( 'ywpc-frontend', $inline_css );

			}

		}

		/**
		 * Check if "Add to cart" needs to be hidden (if option is enabled)
		 *
		 * @param   $product_id
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function hide_add_to_cart_check( $product_id = false ) {

			global $post;

			$id = ( $product_id ) ? $product_id : ( $post ? $post->ID : 0 );

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$id = yit_wpml_object_id( $id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $id );

			if ( ! $product ) {
				return array();
			}

			$has_ywpc = yit_get_prop( $product, '_ywpc_enabled' );
			$result   = array();

			if ( $has_ywpc == 'yes' ) {

				$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

				if ( ( ! $product->is_type( 'variable' ) ) || ( $product->is_type( 'variable' ) && $variation_global == 'yes' ) ) {

					if ( $this->check_ywpc_expiration( $id ) ) {

						$result['type'] = 'simple';

					}

				} else {

					$product_variables = $product->get_available_variations();

					if ( count( array_filter( $product_variables ) ) > 0 ) {

						$product_variables = array_filter( $product_variables );
						$result['type']    = 'variable';
						$count             = 0;

						foreach ( $product_variables as $product_variable ) {

							if ( $this->check_ywpc_expiration( $product_variable['variation_id'] ) ) {

								$result['ids'][] = $product_variable['variation_id'];
								$count ++;

							}

						}

						if ( $count == count( $product_variables ) ) {
							$result['type'] = 'variable-all';
						}

					}
				}

			}

			return $result;

		}

		/**
		 * Avoid "Add to cart" action (if option is enabled)
		 *
		 * @param   $passed
		 * @param   $product_id
		 *
		 * @return  bool
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function avoid_add_to_cart( $passed, $product_id ) {

			$result = $this->hide_add_to_cart_check( $product_id );

			if ( ! empty( $result ) ) {
				if ( $result['type'] == 'simple' ) {
					$passed = false;
				}
			}

			return $passed;
		}

		/**
		 * Hide product form the shop
		 *
		 * @param    $query
		 *
		 * @return   void
		 * @since    1.0.0
		 *
		 * @author   Alberto Ruggiero
		 */
		public function hide_product_from_catalog( $query ) {

			$products_list = $this->get_expired_products();

			if ( ! empty( $products_list ) ) {

				$query->set( 'post__not_in', $products_list );

			}

		}

		/**
		 * Hide product form the shop
		 *
		 * @param    $related
		 *
		 * @return   array
		 * @since    1.2.1
		 *
		 * @author   Alberto Ruggiero
		 */
		public function hide_from_related_products( $related ) {

			$products_list = $this->get_expired_products();

			if ( ! empty( $products_list ) ) {

				$related = array_diff( $related, $products_list );
			}

			return $related;

		}

		/**
		 * Hide product form the shop
		 *
		 * @param    $query_args
		 *
		 * @return   array
		 * @since    1.2.1
		 *
		 * @author   Alberto Ruggiero
		 */
		public function hide_product_from_shortcodes( $query_args ) {

			$products_list = $this->get_expired_products();

			if ( ! empty( $products_list ) ) {

				$query_args['post__not_in'] = $products_list;

			}

			return $query_args;

		}

		/**
		 * Get expired products
		 *
		 * @return   array
		 * @since    1.2.1
		 * @author   Alberto Ruggiero
		 */
		public function get_expired_products() {

			$products_list = array();
			$product_args  = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'   => '_ywpc_enabled',
						'value' => 'yes',
					)
				)
			);


			$products = get_posts( $product_args );

			foreach ( $products as $prod ) {
				$product          = wc_get_product( $prod->ID );
				$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

				if ( $this->check_ywpc_expiration( $prod->ID ) && ( ! $product->is_type( 'variable' ) || ( $product->is_type( 'variable' ) && $variation_global == 'yes' ) ) ) {

					$products_list[] = $prod->ID;

				}
			}

			return $products_list;

		}

		/**
		 * Hide variation
		 *
		 * @param    $visible
		 * @param    $variation_id
		 *
		 * @return   bool
		 * @since    1.0.0
		 *
		 * @author   Alberto Ruggiero
		 */
		public function hide_variations( $visible, $variation_id ) {

			if ( $this->check_ywpc_expiration( $variation_id ) ) {
				$visible = false;
			}

			return $visible;

		}

		/**
		 * Avoid direct access to disabled products
		 *
		 * @return   void
		 * @since    1.0.0
		 * @author   Alberto Ruggiero
		 */
		public function avoid_direct_access() {

			global $post;

			if ( is_singular( 'product' ) ) {

				$product  = wc_get_product( $post->ID );
				$has_ywpc = yit_get_prop( $product, '_ywpc_enabled' );

				if ( $has_ywpc == 'yes' ) {

					$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

					if ( $this->check_ywpc_expiration( $post->ID ) && ( ! $product->is_type( 'variable' ) || ( $product->is_type( 'variable' ) && $variation_global == 'yes' ) ) ) {

						include( get_query_template( '404' ) );
						exit;

					}

				}

			}

		}

		/**
		 * Initialize topbar
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero
		 */
		public function initialize_topbar() {

			if ( get_option( 'ywpc_topbar_enable' ) == 'yes' ) {

				add_filter( 'body_class', array( $this, 'add_body_classes' ) );
				add_action( 'wp_footer', array( $this, 'print_bar_countdown' ), 999 );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts_topbar' ), 9 );

			}

		}

		/**
		 * Add countdown all pages
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function print_bar_countdown() {

			$prod_id = get_option( 'ywpc_topbar_product' );

			global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywpc_wpml_use_default_language_settings', false ) ) {
				$prod_id = yit_wpml_object_id( $prod_id, 'product', true, wpml_get_default_language() );
			}

			$product  = wc_get_product( $prod_id );
			$has_ywpc = yit_get_prop( $product, '_ywpc_enabled' );
			$args     = array();

			if ( $has_ywpc == 'yes' ) {

				$current_time = strtotime( current_time( "Y-m-d G:i:s" ) );

				$variation_global = yit_get_prop( $product, '_ywpc_variations_global_countdown' );

				if ( ( ! $product->is_type( 'variable' ) ) || ( $product->is_type( 'variable' ) && $variation_global == 'yes' ) ) {

					$sale_start = yit_get_prop( $product, '_ywpc_sale_price_dates_from' );
					$sale_end   = yit_get_prop( $product, '_ywpc_sale_price_dates_to' );

					if ( $current_time >= $sale_start && $current_time <= $sale_end ) {

						$args['id']       = $prod_id;
						$args['end_date'] = $sale_end;
						$args['type']     = 'simple';
					}

				} else {

					$product_variables = $product->get_available_variations();

					if ( count( array_filter( $product_variables ) ) > 0 ) {

						$product_variables = array_filter( $product_variables );
						$check_default     = yit_get_prop( $product, '_default_attributes' );

						foreach ( $product_variables as $product_variable ) {

							if ( $check_default && is_array( $check_default ) ) {

								$variation          = wc_get_product( $product_variable['variation_id'] );
								$key_select_default = key( $check_default );
								$check_default      = $check_default[ $key_select_default ];
								$key_attr           = str_replace( 'attribute_', '', key( $product_variable['attributes'] ) );
								$data_attr          = $product_variable['attributes'][ 'attribute_' . $key_attr ];

								if ( $key_attr == $key_select_default && $check_default == $data_attr ) {

									$sale_start = yit_get_prop( $variation, '_ywpc_sale_price_dates_from' );
									$sale_end   = yit_get_prop( $variation, '_ywpc_sale_price_dates_to' );

									if ( ! empty( $sale_end ) && ! empty( $sale_start ) ) {

										if ( $current_time >= $sale_start && $current_time <= $sale_end ) {

											$args['id']       = $prod_id;
											$args['end_date'] = $sale_end;
											$args['type']     = 'variable';
											$args['url']      = '?attribute_' . $key_attr . '=' . $data_attr;

										}

									}

								}

							}

						}

					}

				}

				if ( ! empty( $args ) ) {
					wc_get_template( '/frontend/bar-timer.php', array( 'args' => $args ), '', YWPC_TEMPLATE_PATH );
				}

			}

		}

		/**
		 * Enqueue frontend script files
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function frontend_scripts_topbar() {

			$template = get_option( 'ywpc_topbar_template', '1' );

			wp_enqueue_style( 'ywpc-frontend-topbar', YWPC_ASSETS_URL . '/css/ywpc-bar-style-' . $template . ywpc_get_minified() . '.css' );

			if ( apply_filters( 'ywpc_force_two_cypher_days', false ) && '2' == $template ) {

				$css = '.ywpc-countdown-topbar > .ywpc-timer > .ywpc-days > .ywpc-amount { width: 72px }';

				wp_add_inline_style( 'ywpc-frontend-topbar', $css );

			}

		}

		/**
		 * Add classes to body
		 *
		 * @param   $classes
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_body_classes( $classes ) {

			$position = get_option( 'ywpc_topbar_position', 'top' );

			$classes[] = 'ywpc-' . $position;

			if ( is_admin_bar_showing() && $position == 'top' ) {
				$classes[] = 'ywpc-admin';
			}

			return $classes;

		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Enqueue css file
		 *
		 * @return  void
		 * @since   1.0.0
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
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $links | links plugin array
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {

			$links = yith_add_action_links( $links, $this->_panel_page, true );

			return $links;
		}

		/**
		 * Plugin row meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param   $new_row_meta_args
		 * @param   $plugin_meta
		 * @param   $plugin_file
		 * @param   $plugin_data
		 * @param   $status
		 * @param   $init_file
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWPC_INIT' ) {

			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']       = YWPC_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;

		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWPC_INIT, YWPC_SECRET_KEY, YWPC_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWPC_SLUG, YWPC_INIT );
		}

		/**
		 * Add Plugin Requirements
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_plugin_requirements() {

			$plugin_data  = get_plugin_data( plugin_dir_path( __FILE__ ) . '/init.php' );
			$plugin_name  = $plugin_data['Name'];
			$requirements = array(
				'min_wp_version' => '5.2.0',
				'min_wc_version' => '4.0.0',
			);
			yith_plugin_fw_add_requirements( $plugin_name, $requirements );
		}

	}

}