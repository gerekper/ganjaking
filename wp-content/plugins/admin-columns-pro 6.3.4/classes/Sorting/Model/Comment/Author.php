<?php

namespace ACP\Sorting\Model\Comment;

use AC\Settings\FormatValue;
use ACP;
use ACP\Sorting\Model\WarningAware;

class Author extends FieldFormat implements WarningAware {

	public function __construct( FormatValue $formatter ) {
		parent::__construct( 'user_id', new ACP\Sorting\FormatValue\SettingFormatter( $formatter ) );
	}

}