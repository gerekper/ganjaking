<?php

namespace ACP\Export;

use ACP;

interface Exportable {

	/**
	 * @return ACP\Export\Service|false
	 */
	public function export();

}