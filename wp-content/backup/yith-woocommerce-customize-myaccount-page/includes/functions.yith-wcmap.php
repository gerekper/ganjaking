<?php
/**
 * Plugins Functions and Hooks
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

/*######################################
 CLASSES
######################################*/

if ( ! function_exists( 'YITH_WCMAP_Admin' ) ) {
	/**
	 * The admin class
	 *
	 * @since  2.5.0
	 * @author Francesco Licandro
	 * @return \YITH_WCMAP_Admin|null
	 */
	function YITH_WCMAP_Admin() {
		return YITH_WCMAP()->admin;
	}
}

if ( ! function_exists( 'YITH_WCMAP_Frontend' ) ) {
	/**
	 * The frontend class
	 *
	 * @since  2.5.0
	 * @author Francesco Licandro
	 * @return \YITH_WCMAP_Frontend|null
	 */
	function YITH_WCMAP_Frontend() {
		return YITH_WCMAP()->frontend;
	}
}

/*######################################
 ADMIN FUNCTION
 ######################################*/

if ( ! function_exists( 'yith_wcmap_admin_print_endpoint_field' ) ) {
	/**
	 * Print endpoint field options
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param array $args Template args array
	 */
	function yith_wcmap_admin_print_endpoint_field( $args ) {

		// let third part filter template args
		$args = apply_filters( 'yith_wcmap_admin_print_endpoint_field', $args );
		extract( $args );

		include( YITH_WCMAP_TEMPLATE_PATH . '/admin/endpoint-item.php' );
	}
}

if ( ! function_exists( 'yith_wcmap_admin_print_group_field' ) ) {
	/**
	 * Print endpoints group field options
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @param array $args Template args array
	 */
	function yith_wcmap_admin_print_group_field( $args ) {

		// let third part filter template args
		$args = apply_filters( 'yith_wcmap_admin_print_endpoints_group', $args );
		extract( $args );

		include( YITH_WCMAP_TEMPLATE_PATH . '/admin/group-item.php' );
	}
}

if ( ! function_exists( 'yith_wcmap_admin_print_link_field' ) ) {
	/**
	 * Print endpoints link field options
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @param array $args Template args array
	 */
	function yith_wcmap_admin_print_link_field( $args ) {
		// let third part filter template args
		$args = apply_filters( 'yith_wcmap_admin_print_link_field', $args );
		extract( $args );

		include( YITH_WCMAP_TEMPLATE_PATH . '/admin/link-item.php' );
	}
}

/*####################################
 COMMON FUNCTION
#####################################*/

if ( ! function_exists( 'yith_wcmap_get_editable_roles' ) ) {
	/**
	 * Get editable roles for endpoints
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_editable_roles() {
		// get user role
		$roles     = get_editable_roles();
		$usr_roles = array();
		foreach ( $roles as $key => $role ) {
			if ( empty( $role['capabilities'] ) ) {
				continue;
			}
			$usr_roles[ $key ] = $role['name'];
		}

		return $usr_roles;
	}
}

if ( ! function_exists( 'yith_wcmap_build_label' ) ) {
	/**
	 * Build endpoint label by name
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $name
	 * @return string
	 */
	function yith_wcmap_build_label( $name ) {

		$label = preg_replace( '/[^a-z]/', ' ', $name );
		$label = trim( $label );
		$label = ucfirst( $label );

		return $label;
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_endpoint_options' ) ) {
	/**
	 * Get default options for new endpoints
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @return array
	 */
	function yith_wcmap_get_default_endpoint_options( $endpoint ) {

		$endpoint_name = yith_wcmap_build_label( $endpoint );

		// build endpoint options
		$options = array(
			'slug'      => $endpoint,
			'active'    => true,
			'label'     => $endpoint_name,
			'icon'      => '',
			'class'     => '',
			'content'   => '',
			'usr_roles' => '',
		);

		return apply_filters( 'yith_wcmap_get_default_endpoint_options', $options );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_group_options' ) ) {
	/**
	 * Get default options for new group
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $group
	 * @return array
	 */
	function yith_wcmap_get_default_group_options( $group ) {

		$group_name = yith_wcmap_build_label( $group );

		// build endpoint options
		$options = array(
			'active'    => true,
			'label'     => $group_name,
			'usr_roles' => '',
			'icon'      => '',
			'class'     => '',
			'open'      => true,
			'children'  => array(),
		);

		return apply_filters( 'yith_wcmap_get_default_group_options', $options );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_link_options' ) ) {
	/**
	 * Get default options for new links
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @return array
	 */
	function yith_wcmap_get_default_link_options( $endpoint ) {

		$endpoint_name = yith_wcmap_build_label( $endpoint );
		// build endpoint options
		$options = array(
			'url'          => '#',
			'active'       => true,
			'label'        => $endpoint_name,
			'icon'         => '',
			'class'        => '',
			'usr_roles'    => '',
			'target_blank' => false,
		);

		return apply_filters( 'yith_wcmap_get_default_link_options', $options );
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoints' ) ) {
	/**
	 * Get ordered endpoints based on plugin option
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_endpoints() {
		_deprecated_function( __FUNCTION__, '2.4.0', 'YITH_WCMAP()->items->get_items()' );
		$return = YITH_WCMAP()->items->get_items();

		if ( has_filter( 'yith_wcmap_get_endpoints' ) ) {
			wc_deprecated_hook( 'yith_wcmap_get_endpoints', '2.4.0', 'yith_wcmap_get_items' );
		}

		return apply_filters( 'yith_wcmap_get_endpoints', $return );
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoints_keys' ) ) {
	/**
	 * Get all endpoints keys
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_endpoints_keys() {
		_deprecated_function( __FUNCTION__, '2.4.0', 'YITH_WCMAP()->items->get_items_keys()' );
		return YITH_WCMAP()->items->get_items_keys();
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_endpoints' ) ) {
	/**
	 * Get default endpoints and options
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_default_endpoints() {
		_deprecated_function( __FUNCTION__, '2.4.0', 'YITH_WCMAP()->items->get_default_items()' );
		$default_endpoints = YITH_WCMAP()->items->get_default_items();

		if ( has_filter( 'yith_wcmap_get_endpoints' ) ) {
			wc_deprecated_hook( 'yith_wcmap_get_endpoints', '2.4.0', 'yith_wcmap_get_default_items' );
		}

		return apply_filters( 'yith_wcmap_get_default_endpoints_array', $default_endpoints );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_endpoints_keys' ) ) {
	/**
	 * Get default endpoints key
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 */
	function yith_wcmap_get_default_endpoints_keys() {
		$endpoints = yith_wcmap_get_default_endpoints();
		return apply_filters( 'yith_wcmap_get_default_endpoints_keys_array', array_keys( $endpoints ) );
	}
}

if ( ! function_exists( 'yith_wcmap_is_default_item' ) ) {
	/**
	 * Check if an item is a default
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $item
	 * @return boolean
	 */
	function yith_wcmap_is_default_item( $item ) {
		$defaults = YITH_WCMAP()->items->get_default_items();
		return array_key_exists( $item, $defaults );
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoints_slug' ) ) {
	/**
	 * Get endpoints slugs for register endpoints
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_endpoints_slug() {
		_deprecated_function( __FUNCTION__, '2.4.0', 'YITH_WCMAP()->items->get_items_slug()' );
		return YITH_WCMAP()->items->get_items_slug();
	}
}

if ( ! function_exists( 'yith_wcmap_endpoint_already_exists' ) ) {
	/**
	 * Check if endpoints already exists
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @return boolean
	 */
	function yith_wcmap_endpoint_already_exists( $endpoint ) {
		_deprecated_function( __FUNCTION__, '2.4.0', 'YITH_WCMAP()->items->get_items_slug()' );
		return yith_wcmap_item_already_exists( $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_item_already_exists' ) ) {
	/**
	 * Check if item already exists
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @return boolean
	 */
	function yith_wcmap_item_already_exists( $endpoint ) {

		// check first in key
		$field_key = YITH_WCMAP()->items->get_items_keys();
		$exists    = in_array( $endpoint, $field_key, true );

		// check also in slug
		if ( ! $exists ) {
			$endpoint_slug = YITH_WCMAP()->items->get_items_slug();
			$exists        = in_array( $endpoint, $endpoint_slug );
		}

		return $exists;
	}
}

if ( ! function_exists( 'yith_wcmap_get_current_endpoint' ) ) {
	/**
	 * Check if and endpoint is active on frontend. Used for add class 'active' on account menu in frontend
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wcmap_get_current_endpoint() {

		global $wp;

		$current = '';
		foreach ( WC()->query->get_query_vars() as $key => $value ) {
			// check for dashboard
			if ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) {
				$current = 'dashboard';
				break;
			} elseif ( isset( $wp->query_vars[ $key ] ) ) {
				$current = $key;
				break;
			}
		}

		return apply_filters( 'yith_wcmap_get_current_endpoint', $current );
	}
}

if ( ! function_exists( 'yith_wcmap_endpoints_list' ) ) {
	/**
	 * Get endpoints slugs for register endpoints
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_endpoints_list() {

		$return = array();
		$fields = YITH_WCMAP()->items->get_items();

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['children'] ) ) {
				foreach ( $field['children'] as $child_key => $child ) {
					isset( $child['slug'] ) && $return[ $child_key ] = $child['label'];
				}
				continue;
			}
			isset( $field['slug'] ) && $return[ $key ] = $field['label'];
		}

		return $return;
	}
}

if ( ! function_exists( 'yith_wcmap_endpoints_option_default' ) ) {
	/**
	 * Get endpoints slugs for register endpoints
	 *
	 * @since      2.0.0
	 * @author     Francesco Licandro
	 * @return array
	 * @deprecated Since version 2.6.0 use instead yith_wcmap_endpoints_list
	 */
	function yith_wcmap_endpoints_option_default() {
		return yith_wcmap_endpoints_list();
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoint_by' ) ) {
	/**
	 * Get endpoint by a specified key
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $value
	 * @param string $key   Can be key or slug
	 * @param array  $items Endpoint array
	 * @return array
	 */
	function yith_wcmap_get_endpoint_by( $value, $key = 'key', $items = array() ) {

		$accepted = apply_filters( 'yith_wcmap_get_endpoint_by_accepted_key', array( 'key', 'slug' ) );

		if ( ! in_array( $key, $accepted ) ) {
			return array();
		}

		empty( $items ) && $items = YITH_WCMAP()->items->get_items();
		$find = array();

		foreach ( $items as $id => $item ) {
			if ( ( $key == 'key' && $id == $value ) || ( isset( $item[ $key ] ) && $item[ $key ] == $value ) ) {
				$find[ $id ] = $item;
				continue;
			} elseif ( isset( $item['children'] ) ) {
				foreach ( $item['children'] as $child_id => $child ) {
					if ( ( $key == 'key' && $child_id == $value ) || ( isset( $child[ $key ] ) && $child[ $key ] == $value ) ) {
						$find[ $child_id ] = $child;
						continue;
					}
				}
				continue;
			}
		}
		return apply_filters( 'yith_wcmap_get_endpoint_by_result', $find );
	}
}

/*#####################################
 PRINT ENDPOINT FRONTEND
######################################*/

add_action( 'yith_wcmap_print_single_endpoint', 'yith_wcmap_print_single_endpoint', 10, 2 );
add_action( 'yith_wcmap_print_endpoints_group', 'yith_wcmap_print_endpoints_group', 10, 2 );

if ( ! function_exists( 'yith_wcmap_print_single_endpoint' ) ) {
	/**
	 * Print single endpoint on front menu
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @param array  $options
	 */
	function yith_wcmap_print_single_endpoint( $endpoint, $options ) {

		global $wp_query;

		if ( ! isset( $options['url'] ) ) {
			$url = get_permalink( wc_get_page_id( 'myaccount' ) );
			$endpoint != 'dashboard' && $url = wc_get_endpoint_url( $endpoint, '', $url );
		} else {
			$url = esc_url( $options['url'] );
		}

		// check if endpoint is active
		$current = yith_wcmap_get_current_endpoint();
		$classes = array();
		! empty( $options['class'] ) && $classes[] = $options['class'];
		( $endpoint == $current ) && $classes[] = 'active';

		if ( $endpoint == 'orders' ) {
			$view_order = get_option( 'woocommerce_myaccount_view_order_endpoint', 'view-order' );
			( $current == $view_order && ! in_array( 'active', $classes ) ) && $classes[] = 'active';
		} elseif ( $endpoint == 'refund-requests' ) {
			isset( $wp_query->query_vars[ YITH_Advanced_Refund_System_My_Account::$view_request_endpoint ] ) && $classes[] = 'active';
		} elseif ( $endpoint == 'payment-methods' ) {
			( in_array( $current, array( 'add-payment-method', 'delete-payment-method', 'set-default-payment-method' ) ) && in_array( 'active', $classes ) ) && $classes[] = 'active';
		}

		$classes = apply_filters( 'yith_wcmap_endpoint_menu_class', $classes, $endpoint, $options );

		// build args array
		$args = apply_filters( 'yith_wcmap_print_single_endpoint_args', array(
			'url'      => $url,
			'endpoint' => $endpoint,
			'options'  => $options,
			'classes'  => $classes,
		) );

		wc_get_template( 'ywcmap-myaccount-menu-item.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
	}
}

if ( ! function_exists( 'yith_wcmap_print_endpoints_group' ) ) {
	/**
	 * Print endpoints group on front menu
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint
	 * @param array  $options
	 */
	function yith_wcmap_print_endpoints_group( $endpoint, $options ) {

		$classes = array( 'group-' . $endpoint );
		$current = yith_wcmap_get_current_endpoint();

		! empty( $options['class'] ) && $classes[] = $options['class'];

		// check in child and add class active
		foreach ( $options['children'] as $child_key => $child ) {
			if ( isset( $child['slug'] ) && $child_key == $current && WC()->query->get_current_endpoint() != '' ) {
				$options['open'] = true;
				$classes[]       = 'active';
				break;
			}
		}

		$class_icon = $options['open'] ? 'fa-chevron-up' : 'fa-chevron-down';

		$istab = get_option( 'yith-wcmap-menu-style', 'sidebar' ) == 'tab';
		// options for style tab
		if ( $istab ) {
			// force option open to true
			$options['open'] = true;
			$class_icon      = 'fa-chevron-down';
			$classes[]       = 'is-tab';
		}

		$classes = apply_filters( 'yith_wcmap_endpoints_group_class', $classes, $endpoint, $options );

		// build args array
		$args = apply_filters( 'yith_wcmap_print_endpoints_group_group', array(
			'options'    => $options,
			'classes'    => $classes,
			'class_icon' => $class_icon,
		) );

		wc_get_template( 'ywcmap-myaccount-menu-group.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
	}
}

/*#####################################
 AVATAR FUNCTION
#####################################*/

if ( ! function_exists( 'yith_wcmap_generate_avatar_path' ) ) {
	/**
	 * Generate avatar path
	 *
	 * @param $attachment_id
	 * @param $size
	 * @return string
	 */
	function yith_wcmap_generate_avatar_path( $attachment_id, $size ) {
		// Retrieves attached file path based on attachment ID.
		$filename = get_attached_file( $attachment_id );

		$pathinfo  = pathinfo( $filename );
		$dirname   = $pathinfo['dirname'];
		$extension = $pathinfo['extension'];

		// i18n friendly version of basename().
		$basename = wp_basename( $filename, '.' . $extension );

		$suffix    = $size . 'x' . $size;
		$dest_path = $dirname . '/' . $basename . '-' . $suffix . '.' . $extension;

		return $dest_path;
	}
}

if ( ! function_exists( 'yith_wcmap_generate_avatar_url' ) ) {
	/**
	 * Generate avatar url
	 *
	 * @param $attachment_id
	 * @param $size
	 * @return mixed
	 */
	function yith_wcmap_generate_avatar_url( $attachment_id, $size ) {
		// Retrieves path information on the currently configured uploads directory.
		$upload_dir = wp_upload_dir();

		// Generates a file path of an avatar image based on attachment ID and size.
		$path = yith_wcmap_generate_avatar_path( $attachment_id, $size );

		return str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $path );
	}
}

if ( ! function_exists( 'yith_wcmap_resize_avatar_url' ) ) {
	/**
	 * Resize avatar
	 *
	 * @param $attachment_id
	 * @param $size
	 * @return boolean
	 */
	function yith_wcmap_resize_avatar_url( $attachment_id, $size ) {

		$dest_path = yith_wcmap_generate_avatar_path( $attachment_id, $size );

		if ( file_exists( $dest_path ) ) {
			$resize = true;
		} else {
			// Retrieves attached file path based on attachment ID.
			$path = get_attached_file( $attachment_id );

			// Retrieves a WP_Image_Editor instance and loads a file into it.
			$image = wp_get_image_editor( $path );

			if ( ! is_wp_error( $image ) ) {

				// Resizes current image.
				$image->resize( $size, $size, true );

				// Saves current image to file.
				$image->save( $dest_path );

				$resize = true;

			} else {
				$resize = false;
			}
		}

		return $resize;
	}
}

/*#########################################
 CUSTOM PLUGINS ENDPOINTS
###########################################*/

if ( ! function_exists( 'yith_wcmap_get_plugins_endpoints' ) ) {
	/**
	 * Get plugins endpoints
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param string $key
	 * @return array
	 */
	function yith_wcmap_get_plugins_endpoints( $key = '' ) {
		_deprecated_function( __FUNCTION__, '2.4.0', $key ? 'YITH_WCMAP()->items->get_plugin_item_by_key( $key )' : 'YITH_WCMAP()->items->get_plugins_items()' );
		$endpoints = $key ? YITH_WCMAP()->items->get_plugin_item_by_key( $key ) : YITH_WCMAP()->items->get_plugins_items();
		return $endpoints;
	}
}

if ( ! function_exists( 'yith_wcmap_is_plugin_endpoint' ) ) {
	/**
	 * Check if an endpoint is a plugin
	 *
	 * @since  1.0.4
	 * @author Francesco Licandro
	 * @return boolean
	 */
	function yith_wcmap_is_plugin_endpoint( $endpoint ) {
		_deprecated_function( __FUNCTION__, '2.4.0', 'yith_wcmap_is_plugin_item' );
		return yith_wcmap_is_plugin_item( $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_is_plugin_item' ) ) {
	/**
	 * Check if an item is a plugin
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $item
	 * @return boolean
	 */
	function yith_wcmap_is_plugin_item( $item ) {
		$plugins = YITH_WCMAP()->items->get_plugins_items();
		return array_key_exists( $item, $plugins );
	}
}

/*####################################
* YITH WOOCOMMERCE ONE CLICK CHECKOUT
######################################*/

if ( defined( 'YITH_WOCC_PREMIUM' ) && YITH_WOCC_PREMIUM ) {
	/**
	 * Add One Click Checkout compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_wocc_one_click_compatibility() {

		if ( class_exists( 'YITH_WOCC_User_Account' ) ) {
			// remove content in my account
			remove_action( 'woocommerce_after_my_account', array( YITH_WOCC_User_Account(), 'my_account_options' ) );
		}

		add_filter( 'yith_wcmap_endpoint_menu_class', 'yith_wocc_set_active_one_click', 10, 3 );
	}

	/**
	 * Assign active class to endpoint one-click
	 *
	 * @since  1.1.0
	 * @author Francesco Licandro
	 * @param array  $classes
	 * @param string $endpoint
	 * @param array  $options
	 * @return array
	 */
	function yith_wocc_set_active_one_click( $classes, $endpoint, $options ) {

		global $wp;

		if ( $endpoint == 'one-click' && ! in_array( 'active', $classes ) && isset( $wp->query_vars['custom-address'] ) ) {
			$classes[] = 'active';
		}

		return $classes;
	}

	add_action( 'template_redirect', 'yith_wocc_one_click_compatibility', 5 );
}

/*####################################
* YITH WOOCOMMERCE REQUEST A QUOTE
######################################*/

if ( defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM ) {
	/**
	 * Add Request Quote compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_wcmap_request_quote_compatibility() {

		if ( class_exists( 'YITH_YWRAQ_Order_Request' ) ) {
			// remove content in my account
			remove_action( 'woocommerce_before_my_account', array( YITH_YWRAQ_Order_Request(), 'my_account_my_quotes' ) );
			remove_action( 'template_redirect', array( YITH_YWRAQ_Order_Request(), 'load_view_quote_page' ) );
		}
	}

	add_action( 'template_redirect', 'yith_wcmap_request_quote_compatibility', 5 );
}

/*####################################
* YITH WOOCOMMERCE WAITING LIST
######################################*/

if ( defined( 'YITH_WCWTL_PREMIUM' ) && YITH_WCWTL_PREMIUM ) {
	/**
	 * Add Waiting List compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_wcmap_waiting_list_compatibility() {

		if ( class_exists( 'YITH_WCWTL_Frontend' ) ) {
			// remove content in my account
			remove_action( 'woocommerce_before_my_account', array( YITH_WCWTL_Frontend(), 'add_waitlist_my_account' ) );
		}
	}

	add_action( 'template_redirect', 'yith_wcmap_waiting_list_compatibility', 5 );
}

if ( defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM ) {
	/**
	 * Add YITH Membership compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_membership_compatibility() {

		if ( class_exists( 'YITH_WCMBS_Frontend_Premium' ) ) {
			// remove content in my account
			remove_action( 'woocommerce_after_my_account', array( YITH_WCMBS_Frontend(), 'print_membership_history' ), 10 );
			remove_action( 'woocommerce_account_dashboard', array( YITH_WCMBS_Frontend(), 'print_membership_history' ), 10 );
		}
	}

	add_action( 'template_redirect', 'yith_membership_compatibility', 5 );
}

if ( defined( 'YITH_YWSBS_PREMIUM' ) && YITH_YWSBS_PREMIUM ) {
	/**
	 * Add Request Quote compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_subscription_compatibility() {
		if ( function_exists( 'YWSBS_Subscription_My_Account' ) ) {
			// remove content in my account
			remove_action( 'woocommerce_before_my_account', array( YWSBS_Subscription_My_Account(), 'my_account_subscriptions' ), 10 );
		}

		add_filter( 'yith_wcmap_endpoint_menu_class', 'yith_subscription_set_active_one_click', 10, 3 );
	}

	/**
	 * Assign active class to endpoint one-click
	 *
	 * @since  1.1.0
	 * @author Francesco Licandro
	 * @param array  $classes
	 * @param string $endpoint
	 * @param array  $options
	 * @return array
	 */
	function yith_subscription_set_active_one_click( $classes, $endpoint, $options ) {

		global $wp;

		if ( $endpoint == 'yith-subscription' && ! in_array( 'active', $classes ) && isset( $wp->query_vars['view-subscription'] ) ) {
			$classes[] = 'active';
		}

		return $classes;
	}

	add_action( 'template_redirect', 'yith_subscription_compatibility', 10 );
}

if ( ! function_exists( 'yith_wcmap_woocommerce_subscription_compatibility' ) ) {
	/**
	 * Add Request Quote compatibility
	 *
	 * @author Francesco Licandro
	 */
	function yith_wcmap_woocommerce_subscription_compatibility() {

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return;
		}

		// remove content in my account
		remove_action( 'woocommerce_before_my_account', array( 'WC_Subscriptions', 'get_my_subscriptions_template' ) );
		add_shortcode( 'ywcmap_woocommerce_subscription', 'ywcmap_woocommerce_subscription' );
	}

	function ywcmap_woocommerce_subscription( $args ) {

		global $wp;

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return '';
		}

		ob_start();
		if ( ! empty( $wp->query_vars['view-subscription'] ) ) {

			$subscription = wcs_get_subscription( absint( $wp->query_vars['view-subscription'] ) );
			wc_get_template( 'myaccount/view-subscription.php', array( 'subscription' => $subscription ), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );

		} else {
			WC_Subscriptions::get_my_subscriptions_template();
		}

		return ob_get_clean();
	}
}
add_action( 'template_redirect', 'yith_wcmap_woocommerce_subscription_compatibility', 5 );

if ( ! function_exists( 'yith_wcmap_get_icon_list' ) ) {
	/**
	 * Get FontAwesome icon list
	 *
	 * @since  2.2.3
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_icon_list() {
		if ( file_exists( YITH_WCMAP_DIR . 'plugin-options/icon-list.php' ) ) {
			return include( YITH_WCMAP_DIR . 'plugin-options/icon-list.php' );
		}

		return array();
	}
}

if ( ! function_exists( 'yith_wcmap_get_custom_css' ) ) {
	/**
	 * Get plugin custom css style
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wcmap_get_custom_css() {
		$inline_css = '
				#my-account-menu .logout a, #my-account-menu-tab .logout a {
					color:' . get_option( 'yith-wcmap-logout-color' ) . ';
					background-color:' . get_option( 'yith-wcmap-logout-background' ) . ';
				}
				#my-account-menu .logout:hover a, #my-account-menu-tab .logout:hover a {
					color:' . get_option( 'yith-wcmap-logout-color-hover' ) . ';
					background-color:' . get_option( 'yith-wcmap-logout-background-hover' ) . ';
				}
				.myaccount-menu li a {
					color:' . get_option( 'yith-wcmap-menu-item-color' ) . ';
				}
				.myaccount-menu li a:hover, .myaccount-menu li.active > a, .myaccount-menu li.is-active > a {
					color:' . get_option( 'yith-wcmap-menu-item-color-hover' ) . ';
				}';

		return apply_filters( 'yith_wcmap_get_custom_css', $inline_css );
	}
}


if( ! function_exists( 'yith_wocc_get_proteo_custom_style' ) ){
    /**
     * Get Proteo custom style
     *
     * @since 2.6.4
     * @author Alessio Torrisi
     * @return string
     */
    function yith_wcmap_get_proteo_custom_style(){

        $button_bkg             = get_theme_mod( 'yith_proteo_button_style_1_bg_color', '#448a85' );
        $button_color           = get_theme_mod( 'yith_proteo_button_style_1_text_color', '#ffffff' );
        $button_bkg_h           = get_theme_mod( 'yith_proteo_button_style_1_bg_color_hover', yith_proteo_adjust_brightness( get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ), 0.2 ) );
        $button_color_h         = get_theme_mod( 'yith_proteo_button_style_1_text_color_hover', '#ffffff' );
        $main_color_link        = get_theme_mod( 'yith_proteo_main_color_shade', '#18BCA9' );
        $main_color_link_h      = get_theme_mod( 'yith_proteo_general_link_hover_color', '#18BCA9' );

        $proteo_style = '
            #my-account-menu .logout a, #my-account-menu-tab .logout a {
				color:' . $button_color . ';
				background-color:' . $button_bkg . ';
			}
			#my-account-menu .logout:hover a, #my-account-menu-tab .logout:hover a {
				color:' . $button_color_h . ';
				background-color:' . $button_bkg_h . ';
			}
			.myaccount-menu li a {
				color:' . $main_color_link . ';
			}
			.myaccount-menu li a:hover, .myaccount-menu li.active > a, .myaccount-menu li.is-active > a {
				color:' . $main_color_link_h . ';
			}';
        ;

        return $proteo_style ;
    }
}