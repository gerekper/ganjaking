<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Service\ColumnGroup;
use ACP;

class AltText extends Column implements ACP\Editing\Editable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'alt_text' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function editing() {
		return new ACP\Editing\Service\Media\AlternateText();
	}

}