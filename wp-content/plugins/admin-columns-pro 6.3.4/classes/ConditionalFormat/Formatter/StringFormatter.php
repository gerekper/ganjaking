<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat\Formatter;

class StringFormatter extends BaseFormatter {

	public function __construct() {
		parent::__construct( self::STRING );
	}

}