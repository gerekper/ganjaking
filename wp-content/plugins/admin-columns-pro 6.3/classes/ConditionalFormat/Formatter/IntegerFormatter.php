<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat\Formatter;

class IntegerFormatter extends BaseFormatter {

	public function __construct() {
		parent::__construct( self::INTEGER );
	}

}