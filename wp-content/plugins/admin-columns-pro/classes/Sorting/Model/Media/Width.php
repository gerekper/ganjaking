<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Type\DataType;

class Width extends AttachmentMetaData {

	public function __construct() {
		parent::__construct( new FormatValue\Width(), new DataType( DataType::NUMERIC ) );
	}

}