<?php

/**
 * Class FUE_Email_Type
 */
class FUE_Email_Type {

	/**
	 * @var string The type's unique ID
	 */
	public $id;

	/**
	 * The priority the type is loaded
	 * @var int
	 */
	public $priority = 10;

	/**
	 * @var string Label of the email type, in plural form
	 */
	public $label;

	/**
	 * @var string Label of the email type
	 */
	public $singular_label;

	/**
	 * @var array Array of available triggers for this type (e.g. signup, first_purchase)
	 */
	public $triggers;

	/**
	 * @var array Array of available conditions
	 */
	public $conditions;

	/**
	 * @var array The different durations available to this type
	 */
	public $durations;

	/**
	 * @var string Description that is displayed on the email form
	 */
	public $short_description;

	/**
	 * @var string Description that is displayed on the emails list page
	 */
	public $long_description;

	/**
	 * Constructor. Set the ID and apply the properties
	 * @param $id
	 * @param array $props
	 */
	public function __construct( $id, $props = array() ) {
		$this->id = $id;

		foreach ( $props as $key => $value ) {
			$this->$key = $value;
		}
	}

	public function __set( $name, $value ) {
		$this->$name = $value;
	}

	public function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Get the printable string of a trigger
	 *
	 * @param string $trigger
	 * @return string
	 */
	public function get_trigger_name( $trigger ) {
		if ( isset( $this->triggers[ $trigger ] ) ) {
			return $this->triggers[ $trigger ];
		}

		return $trigger;
	}
}
