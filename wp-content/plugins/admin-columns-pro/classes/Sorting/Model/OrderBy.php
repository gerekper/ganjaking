<?php

namespace ACP\Sorting\Model;

use ACP\Sorting\AbstractModel;

class OrderBy extends AbstractModel {

	/**
	 * @var string
	 */
	protected $orderby;

	public function __construct( $orderby ) {
		parent::__construct();

		$this->orderby = (string) $orderby;
	}

	/**
	 * @return array
	 */
	public function get_sorting_vars() {
		return [
			'orderby' => $this->orderby,
		];
	}

}