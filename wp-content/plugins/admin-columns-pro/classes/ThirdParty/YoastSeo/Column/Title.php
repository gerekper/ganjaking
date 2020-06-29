<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\ThirdParty\YoastSeo;

class Title extends YoastSeo\Column
	implements Editing\Editable, Filtering\Filterable, Export\Exportable {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-title' );
	}

	public function editing() {
		return new YoastSeo\Editing\Title( $this );
	}

	public function filtering() {
		return new YoastSeo\Filtering\Title( $this );
	}

	public function export() {
		return new YoastSeo\Export\Title( $this );
	}

}