<?php

namespace ACP\Editing\RequestHandler;

use AC;
use AC\Request;
use AC\Response;
use ACP;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\RequestHandler;

class DeleteUserSelectValues implements RequestHandler {

	public function handle( Request $request ) {
		$response = new Response\Json();
		$search = $request->get('searchterm', '');

		$options = new PaginatedOptions\Users( [ 'number' => 200 ] );
		$select = new AC\Helper\Select\Response( $options->create( $search, 1 ), false );

		$response
			->set_parameters( $select() )
			->success();
	}

}