<?php

/**
 * SearchWP Rule.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Rule defines the model for an individual rule for a Source. Each Rule
 * is part of a group of Rules. Each Rule group can either limit the search
 * results pool (IN) or exclude results from the pool (NOT IN).
 *
 * Examples of Rules:
 *    - In a Category
 *    - Has a Tag
 *    - Published more than 6 months ago
 *    - Has fewer than 2 comments
 *
 * All Rules within a group will be combined using OR logic. Groups of rules will
 * be combined using AND logic.
 *
 * Each Rule has its own conditions as well. The Rule should return Entry IDs that
 * match the conditions of the Rule itself, and the Rule group will handle those IDs.
 * The return can either be an array of IDs or a fully prepared SQL query that will
 * return ONLY a single column of IDs.
 *
 * e.g.
 *
 * (Rule group) Exclude entries when:
 *    (Rule) Entry ID is IN 123, 456, 789
 *    AND (Rule) Entry is in the Uncategorized Category
 * OR (Rule group) Exclude entries when:
 *    (Rule) Entry was published more than 6 months ago
 *
 * @since 4.0
 */
final class Rule {

	/**
	 * A unique name for this Rule.
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
	 * Note(s) about the Rule.
	 *
	 * @since 4.0
	 * @var   string[]
	 */
	private $notes;

	/**
	 * Tooltip for the Rule.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $tooltip;

	/**
	 * The settings for this Rule.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $settings = null;

	/**
	 * Options define the choices for this Rule, when applicable Typically.
	 * an Array but can be a callable (that returns an Array) as well.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $options = false;

	/**
	 * Conditions for each Option
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $conditions = false;

	/**
	 * Values for each Option
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $values = false;

	/**
	 * The application logic for this Rule. Can return array of Source Entry IDs
	 * or Callable function. If Callable, return either the prepared SQL to retrieve
	 * the Source Entry IDs for the rule, or an array of IDs.
	 *     - 1st parameter is the chosen option for this rule.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $application;

	/**
	 * AJAX tag to retrieve Option Values.
	 *
	 * @since 4.0
	 * @var string
	 */
	public $option_values_ajax_tag = '';

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	public function __construct( array $args ) {
		$this->name   = ! empty( $args['name'] ) ? trim( sanitize_key( (string) $args['name'] ) ) : '';

		// Rules require a name.
		if ( empty( $this->name ) ) {
			wp_die(
				__( 'Invalid name for SearchWP Rule:', 'searchwp' ) . ' <code>' . esc_html( $this->name ) . '</code>',
				__( 'SearchWP Rule Error', 'searchwp' )
			);
		}

		// The data for this rule is retrieved via callback, which must be defined.
		if ( empty( $args['application'] ) ) {
			wp_die(
				__( 'Missing application handler for SearchWP Rule:', 'searchwp' ) . ' <code>' . esc_html( $this->name ) . '</code>',
				__( 'SearchWP Rule Error', 'searchwp' )
			);
		}

		$this->label       = empty( $args['label'] )       ? ucfirst( $this->name ) : $args['label'];
		$this->notes       = empty( $args['notes'] )       ? []    : $args['notes'];
		$this->tooltip     = empty( $args['tooltip'] )     ? ''    : $args['tooltip'];
		$this->options     = empty( $args['options'] )     ? false : $args['options'];
		$this->conditions  = empty( $args['conditions'] )  ? false : $args['conditions'];
		$this->values      = empty( $args['values'] )      ? false : $args['values'];
		$this->application = empty( $args['application'] ) ? false : $args['application'];
	}

	/**
	 * Adds a Rule implementation to these settings.
	 *
	 * @since 4.0
	 * @param int $group_index The index of the Rule group.
	 * @param string $type The logic for the Rule group.
	 * @param array $settings Settings of the Rule.
	 * @return void
	 */
	public function implement( int $group_index, string $type, array $settings ) {
		if ( ! is_array( $this->settings ) ) {
			$this->settings = [];
		}

		// We're retaining our groups by index as defined in the settings.
		if ( ! array_key_exists( $group_index, $this->settings ) ) {
			$this->settings[ $group_index ] = [];
		}

		// Ensure that our type is set and available.
		if ( ! array_key_exists( $type, $this->settings[ $group_index ] ) ) {
			$this->settings[ $group_index ][ $type ] = [];
		}

		// Validate the Rule condition.
		if ( ! in_array( $settings['condition'], $this->get_conditions(), true ) ) {
			return;
		}

		// Validate the Rule value(s) if applicable.
		// MAYBE: This could be very resource intensive, and may not apply all the time. Omitted for now.

		// Remove redundancy.
		unset( $settings['rule'] );

		// Store this implementation.
		$this->settings[ $group_index ][ $type ][] = $settings;
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
	public function get_label() {
		return $this->label;
	}

	/**
	 * Getter for options.
	 *
	 * @since 4.0
	 * @return Options[]|string[] Array of Options or property values.
	 */
	public function get_options( $property = '' ) {
		if ( ! empty( $property ) && 'value' !== $property ) {
			$property = 'label';
		}

		$options = is_callable( $this->options ) ? call_user_func( $this->options ) : $this->options;

		if ( empty( $options ) ) {
			return $options;
		} else {
			$options = (array) $options;
		}

		// Options must be Options.
		$options = array_filter( $options, function( $option ) {
			return $option instanceof Option;
		} );

		// Limit to property if applicable.
		return $property ? array_map( function( $option ) use ( $property ) {
			return $option->{'get_' . $property}();
		}, $options ) : $options;
	}

	/**
	 * Getter for conditions.
	 *
	 * @since 4.0
	 * @return mixed
	 */
	public function get_conditions() {
		return $this->conditions;
	}

	/**
	 * Getter for Rule Option values.
	 *
	 * @since 4.0
	 * @param string      $option  The Option (name) to consider.
	 * @param bool|string $search  When populated, will find Values like $search.
	 * @param array       $include  Existing Values to use as a limiter when retrieving Values.
	 * @return Options[]
	 */
	public function get_values( $option = '', $search = false, array $include = [] ) {
		if ( ! empty( $option ) ) {
			$values = call_user_func( $this->values, $option, $search, $include );
		} elseif ( false === $this->get_options() ) {
			if ( is_callable( $this->values ) ) {
				$values = call_user_func( $this->values, $search, $include );
			} else {
				$values = $this->values;

				if ( ! empty( $include ) ) {
					$values = array_filter( $values, function( $value ) use ( $include ) {
						return in_array( $value->get_value(), $include );
					} );
				}
			}
		} else {
			$values = array_map( function( $option ) use ( $search, $include ) {
				if ( is_callable( $this->values ) ) {
					$values = call_user_func( $this->values, $option, $search, $include );
				} else {
					$values = $this->values;

					if ( ! empty( $include ) ) {
						$values = array_filter( $values, function( $value ) use ( $include ) {
							return in_array( $value->get_value(), $include );
						} );
					}
				}

				return $values;
			}, $this->get_options( 'value' ) );
		}

		// Values must be instances of Options.
		if ( is_array( $values ) ) {
			$values = array_filter( $values, function( $value ) {
				return $value instanceof Option;
			} );
		}

		return $values;
	}

	/**
	 * Getter for application.
	 *
	 * @since 4.0
	 * @return mixed
	 */
	public function get_application( array $settings ) {
		return is_callable( $this->application )
			? call_user_func( $this->application, $settings )
			: $this->application;
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
	 * Getter for notes.
	 *
	 * @since 4.0
	 * @return string[]
	 */
	public function get_notes() {
		return $this->notes;
	}

	/**
	 * Getter for tooltip.
	 *
	 * @since 4.0
	 * @return string
	 */
	public function get_tooltip() {
		return $this->tooltip;
	}
}
