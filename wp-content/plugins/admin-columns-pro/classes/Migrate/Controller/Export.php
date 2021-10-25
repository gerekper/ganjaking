<?php
namespace ACP\Migrate\Controller;

use AC\Ajax;
use AC\Capabilities;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Aggregate;
use AC\Message;
use AC\Registrable;
use AC\Request;
use ACP\Parser;
use RuntimeException;

class Export implements Registrable {

	const FILE_PREFIX = 'admin-columns-export';
	const RESPONSE_TYPE_FILE = 'file';
	const RESPONSE_TYPE_STRING = 'string';

	/** @var Aggregate */
	private $repository;

	/** @var Parser\FileEncodeFactory */
	private $encodeFactory;

	/** @var Request */
	private $request;

	public function __construct( Aggregate $repository, Parser\FileEncodeFactory $fileEncodeFactory, Request $request ) {
		$this->repository = $repository;
		$this->encodeFactory = $fileEncodeFactory;
		$this->request = $request;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_post_request' ] );

		$this->get_ajax_handler()->register();
	}

	private function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'acp-export-php' )
		        ->set_callback( [ $this, 'handle_ajax_request' ] );

		return $handler;
	}

	public function handle_ajax_request() {
		$this->get_ajax_handler()->verify_request();

		$this->handle_request(
			$this->request->get( 'encoder' ),
			$this->request->filter( 'list_screen_id', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
			$this->request->get( 'response_type' )
		);
	}

	public function handle_post_request() {
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_ac_nonce' ), 'export' ) || ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( 'acp-export' !== $this->request->get( 'action' ) ) {
			return;
		}

		$this->handle_request(
			$this->request->get( 'encoder' ),
			$this->request->filter( 'list_screen_id', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
			$this->request->get( 'response_type' )
		);
	}

	private function handle_request( $encodeType, array $ids, $responseType = null ) {
		try {
			$encoder = $this->encodeFactory->create( $encodeType );
		} catch ( RuntimeException $e ) {
			return;
		}

		$listScreens = $this->get_list_screens( $ids );

		if ( 0 === $listScreens->count() ) {
			$this->error_notice( __( 'Export field is empty. Please select your types from the left column.', 'codepress-admin-columns' ) );

			return;
		}

		$encodedString = $encoder->format( $listScreens );

		switch ( $responseType ) {
			case self::RESPONSE_TYPE_FILE :
				$fileName = sprintf( '%s_%s.%s', self::FILE_PREFIX, date( 'Y-m-d-Hi' ), $encoder->get_file_type() );
				$this->download_file( $fileName, $encodedString );

				exit;
			default :
				echo $encodedString;

				exit;
		}
	}

	/**
	 * @param string $filename
	 * @param string $data
	 */
	private function download_file( $filename, $data ) {
		header( 'Content-disposition: attachment; filename=' . $filename );
		header( 'Content-type: application/json' );

		echo $data;
		exit;
	}

	private function error_notice( $message ) {
		$notice = new Message\Notice( $message );
		$notice->set_type( Message::ERROR )
		       ->register();
	}

	/**
	 * @param array $ids
	 *
	 * @return ListScreenCollection
	 */
	private function get_list_screens( array $ids = [] ) {
		$listScreens = new ListScreenCollection();

		foreach ( $ids as $id ) {
			$listScreen = $this->repository->find( $id );

			if ( $listScreen ) {
				$listScreens->push( $listScreen );
			}
		}

		return $listScreens;
	}

}