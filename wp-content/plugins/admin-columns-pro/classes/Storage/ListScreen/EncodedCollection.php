<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreen;
use Iterator;
use LogicException;

final class EncodedCollection implements Iterator, Decoder {

	private $data;

	private $decoder_factory;

	public function __construct( array $encoded_list_screens, DecoderFactory $decoder_factory ) {
		$this->decoder_factory = $decoder_factory;
		$this->data = $encoded_list_screens;

		$this->validate();
	}

	public static function is_valid_collection( array $encoded_list_screens ): bool {
		foreach ( $encoded_list_screens as $encoded_list_screen ) {
			if ( ! is_array( $encoded_list_screen ) ) {
				return false;
			}
		}

		return true;
	}

	private function validate(): void {
		if ( ! self::is_valid_collection( $this->data ) ) {
			throw new LogicException( 'Invalid collection found. Expected array of arrays.' );
		}
	}

	public function decode( array $encoded_list_screen ): ListScreen {
		return $this->decoder_factory
			->create( $encoded_list_screen )
			->decode( $encoded_list_screen );
	}

	public function can_decode( array $encoded_list_screen ): bool {
		return $this->decoder_factory
			->create( $encoded_list_screen )
			->can_decode( $encoded_list_screen );
	}

	/**
	 * @return array
	 */
	public function current() {
		return current( $this->data );
	}

	public function next() {
		return next( $this->data );
	}

	public function key() {
		return key( $this->data );
	}

	public function valid() {
		return $this->key() !== null;
	}

	public function rewind() {
		reset( $this->data );
	}
}