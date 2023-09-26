<?php

namespace ACP\Table\HideElement;

use ACP\Table\HideElement;

class FilterPostFormats implements HideElement {

	public function hide() {
		add_filter( 'disable_formats_dropdown', '__return_true' );
	}

}