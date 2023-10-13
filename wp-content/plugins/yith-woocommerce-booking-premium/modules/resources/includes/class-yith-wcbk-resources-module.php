<?php
/**
 * Class YITH_WCBK_Resources_Module
 * Handle the Resources module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resources_Module' ) ) {
	/**
	 * YITH_WCBK_Resources_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Resources_Module extends YITH_WCBK_Module {

		const KEY = 'resources';

		/**
		 * On load.
		 */
		public function on_load() {
			add_filter( 'yith_wcbk_panel_configuration_sub_tabs', array( $this, 'add_resources_tab' ), 10, 1 );
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ), 10, 1 );
			add_filter( 'yith_wcbk_post_types', array( $this, 'add_post_type' ), 10, 1 );
			add_action( 'delete_post', array( $this, 'handle_delete_post' ), 10, 1 );

			YITH_WCBK_Resources_Products::get_instance();
			YITH_WCBK_Resources_Bookings::get_instance();
		}

		/**
		 * On activate.
		 */
		public function on_activate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::RESOURCE );
			yith_wcbk_add_capabilities( $caps );
		}

		/**
		 * On deactivate.
		 */
		public function on_deactivate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::RESOURCE );
			yith_wcbk_remove_capabilities( $caps );
		}

		/**
		 * Register post type.
		 */
		public function on_register_post_types() {
			$post_type_args = array(
				'labels'              => array(
					'menu_name'          => __( 'Resources', 'yith-booking-for-woocommerce' ),
					'all_items'          => __( 'Resources', 'yith-booking-for-woocommerce' ),
					'name'               => __( 'Resources', 'yith-booking-for-woocommerce' ),
					'singular_name'      => __( 'Resource', 'yith-booking-for-woocommerce' ),
					'add_new'            => __( 'Add new resource', 'yith-booking-for-woocommerce' ),
					'add_new_item'       => __( 'New resource', 'yith-booking-for-woocommerce' ),
					'edit_item'          => __( 'Edit resource', 'yith-booking-for-woocommerce' ),
					'view_item'          => __( 'View this resource', 'yith-booking-for-woocommerce' ),
					'search_items'       => __( 'Search resource', 'yith-booking-for-woocommerce' ),
					'not_found'          => __( 'No resources found', 'yith-booking-for-woocommerce' ),
					'not_found_in_trash' => __( 'No resources found in trash', 'yith-booking-for-woocommerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => YITH_WCBK_Post_Types::RESOURCE,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
				'show_in_menu'        => false,
			);

			register_post_type( YITH_WCBK_Post_Types::RESOURCE, $post_type_args );
		}

		/**
		 * Load post type handlers.
		 */
		public function on_post_type_handlers_loaded() {
			require_once $this->get_path( 'includes/class-yith-wcbk-resource-post-type-admin.php' );
		}

		/**
		 * Add resources tab.
		 *
		 * @param array $sub_tabs The sub tabs.
		 *
		 * @return array
		 */
		public function add_resources_tab( array $sub_tabs ): array {
			$sub_tabs['configuration-resources'] = array(
				'title'        => _x( 'Resources', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'description'  => __( 'A resource is basically anything you can think of that you may need to share among multiple bookable products or something that you want your users to select for the product.', 'yith-booking-for-woocommerce' ),
				'options_path' => $this->get_path( 'options/configuration-resources-options.php' ),
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
		public static function register_data_stores( $data_stores ) {
			$data_stores['yith-booking-resource'] = 'YITH_WCBK_Resource_Data_Store';

			return $data_stores;
		}

		/**
		 * Add post type to allow handling meta-box saving through data stores.
		 *
		 * @param array $post_types Post types.
		 *
		 * @return array
		 */
		public function add_post_type( array $post_types ): array {
			$post_types['resource'] = YITH_WCBK_Post_Types::RESOURCE;

			return $post_types;
		}

		/**
		 * Handle post delete
		 *
		 * @param int $id ID of post being deleted.
		 *
		 * @since 4.0.0
		 */
		public function handle_delete_post( $id ) {
			if ( ! $id ) {
				return;
			}

			if ( YITH_WCBK_Post_Types::RESOURCE === get_post_type( $id ) ) {
				yith_wcbk_clear_resource_related_caches( $id );

				global $wpdb;
				$wpdb->delete( $wpdb->yith_wcbk_product_resources, array( 'resource_id' => $id ) );
				// We don't want to delete resources data on bookings, to keep them as historical data (also just only the ID).
			}
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
			$styles['yith-wcbk-resources-product'] = array(
				'src'     => $this->get_url( 'assets/css/admin/product.css' ),
				'context' => 'admin',
				'enqueue' => 'product',
			);

			$styles['yith-wcbk-resources-booking'] = array(
				'src'     => $this->get_url( 'assets/css/admin/booking.css' ),
				'context' => 'admin',
				'enqueue' => YITH_WCBK_Post_Types::BOOKING,
			);

			$styles['yith-wcbk-resources-resources'] = array(
				'src'     => $this->get_url( 'assets/css/admin/resources.css' ),
				'context' => 'admin',
				'enqueue' => array( 'edit-' . YITH_WCBK_Post_Types::RESOURCE, YITH_WCBK_Post_Types::RESOURCE ),
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
			$scripts['yith-wcbk-resources-admin-product'] = array(
				'src'              => $this->get_url( 'assets/js/admin/product.js' ),
				'context'          => 'admin',
				'deps'             => array( 'jquery', 'yith-wcbk-admin-ajax', 'yith-ui', 'wp-util', 'wp-i18n', 'lodash' ),
				'localize_globals' => array( 'bk', 'wcbk_admin' ),
				'enqueue'          => 'product',
			);

			$scripts['yith-wcbk-resources-booking-form'] = array(
				'src'              => $this->get_url( 'assets/js/booking-form.js' ),
				'context'          => 'common',
				'deps'             => array( 'jquery', 'yith-wcbk-ajax' ),
				'localize_globals' => array( 'bk', 'wcbk_admin' ),
				'enqueue'          => 'product',
			);

			if ( isset( $scripts['yith-wcbk-booking-form'] ) ) {
				$scripts['yith-wcbk-booking-form']['deps'][] = 'yith-wcbk-resources-booking-form';
			}

			return $scripts;
		}
	}
}
