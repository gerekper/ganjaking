<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Email extends Field
	implements Field\Placeholder, Field\DefaultValue, Field\ValueWrapper {

	use PlaceholderTrait,
		ValueDecoratorTrait,
		DefaultValueTrait;
}