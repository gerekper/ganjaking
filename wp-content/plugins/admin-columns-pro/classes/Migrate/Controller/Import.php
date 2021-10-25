<?php

namespace ACP\Migrate\Controller;

use AC\Capabilities;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository;
use AC\Message;
use AC\Registrable;
use ACP\Parser;
use RuntimeException;
use SplFileInfo;

class Import implements Registrable {

	const NONCE = 'file-import';

	/** @var ListScreenRepository\Aggregate */
	private $repository;

	/** @var Parser\FileDecodeFactory */
	private $fileDecodeFactory;

	public function __construct( ListScreenRepository\Aggregate $repository, Parser\FileDecodeFactory $fileDecodeFactory ) {
		$this->repository = $repository;
		$this->fileDecodeFactory = $fileDecodeFactory;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	public function handle_request() {
		if ( empty( $_FILES['import'] )
		     || ! $this->verify_nonce( self::NONCE )
		     || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$file = wp_import_handle_upload();

		if ( isset( $file['error'] ) ) {
			$this->error_notice( esc_html( $file['error'] ) );

			return;
		}

		$fileInfo = new SplFileInfo( $file['file'] );

		$jsonDecoder = $this->fileDecodeFactory->create( Parser\FileDecodeFactory::FORMAT_JSON );

		try {
			$list_screens = $jsonDecoder->decode_file( $fileInfo );
		} catch ( RuntimeException $e ) {
			$this->error_notice( $e->getMessage() );

			return;
		}

		// cleanup
		wp_delete_attachment( $file['id'] );

		if ( 0 === $list_screens->count() ) {
			return;
		}

		foreach ( $list_screens as $list_screen ) {
			$this->repository->save( $list_screen );
		}

		$this->success_notice( $list_screens );
	}

	private function success_notice( ListScreenCollection $list_screens ) {
		$grouped = [];

		/** @var ListScreen $list_screen */
		foreach ( $list_screens as $list_screen ) {
			$grouped[ $list_screen->get_label() ][] = sprintf( '<a href="%s">%s</a>', $list_screen->get_edit_link(), '<strong>' . esc_html( $list_screen->get_title() ) . '</strong>' );
		}

		foreach ( $grouped as $label => $links ) {
			$message = sprintf(
				__( 'Succesfully imported %s for %s.', 'codepress-admin-columns' ),
				ac_helper()->string->enumeration_list( $links, 'and' ) . ' ' . _n( 'set', 'sets', count( $links ), 'codepress-admin-columns' ),
				"<strong>" . $label . "</strong>"
			);

			( new Message\Notice( $message ) )->register();
		}
	}

	private function error_notice( $message ) {
		$message = __( 'Sorry, there has been an error.', 'codepress-admin-columns' ) . '<br>' . $message;

		$notice = new Message\Notice( $message );
		$notice->set_type( Message::ERROR )
		       ->register();
	}

	private function verify_nonce( $action ) {
		return wp_verify_nonce( filter_input( INPUT_POST, '_ac_nonce' ), $action );
	}

}