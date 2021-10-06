<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class AjaxSelect extends View {

	use AjaxTrait,
		TagsTrait,
		MethodTrait,
		MultipleTrait;

	public function __construct() {
		parent::__construct( 'select2_dropdown' );

		$this->set_ajax_populate( true );
	}

	public function set_tags( $enable ) {
		$this->set( 'tags', (bool) $enable );

		return $this;
	}

}