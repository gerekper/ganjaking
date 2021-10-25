<?php
namespace ACP\Migrate\Admin;

use AC\View;
use ACP\Admin\Renderable;

class ImportSection implements Renderable {

	public function render() {
		echo ( new View() )->set_template( 'admin/section-import' );
	}

}