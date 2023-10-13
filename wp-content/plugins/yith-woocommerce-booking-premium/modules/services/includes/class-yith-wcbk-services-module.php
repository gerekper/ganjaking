<?php
/**
 * Class YITH_WCBK_Services_Module
 * Handle the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Module' ) ) {
	/**
	 * YITH_WCBK_Services_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Services_Module extends YITH_WCBK_Module {

		const KEY = 'services';

		/**
		 * On load.
		 */
		public function on_load() {
			add_filter( 'yith_wcbk_panel_configuration_sub_tabs', array( $this, 'add_services_tab' ), 10, 1 );
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ), 10, 1 );
			add_filter( 'yith_wcbk_language_default_labels', array( $this, 'filter_language_labels' ), 10, 1 );

			YITH_WCBK_Service_Tax_Admin::get_instance();
			YITH_WCBK_Services_Products::get_instance();
			YITH_WCBK_Services_Bookings::get_instance();
			YITH_WCBK_Services_Shortcodes::get_instance();
		}

		/**
		 * On activate.
		 */
		public function on_activate() {
			$caps = yith_wcbk_get_capabilities( 'tax', YITH_WCBK_Post_Types::SERVICE_TAX );
			yith_wcbk_add_capabilities( $caps );
		}

		/**
		 * On deactivate.
		 */
		public function on_deactivate() {
			$caps = yith_wcbk_get_capabilities( 'tax', YITH_WCBK_Post_Types::SERVICE_TAX );
			yith_wcbk_remove_capabilities( $caps );
		}

		/**
		 * Register post type.
		 */
		public function on_register_post_types() {
			$objects = apply_filters( 'yith_wcbk_taxonomy_objects_booking_service', array( 'product', YITH_WCBK_Post_Types::BOOKING ) );
			$caps    = yith_wcbk_get_capabilities( 'tax', YITH_WCBK_Post_Types::SERVICE_TAX );

			$args = apply_filters(
				'yith_wcbk_taxonomy_args_booking_service',
				array(
					'hierarchical'      => true,
					'label'             => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
					'labels'            => array(
						'name'                       => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
						'singular_name'              => __( 'Booking Service', 'yith-booking-for-woocommerce' ),
						'menu_name'                  => _x( 'Booking Services', 'Admin menu name', 'yith-booking-for-woocommerce' ),
						'all_items'                  => __( 'All Booking Services', 'yith-booking-for-woocommerce' ),
						'edit_item'                  => __( 'Edit Booking Service', 'yith-booking-for-woocommerce' ),
						'view_item'                  => __( 'View Booking Service', 'yith-booking-for-woocommerce' ),
						'update_item'                => __( 'Update Booking Service', 'yith-booking-for-woocommerce' ),
						'add_new_item'               => __( 'Add New Booking Service', 'yith-booking-for-woocommerce' ),
						'new_item_name'              => __( 'New Booking Service Name', 'yith-booking-for-woocommerce' ),
						'parent_item'                => __( 'Parent Booking Service', 'yith-booking-for-woocommerce' ),
						'parent_item_colon'          => __( 'Parent Booking Service:', 'yith-booking-for-woocommerce' ),
						'search_items'               => __( 'Search Booking Services', 'yith-booking-for-woocommerce' ),
						'separate_items_with_commas' => __( 'Separate booking services with commas', 'yith-booking-for-woocommerce' ),
						'add_or_remove_items'        => __( 'Add or remove booking services', 'yith-booking-for-woocommerce' ),
						'choose_from_most_used'      => __( 'Choose among the most popular booking services', 'yith-booking-for-woocommerce' ),
						'not_found'                  => __( 'No booking service found.', 'yith-booking-for-woocommerce' ),
						'back_to_items'              => __( 'Go to all services', 'yith-booking-for-woocommerce' ),
					),
					'show_ui'           => true,
					'query_var'         => true,
					'show_in_nav_menus' => false,
					'show_in_menu'      => false,
					'show_admin_column' => true,
					'capabilities'      => $caps,
					'rewrite'           => true,
				)
			);

			register_taxonomy( YITH_WCBK_Post_Types::SERVICE_TAX, $objects, $args );
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
			$styles['yith-wcbk-services-admin-services'] = array(
				'src'     => $this->get_url( 'assets/css/admin/services.css' ),
				'context' => 'admin',
				'deps'    => array( 'yith-wcbk' ),
				'enqueue' => 'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX,
			);

			return $styles;
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
			$scripts['yith-wcbk-services-admin-services'] = array(
				'src'     => $this->get_url( 'assets/js/admin/services.js' ),
				'context' => 'admin',
				'deps'    => array( 'jquery' ),
				'enqueue' => 'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX,
			);

			return $scripts;
		}

		/**
		 * Add services tab.
		 *
		 * @param array $sub_tabs The sub tabs.
		 *
		 * @return array
		 */
		public function add_services_tab( array $sub_tabs ): array {
			$sub_tabs['configuration-services'] = array(
				'title'        => _x( 'Services', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'description'  => __( 'A service is anything you can think of that you may want to provide with your bookable products; it can be optional or not. Example: breakfast, WiFi, air-conditioning.', 'yith-booking-for-woocommerce' ),
				'options_path' => $this->get_path( 'options/configuration-services-options.php' ),
			);

			return $sub_tabs;
		}

		/**
		 * Register data stores
		 *
		 * @param array $data_stores WooCommerce Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['yith-booking-service'] = 'YITH_WCBK_Service_Data_Store';

			return $data_stores;
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
				'additional-services' => __( 'Additional Services', 'yith-booking-for-woocommerce' ),
				'booking-services'    => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
				'included-services'   => __( 'Included Services', 'yith-booking-for-woocommerce' ),
				'services'            => __( 'Services', 'yith-booking-for-woocommerce' ),
			);

			return array_merge( $labels, $module_labels );
		}
	}
}
