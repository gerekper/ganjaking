<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\Post\MetaFormat;
use ACP\Sorting\Type\DataType;

abstract class AttachmentMetaData extends MetaFormat {

	public function __construct( FormatValue $formatter, DataType $data_type = null ) {
		parent::__construct( $formatter, '_wp_attachment_metadata', $data_type );
	}

}