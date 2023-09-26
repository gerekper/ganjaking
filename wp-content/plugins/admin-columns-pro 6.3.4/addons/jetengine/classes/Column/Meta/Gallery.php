<?php

namespace ACA\JetEngine\Column\Meta;

use AC;
use ACA\JetEngine\Column;
use ACA\JetEngine\Editing\EditableTrait;
use ACA\JetEngine\Field;
use ACA\JetEngine\Value\DefaultValueFormatterTrait;
use ACP;

/**
 * @property Field\Type\Gallery $field
 */
class Gallery extends Column\Meta implements ACP\Editing\Editable {

	use EditableTrait,
		DefaultValueFormatterTrait;

	protected function register_settings() {
		$this->add_setting( new AC\Settings\Column\Images( $this ) );
	}

}