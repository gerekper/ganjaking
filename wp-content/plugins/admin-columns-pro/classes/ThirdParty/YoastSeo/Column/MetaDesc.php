<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use ACP\Editing;
use ACP\Export;
use ACP\ThirdParty\YoastSeo;

class MetaDesc extends YoastSeo\Column
	implements Editing\Editable, Export\Exportable {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-metadesc' );
	}

	public function editing() {
		return new YoastSeo\Editing\MetaDesc( $this );
	}

	public function export() {
		return new YoastSeo\Export\MetaDesc( $this );
	}
}