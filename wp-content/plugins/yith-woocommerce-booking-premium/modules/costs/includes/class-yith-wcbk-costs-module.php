<?php
/**
 * Class YITH_WCBK_Costs_Module
 * Handle the Costs module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Costs_Module' ) ) {
	/**
	 * YITH_WCBK_Costs_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Costs_Module extends YITH_WCBK_Module {

		const KEY = 'costs';

		/**
		 * On load.
		 */
		public function on_load() {
			add_filter( 'yith_wcbk_post_types', array( $this, 'add_post_type' ), 10, 1 );
			add_filter( 'yith_wcbk_panel_configuration_sub_tabs', array( $this, 'add_costs_tab' ), 10, 1 );

			YITH_WCBK_Costs_Products::get_instance();
		}

		/**
		 * On activate.
		 */
		public function on_activate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::EXTRA_COST );
			yith_wcbk_add_capabilities( $caps );
		}

		/**
		 * On deactivate.
		 */
		public function on_deactivate() {
			$caps = yith_wcbk_get_capabilities( 'post', YITH_WCBK_Post_Types::EXTRA_COST );
			yith_wcbk_remove_capabilities( $caps );
		}

		/**
		 * Register post type.
		 */
		public function on_register_post_types() {
			$extra_cost_args = array(
				'labels'              => array(
					'menu_name'          => _x( 'Extra Costs', 'Admin menu name', 'yith-booking-for-woocommerce' ),
					'all_items'          => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
					'name'               => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
					'singular_name'      => __( 'Extra Cost', 'yith-booking-for-woocommerce' ),
					'add_new'            => __( 'Add New Extra Cost', 'yith-booking-for-woocommerce' ),
					'add_new_item'       => __( 'New Extra Cost', 'yith-booking-for-woocommerce' ),
					'edit_item'          => __( 'Edit Extra Cost', 'yith-booking-for-woocommerce' ),
					'view_item'          => __( 'View this extra cost', 'yith-booking-for-woocommerce' ),
					'search_items'       => __( 'Search extra cost', 'yith-booking-for-woocommerce' ),
					'not_found'          => __( 'Extra cost not found', 'yith-booking-for-woocommerce' ),
					'not_found_in_trash' => __( 'Extra cost not found in trash', 'yith-booking-for-woocommerce' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => YITH_WCBK_Post_Types::EXTRA_COST,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title', 'editor' ),
				'show_in_menu'        => false,
			);

			register_post_type( YITH_WCBK_Post_Types::EXTRA_COST, $extra_cost_args );
		}

		/**
		 * Load post type handlers.
		 */
		public function on_post_type_handlers_loaded() {
			require_once $this->get_path( 'includes/class-yith-wcbk-extra-cost-post-type-admin.php' );
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
			$scripts['yith-wcbk-costs-admin-product'] = array(
				'src'     => $this->get_url( 'assets/js/admin/product.js' ),
				'context' => 'admin',
				'deps'    => array( 'jquery' ),
				'enqueue' => 'product',
			);

			return $scripts;
		}

		/**
		 * Add people tab.
		 *
		 * @param array $sub_tabs The sub tabs.
		 *
		 * @return array
		 */
		public function add_costs_tab( array $sub_tabs ): array {
			$sub_tabs['configuration-costs'] = array(
				'title'        => _x( 'Costs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'description'  => __( 'You can use global costs to add specific costs to your bookable products. Example: cleaning fee, daily tax.', 'yith-booking-for-woocommerce' ),
				'options_path' => $this->get_path( 'options/configuration-costs-options.php' ),
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
			$post_types['extra_cost'] = YITH_WCBK_Post_Types::EXTRA_COST;

			return $post_types;
		}
	}
}
