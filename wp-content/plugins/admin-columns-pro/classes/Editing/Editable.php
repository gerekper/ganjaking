<?php

namespace ACP\Editing;

interface Editable {

	/**
	 * Return the editing model for this column
	 * @return Model
	 */
	public function editing();

}