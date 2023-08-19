<?php

namespace ACP\Editing\Model\Taxonomy;

use AC\Column;
use ACP\Editing;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage;

/**
 * @deprecated 5.6
 */
class Description extends Basic {

	public function __construct( Column $column ) {
		parent::__construct(
			new Editing\View\TextArea(),
			( new Storage\Taxonomy\Field( $column->get_taxonomy(), 'description' ) )
		);
	}

}