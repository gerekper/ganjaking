<?php

namespace ACP\ThirdParty\Polylang;

use AC;
use AC\Registrable;
use ACP\ListScreen;
use ACP\ThirdParty\Polylang;

class Addon implements Registrable {

	public function register() {
		( new ColumnGroup() )->register();

		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
		add_action( 'ac/table/list_screen', [ $this, 'register_column_replacement' ] );
		add_action( 'ac/admin_scripts', [ $this, 'admin_style' ] );
	}

	public function register_column_replacement( AC\ListScreen $list_screen ) {
		if ( ! $this->is_active() ) {
			return;
		}

		$replacement = new ColumnReplacement( $list_screen );
		$replacement->register();
	}

	public function admin_style() {
		add_action( 'admin_head', function () {
			?>
			<style>
				.ac-column.ac-<?= Polylang\Column\Language::TYPE ?> .ac-column-setting--label,
				.ac-column.ac-<?= Polylang\Column\Language::TYPE ?> .ac-column-setting--width,
				.ac-column.ac-<?= Polylang\Column\Language::TYPE ?> .ac-column-setting--export {
					display: none !important;
				}
			</style>
			<?php
		} );
	}

	public function add_columns( AC\ListScreen $list_screen ) {
		if ( $this->is_active() && ( $list_screen instanceof ListScreen\Post || $list_screen instanceof ListScreen\Taxonomy || $list_screen instanceof ListScreen\Media ) ) {
			$list_screen->register_column_type( new Polylang\Column\Language() );
		}
	}

	/**
	 * @return bool
	 */
	private function is_active() {
		return defined( 'POLYLANG_VERSION' );
	}

}