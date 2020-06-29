<?php

namespace ACP\Filtering;

use ACP;

abstract class Model extends ACP\Model {

	/**
	 * @var Strategy\Comment | Strategy\Post | Strategy\User;
	 */
	protected $strategy;

	/**
	 * @var bool
	 */
	private $ranged;

	/**
	 * Get the query vars to filter on
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	abstract public function get_filtering_vars( $vars );

	/**
	 * Return the data required to generate the filtering gui on a list screen
	 * @return array
	 */
	abstract public function get_filtering_data();

	/**
	 * @param Strategy $strategy
	 */
	public function set_strategy( Strategy $strategy ) {
		$this->strategy = $strategy;
	}

	/**
	 * @return Strategy
	 */
	public function get_strategy() {
		return $this->strategy;
	}

	/**
	 * @param bool $is_ranged
	 */
	public function set_ranged( $is_ranged ) {
		$this->ranged = (bool) $is_ranged;
	}

	/**
	 * @return bool
	 */
	public function is_ranged() {
		if ( null === $this->ranged ) {
			$setting = $this->column->get_setting( 'filter' );
			$is_ranged = $setting instanceof Settings\Ranged && $setting->is_ranged();

			$this->set_ranged( $is_ranged );
		}

		return $this->ranged;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		$setting = $this->column->get_setting( 'filter' );

		if ( ! $setting instanceof Settings ) {
			return false;
		}

		return $setting->is_active();
	}

	/**
	 * Register column settings
	 */
	public function register_settings() {
		$this->column->add_setting( new Settings( $this->column ) );
	}

	/**
	 * @return string|array
	 */
	public function get_filter_value() {
		if ( $this->is_ranged() ) {
			$value = [
				'min' => $this->get_request_var( 'min' ),
				'max' => $this->get_request_var( 'max' ),
			];

			return false !== $value['min'] || false !== $value['max'] ? $value : false;
		}

		return $this->get_request_var();
	}

	/**
	 * Validate a value: can it be used to filter results?
	 *
	 * @param string|integer $value
	 * @param string         $filters Options: all, serialize, length and empty. Use a | to use a selection of filters e.g. length|empty
	 *
	 * @return bool
	 */
	protected function validate_value( $value, $filters = 'all' ) {
		$available = [ 'serialize', 'length', 'empty' ];

		switch ( $filters ) {
			case 'all':
				$applied = $available;

				break;
			default:
				$applied = array_intersect( $available, explode( '|', $filters ) );

				if ( empty( $applied ) ) {
					$applied = $available;
				}
		}

		foreach ( $applied as $filter ) {
			switch ( $filter ) {
				case 'serialize':
					if ( is_serialized( $value ) ) {
						return false;
					}

					break;
				case 'length':
					if ( strlen( $value ) > 1024 ) {
						return false;
					}

					break;
				case 'empty':
					if ( empty( $value ) && 0 !== $value ) {
						return false;
					}

					break;
			}
		}

		return true;
	}

	/**
	 * @param string $label
	 *
	 * @return array
	 */
	protected function get_empty_labels( $label = '' ) {
		if ( ! $label ) {
			$label = strtolower( $this->column->get_label() );
		}

		return [
			sprintf( __( "Without %s", 'codepress-admin-columns' ), $label ),
			sprintf( __( "Has %s", 'codepress-admin-columns' ), $label ),
		];
	}

	/**
	 * Get a request var for all columns
	 *
	 * @param string $suffix
	 *
	 * @return string|false
	 */
	public function get_request_var( $suffix = '' ) {
		$key = 'acp_filter';

		if ( $suffix ) {
			$key .= '-' . ltrim( $suffix, '-' );
		}

		$values = filter_input( INPUT_GET, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! isset( $values[ $this->column->get_name() ] ) ) {
			return false;
		}

		$value = $values[ $this->column->get_name() ];

		if ( ! is_scalar( $value ) || mb_strlen( $value ) < 1 ) {
			return false;
		}

		return $value;
	}

	/**
	 * @param $format
	 *
	 * @return array|false
	 * @deprecated 4.2
	 */
	protected function get_date_options_relative( $format ) {
		_deprecated_function( __METHOD__, '4.2', 'ACP\Filtering\Helper::get_date_options_relative()' );

		return ( new Helper() )->get_date_options_relative( $format );
	}

	/**
	 * @param array  $dates
	 * @param        $display
	 * @param string $format
	 * @param null   $key
	 *
	 * @return array
	 * @deprecated 4.2
	 */
	protected function get_date_options( array $dates, $display, $format = 'Y-m-d', $key = null ) {
		_deprecated_function( __METHOD__, '4.2', 'ACP\Filtering\Helper::get_date_options()' );

		return ( new Helper() )->get_date_options( $dates, $display, $format, $key );
	}

}