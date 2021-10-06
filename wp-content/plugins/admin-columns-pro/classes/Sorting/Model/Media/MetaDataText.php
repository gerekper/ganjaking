<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;

class MetaDataText extends AttachmentMetaData {

	/**
	 * @param string $key
	 */
	public function __construct( $key ) {
		parent::__construct( new FormatValue\SerializedKey( $key ) );
	}

}