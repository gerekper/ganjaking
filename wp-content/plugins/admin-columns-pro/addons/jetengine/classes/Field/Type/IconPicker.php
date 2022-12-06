<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;

class IconPicker extends Field implements DefaultValue {

	use DefaultValueTrait;

	const TYPE = 'iconpicker';

}