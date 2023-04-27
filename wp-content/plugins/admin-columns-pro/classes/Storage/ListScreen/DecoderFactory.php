<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreenFactoryInterface;
use ACP\Exception\DecoderNotFoundException;
use ACP\Storage\ListScreen\Decoder\Version510;

final class DecoderFactory {

	private $list_screen_factory;

	public function __construct( ListScreenFactoryInterface $list_screen_factory ) {
		$this->list_screen_factory = $list_screen_factory;
	}

	public function create( array $data ): Decoder {
		$decoders = [
			new Version510( $this->list_screen_factory ),
		];

		foreach ( $decoders as $decoder ) {
			if ( $decoder->can_decode( $data ) ) {
				return $decoder;
			}
		}

		throw new DecoderNotFoundException( $data );
	}

}