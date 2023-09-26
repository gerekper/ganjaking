<?php

namespace ACP\Editing\BulkDelete;

interface ListScreen {

	/**
	 * @return Deletable
	 */
	public function deletable();

}