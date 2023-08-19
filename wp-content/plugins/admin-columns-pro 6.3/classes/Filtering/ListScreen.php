<?php

namespace ACP\Filtering;

interface ListScreen {

	/**
	 * @param Model $model
	 *
	 * @return Strategy
	 */
	public function filtering( $model );

}