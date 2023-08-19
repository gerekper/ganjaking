<?php

namespace ACA\MetaBox\Column;

use AC;
use ACA\MetaBox\Editing;
use ACP\ConditionalFormat\IntegerFormattableTrait;

class Number extends Text {

	use IntegerFormattableTrait;

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\NumberFormat( $this ) );

		parent::register_settings();
	}

	public function editing() {
		return ( new Editing\ServiceFactory\Number )->create( $this );
	}

}