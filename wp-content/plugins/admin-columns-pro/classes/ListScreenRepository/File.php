<?php

declare( strict_types=1 );

namespace ACP\ListScreenRepository;

use AC;
use AC\Exception\MissingListScreenIdException;
use AC\Exception\SourceNotAvailableException;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Filter;
use AC\ListScreenRepository\ListScreenPermissionTrait;
use AC\ListScreenRepository\Sort;
use AC\ListScreenRepository\SourceAware;
use AC\OpCacheInvalidateTrait;
use AC\Type\ListScreenId;
use ACP\Exception\DecoderNotFoundException;
use ACP\Exception\DirectoryNotWritableException;
use ACP\Exception\FileNotWritableException;
use ACP\Storage\Directory;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\Serializer;
use ACP\Storage\ListScreen\Unserializer;
use LogicException;
use SplFileInfo;
use WP_User;

final class File implements AC\ListScreenRepositoryWritable, SourceAware {

	use OpCacheInvalidateTrait;
	use ListScreenPermissionTrait;

	/**
	 * @var Directory
	 */
	private $directory;

	/**
	 * @var null
	 */
	private $extension;

	/**
	 * @var DecoderFactory
	 */
	private $decoder_factory;

	/**
	 * @var Unserializer|null
	 */
	private $unserializer;

	/**
	 * @var Encoder
	 */
	private $encoder;

	/**
	 * @var Serializer
	 */
	private $serializer;

	public function __construct(
		Directory $directory,
		string $extension,
		Encoder $encoder,
		DecoderFactory $decoder_factory,
		Serializer $serializer,
		Unserializer $unserializer = null
	) {
		$this->directory = $directory;
		$this->extension = $extension;
		$this->encoder = $encoder;
		$this->decoder_factory = $decoder_factory;
		$this->serializer = $serializer;
		$this->unserializer = $unserializer;

		$this->validate();
	}

	private function validate(): void {
		if ( $this->extension !== null && ! preg_match( '/^[a-z0-9]{2,4}$/', $this->extension ) ) {
			throw new LogicException( 'Invalid extension found.' );
		}
	}

	public function find_all( Sort $sort = null ): ListScreenCollection {
		$list_screens = $this->find_all_from_files();

		return $sort
			? $sort->sort( $list_screens )
			: $list_screens;
	}

	public function find( ListScreenId $id ): ?ListScreen {
		$list_screens = ( new Filter\ListId( $id ) )->filter(
			$this->find_all_from_files()
		);

		return $list_screens->get_first() ?: null;
	}

	public function find_by_user( ListScreenId $id, WP_User $user ): ?ListScreen {
		$list_screen = $this->find( $id );

		return $list_screen && $this->user_can_view_list_screen( $list_screen, $user )
			? $list_screen
			: null;
	}

	public function find_all_by_key( string $key, Sort $sort = null ): ListScreenCollection {
		$list_screens = ( new Filter\ListKey( $key ) )->filter(
			$this->find_all_from_files()
		);

		return $sort
			? $sort->sort( $list_screens )
			: $list_screens;
	}

	public function exists( ListScreenId $id ): bool {
		return null !== $this->find( $id );
	}

	private function find_all_from_files(): ListScreenCollection {
		$list_screens = new ListScreenCollection();

		foreach ( $this->get_files() as $file ) {
			$encoded_list_screen = $this->unserializer
				? $this->unserializer->unserialize( $file->openFile()->fread( $file->getSize() ) )
				: require( $file->getRealPath() );

			try {
				$decoder = $this->decoder_factory->create( $encoded_list_screen );
			} catch ( DecoderNotFoundException $e ) {
				continue;
			}

			if ( ! $decoder->can_decode( $encoded_list_screen ) ) {
				continue;
			}

			$list_screen = $decoder->decode( $encoded_list_screen );

			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	public function find_all_by_user( string $key, WP_User $user, Sort $sort = null ): ListScreenCollection {
		$list_screens = $this->find_all_by_key( $key, $sort );

		return ( new Filter\User( $user ) )->filter( $list_screens );
	}

	public function save( ListScreen $list_screen ): void {
		if ( ! $this->directory->exists() ) {
			$this->directory->create();
		}

		if ( ! $this->directory->get_info()->isWritable() ) {
			throw new DirectoryNotWritableException( $this->directory->get_path() );
		}

		if ( ! $list_screen->has_id() ) {
			throw MissingListScreenIdException::from_saving_list_screen();
		}

		$file = $this->create_file_name(
			$this->directory->get_path(),
			$list_screen->get_id()
		);

		$result = file_put_contents(
			$file,
			$this->serializer->serialize( $this->encoder->encode( $list_screen ) )
		);

		if ( $result === false ) {
			throw FileNotWritableException::from_saving_list_screen( $list_screen );
		}

		$this->opcache_invalidate( $file );
	}

	public function delete( ListScreen $list_screen ): void {
		$file = $this->create_file_name(
			$this->directory->get_path(),
			$list_screen->get_id()
		);

		$this->opcache_invalidate( $file );

		$result = unlink( $file );

		if ( $result === false ) {
			throw FileNotWritableException::from_removing_list_screen( $list_screen );
		}
	}

	/**
	 * Get all files and do superficial checks on them
	 * @return SplFileInfo[]
	 */
	private function get_files(): array {
		$files = [];

		if ( $this->directory->is_readable() ) {
			foreach ( $this->directory->get_files() as $file ) {
				if ( ! $file->isFile() || ! $file->isReadable() || $file->getSize() === 0 ) {
					continue;
				}

				if ( $this->extension !== null && $this->extension !== $file->getExtension() ) {
					continue;
				}

				$files[] = $file->getFileInfo();
			}
		}

		return $files;
	}

	private function create_file_name( string $path, ListScreenId $id ): string {
		return sprintf( '%s/%s.%s', $path, $id->get_id(), $this->extension );
	}

	public function get_directory(): Directory {
		return $this->directory;
	}

	public function get_source( ListScreenId $id ): string {
		if ( ! $this->has_source( $id ) ) {
			throw new SourceNotAvailableException();
		}

		return $this->create_file_name(
			$this->directory->get_path(),
			$id
		);
	}

	public function has_source( ListScreenId $id ): bool {
		return $this->exists( $id );
	}

}