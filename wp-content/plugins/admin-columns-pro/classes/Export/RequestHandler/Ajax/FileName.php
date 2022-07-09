<?php

namespace ACP\Export\RequestHandler\Ajax;

use AC\ListScreenRepository;
use AC\Nonce;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\RequestAjaxHandler;

class FileName implements RequestAjaxHandler {

	/**
	 * @var ListScreenRepository
	 */
	private $list_screen_repository;

	public function __construct( ListScreenRepository $list_screen_repository ) {
		$this->list_screen_repository = $list_screen_repository;
	}

	public function handle() {
		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error();
		}

		$id = (string) $request->filter( 'layout', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! ListScreenId::is_valid_id( $id ) ) {
			wp_send_json_error();
		}

		$list_screen = $this->list_screen_repository->find(
			new ListScreenId( $id )
		);

		if ( ! $list_screen ) {
			wp_send_json_error();
		}

		// This hook allows you to change the default generated CSV filename.
		$file_name = apply_filters(
			'acp/export/file_name',
			(string) $request->filter( 'file_name', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS ),
			$list_screen
		);

		wp_send_json_success( (string) $file_name );
	}

}