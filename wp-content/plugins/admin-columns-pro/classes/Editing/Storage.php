<?php

namespace ACP\Editing;

interface Storage {

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get( $id );

	/**
	 * @param int   $id
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function update( $id, $value );

}