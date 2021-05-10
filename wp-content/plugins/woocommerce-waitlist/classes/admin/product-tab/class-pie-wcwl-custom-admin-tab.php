<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Custom_Tab' ) ) {
	/**
	 * Pie_WCWL_Custom_Tab
	 *
	 * @package WooCommerce Waitlist
	 */
	class Pie_WCWL_Custom_Tab {

		/**
		 * WC_Product
		 *
		 * @var object
		 */
		public static $product;
		/**
		 * Path to admin tab components
		 *
		 * @var string
		 */
		public static $component_path = '';

		/**
		 * Assigns the settings that have been passed in to the appropriate parameters
		 *
		 * @access protected
		 *
		 * @param  object $product current product
		 */
		public function __construct( $product ) {
			self::$product        = $product;
			self::$component_path = plugin_dir_path( __FILE__ ) . 'components/';
		}

		/**
		 * Initialise waitlist tab
		 */
		public function init() {
			$this->load_hooks();
		}

		/**
		 * Load hooks
		 */
		protected function load_hooks() {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_waitlist_tab_to_product_options_panel' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'output_content_for_waitlist_panel' ) );
		}

		/**
		 * Add custom waitlist tab to the product
		 */
		public function add_waitlist_tab_to_product_options_panel() {
			include apply_filters( 'wcwl_include_path_admin_panel_side_tab', self::$component_path . 'panel-side-tab.php' );
		}

		/**
		 * Output the HTML required for the custom tab
		 */
		public function output_content_for_waitlist_panel() {
			if ( WooCommerce_Waitlist_Plugin::is_variable( self::$product ) ) {
				include apply_filters( 'wcwl_include_path_admin_panel_variable', self::$component_path . 'panel-variable.php' );
			} else {
				include apply_filters( 'wcwl_include_path_admin_panel_simple', self::$component_path . 'panel-simple.php' );
			}
		}

		/**
		 * Return title to be applied to the custom tab for variations
		 *
		 * @access public
		 *
		 * @param $variation
		 *
		 * @return string
		 */
		public static function return_variation_tab_title( $variation ) {
			$title = self::get_variation_name( $variation );
			$count = get_post_meta( $variation, '_woocommerce_waitlist_count', true );
			if ( ! $count ) {
				$count = 0;
			}
			$variation_title = $title . ' : <span class="wcwl_count">' . $count . '</span>';

			return apply_filters( 'wcwl_variation_tab_title', $variation_title, $variation );
		}

		/**
		 * Get the name of the variation that matches the given ID - returning each attribute
		 * To be used as the title for each variation waitlist on the tab
		 *
		 * @param  int $variation the current variation
		 *
		 * @access public
		 * @return string the attribute of the required variation
		 */
		public static function get_variation_name( $variation ) {
			$variation = wc_get_product( $variation );
			$title     = '#' . $variation->get_id();
			foreach ( $variation->get_attributes() as $attribute ) {
				$title .= ' ' . ucfirst( $attribute ) . ', ';
			}

			return rtrim( $title, ', ' );
		}

		/**
		 * Check we have a valid date to return, if so format as required
		 *
		 * @param $date
		 *
		 * @return bool|string
		 */
		public static function format_date( $date ) {
			if ( is_numeric( $date ) ) {
				return date( 'd M, y', $date );
			} else {
				return '-';
			}
		}

		/**
		 * Get the flag for the users language to display next to their name in the waitlist table
		 *
		 * @param $email
		 * @param $product_id
		 *
		 * @return string
		 */
		public static function get_user_language_flag( $email, $product_id ) {
			if ( function_exists( 'icl_object_id' ) ) {
				$language = wcwl_get_user_language( $email, $product_id );
				if ( $language ) {
					global $sitepress;
					if ( ! isset( $sitepress ) ) {
						return '';
					}
					$flag_url = $sitepress->get_flag_url( $language );
					return '  <img src="' . $flag_url . '" />';
				}
			}
			return '';
		}

		/**
		 * Retrieve archives for current product from database and sort in reverse time order
		 *
		 * @param  int $product_id current product ID
		 *
		 * @return mixed
		 */
		public static function retrieve_and_sort_archives( $product_id ) {
			$archives = get_post_meta( $product_id, 'wcwl_waitlist_archive', true );
			if ( empty( $archives ) ) {
				return array();
			}
			if ( ! get_option( '_' . WCWL_SLUG . '_metadata_updated' ) ) {
				$archives = Pie_WCWL_Admin_Ajax::fix_multiple_entries_for_days( $archives, $product_id );
			}

			return $archives;
		}
	}
}
