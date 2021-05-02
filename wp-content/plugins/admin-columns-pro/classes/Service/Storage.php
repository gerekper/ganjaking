<?php

namespace ACP\Service;

use AC;
use AC\ListScreenRepository\Storage\ListScreenRepository;
use AC\Registrable;
use ACP\ListScreenRepository\Collection;
use ACP\ListScreenRepository\FileFactory;
use ACP\Storage\Directory;
use ACP\Storage\ListScreen\LegacyCollectionDecoder;
use ACP\Storage\ListScreen\SerializerTypes;
use ACP\Storage\ListScreenRepositoryFactory;

final class Storage implements Registrable {

	/**
	 * @var AC\ListScreenRepository\Storage
	 */
	private $storage;

	/**
	 * @var FileFactory
	 */
	private $file_factory;

	/**
	 * @var AC\EncodedListScreenDataFactory
	 */
	private $encoded_list_screen_data_factory;

	/**
	 * @var LegacyCollectionDecoder
	 */
	private $collection_decoder;

	public function __construct(
		AC\ListScreenRepository\Storage $storage,
		FileFactory $file_factory,
		AC\EncodedListScreenDataFactory $encoded_list_screen_data_factory,
		LegacyCollectionDecoder $collection_decoder
	) {
		$this->storage = $storage;
		$this->file_factory = $file_factory;
		$this->encoded_list_screen_data_factory = $encoded_list_screen_data_factory;
		$this->collection_decoder = $collection_decoder;
	}

	public function register() {
		add_action( 'ac/list_screens', [ $this, 'configure' ], 20 );
	}

	public function configure() {
		$repositories = $this->storage->get_repositories();

		$this->configure_file_storage( $repositories );

		$repositories = apply_filters( 'acp/storage/repositories',
			$repositories,
			new ListScreenRepositoryFactory( $this->file_factory )
		);

		$this->configure_api_storage( $repositories );

		$this->storage->set_repositories( $repositories );
	}

	private function configure_api_storage( array &$repositories ) {
		$collection = new AC\ListScreenCollection();

		foreach ( $this->encoded_list_screen_data_factory->create() as $data ) {
			if ( ! $this->collection_decoder->can_decode( $data ) ) {
				continue;
			}

			foreach ( $this->collection_decoder->decode( $data ) as $list_screen ) {
				$collection->add( $list_screen );
			}
		}

		if ( ! $collection->count() ) {
			return;
		}

		$repositories['acp-collection'] = new ListScreenRepository(
			new Collection( $collection ),
			false
		);
	}

	private function configure_file_storage( array &$repositories ) {
		if ( apply_filters( 'acp/storage/file/enable_for_multisite', false ) && is_multisite() ) {
			return;
		}

		$path = apply_filters( 'acp/storage/file/directory', null );

		if ( ! is_string( $path ) || $path === '' ) {
			return;
		}

		$directory = new Directory( $path );

		if ( ! $directory->exists() && $directory->has_path( WP_CONTENT_DIR ) ) {
			$directory->create();
		}

		$file = new ListScreenRepository(
			$this->file_factory->create(
				SerializerTypes::PHP,
				$directory
			),
			apply_filters( 'acp/storage/file/directory/writable', true )
		);

		$repositories['acp-file'] = $file;

		if ( ! $file->is_writable() || ! $this->storage->has_repository( 'acp-database' ) ) {
			return;
		}

		$database = $this->storage->get_repository( 'acp-database' );

		if ( apply_filters( 'acp/storage/file/directory/migrate', false ) ) {
			$this->run_migration( $database, $file );
		}

		if ( apply_filters( 'acp/storage/file/directory/copy', false ) ) {
			$this->run_copy( $database, $file );
		}

		$repositories['acp-database'] = $database->with_writable( false );
	}

	private function run_migration( ListScreenRepository $from, ListScreenRepository $to ) {
		foreach ( $from->with_writable( true )->find_all() as $list_screen ) {
			$to->save( $list_screen );
			$from->delete( $list_screen );
		}
	}

	private function run_copy( ListScreenRepository $from, ListScreenRepository $to ) {
		foreach ( $from->find_all() as $list_screen ) {
			$to->save( $list_screen );
		}
	}

}