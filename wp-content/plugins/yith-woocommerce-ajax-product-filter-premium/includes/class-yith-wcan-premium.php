<?php
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Premium' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Premium extends YITH_WCAN_Extended {

		/**
		 * Constructor
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->version = YITH_WCAN_VERSION;

			// Require Premium Files.
			add_filter( 'yith_wcan_required_files', array( $this, 'require_additional_files' ) );

			// Add premium filters type.
			add_filter( 'yith_wcan_supported_filters', array( $this, 'supported_filters' ) );
			add_filter( 'yith_wcan_supported_filter_designs', array( $this, 'supported_designs' ) );
			add_filter( 'yith_wcan_supported_preset_layouts', array( $this, 'supported_layouts' ) );

			// enable hierarchical tags.
			add_filter( 'woocommerce_taxonomy_args_product_tag', array( $this, 'enabled_hierarchical_product_tags' ), 10, 1 );

			// add premium shortcodes/widgets.
			add_filter( 'yith_wcan_shortcodes', array( $this, 'supported_shortcodes' ) );
			add_filter( 'yith_wcan_widgets', array( $this, 'supported_widgets' ) );

			// register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			parent::__construct();
		}

		/**
		 * Init plugin, by creating main objects
		 *
		 * @return void
		 * @since  1.4
		 */
		public function init() {
			// do startup operations.
			YITH_WCAN_Install_Premium::init();

			// init general classes.
			YITH_WCAN_Presets();
			YITH_WCAN_Sessions();
			YITH_WCAN_Cron();

			// init shortcodes.
			YITH_WCAN_Shortcodes::init();

			// init widgets.
			YITH_WCAN_Widgets::init();

			// init specific classes.
			if ( is_admin() ) {
				$this->admin = new YITH_WCAN_Admin_Premium();
			} else {
				$this->frontend = new YITH_WCAN_Frontend_Premium();
			}
		}

		/**
		 * Register assets used both on frontend and backend
		 * Specific assets may be found on Frontend and Admin classes; here we only register assets that we need to load both
		 * on frontend and backend, maybe because they are needed for Gutenberg editor.
		 *
		 * @return void
		 * @since  4.0
		 */
		public function register_assets() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_style( 'ion.range-slider', YITH_WCAN_URL . 'assets/css/ion.range-slider.css', array(), '2.3.1' );
			wp_register_style( 'yith-wcan-shortcodes', YITH_WCAN_URL . 'assets/css/shortcodes.css', array( 'ion.range-slider' ), YITH_WCAN_VERSION );
			wp_register_script( 'ion.range-slider', YITH_WCAN_URL . 'assets/js/ion.range-slider' . $suffix . '.js', array( 'jquery' ), '2.3.1', true );
			wp_register_script( 'yith-wcan-shortcodes', YITH_WCAN_URL . 'assets/js/yith-wcan-shortcodes' . $suffix . '.js', array( 'jquery', 'ion.range-slider', 'accounting', 'selectWoo' ), YITH_WCAN_VERSION, true );

			if ( is_admin() ) {
				wp_localize_script( 'yith-wcan-shortcodes', 'yith_wcan_shortcodes', array() );
			}
		}

		/**
		 * Add require premium files
		 *
		 * @param array $files Files to include.
		 *
		 * @return array Filtered array of files to include
		 * @since 1.3.2
		 */
		public function require_additional_files( $files ) {
			$files = parent::require_additional_files( $files );

			$files[] = 'class-yith-wcan-install-premium.php';
			$files[] = 'class-yith-wcan-query-premium.php';
			$files[] = 'class-yith-wcan-admin-premium.php';
			$files[] = 'class-yith-wcan-frontend-premium.php';

			return $files;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCAN_DIR . 'plugin-fw/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCAN_INIT, YITH_WCAN_SECRET_KEY, YITH_WCAN_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCAN_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCAN_SLUG, YITH_WCAN_INIT );
		}

		/**
		 * Add additional filter types
		 *
		 * @param array $supported_filters Array of supported filter types.
		 * @return array Filtered array of supported types.
		 */
		public function supported_filters( $supported_filters ) {
			$supported_filters = array_merge(
				$supported_filters,
				array(
					'orderby'      => _x( 'Order by', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'price_range'  => _x( 'Price Range', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'price_slider' => _x( 'Price Slider', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'review'       => _x( 'Review', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
					'stock_sale'   => _x( 'In stock/On sale', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				)
			);

			return $supported_filters;
		}

		/**
		 * Add additional filter designs
		 *
		 * @param array $supported_designs Array of supported designs.
		 * @return array Filtered array of supported designs.
		 */
		public function supported_designs( $supported_designs ) {
			$supported_designs['label'] = _x( 'Label/Image', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' );

			$supported_designs = yith_wcan_merge_in_array(
				$supported_designs,
				array(
					'radio' => _x( 'Radio', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
				'checkbox'
			);

			return $supported_designs;
		}

		/**
		 * Add additional preset layouts
		 *
		 * @param array $supported_layouts Array of supported designs.
		 * @return array Filtered array of supported designs.
		 */
		public function supported_layouts( $supported_layouts ) {
			$supported_layouts['horizontal'] = _x( 'Horizontal', '[Admin] Label in new preset page', 'yith-woocommerce-ajax-navigation' );

			return $supported_layouts;
		}

		/**
		 * Filters available widgets for the plugin
		 *
		 * @param array $widgets Array of available widgets classes.
		 * @return array Array of filtered widgets.
		 */
		public function supported_widgets( $widgets ) {
			$widgets = array_merge(
				$widgets,
				array(
					'YITH_WCAN_Navigation_Widget_Premium',
					'YITH_WCAN_Reset_Navigation_Widget_Premium',
					'YITH_WCAN_Sort_By_Widget',
					'YITH_WCAN_Stock_On_Sale_Widget',
					'YITH_WCAN_List_Price_Filter_Widget',
				)
			);

			$navigation_free = array_search( 'YITH_WCAN_Navigation_Widget', $widgets, true );

			if ( false !== $navigation_free ) {
				unset( $widgets[ $navigation_free ] );
			}

			$reset_free = array_search( 'YITH_WCAN_Reset_Navigation_Widget', $widgets, true );

			if ( false !== $reset_free ) {
				unset( $widgets[ $reset_free ] );
			}

			return $widgets;
		}

		/**
		 * Filters available shortcodes for the plugin
		 *
		 * @param array $shortcodes Array of available shortcodes classes.
		 * @return array Array of filtered shortcodes.
		 */
		public function supported_shortcodes( $shortcodes ) {
			$shortcodes = array_merge(
				$shortcodes,
				array(
					'yith_wcan_active_filters_labels',
					'yith_wcan_mobile_modal_opener',
				)
			);

			return $shortcodes;
		}

		/**
		 * Enable hierarchical behaviour for product tags
		 *
		 * @param array $args Product tag taxonomy parameters.
		 *
		 * @return array Array of filtered params.
		 */
		public function enabled_hierarchical_product_tags( $args ) {
			$args['hierarchical'] = 'yes' === yith_wcan_get_option( 'yith_wcan_enable_hierarchical_tags_link', 'no' ) ? true : false;

			$args['labels']['parent_item']       = __( 'Parent tag', 'yith-woocommerce-ajax-navigation' );
			$args['labels']['parent_item_colon'] = __( 'Parent tag', 'yith-woocommerce-ajax-navigation' );

			return $args;
		}

		/**
		 * Return list of compatible plugins
		 *
		 * @return array Array of compatible plugins
		 *
		 * @since 4.0
		 */
		protected function get_compatible_plugins() {
			if ( empty( $this->supported_plugins ) ) {
				$supported_plugins = parent::get_compatible_plugins();

				$this->supported_plugins = array_merge(
					$supported_plugins,
					array(
						'wc-list-grid' => array(
							'check' => array( 'class_exists', array( 'WC_List_Grid' ) ),
						),
					)
				);
			}

			return apply_filters( 'yith_wcan_compatible_plugins', $this->supported_plugins );
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_WCAN_Premium Main instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

if ( ! function_exists( 'YITH_WCAN_Premium' ) ) {
	/**
	 * Return single instance for YITH_WCAN_Premium class
	 *
	 * @return YITH_WCAN_Premium
	 * @since 4.0.0
	 */
	function YITH_WCAN_Premium() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		return YITH_WCAN_Premium::instance();
	}
}
