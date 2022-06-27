<?php

namespace ACP\Table\HideElement;

use ACP\Table\HideElement;

class ActionsBar implements HideElement {

	public function hide() {
		add_action( 'ac/admin_head', function () {
			?>
			<style>
				[class="alignleft actions"] {
					display: none !important;
				}
			</style>
			<?php
		} );
	}

}