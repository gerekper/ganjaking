<?php
/**
 * UAEL Posts Helper.
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAEL_Posts_Helper.
 */
class UAEL_Posts_Helper {

	/**
	 * Get Post Types.
	 *
	 * @since 1.5.2
	 * @access public
	 */
	public static function get_post_types() {

		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$options = array();

		foreach ( $post_types as $post_type ) {
			$options[ $post_type->name ] = $post_type->label;
		}

		// Deprecated 'Media' post type.
		$key = array_search( 'Media', $options, true );
		if ( 'attachment' === $key ) {
			unset( $options[ $key ] );
		}

		return apply_filters( 'uael_loop_post_types', $options );
	}

	/**
	 * Get Post Taxonomies.
	 *
	 * @since 1.5.2
	 * @param string $post_type Post type.
	 * @access public
	 */
	public static function get_taxonomy( $post_type ) {

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$data       = array();

		foreach ( $taxonomies as $tax_slug => $tax ) {
			if ( ! $tax->public || ! $tax->show_ui ) {
				continue;
			}

			$data[ $tax_slug ] = $tax;
		}

		return apply_filters( 'uael_post_loop_taxonomies', $data, $taxonomies, $post_type );
	}

	/**
	 * Get size information for all currently-registered image sizes.
	 *
	 * @global $_wp_additional_image_sizes
	 * @uses   get_intermediate_image_sizes()
	 * @link   https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
	 * @since 1.5.2
	 * @return array $sizes Data for all currently-registered image sizes.
	 */
	public static function get_image_sizes() {

		global $_wp_additional_image_sizes;

		$sizes  = get_intermediate_image_sizes(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_intermediate_image_sizes_get_intermediate_image_sizes
		$result = array();

		foreach ( $sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
				$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
			} else {
				$result[ $size ] = sprintf(
					'%1$s (%2$sx%3$s)',
					ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					$_wp_additional_image_sizes[ $size ]['width'],
					$_wp_additional_image_sizes[ $size ]['height']
				);
			}
		}

		$result = array_merge(
			array(
				'full' => esc_html__( 'Full', 'uael' ),
			),
			$result
		);

		$result['custom'] = esc_html__( 'Custom', 'uael' );

		$result = apply_filters( 'uael_post_featured_image_sizes', $result );

		return $result;
	}

	/**
	 * Get list of users.
	 *
	 * @uses   get_users()
	 * @link   https://codex.wordpress.org/Function_Reference/get_users
	 * @since 1.5.2
	 * @return array $users Data for all users.
	 */
	public static function get_users() {
		$users     = get_users( array( 'role__in' => array( 'administrator', 'editor', 'author', 'contributor' ) ) );
		$user_list = array();

		if ( empty( $users ) ) {
			return $user_list;
		}

		foreach ( $users as $key => $value ) {
			$user_list[ $value->ID ] = $value->data->user_login;
		}

		return apply_filters( 'uael_post_loop_user_list', $user_list );
	}
}
