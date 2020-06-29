<?php

namespace ACP\Sorting;

use ACP;
use ACP\Sorting\Type\DataType;

abstract class AbstractModel {

	/**
	 * @var DataType
	 */
	protected $data_type;

	/**
	 * @var bool
	 */
	protected $show_empty;

	/**
	 * @var Strategy\Comment|Strategy\Post|Strategy\User
	 */
	protected $strategy;

	public function __construct( DataType $data_type = null, $show_empty = null ) {
		if ( null === $data_type ) {
			$data_type = new DataType( DataType::STRING );
		}
		if ( null === $show_empty ) {
			$show_empty = acp_sorting_show_all_results();
		}

		$this->data_type = $data_type;
		$this->show_empty = (bool) $show_empty;
	}

	/**
	 * @return array
	 */
	public abstract function get_sorting_vars();

	/**
	 * @param Strategy $strategy
	 */
	public function set_strategy( Strategy $strategy ) {
		$this->strategy = $strategy;
	}

	/**
	 * @return DataType
	 */
	public function get_data_type() {
		return $this->data_type;
	}

	/**
	 * Return the default or set order from the strategy.
	 * Falls back to ASC if an invalid order is found
	 * @return string ASC|DESC
	 */
	public function get_order() {
		$order = strtoupper( $this->strategy->get_order() );

		if ( 'ASC' !== $order ) {
			$order = 'DESC';
		}

		return $order;
	}

	/**
	 * Sorts an array ascending, maintains index association and returns keys
	 *
	 * @param array $array
	 *
	 * @return array Returns the array keys of the sorted array
	 * @deprecated 5.2
	 */
	public function sort( array $array ) {
		return ( new Sorter() )->sort( $array, $this->get_order(), $this->data_type, $this->show_empty );
	}

}