<?php

namespace ACA\MLA\Column;

use AC\Column;
use ACA\MLA\Export;
use ACA\MLA\Service\ColumnGroup;
use ACP;
use MLACore;

class Features extends Column implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_original( true )
		     ->set_type( 'featured' )
		     ->set_group( ColumnGroup::NAME );
	}

	public function export() {
		if ( ! MLACore::$process_featured_in ) {
			return false;
		}

		return new Export\Model\FeaturedIn();
	}

}