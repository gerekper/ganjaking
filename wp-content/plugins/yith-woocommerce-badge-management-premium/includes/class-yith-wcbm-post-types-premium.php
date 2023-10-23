<?php
/**
 * Post Types class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Classes
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBM' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'YITH_WCBM_Post_Types_Premium' ) ) {
	/**
	 * Post Types class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBM_Post_Types_Premium extends YITH_WCBM_Post_Types {

		/**
		 * Badge Rule Post Type
		 *
		 * @var string
		 */
		public static $badge_rule = 'ywcbm-badge-rule';

		/**
		 * Register Badge custom post type with options metabox
		 *
		 * @return   void
		 * @since    1.0
		 */
		public static function post_type_register() {
			parent::post_type_register();

			$labels = array(
				self::$badge_rule => array(
					'name'               => __( 'Badge Rules', 'yith-woocommerce-badges-management' ),
					'singular_name'      => __( 'Badge Rule', 'yith-woocommerce-badges-management' ),
					'add_new'            => __( 'Add Badge Rule', 'yith-woocommerce-badges-management' ),
					'add_new_item'       => __( 'Add new Badge Rule', 'yith-woocommerce-badges-management' ),
					'edit_item'          => __( 'Edit Badge Rule', 'yith-woocommerce-badges-management' ),
					'view_item'          => __( 'View Badge Rule', 'yith-woocommerce-badges-management' ),
					'not_found'          => __( 'Badge Rule not found', 'yith-woocommerce-badges-management' ),
					'not_found_in_trash' => __( 'Badge Rule not found in trash', 'yith-woocommerce-badges-management' ),
					'edit'               => __( 'Edit', 'yith-woocommerce-badges-management' ),
					'new_item'           => __( 'New Badge Rule', 'yith-woocommerce-badges-management' ),
					'view'               => __( 'View Badge Rule', 'yith-woocommerce-badges-management' ),
					'search_items'       => __( 'Search Badge Rules', 'yith-woocommerce-badges-management' ),
					'parent'             => __( 'Parent Badge Rules', 'yith-woocommerce-badges-management' ),
					'menu_name'          => _x( 'Badge Rules', 'Admin menu name', 'yith-woocommerce-badges-management' ),
					'all_items'          => __( 'All Badge Rules', 'yith-woocommerce-badges-management' ),
					'view_items'         => __( 'View Badge Rules', 'yith-woocommerce-badges-management' ),
					'archives'           => __( 'Badge Rule archives', 'yith-woocommerce-badges-management' ),
					'attributes'         => __( 'Badge Rule attributes', 'yith-woocommerce-badges-management' ),
					'filter_items_list'  => __( 'Filter badge rule list', 'yith-woocommerce-badges-management' ),
					'filter_by_date'     => __( 'Filter by date', 'yith-woocommerce-badges-management' ),
					'items_list'         => __( 'Badge Rule list', 'yith-woocommerce-badges-management' ),
					'item_published'     => __( 'Badge Rule saved', 'yith-woocommerce-badges-management' ),
					'item_updated'       => __( 'Badge Rule saved', 'yith-woocommerce-badges-management' ),
				),
			);

			$types_args = array(
				self::$badge_rule => array(
					'labels'              => $labels[ self::$badge_rule ],
					'public'              => false,
					'show_ui'             => true,
					'exclude_from_search' => true,
					'capability_type'     => array( 'badge', 'badges' ),
					'map_meta_cap'        => true,
					'rewrite'             => true,
					'has_archive'         => true,
					'hierarchical'        => false,
					'show_in_menu'        => false,
					'show_in_nav_menus'   => false,
					'menu_icon'           => 'dashicons-visibility',
					'supports'            => array( 'title' ),
				),
			);

			foreach ( $types_args as $post_type => $args ) {
				register_post_type( $post_type, $args );
			}
		}
	}
}
