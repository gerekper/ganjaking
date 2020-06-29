<?php

namespace ACP\Export\Model;

use AC\Column;

/**
 * @property Column\CustomField $column
 * @since 4.1
 */
class CustomField extends RawValue {

	public function __construct( Column\CustomField $column ) {
		parent::__construct( $column );
	}

}