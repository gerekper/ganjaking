<?php
/**
 * Class YITH_WCBK_Search_Forms_Module
 * Handle the Search Forms module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Forms_Module' ) ) {
	/**
	 * YITH_WCBK_Search_Forms_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Search_Forms_Module extends YITH_WCBK_Module {

		const KEY = 'search-forms';

		/**
		 * On load.
		 */
		public function on_load() {
			add_filter( 'yith_wcbk_panel_configuration_sub_tabs', array( $this, 'add_configuration_sub_tabs' ), 10, 1 );
			add_filter( 'yith_wcbk_post_types', array( $this, 'add_post_type' ), 10, 1 );
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ), 10, 1 );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_filter( 'yith_wcbk_language_default_labels', array( $this, 'filter_language_labels' ), 10, 1 );

			if ( yith_wcbk()->is_request( 'frontend' ) ) {
				YITH_WCBK_Search_Forms_Frontend::get_instance();
			}

			YITH_WCBK_Search_Forms_Ajax::get_instance();
			YITH_WCBK_Search_Forms_Shortcodes::get_instance();
		}

		/**
		 * On activate.
		 */
		public function on_activate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::SEARCH_FORM );
			yith_wcbk_add_capabilities( $caps );
		}

		/**
		 * On deactivate.
		 */
		public function on_deactivate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::SEARCH_FORM );
			yith_wcbk_remove_capabilities( $caps );
		}

		/**
		 * Register post type.
		 */
		public function on_register_post_types() {
			$args = array(
				'labels'              => array(
					'menu_name'          => _x( 'Search Forms', 'Admin menu name', 'yith-booking-for-woocommerce' ),
					'all_items'          => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
					'name'               => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
					'singular_name'      => __( 'Search form', 'yith-booking-for-woocommerce' ),
					'add_new'            => __( 'Add search form', 'yith-booking-for-woocommerce' ),
					'add_new_item'       => __( 'New search form', 'yith-booking-for-woocommerce' ),
					'edit_item'          => __( 'Edit search form', 'yith-booking-for-woocommerce' ),
					'view_item'          => __( 'View search form', 'yith-booking-for-woocommerce' ),
					'search_items'       => _x( 'Search', 'Search label for "search forms"', 'yith-booking-for-woocommerce' ),
					'not_found'          => __( 'Search form not found', 'yith-booking-for-woocommerce' ),
					'not_found_in_trash' => __( 'Search form not found in trash', 'yith-booking-for-woocommerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => YITH_WCBK_Post_Types::SEARCH_FORM,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
				'show_in_menu'        => false,
				'show_in_rest'        => true,
				'rest_base'           => 'booking-search-forms',
			);

			register_post_type( YITH_WCBK_Post_Types::SEARCH_FORM, $args );
		}

		/**
		 * Load post type handlers.
		 */
		public function on_post_type_handlers_loaded() {
			require_once $this->get_path( 'includes/class-yith-wcbk-search-form-post-type-admin.php' );
		}

		/**
		 * Add resources tab.
		 *
		 * @param array $sub_tabs The sub tabs.
		 *
		 * @return array
		 */
		public function add_configuration_sub_tabs( array $sub_tabs ): array {
			$sub_tabs['configuration-search-forms'] = array(
				'title'        => _x( 'Search Forms', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'description'  => __( 'Showing a search form helps users quickly find the bookable product that best meets their needs. You can create endless search forms which you can later add in a widget or insert as a shortcode.', 'yith-booking-for-woocommerce' ),
				'options_path' => $this->get_path( 'options/configuration-search-forms-options.php' ),
			);

			return $sub_tabs;
		}

		/**
		 * Add post type to allow handling meta-box saving through data stores.
		 *
		 * @param array $post_types Post types.
		 *
		 * @return array
		 */
		public function add_post_type( array $post_types ): array {
			$post_types['search_form'] = YITH_WCBK_Post_Types::SEARCH_FORM;

			return $post_types;
		}

		/**
		 * Register data stores
		 *
		 * @param array $data_stores WooCommerce Data Stores.
		 *
		 * @return array
		 */
		public static function register_data_stores( $data_stores ) {
			$data_stores['yith-booking-search-form'] = 'YITH_WCBK_Search_Form_Data_Store';

			return $data_stores;
		}

		/**
		 * Register Widgets
		 */
		public function register_widgets() {
			register_widget( 'YITH_WCBK_Search_Form_Widget' );
		}

		/**
		 * Add admin styles.
		 *
		 * @param array  $styles  The styles.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function filter_styles( array $styles, string $context ): array {
			$module_styles = array(
				'yith-wcbk-admin-booking-search-form' => array(
					'src'     => $this->get_url( 'assets/css/admin/search-form.css' ),
					'context' => 'admin',
					'deps'    => array( 'yith-wcbk' ),
					'enqueue' => YITH_WCBK_Post_Types::SEARCH_FORM,
				),
				'yith-wcbk-search-form'               => array(
					'src'     => $this->get_url( 'assets/css/search-form.css' ),
					'context' => 'frontend',
					'deps'    => array( 'yith-wcbk', 'yith-wcbk-fields' ),
					'enqueue' => true,
				),
			);

			return array_merge( $styles, $module_styles );
		}

		/**
		 * Add admin scripts.
		 *
		 * @param array  $scripts The scripts.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function filter_scripts( array $scripts, string $context ): array {
			$module_scripts = array(
				'yith-wcbk-admin-booking-search-form' => array(
					'src'     => $this->get_url( 'assets/js/admin/search-form.js' ),
					'context' => 'admin',
					'deps'    => array( 'jquery', 'jquery-ui-sortable' ),
					'enqueue' => YITH_WCBK_Post_Types::SEARCH_FORM,
				),
				'yith-wcbk-booking-search-form'       => array(
					'src'              => $this->get_url( 'assets/js/search-form.js' ),
					'context'          => 'frontend',
					'deps'             => array( 'jquery', 'jquery-blockui', 'yith-wcbk-popup', 'select2', 'yith-wcbk-people-selector', 'yith-wcbk-services-selector', 'yith-wcbk-fields', 'yith-wcbk-ajax' ),
					'localize_globals' => array( 'bk' ),
				),
			);

			return array_merge( $scripts, $module_scripts );
		}

		/**
		 * Filter labels
		 *
		 * @param array $labels The labels.
		 *
		 * @return array
		 */
		public function filter_language_labels( array $labels ): array {
			$module_labels = array(
				'distance' => __( 'Distance (Km)', 'yith-booking-for-woocommerce' ),
				'location' => __( 'Location', 'yith-booking-for-woocommerce' ),
			);

			return array_merge( $labels, $module_labels );
		}
	}
}
