<?php

namespace ACP\RequestHandler\Ajax;

use AC;
use AC\Registrable;
use ACP\Helper\Select;

class ListScreenUsers implements Registrable {

	public function register() {
		$this->get_ajax_handler()->register();
	}

	private function get_ajax_handler() {
		$handler = new AC\Ajax\Handler();
		$handler->set_action( 'acp_layout_get_users' )
		        ->set_callback( [ $this, 'ajax_get_users' ] );

		return $handler;
	}

	public function ajax_get_users() {
		$this->get_ajax_handler()->verify_request();

		$paged = filter_input( INPUT_POST, 'page' );

		$entities = new Select\Entities\User( [
			'search' => filter_input( INPUT_POST, 'search' ),
			'paged'  => $paged ?: 1,
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