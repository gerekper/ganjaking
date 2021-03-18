<?php

/**
 * Class CT_Ultimate_GDPR_Model_View
 */
class CT_Ultimate_GDPR_Model_Front_View {

	/**
	 * @var self $instance
	 */
	private static $instance;

	/**
	 * @var array $data view data
	 */
	private $data;

	/**
	 * @return CT_Ultimate_GDPR_Model_Front_View
	 */
	public static function instance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * CT_Ultimate_GDPR_Model_Services constructor.
	 */
	private function __construct() {
		$this->data = array();
	}

	/**
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function __get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : false;
	}

	/**
	 * @param $key
	 * @param $variable
	 *
	 * @return $this
	 */
	public function set( $key, $variable ) {
		$this->data[ $key ] = $variable;

		return $this;
	}

	/**
	 * @param $key
	 * @param $variable
	 *
	 * @return $this
	 */
	public function add( $key, $variable ) {

		if ( ! isset( $this->data[ $key ] ) || ! is_array( $this->data[ $key ] ) ) {
			$this->data[ $key ] = array();
		}

		$this->data[ $key ][] = $variable;

		return $this;

	}

	/**
	 * @param $key
	 * @param bool $default
	 *
	 * @return bool|mixed
	 */
	public function get( $key, $default = false ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return (array) $this->data;
	}

}