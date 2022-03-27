<?php

namespace ACP;

use AC\Request;

interface RequestHandler {

	/**
	 * @param Request $request
	 *
	 * @return void
	 */
	public function handle( Request $request );

}