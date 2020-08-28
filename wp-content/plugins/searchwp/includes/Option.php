<?php

/**
 * SearchWP Option.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Option is responsible for storing a value with an associated label.
 *
 * @since 4.0
 */
class Option implements \JsonSerializable {

	/**
	 * The value of this Option.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $value;

	/**
	 * The label of this Option.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $label;

	/**
	 * The icon of this Option.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $icon;

	/**
	 * Option constructor.
	 *
	 * @since 4.0
	 * @param mixed  $value The value to store.
	 * @param string $label The label to use.
	 */
	function __construct( $value, $label = '', $icon = '' ) {
		// If no label was provided, generate one from the submitted value.
		if ( empty( $label ) || ! is_string( $label ) ) {
			$label = substr( (string) $value, 0, 32 );
		}

		$this->label = sanitize_text_field( $label );
		$this->value = $value;
		$this->icon  = $icon;
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
	 * Getter for value.
	 *
	 * @since 4.0
	 * @return mixed The value.
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Getter for icon.
	 *
	 * @since 4.0
	 * @return string The icon.
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Provides the model to use when representing this Source as JSON.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'label' => $this->label,
			'value' => $this->value,
			'icon'  => $this->get_icon(),
		];
	}
}
