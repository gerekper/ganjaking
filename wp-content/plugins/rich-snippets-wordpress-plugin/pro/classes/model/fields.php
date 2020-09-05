<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Schema_Property;
use wpbuddy\rich_snippets\Schemas_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Fields.
 *
 * Prepares HTML fields to use.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Fields_Model extends \wpbuddy\rich_snippets\Fields_Model {

	/**
	 * Fields_Model constructor.
	 * @since 2.19.0
	 */
	public function __construct() {
		parent::__construct();

		add_filter( 'wpbuddy/rich_snippets/fields/link_to_subselect/values', [ $this, 'more_references' ] );
	}

	/**
	 * Adds Global Snippets to the list of references.
	 *
	 * @param array $values
	 *
	 * @return array
	 *
	 * @since 2.19.0
	 */
	public static function more_references( $values ) {
		$global_posts = call_user_func( function () {

			$cache = wp_cache_get( 'post_reference_values', 'wpb_rs' );

			if ( is_array( $cache ) ) {
				return $cache;
			}

			global $wpdb, $post;

			$sql = "SELECT pm.meta_value as global_schemas, p.post_title, p.ID "
			       . " FROM {$wpdb->posts} p "
			       . " LEFT JOIN {$wpdb->postmeta} as pm ON (p.ID = pm.post_id AND pm.meta_key = '_wpb_rs_schema') "
			       . " WHERE p.post_status = 'publish' AND p.post_type = 'wpb-rs-global' ";

			if ( isset( $post ) && $post instanceof \WP_Post ) {
				# do not include Post ID
				$sql .= sprintf( ' AND p.ID != "%d" ', $post->ID );
			}

			$results = $wpdb->get_results( $sql );

			if ( ! is_array( $results ) ) {
				wp_cache_set( 'post_reference_values', array(), 'wpb_rs', MINUTE_IN_SECONDS );

				return array();
			}

			$values = array();

			foreach ( $results as $result ) {
				$global_schemas = maybe_unserialize( $result->global_schemas );
				if ( ! is_array( $global_schemas ) ) {
					continue;
				}

				/**
				 * @var \wpbuddy\rich_snippets\Rich_Snippet $global_schema
				 */
				foreach ( $global_schemas as $snippet_uid => $global_schema ) {
					$values[ 'global_snippet_' . $snippet_uid ] = sprintf(
						_x( 'Global snippet: %2$s/%1$s (%3$d)', '%1$s is the title of the global schema. %2$s is the schema class name. %3$d is the post ID', 'rich-snippets-schema' ),
						$result->post_title,
						$global_schema->type,
						$result->ID
					);
				}
			}

			wp_cache_set( 'post_reference_values', $values, 'wpb_rs', MINUTE_IN_SECONDS );

			return $values;

		} );

		$values = array_merge( $values, $global_posts );

		return $values;
	}


	/**
	 * Returns a list of values available for loops.
	 *
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public static function get_loop_values() {
		$values = [
			''             => __( 'No loop', 'rich-snippets-schema' ),
			'main_query'   => __( 'Main query items (ie. for archive pages)', 'rich-snippets-schema' ),
			'page_parents' => __( 'Page parents', 'rich-snippets-schema' ),
		];

		$menus = call_user_func( function () {
			$values = [];

			$menus = wp_get_nav_menus();

			foreach ( $menus as $menu ) {
				if ( ! is_object( $menu ) ) {
					continue;
				}

				if ( ! isset( $menu->term_id ) ) {
					continue;
				}

				if ( ! isset( $menu->name ) ) {
					continue;
				}

				$values[ 'menu_' . $menu->term_id ] = sprintf(
					_x( 'Menu: %s', 'Menu label', 'rich-snippets-schema' ),
					esc_html( $menu->name )
				);
			}

			return $values;
		} );

		$values = array_merge( $values, $menus );

		$taxonomies = call_user_func( function () {
			$values = [];

			/**
			 * @var \WP_Taxonomy[]
			 */
			$taxonomies = get_taxonomies(
				[ 'public' => true ],
				'objects'
			);

			foreach ( $taxonomies as $tax_key => $tax ) {
				$values[ 'taxonomy_' . $tax_key ] = sprintf(
					_x( 'Taxonomy: %s', 'Taxonomy label', 'rich-snippets-schema' ),
					esc_html( $tax->label )
				);
			}

			return $values;
		} );

		$values = array_merge( $values, $taxonomies );

		/**
		 * 'loop' subselect values filter.
		 *
		 * This filter can be used to add additional options to the 'link to' subfield select.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/loop_subselect/values
		 *
		 * @param {array} $values The return parameter: an array of values.
		 * @returns {array} An array of values.
		 *
		 * @since 2.8.0
		 */
		$values = apply_filters(
			'wpbuddy/rich_snippets/fields/loop_subselect/values',
			$values
		);

		return $values;
	}


	/**
	 * Fetches 'loop' subselect options.
	 *
	 * @param Schema_Property $prop
	 * @param string $schema
	 * @param string $selected The selected item.
	 *
	 * @return string[] Array of HTML <option> fields.
	 * @since 2.8.0
	 *
	 */
	public static function get_loop_subselect_options( $schema, $selected ) {
		$options = array();

		$values = self::get_loop_values();

		foreach ( $values as $value => $label ) {
			$options[] = sprintf(
				'<option data-use-textfield="%s" value="%s" %s>%s</option>',
				false !== stripos( $value, 'textfield' ) ? 1 : 0,
				$value,
				selected( $selected, $value, false ),
				esc_html( $label )
			);
		}

		/**
		 * Internal 'loop' values.
		 *
		 * This filter can be used to add additional options to the 'loop' subselect.
		 *
		 * @hook  wpbuddy/rich_snippets/fields/loop_subselect/options
		 *
		 * @param {array}  $options The return parameter: an array of options.
		 * @param {string} $schema   The current schema class.
		 * @param {string} $selected The current selected item.
		 *
		 * @returns {array} An array of options.
		 *
		 * @since 2.8.0
		 */
		return apply_filters(
			'wpbuddy/rich_snippets/fields/loop_subselect/options',
			$options,
			$schema,
			$selected
		);
	}


}