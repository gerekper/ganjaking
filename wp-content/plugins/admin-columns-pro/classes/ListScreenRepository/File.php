<?php

namespace ACP\ListScreenRepository;

use AC;
use AC\Exception\MissingListScreenIdException;
use AC\Exception\SourceNotAvailableException;
use AC\ListScreen;
use AC\ListScreenCollection;
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

final class File implements AC\ListScreenRepositoryWritable, SourceAware {

	use OpCacheInvalidateTrait;

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

	/**
	 * @param Directory         $directory
	 * @param string            $extension
	 * @param Encoder           $encoder
	 * @param DecoderFactory    $decoder_factory
	 * @param Serializer        $serializer
	 * @param Unserializer|null $unserializer
	 */
	public function __construct( Directory $directory, $extension, Encoder $encoder, DecoderFactory $decoder_factory, Serializer $serializer, Unserializer $unserializer = null ) {
		$this->directory = $directory;
		$this->extension = $extension;
		$this->encoder = $encoder;
		$this->decoder_factory = $decoder_factory;
		$this->serializer = $serializer;
		$this->unserializer = $unserializer;

		$this->validate();
	}

	private function validate() {
		if ( $this->extension !== null && ! preg_match( '/^[a-z0-9]{2,4}$/', $this->extension ) ) {
			throw new LogicException( 'Invalid extension found.' );
		}
	}

	/**
	 * @param ListScreenId $id
	 *
	 * @return ListScreen|null
	 */
	public function find( ListScreenId $id ) {
		foreach ( $this->find_all() as $list_screen ) {
			if ( $id->equals( $list_screen->get_id() ) ) {
				return $list_screen;
			}
		}

		return null;
	}

	/**
	 * @param ListScreenId $id
	 *
	 * @return bool
	 */
	public function exists( ListScreenId $id ) {
		foreach ( $this->find_all() as $list_screen ) {
			if ( $id->equals( $list_screen->get_id() ) ) {
				return true;
			}
		}

		return false;
	}

	public function find_all( array $args = [] ) {
		$args = array_merge( [
			self::KEY => null,
		], $args );

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

			if ( $args[ self::KEY ] && $list_screen->get_key() !== $args[ self::KEY ] ) {
				continue;
			}

			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function save( ListScreen $list_screen ) {
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

	/**
	 * @param ListScreen $list_screen
	 */
	public function delete( ListScreen $list_screen ) {
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
	private function get_files() {
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

	/**
	 * @param string       $path
	 * @param ListScreenId $id
	 *
	 * @return string
	 */
	private function create_file_name( $path, ListScreenId $id ) {
		return sprintf( '%s/%s.%s', $path, $id->get_id(), $this->extension );
	}

	/**
	 * @return Directory
	 */
	public function get_directory() {
		return $this->directory;
	}

	public function get_source( ListScreenId $id ) {
		if ( ! $this->has_source( $id ) ) {
			throw new SourceNotAvailableException();
		}

		return $this->create_file_name(
			$this->directory->get_path(),
			$id
		);
	}

	public function has_source( ListScreenId $id ) {
		return $this->exists( $id );
	}
}