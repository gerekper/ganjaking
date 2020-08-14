<?php
/**
 * Post Types class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Badge Management
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
			add_action( 'init', array( __CLASS__, 'post_type_register' ) );
		}

		/**
		 * Register Badge custom post type with options metabox
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public static function post_type_register() {
			$labels = array(
				'name'               => __( 'Badges', 'yith-woocommerce-badges-management' ),
				'singular_name'      => __( 'Badge', 'yith-woocommerce-badges-management' ),
				'add_new'            => __( 'Add Badge', 'yith-woocommerce-badges-management' ),
				'add_new_item'       => __( 'Add new Badge', 'yith-woocommerce-badges-management' ),
				'edit_item'          => __( 'Edit Badge', 'yith-woocommerce-badges-management' ),
				'view_item'          => __( 'View Badge', 'yith-woocommerce-badges-management' ),
				'not_found'          => __( 'Badge not found', 'yith-woocommerce-badges-management' ),
				'not_found_in_trash' => __( 'Badge not found in trash', 'yith-woocommerce-badges-management' ),
			);

			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => array( 'badge', 'badges' ),
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'menu_icon'           => 'dashicons-visibility',
				'supports'            => array( 'title' ),
			);

			register_post_type( self::$badge, $args );
		}
	}
}
