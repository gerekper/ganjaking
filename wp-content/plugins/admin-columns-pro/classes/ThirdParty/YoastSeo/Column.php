<?php

namespace ACP\ThirdParty\YoastSeo;

use AC;

abstract class Column extends AC\Column {

	public function __construct() {
		$this->set_original( true );
		$this->set_group( 'yoast-seo' );
	}

}