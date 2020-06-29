<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Export;

/**
 * @since 4.0
 */
class Description extends AC\Column
	implements Editing\Editable, Export\Exportable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'description' );
	}

	public function editing() {
		return new Editing\Model\Taxonomy\Description( $this );
	}

	public function export() {
		return new Export\Model\Term\Description( $this );
	}

}