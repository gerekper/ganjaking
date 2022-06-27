<?php

namespace ACP\Table\HideElement;

use ACP\Table\HideElement;

class SubMenu implements HideElement {

	public function hide() {
		add_action( 'ac/admin_head', [ $this, 'render' ] );
	}

	public function render() {
		?>
		<style>
			<?= sprintf( '%s { display: none; }', '.wrap ul.subsubsub' ); ?>
		</style>
		<?php
	}

}