<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Number extends Field implements Field\Number, Field\DefaultValue, Field\ValueWrapper {

	use DefaultValueTrait,
		ValueDecoratorTrait,
		NumberTrait;
}