<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\NumberInput;
use ACA\JetEngine\Field\NumberInputTrait;

class Number extends Field implements DefaultValue, NumberInput {

	const TYPE = 'number';

	use DefaultValueTrait,
		NumberInputTrait;
}