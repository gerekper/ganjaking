<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class Dimensions extends AttachmentMetaData {

	public function __construct() {
		parent::__construct( new FormatValue\Dimensions(), new DataType( DataType::NUMERIC ) );
	}

}