<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Helper;
use ACP\Filtering\Model;
use ACP\Filtering\Settings;

class Date extends Model {

	/**
	 * @var string
	 */
	private $date_field = 'post_date';

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	public function register_settings() {
		$this->column->add_setting( new Settings\Date( $this->column ) );
	}

	public function get_filtering_vars( $vars ) {

		switch ( $this->get_filter_format() ) {

			case 'monthly' :
				$timestamp = strtotime( $this->get_filter_value() . '01' );

				$vars['date_query'][] = [
					'year'  => date( 'Y', $timestamp ),
					'month' => date( 'm', $timestamp ),
				];

				break;
			case 'yearly' :
				$vars['date_query'][] = [
					'year' => $this->get_filter_value(),
				];

				break;
			case 'future_past' :
				$date = date( 'Y-m-d' );

				if ( 'future' == $this->get_filter_value() ) {
					$vars['date_query'][] = [
						'inclusive' => true,
						'after'     => $date,
					];
				} else {
					$vars['date_query'][] = [
						'inclusive' => true,
						'before'    => $date,
					];
				}

				break;
			case 'range' :
				$value = $this->get_filter_value();

				if ( $value ) {
					$vars['date_query'][] = [
						'inclusive' => true,
						'before'    => $value['max'],
						'after'     => $value['min'],
					];
				}

				break;
			case 'daily' :
			default :
				$timestamp = strtotime( $this->get_filter_value() );

				$vars['date_query'][] = [
					'year'  => date( 'Y', $timestamp ),
					'month' => date( 'm', $timestamp ),
					'day'   => date( 'd', $timestamp ),
				];

				break;
		}

		$vars['date_query']['column'] = $this->get_date_field();

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

		$helper = new Helper();

		$options = $helper->get_date_options_relative( $format );

		if ( ! $options ) {
			$options = $helper->get_date_options( $this->get_dates( $this->get_date_field() ), $format, 'Y-m-d H:i:s' );
		}

		return [
			'order'   => false,
			'options' => $options,
		];
	}

	/**
	 * @param string $field
	 *
	 * @return array
	 */
	private function get_dates( $field ) {
		global $wpdb;

		$field = sanitize_key( $field );

		$query = "
			SELECT $field AS `date`
			FROM $wpdb->posts
			WHERE post_type = %s
			ORDER BY `date`
		";

		$sql = $wpdb->prepare( $query, $this->column->get_post_type() );

		return $wpdb->get_col( $sql );
	}

	public function get_filter_format() {
		return $this->column->get_setting( 'filter' )->get_value( 'filter_format' );
	}

	/**
	 * @return string
	 */
	public function get_date_field() {
		return $this->date_field;
	}

	/**
	 * @param string $date_field
	 */
	public function set_date_field( $date_field ) {
		$this->date_field = $date_field;
	}

}