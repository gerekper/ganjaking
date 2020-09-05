<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Snippets.
 *
 * A model to handle snippets built with this plugin.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
final class Snippets_Model {


	/**
	 * Saves Rich Snippets to a post.
	 *
	 * @param int $post_id
	 * @param \wpbuddy\rich_snippets\Rich_Snippet[] $rich_snippets
	 *
	 * @return bool
	 */
	public static function update_snippets( int $post_id, array $rich_snippets ): bool {

		/**
		 * Updates Snippets Action.
		 *
		 * Allows plugins to add their own hooks when snippets get updated.
		 *
		 * @hook  wpbuddy/rich_snippets/update_snippets
		 *
		 * @param {\wpbuddy\rich_snippets\Rich_Snippet[]} $rich_snippets
		 * @param {int} $post_id
		 *
		 * @since 2.19.2
		 */
		do_action( 'wpbuddy/rich_snippets/update_snippets', $rich_snippets, $post_id );

		return false !== update_post_meta( $post_id, '_wpb_rs_schema', $rich_snippets );
	}


	/**
	 * Returns Rich_Snippet-Objects from a single post.
	 *
	 * @param int $post_id
	 *
	 * @return \wpbuddy\rich_snippets\Rich_Snippet[]
	 * @since 2.0.0
	 *
	 */
	public static function get_snippets( int $post_id ): array {

		/**
		 * @var Rich_Snippet[] $rich_snippets
		 */
		$rich_snippets = get_post_meta( $post_id, '_wpb_rs_schema', true );

		if ( ! is_array( $rich_snippets ) ) {
			return array();
		}

		$rich_snippets = array_map( function ( $snippet ) {

			if ( ! $snippet instanceof Rich_Snippet ) {
				$snippet = new Rich_Snippet( [
					'_is_main_snippet' => true,
				] );
			}

			$snippet->set_is_main_snippet( true );

			$snippet->idfy( $snippet->id, $snippet->id );

			return $snippet;
		}, $rich_snippets );

		/**
		 * Structured Data filter.
		 *
		 * Allows to filter Structured Data from a given post ID.
		 *
		 * @hook  wpbuddy/rich_snippets/model/schemas/get
		 *
		 * @param {Rich_Snippet[]} $rich_snippets Array of Rich_Snippet objects.
		 * @param {int} $post_id The post ID where the snippets are loaded from.
		 *
		 * @returns {Rich_Snippet[]} Array of Rich_Snippet objects.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'wpbuddy/rich_snippets/model/schemas/get', $rich_snippets, $post_id );
	}


	/**
	 * Returns a single snippet.
	 *
	 * @param string $snippet_id
	 * @param int $post_id
	 *
	 * @return bool|\wpbuddy\rich_snippets\Rich_Snippet
	 * @since 2.0.0
	 *
	 */
	public static function get_snippet( string $snippet_id, int $post_id ) {

		$snippets = self::get_snippets( $post_id );

		if ( isset( $snippets[ $snippet_id ] ) ) {
			return $snippets[ $snippet_id ];
		}

		return false;
	}


	/**
	 * Deletes a snippet from a post.
	 *
	 * @param string $snippet_id
	 * @param int $post_id
	 *
	 * @return bool|\WP_Error
	 */
	public static function delete_snippet( $snippet_id, $post_id ) {

		$snippets = self::get_snippets( $post_id );

		if ( ! isset( $snippets[ $snippet_id ] ) ) {
			return true;
		}

		unset( $snippets[ $snippet_id ] );
		$snippets_updated = self::update_snippets( $post_id, $snippets );

		if ( ! $snippets_updated ) {
			return new \WP_Error(
				'wpbuddy/rich_snippets/schemas/delete',
				__( 'Could not delete snippet.', 'rich-snippets-schema' )
			);
		}

		/**
		 * Delete Snippet Hook.
		 *
		 * Gets executed when a snippet gets deleted.
		 *
		 * @hook  wpbuddy/rich_snippets/delete_snippet
		 *
		 * @param {string} $snippet_id
		 * @param {int} $post_id
		 *
		 * @since 2.19.2
		 */
		do_action( 'wpbuddy/rich_snippets/delete_snippet', $snippet_id, $post_id );

		return true;
	}


	/**
	 * Get the first found snippet from a post.
	 *
	 * @param int $post_id
	 *
	 * @return \wpbuddy\rich_snippets\Rich_Snippet
	 * @since 2.0.0
	 *
	 */
	public static function get_first_snippet( int $post_id ) {

		$snippets = self::get_snippets( $post_id );
		$snippets = array_values( $snippets );

		if ( isset( $snippets[0] ) ) {
			return $snippets[0];
		}

		return new Rich_Snippet();
	}


	/**
	 * Gets the post ID where the snippet with a specific ID is saved.
	 *
	 * @param string $snippet_id
	 *
	 * @return int
	 * @since 2.2.0
	 *
	 */
	public static function get_post_id_by_snippet_id( $snippet_id ) {

		if ( empty( $snippet_id ) ) {
			return 0;
		}

		global $wpdb;

		$like = sprintf( '%%"%s"%%', $wpdb->esc_like( $snippet_id ) );
		$sql  = $wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wpb_rs_schema' AND meta_value LIKE '%s' LIMIT 1",
			$like
		);

		$post_id = $wpdb->get_var( $sql );

		return absint( $post_id );
	}


	/**
	 * Generates a snippet from post data.
	 *
	 * @param array $post_data
	 *
	 * @return Rich_Snippet[]
	 * @since 2.5.4
	 * @since 2.13.0 moved from Admin_Snippets_Controller class
	 *
	 */
	public static function generate_snippets( $post_data ) {
		$built_snippets = array();

		array_walk( $post_data, array( __CLASS__, 'sanitize_schema' ) );

		$snippets = array_diff_key( $post_data, self::fetch_references( $post_data ) );

		foreach ( $snippets as $snippet_id => $snippet ) {
			$built_snippets[ $snippet_id ] = self::create_snippet( $post_data, $snippet_id, true );
		}

		return $built_snippets;
	}


	/**
	 * Sanitizes schema values sent via the form.
	 *
	 * @param array $schema
	 *
	 * @return array
	 * @since 2.0.0
	 * @since 2.13.0 Moved from Admin_Snippets_Controller
	 *
	 */
	public static function sanitize_schema( $schema ) {

		if ( ! isset( $schema['id'] ) ) {
			return array();
		}

		$schema['id'] = sanitize_text_field( $schema['id'] );

		if ( ! isset( $schema['properties'] ) ) {
			return array();
		}

		foreach ( $schema['properties'] as $property_uid => $property_values ) {
			$property_uid = sanitize_text_field( $property_uid );

			foreach ( $property_values as $property_label => $property_value ) {
				$property_label = sanitize_text_field( $property_label );

				/**
				 * Sanitize schema property filter.
				 *
				 * Allows to filter the schema property value during sanitization.
				 *
				 * @hook  wpbuddy/rich_snippets/save_snippet/property/sanitize
				 *
				 * @param {mixed} $property_value The value of the property.
				 * @returns {mixed} The modified value.
				 *
				 * @since 2.0.0
				 */
				$property_value = apply_filters(
					'wpbuddy/rich_snippets/save_snippet/property/sanitize',
					$property_value
				);

				$schema['properties'][ $property_uid ][ $property_label ] = $property_value;
			}
		}


		return $schema;
	}


	/**
	 * Fetches all reference snippets_ids.
	 *
	 * @param $snippets
	 *
	 * @return array Array of snippet ids.
	 * @since 2.0.0
	 * @since 2.13.0 moved from Admin_Snippets_Controller class
	 *
	 */
	public static function fetch_references( $snippets ) {

		$refs = array();

		foreach ( $snippets as $snippet ) {
			if ( ! isset( $snippet['properties'] ) ) {
				continue;
			}

			if ( ! is_array( $snippet['properties'] ) ) {
				continue;
			}

			foreach ( $snippet['properties'] as $prop ) {
				if ( ! isset( $prop['ref'] ) ) {
					continue;
				}

				if ( empty( $prop['ref'] ) ) {
					continue;
				}

				$refs[ $prop['ref'] ] = '';
			}
		}

		return $refs;

	}


	/**
	 * Processes an array of classes to a single Rich_Snippet object.
	 *
	 * @param array $schemas
	 * @param string $snippet_id
	 * @param bool $is_parent If this is a parent snippet.
	 *
	 * @return Rich_Snippet
	 * @since 2.5.4 added $is_parent parameter.
	 *
	 * @since 2.0.0
	 * @since 2.13.0 moved from Admin_Snippets_Controller
	 */
	public static function create_snippet( &$schemas, $snippet_id = 'main', $is_parent = false ) {

		$plugin_data = Helper_Model::instance()->get_plugin_data();

		$snippet = new Rich_Snippet( [
			'_is_main_snippet' => $is_parent,
			'_version_created' => $plugin_data['Version'] ?? null,
			'_loop'            => isset( $schemas[ $snippet_id ]['loop'] ) ? $schemas[ $snippet_id ]['loop'] : null
		] );

		if ( ! isset( $schemas[ $snippet_id ] ) ) {
			return $snippet;
		}

		$snippet->type = Helper_Model::instance()->remove_schema_url( $schemas[ $snippet_id ]['id'] );

		if ( ! isset( $schemas[ $snippet_id ]['properties'] ) ) {
			return $snippet;
		}

		$snippet->id = $snippet_id;

		$allowed_html = [
			'h1'     => [],
			'h2'     => [],
			'h3'     => [],
			'h4'     => [],
			'h5'     => [],
			'h6'     => [],
			'br'     => [],
			'ol'     => [],
			'ul'     => [],
			'li'     => [],
			'a'      => [
				'href' => array(),
			],
			'p'      => [],
			'div'    => [],
			'b'      => [],
			'strong' => [],
			'i'      => [],
			'em'     => [],
		];

		$props = array();

		foreach ( $schemas[ $snippet_id ]['properties'] as $property_uid => $property_values ) {
			$p_label = Helper_Model::instance()->remove_schema_url( $property_values['id'] );

			$p_subfield = $property_values['subfield_select'];

			if ( isset( $property_values['ref'] ) && ! empty( $property_values['ref'] ) && isset( $schemas[ $property_values['ref'] ] ) ) {
				$p_value = self::create_snippet( $schemas, $property_values['ref'] );
			} elseif ( false !== stripos( $p_subfield, 'textfield' ) ) {

				/**
				 * Allowed HTML filter for input fields.
				 *
				 * Allows to change what HTML types are allowed on input fields.
				 *
				 * @hook  wpbuddy/rich_snippets/model/create_snippets/allowed_html
				 *
				 * @param {array} $allowed_Html Array of allowed HTML tags. @see wp_kses() function in WordPress.
				 * @param {string} $p_subfield The subfield type. E.g. "textfield".
				 * @param {array} $property_values The property values.
				 *
				 * @returns {array} Array of allowed HTML tags. @see wp_kses() function in WordPress.
				 *
				 * @since 2.17.0
				 */
				$a_html = apply_filters( 'wpbuddy/rich_snippets/model/create_snippets/allowed_html', $allowed_html, $p_subfield, $property_values );

				# strip tags: only allow @see https://developers.google.com/search/docs/data-types/faqpage#answer
				$p_value = wp_kses(
					$property_values['textfield'],
					$a_html
				);
			} elseif ( false !== stripos( $p_subfield, 'misc_rating_5_star' ) ) {
				$p_value = absint( $property_values['rating5'] );
			} elseif ( false !== stripos( $p_subfield, 'misc_rating_100_points' ) ) {
				$p_value = absint( $property_values['rating100'] );
			} elseif ( false !== stripos( $p_subfield, 'misc_duration_minutes' ) ) {
				$p_value = absint( $property_values['duration_minutes'] );
			} else {
				$p_value = null;
			}

			$p_overwrite = $property_values['overridable'] ?? false;
			$p_overwrite = Helper_Model::instance()->string_to_bool( $p_overwrite );

			$p_overwrite_multiple = $property_values['overridable_multiple'] ?? false;
			$p_overwrite_multiple = Helper_Model::instance()->string_to_bool( $p_overwrite_multiple );

			$props[] = array(
				'id'    => $property_uid,
				'name'  => $p_label,
				'value' => array(
					$p_subfield,
					$p_value,
					'overridable'          => $p_overwrite,
					'overridable_multiple' => $p_overwrite_multiple,
				),
			);
		}

		$snippet->set_props( $props );

		return $snippet;
	}


	/**
	 * Converts (and sanitizes) a raw json object and creates a Rich_Snippet object.
	 *
	 * @param array $obj
	 *
	 * @return Rich_Snippet
	 * @since 2.13.0
	 */
	public static function convert_from_json( $obj ) {

		if ( isset( $obj['loop'] ) ) {
			$obj['_loop'] = $obj['loop'];
		}

		$new_obj = [];

		foreach ( $obj as $key => $el ) {
			if ( '_is_export' === $key ) {
				continue;
			}

			if ( is_scalar( $el ) ) {
				$new_obj[ $key ] = sanitize_text_field( $el );
				continue;
			}

			if ( is_array( $el ) ) {
				if ( isset( $el[0] ) ) {
					$new_obj[ $key ][0] = sanitize_text_field( $el[0] );
				}

				if ( isset( $el[1] ) ) {
					if ( is_scalar( $el[1] ) ) {
						$new_obj[ $key ][1] = sanitize_text_field( $el[1] );
					} elseif ( is_array( $el[1] ) ) {
						$new_obj[ $key ][1] = self::convert_from_json( $el[1] );
					}
				}

				if ( isset( $el['overridable'] ) ) {
					$new_obj[ $key ]['overridable'] = Helper_Model::instance()->string_to_bool( $el['overridable'] );
				}

				if ( isset( $el['overridable_multiple'] ) ) {
					$new_obj[ $key ]['overridable_multiple'] = Helper_Model::instance()->string_to_bool( $el['overridable_multiple'] );
				}
			}
		}

		return new Rich_Snippet( $new_obj );
	}
}
