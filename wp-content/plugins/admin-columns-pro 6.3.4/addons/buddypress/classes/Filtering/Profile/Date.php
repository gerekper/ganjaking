<?php

namespace ACA\BP\Filtering\Profile;

use ACA\BP\Filtering;
use ACP;
use DateTime;
use Exception;

class Date extends Filtering\Profile {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	public function register_settings() {
		$this->column->add_setting( new ACP\Filtering\Settings\Date( $this->column ) );
	}

	public function get_filtering_data() {
		$format = $this->get_filter_format();

		$helper = new ACP\Filtering\Helper();

		$options = $helper->get_date_options_relative( $format );

		if ( ! $format && ! $options ) {
			$options = $helper->get_date_options( $this->get_xprofile_values(), 'j F Y', 'Y-m-d H:i:s', 'Ymd' );
		}

		if ( ! $options ) {
			$options = $helper->get_date_options( $this->get_xprofile_values(), $format, 'Y-m-d H:i:s' );
		}

		return [
			'empty_option' => true,
			'order'        => false,
			'options'      => $options,
		];
	}

	public function filter_by_callback( $query ) {
		global $wpdb;

		switch ( $this->get_filter_format() ) {

			case 'monthly' :
				$date = $this->get_date_format( $this->get_filter_value() . '01', 'Y-m-' );
				$where = $wpdb->prepare( "value LIKE '%s'", $date . '%' );

				$this->add_sql_where( $query, $where );

				break;
			case 'yearly' :
				$date = $this->get_date_format( $this->get_filter_value() . '0101', 'Y-' );
				$where = $wpdb->prepare( "value LIKE '%s'", $date . '%' );

				$this->add_sql_where( $query, $where );

				break;
			case 'future_past' :
				$date = date( 'Y-m-d' );

				if ( 'future' === $this->get_filter_value() ) {
					$where = $wpdb->prepare( "value >= '%s'", $date );
				} else {
					$where = $wpdb->prepare( "value <= '%s'", $date );
				}

				$this->add_sql_where( $query, $where );

				break;
			case 'range':
				$this->filter_by_ranged( $query, 'char' );

				break;
			default:
				$date = $this->get_date_format( $this->get_filter_value(), 'Y-m-d' );

				$where = $wpdb->prepare( "value LIKE '%s'", $date . '%' );

				$this->add_sql_where( $query, $where );
		}
	}

	/**
	 * @param string $date   Input
	 * @param string $format Output
	 *
	 * @return false|string
	 */
	private function get_date_format( $date, $format ) {
		try {
			$_date = new DateTime( $date );
		} catch ( Exception $e ) {
			return false;
		}

		return $_date->format( $format );
	}

	private function get_filter_format() {
		return $this->column->get_setting( 'filter' )->get_value( 'filter_format' );
	}

}