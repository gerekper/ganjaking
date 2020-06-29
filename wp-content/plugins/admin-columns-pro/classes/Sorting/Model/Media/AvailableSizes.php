<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class AvailableSizes extends AttachmentMetaData {

	public function __construct() {
		parent::__construct( new FormatValue\AvailableSizes(), new DataType( DataType::NUMERIC ) );
	}

}