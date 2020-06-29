<?php

namespace ACP\Sorting;

interface ListScreen {

	/**
	 * @param AbstractModel $model
	 *
	 * @return Strategy
	 */
	public function sorting( $model );

}