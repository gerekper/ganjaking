<?php

namespace ACP\ThirdParty\YoastSeo\Column;

use ACP\Export;
use ACP\ThirdParty\YoastSeo;

class Linked extends YoastSeo\Column
	implements Export\Exportable {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-linked' );
	}

	public function export() {
		return new Export\Model\Disabled( $this );
	}

}