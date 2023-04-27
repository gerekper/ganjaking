<?php
declare( strict_types=1 );

namespace ACA\BeaverBuilder\ListScreenFactory;

use ACA\BeaverBuilder\ListScreenFactory;

class SavedRows extends ListScreenFactory {

	protected function get_label(): string {
		return __( 'Saved Rows', 'fl-builder' );
	}

	protected function get_page(): string {
		return 'row';
	}

}