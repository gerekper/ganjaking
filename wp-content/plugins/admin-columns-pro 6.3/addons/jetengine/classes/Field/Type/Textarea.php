<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\MaxLength;
use ACA\JetEngine\Field\MaxLengthTrait;

class Textarea extends Field implements MaxLength, DefaultValue {

	const TYPE = 'textarea';

	use MaxLengthTrait,
		DefaultValueTrait;
}