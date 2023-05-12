<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * Local Pickup Plus post types.
 *
 * This class is responsible for registering custom post types used by the plugin.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Post_Types {


	/**
	 * Initialize and register custom post types.
	 *
	 * @since 2.0.0
	 */
	public static function init() {

		self::register_post_types();
		self::set_user_roles_and_capabilities();

		// handle custom post type admin messages
		add_filter( 'post_updated_messages',      array( __CLASS__, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( __CLASS__, 'bulk_updated_messages' ), 10, 2 );
	}


	/**
	 * Register custom post types.
	 *
	 * @since 2.0.0
	 */
	public static function register_post_types() {

		$pickup_location_labels = array(
			'name'               => __( 'Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ),
			'singular_name'      => __( 'Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'menu_name'          => _x( 'Pickup Locations', 'Admin menu name', 'woocommerce-shipping-local-pickup-plus' ),
			'add_new'            => __( 'Add Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'add_new_item'       => __( 'Add New Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'edit'               => __( 'Edit', 'woocommerce-shipping-local-pickup-plus' ),
			'edit_item'          => __( 'Edit Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'new_item'           => __( 'New Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'view'               => __( 'View Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ),
			'view_item'          => __( 'View Pickup Location', 'woocommerce-shipping-local-pickup-plus' ),
			'search_items'       => __( 'Search Pickup Locations', 'woocommerce-shipping-local-pickup-plus' ),
			'not_found'          => __( 'No Pickup Location found', 'woocommerce-shipping-local-pickup-plus' ),
			'not_found_in_trash' => __( 'No Pickup Locations found in trash', 'woocommerce-shipping-local-pickup-plus' ),
		);

		$local_pickup_post_type_args = array(
			'labels'              => $pickup_location_labels,
			'description'         => __( 'This is where you can add new Pickup Locations.', 'woocommerce-shipping-local-pickup-plus' ),
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'pickup_location',
			'map_meta_cap'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => false,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array(
				'title',
			),
			'show_in_nav_menus'   => false,
		);

		register_post_type( 'wc_pickup_location', $local_pickup_post_type_args );
	}


	/**
	 * Set up custom user roles and capabilities.
	 *
	 * @since 2.0.0
	 */
	private static function set_user_roles_and_capabilities() {
		global $wp_roles;

		if ( ! $wp_roles instanceof \WP_Roles && class_exists( 'WP_Roles' ) ) {
			$wp_roles = new \WP_Roles();
		}

		// allow shop managers and admins to manage local pickup locations
		if ( is_object( $wp_roles ) ) {

			$args = new \stdClass();
			$args->map_meta_cap = true;
			$args->capability_type = 'pickup_location';
			$args->capabilities = array();

			foreach ( (array) get_post_type_capabilities( $args ) as $builtin => $mapped ) {

				$wp_roles->add_cap( 'shop_manager', $mapped );
				$wp_roles->add_cap( 'administrator', $mapped );
			}

			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_pickup_locations' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_pickup_locations' );

			$wp_roles->add_cap( 'shop_manager',  'manage_woocommerce_pickup_locations' );
			$wp_roles->add_cap( 'administrator', 'manage_woocommerce_pickup_locations' );
		}
	}


	/**
	 * Customize updated messages for custom post types.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $messages original messages
	 * @return array $messages modified messages
	 */
	public static function updated_messages( $messages ) {

		$messages['wc_pickup_location'] = array(
			0  => '', // Unused.
			1  => __( 'Pickup Location saved.', 'woocommerce-shipping-local-pickup-plus' ),
			2  => __( 'Custom field updated.', 'woocommerce-shipping-local-pickup-plus' ),
			3  => __( 'Custom field deleted.', 'woocommerce-shipping-local-pickup-plus' ),
			4  => __( 'Pickup Location saved.', 'woocommerce-shipping-local-pickup-plus' ),
			5  => '', // Unused.
			6  => __( 'Pickup Location saved.', 'woocommerce-shipping-local-pickup-plus' ),
			7  => __( 'Pickup Location saved.', 'woocommerce-shipping-local-pickup-plus' ),
			8  => '', // Unused.
			9  => '', // Unused.
			10 => __( 'Pickup Location draft updated.', 'woocommerce-shipping-local-pickup-plus' ),
		);

		return $messages;
	}


	/**
	 * Customize bulk updated messages for custom post types.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $messages original messages
	 * @param array $bulk_counts bulk counts
	 * @return array $messages modified messages
	 */
	public static function bulk_updated_messages( $messages, $bulk_counts ) {

		$messages['wc_pickup_location'] = array(
			'updated'   => _n( '%s pickup location updated.', '%s pickup locations updated.', $bulk_counts['updated'], 'woocommerce-shipping-local-pickup-plus' ),
			'locked'    => _n( '%s pickup location not updated, somebody is editing it.', '%s pickup locations not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce-shipping-local-pickup-plus' ),
			'deleted'   => _n( '%s pickup location permanently deleted.', '%s pickup locations permanently deleted.', $bulk_counts['deleted'], 'woocommerce-shipping-local-pickup-plus' ),
			'trashed'   => _n( '%s pickup location moved to the Trash.', '%s pickup locations moved to the Trash.', $bulk_counts['trashed'], 'woocommerce-shipping-local-pickup-plus' ),
			'untrashed' => _n( '%s pickup location restored from the Trash.', '%s pickup locations restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce-shipping-local-pickup-plus' ),
		);

		return $messages;
	}


}
