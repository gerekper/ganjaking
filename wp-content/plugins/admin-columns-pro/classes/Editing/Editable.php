<?php

namespace ACP\Editing;

interface Editable {

	/**
	 * @return Service|false
	 */
	public function editing();

}