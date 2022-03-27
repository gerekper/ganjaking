<?php

namespace ACP\Filtering;

use AC;
use AC\Request;
use AC\Table\TableFormView;
use ACP;
use ACP\Bookmark\SegmentRepository;
use ACP\Filtering\Bookmark\PreferredFilter;
use ACP\Filtering\Markup\Dropdown;

abstract class Model {

	/**
	 * @var AC\Column
	 */
	protected $column;

	/**
	 * @var string
	 */
	private $data_type = 'string';

	/**
	 * @var Strategy
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

	public function __construct( AC\Column $column ) {
		$this->column = $column;
	}

	/**
	 * @return AC\Column
	 */
	public function get_column() {
		return $this->column;
	}

	/**
	 * @param string $data_type
	 *
	 * @return $this
	 */
	public function set_data_type( $data_type ) {
		$data_type = strtolower( $data_type );

		if ( in_array( $data_type, [ 'string', 'numeric', 'date' ] ) ) {
			$this->data_type = $data_type;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_data_type() {
		return $this->data_type;
	}

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
	 * @param string $request_key
	 *
	 * @return array
	 */
	private function get_preferred_filters( $request_key ) {
		return ( new PreferredFilter( new SegmentRepository() ) )->findFilters( $this->column->get_list_screen(), $request_key );
	}

	/**
	 * Get a request var for all columns
	 *
	 * @param string $suffix
	 *
	 * @return string|false
	 */
	public function get_request_var( $suffix = '' ) {
		$request = new Request();

		$request_key = Dropdown::OPTION_FILTER;

		if ( $suffix ) {
			$request_key .= '-' . ltrim( $suffix, '-' );
		}

		$values = $request->filter( $request_key, [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		// Ignore when switching to another segment or when the filter form is submitted.
		if ( ! $values && ! $request->filter( 'ac-segment' ) && null === $request->get( TableFormView::PARAM_ACTION ) ) {
			$values = $this->get_preferred_filters( $request_key );
		}

		if ( ! isset( $values[ $this->column->get_name() ] ) ) {
			return false;
		}

		$value = $values[ $this->column->get_name() ];

		if ( ! is_scalar( $value ) || mb_strlen( $value ) < 1 ) {
			return false;
		}

		return $value;
	}

}