<?php

namespace ACA\WC\Sorting\Comment;

use ACP;

class Rating extends ACP\Sorting\AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		$id = uniqid();

		$vars = [
			'meta_query' => [
				$id => [
					'key'     => $this->meta_key,
					'type'    => $this->data_type->get_value(),
					'value'   => '',
					'compare' => '!=',
				],
			],
			'orderby'    => $id,
		];

		return $vars;
	}

}