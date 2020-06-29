<?php

namespace ACP\Table;

use AC\ListScreen;
use AC\Registrable;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideSearch implements Registrable {

	public function register() {
		add_action( 'ac/admin_head', [ $this, 'admin_head' ] );
	}

	public function admin_head( ListScreen $list_screen ) {
		if ( ! ( new HideOnScreen\Search() )->is_hidden( $list_screen ) ) {
			return;
		}

		$selector = $this->get_search_selector( $list_screen );

		if ( ! $selector ) {
			return;
		}
		?>
		<style>
			<?= sprintf( '%s { display: none; }', $selector ); ?>
		</style>
		<?php
	}

	private function get_search_selector( ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ListScreen\Media :
				return '.wrap form#posts-filter div.search-form';
			case $list_screen instanceof ListScreen\Post :
				return '.wrap form#posts-filter p.search-box';
			default :
				return 'p.search-box';
		}
	}

}