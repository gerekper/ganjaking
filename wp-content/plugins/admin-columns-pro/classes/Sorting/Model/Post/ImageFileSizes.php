<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue\ContentTotalImageSize;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\DataType;

class ImageFileSizes extends FieldFormat implements WarningAware {

	public function __construct() {
		parent::__construct( 'post_content', new ContentTotalImageSize(), new DataType( DataType::NUMERIC ) );
	}

}