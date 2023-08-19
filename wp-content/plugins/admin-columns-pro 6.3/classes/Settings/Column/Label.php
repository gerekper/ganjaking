<?php

namespace ACP\Settings\Column;

use AC;

class Label extends AC\Settings\Column\Label {

	public function create_view() {
		$view = parent::create_view();

		$view->set_template( 'settings/setting-label-icons' );

		return $view;
	}

}