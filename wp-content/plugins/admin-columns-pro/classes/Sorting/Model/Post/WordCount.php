<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Type\DataType;

class WordCount extends FieldFormat implements WarningAware {

	public function __construct() {
		parent::__construct( 'post_content', new FormatValue\WordCount(), new DataType( DataType::NUMERIC ) );
	}

}