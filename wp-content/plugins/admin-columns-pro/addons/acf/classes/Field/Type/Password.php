<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class Password extends Field
	implements Field\Placeholder, Field\ValueWrapper {

	use PlaceholderTrait,
		ValueDecoratorTrait;
}