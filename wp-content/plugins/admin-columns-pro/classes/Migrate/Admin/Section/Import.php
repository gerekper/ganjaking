<?php

namespace ACP\Migrate\Admin\Section;

use AC\Renderable;
use AC\View;

class Import implements Renderable {

	public function render() {
		$view = new View();
		$view->set_template( 'admin/section-import' );

		return $view->render();
	}

}