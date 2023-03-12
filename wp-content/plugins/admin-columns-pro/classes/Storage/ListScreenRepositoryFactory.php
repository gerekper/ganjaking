<?php

namespace ACP\Storage;

use AC;
use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Storage\ListScreenRepository;
use ACP\ListScreenRepository\FileFactory;
use ACP\Storage\ListScreen\SerializerTypes;
use LogicException;

final class ListScreenRepositoryFactory implements AC\ListScreenRepository\Storage\ListScreenRepositoryFactory {

	private $file_factory;

	public function __construct( FileFactory $file_factory ) {
		$this->file_factory = $file_factory;
	}

	public function create( string $path, bool $writable, Rules $rules = null ): ListScreenRepository {
		if ( $path === '' ) {
			throw new LogicException( 'Invalid path.' );
		}

		$file = $this->file_factory->create(
			SerializerTypes::PHP,
			new Directory( $path )
		);

		return new ListScreenRepository( $file, $writable, $rules );
	}

}