<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreen;
use Iterator;
use LogicException;

final class EncodedCollection implements Iterator, Decoder {

	/**
	 * @var DecoderFactory
	 */
	private $decoder_factory;

	/**
	 * @var array
	 */
	private $data;

	public function __construct( array $encoded_list_screens, DecoderFactory $decoder_factory ) {
		$this->decoder_factory = $decoder_factory;
		$this->data = $encoded_list_screens;

		$this->validate( $encoded_list_screens );
	}

	public static function is_valid_collection( array $encoded_list_screens ) {
		foreach ( $encoded_list_screens as $encoded_list_screen ) {
			if ( ! is_array( $encoded_list_screen ) ) {
				return false;
			}
		}

		return true;
	}

	private function validate( array $encoded_list_screens ) {
		if ( ! self::is_valid_collection( $encoded_list_screens ) ) {
			throw new LogicException( 'Invalid collection found. Expected array of arrays.' );
		}
	}

	/**
	 * @param array $encoded_list_screen
	 *
	 * @return ListScreen
	 */
	public function decode( array $encoded_list_screen ) {
		$decoder = $this->decoder_factory->create( $encoded_list_screen );

		return $decoder->decode( $encoded_list_screen );
	}

	/**
	 * @param array $encoded_list_screen
	 *
	 * @return bool
	 */
	public function can_decode( array $encoded_list_screen ) {
		$decoder = $this->decoder_factory->create( $encoded_list_screen );

		return $decoder->can_decode( $encoded_list_screen );
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