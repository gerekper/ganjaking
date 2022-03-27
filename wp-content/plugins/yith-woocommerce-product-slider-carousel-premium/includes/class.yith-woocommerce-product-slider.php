<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\WooCommerceProductSliderCarousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WooCommerce_Product_Slider' ) ) {

	/**
	 * YITH_WooCommerce_Product_Slider
	 */
	class YITH_WooCommerce_Product_Slider {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WooCommerce_Product_Slider $instance
		 */
		protected static $instance;
		/**
		 * YITH WooCommerce Sequential Order Number Premium Panel object
		 *
		 * @var YITH_WooCommerce_Product_Slider $panel
		 */
		protected $panel = null;
		/**
		 * YITH WooCommerce Sequential Order Number Premium Panel Page
		 *
		 * @var YITH_WooCommerce_Product_Slider $panel_page
		 */
		protected $panel_page = 'yith_wc_product_slider';
		/**
		 * YITH WooCommerce Sequential Order Number Premium Premium
		 *
		 * @var YITH_WooCommerce_Product_Slider $premium
		 */
		protected $premium = 'premium.php';
		/**
		 * YITH WooCommerce Sequential Order Number Premium $is_wc_2_7
		 *
		 * @var YITH_WooCommerce_Product_Slider $is_wc_2_7
		 */
		public $is_wc_2_7;
		/**
		 * __construct function
		 */
		public function __construct() {
			// Load Plugin Framework!
			$this->product_slider = YITH_Product_Slider_Type();

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			// Add action links!
			add_filter(
				'plugin_action_links_' . plugin_basename( YWCPS_DIR . '/' . basename( YWCPS_FILE ) ),
				array(
					$this,
					'action_links',
				)
			);
			// Add row meta!
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			// Add menu  field under YITH_PLUGIN!
			add_action( 'yith_wc_product_slider_carousel_premium', array( $this, 'premium_tab' ) );
			add_action( 'admin_menu', array( $this, 'add_product_slider_carousel_menu' ), 5 );

			add_action( 'wp_enqueue_scripts', array( $this, 'include_style_script' ) );

			if ( is_admin() ) {
				add_action( 'plugins_loaded', 'ywcps_add_gutenberg_block', 20 );
				add_action( 'current_screen', array( $this, 'add_shortcodes_button' ) );
			}

			$this->is_wc_2_7 = version_compare( WC()->version, '2.7.0', '>=' );

			if ( ! defined( 'YWCPS_PREMIUM' ) ) {
				$this->_include_templates();
				add_action( 'woocommerce_admin_field_ajax-category', 'YWCPS_Ajax_Category::output' );
			}

		}

		/**
		 * Get_instance
		 *
		 * @return instance
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Include lightbox in thickbox
		 *
		 * @package YITH
		 * @since 1.0.6
		 */
		private function _include_templates() { //phpcs:ignore
			include_once YWCPS_TEMPLATE_PATH . '/admin/ajax-category.php';
		}

		/**
		 * Plugin_fw_loader
		 *
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param mixed $links | links plugin array.
		 *
		 * @return   mixed Array
		 * @use plugin_action_links_{$plugin_file_name}
		 * @package YITH
		 * @since    1.0
		 */
		public function action_links( $links ) {
			$is_premium = defined( 'YWCPS_INIT' );

			$links = yith_add_action_links( $links, $this->panel_page, $is_premium, YWCPS_SLUG );

			return $links;
		}

		/**
		 * Plugin_row_meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param mixed  $new_row_meta_args new_row_meta_args.
		 * @param mixed  $plugin_meta plugin_meta.
		 * @param mixed  $plugin_file plugin_file.
		 * @param mixed  $plugin_data plugin_data.
		 * @param mixed  $status status.
		 * @param string $init_file init file.
		 *
		 * @return   array
		 * @since    1.0
		 * @package YITH
		 * @use yith_show_plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCPS_FREE_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['slug'] = YWCPS_SLUG;
			}

			return $new_row_meta_args;
		}


		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return  void
		 * @package YITH
		 * @since   1.0.0
		 */
		public function premium_tab() {
			$premium_tab_template = YWCPS_TEMPLATE_PATH . '/admin/' . $this->premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once $premium_tab_template;
			}
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @package YITH
		 * @use     /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function add_product_slider_carousel_menu() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'settings' => __( 'Settings', 'yith-woocommerce-product-slider-carousel' ),
			);

			if ( ! defined( 'YWCPS_PREMIUM' ) ) {
				$admin_tabs['premium-landing'] = __( 'Premium Version', 'yith-woocommerce-product-slider-carousel' );
			} else {
				$admin_tabs['slider-list'] = __( 'Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' );
				$admin_tabs['layout1']     = __( 'Layout 1', 'yith-woocommerce-product-slider-carousel' );
				$admin_tabs['layout2']     = __( 'Layout 2', 'yith-woocommerce-product-slider-carousel' );
				$admin_tabs['layout3']     = __( 'Layout 3', 'yith-woocommerce-product-slider-carousel' );

			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'YITH WooCommerce Product Slider Carousel',
				'menu_title'       => 'Product Slider Carousel',
				'capability'       => apply_filters( 'ywcps_menu_capability','manage_options' ),
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'class'            => yith_set_wrapper_class(),
				'options-path'     => YWCPS_DIR . '/plugin-options',
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}


		/**Include style and script
		 *
		 * @package YITH
		 * @since 1.0.0
		 */
		public function include_style_script() {
			wp_register_style( 'fontawesome', YWCPS_ASSETS_URL . 'css/font-awesome.min.css', array(), YWCPS_VERSION );
			wp_register_style( 'yith-animate', YWCPS_ASSETS_URL . 'css/animate.css', array(), YWCPS_VERSION );
			wp_register_style( 'yith-product-slider-style', YWCPS_ASSETS_URL . 'css/product_slider_style.css', array(), YWCPS_VERSION );

			if ( defined( 'YIT' ) ) {
				$add_inline_style = '.ywcps-slider{visibility:visible!important;}';
				wp_add_inline_style( 'yith-product-slider-style', $add_inline_style );

			} else {
				wp_register_style( 'owl-carousel-style', YWCPS_ASSETS_URL . 'css/owl.css', array(), '2.2.1' );
				wp_register_style( 'owl-carousel-style-3d', YWCPS_ASSETS_URL . 'css/owl-translate3d.css', array( 'owl-carousel-style' ), '2.2.1' );
				wp_register_script( 'owl-carousel', YWCPS_ASSETS_URL . 'js/owl.carousel.js', array( 'jquery' ), '2.2.1', true );

			}
		}


		/**
		 * Add shortcode button
		 *
		 * Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @param WP_Screen $current_screen current screen.
		 *
		 * @return void
		 * @since 1.0.0
		 * @package YITH
		 */
		public function add_shortcodes_button( $current_screen ) {

			if ( ! is_null( $current_screen ) && ! in_array( $current_screen->post_type, array( 'post', 'page' ), true ) ) {
				if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
					return;
				}
				if ( get_user_option( 'rich_editing' ) === 'true' ) {
					add_action( 'media_buttons', array( &$this, 'ywcps_media_buttons_context' ) );
					add_action( 'admin_print_footer_scripts', array( &$this, 'ywcps_add_quicktags' ) );
					add_action( 'admin_action_print_shortcode_popup', array( $this, 'print_shortcode_popup' ) );
					add_filter( 'mce_external_plugins', array( $this, 'add_shortcodes_tinymce_plugin' ) );
					add_filter( 'mce_buttons', array( $this, 'register_shortcodes_button' ) );
				}
				// Register shortcodes to WPBackery Visual Composer!
				add_action( 'vc_before_init', array( $this, 'register_vc_shortcodes' ) );
			}
		}

		/**
		 * Add shortcode plugin
		 *
		 * Add a script to TinyMCE script list
		 *
		 * @param array $plugin_array array() Array containing TinyMCE script list.
		 *
		 * @return array() The edited array containing TinyMCE script list
		 * @since 1.0.0
		 * @package YITH
		 */
		public function add_shortcodes_tinymce_plugin( $plugin_array ) {
			$plugin_array['ywcps_shortcode'] = YWCPS_ASSETS_URL . 'js/tinymce.js';

			return $plugin_array;
		}

		/**
		 * Register shortcode button
		 *
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @param array $buttons array() Array containing buttons list for TinyMCE toolbar.
		 *
		 * @return array() The edited array containing buttons list for TinyMCE toolbar
		 * @since 1.0.0
		 * @package YITH
		 */
		public function register_shortcodes_button( $buttons ) {
			array_push( $buttons, '|', 'ywcps_shortcode' );

			return $buttons;
		}


		/**
		 * The markup of shortcode
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @package YITH
		 */
		public function ywcps_media_buttons_context() {

			global $post_ID, $temp_ID; // phpcs:ignore WordPress.NamingConventions

			$query_args = array(
				'post_id'   => (int) ( 0 === $post_ID ? $temp_ID : $post_ID ), // phpcs:ignore WordPress.NamingConventions
				'action'    => 'print_shortcode_popup',
				'KeepThis'  => true,
				'TB_iframe' => true,

			);
			$lightbox_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

			$out = sprintf( '<a id="ywcps_shortcode" style="display:none" href="%s" class="hide-if-no-js thickbox" title="%s"></a>', $lightbox_url, __( 'Add YITH WooCommerce Product Slider Carousel shortcode', 'yith-woocommerce-product-slider-carousel' ) );

			echo $out; // phpcs:ignore WordPress.Security.EscapeOutput

		}

		/**
		 * Print_shortcode_popup
		 *
		 * @return void
		 */
		public function print_shortcode_popup() {

			require_once YWCPS_DIR . '/templates/admin/lightbox.php';
		}

		/**
		 * Add quicktags to visual editor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @package YITH
		 */
		public function ywcps_add_quicktags() {

			?>
			<script type="text/javascript">

				if (window.QTags !== undefined) {
					QTags.addButton('ywcps_shortcode', 'add ywcps shortcode', function () {
						jQuery('#ywcps_shortcode').click()
					});
				}


			</script>
			<?php
		}

		/**
		 * Register product slider carousel shortcode to visual composer
		 *
		 * @return void
		 * @package YITH
		 * @since 1.0.6
		 */
		public function register_vc_shortcodes() {

			$all_sliders = $this->get_productslider();

			$options = array();

			foreach ( $all_sliders as $key => $value ) {
				$options[ "{$value['text']}" ] = $value['value'];
			}

			$vc_map_params = apply_filters(
				'yith_wcps_vc_shortcodes_params',
				array(

					'yith_wc_productslider' => array(
						'name'        => __( 'YITH WooCommerce Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
						'base'        => 'yith_wc_productslider',
						'description' => __( 'Add Product Slider', 'yith-woocommerce-product-slider-carousel' ),
						'category'    => __( 'Product Slider Carousel', 'yith-woocommerce-product-slider-carousel' ),
						'params'      => array(
							array(
								'type'        => 'dropdown',
								'holder'      => '',
								'heading'     => __( 'Choose a Product Slider', 'yith-woocommerce-product-slider-carousel' ),
								'param_name'  => 'id',
								'value'       => $options,
								'description' => __( 'Choose to show empty terms or not', 'yith-wcbr' ),
							),
							array(
								'type'       => 'textfield',
								'holder'     => '',
								'param_name' => 'z_index',
								'heading'    => __( 'Z-Index', 'yith-woocommerce-product-slider-carousel' ),
							),
						),

					),

				)
			);

			if ( ! empty( $vc_map_params ) && function_exists( 'vc_map' ) ) {
				foreach ( $vc_map_params as $params ) {
					vc_map( $params );
				}
			}
		}

		/**
		 * Return all product slider
		 *
		 * @return array
		 * @since 1.0.0
		 * @used include_style_script
		 * @package YITH
		 */
		public function get_productslider() {

			global $wpdb;
			$slider_free_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id   WHERE post_type = 'yith_wcps_type' AND {$wpdb->postmeta}.meta_key ='_ywcps_free_slider_id' " ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery

			return array(
				array(
					'text'  => get_option( 'ywcps_title' ),
					'value' => $slider_free_id,
				),
			);

		}


	}
}
