<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat\Formatter;

class FloatFormatter extends BaseFormatter {

	public function __construct() {
		parent::__construct( self::FLOAT );
	}

}