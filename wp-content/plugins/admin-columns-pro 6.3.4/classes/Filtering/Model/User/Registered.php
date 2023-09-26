<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Helper;
use ACP\Filtering\Model;
use ACP\Filtering\Settings;
use DateTime;

class Registered extends Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	public function register_settings() {
		$this->column->add_setting( new Settings\DatePast( $this->column ) );
	}

	public function filter_by_user_registered( $query ) {
		global $wpdb;

		switch ( $this->get_filter_format() ) {
			case 'monthly' :
				$timestamp = strtotime( $this->get_filter_value() . '01' );

				$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_registered LIKE %s", $wpdb->esc_like( date( 'Y-m-', $timestamp ) ) . '%' );
				break;
			case 'yearly' :
				$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_registered LIKE %s", $wpdb->esc_like( $this->get_filter_value() ) . '%' );
				break;
			case 'range' :
				$value = $this->get_filter_value();

				if ( $value['min'] ) {
					$query->query_where .= ' ' . $wpdb->prepare( "AND DATE({$wpdb->users}.user_registered) >= %s", $value['min'] );
				}

				if ( $value['max'] ) {
					$max = new DateTime( $value['max'] );
					$max->modify( '+1day' );

					$query->query_where .= ' ' . $wpdb->prepare( "AND DATE({$wpdb->users}.user_registered) < %s", $max->format( 'Y-m-d' ) );
				}
				break;
			case 'daily' :
			default :
				$timestamp = strtotime( $this->get_filter_value() );

				$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_registered LIKE %s", $wpdb->esc_like( date( 'Y-m-d', $timestamp ) ) . '%' );
				break;
		}

		return $query;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'pre_user_query', [ $this, 'filter_by_user_registered' ] );

		return $vars;
	}

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$format = $this->get_filter_format();

		if ( ! $format ) {
			$format = 'daily';
		}

		return [
			'order'   => false,
			'options' => ( new Helper() )->get_date_options( $this->strategy->get_values_by_db_field( 'user_registered' ), $format, 'Y-m-d H:i:s' ),
		];
	}

	private function get_filter_format() {
		$setting = $this->column->get_setting( 'filter' );

		if ( ! $setting instanceof Settings\Ranged ) {
			return false;
		}

		return $setting->get_filter_format();
	}

}