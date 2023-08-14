<?php

/**
 * SearchWP Engine.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Utils;
use SearchWP\Source;

/**
 * Class Engine is responsible for modeling an engine from a settings array.
 *
 * @since 4.0
 */
class Engine implements \JsonSerializable {

	/**
	 * Engine name.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $name;

	/**
	 * Engine label.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $label;

	/**
	 * Engine Sources.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $sources = [];

	/**
	 * Engine Settings.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $settings = [
		'stemming'    => true,
		'adminengine' => false,
	];

	/**
	 * Error collection.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $errors = [];

	/**
	 * Engine constructor. Builds Engine model from saved Engine settings.
	 *
	 * @since 4.0
	 * @param string $name The engine name.
	 */
	function __construct( string $name = 'default', array $settings = [] ) {
		$name = sanitize_key( trim( $name ) );

		if ( empty( $name ) || ( is_string( $name ) && strlen( $name ) > 191 ) ) {
			$this->errors[] = new \WP_Error( 'name', __( 'Invalid SearchWP engine name', 'searchwp' ), $name );
		} else {
			$this->name = sanitize_key( $name );

			// If this engine name exists, we can load the settings.
			if ( empty( $settings ) && $existing_settings = Settings::get_engine_settings( $this->name ) ) {
				$settings = $existing_settings;
			}

			// If there were no settings, we are going to assume a default set of Sources.
			if ( empty( $settings ) ) {
				// We're going to normalize anyway, but we needed to know the incoming settings were empty.
				$settings = $this->normalize_settings( $settings );

				$index = \SearchWP::$index;

				$settings['sources'] = array_filter( array_map( function( $source ) {
					// A Source is only default if it represents WP_Post.
					if ( 0 !== strpos( $source->get_name(), 'post' . SEARCHWP_SEPARATOR ) ) {
						return false;
					}

					return json_decode( json_encode( $source ), true );
				}, $index->get_default_sources( true ) ) );

				$settings = Utils::normalize_engine_config( $settings );
			}

			$this->label = ! empty( $settings['label'] ) ? $settings['label'] : ucfirst( $this->name );
			$settings    = $this->normalize_settings( $settings );

			$this->apply_settings( $settings );
		}
	}

	/**
	 * Applies saved settings array to properties.
	 *
	 * @since 4.0
	 * @param array $dirty_settings Settings as array.
	 * @return void
	 */
	private function apply_settings( array $settings ) {
		$errors  = [];
		$models  = \SearchWP::$index->get_sources();
		$sources = array_filter( array_map( function( $source_name, $source_settings ) use ( $models, $errors ) {
			if (
				! array_key_exists( $source_name, $models )
				|| ( apply_filters( 'searchwp\source\check_db', false ) && ! $models[ $source_name ]->is_valid() )
			) {
				$errors[] = $source_name;

				return false;
			}

			$settings = $this->normalize_source_settings( $source_settings );
			$source   = clone $models[ $source_name ];

			$this->apply_attributes_settings( $settings['attributes'], $source );
			$this->apply_rules_settings( $settings['rules'], $source );
			$this->apply_options_settings( $settings['options'], $source );

			return $source;
		}, array_keys( $settings['sources'] ), array_values( $settings['sources'] ) ) );

		foreach ( $sources as $source ) {
			$source->init();
			$this->sources[ $source->get_name() ] = $source;
		}

		if ( ! empty( $errors ) ) {
			$this->errors[] = new \WP_Error(
				'source',
				__( 'Invalid SearchWP engine Source(s)', 'searchwp' ),
				$errors
			);
		}

		if (
			! isset( $settings['settings']['stemming'] )
			|| empty( $settings['settings']['stemming'] )
		) {
			$this->settings['stemming'] = false;
		}

		if (
			isset( $settings['settings']['adminengine'] )
			&& ! empty( $settings['settings']['adminengine'] )
			&& 'false' !== $settings['settings']['adminengine']
		) {
			$this->settings['adminengine'] = true;
		}
	}

	/**
	 * Applies saved Options settings to this Source.
	 *
	 * @since 4.0
	 * @param array $settings Settings to apply.
	 * @return void
	 */
	private function apply_options_settings( array $settings, Source $source ) {
		// Options are quite restricted. At this time there's only Weight Transfer handling.
		foreach ( $settings as $option => $config ) {
			$source->set_option_config( $option, $config );
		}
	}

	/**
	 * Applies saved Attribute settings to this Source's Attributes.
	 *
	 * @since 4.0
	 * @param array $settings Settings to apply.
	 * @return void
	 */
	private function apply_attributes_settings( array $settings, Source $source ) {
		$settings = array_filter( $settings, function( $setting ) use ( $source ) {
			return array_key_exists( $setting, $source->get_attributes() );
		}, ARRAY_FILTER_USE_KEY );

		foreach( $settings as $attribute_name => $attribute_settings ) {
			$source->get_attributes()[ $attribute_name ]->set_settings( $attribute_settings );
		}
	}

	/**
	 * Applies saved Rules settings to this Source.
	 *
	 * @since 4.0
	 * @param array $settings Settings to apply.
	 * @return void
	 */
	private function apply_rules_settings( array $settings, Source $source ) {
		// Rules are stored in groups, as each group controls the logic of the Rule combination.
		$rule_group_index = 0;
		foreach ( $settings as $rule_group ) {
			$type  = isset( $rule_group['type'] ) && 'NOT IN' === $rule_group['type'] ? 'NOT IN' : 'IN';
			$rules = isset( $rule_group['rules'] ) && is_array( $rule_group['rules'] ) ? $rule_group['rules'] : [];

			foreach ( $rules as $rule_settings ) {
				$source->get_rules()[ $rule_settings['rule'] ]->implement( $rule_group_index, $type, $rule_settings );
			}

			$rule_group_index++;
		}
	}

	/**
	 * Removes a Source from the Engine Sources.
	 *
	 * @since 4.0
	 * @param string $source_name Source to remove.
	 * @return bool Whether the Source was removed.
	 */
	public function remove_source( string $source_name ) {
		foreach ( $this->sources as $index => $source ) {
			if ( $source_name === $source->get_name() ) {
				unset( $this->sources[ $index ] );
				return true;
			}
		}

		return false;
	}

	/**
	 * Getter for Engine settings.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Retrieves Source Attribute (with Options) settings.
	 *
	 * @since 4.0
	 * @param Source      $source    Source to consider.
	 * @param null|string $attribute Attribute to consider.
	 * @return array Attribute settings for the Source.
	 */
	public function get_source_attribute_options_settings( Source $source, string $attribute_name = '' ) {

		$settings = array_values( array_filter( array_map( function( $source ) use ( $attribute_name ) {
			if ( $attribute_name && $source->get_attribute( $attribute_name ) ) {
				return $source->get_attribute( $attribute_name )->get_settings();
			} else {
				return array_map( function( $attribute ) {
					return $attribute->get_settings();
				}, $source->get_attributes() );
			}
		}, array_filter( $this->sources, function( $this_source ) use ( $source ) {
			return $source->get_name() === $this_source->get_name();
		} ) ) ) );

		// $settings is either a one dimensional array, or an array of arrays.
		if ( empty( $settings ) ) {
			return $settings;
		}

		// If this Attribute has Options, we have a multidimensional array that needs to be merged.
		if ( isset( $settings[0] ) && is_array( $settings[0] ) ) {
			$settings = call_user_func_array( 'array_merge', $settings );
		}

		return $settings;
	}

	/**
	 * Ensure that expected array keys exist when retrieving Source settings.
	 *
	 * @since 4.0
	 * @param array $settings The settings array to normalize.
	 * @return array The normalized settings array.
	 */
	public function normalize_source_settings( array $settings ) {
		return [
			'options'    => empty( $settings['options'] )    ? [] : $settings['options'],
			'rules'      => empty( $settings['rules'] )      ? [] : $settings['rules'],
			'attributes' => empty( $settings['attributes'] ) ? [] : $settings['attributes'],
		];
	}

	/**
	 * Ensure that expected array keys exist when retrieving settings.
	 *
	 * @since 4.0
	 * @param array $settings The settings array to normalize.
	 * @return array The normalized settings array.
	 */
	public function normalize_settings( array $settings ) {
		return [
			'label'    => empty( $settings['label'] )    ? '' : sanitize_text_field( $settings['label'] ),
			'settings' => empty( $settings['settings'] ) ? [] : $settings['settings'],
			'sources'  => empty( $settings['sources'] )  ? [] : $settings['sources'],
		];
	}

	/**
	 * Getter for Engine Sources.
	 *
	 * @since 4.0
	 * @return array Sources.
	 */
	public function get_sources() {
		return $this->sources;
	}

	/**
	 * Getter for a single Source of this Engine.
	 *
	 * @since 4.0
	 * @param string $source_name Name of the Source
	 * @return mixed|false Source (or false when Source is invalid)
	 */
	public function get_source( string $source_name ) {
		foreach ( $this->sources as $index => $source ) {
			if ( $source_name === $source->get_name() ) {
				return $source;
			}
		}

		return false;
	}

	/**
	 * Getter for label.
	 *
	 * @since 4.0
	 * @return string The label.
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Getter for name.
	 *
	 * @since 4.0
	 * @return mixed The engine name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Getter for errors.
	 *
	 * @since 4.2.6
	 *
	 * @return array Error collection.
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Provides the model to use when representing this Engine as JSON.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function jsonSerialize(): array {
		$sources = array_map( function ( $source ) {
			// We want to trigger $source->jsonSerialize().
			return json_decode( json_encode( $source ) );
		}, $this->sources );

		$model = [
			'name'     => $this->name,
			'label'    => $this->label,
			'settings' => $this->settings,
			'sources'  => $sources,
		];

		return $model;
	}
}
