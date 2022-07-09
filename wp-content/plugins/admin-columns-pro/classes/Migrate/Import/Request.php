<?php

namespace ACP\Migrate\Import;

use AC\Capabilities;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Storage;
use AC\Message;
use AC\Registrable;
use ACP\Exception\DecoderNotFoundException;
use ACP\Migrate\MessageTrait;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\EncodedCollection;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use ACP\Storage\ListScreen\SerializerTypes;
use ACP\Storage\ListScreen\Unserializer;
use Exception;

final class Request implements Registrable {

	use MessageTrait;

	const ACTION = 'acp-import';
	const NONCE_NAME = 'acp_import_nonce';

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var LegacyCollectionDecoder
	 */
	private $legacy_collection_decoder;

	/**
	 * @var DecoderFactory
	 */
	private $decoder_factory;

	public function __construct( Storage $storage, DecoderFactory $decoder_factory, LegacyCollectionDecoder $legacy_collection_decoder ) {
		$this->storage = $storage;
		$this->decoder_factory = $decoder_factory;
		$this->legacy_collection_decoder = $legacy_collection_decoder;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	/**
	 * @return bool
	 */
	private function is_request() {
		$data = filter_input_array( INPUT_POST, [
			'action'         => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			self::NONCE_NAME => FILTER_DEFAULT,
		] );

		if ( ! isset( $data['action'] ) || $data['action'] !== self::ACTION ) {
			return false;
		}

		if ( ! wp_verify_nonce( $data[ self::NONCE_NAME ], $data['action'] ) ) {
			return false;
		}

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return false;
		}

		if ( ! isset( $_FILES['import']['name'], $_FILES['import']['tmp_name'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @return void
	 */
	public function handle_request() {
		if ( ! $this->is_request() ) {
			return;
		}

		$extension = pathinfo( $_FILES['import']['name'], PATHINFO_EXTENSION );

		if ( $extension !== SerializerTypes::JSON ) {
			$this->set_message( sprintf( __( 'Uploaded file does not have a %s extension.', 'codepress-admin-columns' ), '.' . SerializerTypes::JSON ) );

			return;
		}

		$file_contents = file_get_contents( $_FILES['import']['tmp_name'] );

		if ( empty( $file_contents ) ) {
			$this->set_message( __( 'Uploaded file is empty or not readable.', 'codepress-admin-columns' ) );

			return;
		}

		$encoded_list_screens = ( new Unserializer\JsonUnserializer() )->unserialize( $file_contents );

		if ( ! is_array( $encoded_list_screens ) ) {
			$this->set_message( __( 'Error parsing the uploaded file.', 'codepress-admin-columns' ) );

			return;
		}

		$list_screens = new ListScreenCollection();
		$errors = [];

		if ( $this->legacy_collection_decoder->can_decode( $encoded_list_screens ) ) {
			$list_screens = $this->legacy_collection_decoder->decode( $encoded_list_screens );
		} else {
			if ( ! EncodedCollection::is_valid_collection( $encoded_list_screens ) ) {
				$this->set_message( __( 'Error parsing the contents from the uploaded file.', 'codepress-admin-columns' ) );

				return;
			}

			$encoded_collection = new EncodedCollection( $encoded_list_screens, $this->decoder_factory );

			foreach ( $encoded_collection as $encoded_list_screen ) {
				try {
					if ( $encoded_collection->can_decode( $encoded_list_screen ) ) {
						$list_screens->add( $encoded_collection->decode( $encoded_list_screen ) );
					}
				} catch ( DecoderNotFoundException $e ) {
					if ( isset( $encoded_list_screen['id'] ) ) {
						$errors[] = sprintf( __( 'Column settings with id %s cannot be imported.', 'codepress-admin-columns' ), $encoded_list_screen['id'] );
					}
				}
			}
		}

		if ( empty( $errors ) && ! $list_screens->count() ) {
			$this->set_message( __( 'The uploaded file does not contain any column settings.', 'codepress-admin-columns' ), Message::WARNING );

			return;
		}

		foreach ( $list_screens as $list_screen ) {
			try {
				$this->storage->save( $list_screen );
			} catch ( Exception $e ) {
				$errors[] = sprintf( __( 'Columns settings with id %s could not be saved.', 'codepress-admin-columns' ), $list_screen->get_id()->get_id() );
			}
		}

		$this->success( $list_screens );

		foreach ( $errors as $error ) {
			$this->set_message( $error, Message::WARNING );
		}
	}

	private function success( ListScreenCollection $list_screens ) {
		$grouped = [];

		foreach ( $list_screens as $list_screen ) {
			$grouped[ $list_screen->get_label() ][] = sprintf( '<a href="%s">%s</a>', $list_screen->get_edit_link(), '<strong>' . esc_html( $list_screen->get_title() ) . '</strong>' );
		}

		foreach ( $grouped as $label => $links ) {
			$message = sprintf(
				__( 'Successfully imported %s for %s.', 'codepress-admin-columns' ),
				ac_helper()->string->enumeration_list( $links, 'and' ) . ' ' . _n( 'set', 'sets', count( $links ), 'codepress-admin-columns' ),
				"<strong>" . $label . "</strong>"
			);

			$this->set_message( $message, Message::SUCCESS );
		}
	}

}