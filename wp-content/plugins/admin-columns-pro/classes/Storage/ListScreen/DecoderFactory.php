<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreenTypes;
use ACP\Exception\DecoderNotFoundException;
use ACP\Storage\ListScreen\Decoder\Version510;

final class DecoderFactory {

	/**
	 * @var ListScreenTypes
	 */
	private $list_screen_types;

	public function __construct( ListScreenTypes $list_screen_types ) {
		$this->list_screen_types = $list_screen_types;
	}

	/**
	 * @param array $data
	 *
	 * @return Decoder
	 */
	public function create( array $data ) {
		$decoders = [
			new Version510( $this->list_screen_types ),
		];

		foreach ( $decoders as $decoder ) {
			if ( $decoder->can_decode( $data ) ) {
				return $decoder;
			}
		}

		throw new DecoderNotFoundException( $data );
	}

}