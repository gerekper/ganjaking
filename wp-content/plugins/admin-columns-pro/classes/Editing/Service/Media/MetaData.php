<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service;
use ACP\Editing\Storage;

class MetaData extends Service\SerializedMeta {

	public function __construct( $sub_key ) {
		parent::__construct( new Storage\Post\Meta( '_wp_attachment_metadata' ), [ (string) $sub_key ] );
	}

}