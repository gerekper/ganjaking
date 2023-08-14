<?php

/**
 * SearchWP AdvancedCustomFields.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Integrations;

use SearchWP\Option;
use SearchWP\Source;

/**
 * Class AdvancedCustomFields is responsible for integrating ACF with SearchWP where applicable.
 *
 * @since 4.0
 */
class AdvancedCustomFields {

	/**
	 * All registered ACF fields.
	 *
	 * @since 4.0
	 * @var array
	 */
	public $fields = [];

	/**
	 * Stores field name for 'repeatables' e.g. Field Group, Repeater, Flexible.
	 *
	 * @since 4.0
	 * @var array
	 */
	public $repeatables = [];

	/**
	 * The Source for this integration.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $source;

	/**
	 * The post type for this integration.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $post_type;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct( Source $source ) {
		if (
			! method_exists( $source, 'get_post_type' ) // Only applies to WP_Post sources.
			|| ! function_exists( 'acf' )
			|| ! function_exists( 'acf_get_field_groups' )
			|| ! function_exists( 'acf_get_fields' )
			|| ! version_compare( acf()->settings['version'], '5.0', '>=' )
		) {
			return;
		}

		$this->post_type = $source->get_post_type();
		$this->source    = $source->get_name();

		add_action( 'searchwp\settings\page', [ $this, 'init' ] );
	}

	/**
	 * Initializer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function init() {
		$this->get_fields();

		// Repeatables
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'repeatable_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options',    [ $this, 'repeatable_meta_keys' ], 5, 2 );
		}

		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'repeatable_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'repeatable_meta_keys' ], 5, 2 );
		}

		// Non-Repeatables
		if ( ! has_filter( 'searchwp\source\attribute\options', [ $this, 'individual_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options',    [ $this, 'individual_meta_keys' ], 5, 2 );
		}

		if ( ! has_filter( 'searchwp\source\attribute\options\special', [ $this, 'individual_meta_keys' ] ) ) {
			add_filter( 'searchwp\source\attribute\options\special', [ $this, 'individual_meta_keys' ], 5, 2 );
		}

		// Prevent SearchWP from interfering with ACF's front end searching of its own fields.
		add_filter( 'searchwp\native\short_circuit', function( $short_circuit ) {
			$acf_actions = apply_filters( 'searchwp\acf\short_circuit_actions', array(
				'acf/fields/oembed/search',
				'acf/fields/post_object/query',
				'acf/fields/relationship/query',
			) );

			return $short_circuit
				? $short_circuit
				: isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $acf_actions );
		}, 5 );
	}

	/**
	 * Retrieve all registered ACF fields.
	 *
	 * @since 4.0
	 */
	public function get_fields() {
		add_action( 'pre_get_posts', [ $this, 'suppress_filters' ] );
		$field_groups = acf_get_field_groups();
		remove_action( 'pre_get_posts', [ $this, 'suppress_filters' ] );

		$fields = [];

		foreach ( $field_groups as $field_group ) {
			$fields = acf_get_fields( $field_group );

			if ( ! empty( $fields ) ) {
				$this->categorize_fields( $fields, $field_group );
			}
		}

		return $this->repeatables;
	}

	/**
	 * Recursive function to find all repeatable ACF fields keys.
	 *
	 * @since 4.0
	 * @param array $fields The fields for this field group.
	 * @param array $field_group The field group itself.
	 */
	public function categorize_fields( $fields, $field_group, $prefix = '' ) {
		foreach ( $fields as $field ) {
			if ( 'repeater' == $field['type'] || 'group' == $field['type'] ) {
				if ( empty( $field['sub_fields'] ) ) {
					continue;
				}

				$this->repeatables[] = [
					'field_group' => $field_group,
					'field'       => $this->reduce_repeatable( $field, $prefix ),
				];

				$this_prefix = ! empty( $prefix ) ? $prefix . '*' . $field['name'] . '*' : $field['name'] . '*';

				$this->categorize_fields( $field['sub_fields'], $field_group, $this_prefix );
			} elseif ( 'flexible_content' == $field['type'] ) {
				$this->repeatables[] = [
					'field_group' => $field_group,
					'field'       => $this->reduce_repeatable( $field, $prefix ),
				];

				foreach ( (array) $field['layouts'] as $layout ) {
					if ( empty( $field['sub_fields'] ) ) {
						continue;
					}

					$this_prefix = ! empty( $prefix ) ? $prefix . '*' . $field['name'] . '*' : $field['name'] . '*';

					$this->categorize_fields( $layout['sub_fields'], $field_group, $this_prefix );
				}
			} else {
				// This is an individual field.
				$this->fields[] = [
					'field_group' => $field_group,
					'field'       => $field,
				];
			}
		}
	}

	/**
	 * Reduce an ACF Field to only what we need.
	 *
	 * @since 4.0
	 * @param array $field
	 * @return array
	 */
	private function reduce_repeatable( array $field, string $prefix ) {
		return [
			'id'    => isset( $field['ID'] )    ? $field['ID']    : null,
			'key'   => isset( $field['key'] )   ? $field['key']   : null,
			'label' => isset( $field['label'] ) ? $field['label'] : null,
			'name'  => isset( $field['name'] )  ? $prefix . $field['name'] . '*' : null,
		];
	}

	/**
	 * Callback to suppress filters when retrieving ACF Field Groups.
	 *
	 * @since 4.0
	 */
	public function suppress_filters( $query ) {
		$query->set( 'suppress_filters', true );
	}

	/**
	 * Callback to return 'repeatable' ACF Fields e.g. Repeater, Flexible Content
	 *
	 * @since 4.0
	 * @param mixed $keys
	 * @param mixed $args
	 * @return mixed|array
	 */
	public function repeatable_meta_keys( $keys, $args ) {
		if (
			! apply_filters( 'searchwp\acf\show_repeatable_fields', true, $args )
			|| $args['source'] !== $this->source
			|| $args['attribute'] !== 'meta'
		) {
			return $keys;
		}

		// Add Repeatables.
		foreach ( $this->repeatables as $pair ) {
			$field  = $pair['field'];
			$group  = $pair['field_group'];
			$key    = $field['name'];
			$icon   = 'dashicons dashicons-welcome-widgets-menus'; // This is what ACF uses.

			$label  = 'ACF: ' . $field['label'];
			if ( apply_filters( 'searchwp\acf\include_field_group_in_label', false, $pair ) ) {
				$label .= ' (' . $group['title'] . ')';
			}

			$option = new Option( $key, $label, $icon );

			// If there's already a match, remove it because we want ours there.
			$keys = array_filter( $keys, function( $option ) use ( $key ) {
				return $key !== $option->get_value();
			});

			// Add custom Option for Repeatable.
			$keys[] = $option;
		}

		return $keys;
	}

	/**
	 * Callback to include individual ACF fields as Custom Field optoins.
	 *
	 * @since 4.1
	 * @param mixed $keys
	 * @param mixed $args
	 * @return mixed|array
	 */
	public function individual_meta_keys( $keys, $args ) {
		if (
			! apply_filters( 'searchwp\acf\show_individual_fields', true, $args )
			|| $args['source'] !== $this->source
			|| $args['attribute'] !== 'meta'
		) {
			return $keys;
		}

		// Add individual fields.
		foreach ( $this->fields as $pair ) {
			$field  = $pair['field'];
			$group  = $pair['field_group'];
			$key    = $field['name'];
			$icon   = 'dashicons dashicons-welcome-widgets-menus'; // This is what ACF uses.

			$label  = 'ACF: ' . $field['label'];
			if ( apply_filters( 'searchwp\acf\include_field_group_in_label', false, $pair ) ) {
				$label .= ' (' . $group['title'] . ')';
			}

			$option = new Option( $key, $label, $icon );

			// If there's already a match, remove it because we want ours there.
			$keys = array_filter( $keys, function( $option ) use ( $key ) {
				return $key !== $option->get_value();
			});

			// Add custom Option for ACF field.
			$keys[] = $option;
		}

		return $keys;
	}
}
