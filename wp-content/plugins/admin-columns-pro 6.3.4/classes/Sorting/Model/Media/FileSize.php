<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\Post\MetaFormat;
use ACP\Sorting\Type\DataType;

class FileSize extends MetaFormat {

	public function __construct() {
		parent::__construct( new FormatValue\FileSize(), '_wp_attached_file', new DataType( DataType::NUMERIC ) );
	}

}
