<?php

/**
 * SearchWP Attribute.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Utils;
use SearchWP\Option;

/**
 * Class Attribute defines individual attributes of a Source.
 *
 * @since 4.0
 */
final class Attribute {

	/**
	 * A unique (per Source) name for this Attribute.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $name;

	/**
	 * A (more) readable version of the name.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $label;

	/**
	 * Notes for this Attribute when displayed in the UI.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $notes;

	/**
	 * Tooltip for this Attribute when displayed in the UI.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $tooltip;

	/**
	 * If this Attribute is repeatable, the options define the choices for this Attribute.
	 * Typically an Array of Options but can be a Callable (that returns an Array of Options) as well.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $options = false;

	/**
	 * Whether custom (user-defined) Options can be added.
	 *
	 * @since 4.0
	 * @var   boolean
	 */
	public $allow_custom = false;

	/**
	 * Attribute settings.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $settings = null;

	/**
	 * The value for this Attribute. Can be a Number, String, or Callable function.
	 * If Callable:
	 *     - 1st parameter is the entry ID as per the ID column from the Source.
	 *     - 2nd parameter is the option value as per the 'options' property of this Attribute.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $data;

	/**
	 * Whether Phrase logic should consider this Attribute. Can be a string that represents
	 * the column name of the Source's db_table to be used. Can also be an array of arrays with:
	 *     `table`  Table name.
	 *     `column` Column to consider for source matches.
	 *     `id`     Column containing Entry ID.
	 *
	 * @since 4.0
	 * @var bool
	 */
	private $phrases = false;

	/**
	 * Whether this Attribute should have a default weight (i.e. is automatically added to the Source).
	 *
	 * @since 4.0
	 * @var bool|Int
	 */
	private $default = false;

	/**
	 * AJAX tag to retrieve Options.
	 *
	 * @since 4.0
	 * @var string
	 */
	public $options_ajax_tag = '';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	public function __construct( array $args ) {
		if ( empty( trim( (string) $args['name'] ) ) ) {
			wp_die(
				__( 'Missing data property for SearchWP Attribute:', 'searchwp' ) . ' <code>' . esc_html( $this->name ) . '</code>',
				__( 'SearchWP Attribute Error', 'searchwp' )
			);
		}

		// The data for this attribute is retrieved via callback, which must be defined.
		if ( empty( $args['data'] ) ) {
			wp_die(
				__( 'Missing data property for SearchWP Attribute:', 'searchwp' ) . ' <code>' . esc_html( $this->name ) . '</code>',
				__( 'SearchWP Attribute Error', 'searchwp' )
			);
		}

		$this->name         = trim( sanitize_key( (string) $args['name'] ) );
		$this->data         = $args['data'];
		$this->label        = empty( $args['label'] )        ? ucfirst( $this->name ) : $args['label'];
		$this->notes        = empty( $args['notes'] )        ? [] : $args['notes'];
		$this->tooltip      = empty( $args['tooltip'] )      ? false : $args['tooltip'];
		$this->options      = empty( $args['options'] )      ? false : $args['options'];
		$this->allow_custom = empty( $args['allow_custom'] ) ? false : true;
		$this->phrases      = empty( $args['phrases'] )      ? false : $args['phrases'];
		$this->default      = empty( $args['default'] )      ? false : (int) $args['default'];

		if ( strlen( $this->name ) > 80 ) {
			do_action( 'searchwp\debug\log', 'Name too long (max 80 chars) : ' . $this->name, 'attribute' );
			return;
		}
	}

	/**
	 * Getter for name.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Getter for label.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_label( $source = null ) {
		$label = $this->label;

		if ( $source instanceof Source ) {
			$label = (string) apply_filters(
				'searchwp\source\attribute\label',
				$label, [
					'source'    => $source->get_name(),
					'attribute' => $this->get_name(),
				] );
		}

		return $label;
	}

	/**
	 * Getter for notes.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get_notes() {
		return array_map( function( $note ) {
			return sanitize_text_field( (string) $note );
		}, (array) $this->notes );
	}

	/**
	 * Getter for tooltip.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_tooltip() {
		return sanitize_text_field( (string) $this->tooltip );
	}

	/**
	 * Phrases support details.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public function get_phrases() {
		return $this->phrases;
	}

	/**
	 * Returns whether options are statically defined.
	 *
	 * @since 4.0
	 * @return bool
	 */
	public function options_static() {
		return ! is_callable( $this->options );
	}

	/**
	 * Getter for Attribute options.
	 *
	 * @since 4.0
	 * @param bool|string $search   Search string to consider when finding options.
	 * @param array       $include  Option values to use as a limiter when retrieving Options.
	 * @return Options[]|mixed[] Array of Options or property values.
	 */
	public function get_options( $search = false, array $include = [] ) {
		$options = is_callable( $this->options ) ? call_user_func( $this->options, $search, $include ) : $this->options;

		if ( empty( $options ) ) {
			return $options;
		} else {
			$options = (array) $options;

			if ( ! empty( $include ) ) {
				$options = array_filter( $options, function( $option ) use ( $include ) {
					return in_array( $option->get_value(), $include );
				} );
			}
		}

		// Options must be Options.
		return array_filter( $options, function( $option ) {
			return $option instanceof Option;
		} );
	}

	/**
	 * Getter for settings.
	 *
	 * @since 4.0
	 * @return mixed
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Getter for default.
	 *
	 * @since 4.0
	 * @return bool|int
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Setter for settings.
	 *
	 * @param mixed $settings Settings to set.
	 * @return void
	 */
	public function set_settings( $settings ) {
		// Omit any invalid options.
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			// Validate Options by using the existing settings as the include parameter.
			$options = array_map( function( $option ) {
				return $option->get_value();
			}, $this->get_options( false, array_keys( $settings ) ) );

			// Validate Option values.
			// TODO: If a Taxonomy is registered outside `init` we get
			//       Warning: array_merge() expects at least 1 parameter, 0 given
			//       for this array_merge call.
			$settings = call_user_func_array( 'array_merge',
				array_filter( array_map( function( $key, $value ) use ( $options ) {
					if ( ! in_array( $key, $options ) ) {
						return false;
					}

					// The value here is the chosen weight.
					return [ $key => absint( $value ) ];
				}, array_keys( $settings ), array_values( $settings ) ) )
			);
		} else if ( is_numeric( $settings ) ) {
			// The value here is the chosen weight.
			$settings = absint( $settings );
		}

		$this->settings = $settings;
	}

	/**
	 * Retrieves the data for this Attribute.
	 *
	 * @since 4.0
	 * @param string|int $id     The Entry ID.
	 * @param string     $option The chosen option.
	 * @param bool       $raw    Whether to return the raw data, default is to tokenize.
	 * @return mixed
	 */
	public function get_data( $id = 0, $option = '', $raw = false ) {
		if ( ! is_callable( $this->data ) ) {
			$data = $this->data;
		} else {
			if ( false === $this->options ) {
				$data = call_user_func( $this->data, $id );
			} else {
				$data = empty( $option ) ? false : call_user_func( $this->data, $id, $option );
			}
		}

		if ( ! $raw ) {
			$data = Utils::tokenize( $data );
		}

		return $data;
	}
}
