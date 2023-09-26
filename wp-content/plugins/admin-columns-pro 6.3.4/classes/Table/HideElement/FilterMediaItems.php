<?php

namespace ACP\Table\HideElement;

use ACP\Table\HideElement;

class FilterMediaItems implements HideElement {

	public function hide() {
		add_action( 'ac/admin_head', function () {
			?>
			<style>
				select#attachment-filter {
					display: none !important;
				}
			</style>
			<?php
		} );
	}

}