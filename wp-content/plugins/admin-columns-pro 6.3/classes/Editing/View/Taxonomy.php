<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Taxonomy extends View {

	use AjaxTrait,
		MultipleTrait,
		TagsTrait;

	public function __construct() {
		parent::__construct( 'taxonomy' );

		$this->set_ajax_populate( true );
	}

}