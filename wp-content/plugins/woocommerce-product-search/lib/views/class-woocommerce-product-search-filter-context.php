<?php
/**
 * class-woocommerce-product-search-filter-context.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.9.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_filter_context' ) ) {
	/**
	 * Prepares the filter context based on the options.
	 *
	 * @param array $atts desired context options
	 */
	function woocommerce_product_filter_context( $atts = array() ) {
		WooCommerce_Product_Search_Filter_Context::set_context( $atts );
	}
}

/**
 * Context for filters.
 */
class WooCommerce_Product_Search_Filter_Context {

	/**
	 * @var array the current filter context
	 */
	private static $context = null;

	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_context', array( __CLASS__, 'shortcode_context' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 3 );
	}

	/**
	 * Returns the current filter context.
	 *
	 * 1. If the filter context has been determined using set_context(), that will be returned subsequently.
	 * 2. If no filter context has been determined using set_context(), this will try to obtain the
	 *    filter context for the given $post_id or, if not provided, for the current post if applicable.
	 *    If obtained, that filter context will be returned subsequently.
	 *
	 * @param int $post_id
	 *
	 * @return array|null an array holding the context or null if there is no context for the post
	 */
	public static function get_context( $post_id = null ) {

		if ( self::$context !== null ) {
			return self::$context;
		}

		$context = null;

		if ( $post_id === null ) {
			$object = get_queried_object();
			if ( $object instanceof WP_Post ) {
				$post_id = get_queried_object_id();
				if ( !$post_id ) {
					$post_id = null;
				}
			}
		}

		if ( $post_id !== null ) {
			$meta = get_post_meta( $post_id, 'woocommerce_product_filter_context', true );
			if ( is_array( $meta ) && count( $meta ) > 0 ) {
				$context = self::get_context_from_attributes( $meta );
				self::$context = $context;
			}
		}

		return $context;
	}

	/**
	 * Determines the current filter context based on the attributes given.
	 *
	 * @param array $atts if omitted or null is passed, clears the current filter context
	 */
	public static function set_context( $atts = null ) {
		$context = null;
		if ( $atts !== null && is_array( $atts ) ) {
			$context_attributes = self::get_context_attributes( $atts );
			$context = self::get_context_from_attributes( $context_attributes );
		}
		self::$context = $context;
	}

	/**
	 * Saves the filter context.
	 *
	 * @param int $post_id
	 * @param object $post
	 * @param boolean $update
	 */
	public static function save_post( $post_id = null, $post = null, $update = false ) {


		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
		} else {
			if ( $post = get_post( $post_id ) ) {
				$found = false;
				$tags = array( 'woocommerce_product_filter_context', 'woocommerce_product_filter_products' );
				foreach ( $tags as $tag ) {
					if ( has_shortcode( $post->post_content, $tag ) ) {
						$content = $post->post_content;
						if ( strpos( $content, '[' ) !== false ) {
							if ( shortcode_exists( $tag ) ) {
								preg_match_all( '/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER );
								if ( !empty( $matches ) ) {
									foreach ( $matches as $shortcode ) {
										if ( $tag === $shortcode[2] ) {
											$attr = shortcode_parse_atts( $shortcode[3] );
											$context_attributes = self::get_context_attributes( $attr );

											update_post_meta( $post_id, 'woocommerce_product_filter_context', $context_attributes );
											$found = true;
										}

									}
								}
							}
						}
					}
					if ( $found ) {
						break;
					}
				}
				if ( !$found ) {
					delete_post_meta( $post_id, 'woocommerce_product_filter_context' );
				}
			}
		}
	}

	/**
	 * [woocommerce_product_filter_context] shortcode handler.
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string always an empty string
	 */
	public static function shortcode_context( $atts = array(), $content = '' ) {
		return '';
	}

	/**
	 * Scans the given array and return an array with allowed attributes.
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public static function get_context_attributes( $atts = array() ) {

		$atts = shortcode_atts(
			array(

				'taxonomy'              => '',
				'term'                  => '',
				'taxonomy_op'           => 'OR'
			),
			$atts,
			'woocommerce_product_filter_context'
		);

		foreach ( $atts as $key => $value ) {
			$valid = true;
			switch ( $key ) {

				case 'taxonomy' :
				case 'term' :
					if ( is_string( $value ) ) {
						$value = array_map( 'trim', explode( ',', $value ) );
					}
					break;
				case 'taxonomy_op' :
					$value = strtoupper( $value );
					switch( $value ) {
						case 'OR' :
						case 'AND' :
							break;
						default:
							$value = 'OR';
					}
					break;
				default :
					$valid = false;
			}
			if ( $valid ) {
				$atts[$key] = $value;
			} else {
				unset( $atts[$key] );
			}
		}

		return $atts;
	}

	/**
	 * Prepares a filter context based on the given attributes.
	 *
	 * @param array $atts
	 * @return array
	 */
	public static function get_context_from_attributes( $atts = array() ) {
		$context = array();

		$taxonomy_terms = array();
		if ( is_array( $atts['taxonomy'] ) && is_array( $atts['term'] ) ) {

			$taxonomies = array();
			foreach ( $atts['taxonomy'] as $maybe_taxonomy ) {
				if ( $taxonomy = get_taxonomy( trim( $maybe_taxonomy ) ) ) {
					$taxonomies[] = $taxonomy->name;
				}
			}

			$n_taxonomies = count( $taxonomies );
			if ( $n_taxonomies  === 0 ) {
				$taxonomies = array( 'product_cat' );
				$n_taxonomies = 1;
			}

			if ( count( $atts['term'] ) > 0 ) {
				$entries = array_map( 'trim', $atts['term'] );
				$term_ids = array();
				for ( $i = 0; $i < count( $entries ); $i++ ) {
					$entry = $entries[$i];
					if ( $n_taxonomies - 1 >= $i ) {
						$taxonomy = $taxonomies[$i];
					} else {
						$taxonomy = $taxonomies[$n_taxonomies - 1];
					}
					if ( !( $term = get_term_by( 'id', $entry, $taxonomy ) ) ) {
						if ( !( $term = get_term_by( 'slug', $entry, $taxonomy ) ) ) {
							$term = get_term_by( 'name', $entry, $taxonomy );
						}
					}
					if ( $term ) {
						$term_ids[] = $term->term_id;
						$taxonomy_terms[$taxonomy][] = $term->term_id;
					}
				}
			}
		}
		$context['taxonomy_terms'] = $taxonomy_terms;

		$taxonomy_op = '';
		if ( isset( $atts['taxonomy_op'] ) ) {
			$taxonomy_op = strtoupper( $atts['taxonomy_op'] );
			switch ( $taxonomy_op ) {
				case 'AND' :
				case 'OR' :
					break;
				default :
					$taxonomy_op = '';
			}
		}
		$context['taxonomy_op'] = $taxonomy_op;


		return $context;
	}

}
WooCommerce_Product_Search_Filter_Context::init();
