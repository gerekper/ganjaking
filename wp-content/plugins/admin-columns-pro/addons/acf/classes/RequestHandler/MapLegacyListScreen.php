<?php

namespace ACA\ACF\RequestHandler;

use AC\Capabilities;
use AC\ListScreenRepository;
use AC\Request;
use AC\Type\ListScreenId;
use ACA\ACF\Nonce\UpdateDeprecatedNonce;
use ACA\ACF\Utils\V2ToV3Migration;
use ACP\RequestHandler;

class MapLegacyListScreen implements RequestHandler {

	/**
	 * @var ListScreenRepository\Storage
	 */
	private $list_screen_repository;

	/**
	 * @param ListScreenRepository\Storage $list_screen_repository
	 */
	public function __construct( ListScreenRepository\Storage $list_screen_repository ) {
		$this->list_screen_repository = $list_screen_repository;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new UpdateDeprecatedNonce() )->verify( $request ) ) {
			return;
		}

		$list_screen_id = filter_input( INPUT_POST, 'migrate_list_screen_id' );

		if ( ! ListScreenId::is_valid_id( $list_screen_id ) ) {
			return;
		}

		$list_screen = $this->list_screen_repository->find( new ListScreenId( $list_screen_id ) );

		if ( ! $list_screen || $list_screen->is_read_only() ) {
			return;
		}

		( new V2ToV3Migration )->migrate_list_screen_settings( $list_screen );

		$this->list_screen_repository->save( $list_screen );
	}

}