<?php
! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Post_Types' ) ) {
	/**
	 * Class YITH_WCMBS_Post_Types
	 * handle post types
	 *
	 * @since  1.4.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Post_Types {

		/**
		 * Membership Plan Post Type
		 *
		 * @var string
		 * @static
		 */
		public static $plan = 'yith-wcmbs-plan';

		/**
		 * Thread Post Type
		 *
		 * @var string
		 * @static
		 */
		public static $thread = 'yith-wcmbs-thread';

		/**
		 * Membership Post Type
		 *
		 * @var string
		 * @static
		 */
		public static $membership = 'ywcmbs-membership';


		public static $alternative_contents = 'ywcmbs-alt-cont';

		/**
		 * Hook in methods.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

			add_action( 'yith_wcmbs_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
			add_action( 'update_option_yith-wcmbs-hide-contents', array( __CLASS__, 'queue_flush_rewrite_rules' ) );
		}

		/**
		 * Register core post types.
		 */
		public static function register_post_types() {
			if ( post_type_exists( self::$membership ) ) {
				return;
			}

			do_action( 'yith_wcmbs_register_post_type' );

			// Membership.
			$labels = array(
				'menu_name'          => __( 'All Memberships', 'yith-woocommerce-membership' ),
				'all_items'          => __( 'All Memberships', 'yith-woocommerce-membership' ),
				'name'               => __( 'All Memberships', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Membership', 'yith-woocommerce-membership' ),
				'new_item'           => __( 'New Membership', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Membership', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New Membership', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Membership', 'yith-woocommerce-membership' ),
				'view'               => __( 'View Membership', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View Membership', 'yith-woocommerce-membership' ),
				'search_items'       => __( 'Search Memberships', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Membership not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Membership not found in trash', 'yith-woocommerce-membership' ),
			);


			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => self::$membership,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
			);

			register_post_type( self::$membership, $args );
			remove_post_type_support( self::$membership, 'title' );

			// Messages.

			$labels = array(
				'all_items'          => __( 'Messages', 'yith-woocommerce-membership' ),
				'name'               => __( 'Messages', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Message', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Add Message', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New Message', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Message', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View Message', 'yith-woocommerce-membership' ),
				'search_items'       => __( 'Search Messages', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Message not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Message not found in trash', 'yith-woocommerce-membership' ),
			);

			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => self::$thread,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => false,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => false,
			);

			register_post_type( self::$thread, $args );

			// Membership Plans.

			$labels = array(
				'menu_name'          => __( 'Membership plans', 'yith-woocommerce-membership' ),
				'all_items'          => __( 'Membership plans', 'yith-woocommerce-membership' ),
				'name'               => __( 'Membership plans', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Membership plan', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Add new plan', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New Plan', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Edit Plan', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View Plan', 'yith-woocommerce-membership' ),
				'search_items'       => __( 'Search Plans', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Membership plan not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Membership plan not found in trash', 'yith-woocommerce-membership' ),
			);

			$args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'exclude_from_search' => true,
				'capability_type'     => 'plan', // used instead of the post-type for backward compatibility (i.e. Multi Vendor integration).
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
			);

			register_post_type( self::$plan, $args );


			// Alternative Contents
			$labels = array(
				'menu_name'          => __( 'Alternative content blocks', 'yith-woocommerce-membership' ),
				'all_items'          => __( 'Alternative content blocks', 'yith-woocommerce-membership' ),
				'name'               => __( 'Alternative content blocks', 'yith-woocommerce-membership' ),
				'singular_name'      => __( 'Alternative content block', 'yith-woocommerce-membership' ),
				'add_new'            => __( 'Add new block', 'yith-woocommerce-membership' ),
				'add_new_item'       => __( 'New alternative content block', 'yith-woocommerce-membership' ),
				'edit_item'          => __( 'Edit block', 'yith-woocommerce-membership' ),
				'view_item'          => __( 'View block', 'yith-woocommerce-membership' ),
				'search_items'       => __( 'Search blocks', 'yith-woocommerce-membership' ),
				'not_found'          => __( 'Alternative content block not found', 'yith-woocommerce-membership' ),
				'not_found_in_trash' => __( 'Alternative content block not found in trash', 'yith-woocommerce-membership' ),
			);

			$args = array(
				'labels'              => $labels,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_rest'        => true,
				'capability_type'     => self::$alternative_contents,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
			);

			register_post_type( self::$alternative_contents, $args );


			do_action( 'yith_wcmbs_after_register_post_type' );
		}

		/**
		 * Add capabilities to Admin and Shop Manager
		 *
		 * @since 1.4.0
		 */
		public static function add_capabilities() {
			$admin = get_role( 'administrator' );

			$capability_types = array(
				'plan'                      => 'post', // use 'plan' instead of the post-type for backward compatibility (i.e. Multi Vendor integration).
				self::$membership           => 'post',
				self::$thread               => 'post',
				self::$alternative_contents => 'post',
			);

			foreach ( $capability_types as $capability_type => $type ) {
				$caps = array();
				switch ( $type ) {
					case 'post':
						$caps = array(
							'edit_post'              => "edit_{$capability_type}",
							'delete_post'            => "delete_{$capability_type}",
							'edit_posts'             => "edit_{$capability_type}s",
							'edit_others_posts'      => "edit_others_{$capability_type}s",
							'publish_posts'          => "publish_{$capability_type}s",
							'read_private_posts'     => "read_private_{$capability_type}s",
							'delete_posts'           => "delete_{$capability_type}s",
							'delete_private_posts'   => "delete_private_{$capability_type}s",
							'delete_published_posts' => "delete_published_{$capability_type}s",
							'delete_others_posts'    => "delete_others_{$capability_type}s",
							'edit_private_posts'     => "edit_private_{$capability_type}s",
							'edit_published_posts'   => "edit_published_{$capability_type}s",
							'create_posts'           => "create_{$capability_type}s",
						);

						break;

					case 'tax':
						$caps = array(
							'manage_terms' => 'manage_' . $capability_type . 's',
							'edit_terms'   => 'edit_' . $capability_type . 's',
							'delete_terms' => 'delete' . $capability_type . 's',
							'assign_terms' => 'assign' . $capability_type . 's',
						);
						break;
					case 'single':
						$caps = array( $capability_type );
				}

				foreach ( $caps as $key => $cap ) {
					if ( $admin ) {
						$admin->add_cap( $cap );
					}
				}
			}
		}

		/**
		 * Flush rules if the event is queued.
		 *
		 * @since 1.4.0
		 */
		public static function maybe_flush_rewrite_rules() {
			if ( 'yes' === get_option( 'yith_wcmbs_queue_flush_rewrite_rules' ) ) {
				update_option( 'yith_wcmbs_queue_flush_rewrite_rules', 'no' );
				self::flush_rewrite_rules();
			}
		}

		/**
		 * Flush rewrite rules.
		 *
		 * @since 1.4.0
		 */
		public static function flush_rewrite_rules() {
			flush_rewrite_rules();
		}

		/**
		 * Queue flushing rewrite rules.
		 *
		 * @since 1.4.0
		 */
		public static function queue_flush_rewrite_rules() {
			update_option( 'yith_wcmbs_queue_flush_rewrite_rules', 'yes' );
		}

		/**
		 * Install.
		 *
		 * @since 1.4.0
		 */
		public static function install() {
			self::queue_flush_rewrite_rules();
		}
	}
}
