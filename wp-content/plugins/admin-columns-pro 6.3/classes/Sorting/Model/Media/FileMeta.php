<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;

class FileMeta extends AttachmentMetaData {

	public function __construct( array $keys ) {
		parent::__construct( new FormatValue\FileMeta( $keys ) );
	}

}