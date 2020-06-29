<?php

namespace ACP\Filtering\Model;

use AC;
use ACP\Filtering\Model;

/**
 * @property AC\Column\CustomField $column
 */
class CustomField extends Model\Meta {

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );
	}

}