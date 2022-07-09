<?php

namespace ACP\Editing\RequestHandler;

use AC\Request;
use AC\Response;
use ACP\Editing\Preference;
use ACP\Editing\RequestHandler;

class EditState implements RequestHandler {

	/**
	 * @var Preference\EditState
	 */
	private $edit_state;

	public function __construct( Preference\EditState $edit_state ) {
		$this->edit_state = $edit_state;
	}

	public function handle( Request $request ) {
		$response = new Response\Json();

		$list_screen_key = $request->filter( 'list_screen', '', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! $list_screen_key ) {
			$response->error();
		}

		$value = $request->get( 'value' )
			? 1
			: 0;

		$result = $this->edit_state->set( $list_screen_key, $value );

		$result
			? $response->success()
			: $response->error();
	}

}