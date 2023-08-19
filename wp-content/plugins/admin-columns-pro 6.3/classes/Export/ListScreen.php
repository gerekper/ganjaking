<?php

namespace ACP\Export;

use ACP;

interface ListScreen {

	/**
	 * @return ACP\Export\Strategy
	 */
	public function export();

}