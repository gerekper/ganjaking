<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships Post Types class.
 *
 * This class is responsible for registering the custom post types & taxonomy required for Memberships.
 *
 * @since 1.0.0
 */
class WC_Memberships_Post_Types {


	/**
	 * Initializes and registers the Memberships post types.
	 *
	 * @since 1.0.0
	 */
	public static function initialize() {

		self::init_post_types();
		self::init_user_roles();
		self::init_post_statuses();

		add_filter( 'post_updated_messages',      array( __CLASS__, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( __CLASS__, 'bulk_updated_messages' ), 10, 2 );

		// maybe remove overzealous 3rd-party meta boxes
		add_action( 'add_meta_boxes', array( __CLASS__, 'maybe_remove_meta_boxes' ), 30 );
	}


	/**
	 * Init WooCommerce Memberships user roles.
	 *
	 * @since 1.0.0
	 */
	private static function init_user_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		// allow shop managers and admins to manage membership plans and user memberships
		if ( is_object( $wp_roles ) ) {

			foreach ( array( 'membership_plan', 'user_membership' ) as $post_type ) {

				$args = new stdClass();
				$args->map_meta_cap = true;
				$args->capability_type = $post_type;
				$args->capabilities = array();

				foreach ( get_post_type_capabilities( $args ) as $builtin => $mapped ) {

					$wp_roles->add_cap( 'shop_manager', $mapped );
					$wp_roles->add_cap( 'administrator', $mapped );
				}
			}

			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_membership_plans' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_membership_plans' );

			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_user_memberships' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_user_memberships' );
		}
	}


	/**
	 * Init WooCommerce Memberships post types.
	 *
	 * @since 1.0.0
	 */
	private static function init_post_types() {

		if ( current_user_can( 'manage_woocommerce' ) ) {
			$show_in_menu = 'woocommerce';
		} else {
			$show_in_menu = true;
		}

		register_post_type( 'wc_membership_plan',
			array(
				'labels' => array(
						'name'               => __( 'Membership Plans', 'woocommerce-memberships' ),
						'singular_name'      => __( 'Membership Plan', 'woocommerce-memberships' ),
						'menu_name'          => _x( 'Memberships', 'Admin menu name', 'woocommerce-memberships' ),
						'add_new'            => __( 'Add Membership Plan', 'woocommerce-memberships' ),
						'add_new_item'       => __( 'Add New Membership Plan', 'woocommerce-memberships' ),
						'edit'               => __( 'Edit', 'woocommerce-memberships' ),
						'edit_item'          => __( 'Edit Membership Plan', 'woocommerce-memberships' ),
						'new_item'           => __( 'New Membership Plan', 'woocommerce-memberships' ),
						'view'               => __( 'View Membership Plans', 'woocommerce-memberships' ),
						'view_item'          => __( 'View Membership Plan', 'woocommerce-memberships' ),
						'search_items'       => __( 'Search Membership Plans', 'woocommerce-memberships' ),
						'not_found'          => __( 'No Membership Plans found', 'woocommerce-memberships' ),
						'not_found_in_trash' => __( 'No Membership Plans found in trash', 'woocommerce-memberships' ),
					),
				'description'         => __( 'This is where you can add new Membership Plans.', 'woocommerce-memberships' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'membership_plan',
				'map_meta_cap'        => true,
				'show_in_menu'        => $show_in_menu,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
			)
		);

		register_post_type( 'wc_user_membership',
			array(
				'labels' => array(
						'name'               => __( 'Members', 'woocommerce-memberships' ),
						'singular_name'      => __( 'User Membership', 'woocommerce-memberships' ),
						'menu_name'          => _x( 'Memberships', 'Admin menu name', 'woocommerce-memberships' ),
						'add_new'            => __( 'Add Member', 'woocommerce-memberships' ),
						'add_new_item'       => __( 'Add New User Membership', 'woocommerce-memberships' ),
						'edit'               => __( 'Edit', 'woocommerce-memberships' ),
						'edit_item'          => __( 'Edit User Membership', 'woocommerce-memberships' ),
						'new_item'           => __( 'New User Membership', 'woocommerce-memberships' ),
						'view'               => __( 'View User Memberships', 'woocommerce-memberships' ),
						'view_item'          => __( 'View User Membership', 'woocommerce-memberships' ),
						'search_items'       => __( 'Search Members', 'woocommerce-memberships' ),
						'not_found'          => __( 'No User Memberships found', 'woocommerce-memberships' ),
						'not_found_in_trash' => __( 'No User Memberships found in trash', 'woocommerce-memberships' ),
					),
				'description'         => __( 'This is where you can add new User Memberships.', 'woocommerce-memberships' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'user_membership',
				'map_meta_cap'        => true,
				'show_in_menu'        => $show_in_menu,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( null ),
			)
		);

	}


	/**
	 * Registers WooCommerce Memberships post statuses.
	 *
	 * @since 1.0.0
	 */
	private static function init_post_statuses() {

		$statuses = wc_memberships_get_user_membership_statuses();

		foreach ( $statuses as $status => $args ) {

			$args = wp_parse_args( $args, array(
				'label'     => ucfirst( $status ),
				'public'    => false,
				'protected' => true,
			) );

			register_post_status( $status, $args );
		}
	}


	/**
	 * Customizes updated messages for custom post types.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages original messages
	 * @return array $messages modified messages
	 */
	public static function updated_messages( $messages ) {

		$messages['wc_membership_plan'] = array(
			0  => '', // unused, messages start at index 1
			1  => __( 'Membership Plan saved.', 'woocommerce-memberships' ),
			2  => __( 'Custom field updated.', 'woocommerce-memberships' ),
			3  => __( 'Custom field deleted.', 'woocommerce-memberships' ),
			4  => __( 'Membership Plan saved.', 'woocommerce-memberships' ),
			5  => '', // unused for membership plans
			6  => __( 'Membership Plan saved.', 'woocommerce-memberships' ), // original: "Post published"
			7  => __( 'Membership Plan saved.', 'woocommerce-memberships' ),
			8  => '', // unused for membership plans
			9  => '', // unused for membership plans
			10 => __( 'Membership Plan draft updated.', 'woocommerce-memberships' ), // original: "Post draft updated"
		);

		$messages['wc_user_membership'] = array(
			0  => '', // unused, messages start at index 1
			1  => __( 'User Membership saved.', 'woocommerce-memberships' ),
			2  => __( 'Custom field updated.', 'woocommerce-memberships' ),
			3  => __( 'Custom field deleted.', 'woocommerce-memberships' ),
			4  => __( 'User Membership saved.', 'woocommerce-memberships' ),
			5  => '', // unused for user memberships
			6  => __( 'User Membership saved.', 'woocommerce-memberships' ), // original: "Post published"
			7  => __( 'User Membership saved.', 'woocommerce-memberships' ),
			8  => '', // unused for user memberships
			9  => '', // unused for user memberships
			10 => __( 'User Membership saved.', 'woocommerce-memberships' ), // original: "Post draft updated"
		);

		return $messages;
	}


	/**
	 * Customizes updated messages for custom post types.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages original messages
	 * @param array $bulk_counts counter
	 * @return array $messages modified messages
	 */
	public static function bulk_updated_messages( $messages, $bulk_counts ) {

		$messages['wc_membership_plan'] = array(
			'updated'   => _n( '%s membership plan updated.', '%s membership plans updated.', $bulk_counts['updated'], 'woocommerce-memberships' ),
			'locked'    => _n( '%s membership plan not updated, somebody is editing it.', '%s membership plans not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce-memberships' ),
			'deleted'   => _n( '%s membership plan permanently deleted.', '%s membership plans permanently deleted.', $bulk_counts['deleted'], 'woocommerce-memberships' ),
			'trashed'   => _n( '%s membership plan moved to the Trash.', '%s membership plans moved to the Trash.', $bulk_counts['trashed'], 'woocommerce-memberships' ),
			'untrashed' => _n( '%s membership plan restored from the Trash.', '%s membership plans restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce-memberships' ),
		);

		$messages['wc_user_membership'] = array(
			'updated'   => _n( '%s user membership updated.', '%s user memberships updated.', $bulk_counts['updated'], 'woocommerce-memberships' ),
			'locked'    => _n( '%s user membership not updated, somebody is editing it.', '%s user memberships not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce-memberships' ),
			'deleted'   => _n( '%s user membership permanently deleted.', '%s user memberships permanently deleted.', $bulk_counts['deleted'], 'woocommerce-memberships' ),
			'trashed'   => _n( '%s user membership moved to the Trash.', '%s user memberships moved to the Trash.', $bulk_counts['trashed'], 'woocommerce-memberships' ),
			'untrashed' => _n( '%s user membership restored from the Trash.', '%s user memberships restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce-memberships' ),
		);

		return $messages;
	}


	/**
	 * Removes third party meta boxes from Memberships Custom Post Type admin screens.
	 *
	 * Runs a filter whitelist to exclude meta boxes, like those added by Memberships itself.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param string $post_type
	 */
	public static function maybe_remove_meta_boxes( $post_type ) {

		if ( in_array( $post_type, array( 'wc_membership_plan', 'wc_user_membership' ), true ) ) {

			$screen = get_current_screen();

			/**
			 * Whitelist of allowed meta boxes to appear on Memberships custom post type admin screens.
			 *
			 * @since 1.0.0
			 *
			 * @param string[] array of meta box IDs
			 */
			$allowed_meta_box_ids = apply_filters( 'wc_memberships_allowed_meta_box_ids', array_merge( array( 'submitdiv' ), wc_memberships()->get_admin_instance()->get_meta_box_ids() ) );

			if ( $screen && isset( $GLOBALS['wp_meta_boxes'][ $screen->id ] ) ) {

				foreach ( $GLOBALS['wp_meta_boxes'][ $screen->id ] as $context => $meta_boxes_by_context ) {

					foreach ( $meta_boxes_by_context as $subcontext => $meta_boxes_by_subcontext ) {

						foreach ( $meta_boxes_by_subcontext as $meta_box_id => $meta_box ) {

							if ( ! in_array( $meta_box_id, $allowed_meta_box_ids, true ) ) {
								remove_meta_box( $meta_box_id, $post_type, $context );
							}
						}
					}
				}
			}
		}
	}


}
