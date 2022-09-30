<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Activity
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Activity {

	/**
	 * activity name
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $activity;

	/**
	 * status
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $status;

	/**
	 * timestamp
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $timestamp;

	/**
	 * note
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $note;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $activity, $status, $timestamp, $note ) {
		$this->activity  = $activity;
		$this->status    = $status;
		$this->timestamp = $timestamp;
		$this->note      = $note;
	}


	/**
	 * return the formatted date
	 *
	 * @param bool        $gmt
	 * @param bool|string $format
	 *
	 * @return string
	 */
	public function get_formatted_date( $gmt = true, $format = false ) {
		$timestamp = $this->timestamp;
		if ( $gmt ) {
			$offset    = get_option( 'gmt_offset' );
			$timestamp += $offset * HOUR_IN_SECONDS;
		}

		if ( $format === false ) {
			$format = wc_date_format() . ' ' . wc_time_format();
		}

		return date_i18n( $format, $timestamp );
	}

	/**
	 * return the status text
	 *
	 * @return string
	 */
	public function get_status_text() {
		return strtr( $this->status, yith_wcmbs_get_membership_statuses() );
	}

	/**
	 * return the status text
	 *
	 * @return string
	 */
	public function get_i18n_note() {
		return call_user_func( '__', $this->note, 'yith-woocommerce-membership' );
	}
}