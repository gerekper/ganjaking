<?php

namespace ACP\Sorting\Type;

use ACP\Sorting\Request;
use InvalidArgumentException;

class SortType {

	/**
	 * @var string
	 */
	private $order_by;

	/**
	 * @var string
	 */
	private $order;

	public function __construct( $order_by, $order ) {
		if ( 'asc' !== $order ) {
			$order = 'desc';
		}

		$this->order_by = $order_by;
		$this->order = $order;

		$this->validate();
	}

	private function validate() {
		if ( ! is_string( $this->order_by ) ) {
			throw new InvalidArgumentException( 'Expected a string for order by.' );
		}
	}

	public function get_order_by() {
		return $this->order_by;
	}

	public function get_order() {
		return $this->order;
	}

	/**
	 * @param SortType $sort_type
	 *
	 * @return bool
	 */
	public function equals( $sort_type ) {
		return $sort_type->get_order() === $this->order && $sort_type->get_order_by() === $this->order_by;
	}

	/**
	 * @param Request\Sort $request
	 *
	 * @return SortType
	 */
	public static function create_by_request( Request\Sort $request ) {
		return new self(
			(string) $request->get_order_by(),
			$request->get_order()
		);
	}

}