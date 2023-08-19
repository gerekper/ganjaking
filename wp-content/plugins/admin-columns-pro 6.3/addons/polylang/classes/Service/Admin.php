<?php

namespace ACA\Polylang\Service;

use AC\Registerable;
use ACA\Polylang\Column;

class Admin implements Registerable {

	public function register(): void
    {
		add_action( 'ac/admin_scripts', [ $this, 'admin_style' ] );
	}

	public function admin_style() {
		add_action( 'admin_head', function () {
			?>
			<style>
				.ac-column.ac-<?= Column\Language::TYPE ?> .ac-column-setting--label,
				.ac-column.ac-<?= Column\Language::TYPE ?> .ac-column-setting--width,
				.ac-column.ac-<?= Column\Language::TYPE ?> .ac-column-setting--export {
					display: none !important;
				}
			</style>
			<?php
		} );
	}
}