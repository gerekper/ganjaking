<?php

namespace ACP\Sorting\Controller;

use AC\Ajax;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Type\ListScreenId;
use ACP\Sorting\UserPreference;

class AjaxResetSorting implements Registerable {

	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function register() {
		$this->get_ajax_handler()->register();
	}

	private function get_ajax_handler(): Ajax\Handler {
		$handler = new Ajax\Handler();
		$handler
			->set_action( 'acp_reset_sorting' )
			->set_callback( [ $this, 'handle_reset' ] );

		return $handler;
	}

	public function handle_reset() {
		$this->get_ajax_handler()->verify_request();

		$list_id = filter_input( INPUT_POST, 'layout' );

		if ( ! ListScreenId::is_valid_id( $list_id ) ) {
			wp_send_json_error();
		}

		$list_screen = $this->storage->find_by_user( new ListScreenId( $list_id ), wp_get_current_user() );

		if ( ! $list_screen ) {
			wp_send_json_error();
		}

		$preference = new UserPreference\SortType( $list_screen->get_storage_key() );

		wp_send_json_success( $preference->delete() );
	}

}