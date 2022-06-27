<?php

namespace ACP\Table\HideElement;

use ACP\Table\HideElement;

class Filters implements HideElement {

	public function hide() {
		add_action( 'ac/admin_head', function () {
			?>
			<style>
				[class="alignleft actions"] > select {
					display: none !important;
				}
			</style>
			<?php
		} );
	}

}