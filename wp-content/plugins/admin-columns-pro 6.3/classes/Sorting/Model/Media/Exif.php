<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;

class Exif extends AttachmentMetaData {

	public function __construct( $field ) {
		parent::__construct( new FormatValue\Exif( $field ) );
	}

}