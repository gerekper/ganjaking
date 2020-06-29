<?php

namespace ACP\Table;

use AC\ListScreen;
use AC\Registrable;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideBulkActions implements Registrable {

	public function register() {
		add_action( 'ac/admin_head', [ $this, 'admin_head' ] );
	}

	public function admin_head( ListScreen $list_screen ) {
		if ( ! ( new HideOnScreen\BulkActions() )->is_hidden( $list_screen ) ) {
			return;
		}

		$selector = $this->get_bulk_actions_selector();

		if ( ! $selector ) {
			return;
		}
		?>
		<style>
			<?= sprintf( '%s { display: none; }', $selector ); ?>
		</style>
		<?php
	}

	private function get_bulk_actions_selector() {
		return '.tablenav div.actions.bulkactions';
	}

}