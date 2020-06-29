<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Export;

/**
 * @since 4.0
 */
class Name extends AC\Column
	implements Editing\Editable, Export\Exportable {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'name' );
	}

	public function editing() {
		return new Editing\Model\Taxonomy\Name( $this );
	}

	public function export() {
		return new Export\Model\Term\Name( $this );
	}

}