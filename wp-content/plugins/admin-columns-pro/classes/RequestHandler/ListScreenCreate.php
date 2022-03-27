<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\ListScreenTypes;
use AC\Message\Notice;
use AC\Request;
use AC\Storage\ListScreenOrder;
use AC\Type\ListScreenId;
use ACP\Nonce;
use ACP\RequestHandler;

class ListScreenCreate implements RequestHandler {

	const PARAM_ACTION = 'action';
	const PARAM_CREATE_LIST = 'create-layout';
	const PARAM_DELETE_LIST = 'delete-layout';

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var ListScreenOrder
	 */
	private $order;

	public function __construct( Storage $storage, ListScreenOrder $order ) {
		$this->storage = $storage;
		$this->order = $order;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\LayoutNonce() )->verify( $request ) ) {
			return;
		}

		$list_id = ListScreenId::is_valid_id( $request->get( 'list_id' ) )
			? new ListScreenId( $request->get( 'list_id' ) )
			: null;

		$current_list_screen = false;

		if ( $list_id && $this->storage->exists( $list_id ) ) {
			$current_list_screen = $this->storage->find( $list_id );
		}

		if ( ! $current_list_screen ) {
			$current_list_screen = ListScreenTypes::instance()->get_list_screen_by_key( $request->get( 'list_key' ) );
		}

		$title = trim( $request->get( 'title' ) );

		if ( empty( $title ) ) {
			$notice = new Notice( __( 'Name can not be empty.', 'codepress-admin-columns' ) );
			$notice->set_type( Notice::ERROR )->register();

			return;
		}

		$list_screen = ListScreenTypes::instance()->get_list_screen_by_key( $request->get( 'list_key' ) );

		if ( null === $list_screen ) {
			return;
		}

		$settings = [];
		$preferences = [];

		if ( $request->get( 'clone_current' ) === '1' ) {
			$settings = $current_list_screen->get_settings();
			$preferences = $current_list_screen->get_preferences();
		}

		$list_screen->set_layout_id( ListScreenId::generate()->get_id() )
		            ->set_title( $title )
		            ->set_settings( $settings )
		            ->set_preferences( $preferences );

		$this->storage->save( $list_screen );

		$this->order->add( $list_screen->get_key(), $list_screen->get_layout_id() );

		wp_redirect( $list_screen->get_edit_link() );
		exit;

	}

}