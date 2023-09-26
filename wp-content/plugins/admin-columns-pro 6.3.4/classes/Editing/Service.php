<?php

namespace ACP\Editing;

interface Service {

	const CONTEXT_SINGLE = 'single';
	const CONTEXT_BULK = 'bulk';

	public function get_view( string $context ): ?View;

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get_value( int $id );

	/**
	 * @param int   $id
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function update( int $id, $data ): void;

}