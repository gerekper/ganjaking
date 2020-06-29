<?php

namespace ACP\Column;

use AC;
use ACP\Export;

/**
 * @since 4.1
 */
class Actions extends AC\Column\Actions
	implements Export\Exportable {

	public function export() {
		return new Export\Model\Disabled( $this );
	}

}