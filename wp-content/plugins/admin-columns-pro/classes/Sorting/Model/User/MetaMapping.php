<?php

namespace ACP\Sorting\Model\User;

use ACP\Sorting\Model\SqlOrderByFactory;

class MetaMapping extends Meta {

	/**
	 * @var array
	 */
	protected $fields;

	public function __construct( string $meta_key, array $fields ) {
		parent::__construct( $meta_key );

		$this->fields = $fields;
	}

	protected function get_order_by(): string {
		return SqlOrderByFactory::create_with_field(
			"acsort_usermeta.meta_value",
			$this->fields,
			$this->get_order(),
			$this->data_type
		);
	}

}