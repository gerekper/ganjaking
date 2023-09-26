<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

interface Placeholder {

	/**
	 * @param string $placeholder
	 *
	 * @return View
	 */
	public function set_placeholder( $placeholder );

}