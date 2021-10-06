<?php

namespace ACP\Editing\Service\Taxonomy;

use ACP\Editing\View\Text;

class Slug extends Field {

	public function __construct( $taxonomy ) {
		parent::__construct( new Text(), $taxonomy, 'slug' );
	}

}