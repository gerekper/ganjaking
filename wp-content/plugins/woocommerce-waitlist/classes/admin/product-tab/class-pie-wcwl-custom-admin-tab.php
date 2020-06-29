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
		private $product;
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
			$this->product = $product;
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
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
			if ( WooCommerce_Waitlist_Plugin::is_variable( $this->product ) ) {
				include apply_filters( 'wcwl_include_path_admin_panel_variable', self::$component_path . 'panel-variable.php' );
			} else {
				include apply_filters( 'wcwl_include_path_admin_panel_simple', self::$component_path . 'panel-simple.php' );
			}
		}

		/**
		 * Enqueue any styles and scripts used for the custom tab
		 *
		 * @access public
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_enqueue_script( 'wcwl_admin_custom_tab', WCWL_ENQUEUE_PATH . '/includes/js/src/wcwl_admin_custom_tab.min.js', array(), WCWL_VERSION, true );
			$data = $this->get_data_required_for_js();
			wp_localize_script( 'wcwl_admin_custom_tab', 'wcwl_tab', $data );
		}

		/**
		 * Setup data for JS
		 *
		 * @return array
		 */
		protected function get_data_required_for_js() {
			return array(
				'admin_email'            => get_option( 'woocommerce_email_from_address' ),
				'invalid_email'          => __( 'One or more emails entered appear to be invalid', 'woocommerce-waitlist' ),
				'add_text'               => __( 'Add', 'woocommerce-waitlist' ),
				'no_users_text'          => __( 'No users selected', 'woocommerce-waitlist' ),
				'no_action_text'         => __( 'No action selected', 'woocommerce-waitlist' ),
				'view_profile_text'      => __( 'View User Profile', 'woocommerce-waitlist' ),
				'go_text'                => __( 'Go', 'woocommerce-waitlist' ),
				'update_button_text'     => __( 'Update Options', 'woocommerce-waitlist' ),
				'update_waitlist_notice' => __( 'Waitlists may be appear inaccurate due to an update to variations. Please update the product or refresh the page to update waitlists', 'woocommerce-waitlist' ),
				'current_user'           => get_current_user_id(),
			);
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
		public function return_variation_tab_title( $variation ) {
			$title           = $this->get_variation_name( $variation );
			$count           = get_post_meta( $variation, '_woocommerce_waitlist_count', true );
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
		public function get_variation_name( $variation ) {
			$variation = wc_get_product( $variation );
			$title     = '#' . $variation->get_id();
			foreach ( $variation->get_attributes() as $attribute ) {
				$title    .= ' ' . ucfirst( $attribute ) . ', ';
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
		public function format_date( $date ) {
			if ( is_numeric( $date ) ) {
				return date( 'd M, y', $date );
			} else {
				return '-';
			}
		}

		/**
		 * Get the flag for the users language to display next to their name in the waitlist table
		 *
		 * @param $user_id
		 * @param $product_id
		 *
		 * @return string
		 */
		public function get_user_language_flag( $user_id, $product_id ) {
			if ( function_exists( 'icl_object_id' ) ) {
				$user_languages = get_user_meta( $user_id, 'wcwl_languages', true );
				if ( isset( $user_languages[ $product_id ] ) ) {
					global $sitepress;
					$flag_url = $sitepress->get_flag_url( $user_languages[ $product_id ] );

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
		public function retrieve_and_sort_archives( $product_id ) {
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