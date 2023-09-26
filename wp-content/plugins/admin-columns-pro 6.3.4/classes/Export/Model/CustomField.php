<?php

namespace ACP\Export\Model;

use AC\Column;

class CustomField extends RawValue {

	public function __construct( Column\CustomField $column ) {
		parent::__construct( $column );
	}

}