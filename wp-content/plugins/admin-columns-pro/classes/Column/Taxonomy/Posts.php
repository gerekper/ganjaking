<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Export;

/**
 * @since 4.1
 */
class Posts extends AC\Column
	implements Export\Exportable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'posts' );
	}

	public function export() {
		return new Export\Model\Term\Posts( $this );
	}

}