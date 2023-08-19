<?php

namespace ACP\Sorting\FormatValue;

use AC;
use ACP\Sorting\FormatValue;

class SettingFormatter implements FormatValue {

	/**
	 * @var AC\Settings\FormatValue
	 */
	private $setting;

	public function __construct( AC\Settings\FormatValue $setting ) {
		$this->setting = $setting;
	}

	public function format_value( $value ) {
		return $this->setting->format( $value, null );
	}

}