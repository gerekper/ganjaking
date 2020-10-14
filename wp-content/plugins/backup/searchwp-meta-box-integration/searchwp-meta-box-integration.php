<?php
/*
Plugin Name: SearchWP Meta Box Integration
Plugin URI: https://searchwp.com/extensions/meta-box-integration/
Description: Integrate SearchWP with Meta Box
Version: 1.0
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2019 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_META_BOX_INTEGRATION_VERSION' ) ) {
	define( 'SEARCHWP_META_BOX_INTEGRATION_VERSION', '1.0' );
}

/**
 * SearchWP Meta Box Integration
 *
 * Class SearchWP_Meta_Box_Integration
 */
class SearchWP_Meta_Box_Integration {
	private $flag = '[mbct:';

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initializer adds filters for SearchWP Meta Groups and SearchWP Extra Metadata.
	 *
	 * @since 1.0
	 */
	public function init() {
		if ( ! function_exists( 'rwmb_get_registry' ) ) {
			return;
		}

		add_filter( 'searchwp_meta_groups', array( $this, 'add_meta_box_fields' ), 10, 2 );
		add_filter( 'searchwp_meta_groups', array( $this, 'add_meta_box_custom_table_groups' ), 10, 2 );

		add_filter( 'searchwp_extra_metadata', array( $this, 'index_custom_table_data' ), 10, 2 );
	}

	/**
	 * Retrieves a list of Custom Fields across all engines for a given post type.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return array Custom Field keys for the post type across all engines
	 *
	 * @since 1.0
	 */
	public function get_custom_fields_for_post_type_from_settings( $post_type = 'post' ) {
		$custom_fields = array();
		$engines = SWP()->settings['engines'];

		foreach ( $engines as $engine_settings ) {
			$post_types = array_filter(
				$engine_settings,
				function( $post_type ) {
					return ! empty( $post_type['enabled'] );
				}
			);

			foreach ( $post_types as $post_type ) {
				if (
					! isset( $post_type['weights']['cf'] )
					|| empty( $post_type['weights']['cf'] ) ) {
						continue;
				}

				$custom_fields = array_merge(
					$custom_fields,
					wp_list_pluck( $post_type['weights']['cf'], 'metakey' )
				);
			}
		}

		return array_unique( array_values( $custom_fields ) );
	}

	/**
	 * Retrieves MB Custom Table Custom Fields from all engines.
	 *
	 * @param WP_Post $the_post The post being indexed.
	 *
	 * @return array The Custom Field keys.
	 *
	 * @since 1.0
	 */
	public function get_meta_box_custom_table_fields_from_post( $the_post ) {
		$custom_fields = $this->get_custom_fields_for_post_type_from_settings( $the_post->post_type );

		$custom_fields = array_filter(
			$custom_fields,
			function( $custom_field ) {
				return false !== strpos( $custom_field, ' ' . $this->flag );
			}
		);

		return $custom_fields;
	}

	/**
	 * Produces an associative array with keys for each MB Custom Table that have values
	 * of the Meta Box field names for that custom table.
	 *
	 * @param array $custom_fields The array to normalize.
	 *
	 * @return array The normalized Custom Fields.
	 *
	 * @since 1.0
	 */
	private function normalize_mb_custom_table_fields( $custom_fields ) {
		$normalized = array();

		foreach ( $custom_fields as $custom_field ) {
			$flag_pos = strpos( $custom_field, $this->flag );

			$custom_table_name = trim(
				str_replace(
					$this->flag,
					'',
					substr(
						$custom_field,
						$flag_pos - 1,
						-1 // Consider the closing bracket.
					)
				)
			);

			if ( ! array_key_exists( $custom_table_name, $normalized ) ) {
				$normalized[ $custom_table_name ] = array();
			}

			$field_name = trim(
				substr(
					$custom_field,
					0,
					strpos( $custom_field, $this->flag )
				)
			);

			$normalized[ $custom_table_name ][] = $field_name;
		}

		return $normalized;
	}

	/**
	 * Utilize Extra Meta to capture content from MB Custom Table where applicable.
	 *
	 * @param array   $extra_meta The existing Extra Meta.
	 * @param WP_Post $the_post   The post being indexed.
	 *
	 * @return array The updated Extra Meta.
	 *
	 * @since 1.0
	 */
	public function index_custom_table_data( $extra_meta, $the_post ) {
		if ( ! function_exists( 'rwmb_meta' ) ) {
			return;
		}

		$mb_custom_table_fields = $this->normalize_mb_custom_table_fields(
			$this->get_meta_box_custom_table_fields_from_post( $the_post )
		);

		if ( empty( $mb_custom_table_fields ) ) {
			return $extra_meta;
		}

		// Using our normalized Custom Table fields, we can re-map the Extra Metadata
		// values to match those that were originally set up.
		foreach ( $mb_custom_table_fields as $mb_custom_table_name => $custom_fields ) {
			foreach ( $custom_fields as $custom_field ) {
				$extra_meta_key = $custom_field . ' ' . $this->flag . $mb_custom_table_name . ']';

				$extra_meta[ $extra_meta_key ] = rwmb_meta(
					$custom_field,
					array(
						'storage_type' => 'custom_table',
						'table'        => $mb_custom_table_name,
					),
					$the_post->ID
				);
			}
		}

		return $extra_meta;
	}

	/**
	 * Adds Meta Box fields that are not using MB Custom Table.
	 *
	 * @param array $meta_groups The existing meta groups.
	 * @param array $args        The arguments for this engine/post type.
	 *
	 * @return array The modified meta groups.
	 *
	 * @since 1.0
	 */
	public function add_meta_box_fields( $meta_groups, $args ) {
		$meta_boxes = $this->get_core_meta_boxes();

		$applicable_meta_boxes = array_filter(
			$meta_boxes,
			function( $meta_box ) use ( $args ) {
				return in_array( $args['post_type'], $meta_box->meta_box['post_types'] );
			}
		);

		foreach ( $applicable_meta_boxes as $meta_box ) {
			$custom_table_keys = $this->get_meta_box_keys( $meta_box );

			if ( ! empty( $custom_table_keys ) ) {
				$meta_groups['searchwp_meta_box_' . $meta_box->id ] = array(
					'label'    => 'Meta Box: ' . $meta_box->title,
					'metakeys' => $custom_table_keys,
				);
			}
		}

		return $meta_groups;
	}

	/**
	 * Retrieve Meta Box keys prefixed by their meta box ID.
	 *
	 * @param object $meta_box The Meta Box.
	 *
	 * @return array Meta keys prefixed by their meta box ID.
	 *
	 * @since 1.0
	 */
	public function get_meta_box_keys( $meta_box, $prefix = '', $suffix = '' ) {
		$keys = array_map( function( $field ) use ( $prefix, $suffix ) {
			return $prefix . $field . $suffix;
		}, wp_list_pluck( $meta_box->meta_box['fields'], 'id' ) );

		return $keys;
	}

	/**
	 * Adds SearchWP Custom Field Groups for each MB Custom Table custom table.
	 *
	 * @param array $meta_groups The existing meta groups.
	 * @param array $args        The arguments for this engine/post type.
	 *
	 * @return array The modified meta groups.
	 *
	 * @since 1.0
	 */
	public function add_meta_box_custom_table_groups( $meta_groups, $args ) {
		$meta_box_groups = $this->get_grouped_custom_table_meta_boxes();

		foreach ( $meta_box_groups as $custom_table_name => $meta_boxes ) {
			// Limit applicable meta boxes by post type.
			$applicable_meta_boxes = array_filter(
				$meta_boxes,
				function( $meta_box ) use ( $args ) {
					return in_array( $args['post_type'], $meta_box->meta_box['post_types'] );
				}
			);

			// Use applicable meta boxes to retrieve the meta keys we want.
			$custom_table_keys = array();
			foreach ( $applicable_meta_boxes as $meta_box ) {
				$custom_table_keys = array_merge(
					$custom_table_keys,
					$this->get_meta_box_keys( $meta_box, '', ' ' . $this->flag . $custom_table_name . ']' ) );
			}

			// Create our SearchWP Custom Field group per MB Custom Table.
			if ( ! empty( $custom_table_keys ) ) {
				$meta_groups['searchwp_meta_box_custom_table_' . $custom_table_name ] = array(
					'label'    => 'MB Custom Table: ' . $custom_table_name,
					'metakeys' => $custom_table_keys,
				);
			}
		}

		return $meta_groups;
	}

	/**
	 * Retrieve Meta Boxes that do not use MB Custom Table.
	 *
	 * @return array Meta boxes that do not use MB Custom Table.
	 *
	 * @since 1.0
	 */
	public function get_core_meta_boxes() {
		$meta_boxes = rwmb_get_registry( 'meta_box' )->get_by( array( 'object_type' => 'post' ) );

		return array_filter( $meta_boxes, function( $meta_box ) {
			return ! isset( $meta_box->meta_box['storage_type'] )
					|| 'custom_table' != $meta_box->meta_box['storage_type'];
		} );
	}

	/**
	 * Returns a filtered set of registered Meta Boxes that use MB Custom Table.
	 *
	 * @return array Meta Boxes that use MB Custom Table
	 *
	 * @since 1.0
	 */
	public function get_custom_table_meta_boxes() {
		$meta_boxes = rwmb_get_registry( 'meta_box' )->get_by( array( 'object_type' => 'post' ) );

		return array_filter( $meta_boxes, function( $meta_box ) {
			return isset( $meta_box->meta_box['storage_type'] )
					&& 'custom_table' == $meta_box->meta_box['storage_type']
					&& isset( $meta_box->meta_box['table'] );
		} );
	}

	/**
	 * Returns MB Custom Table Meta Boxes grouped by their custom database table.
	 *
	 * @return array MB Custom Table Meta Boxes grouped by database table.
	 *
	 * @since 1.0
	 */
	public function get_grouped_custom_table_meta_boxes() {
		$meta_boxes = array();

		foreach ( (array) $this->get_custom_table_meta_boxes() as $meta_box ) {
			if ( ! array_key_exists( $meta_box->meta_box['table'], $meta_boxes ) ) {
				$meta_boxes[ $meta_box->meta_box['table'] ] = array();
			}

			$meta_boxes[ $meta_box->meta_box['table'] ][] = $meta_box;
		}

		return $meta_boxes;
	}
}

new SearchWP_Meta_Box_Integration();

/**
 * Instantiate the updater.
 */
if ( ! class_exists( 'SWP_Meta_Box_Integration_Updater' ) ) {
	include_once dirname( __FILE__ ) . '/vendor/updater.php';
}

function searchwp_meta_box_integration_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_VERSION' ) ) {
		return false;
	}

	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	$searchwp_meta_box_integration_updater = new SWP_Meta_Box_Integration_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 193494,
			'version'   => SEARCHWP_EDD_VERSION,
			'license'   => $license_key,
			'item_name' => 'Meta Box Integration',
			'author'    => 'SearchWP, LLC',
			'url'       => site_url(),
		)
	);

	return $searchwp_meta_box_integration_updater;
}

add_action( 'admin_init', 'searchwp_meta_box_integration_update_check' );
