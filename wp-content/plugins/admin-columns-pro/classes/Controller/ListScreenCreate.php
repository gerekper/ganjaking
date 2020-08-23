<?php

namespace ACP\Controller;

use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\ListScreenTypes;
use AC\Message\Notice;
use AC\Registrable;
use AC\Request;
use AC\Storage\ListScreenOrder;
use AC\Type\ListScreenId;

class ListScreenCreate implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var ListScreenOrder
	 */
	private $order;

	public function __construct( Storage $storage, Request $request, ListScreenOrder $order ) {
		$this->storage = $storage;
		$this->request = $request;
		$this->order = $order;
	}

	private function verify_nonce( $action ) {
		return wp_verify_nonce( filter_input( INPUT_POST, '_ac_nonce' ), $action );
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	public function handle_request() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		switch ( filter_input( INPUT_POST, 'acp_action' ) ) {

			case 'create_layout':
				if ( ! $this->verify_nonce( 'create-layout' ) ) {
					return;
				}

				$list_id = ListScreenId::is_valid_id( $this->request->get( 'list_id' ) )
					? new ListScreenId( $this->request->get( 'list_id' ) )
					: null;

				$current_list_screen = false;

				if ( $list_id && $this->storage->exists( $list_id ) ) {
					$current_list_screen = $this->storage->find( $list_id );
				}

				if ( ! $current_list_screen ) {
					$current_list_screen = ListScreenTypes::instance()->get_list_screen_by_key( $this->request->get( 'list_key' ) );
				}

				$title = trim( $this->request->get( 'title' ) );

				if ( empty( $title ) ) {
					$notice = new Notice( __( 'Name can not be empty.', 'codepress-admin-columns' ) );
					$notice->set_type( Notice::ERROR )->register();

					return;
				}

				$list_screen = ListScreenTypes::instance()->get_list_screen_by_key( $this->request->get( 'list_key' ) );

				if ( null === $list_screen ) {
					return;
				}

				$list_screen->set_layout_id( ListScreenId::generate()->get_id() )
				            ->set_title( $title )
				            ->set_settings( $current_list_screen->get_settings() )
				            ->set_preferences( $current_list_screen->get_preferences() );

				$this->storage->save( $list_screen );

				$this->order->add( $list_screen->get_key(), $list_screen->get_layout_id() );

				wp_redirect( $list_screen->get_edit_link() );
				exit;

			case 'delete_layout' :
				if ( ! $this->verify_nonce( 'delete-layout' ) ) {
					return;
				}

				$list_screen = $this->storage->find( new ListScreenId( $this->request->get( 'layout_id' ) ) );

				if ( ! $list_screen ) {
					return;
				}

				$this->storage->delete( $list_screen );

				$notice = new Notice( sprintf( __( 'Column set %s successfully deleted.', 'codepress-admin-columns' ), sprintf( '<strong>"%s"</strong>', esc_html( $list_screen->get_title() ) ) ) );
				$notice->register();

				do_action( 'acp/list_screen/deleted', $list_screen );

				break;
		}
	}

}