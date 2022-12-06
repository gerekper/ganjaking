<?php

namespace ACA\JetEngine\Column\Meta;

use AC\Settings\Column\NumberOfItems;

class MultiSelect extends Select {

	protected function register_settings() {
		$this->add_setting( new NumberOfItems( $this ) );
	}

}