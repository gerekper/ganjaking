<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\MaxLength;
use ACA\JetEngine\Field\MaxLengthTrait;

class Text extends Field implements MaxLength, DefaultValue {

	const TYPE = 'text';

	use MaxLengthTrait,
		DefaultValueTrait;
}