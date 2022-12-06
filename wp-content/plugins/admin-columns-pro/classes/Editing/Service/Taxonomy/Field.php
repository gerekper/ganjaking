<?php

namespace ACP\Editing\Service\Taxonomy;

use ACP\Editing;
use ACP\Editing\Service\BasicStorage;

abstract class Field extends BasicStorage {

	public function __construct( $taxonomy, $field ) {
		parent::__construct( new Editing\Storage\Taxonomy\Field( $taxonomy, $field ) );
	}

}