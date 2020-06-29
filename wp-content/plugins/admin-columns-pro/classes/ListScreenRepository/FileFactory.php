<?php

namespace ACP\ListScreenRepository;

use ACP\Storage\Directory;
use ACP\Storage\ListScreen\DecoderFactory;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\Serializer\JsonSerializer;
use ACP\Storage\ListScreen\Serializer\PhpSerializer;
use ACP\Storage\ListScreen\SerializerTypes;
use ACP\Storage\ListScreen\Unserializer\JsonUnserializer;
use RuntimeException;

final class FileFactory {

	/**
	 * @var Encoder
	 */
	private $encoder;

	/**
	 * @var DecoderFactory
	 */
	private $decoder_factory;

	public function __construct( Encoder $encoder, DecoderFactory $decoder_factory ) {
		$this->encoder = $encoder;
		$this->decoder_factory = $decoder_factory;
	}

	/**
	 * @param string    $type
	 * @param Directory $directory
	 *
	 * @return File
	 */
	public function create( $type, Directory $directory ) {
		switch ( $type ) {
			case SerializerTypes::PHP:
				$serializer = new PhpSerializer\File();
				$unserializer = null;

				break;
			case SerializerTypes::JSON:
				$serializer = new JsonSerializer();
				$unserializer = new JsonUnserializer();

				break;
			default:
				throw new RuntimeException( 'Type of file not supported.' );
		}

		return new File(
			$directory,
			$type,
			$this->encoder,
			$this->decoder_factory,
			$serializer,
			$unserializer
		);
	}

}