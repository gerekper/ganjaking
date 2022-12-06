<?php

namespace ACP\ConditionalFormat;

use ACP\ConditionalFormat\Formatter\StringFormatter;

final class FormattableConfig {

	/**
	 * @var Formatter
	 */
	private $formatter;

	public function __construct( Formatter $formatter = null ) {
		if ( null === $formatter ) {
			$formatter = new StringFormatter();
		}

		$this->formatter = $formatter;
	}

	// TODO David get vs tell or parametrize this call to make deductions on which formatter, which would imply an interface or abstract class
	public function get_value_formatter(): Formatter {
		return $this->formatter;
	}

}