<?php

/**
 * Class FUE_Sending_Email_Variables
 *
 * Handles the registration of email variables and the substition
 * of variables with the actual values
 */
class FUE_Sending_Email_Variables {

	/**
	 * @var FUE_Sending_Email_Variables
	 */
	private static $instance = null;

	/**
	 * @var array Storage for all registered variables along with their replacements
	 */
	private $vars = array();

	/**
	 * class constructor
	 *
	 * Setup the variables that are common to all email types
	 *
	 */
	public function __construct() {
		$this->init();

		self::$instance = $this;
	}

	/**
	 * Set the initial variables
	 */
	public function init() {
		$vars = array(
			'store_name'    => get_bloginfo('name')
		);

		foreach ( $vars as $var => $value ) {
			$this->register( $var, $value );
		}
	}

	/**
	 * Get the current stored variables
	 * @return array
	 */
	public function get_variables() {
		return $this->vars;
	}

	/**
	 * Get an instance of this class
	 *
	 * @return FUE_Sending_Email_Variables
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )
			new FUE_Sending_Email_Variables();

		return self::$instance;
	}

	/**
	 * Add a variable or an array of variables to the container
	 *
	 * @param mixed     $variable
	 * @param mixed     $value
	 */
	public function register( $variable, $value = '' ) {

		if ( is_array( $variable ) ) {
			foreach ( $variable as $key => $val ) {
				$this->register( $key, $val );
			}
		} elseif ( empty( $this->vars[ $variable ] ) || ! empty( $value ) ) {
			$this->vars[ $variable ] = $value;
		}

	}

	/**
	 * Apply the replacements to the supplied text
	 *
	 * @param string $text
	 * @return string $text with substitutions done
	 */
	public function apply_replacements( $text ) {

		foreach ( $this->vars as $placeholder => $replacement ) {

			if ( $replacement instanceof Closure ) {
				// advanced search and replace
				$text = $replacement( $text, $placeholder );
			} else {
				// simple search and replace
				$text = str_replace( '{'. $placeholder .'}', $replacement, $text );
			}

		}

		return $text;
	}

	/**
	 * Reset to the initial state
	 * @return void
	 */
	public function reset() {
		$this->vars = array();
		$this->init();
	}

}
