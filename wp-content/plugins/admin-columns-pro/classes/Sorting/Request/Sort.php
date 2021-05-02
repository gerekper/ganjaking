<?php

namespace ACP\Sorting\Request;

class Sort {

	const PARAM_ORDERBY = 'orderby';
	const PARAM_ORDER = 'order';

	/**
	 * @var string|null
	 */
	private $order_by;

	/**
	 * @var string|null
	 */
	private $order;

	public function __construct( $order_by, $order ) {
		$this->order_by = $order_by;
		$this->order = $order;
	}

	public static function create_from_globals() {
		return new self(
			isset( $_GET[ self::PARAM_ORDERBY ] ) ? (string) $_GET[ self::PARAM_ORDERBY ] : null,
			isset( $_GET[ self::PARAM_ORDER ] ) ? strtolower( $_GET[ self::PARAM_ORDER ] ) : null
		);
	}

	public function get_order_by() {
		return $this->order_by;
	}

	public function get_order() {
		return $this->order;
	}

}