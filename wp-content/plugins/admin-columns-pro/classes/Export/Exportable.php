<?php

namespace ACP\Export;

use ACP;

interface Exportable {

	/**
	 * @return ACP\Export\Model
	 */
	public function export();

}