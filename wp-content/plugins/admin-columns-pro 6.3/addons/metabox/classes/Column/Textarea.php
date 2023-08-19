<?php

namespace ACA\MetaBox\Column;

use AC\Settings\Column\WordLimit;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

class Textarea extends Text {

	use FilteredHtmlFormatTrait;

	protected function register_settings() {
		parent::register_settings();

		$this->add_setting( new WordLimit( $this ) );
	}

}