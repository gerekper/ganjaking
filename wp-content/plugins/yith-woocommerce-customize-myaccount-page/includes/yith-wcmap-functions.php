<?php
/**
 * Plugins Functions and Hooks
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'YITH_WCMAP' ) ) {
	/**
	 * Unique access to instance of YITH_WCMAP class
	 *
	 * @since 1.0.0
	 * @return YITH_WCMAP
	 */
	function YITH_WCMAP() { // phpcs:ignore
		static $class = '';
		if ( empty( $class ) ) {
			$class = 'YITH_WCMAP';
			if ( class_exists( 'YITH_WCMAP_Premium' ) ) {
				$class = 'YITH_WCMAP_Premium';
			} elseif ( class_exists( 'YITH_WCMAP_Extended' ) ) {
				$class = 'YITH_WCMAP_Extended';
			}
		}

		return $class::get_instance();
	}
}

if ( ! function_exists( 'YITH_WCMAP_Admin' ) ) {
	/**
	 * The admin class
	 *
	 * @since  2.5.0
	 * @return YITH_WCMAP_Admin|null
	 */
	function YITH_WCMAP_Admin() { // phpcs:ignore
		return YITH_WCMAP()->admin;
	}
}

if ( ! function_exists( 'YITH_WCMAP_Frontend' ) ) {
	/**
	 * The frontend class
	 *
	 * @since  2.5.0
	 * @return YITH_WCMAP_Frontend|null
	 */
	function YITH_WCMAP_Frontend() { // phpcs:ignore
		return YITH_WCMAP()->frontend;
	}
}

if ( ! function_exists( 'yith_wcmap_sanitize_item_key' ) ) {
	/**
	 * Sanitize an item key/slug
	 *
	 * @access public
	 * @since  1.0.0
	 * @param string $key The item key to sanitize.
	 * @return string
	 */
	function yith_wcmap_sanitize_item_key( $key ) {
		// Build endpoint key.
		$field_key = strtolower( $key );
		$field_key = trim( $field_key );
		// Clear from space and add -.
		$field_key = sanitize_title( $field_key, '' );

		return $field_key;
	}
}

if ( ! function_exists( 'yith_wcmap_build_label' ) ) {
	/**
	 * Build endpoint label by name
	 *
	 * @since  2.0.0
	 * @param string $name The name to use for the label.
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
	 * @param string $endpoint The endpoint key.
	 * @return array
	 */
	function yith_wcmap_get_default_endpoint_options( $endpoint ) {

		$endpoint_name = yith_wcmap_build_label( $endpoint );

		// Build endpoint options.
		$options = array(
			'slug'    => $endpoint,
			'active'  => true,
			'label'   => $endpoint_name,
			'content' => '',
		);

		/**
		 * APPLY_FILTERS: yith_wcmap_get_default_endpoint_options
		 *
		 * Filters the default options for new endpoints.
		 *
		 * @param array  $options  Endpoint options.
		 * @param string $endpoint Endpoint key.
		 *
		 * @return array
		 */
		return apply_filters( 'yith_wcmap_get_default_endpoint_options', $options, $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_is_default_item' ) ) {
	/**
	 * Check if an item is a default
	 *
	 * @since  2.4.0
	 * @param string $item The item key to check if is default or not.
	 * @return boolean
	 */
	function yith_wcmap_is_default_item( $item ) {
		$defaults = YITH_WCMAP()->items->get_default_items();

		return array_key_exists( $item, $defaults );
	}
}

if ( ! function_exists( 'yith_wcmap_item_already_exists' ) ) {
	/**
	 * Check if item already exists
	 *
	 * @since  2.4.0
	 * @param string $endpoint The endpoint key to check if exists or not.
	 * @return boolean
	 */
	function yith_wcmap_item_already_exists( $endpoint ) {

		// Check first in key.
		$field_key = YITH_WCMAP()->items->get_items_keys();
		$exists    = in_array( $endpoint, $field_key, true );

		// Check also in slug.
		if ( ! $exists ) {
			$endpoint_slug = YITH_WCMAP()->items->get_items_slug();
			$exists        = in_array( $endpoint, $endpoint_slug, true );
		}

		return $exists;
	}
}

if ( ! function_exists( 'yith_wcmap_get_current_endpoint' ) ) {
	/**
	 * Check if and endpoint is active on frontend. Used for add class 'active' on account menu in frontend
	 *
	 * @since  2.0.0
	 * @return string
	 */
	function yith_wcmap_get_current_endpoint() {

		global $wp;

		$current = '';
		foreach ( WC()->query->get_query_vars() as $key => $value ) {
			// Check for dashboard.
			if ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) {
				$current = 'dashboard';
				break;
			} elseif ( isset( $wp->query_vars[ $key ] ) ) {
				$current = $key;
				break;
			}
		}

		/**
		 * APPLY_FILTERS: yith_wcmap_get_current_endpoint
		 *
		 * Filters the current active endpoint.
		 *
		 * @param string $current Current endpoint key.
		 *
		 * @return string
		 */
		return apply_filters( 'yith_wcmap_get_current_endpoint', $current );
	}
}

if ( ! function_exists( 'yith_wcmap_endpoints_list' ) ) {
	/**
	 * Get endpoints slugs for register endpoints
	 *
	 * @since  2.0.0
	 * @return array
	 */
	function yith_wcmap_endpoints_list() {

		$return = array();
		$fields = YITH_WCMAP()->items->get_items();

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['slug'] ) ) {
				$return[ $key ] = $field['label'];
			}
		}

		/**
		 * APPLY_FILTERS: yith_wcmap_endpoints_list
		 *
		 * Filters the endpoints list.
		 *
		 * @param array $endpoints Endpoints list.
		 * @param array $fields    Endpoints options.
		 *
		 * @return array
		 */
		return apply_filters( 'yith_wcmap_endpoints_list', $return, $fields );
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoint_by' ) ) {
	/**
	 * Get endpoint by a specified key
	 *
	 * @since  2.0.0
	 * @param string $value The value to search.
	 * @param string $key The value type. Can be key or slug.
	 * @param array  $items Endpoint array.
	 * @return array
	 */
	function yith_wcmap_get_endpoint_by( $value, $key = 'key', $items = array() ) {

		/**
		 * APPLY_FILTERS: yith_wcmap_get_endpoint_by_accepted_key
		 *
		 * Filters the keys to get endpoints by.
		 *
		 * @param array $accepted_keys Accepted keys to get endpoints.
		 *
		 * @return array
		 */
		$accepted = apply_filters( 'yith_wcmap_get_endpoint_by_accepted_key', array( 'key', 'slug' ) );
		$find     = array();

		if ( ! in_array( $key, $accepted, true ) ) {
			return $find;
		}

		if ( empty( $items ) ) {
			$items = YITH_WCMAP()->items->get_items();
		}

		foreach ( $items as $id => $item ) {
			if ( ( 'key' === $key && $id === $value ) || ( isset( $item[ $key ] ) && $item[ $key ] === $value ) ) {
				$find[ $id ] = $item;
			}
		}

		if ( has_filter( 'yith_wcmap_get_endpoint_by_result' ) ) {
			$find = apply_filters_deprecated( 'yith_wcmap_get_endpoint_by_result', array( $find ), '3.12.0', 'yith_wcmap_get_endpoint_by' );
		}

		/**
		 * APPLY_FILTERS: yith_wcmap_get_endpoint_by
		 *
		 * Filters the endpoint retrieved by a specific key.
		 *
		 * @param array  $endpoint Endpoint retrieved.
		 * @param string $value    Endpoint key.
		 * @param string $key      Key used to get endpoint.
		 * @param array  $items    Endpoints.
		 *
		 * @return array
		 */
		return apply_filters( 'yith_wcmap_get_endpoint_by', $find, $value, $key, $items );
	}
}

if ( ! function_exists( 'yith_wcmap_print_single_endpoint' ) ) {
	/**
	 * Print single endpoint on front menu
	 *
	 * @since  2.0.0
	 * @param string $endpoint The endpoint to print.
	 * @param array  $options The endpoint options.
	 * @deprecated
	 */
	function yith_wcmap_print_single_endpoint( $endpoint, $options ) {
		YITH_WCMAP_Frontend()->print_single_item( $endpoint, $options );
	}
}

if ( ! function_exists( 'yith_wcmap_is_plugin_item' ) ) {
	/**
	 * Check if an item is a plugin
	 *
	 * @since  2.4.0
	 * @param string $item The item key to check if is a plugin or not.
	 * @return boolean
	 */
	function yith_wcmap_is_plugin_item( $item ) {
		$plugins = YITH_WCMAP()->items->get_plugins_items();

		return array_key_exists( $item, $plugins );
	}
}

// DEPRECATED.

if ( ! function_exists( 'yith_wcmap_get_custom_css' ) ) {
	/**
	 * Get plugin custom css style
	 *
	 * @since  2.3.0
	 * @return string
	 * @deprecated
	 */
	function yith_wcmap_get_custom_css() {
		return YITH_WCMAP()->frontend->get_custom_css();
	}
}
