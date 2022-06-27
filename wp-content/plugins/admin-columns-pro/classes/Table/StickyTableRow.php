<?php

namespace ACP\Table;

use AC;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;

class StickyTableRow implements AC\Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function register() {
		add_action( 'ac/table', [ $this, 'register_screen_option' ] );

		$this->ajax_handler()->register();
	}

	private function ajax_handler() {
		$handler = new AC\Ajax\Handler();

		$handler
			->set_action( 'acp_update_sticky_row_option' )
			->set_callback( [ $this, 'update_sticky_table' ] );

		return $handler;
	}

	public function preferences() {
		return new AC\Preferences\Site( 'show_sticky_table_row' );
	}

	public function is_sticky( $key ) {
		return (bool) apply_filters( 'acp/sticky_header/enable', $this->preferences()->get( $key ) );
	}

	public function update_sticky_table() {
		$this->ajax_handler()->verify_request();

		$key = filter_input( INPUT_POST, 'list_screen' );

		$list_screen = ListScreenId::is_valid_id( filter_input( INPUT_POST, 'layout' ) )
			? $this->storage->find( new ListScreenId( filter_input( INPUT_POST, 'layout' ) ) )
			: null;

		if ( $list_screen ) {
			$key = $list_screen->get_storage_key();
		}

		$this->preferences()->set( $key, 'true' === filter_input( INPUT_POST, 'value' ) );
		exit;
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( $table ) {
		$list_screen = $table->get_list_screen();

		if ( ! $list_screen->get_settings() ) {
			return;
		}

		$check_box = ( new AC\Form\Element\Checkbox( 'acp_sticky_table_row' ) )
			->set_options( [
				'yes' => __( 'Sticky Headers', 'codepress-admin-columns' ),
			] )
			->set_value( $this->is_sticky( $table->get_list_screen()->get_storage_key() ) ? 'yes' : '' );

		$table->register_screen_option( $check_box );
	}

}