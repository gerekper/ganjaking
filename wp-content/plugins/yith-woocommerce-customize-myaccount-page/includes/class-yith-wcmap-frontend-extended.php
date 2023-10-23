<?php
/**
 * Frontend class extended
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Frontend_Extended' ) ) {
	/**
	 * Frontend class extended.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Frontend_Extended extends YITH_WCMAP_Frontend {

		/**
		 * Security class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Security|null
		 */
		public $security = null;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();
			$this->init_security_class();

			// Template hooks.
			add_filter( 'yith_wcmap_myaccount_menu_template_args', array( $this, 'add_menu_template_args' ), 10, 1 );
			add_filter( 'yith_wcmap_print_single_endpoint_args', array( $this, 'add_single_endpoint_args' ), 10, 3 );
		}

		/**
		 * Init security class
		 *
		 * @since 3.12.0
		 * @return void
		 */
		protected function init_security_class() {
			$this->security = new YITH_WCMAP_Security();
		}

		/**
		 * Enqueue scripts and styles
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			if ( ! $this->is_myaccount ) {
				return;
			}

			parent::enqueue_scripts();
			// Get AJAX Loader.
			$loader = YITH_WCMAP_ASSETS_URL . '/images/ajax-loader.gif';
			if ( 'custom' === get_option( 'yith_wcmap_ajax_loader_style', 'default' ) && get_option( 'yith_wcmap_ajax_loader_custom_icon', '' ) ) {
				$loader = esc_url( get_option( 'yith_wcmap_ajax_loader_custom_icon', '' ) );
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'ywcmap-frontend', YITH_WCMAP_ASSETS_URL . '/js/ywcmap-frontend' . $suffix . '.js', array( 'jquery', 'wp-util' ), YITH_WCMAP_VERSION, true );
			wp_localize_script(
				'ywcmap-frontend',
				'ywcmap',
				array(
					'ajaxNavigation'       => 'yes' === get_option( 'yith_wcmap_enable_ajax_navigation', 'no' ),
					/**
					 * APPLY_FILTERS: yith_wcmap_enable_ajax_navigation_scroll
					 *
					 * Filters whether to enable the scroll in the Ajax navigation.
					 *
					 * @param bool $enable_ajax_navigation_scroll Whether to enable the scroll in the Ajax navigation or not.
					 *
					 * @return bool
					 */
					'ajaxNavigationScroll' => apply_filters( 'yith_wcmap_enable_ajax_navigation_scroll', true ),
					/**
					 * APPLY_FILTERS: yith_wcmap_main_content_selector
					 *
					 * Filters the main content selector in the My Account page.
					 *
					 * @param string $main_content_selector Main content selector.
					 *
					 * @return string
					 */
					'contentSelector'      => apply_filters( 'yith_wcmap_main_content_selector', '#content, div.woocommerce' ),
					'ajaxLoader'           => $loader,
				)
			);
		}

		/**
		 * Extended template args
		 *
		 * @since  3.12.0
		 * @param array $args An array of template arguments.
		 * @return array
		 */
		public function add_menu_template_args( $args ) {
			// Build wrap id and class.
			$position = get_option( 'yith_wcmap_menu_position', 'vertical-left' );
			$classes  = array(
				'position-' . $position,
				'layout-simple',
				'position-' . ( 'vertical-left' === $position ? 'left' : 'right' ), // Backward compatibility.
			);

			$opts = get_option( 'yith_wcmap_avatar', array() );
			$size = ! empty( $opts['avatar_size'] ) ? absint( $opts['avatar_size'] ) : 120;

			$args = array_merge(
				$args,
				array(
					'wrap_classes' => implode( ' ', $classes ),
					'wrap_id'      => 'horizontal' === $position ? 'my-account-menu-tab' : 'my-account-menu',
					'avatar_size'  => $size,
				)
			);

			return $args;
		}

		/**
		 * Add single endpoint menu arguments
		 *
		 * @since  3.12.0
		 * @param array  $args    The item endpoint arguments.
		 * @param string $item    The item to print.
		 * @param array  $options The item options.
		 * @return array
		 */
		public function add_single_endpoint_args( $args, $item, $options ) {
			if ( 'yes' === get_option( 'yith_wcmap_enable_ajax_navigation', 'no' ) ) {
				$args['classes'][] = 'has-ajax-navigation';
			}
			return $args;
		}
	}
}
