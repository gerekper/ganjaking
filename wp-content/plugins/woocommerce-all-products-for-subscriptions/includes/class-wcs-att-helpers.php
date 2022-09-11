<?php
/**
 * WCS_ATT_Helpers class
 *
 * @package  WooCommerce All Products For Subscriptions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Helper Functions.
 *
 * @class    WCS_ATT_Helpers
 * @version  2.3.0
 */
class WCS_ATT_Helpers {

	/**
	 * Runtime cache for simple storage.
	 *
	 * @var array
	 */
	public static $cache = array();

	/**
	 * Simple runtime cache getter.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @return mixed
	 */
	public static function cache_get( $key, $group_key = '' ) {

		$value = null;

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				$value = self::cache_get( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			$value = self::$cache[ $key ];
		}

		return $value;
	}

	/**
	 * Simple runtime cache setter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_set( $key, $value, $group_key = '' ) {

		if ( $group_key ) {

			if ( null === ( $group_id = self::cache_get( $group_key . '_id' ) ) ) {
				$group_id = md5( $group_key );
				self::cache_set( $group_key . '_id', $group_id );
			}

			self::$cache[ $group_key . '_' . $group_id . '_' . $key ] = $value;

		} else {
			self::$cache[ $key ] = $value;
		}
	}

	/**
	 * Simple runtime cache unsetter.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  string  $group_key
	 * @return void
	 */
	public static function cache_delete( $key, $group_key = '' ) {

		if ( $group_key ) {

			if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
				self::cache_delete( $group_key . '_' . $group_id . '_' . $key );
			}

		} elseif ( isset( self::$cache[ $key ] ) ) {
			unset( self::$cache[ $key ] );
		}
	}

	/**
	 * Simple runtime group cache invalidator.
	 *
	 * @param  string  $key
	 * @param  string  $group_key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function cache_invalidate( $group_key ) {

		if ( $group_id = self::cache_get( $group_key . '_id' ) ) {
			$group_id = md5( $group_key . '_' . $group_id );
			self::cache_set( $group_key . '_id', $group_id );
		}
	}

	/**
	 * True when processing a FE request.
	 *
	 * @return boolean
	 */
	public static function is_front_end() {
		$is_fe = ( ! is_admin() ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		return $is_fe;
	}

	/**
	 * Builds terms tree of a flatten terms array.
	 *
	 * @since  4.0.0
	 *
	 * @param  array  $terms
	 * @param  int    $parent_id
	 * @return array
	 */
	public static function build_taxonomy_tree( $terms, $parent_id = 0 ) {

		$tree = array();
		foreach ( $terms as $index => $term ) {
			if ( $term->parent === $parent_id && ! isset( $tree[ $term->term_id ] ) ) {
				$tree[ $term->term_id ]           = $term;
				$tree[ $term->term_id ]->children = self::build_taxonomy_tree( $terms, $term->term_id );
			}
		}

		return $tree;
	}

	/**
	 * Builds a list of options from a terms tree.
	 *
	 * @since  4.0.0
	 *
	 * @param  array  $term_tree
	 * @param  array  $args
	 * @return void
	 */
	public static function get_taxonomy_tree_options( $term_tree, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'options'       => array(),
			'prefix_html'   => '',
			/* translators: %1$s: Term, %2$s: Next term. */
			'seperator'     => _x( '%1$s&nbsp;&gt;&nbsp;%2$s', 'term separator', 'woocommerce-all-products-for-subscriptions' ),
			'shorten_text'  => true,
			'shorten_level' => 5,
			'term_path'     => array()
		) );

		$term_path = $args[ 'term_path' ];
		$options   = $args[ 'options' ];

		foreach ( $term_tree as $term ) {

			$term_path[] = $term->name;
			$option_text = $term->name;

			if ( ! empty( $args[ 'prefix_html' ] ) ) {
				$option_text = sprintf( $args[ 'seperator' ], $args[ 'prefix_html' ], $option_text );
			}

			if ( $args[ 'shorten_text' ] && count( $term_path ) > $args[ 'shorten_level' ] ) {
				/* translators: %1$s: Term, %2$s: Next term. */
				$options[ $term->term_id ] = sprintf( _x( '%1$s&nbsp;&gt;&nbsp;&hellip;&nbsp;&gt;&nbsp;%2$s', 'many terms separator', 'woocommerce-all-products-for-subscriptions' ), $term_path[ 0 ], $term_path[ count( $term_path ) - 1 ] );
			} else {
				$options[ $term->term_id ] = $option_text;
			}

			// Recursive call to print children.
			if ( ! empty( $term->children ) ) {

				// Reset `prefix_html` argument to recursive mode.
				$reset_args                  = $args;
				$reset_args[ 'prefix_html' ] = ! is_null( $args[ 'prefix_html' ] ) ? $option_text : null;
				$reset_args[ 'term_path' ]   = $term_path;
				$reset_args[ 'options' ]     = $options;

				$options = self::get_taxonomy_tree_options( $term->children, $reset_args );
			}

			$term_path = $args[ 'term_path' ];
		}

		return $options;
	}
}
