<?php

namespace ACP\Table;

use AC\ListScreen;
use AC\Registrable;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideSubMenu implements Registrable {

	/**
	 * @var HideOnScreen\SubMenu
	 */
	private $hide_sub_menu;

	public function __construct( HideOnScreen\SubMenu $hide_on_screen ) {
		$this->hide_sub_menu = $hide_on_screen;
	}

	public function register() {
		add_action( 'ac/admin_head', [ $this, 'admin_head' ] );
	}

	public function admin_head( ListScreen $list_screen ) {
		if ( ! $this->hide_sub_menu->is_hidden( $list_screen ) ) {
			return;
		}

		$selector = $this->get_element_selector( $list_screen );

		if ( ! $selector ) {
			return;
		}
		?>
		<style>
			<?= sprintf( '%s { display: none; }', $selector ); ?>
		</style>
		<?php
	}

	private function get_element_selector( ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ListScreen\Comment :
			case $list_screen instanceof ListScreen\User :
			case $list_screen instanceof ListScreen\Media :
			case $list_screen instanceof ListScreen\Post :
			default :
				return '.wrap ul.subsubsub';
		}
	}

}