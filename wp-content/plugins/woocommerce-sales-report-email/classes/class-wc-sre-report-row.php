<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

interface WC_SRE_IReport_Row {

	/**
	 * Prepare the object, all data should be prepared in this method.
	 * @return mixed
	 */
	public function prepare();

}

abstract class WC_SRE_Report_Row implements WC_SRE_IReport_Row {

	/**
	 * @var WC_SRE_Date_Range
	 */
	private $date_range;

	private $id;
	private $label;
	private $value;

	/**
	 * Constructor of Report Row, calls the required method prepare() of object.
	 *
	 * @param $date_range
	 * @param $id
	 * @param $label
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $date_range, $id, $label ) {

		// Set the date range
		$this->date_range = $date_range;

		// Set id and label
		$this->id    = $id;
		$this->label = $label;

		// Prepare the object
		$this->prepare();
	}

	/**
	 * Get the Date Range object
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return WC_SRE_Date_Range
	 */
	public function get_date_range() {
		return $this->date_range;
	}

	/**
	 * Set the Date Range object
	 *
	 * @param WC_SRE_Date_Range $date_range
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_date_range( $date_range ) {
		$this->date_range = $date_range;
	}

	/**
	 * Set the row identifier
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the row identifier
	 *
	 * @param String $id
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Get the label
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Set the label
	 *
	 * @param String $label
	 */
	public function set_label( $label ) {
		$this->label = $label;
	}

	/**
	 * Get the value
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Set the value
	 *
	 * @param String $value
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_value( $value ) {
		$this->value = $value;
	}
}