<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

interface MaxLength {

	/**
	 * @param int $max_length
	 *
	 * @return View
	 */
	public function set_max_length( $max_length );

}