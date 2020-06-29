<?php

namespace ACP\QuickAdd\Controller;

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
		add_action( 'ac/table/list_screen', [ $this, 'handle_request' ], 11 );
	}

	public function handle_request() {
		if ( 'acp_add_new_inline' !== $this->request->get( 'ac_action' ) ) {
			return;
		}

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

		$response->create_from_list_screen( $list_screen, $id )
		         ->success();
	}

}