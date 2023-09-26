<?php

namespace ACP\Editing\Model\Taxonomy;

use AC\Column;
use ACP\Editing;

/**
 * @deprecated 5.6
 */
class Slug extends Editing\Service\Taxonomy\Slug {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_taxonomy() );
	}

}