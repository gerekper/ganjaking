<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model;

/**
 * @property AC\Column\CustomField $column
 */
class Checkmark extends Model\CustomField {

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

}