<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class Height extends AttachmentMetaData {

	public function __construct() {
		parent::__construct( new FormatValue\Height(), new DataType( DataType::NUMERIC ) );
	}

}