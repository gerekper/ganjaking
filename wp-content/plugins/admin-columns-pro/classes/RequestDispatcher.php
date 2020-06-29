<?php

namespace ACP;

use ACP\API\Request;
use ACP\API\Response;

interface RequestDispatcher {

	/**
	 * @param Request $request
	 * @param array   $args
	 *
	 * @return Response
	 */
	public function dispatch( Request $request, array $args = [] );

}