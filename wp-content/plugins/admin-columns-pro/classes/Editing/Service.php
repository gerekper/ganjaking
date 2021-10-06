<?php

namespace ACP\Editing;

use AC\Request;

interface Service {

	const CONTEXT_SINGLE = 'single';
	const CONTEXT_BULK = 'bulk';

	/**
	 * @param string $context
	 *
	 * @return View|false
	 */
	public function get_view( $context );

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public function get_value( $id );

	/**
	 * @param Request $request
	 *
	 * @return bool
	 */
	public function update( Request $request );

}