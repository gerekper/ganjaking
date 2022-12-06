<?php

namespace ACA\EC\ImportListscreens;

use AC;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\EncodedCollection;
use ACP\Storage\ListScreen\Unserializer\JsonUnserializer;

class Controller implements AC\Registerable {

	const ACTION_KEY = 'aca_ec_action';
	const IMPORT_METHOD_KEY = 'import-sets';
	const DISMISS_METHOD_KEY = 'dismiss-import';

	/**
	 * @var AC\Request
	 */
	private $request;

	/**
	 * @var ImportedSetting
	 */
	private $setting;

	/**
	 * @var DecoderFactory
	 */
	private $decoder_factory;

	/**
	 * @var AC\ListScreenRepository\Storage
	 */
	private $storage;

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	public function __construct( AC\Request $request, AC\ListScreenRepository\Storage $storage, DecoderFactory $decoder_factory, AC\Asset\Location\Absolute $location ) {
		$this->request = $request;
		$this->decoder_factory = $decoder_factory;
		$this->storage = $storage;
		$this->setting = new ImportedSetting();
		$this->location = $location;
	}

	public function register() {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	public function handle_request() {
		$action = $this->request->get( self::ACTION_KEY );

		if ( ! $action || ! in_array( $action, [ self::DISMISS_METHOD_KEY, self::IMPORT_METHOD_KEY ], true ) ) {
			return;
		}

		switch ( $action ) {
			case self::IMPORT_METHOD_KEY:
				$this->import();
				$this->setting->mark_as_imported();
				break;

			case self::DISMISS_METHOD_KEY:
				$this->setting->mark_as_imported();
				break;
		}

		wp_redirect( remove_query_arg( self::ACTION_KEY ) );
		exit;
	}

	private function import() {
		if ( $this->setting->is_imported() ) {
			return;
		}

		$file_content = file_get_contents( $this->location->with_suffix( '/export/events.json' )->get_path() );
		$encoded_list_screens = ( new JsonUnserializer() )->unserialize( $file_content );

		if ( ! EncodedCollection::is_valid_collection( $encoded_list_screens ) ) {
			return;
		}

		$encoded_collection = new EncodedCollection( $encoded_list_screens, $this->decoder_factory );

		foreach ( $encoded_collection as $encoded_list_screen ) {
			if ( ! $encoded_collection->can_decode( $encoded_list_screen ) ) {
				continue;
			}

			$list_screen = $encoded_collection->decode( $encoded_list_screen );
			$this->storage->save( $list_screen );
		}
	}

}