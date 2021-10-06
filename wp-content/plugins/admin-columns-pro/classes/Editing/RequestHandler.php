<?php

namespace ACP\Editing;

use AC\Request;

interface RequestHandler {

	public function handle( Request $request );

}