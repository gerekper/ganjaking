<?php

namespace ACP\Filtering\Model;

use ACP\Filtering\Helper;
use ACP\Filtering\Settings;
use DateTime;
use Exception;

class MetaDate extends Meta {

	/**
	 * @var string
	 */
	private $date_format = 'Y-m-d';

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	/**
	 * @param string $date_format
	 */
	protected function set_date_format( $date_format ) {
		$this->date_format = $date_format;
	}

	/**
	 * @return string
	 */
	protected function get_date_format() {
		return $this->date_format;
	}

	/**
	 * Adds Meta Query vars for dates
	 *
	 * @param array $vars Query args
	 *
	 * @return array
	 * @since 4.0
	 */
	public function get_filtering_vars( $vars ) {
		$value = $this->get_filter_value();

		// empty or not empty
		if ( in_array( $value, [ 'cpac_empty', 'cpac_nonempty' ] ) ) {
			return $this->get_filtering_vars_empty_nonempty( $vars );
		}

		// set filter format
		$filter_format = $this->get_filter_format();

		if ( $this->is_ranged() ) {
			$filter_format = 'default_ranged';
		}

		$args = [];

		if ( 'U' === $this->get_date_format() ) {
			$args['type'] = 'numeric';
		}

		$suffix = '0101 00:00:00';
		$format = $this->get_date_format();

		switch ( $filter_format ) {
			case 'default_ranged':
				foreach ( [ 'min', 'max' ] as $key ) {
					if ( $value[ $key ] ) {
						$args[ $key ] = date( $format, strtotime( $value[ $key ] ) );
					}
				}

				break;
			case 'future_past':
				$date = $this->get_date_time_object();
				$key = 'future' !== $value ? 'max' : 'min';

				if ( $date ) {
					$args[ $key ] = $date->format( $format );
				}

				break;
			case 'yearly' :
				$value .= $suffix;
				$date = $this->get_date_time_object( $value );

				if ( $date ) {
					$args['min'] = $date->format( $format );
					$args['max'] = $date->modify( '+1 year' )->modify( '-1 day' )->format( $format );
				}

				break;
			case 'monthly' :
				$value .= substr( $suffix, 2 );
				$date = $this->get_date_time_object( $value );

				if ( $date ) {
					$args['min'] = $date->format( $format );
					$args['max'] = $date->modify( '+1 month' )->modify( '-1 day' )->format( $format );
				}

				break;
			case 'daily' :
				$value .= substr( $suffix, 4 );
				$date = $this->get_date_time_object( $value );

				if ( $date ) {
					$args['min'] = $date->format( $format );
					$args['max'] = $date->modify( '+1 day' )->modify( '-1 second' )->format( $format );
				}

				break;
			default:
				$this->get_filtering_vars_empty_nonempty( $vars );
		}

		return $this->get_filtering_vars_ranged( $vars, $args );
	}

	public function get_filtering_data() {
		$format = $this->get_filter_format();
		$helper = new Helper();

		$options = $helper->get_date_options_relative( $format );

		if ( ! $options ) {
			$options = $helper->get_date_options( $this->get_meta_values(), $format, $this->get_date_format() );
		}

		return [
			'empty_option' => true,
			'order'        => false,
			'options'      => $options,
		];
	}

	private function get_filter_format() {
		$format = $this->column->get_setting( 'filter' )->get_value( 'filter_format' );

		if ( ! $format ) {
			$format = 'daily';
		}

		return $format;
	}

	/**
	 * @param string $date
	 *
	 * @return DateTime|false
	 */
	private function get_date_time_object( $date = null ) {
		try {
			return new DateTime( $date );
		} catch ( Exception $e ) {
			return false;
		}
	}

	public function register_settings() {
		$this->column->add_setting( new Settings\Date( $this->column ) );
	}

}