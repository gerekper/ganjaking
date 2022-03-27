<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\Message\Notice;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\Nonce;
use ACP\RequestHandler;

class ListScreenDelete implements RequestHandler {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\LayoutNonce() )->verify( $request ) ) {
			return;
		}

		$list_screen = $this->storage->find( new ListScreenId( $request->get( 'layout_id' ) ) );

		if ( ! $list_screen ) {
			return;
		}

		$this->storage->delete( $list_screen );

		$notice = new Notice( sprintf( __( 'Column set %s successfully deleted.', 'codepress-admin-columns' ), sprintf( '<strong>"%s"</strong>', esc_html( $list_screen->get_title() ) ) ) );
		$notice->register();

		do_action( 'acp/list_screen/deleted', $list_screen );
	}

}