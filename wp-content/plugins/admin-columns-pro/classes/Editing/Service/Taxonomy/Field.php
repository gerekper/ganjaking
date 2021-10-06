<?php

namespace ACP\Editing\Service\Taxonomy;

use ACP\Editing;
use ACP\Editing\Service;

class Field extends Service\Basic {

	/**
	 * @param Editing\View $view
	 * @param string       $taxonomy
	 * @param string       $field
	 */
	public function __construct( Editing\View $view, $taxonomy, $field ) {
		parent::__construct( $view, new Editing\Storage\Taxonomy\Field( $taxonomy, $field ) );
	}

}