<?php

namespace ACP\Storage\ListScreen;

use LogicException;

class LegacyCollectionDecoderAggregate implements LegacyCollectionDecoder {

	/**
	 * @var LegacyCollectionDecoder[]
	 */
	private $collection_decoders = [];

	public function __construct( array $collection_decoders ) {
		array_map( [ $this, 'add' ], $collection_decoders );
	}

	private function add( LegacyCollectionDecoder $collection_decoder ) {
		$this->collection_decoders[] = $collection_decoder;
	}

	/**
	 * @inheritDoc
	 */
	public function decode( array $data ) {
		foreach ( $this->collection_decoders as $collection_decoder ) {
			if ( $collection_decoder->can_decode( $data ) ) {
				return $collection_decoder->decode( $data );
			}
		}

		throw new LogicException( 'Unable to decode ListScreen collection.' );
	}

	/**
	 * @inheritDoc
	 */
	public function can_decode( array $data ) {
		foreach ( $this->collection_decoders as $collection_decoder ) {
			if ( $collection_decoder->can_decode( $data ) ) {
				return true;
			}
		}

		return false;
	}

}