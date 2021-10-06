<?php

namespace ACP\Editing\Model\Taxonomy;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class TaxonomyParent extends Service\Taxonomy\TaxonomyParent {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_taxonomy() );
	}

}