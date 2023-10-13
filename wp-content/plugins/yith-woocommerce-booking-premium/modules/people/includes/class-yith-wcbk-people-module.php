<?php
/**
 * Class YITH_WCBK_People_Module
 * Handle the People module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_People_Module' ) ) {
	/**
	 * YITH_WCBK_People_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_People_Module extends YITH_WCBK_Module {

		const KEY = 'people';

		/**
		 * On load.
		 */
		public function on_load() {
			add_filter( 'yith_wcbk_post_types', array( $this, 'add_post_type' ), 10, 1 );
			add_filter( 'yith_wcbk_panel_configuration_sub_tabs', array( $this, 'add_people_tab' ), 10, 1 );
			add_filter( 'yith_wcbk_language_default_labels', array( $this, 'filter_language_labels' ), 10, 1 );

			YITH_WCBK_People_Products::get_instance();
			YITH_WCBK_People_Bookings::get_instance();
		}

		/**
		 * On activate.
		 */
		public function on_activate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::PERSON_TYPE );
			yith_wcbk_add_capabilities( $caps );
		}

		/**
		 * On deactivate.
		 */
		public function on_deactivate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::PERSON_TYPE );
			yith_wcbk_remove_capabilities( $caps );
		}

		/**
		 * Register post type.
		 */
		public function on_register_post_types() {
			$person_type_args = array(
				'labels'              => array(
					'menu_name'          => _x( 'People', 'Admin menu name', 'yith-booking-for-woocommerce' ),
					'all_items'          => __( 'People', 'yith-booking-for-woocommerce' ),
					'name'               => __( 'People', 'yith-booking-for-woocommerce' ),
					'singular_name'      => __( 'Person', 'yith-booking-for-woocommerce' ),
					'add_new'            => __( 'Add new type', 'yith-booking-for-woocommerce' ),
					'add_new_item'       => __( 'New type', 'yith-booking-for-woocommerce' ),
					'edit_item'          => __( 'Edit type', 'yith-booking-for-woocommerce' ),
					'view_item'          => __( 'View this type', 'yith-booking-for-woocommerce' ),
					'search_items'       => __( 'Search person type', 'yith-booking-for-woocommerce' ),
					'not_found'          => __( 'Type not found', 'yith-booking-for-woocommerce' ),
					'not_found_in_trash' => __( 'Type not found in trash', 'yith-booking-for-woocommerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => YITH_WCBK_Post_Types::PERSON_TYPE,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title', 'editor' ),
				'show_in_menu'        => false,
			);

			register_post_type( YITH_WCBK_Post_Types::PERSON_TYPE, $person_type_args );
		}

		/**
		 * Load post type handlers.
		 */
		public function on_post_type_handlers_loaded() {
			require_once $this->get_path( 'includes/class-yith-wcbk-person-type-post-type-admin.php' );
		}

		/**
		 * Add people tab.
		 *
		 * @param array $sub_tabs The sub tabs.
		 *
		 * @return array
		 */
		public function add_people_tab( array $sub_tabs ): array {
			$sub_tabs['configuration-people'] = array(
				'title'        => _x( 'People', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'description'  => __( 'You can use people types to allow your customers to enter a different number of guests for each type. Example: adult, teenager, child.', 'yith-booking-for-woocommerce' ),
				'options_path' => $this->get_path( 'options/configuration-people-options.php' ),
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
			$post_types['person_type'] = YITH_WCBK_Post_Types::PERSON_TYPE;

			return $post_types;
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
				'people'       => __( 'People', 'yith-booking-for-woocommerce' ),
				'total-people' => __( 'Total people', 'yith-booking-for-woocommerce' ),
			);

			return array_merge( $labels, $module_labels );
		}
	}
}
