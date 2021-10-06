<?php

namespace ACP\QuickAdd\Controller;

use AC\ListScreen;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\QuickAdd\Model;
use RuntimeException;

class AjaxNewItem implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Request
	 */
	protected $request;

	public function __construct( Storage $storage, Request $request ) {
		$this->storage = $storage;
		$this->request = $request;
	}

	public function register() {
		if ( $this->is_request() ) {
			add_action( 'ac/table/list_screen', [ $this, 'register_hooks' ] );
		}

	}

	public function register_hooks( ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ListScreen\Post:
				add_action( 'edit_posts_per_page', [ $this, 'handle_request' ] );
				break;
		}
	}

	private function is_request() {
		return $this->request->get( 'ac_action' ) === 'acp_add_new_inline';
	}

	public function handle_request() {
		if ( ! wp_verify_nonce( $this->request->get( '_ajax_nonce' ), 'ac-ajax' ) ) {
			return;
		}

		$response = new JsonResponse();

		$list_screen = $this->storage->find( new ListScreenId( $this->request->get( 'layout' ) ) );

		if ( ! $list_screen ) {
			$response->error();
		}

		$model = Model\Factory::create( $list_screen );

		if ( ! $model || ! $model->has_permission( wp_get_current_user() ) ) {
			$response->error();
		}

		try {
			$id = $model->create();
		} catch ( RuntimeException $e ) {
			$response->set_message( $e->getMessage() )
			         ->error();
		}

		do_action( 'acp/quick_add/saved', $id, $list_screen );

		$response->create_from_list_screen( $list_screen, $id )
		         ->success();
	}

}