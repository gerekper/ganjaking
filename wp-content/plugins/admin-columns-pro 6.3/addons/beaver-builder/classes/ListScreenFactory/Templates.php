<?php
declare( strict_types=1 );

namespace ACA\BeaverBuilder\ListScreenFactory;

use ACA\BeaverBuilder\ListScreenFactory;

class Templates extends ListScreenFactory {

	protected function get_label(): string {
		return __( 'Templates', 'fl-builder' );
	}

	protected function get_page(): string {
		return 'layout';
	}

}