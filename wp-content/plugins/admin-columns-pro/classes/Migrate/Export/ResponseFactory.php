<?php

namespace ACP\Migrate\Export;

use AC\ListScreenCollection;
use ACP\Migrate\Export\Response\File;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\SerializerTypes;
use LogicException;

final class ResponseFactory {

	const FILE = 'file';

	/**
	 * @var Encoder
	 */
	private $encoder;

	public function __construct( Encoder $encoder ) {
		$this->encoder = $encoder;
	}

	/**
	 * @param ListScreenCollection $list_screens
	 * @param string|null          $type
	 *
	 * @return Response
	 */
	public function create( ListScreenCollection $list_screens, $type = null ) {
		if ( null === $type ) {
			$type = self::FILE;
		}

		if ( $type === self::FILE ) {
			return new File(
				SerializerTypes::JSON,
				$list_screens,
				$this->encoder
			);
		}

		throw new LogicException( 'Invalid response type found.' );
	}

}