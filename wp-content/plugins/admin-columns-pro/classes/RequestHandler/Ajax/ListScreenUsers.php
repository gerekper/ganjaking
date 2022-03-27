<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Nonce;
use AC\Request;
use ACP\Helper\Select;
use ACP\RequestAjaxHandler;

class ListScreenUsers implements RequestAjaxHandler {

	public function handle() {
		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$entities = new Select\Entities\User( [
			'search' => $request->get( 'search' ),
			'paged'  => $request->get( 'page', 1 ),
			'number' => 10,
		] );

		$options = new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);

		$has_more = ! $options->is_last_page();

		$response = new AC\Helper\Select\Response( $options, $has_more );

		wp_send_json_success( $response() );
	}
}