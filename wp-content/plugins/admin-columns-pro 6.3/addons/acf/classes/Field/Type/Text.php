<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Text extends Field
	implements Field\Placeholder, Field\DefaultValue, Field\MaxLength, Field\ValueWrapper {

	use PlaceholderTrait,
		DefaultValueTrait,
		MaxLengthTrait,
		ValueDecoratorTrait;
}