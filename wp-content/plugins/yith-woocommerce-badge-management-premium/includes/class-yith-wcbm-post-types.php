<?php
/**
 * Post Types class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCBM' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'YITH_WCBM_Post_Types' ) ) {
	/**
	 * Post Types class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCBM_Post_Types {

		/**
		 * Badge Post Type
		 *
		 * @var string
		 */
		public static $badge = 'yith-wcbm-badge';

		/**
		 * Init
		 */
		public static function init() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );
			add_action( 'init', array( $self, 'post_type_register' ) );
		}

		/**
		 * Register Badge custom post type with options metabox
		 *
		 * @return   void
		 * @since    1.0
		 */
		public static function post_type_register() {
			$labels = array(
				self::$badge => array(
					'name'               => __( 'Badges', 'yith-woocommerce-badges-management' ),
					'singular_name'      => __( 'Badge', 'yith-woocommerce-badges-management' ),
					'add_new'            => __( 'Add Badge', 'yith-woocommerce-badges-management' ),
					'add_new_item'       => __( 'Add new Badge', 'yith-woocommerce-badges-management' ),
					'edit_item'          => __( 'Edit Badge', 'yith-woocommerce-badges-management' ),
					'view_item'          => __( 'View Badge', 'yith-woocommerce-badges-management' ),
					'not_found'          => __( 'Badge not found', 'yith-woocommerce-badges-management' ),
					'not_found_in_trash' => __( 'Badge not found in trash', 'yith-woocommerce-badges-management' ),
					'edit'               => __( 'Edit Badge', 'yith-woocommerce-badges-management' ),
					'new_item'           => __( 'New Badge', 'yith-woocommerce-badges-management' ),
					'view'               => __( 'View Badge', 'yith-woocommerce-badges-management' ),
					'search_items'       => __( 'Search Badges', 'yith-woocommerce-badges-management' ),
					'parent'             => __( 'Parent Badges', 'yith-woocommerce-badges-management' ),
					'menu_name'          => _x( 'Badges', 'Admin menu name', 'yith-woocommerce-badges-management' ),
					'all_items'          => __( 'All Badges', 'yith-woocommerce-badges-management' ),
					'view_items'         => __( 'View Badges', 'yith-woocommerce-badges-management' ),
					'archives'           => __( 'Badge archives', 'yith-woocommerce-badges-management' ),
					'attributes'         => __( 'Badge attributes', 'yith-woocommerce-badges-management' ),
					'filter_items_list'  => __( 'Filter badge list', 'yith-woocommerce-badges-management' ),
					'filter_by_date'     => __( 'Filter by date', 'yith-woocommerce-badges-management' ),
					'items_list'         => __( 'Badge list', 'yith-woocommerce-badges-management' ),
					'item_published'     => __( 'Badge saved', 'yith-woocommerce-badges-management' ),
					'item_updated'       => __( 'Badge saved', 'yith-woocommerce-badges-management' ),
				),
			);

			$types_args = array(
				self::$badge => array(
					'labels'              => $labels[ self::$badge ],
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
