<?php

namespace ACP\QuickAdd\Controller;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use ACP\QuickAdd\Table;

class AjaxScreenOption implements AC\Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Table\Preference\ShowButton
	 */
	private $preference_button;

	public function __construct( Storage $storage, Table\Preference\ShowButton $preference_button ) {
		$this->storage = $storage;
		$this->preference_button = $preference_button;
	}

	public function register() {
		$this->get_ajax_handler()->register();
	}

	protected function get_ajax_handler() {
		$handler = new AC\Ajax\Handler();
		$handler->set_action( 'acp_new_inline_show_button' )
		        ->set_callback( [ $this, 'update_table_option' ] );

		return $handler;
	}

	public function update_table_option() {
		$this->get_ajax_handler()->verify_request();

		$list_screen = $this->storage->find( new ListScreenId( filter_input( INPUT_POST, 'layout' ) ) );

		if ( ! $list_screen ) {
			exit;
		}

		echo $this->preference_button->set( $list_screen->get_key(), 'true' === filter_input( INPUT_POST, 'value' ) );
		exit;
	}

}