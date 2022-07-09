<?php

namespace ACP\Migrate\Export;

use AC;
use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;

final class Request implements AC\Registrable {

	const ACTION = 'acp-export';
	const NONCE_NAME = 'acp_export_nonce';

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var ResponseFactory
	 */
	private $response_factory;

	public function __construct( Storage $storage, ResponseFactory $response_factory ) {
		$this->storage = $storage;
		$this->response_factory = $response_factory;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	/**
	 * @return void
	 */
	public function handle_request() {
		$data = (object) filter_input_array( INPUT_POST, [
			'action'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'list_screen_ids' => [
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_REQUIRE_ARRAY,
			],
			self::NONCE_NAME  => FILTER_DEFAULT,
		] );

		if ( ! isset( $data->action ) || $data->action !== self::ACTION ) {
			return;
		}

		if ( ! wp_verify_nonce( $data->{self::NONCE_NAME}, $data->action ) ) {
			return;
		}

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( empty( $data->list_screen_ids ) ) {
			return;
		}

		$response = $this->response_factory->create(
			$this->get_list_screens_from_request( $data->list_screen_ids )
		);

		$response->send();
	}

	/**
	 * @param array $ids
	 *
	 * @return AC\ListScreenCollection
	 */
	protected function get_list_screens_from_request( array $ids ) {
		$list_screens = new AC\ListScreenCollection();

		foreach ( $ids as $id ) {
			$list_screen = $this->storage->find( new ListScreenId( $id ) );

			if ( $list_screen ) {
				$list_screens->add( $list_screen );
			}
		}

		return $list_screens;
	}

}